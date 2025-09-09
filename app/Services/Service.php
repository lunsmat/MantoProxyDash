<?php

namespace App\Services;

abstract class Service
{
    protected bool $log = true;

    protected bool $isSystemRunning = false;

    public function enableLogging(): void
    {
        $this->log = true;
    }

    public function disableLogging(): void
    {
        $this->log = false;
    }

    public function startSystemOperations(): void
    {
        $this->isSystemRunning = true;
    }
}
