<?php

namespace App\Services;

class Service
{
    protected bool $log = true;

    public function enableLogging(): void
    {
        $this->log = true;
    }

    public function disableLogging(): void
    {
        $this->log = false;
    }
}
