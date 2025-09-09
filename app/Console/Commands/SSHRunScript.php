<?php

namespace App\Console\Commands;

use App\Handlers\SSHHandler;
use Illuminate\Console\Command;

class SSHRunScript extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:ssh-run-script {--host=} {--port=22} {--username=} {--password=} {--publicKeyFilePath=} {--privateKeyFilePath=} {--passphrase=} {--scriptPath=} {--command=}';

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

        if (!$host || !$port || !$username) {
            echo ("Host, port, and username are required.\n");
            return 1;
        }

        if (!($scriptPath || $command) && !($scriptPath && $command)) {
            echo ("Either script path or command must be provided.\n");
            return 1;
        }

        $connection = SSHHandler::create($host, $port, $username);
        $connection->connect();

        if (!$connection->isConnected()) {
            echo ("Failed to connect to the device at {$host}:{$port}\n");
            return 1;
        }

        echo "Conectou\n";

        if ($password) {
            $connection->usePasswordAuthentication($password);
            echo "Autenticou com senha\n";
        } else if ($publicKeyFilePath && $privateKeyFilePath) {
            $connection->usePublicKeyAuthentication($publicKeyFilePath, $privateKeyFilePath, $passphrase);
        } else {
            echo("No authentication method provided.\n");
            return 1;
        }

        if (!$connection->isAuthenticated()) {
            echo ("Authentication failed for user {$username} on device.\n");
            return 1;
        }

        echo "comando: $command\n";

        try {
            $output = null;
            if ($scriptPath) {
                $output = $connection->executeScript($scriptPath);
            } else if ($command) {
                $output = $connection->executeCommand($command);
            }

            if ($output === null) {
                echo ("Failed to execute the command or script on device .\n");
                return 1;
            }

            echo ("Command/Script executed successfully on device . Output:");
            echo ($output);
        } catch (\Exception $e) {
            echo ("An error occurred: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
