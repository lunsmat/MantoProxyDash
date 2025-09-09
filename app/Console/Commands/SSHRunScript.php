<?php

namespace App\Console\Commands;

use App\Handlers\SSHHandler;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SSHRunScript extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:ssh-run-script {--host=} {--port=22} {--username=} {--password=} {--publicKeyFilePath=} {--privateKeyFilePath=} {--passphrase=} {--scriptPath=} {--command=} {--executionId=0}';

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
        $host = filter_var($this->option('host'), FILTER_VALIDATE_URL | FILTER_VALIDATE_IP);
        $port = filter_var($this->option('port'), FILTER_VALIDATE_INT);
        $username = filter_var($this->option('username'), FILTER_DEFAULT);

        $password = $this->option('password');
        $publicKeyFilePath = $this->option('publicKeyFilePath');
        $privateKeyFilePath = $this->option('privateKeyFilePath');
        $passphrase = $this->option('passphrase');

        $scriptPath = $this->option('scriptPath');
        $command = $this->option('command');
        $executionId = $this->option('executionId');

        if (!$host || !$port || !$username) {
            Storage::disk('logs')->put("ssh_execution_{$executionId}.log", "Host, port, and username are required.\n", FILE_APPEND);
            return 1;
        }

        if (!($scriptPath || $command) && !($scriptPath && $command)) {
            Storage::disk('logs')->put("ssh_execution_{$executionId}.log", "Either scriptPath or command must be provided.\n", FILE_APPEND);
            return 1;
        }

        $connection = SSHHandler::create($host, $port, $username);
        $connection->connect();

        if (!$connection->isConnected()) {
            Storage::disk('logs')->put("ssh_execution_{$executionId}.log", "Failed to connect to the device at {$host}:{$port}\n", FILE_APPEND);
            return 1;
        }

        Storage::disk('logs')->put("ssh_execution_{$executionId}.log", "Connected to the device at {$host}:{$port}\n", FILE_APPEND);

        if ($password) {
            $connection->usePasswordAuthentication($password);
            Storage::disk('logs')->put("ssh_execution_{$executionId}.log", "Authenticated using password.\n", FILE_APPEND);
        } else if ($publicKeyFilePath && $privateKeyFilePath) {
            $connection->usePublicKeyAuthentication($publicKeyFilePath, $privateKeyFilePath, $passphrase);
        } else {
            Storage::disk('logs')->put("ssh_execution_{$executionId}.log", "No authentication method provided.\n", FILE_APPEND);
            return 1;
        }

        if (!$connection->isAuthenticated()) {
            Storage::disk('logs')->put("ssh_execution_{$executionId}.log", "Authentication failed for user {$username}.\n", FILE_APPEND);
            return 1;
        }

        Storage::disk('logs')->put("ssh_execution_{$executionId}.log", "Authentication successful.\n", FILE_APPEND);

        try {
            $output = null;
            if ($scriptPath) {
                $output = $connection->executeScript($scriptPath);
            } else if ($command) {
                $output = $connection->executeCommand($command);
            }

            if ($output === null) {
                Storage::disk('logs')->put("ssh_execution_{$executionId}.log", "Failed to execute the command or script on device.\n", FILE_APPEND);
                return 1;
            }

            Storage::disk('logs')->put("ssh_execution_{$executionId}.log", "Command/Script executed successfully. Output:\n" . $output . "\n", FILE_APPEND);
            Storage::disk('logs')->put("ssh_execution_{$executionId}.log", "{$output}.\n", FILE_APPEND);
            Storage::disk('logs')->put("ssh_execution_{$executionId}.log", "Execution completed.\n", FILE_APPEND);
        } catch (\Exception $e) {
            Storage::disk('logs')->put("ssh_execution_{$executionId}.log", "An error occurred: " . $e->getMessage() . "\n", FILE_APPEND);
            return 1;
        }

        return 0;
    }
}
