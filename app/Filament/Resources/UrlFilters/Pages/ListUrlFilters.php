<?php

namespace App\Filament\Resources\UrlFilters\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\UrlFilters\UrlFilterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUrlFilters extends ListRecords
{
    protected static string $resource = UrlFilterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
