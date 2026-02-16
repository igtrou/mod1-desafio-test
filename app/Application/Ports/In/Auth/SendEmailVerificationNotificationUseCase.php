<?php

namespace App\Application\Ports\In\Auth;

interface SendEmailVerificationNotificationUseCase
{
    public function __invoke(?object $user): bool;
}
