<?php

namespace App\Filament\Resources\UrlFilters\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\UrlFilters\UrlFilterResource;
use App\Services\FilterService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditUrlFilter extends EditRecord
{
    protected static string $resource = UrlFilterResource::class;

    private FilterService $filterService;

    public function __construct() {
        $this->filterService = new FilterService();
    }

    protected function getHeaderActions(): array
    {
        $actions = [];
        if (Auth::user()->is_admin) {
            $actions[] = DeleteAction::make();
        }
        return $actions;
    }

    protected function afterSave()
    {
        $this->filterService->registerLog($this->record, "Filtro editado", [
            'user_id' => Auth::user()?->id,
            'filter' => $this->record->toArray(),
        ]);
    }
}
