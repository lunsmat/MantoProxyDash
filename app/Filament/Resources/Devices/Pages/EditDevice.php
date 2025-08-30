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
            DeleteAction::make(),
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
        $this->deviceService->clearPermissionCache($this->record);
    }

    protected function beforeSave(): void
    {
        $this->deviceService->clearDeviceIdFromMac($this->record->mac_address);
    }
}
