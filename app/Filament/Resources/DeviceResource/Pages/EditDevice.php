<?php

namespace App\Filament\Resources\DeviceResource\Pages;

use App\Filament\Resources\DeviceResource;
use App\Filament\Resources\DeviceResource\Widgets\DeviceGroupsTable;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Cache;

class EditDevice extends EditRecord
{
    protected static string $resource = DeviceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
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
