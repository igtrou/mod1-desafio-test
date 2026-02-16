<?php

namespace App\Application\Ports\In\Auth;

interface LogoutSessionUseCase
{
    public function __invoke(): void;
}
