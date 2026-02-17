<?php

namespace App\Application\Ports\Out;

use App\Domain\Auth\PasswordResetResult;

interface PasswordResetBrokerPort
{
    public function sendResetLink(string $email): string;

    /**
     * @param  array{
     *     token: string,
     *     email: string,
     *     password: string,
     *     password_confirmation: string
     * }  $payload
     */
    public function reset(array $payload, string $hashedPassword, string $rememberToken): PasswordResetResult;

    public function resetLinkSentStatus(): string;

    public function passwordResetStatus(): string;
}
