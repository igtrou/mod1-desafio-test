<?php

namespace App\Application\Ports\Out;

interface PasswordHasherPort
{
    public function make(string $plain): string;

    public function check(string $plain, string $hashed): bool;
}
