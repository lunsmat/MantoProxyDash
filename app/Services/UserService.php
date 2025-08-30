<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserService extends Service
{
    public function registerLog(User $user, string $message, mixed $context = null): void
    {
        if (!$this->log) return;

        $userId = Auth::user()?->id;

        $user->systemLog()->create([
            'message' => $message,
            'context' => json_encode($context),
            'user_id' => $this->isSystemRunning ? -1 : $userId,
        ]);
    }
}
