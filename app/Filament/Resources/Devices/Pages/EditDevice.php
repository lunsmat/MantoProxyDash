<?php

namespace App\Filament\Resources\Devices\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\Devices\DeviceResource;
use App\Filament\Resources\Devices\Widgets\DeviceGroupsTable;
use App\Filament\Resources\Devices\Widgets\UrlFilterTable;
use App\Services\DeviceService;
use Filament\Resources\Pages\EditRecord;

class EditDevice extends EditRecord
{
    protected static string $resource = DeviceResource::class;

    private DeviceService $deviceService;

    public function __construct()
    {
        $this->deviceService = new DeviceService();
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function () {
                    $this->record->load(['groups', 'filters']);
                    $this->deviceService->registerLog($this->record, 'Device deleted', [
                        'user_id' => auth()->id(),
                        'device_id' => $this->record->toArray(),
                    ]);
                }),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            UrlFilterTable::make(),
            DeviceGroupsTable::make(),
        ];
    }

    protected function afterSave(): void
    {
        $this->record->load('groups');
        $this->deviceService->clearDeviceCache($this->record);

        $this->deviceService->registerLog($this->record, 'Device updated', [
            'user_id' => auth()->id(),
            'device_id' => $this->record->toArray(),
        ]);
    }

    protected function beforeSave(): void
    {
        $this->deviceService->clearDeviceCache($this->record);
    }
}
