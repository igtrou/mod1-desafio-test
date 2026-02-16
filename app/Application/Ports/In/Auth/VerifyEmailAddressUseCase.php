<?php

namespace App\Application\Ports\In\Auth;

interface VerifyEmailAddressUseCase
{
    public function __invoke(?object $user): bool;
}
