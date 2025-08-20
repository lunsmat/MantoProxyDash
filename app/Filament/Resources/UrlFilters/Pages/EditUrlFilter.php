<?php

namespace App\Filament\Resources\UrlFilters\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\UrlFilters\UrlFilterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUrlFilter extends EditRecord
{
    protected static string $resource = UrlFilterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
