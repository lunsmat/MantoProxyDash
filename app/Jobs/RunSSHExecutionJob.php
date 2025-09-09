<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Artisan;

class RunSSHExecutionJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $executionId
    ) {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Artisan::call('app:run-ssh-execution', ['executionId' => $this->executionId]);
    }
}
