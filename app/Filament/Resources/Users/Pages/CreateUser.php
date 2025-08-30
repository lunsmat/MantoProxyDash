<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Services\UserService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    private UserService $userService;

    public function __construct() {
        $this->userService = new UserService();
    }

    protected function afterSave()
    {
        $this->userService->registerLog($this->record, "Usuário criado", [
            'user_id' => Auth::user()?->id,
            'user' => $this->record->toArray(),
        ]);
    }
}
