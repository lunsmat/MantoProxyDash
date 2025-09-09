<?php

namespace App\Filament\Resources\SSHExecutions\Pages;

use App\Filament\Resources\SSHExecutions\SSHExecutionsResource;
use App\Jobs\RunSSHExecutionJob;
use App\Services\SSHExecutionService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateSSHExecutions extends CreateRecord
{
    protected static string $resource = SSHExecutionsResource::class;

    private SSHExecutionService $service;

    public function __construct()
    {
        $this->service = new SSHExecutionService();
    }


    protected function getRedirectUrl(): string
    {
        return SSHExecutionsResource::getUrl('index');
    }

    protected function afterCreate(): void
    {
        RunSSHExecutionJob::dispatch($this->record->id);
        $this->record->load('object');
        $this->service->registerLog($this->record, "Grupo criado", [
            'user_id' => Auth::user()->id,
            'execution_id' => $this->record,
            'data' => $this->record->toArray(),
        ]);
    }
}
