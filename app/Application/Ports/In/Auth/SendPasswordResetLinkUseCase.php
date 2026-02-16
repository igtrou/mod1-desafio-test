<?php

namespace App\Application\Ports\In\Auth;

interface SendPasswordResetLinkUseCase
{
    public function __invoke(array $validatedPayload): string;
}
