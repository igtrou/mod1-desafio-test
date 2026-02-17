<?php

namespace App\Application\Ports\Out;

interface EmailVerificationPort
{
    public function isVerified(int $userId): bool;

    public function sendNotification(int $userId): bool;

    public function markAsVerified(int $userId): bool;
}
