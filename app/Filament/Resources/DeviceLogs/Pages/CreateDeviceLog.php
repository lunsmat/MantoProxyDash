<?php

namespace App\Filament\Resources\DeviceLogs\Pages;

use App\Filament\Resources\DeviceLogs\DeviceLogResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDeviceLog extends CreateRecord
{
    protected static string $resource = DeviceLogResource::class;
}
