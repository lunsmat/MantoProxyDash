<?php

namespace App\Filament\Resources\Devices\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Devices\DeviceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDevices extends ListRecords
{
    protected static string $resource = DeviceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
