<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Services\UserService;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;


    private UserService $userService;

    public function __construct() {
        $this->userService = new UserService();
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->before(function (mixed $record) {
                $this->userService->registerLog($record, "Usuário excluído", [
                    'user_id' => Auth::user()?->id,
                    'user' => $record->toArray(),
                ]);
            }),
        ];
    }

    protected function afterSave()
    {
        $this->userService->registerLog($this->record, "Usuário editado", [
            'user_id' => Auth::user()?->id,
            'user' => $this->record->toArray(),
        ]);
    }

    // protected function mutateFormDataBeforeSave(array $data): array
    // {
    //     if (empty($data['password'])) {
    //         unset($data['password']);
    //     }

    //     return $data;
    // }
}
