<?php

namespace App\Application\Ports\Out;

interface WebSessionStatePort
{
    public function regenerate(): void;

    public function invalidate(): void;

    public function regenerateToken(): void;
}
