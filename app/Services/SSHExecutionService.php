<?php

namespace App\Services;

use App\Models\SSHExecution;
use Illuminate\Support\Facades\Auth;

class SSHExecutionService extends Service
{
    public function getExecutionById(int $id): ?SSHExecution
    {
        return SSHExecution::where('id', $id)->first();
    }

    public function getPendingParentExecutions()
    {
        return SSHExecution::where('status', 'pending')
            ->where('parent_id', null)
            ->get();
    }

    public function updateExecutionStatus(SSHExecution $execution, string $status): void
    {
        $execution->status = $status;
        $execution->save();

        switch ($status) {
            case 'completed':
                $this->registerLog($execution, "Execução SSH {$execution->id} concluída com sucesso.");
                break;
            case 'failed':
                $this->registerLog($execution, "Execução SSH {$execution->id} falhou.");
                break;
            case 'in_progress':
                $this->registerLog($execution, "Execução SSH {$execution->id} em progresso.");
                break;
            case 'partial_failure':
                $this->registerLog($execution, "Execução SSH {$execution->id} concluída com falhas em alguns dispositivos.");
                break;
        }
    }

    public function registerLog(SSHExecution $execution, string $message, mixed $context = null): void
    {
        if (!$this->log) return;

        $userId = Auth::user()?->id;
        $execution->systemLog()->create([
            'message' => $message,
            'context' => json_encode($context),
            'user_id' => $this->isSystemRunning ? -1 : $userId,
        ]);
    }
}
