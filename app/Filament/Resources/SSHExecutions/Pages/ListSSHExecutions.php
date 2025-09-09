<?php

namespace App\Filament\Resources\SSHExecutions\Pages;

use App\Filament\Resources\SSHExecutions\SSHExecutionsResource;
use App\Models\SSHExecution;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class ListSSHExecutions extends ListRecords
{
    protected static string $resource = SSHExecutionsResource::class;

    protected function getTableQuery(): Builder|Relation|null
    {
        return SSHExecution::query()
            ->where('parent_id', null);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
