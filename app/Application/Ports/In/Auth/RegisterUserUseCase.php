<?php

namespace App\Application\Ports\In\Auth;

interface RegisterUserUseCase
{
    public function __invoke(array $validatedPayload): void;
}
