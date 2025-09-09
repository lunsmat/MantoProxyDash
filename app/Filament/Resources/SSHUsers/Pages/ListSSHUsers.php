<?php

namespace App\Filament\Resources\SSHUsers\Pages;

use App\Filament\Resources\SSHUsers\SSHUsersResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSSHUsers extends ListRecords
{
    protected static string $resource = SSHUsersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
