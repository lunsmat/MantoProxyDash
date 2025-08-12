<?php

namespace App\Filament\Resources\DeviceLogResource\Pages;

use App\Filament\Resources\DeviceLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDeviceLog extends EditRecord
{
    protected static string $resource = DeviceLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
