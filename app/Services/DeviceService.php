<?php

namespace App\Services;

use App\Models\Device;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class DeviceService {
    public function detachFilter(Device $device, int $filterId): void {
        $device->filters()->detach($filterId);
        $this->clearFilterCache($device);
    }

    public function attachFilter(Device $device, int $filterId): void {
        $device->filters()->attach($filterId);
        $this->clearFilterCache($device);
    }

    public function detachGroup(Device $device, int $groupId): void {
        $device->groups()->detach($groupId);
    }

    public function updateConnectionState(Device $device, bool $state): void
    {
        $device->allow_connection = $state;
        $device->save();

        $this->clearPermissionCache($device);
    }

    public function updateMultipleConnectionStates(mixed $devices, bool $state): void
    {
        $ids = [];
        $macAddresses = [];

        foreach ($devices as $device) {
            $ids[] = $device->id;
            $macAddresses[] = $device->mac_address;
        }

        Device::whereIn('id', $ids)->update(['allow_connection' => $state]);
        $this->clearMultiplePermissionCache($devices);
    }

    public function clearPermissionCache(Device $device): void {
        Cache::store('redis')->delete('mac-to-permission-' . $device->mac_address);
    }

    public function clearMultiplePermissionCache(mixed $devices): void {
        $keys = [];

        foreach ($devices as $device) {
            $keys[] = 'mac-to-permission-' . $device->mac_address;
        }

        Cache::store('redis')->deleteMultiple($keys);
    }

    public function clearFilterCache(Device $device): void {
        Cache::store('redis')->delete('mac-to-filters-' . $device->mac_address);
    }

    public function clearDeviceIdFromMac($macAddress): void {
        Cache::store('redis')->delete('device-id-from-mac-' . $macAddress);
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
}
