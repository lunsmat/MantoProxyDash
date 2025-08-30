<?php

namespace App\Filament\Resources\Devices\Widgets;

use Filament\Actions\DetachAction;
use App\Models\Device;
use App\Models\Group;
use App\Services\DeviceService;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Builder;

class DeviceGroupsTable extends TableWidget
{
    protected static ?string $model = Group::class;
    public ?Device $record = null;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Grupos';

    private DeviceService $deviceService;

    public function __construct()
    {
        $this->deviceService = new DeviceService();
    }

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
                ->label('Nome do Grupo')
                ->sortable()
                ->searchable(),
            TextColumn::make('description')
                ->label('Descrição')
                ->sortable()
                ->searchable(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            DetachAction::make('detach')
                ->label('Desvincular Grupos Selecionados')
                ->action(function ($record) {
                    $this->deviceService->detachGroup($this->record, $record);
                    $this->dispatch('refresh');
                })
                ->requiresConfirmation(),
        ];
    }
}
