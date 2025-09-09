<?php

namespace App\Filament\Resources\SSHExecutions\Pages;

use App\Filament\Resources\SSHExecutions\SSHExecutionsResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSSHExecutions extends EditRecord
{
    protected static string $resource = SSHExecutionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
