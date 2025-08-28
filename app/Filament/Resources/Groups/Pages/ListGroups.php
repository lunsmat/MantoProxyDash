<?php

namespace App\Filament\Resources\Groups\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Groups\GroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListGroups extends ListRecords
{
    protected static string $resource = GroupResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [];

        if (Auth::user()->is_admin) {
            $actions[] = CreateAction::make();
        }

        return $actions;
    }
}
