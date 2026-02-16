<?php

namespace App\Application\Ports\Out;

interface LoginRateLimiterPort
{
    public function tooManyAttempts(string $key, int $maxAttempts): bool;

    public function hit(string $key): void;

    public function clear(string $key): void;

    public function availableIn(string $key): int;
}
