<?php

namespace App\Infrastructure\Cache;

use App\Application\Ports\Out\RuntimeStateStorePort;
use DateTimeInterface;
use Illuminate\Support\Facades\Cache;

/**
 * Adapter de cache para estados volateis de execucao.
 */
class RuntimeStateStore implements RuntimeStateStorePort
{
    public function put(string $key, mixed $value, DateTimeInterface $expiresAt): void
    {
        Cache::put($key, $value, $expiresAt);
    }

    public function forever(string $key, mixed $value): void
    {
        Cache::forever($key, $value);
    }

    public function get(string $key): mixed
    {
        return Cache::get($key);
    }

    public function forget(string $key): void
    {
        Cache::forget($key);
    }
}
