<?php

namespace App\Filament\Resources\Groups\Pages;

use App\Filament\Resources\Groups\GroupResource;
use App\Models\Group;
use App\Models\UrlFilter;
use App\Services\DeviceService;
use App\Services\FilterService;
use App\Services\GroupDeactivationService;
use App\Services\GroupService;
use DateTime;
use Filament\Resources\Pages\Page;
use GMP;
use Illuminate\Support\Facades\Auth;

class ViewGroup extends Page
{
    protected static string $resource = GroupResource::class;

    protected string $view = 'filament.resources.groups.pages.view-group';

    protected static ?string $title = 'Visualizar Grupo';

    private GroupService $groupService;

    private DeviceService $deviceService;

    private FilterService $filterService;

    private GroupDeactivationService $groupDeactivationService;

    public string $deactivationDateTime = '';

    public string $reactivationDateTime = '';

    public string $deactivationReason = '';

    public string $deactivationFormError = '';

    public bool $isAdmin = false;

    public function __construct()
    {
        $this->deviceService = new DeviceService();
        $this->groupService = new GroupService();
        $this->filterService = new FilterService();
        $this->groupDeactivationService = new GroupDeactivationService();
        $this->isAdmin = Auth::user()?->is_admin ?? false;
    }

    public ?Group $record = null;

    public $devices = [];

    public array $enabledFilters = [];

    public $filters = [];

    public $deactivations = [];

    public function mount()
    {
        $this->record = request()->route('record');
        $this->getGroupDevices();
        $this->getEnabledFilters();
        $this->getUserActiveDeactivations();
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

    private function getUserActiveDeactivations()
    {
        $this->deactivations = $this->groupDeactivationService->getUserGroupActiveDeactivations(Auth::user(), $this->record);
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
        $this->deviceService->disableLogging();
        $this->deviceService->updateMultipleConnectionStates($this->devices, true);
        $this->deviceService->enableLogging();

        $this->groupService->registerLog($this->record, "Todos os dispositivos habilitados", [
            'user_id' => Auth::user()?->id,
            'group' => $this->record->toArray(),
            'devices' => $this->devices,
        ]);

        foreach ($this->devices as $device) {
            $device->allow_connection = true;
        }
    }

    public function disableAll()
    {
        $this->deviceService->disableLogging();
        $this->deviceService->updateMultipleConnectionStates($this->devices, false);
        $this->deviceService->enableLogging();

        $this->groupService->registerLog($this->record, "Todos os dispositivos desabilitados", [
            'user_id' => Auth::user()?->id,
            'group' => $this->record->toArray(),
            'devices' => $this->devices,
        ]);

        foreach ($this->devices as $device) {
            $device->allow_connection = false;
        }
    }

    public function updateFilter(UrlFilter $filter, bool $value)
    {
        if ($value) {
            $this->groupService->attachFilter($this->record, $filter);
            $this->enabledFilters[] = $filter->id;
        } else {
            $this->groupService->detachFilter($this->record, $filter);
            $this->enabledFilters = array_filter($this->enabledFilters, fn($id) => $id !== $filter->id);
        }
    }

    public function scheduleDeactivation()
    {
        $deactivationDateTime = new DateTime($this->deactivationDateTime);
        $reactivationDateTime = new DateTime($this->reactivationDateTime);

        if ($deactivationDateTime >= $reactivationDateTime) {
            $this->deactivationFormError = 'A data e hora de reativação devem ser posteriores à data e hora de desativação.';
            return;
        }
        $this->groupDeactivationService->createDeactivation(
            Auth::user(),
            $this->record,
            $deactivationDateTime,
            $reactivationDateTime,
            $this->deactivationReason
        );

        $this->deactivationDateTime = '';
        $this->reactivationDateTime = '';
        $this->deactivationReason = '';
        $this->deactivationFormError = '';
        $this->getUserActiveDeactivations();

        $this->dispatch('close-modal', id: 'schedule-deactivation-modal');
    }
}
