<?php

namespace App\Filament\Resources\UrlFilters\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\UrlFilters\UrlFilterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditUrlFilter extends EditRecord
{
    protected static string $resource = UrlFilterResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [];
        if (Auth::user()->is_admin) {
            $actions[] = DeleteAction::make();
        }
        return $actions;
    }
}
