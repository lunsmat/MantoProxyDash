<?php

namespace App\Services;

use App\Models\Device;
use App\Models\Group;
use App\Models\UrlFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DeviceService extends Service {
    public function detachFilter(Device $device, UrlFilter $filter): void {
        $device->load('filters');
        $before = [
            'device' => $device->toArray(),
            'filter' => $filter->toArray(),
        ];

        $device->filters()->detach($filter->id);
        $this->clearDeviceCache($device);

        $after = [
            'device' => $device->toArray(),
            'filter' => $filter->toArray(),
        ];
        $this->registerLog($device, "Removido Filtro: " . $filter->name, [
            'action' => 'detach',
            'before' => $before,
            'after' => $after
        ]);
    }

    public function attachFilter(Device $device, UrlFilter $filter): void {
        $device->load('filters');
        $before = [
            'device' => $device->toArray(),
            'filter' => $filter->toArray(),
        ];

        $device->filters()->attach($filter->id);
        $this->clearDeviceCache($device);

        $after = [
            'device' => $device->toArray(),
            'filter' => $filter->toArray(),
        ];
        $this->registerLog($device, "Adicionado Filtro: " . $filter->name, [
            'action' => 'attach',
            'before' => $before,
            'after' => $after
        ]);
    }

    public function detachGroup(Device $device, Group $group): void {
        $device->load('groups');
        $before = [
            'device' => $device->toArray(),
            'group' => $group->toArray(),
        ];

        $device->groups()->detach($group->id);

        $after = [
            'device' => $device->toArray(),
            'group' => $group->toArray(),
        ];
        $this->registerLog($device, "Removido Grupo: " . $group->name, [
            'action' => 'detach',
            'before' => $before,
            'after' => $after
        ]);
    }

    public function updateConnectionState(Device $device, bool $state): void
    {
        $before = [
            'device' => $device->toArray(),
        ];

        $device->allow_connection = $state;
        $device->save();

        $this->clearDeviceCache($device);

        $after = [
            'device' => $device->toArray(),
        ];
        $this->registerLog($device, "Atualizado estado de conexão", [
            'action' => 'update',
            'before' => $before,
            'after' => $after
        ]);
    }

    public function updateMultipleConnectionStates(mixed $devices, bool $state): void
    {
        $before = [];
        $after = [];

        $ids = [];
        $macAddresses = [];

        foreach ($devices as $device) {
            $before[] = $device->toArray();
            $ids[] = $device->id;
            $macAddresses[] = $device->mac_address;
        }

        Device::whereIn('id', $ids)->update(['allow_connection' => $state]);

        foreach ($devices as $device) {
            $device->refresh();
            $after[] = $device->toArray();

        }
        $this->clearMultipleDeviceCache($devices);

        $this->registerLog($device, "Atualizado múltiplos estados de conexão", [
            'action' => 'update',
            'before' => $before,
            'after' => $after
        ]);
    }

    public function clearDeviceCache(Device $device): void
    {
        Cache::store('redis')->delete('data-from-mac-' . $device->mac_address);
    }

    public function clearMultipleDeviceCache(mixed $devices): void {
        $keys = [];

        foreach ($devices as $device) {
            $keys[] = 'data-from-mac-' . $device->mac_address;
        }

        Cache::store('redis')->deleteMultiple($keys);
    }

    public function getGroupDevices(int $groupId): mixed
    {
        return Device::whereHas('groups', function (Builder $query) use ($groupId) {
            $query->where('group_id', $groupId);
        })->get();
    }

    public function getGroupDevicesNameOrdered(int $groupId): mixed
    {
        return Device::whereHas('groups', function (Builder $query) use ($groupId) {
            $query->where('group_id', $groupId);
        })->orderBy('name')->get();
    }

    public function registerLog(Device $device, string $message, mixed $context = null): void
    {
        if (!$this->log) return;

        $userId = Auth::user()?->id;

        $device->systemLog()->create([
            'message' => $message,
            'context' => json_encode($context),
            'user_id' => $this->isSystemRunning ? -1 : $userId,
        ]);
    }
}
