<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\Group;
use App\Models\SSHExecution;
use App\Services\SSHExecutionService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Output\BufferedOutput;

class CheckSSHExecutions extends Command
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
    protected $signature = 'app:check-ssh-executions';

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
        $executions = $this->service->getPendingParentExecutions();

        foreach ($executions as $execution) {
            $this->call('app:run-ssh-execution', ['executionId' => $execution->id]);
        }
    }
}
