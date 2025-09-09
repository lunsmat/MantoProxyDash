<?php

namespace App\Filament\Resources\Devices\Pages;

use App\Filament\Resources\Devices\DeviceResource;
use App\Services\DeviceService;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateDevice extends CreateRecord
{
    protected static string $resource = DeviceResource::class;

    private DeviceService $deviceService;

    public function __construct()
    {
        $this->deviceService = new DeviceService();
    }

    protected function afterCreate(): void
    {
        $this->record->load('groups');
        $this->deviceService->registerLog($this->record, 'Device created', [
            'user_id' => Auth::user()?->id,
            'device_id' => $this->record->toArray(),
        ]);
    }
}
