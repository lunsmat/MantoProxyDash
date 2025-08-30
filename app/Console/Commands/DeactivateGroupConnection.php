<?php

namespace App\Console\Commands;

use App\Services\DeviceService;
use App\Services\GroupService;
use Illuminate\Console\Command;

class DeactivateGroupConnection extends Command
{
    private GroupService $groupService;

    private DeviceService $deviceService;

    public function __construct()
    {
        parent::__construct();
        $this->groupService = new GroupService();
        $this->deviceService = new DeviceService();

        $this->groupService->startSystemOperations();
        $this->deviceService->startSystemOperations();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:deactivate-group-connection {groupId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate all devices connected to a group';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $groupId = $this->argument('groupId');
        $groupId = filter_var($groupId, FILTER_VALIDATE_INT);

        if (!$groupId) {
            $this->error('Invalid group ID');
            return;
        }

        $group = $this->groupService->findGroupById($groupId);

        if (!$group) {
            $this->error('Group not found');
            return;
        }

        $group->load('devices');


        $this->deviceService->disableLogging();
        foreach ($group->devices as $device) {
            $this->deviceService->updateConnectionState($device, false);
        }

        $this->deviceService->enableLogging();
        $this->groupService->registerLog($group, "Desativada conexão de grupo", [
            'action' => 'deactivate',
            'group' => $group->toArray(),
        ]);
        $this->info('All devices in group ' . $group->name . ' have been disconnected.');
    }
}
