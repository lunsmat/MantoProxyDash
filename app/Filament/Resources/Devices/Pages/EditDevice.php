<?php

namespace App\Filament\Resources\Devices\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\Devices\DeviceResource;
use App\Filament\Resources\Devices\Widgets\DeviceGroupsTable;
use App\Filament\Resources\Devices\Widgets\UrlFilterTable;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class EditDevice extends EditRecord
{
    protected static string $resource = DeviceResource::class;

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
        Cache::store('redis')->set('mac-to-permission-' . $this->record->mac_address, $this->record->allow_connection ? '1' : '0',
            60 * 60 * 2 // Cache for 2 hours
        );
    }
}
