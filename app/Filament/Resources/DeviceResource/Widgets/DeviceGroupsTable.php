<?php

namespace App\Filament\Resources\DeviceResource\Widgets;

use App\Models\Device;
use App\Models\Group;
use Filament\Actions\Action;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Builder;

class DeviceGroupsTable extends TableWidget
{
    protected static ?string $model = Group::class;
    public ?Device $record = null;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Groups';

    protected function getTableQuery(): Builder | Relation
    {
        return Group::query()
            ->whereHas('devices', function (Builder $query) {
                $query->where('device_id', $this->record->id);
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
                ->label('Group Name')
                ->sortable()
                ->searchable(),
            TextColumn::make('description')
                ->label('Description')
                ->sortable()
                ->searchable(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            DetachAction::make('detach')
                ->label('Detach Selected Groups')
                ->action(function ($record) {
                    $this->record->groups()->detach($record);
                    $this->dispatch('refresh');
                })
                ->requiresConfirmation(),
        ];
    }
}
