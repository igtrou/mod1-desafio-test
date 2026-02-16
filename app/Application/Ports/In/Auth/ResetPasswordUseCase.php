<?php

namespace App\Application\Ports\In\Auth;

interface ResetPasswordUseCase
{
    public function __invoke(array $validatedPayload): string;
}
