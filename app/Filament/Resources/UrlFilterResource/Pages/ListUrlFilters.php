<?php

namespace App\Filament\Resources\UrlFilterResource\Pages;

use App\Filament\Resources\UrlFilterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUrlFilters extends ListRecords
{
    protected static string $resource = UrlFilterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
