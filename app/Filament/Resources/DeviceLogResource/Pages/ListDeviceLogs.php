<?php

namespace App\Filament\Resources\DeviceLogResource\Pages;

use App\Filament\Resources\DeviceLogResource;
use App\Models\DeviceLog;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListDeviceLogs extends ListRecords
{
    protected static string $resource = DeviceLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): ?Builder
    {
        return DeviceLog::query()->with('device');
    }
}
