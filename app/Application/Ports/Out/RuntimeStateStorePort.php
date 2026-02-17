<?php

namespace App\Application\Ports\Out;

use DateTimeInterface;

interface RuntimeStateStorePort
{
    public function put(string $key, mixed $value, DateTimeInterface $expiresAt): void;

    public function forever(string $key, mixed $value): void;

    public function get(string $key): mixed;

    public function forget(string $key): void;
}
