<?php

namespace App\Application\Ports\In\Auth;

interface SendEmailVerificationNotificationUseCase
{
    public function __invoke(?int $userId): bool;
}
