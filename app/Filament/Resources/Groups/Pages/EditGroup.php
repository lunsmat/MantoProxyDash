<?php

namespace App\Filament\Resources\Groups\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\Groups\GroupResource;
use App\Filament\Resources\Groups\Widgets\GroupDevicesTable;
use App\Filament\Resources\Groups\Widgets\UrlFilterTable;
use App\Services\GroupService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Cache;

class EditGroup extends EditRecord
{
    protected static string $resource = GroupResource::class;

    private GroupService $groupService;

    public function __construct() {
        $this->groupService = new GroupService();
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->before(function (mixed $record) {
                $this->groupService->registerLog($record, "Grupo removido", [
                    'user_id' => auth()->id(),
                    'group_id' => $record->id,
                ]);
            }),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            UrlFilterTable::make(),
            GroupDevicesTable::make(),
        ];
    }

    protected function afterSave(): void
    {
        $this->record->load('devices');
        $this->groupService->registerLog($this->record, "Grupo atualizado", [
            'user_id' => auth()->id(),
            'group_id' => $this->record->id,
        ]);
    }
}
