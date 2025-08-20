<?php

namespace App\Filament\Resources\DeviceLogs\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\DeviceLogs\DeviceLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDeviceLog extends EditRecord
{
    protected static string $resource = DeviceLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
