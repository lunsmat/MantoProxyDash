<?php

namespace App\Filament\Resources\GroupResource\Widgets;

use App\Models\Device;
use App\Models\Group;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;

;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class GroupDevicesTable extends TableWidget
{
    protected static ?string $model = Device::class;
    public ?Group $record = null;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Devices';

    protected function getTableQuery(): Builder | Relation
    {
        return Device::query()
            ->whereHas('groups', function (Builder $query) {
                $query->where('group_id', $this->record->id);
            });
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('id')
                ->label('ID')
                ->sortable()
                ->searchable(),
            TextColumn::make('name')
                ->label('Device Name')
                ->sortable()
                ->searchable(),
            TextColumn::make('mac_address')
                ->label('MAC Address')
                ->sortable()
                ->searchable(),
            ToggleColumn::make('allow_connection')
                ->label('Allow Connection')
                ->sortable()
                ->searchable()
                ->toggleable(),
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            BulkAction::make('allow_all')
                ->label('Allow All Connections')
                ->action(function ($records) {

                    Cache::store('redis')->set('alo', 'z');
                    foreach ($records as $record) {
                        Cache::store('redis')->set('mac-to-permission-' . $record->mac_address, '1', 60 * 60 * 2);
                    }
                    Device::whereIn('id', $records->pluck('id'))->update(['allow_connection' => true]);
                    $this->dispatch('refresh');
                })
                ->requiresConfirmation(),
            BulkAction::make('disallow_all')
                ->label('Disallow All Connections')
                ->action(function ($records) {
                    foreach ($records as $record) {
                        Cache::store('redis')->set('mac-to-permission-' . $record->mac_address, '0', 60 * 60 * 2);
                    }
                    Device::whereIn('id', $records->pluck('id'))->update(['allow_connection' => false]);
                    $this->dispatch('refresh');
                })
                ->requiresConfirmation(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            DetachAction::make('detach')
                ->label('Detach Selected Devices')
                ->action(function ($record) {
                    $this->record->devices()->detach($record);
                    $this->dispatch('refresh');
                })
                ->requiresConfirmation(),
        ];
    }
}
