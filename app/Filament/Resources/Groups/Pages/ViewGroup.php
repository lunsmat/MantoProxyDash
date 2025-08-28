<?php

namespace App\Filament\Resources\Groups\Pages;

use App\Filament\Resources\Groups\GroupResource;
use App\Models\Group;
use App\Models\UrlFilter;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Cache;

class ViewGroup extends Page
{
    protected static string $resource = GroupResource::class;

    protected string $view = 'filament.resources.groups.pages.view-group';

    protected static ?string $title = 'Visualizar Grupo';

    public ?Group $record = null;

    public $devices = [];

    public array $enabledFilters = [];

    public $filters = [];

    public function mount()
    {
        $this->record = Group::find(request()->route('record'))->first();
        $this->devices = $this->record?->devices()->orderBy('name')->get();
        $this->enabledFilters = $this->record?->filters()->pluck('url_filters.id')->toArray();
        $this->filters = UrlFilter::all();
    }

    public function updateDeviceAuthorization(int $deviceId, bool $value)
    {
        $device = null;

        foreach ($this->devices as $d) {
            if ($d->id === $deviceId) {
                $device = $d;
                break;
            }
        }

        if ($device) {
            $device->allow_connection = $value;
            $device->save();
            Cache::store('redis')->delete('mac-to-permission-' . $device->mac_address);
        }
    }

    public function enableAll()
    {
        $record = $this->record;
        if ($record) {
            $record->devices()->update(['allow_connection' => true]);
            $this->devices = $record->devices()->orderBy('name')->get();

            $deletes = [];

            foreach ($this->devices as $device) {
                $deletes[] = 'mac-to-permission-' . $device->mac_address;
            }

            Cache::store('redis')->deleteMultiple($deletes);
        }
    }

    public function disableAll()
    {
        $record = $this->record;
        if ($record) {
            $record->devices()->update(['allow_connection' => false]);
            $this->devices = $record->devices()->orderBy('name')->get();

            $deletes = [];
            foreach ($this->devices as $device) {
                $deletes[] = 'mac-to-permission-' . $device->mac_address;
            }

            Cache::store('redis')->deleteMultiple($deletes);
        }
    }

    public function updateFilter(int $filterId, bool $value)
    {
        if ($value) {
            $this->enabledFilters[] = $filterId;
            $this->record?->filters()->attach($filterId);
        } else {
            $this->enabledFilters = array_filter($this->enabledFilters, fn($id) => $id !== $filterId);
            $this->record?->filters()->detach($filterId);
        }
    }
}
