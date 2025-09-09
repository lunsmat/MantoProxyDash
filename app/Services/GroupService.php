<?php

namespace App\Services;

use App\Models\Device;
use App\Models\Group;
use App\Models\SSHExecution;
use App\Models\SSHUser;
use App\Models\UrlFilter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class GroupService extends Service {
    public function detachFilter(Group $group, UrlFilter $filter): void {
        $before = [
            'group' => $group->toArray(),
            'filter' => $filter->toArray(),
        ];

        $group->filters()->detach($filter->id);

        $after = [
            'group' => $group->toArray(),
            'filter' => $filter->toArray(),
        ];
        $this->registerLog($group, "Removido Filtro: " . $filter->name, [
            'action' => 'detach',
            'before' => $before,
            'after' => $after
        ]);
    }

    public function attachFilter(Group $group, UrlFilter $filter): void {
        $before = [
            'group' => $group->toArray(),
            'filter' => $filter->toArray(),
        ];

        $group->filters()->attach($filter->id);

        $after = [
            'group' => $group->toArray(),
            'filter' => $filter->toArray(),
        ];
        $this->registerLog($group, "Adicionado Filtro: " . $filter->name, [
            'action' => 'attach',
            'before' => $before,
            'after' => $after
        ]);
    }

    public function createExecution(Group $group, SSHUser $user, ?string $command = null, ?string $path = null): SSHExecution
    {
        $execution = $group->sshExecutions()->create([
            'ssh_user_id' => $user->id,
            'command' => $command,
            'script_path' => $path,
            'status' => 'pending',
            'output' => null,
            'error_output' => null,
            'started_at' => null,
            'finished_at' => null,
            'user_id' => Auth::user()?->id,
        ]);

        $this->registerLog($group, "Criada Execução SSH: " . $execution->id, [
            'action' => 'create_execution',
            'execution' => $execution->toArray()
        ]);

        return $execution;
    }

    public function detachDevice(Group $group, Device $device): void {
        $before = [
            'group' => $group->toArray(),
            'device' => $device->toArray(),
        ];

        $group->devices()->detach($device->id);

        $after = [
            'group' => $group->toArray(),
            'device' => $device->toArray(),
        ];
        $this->registerLog($group, "Desvinculado Dispositivo: " . $device->name, [
            'action' => 'detach',
            'before' => $before,
            'after' => $after
        ]);
    }

    public function findGroupById(int $id): ?Group {
        return Group::where('id', $id)->first();
    }

    public function registerLog(Group $group, string $message, mixed $context = null): void
    {
        if (!$this->log) return;

        $userId = Auth::user()?->id;

        $group->systemLog()->create([
            'message' => $message,
            'context' => json_encode($context),
            'user_id' => $this->isSystemRunning ? -1 : $userId,
        ]);
    }
}
