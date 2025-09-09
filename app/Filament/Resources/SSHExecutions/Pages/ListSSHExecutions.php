<?php

namespace App\Filament\Resources\SSHExecutions\Pages;

use App\Filament\Resources\SSHExecutions\SSHExecutionsResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSSHExecutions extends ListRecords
{
    protected static string $resource = SSHExecutionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
