<?php

namespace App\Domain\Auth;

/**
 * Resultado tipado retornado pelo broker de reset de senha.
 */
class PasswordResetResult
{
    public function __construct(
        public readonly string $status,
        public readonly ?UserIdentity $userIdentity = null,
    ) {}
}
