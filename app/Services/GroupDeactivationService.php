<?php

namespace App\Services;

use App\Models\Group;
use App\Models\GroupDeactivation;
use App\Models\User;
use DateTime;
use Illuminate\Support\Facades\Auth;

class GroupDeactivationService extends Service
{
    public function createDeactivation(
        User $user,
        Group $group,
        DateTime $deactivationDateTime,
        DateTime $reactivationDateTime,
        string $reason

    ): GroupDeactivation
    {
        $deactivation = new GroupDeactivation();
        $deactivation->user_id = $user->id;
        $deactivation->group_id = $group->id;
        $deactivation->deactivation_datetime = $deactivationDateTime;
        $deactivation->reactivation_datetime = $reactivationDateTime;
        $deactivation->reason = $reason;
        $deactivation->deactivation_occurred = false;
        $deactivation->reactivation_occurred = false;
        $deactivation->save();

        $deactivation->load(['group', 'user']);

        $this->registerLog($deactivation, "Processo de desativação do grupo {$deactivation->group->name} criado", [
            'deactivation' => $deactivation->toArray(),
        ]);

        return $deactivation;
    }

    public function getPendingDeactivations()
    {
        return GroupDeactivation::where('deactivation_datetime', '<=', now())
            ->where('deactivation_occurred', false)
            ->where('reactivation_occurred', false)
            ->get();
    }

    public function getPendingReactivations()
    {
        return GroupDeactivation::where('deactivation_datetime', '<=', now())
            ->where('reactivation_datetime', '<=', now())
            ->where('deactivation_occurred', true)
            ->where('reactivation_occurred', false)
            ->get();
    }

    public function getUserGroupActiveDeactivations(User $user, Group $group)
    {
        return GroupDeactivation::where('user_id', $user->id)
            ->where('group_id', $group->id)
            ->where(function ($query) {
                $query->where('deactivation_occurred', false)
                    ->orWhere('reactivation_occurred', false);
            })
            ->get();
    }

    public function markDeactivationOccurred(GroupDeactivation $deactivation): void
    {
        $deactivation->deactivation_occurred = true;
        $deactivation->save();

        $this->registerLog($deactivation, "Processo de desativação do grupo {$deactivation->group->name} finalizado", [
            'deactivation_id' => $deactivation->id,
        ]);
    }

    public function markReactivationOccurred(GroupDeactivation $deactivation): void
    {
        $deactivation->reactivation_occurred = true;
        $deactivation->save();

        $this->registerLog($deactivation, "Processo de reativação do grupo {$deactivation->group->name} finalizado", [
            'deactivation_id' => $deactivation->id,
        ]);
    }

    public function registerLog(GroupDeactivation $deactivation, string $message, mixed $context = null): void
    {
        if (!$this->log) return;
        $userId = Auth::user()?->id;

        $deactivation->systemLog()->create([
            'message' => $message,
            'context' => json_encode($context),
            'user_id' => $this->isSystemRunning ? -1 : $userId,
        ]);
    }
}
