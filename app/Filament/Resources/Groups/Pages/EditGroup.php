<?php

namespace App\Filament\Resources\Groups\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\Groups\GroupResource;
use App\Filament\Resources\Groups\Widgets\GroupDevicesTable;
use App\Filament\Resources\Groups\Widgets\UrlFilterTable;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Cache;

class EditGroup extends EditRecord
{
    protected static string $resource = GroupResource::class;

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
            GroupDevicesTable::make(),
        ];
    }
}
