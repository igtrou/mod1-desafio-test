<?php

namespace App\Application\Ports\Out;

interface RememberTokenGeneratorPort
{
    public function generate(int $length = 60): string;
}
