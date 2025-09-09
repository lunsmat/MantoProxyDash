<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\Group;
use App\Models\SSHExecution;
use App\Services\SSHExecutionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Console\Output\BufferedOutput;

class RunSSHExecution extends Command
{
    private SSHExecutionService $service;

    public  function __construct()
    {
        parent::__construct();
        $this->service = new SSHExecutionService();
        $this->service->startSystemOperations();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-ssh-execution {executionId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $id = filter_var($this->argument('executionId'), FILTER_VALIDATE_INT);
        if (!$id) {
            $this->error('Execution ID is required.');
            return;
        }

        $execution = $this->service->getExecutionById($id);
        if (!$execution) {
            $this->error("Execution with ID {$id} not found.");
            return;
        }

        if ($execution->status !== 'pending') {
            $this->error("Execution with ID {$id} is not in pending status.");
            return;
        }

        $this->service->updateExecutionStatus($execution, 'in_progress');
        $execution->load(['object', 'user']);
        switch ($execution->object_type) {
            case Device::class:
                $this->handleDeviceExecution($execution, $execution->object);
                break;
            case Group::class:
                $this->handleGroupExecution($execution, $execution->object);
                break;
            default:
                $this->service->registerLog($execution, "Tipo de objeto não suportado: {$execution->object_type}");
                $execution->status = 'failed';
                $execution->output = "Tipo de objeto não suportado: {$execution->object_type}";
                $execution->save();
                break;
        }
    }

    private function handleDeviceExecution(SSHExecution $execution, Device $device): SSHExecution
    {
        $execution->load('sshUser');
        $return = $this->call("app:device-ssh-run-script", [
            '--device-id' => $device->id,
            '--port' => $execution->sshUser->port,
            '--username' => $execution->sshUser->username,
            '--password' => $execution->sshUser->password,
            '--publicKeyFilePath' => $execution->sshUser->public_key_file_path ?
                Storage::path('private/' . $execution->sshUser->public_key_file_path) : null,
            '--privateKeyFilePath' => $execution->sshUser->private_key_file_path ?
                Storage::path('private/' . $execution->sshUser->private_key_file_path) : null,
            '--passphrase' => $execution->sshUser->passphrase,
            '--scriptPath' => $execution->script_path,
            '--command' => $execution->command,
            '--executionId' => $execution->id,
        ]);

        if ($return === 0) {
            $execution->status = 'completed';
        } else {
            $execution->status = 'failed';
        }
        echo $execution->id;
        $output = Storage::disk('logs')->get("ssh_execution_{$execution->id}.log");
        if (!$execution->output) $execution->output = '';
        $execution->output .= "===================== Final Output - {$execution->updated_at} =====================\n";
        $execution->output .= $output;
        $execution->save();

        return $execution;
    }

    private function handleGroupExecution(SSHExecution $execution, Group $group)
    {
        $group->load('devices');
        $failed = 0;
        $completed = 0;
        foreach ($group->devices as $device) {
            $childExecution = $execution->children()
                ->where('object_type', Device::class)
                ->where('object_id', $device->id)->first();

            if (!$childExecution) {
                $childExecution = new SSHExecution();
                $childExecution->status = 'pending';
                $childExecution->script_path = $execution->script_path;
                $childExecution->command = $execution->command;
                $childExecution->object_type = Device::class;
                $childExecution->object_id = $device->id;
                $childExecution->user_id = $execution->user_id;
                $childExecution->parent_id = $execution->id;
                $childExecution->ssh_user_id = $execution->ssh_user_id;
                $childExecution->save();
            } else {
                if ($childExecution->status === 'completed') {
                    $completed++;
                    continue;
                } elseif ($childExecution->status === 'failed') {
                    $failed++;
                    continue;
                }
            }

            $childExecution = $this->handleDeviceExecution($childExecution, $device);
            if ($childExecution->status === 'completed') {
                $completed++;
            } else {
                $failed++;
            }

            $output = "===================== Device ID: {$device->id} - {$device->name} - {$device->status} - {$childExecution->updated_at} =====================\n";
            $output .= $childExecution->output . "\n\n";
            $execution->output = $execution->output ? $execution->output . "\n\n" . $output : $output;
            $execution->save();
        }

        if ($failed === 0 && $completed > 0) {
            $this->service->updateExecutionStatus($execution, 'completed');
        } elseif ($failed > 0 && $completed === 0) {
            $this->service->updateExecutionStatus($execution, 'failed');
        } elseif ($failed > 0 && $completed > 0) {
            $this->service->updateExecutionStatus($execution, 'partial_failure');
        } else {
            $this->service->updateExecutionStatus($execution, 'failed');
            $execution->output = "Nenhuma execução filha foi realizada.";
            $execution->save();
        }
    }
}
