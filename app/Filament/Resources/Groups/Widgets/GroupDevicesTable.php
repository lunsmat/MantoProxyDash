<?php

namespace App\Filament\Resources\Groups\Widgets;

use Filament\Actions\BulkAction;
use Filament\Actions\DetachAction;
use App\Models\Device;
use App\Models\Group;
use App\Services\DeviceService;
use App\Services\GroupService;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Builder;

class GroupDevicesTable extends TableWidget
{
    protected static ?string $model = Device::class;
    public ?Group $record = null;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Dispositivos';

    private DeviceService $deviceService;

    public function __construct()
    {
        $this->deviceService = new DeviceService();
    }

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
                ->label('Nome do Dispositivo')
                ->sortable()
                ->searchable(),
            TextColumn::make('mac_address')
                ->label('Endereço MAC')
                ->sortable()
                ->searchable(),
            ToggleColumn::make('allow_connection')
                ->label('Permitir Conexão')
                ->sortable()
                ->searchable()
                ->onIcon('heroicon-o-check-circle')
                ->offIcon('heroicon-o-x-circle')
                ->updateStateUsing(function ($record, $state) {
                    $this->deviceService->updateConnectionState($record, $state);

                    return $state ? true : false;
                })
                ->toggleable(),
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            BulkAction::make('allow_all')
                ->label('Permitir acesso a todos selecionados')
                ->action(function ($records) {
                    $this->deviceService->updateMultipleConnectionStates($records, true);
                    $this->dispatch('refresh');
                })
                ->requiresConfirmation(),
            BulkAction::make('disallow_all')
                ->label('Negar acesso a todos selecionados')
                ->action(function ($records) {
                    $this->deviceService->updateMultipleConnectionStates($records, false);
                    $this->dispatch('refresh');
                })
                ->requiresConfirmation(),
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            // Permitir Acesso
            Action::make('allow_all')
                ->label('Permitir Acesso a Todos')
                ->action(function () {
                    $records = $this->deviceService->getGroupDevices($this->record->id);
                    $this->deviceService->updateMultipleConnectionStates($records, true);
                    $this->dispatch('refresh');
                })
                ->requiresConfirmation(),

            // Negar Acesso
            Action::make('disallow_all')
                ->label('Negar Acesso a Todos')
                ->action(function () {
                    $records = $this->deviceService->getGroupDevices($this->record->id);
                    $this->deviceService->updateMultipleConnectionStates($records, false);
                    $this->dispatch('refresh');
                })
                ->requiresConfirmation(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            DetachAction::make('detach')
                ->label('Desvincular Dispositivos Selecionados')
                ->action(function ($record) {
                    $this->record->devices()->detach($record);
                    $this->dispatch('refresh');
                })
                ->requiresConfirmation(),
        ];
    }
}
