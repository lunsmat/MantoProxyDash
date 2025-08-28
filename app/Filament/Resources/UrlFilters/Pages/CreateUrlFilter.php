<?php

namespace App\Filament\Resources\UrlFilters\Pages;

use App\Filament\Resources\UrlFilters\UrlFilterResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateUrlFilter extends CreateRecord
{
    protected static string $resource = UrlFilterResource::class;

    protected function getRedirectUrl(): string
    {
        return Auth::user()->is_admin ? parent::getRedirectUrl() : UrlFilterResource::getUrl('index');
    }
}
