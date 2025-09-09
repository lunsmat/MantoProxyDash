<?php

namespace App\Console\Commands;

use App\Handlers\SSHHandler;
use App\Services\DeviceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Output\BufferedOutput;

class DeviceSSHRunScript extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:device-ssh-run-script {--device-id=} {--port=22} {--username=} {--password=} {--publicKeyFilePath=} {--privateKeyFilePath=} {--passphrase=} {--scriptPath=} {--command=}';

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
        $deviceId = filter_var($this->option('device-id'), FILTER_VALIDATE_INT);
        $port = filter_var($this->option('port'), FILTER_VALIDATE_INT);
        $username = filter_var($this->option('username'), FILTER_DEFAULT);

        $password = $this->option('password');
        $publicKeyFilePath = $this->option('publicKeyFilePath');
        $privateKeyFilePath = $this->option('privateKeyFilePath');
        $passphrase = $this->option('passphrase');

        $scriptPath = $this->option('scriptPath');
        $command = $this->option('command');

        if (!$deviceId)
        {
            echo "Device ID is required.\n";
            return 1;
        }

        $service = new DeviceService();
        $device = $service->getById($deviceId);

        if (!$device) {
            echo "Device not found.\n";
            return 1;
        }

        $ip = $this->findIpFromMac($device->mac_address);
        if (!$ip){
            // echo "IP address not found for device with MAC: " . $device->mac_address . "\n";
            $this->error("IP address not found for device with MAC: " . $device->mac_address);
            return 1;
        }
        $return = $this->pingIP($ip);
        if (!$return) {
            echo "Device with IP $ip is not reachable." . "\n";
            return 1;
        }
        $checkIp = $this->findIpFromMac($device->mac_address);
        if ($ip !== $checkIp) {
            echo "Device IP has changed from $ip to $checkIp. Aborting." . "\n";
            return 1;
        }

        echo "All checks passed, now to app:ssh-run-script command...\n";
        Artisan::call('app:ssh-run-script', [
            '--host' => $ip,
            '--port' => $port,
            '--username' => $username,
            '--password' => $password,
            '--publicKeyFilePath' => $publicKeyFilePath,
            '--privateKeyFilePath' => $privateKeyFilePath,
            '--passphrase' => $passphrase,
            '--scriptPath' => $scriptPath,
            '--command' => $command,
        ]);

        echo Artisan::output(). "\n";
        return 0;
    }

    private function findIpFromMac(string $macAddress): ?string // macAddress in format xx-xx-xx-xx-xx-xx or xx:xx:xx:xx:xx:xx
    {
        $arpTable = [];
        $output = [];
        exec('arp -a', $output);
        foreach ($output as $line) {
            if (preg_match('/\(([^)]+)\) at ([0-9a-fA-F:-]{17}) /', $line, $matches)) {
                $ip = $matches[1];
                $mac = strtolower(str_replace('-', ':', $matches[2]));
                $arpTable[$mac] = $ip;
            }
        }

        $normalizedMac = strtolower(str_replace('-', ':', $macAddress));
        return $arpTable[$normalizedMac] ?? null;
    }

    private function pingIP(string $ip): bool
    {
        $pingResult = [];
        exec("ping -c 1 -W 1 $ip", $pingResult, $status);
        return $status === 0;
    }
}
