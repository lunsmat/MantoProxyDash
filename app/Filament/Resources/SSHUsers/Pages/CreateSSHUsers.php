<?php

namespace App\Filament\Resources\SSHUsers\Pages;

use App\Filament\Resources\SSHUsers\SSHUsersResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Crypt;

class CreateSSHUsers extends CreateRecord
{
    protected static string $resource = SSHUsersResource::class;

    protected function getRedirectUrl(): string
    {
        return SSHUsersResource::getUrl('index');
    }
}
