<?php

namespace App\Application\Ports\Out;

interface ConfigCachePort
{
    public function clear(): void;
}
