<?php

namespace App\Filament\Resources\Groups\Widgets;

use App\Models\Device;
use App\Models\Group;
use App\Models\UrlFilter;
use Filament\Actions\Action;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Builder;

class UrlFilterTable extends TableWidget
{
    protected static ?string $model = UrlFilter::class;
    public ?Group $record = null;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Filtros';

    protected function getTableQuery(): Builder | Relation
    {
        return UrlFilter::query();
    }

    protected function getTableColumns(): array
    {
        $ids = $this->record->filters->pluck('id')->toArray();

        return [
            TextColumn::make('id')
                ->label('ID')
                ->sortable()
                ->searchable(),
            TextColumn::make('name')
                ->label('Nome do Grupo')
                ->sortable()
                ->searchable(),
            ToggleColumn::make('enabled')
                ->state(fn ($record) => $ids ? in_array($record->id, $ids) : false)
                ->label('Habilitado')
                ->onIcon('heroicon-o-check-circle')
                ->offIcon('heroicon-o-x-circle')
                ->updateStateUsing(function ($record, $state) {
                    if ($state) {
                        $this->record->filters()->attach($record->id);
                    } else {
                        $this->record->filters()->detach($record->id);
                    }

                    return $state ? true : false;
                }),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            //
        ];
    }
}
