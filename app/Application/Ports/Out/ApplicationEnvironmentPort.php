<?php

namespace App\Application\Ports\Out;

interface ApplicationEnvironmentPort
{
    public function isTesting(): bool;

    public function isLocalOrTesting(): bool;
}
