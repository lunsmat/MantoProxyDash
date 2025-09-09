<?php

namespace App\Filament\Resources\Groups\Pages;

use App\Filament\Resources\Groups\GroupResource;
use App\Services\GroupService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateGroup extends CreateRecord
{
    protected static string $resource = GroupResource::class;

    private GroupService $groupService;

    public function __construct() {
        $this->groupService = new GroupService();
    }

    protected function afterCreate(): void
    {
        $this->record->load('devices');
        $this->groupService->registerLog($this->record, "Grupo criado", [
            'user_id' => Auth::user()->id,
            'group_id' => $this->record->id,
        ]);
    }
}
