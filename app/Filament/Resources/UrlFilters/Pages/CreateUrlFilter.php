<?php

namespace App\Filament\Resources\UrlFilters\Pages;

use App\Filament\Resources\UrlFilters\UrlFilterResource;
use App\Services\FilterService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateUrlFilter extends CreateRecord
{
    protected static string $resource = UrlFilterResource::class;

    private FilterService $filterService;

    public function __construct() {
        $this->filterService = new FilterService();
    }

    protected function getRedirectUrl(): string
    {
        return Auth::user()->is_admin ? parent::getRedirectUrl() : UrlFilterResource::getUrl('index');
    }

    protected function afterCreate()
    {
        $this->filterService->registerLog($this->record, "Filtro criado", [
            'user_id' => Auth::user()?->id,
            'filter' => $this->record->toArray(),
        ]);
    }
}
