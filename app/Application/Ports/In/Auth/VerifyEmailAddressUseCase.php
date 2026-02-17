<?php

namespace App\Application\Ports\In\Auth;

interface VerifyEmailAddressUseCase
{
    public function __invoke(?int $userId): bool;
}
