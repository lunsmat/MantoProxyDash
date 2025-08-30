<?php

namespace App\Filament\Resources\SystemLogs\Pages;

use App\Filament\Resources\SystemLogs\SystemLogResource;
use App\Models\SystemLog;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class ListSystemLogs extends ListRecords
{
    protected static string $resource = SystemLogResource::class;

    protected function getTableQuery(): Builder|Relation|null
    {
        return SystemLog::query()->with(['user', 'object']);
    }

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
