<?php

namespace App\Application\Ports\In\Auth;

interface AuthenticateSessionUseCase
{
    public function __invoke(string $email, string $password, bool $remember, string $throttleKey): void;
}
