<?php

namespace App\Console\Commands;

use App\Services\GroupDeactivationService;
use App\Services\GroupService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CheckGroupDeactivations extends Command
{
    private GroupDeactivationService $groupDeactivationService;

    public function  __construct()
    {
        parent::__construct();
        $this->groupDeactivationService = new GroupDeactivationService();

        $this->groupDeactivationService->startSystemOperations();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-group-deactivations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pendingDeactivations = $this->groupDeactivationService->getPendingDeactivations();

        foreach ($pendingDeactivations as $deactivation) {
            $deactivation->load(['group', 'user']);
            $this->groupDeactivationService->registerLog($deactivation, "Processo de desativação do grupo {$deactivation->group->name} iniciado", [
                'deactivation' => $deactivation->toArray(),
            ]);
            $this->info("Iniciando processo de desativação do grupo {$deactivation->group->name}");
            Artisan::call('app:deactivate-group-connection', [
                'groupId' => $deactivation->group_id
            ]);
            $this->groupDeactivationService->markDeactivationOccurred($deactivation);
            $this->info("Processo de desativação do grupo {$deactivation->group->name} finalizado");
        }

        $pendingReactivations = $this->groupDeactivationService->getPendingReactivations();

        foreach ($pendingReactivations as $reactivation) {
            $reactivation->load(['group', 'user']);
            $this->groupDeactivationService->registerLog($reactivation, "Processo de reativação do grupo {$reactivation->group->name} iniciado", [
                'reactivation' => $reactivation->toArray(),
            ]);
            $this->info("Iniciando processo de reativação do grupo {$reactivation->group->name}");
            Artisan::call('app:activate-group-connection', [
                'groupId' => $reactivation->group_id
            ]);
            $this->groupDeactivationService->markReactivationOccurred($reactivation);
            $this->info("Processo de reativação do grupo {$reactivation->group->name} finalizado");
        }
    }
}
