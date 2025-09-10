<?php

namespace App\Filament\Resources\Groups\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\Groups\GroupResource;
use App\Filament\Resources\Groups\Widgets\GroupDevicesTable;
use App\Filament\Resources\Groups\Widgets\UrlFilterTable;
use App\Jobs\RunSSHExecutionJob;
use App\Services\GroupService;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditGroup extends EditRecord
{
    protected static string $resource = GroupResource::class;

    private GroupService $groupService;

    public function __construct() {
        $this->groupService = new GroupService();
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->before(function (mixed $record) {
                $this->groupService->registerLog($record, "Grupo removido", [
                    'user_id' => Auth::user()?->id,
                    'group_id' => $record->id,
                ]);
            }),
            Action::make('shutdown')
                ->label('Desligar Computadores do Grupo')
                ->color('danger')
                ->icon('heroicon-o-power')
                ->requiresConfirmation()
                ->action(function () {
                    if (!$this->record?->default_ssh_user)
                        return null;
                    $sshUser = $this->record->sshDefaultUser;
                    if (!$sshUser)
                        return null;
                    $execution = $this->groupService->createExecution($this->record, $sshUser, command: "sudo shutdown -h now");
                    RunSSHExecutionJob::dispatch($execution->id);
                })->hidden(fn () => !$this->record?->default_ssh_user),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            UrlFilterTable::make(),
            GroupDevicesTable::make(),
        ];
    }

    protected function afterSave(): void
    {
        $this->record->load('devices');
        $this->groupService->registerLog($this->record, "Grupo atualizado", [
            'user_id' => Auth::user()?->id,
            'group_id' => $this->record->id,
        ]);
    }
}
