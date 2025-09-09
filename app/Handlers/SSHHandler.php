<?php

namespace App\Handlers;

use App\Enums\SSHAuthenticationType;
use Illuminate\Support\Facades\Storage;

class SSHHandler
{
    private string $host;
    private int $port;

    private bool $isConnected = false;
    private bool $isAuthenticated = false;
    private SSHAuthenticationType $authenticationType;

    private ?string $username;
    private ?string $password;

    private ?string $publicKeyFilePath;
    private ?string $privateKeyFilePath;
    private ?string $passphrase;

    private mixed $connection;

    public function __construct(string $host, int $port, string $username)
    {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
    }

    public static function create(string $host, int $port, string $username): self
    {
        return new self($host, $port, $username);
    }

    public function connect(): void
    {
        try {
            $this->connection = ssh2_connect($this->host, $this->port);
            if (!$this->connection) throw new \Exception("SSH connection failed.");
            $this->isConnected = true;
        } catch (\Exception $e) {
            Storage::disk('local')->put('ssh_errors.log', $e->getMessage());
        }
    }

    public function usePasswordAuthentication(string $password): void
    {
        $this->password = $password;
        $this->usernameAndPasswordAuthentication();
    }

    public function usePublicKeyAuthentication(string $publicKeyFilePath, string $privateKeyFilePath, ?string $passphrase = null): void
    {
        $this->publicKeyFilePath = $publicKeyFilePath;
        $this->privateKeyFilePath = $privateKeyFilePath;
        $this->passphrase = $passphrase;
        $this->publicKeyAuthentication();
    }

    public function executeCommand(string $command): ?string
    {
        try {
            if (!$this->isConnected) throw new \Exception("SSH connection is not established.");
            if (!$this->isAuthenticated) throw new \Exception("SSH connection is not authenticated.");
            if (empty($command)) throw new \Exception("Command cannot be empty.");

            $stream = ssh2_exec($this->connection, $command);
            if (!$stream) throw new \Exception("SSH command execution failed.");

            stream_set_blocking($stream, true);
            $output = stream_get_contents($stream);
            fclose($stream);

            return $output;
        } catch (\Exception $e) {
            Storage::disk('local')->put('ssh_errors.log', $e->getMessage());
            return null;
        }
    }

    public function executeScript(string $filePath): ?string
    {
        try {
            if (!file_exists($filePath)) throw new \Exception("Script file does not exist.");
            if (!$this->connection) throw new \Exception("SSH connection is not established.");
            if (!$this->isAuthenticated) throw new \Exception("SSH connection is not authenticated.");

            $command = "bash " . escapeshellarg($filePath);
            return $this->executeCommand($command);
        } catch (\Exception $e) {
            Storage::disk('local')->put('ssh_errors.log', $e->getMessage());
            return null;
        }
    }

    public function isConnected(): bool
    {
        return $this->isConnected;
    }

    public function isAuthenticated(): bool
    {
        return $this->isAuthenticated;
    }

    public function disconnect(): void
    {
        $this->connection = null;
        $this->isConnected = false;
        $this->isAuthenticated = false;
    }

    public function __destruct()
    {
        $this->disconnect();
    }

    private function usernameAndPasswordAuthentication(): void
    {
        try {
            if (!$this->isConnected) throw new \Exception("No SSH connection established.");

            if ($this->isAuthenticated) return;
            if (!$this->username || !$this->password) {
                throw new \Exception("Username and password must be provided for authentication.");
            }
            $result = ssh2_auth_password($this->connection, $this->username, $this->password);
            if (!$result) throw new \Exception("SSH authentication failed with username and password.");
            $this->isAuthenticated = true;
            $this->authenticationType = SSHAuthenticationType::UsernameAndPassword;
        } catch (\Exception $e) {
            Storage::disk('local')->put('ssh_errors.log', $e->getMessage());
        }
    }

    private function publicKeyAuthentication(): void
    {
        try {
            if (!$this->isConnected) throw new \Exception("No SSH connection established.");

            if ($this->isAuthenticated) return;
            if (!$this->username || !$this->publicKeyFilePath || !$this->privateKeyFilePath) {
                throw new \Exception("Username, public key file, and private key file must be provided for authentication.");
            }
            $result = ssh2_auth_pubkey_file(
                $this->connection,
                $this->username,
                $this->publicKeyFilePath,
                $this->privateKeyFilePath,
                $this->passphrase
            );
            if (!$result) throw new \Exception("SSH public key authentication failed.");
            $this->isAuthenticated = true;
            $this->authenticationType = SSHAuthenticationType::PublicKey;
        } catch (\Exception $e) {
            Storage::disk('local')->put('ssh_errors.log', $e->getMessage());
        }
    }
}
