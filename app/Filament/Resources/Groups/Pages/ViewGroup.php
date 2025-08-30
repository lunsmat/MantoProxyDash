<?php

namespace App\Filament\Resources\Groups\Pages;

use App\Filament\Resources\Groups\GroupResource;
use App\Models\Group;
use App\Services\DeviceService;
use App\Services\FilterService;
use App\Services\GroupService;
use Filament\Resources\Pages\Page;
use GMP;

class ViewGroup extends Page
{
    protected static string $resource = GroupResource::class;

    protected string $view = 'filament.resources.groups.pages.view-group';

    protected static ?string $title = 'Visualizar Grupo';

    private GroupService $groupService;

    private DeviceService $deviceService;

    private FilterService $filterService;

    public function __construct()
    {
        $this->deviceService = new DeviceService();
        $this->groupService = new GroupService();
        $this->filterService = new FilterService();
    }

    public ?Group $record = null;

    public $devices = [];

    public array $enabledFilters = [];

    public $filters = [];

    public function mount()
    {
            $this->record = request()->route('record');
            $this->getGroupDevices();
            $this->getEnabledFilters();
            $this->filters = $this->filterService->getAllFilters();
    }

    private function getGroupDevices(): void
    {
        $this->devices = $this->deviceService->getGroupDevicesNameOrdered($this->record?->id ?? 0);
    }

    private function getEnabledFilters(): void
    {
        $this->enabledFilters = $this->filterService->getGroupFiltersIds($this->record?->id ?? 0);
    }

    public function updateDeviceAuthorization(int $deviceId, bool $value)
    {
        $device = null;
        $deviceKey = null;

        foreach ($this->devices as $key => $d) {
            if ($d->id === $deviceId) {
                $device = $d;
                $deviceKey = $key;
                break;
            }
        }

        if ($device) {
            $this->deviceService->updateConnectionState($device, $value);
            $this->devices[$deviceKey]->allow_connection = $value;
        }
    }

    public function enableAll()
    {
        $this->deviceService->updateMultipleConnectionStates($this->devices, true);

        foreach ($this->devices as $device) {
            $device->allow_connection = true;
        }
    }

    public function disableAll()
    {
        $this->deviceService->updateMultipleConnectionStates($this->devices, false);

        foreach ($this->devices as $device) {
            $device->allow_connection = false;
        }
    }

    public function updateFilter(int $filterId, bool $value)
    {
        if ($value) {
            $this->groupService->attachFilter($this->record, $filterId);
            $this->enabledFilters[] = $filterId;
        } else {
            $this->groupService->detachFilter($this->record, $filterId);
            $this->enabledFilters = array_filter($this->enabledFilters, fn($id) => $id !== $filterId);
        }
    }
}
