<?php

namespace App\Infrastructure\Auth;

use App\Application\Ports\Out\PasswordResetBrokerPort;
use App\Domain\Auth\PasswordResetResult;
use App\Domain\Auth\UserIdentity;
use Illuminate\Support\Facades\Password;

/**
 * Wraps password broker operations.
 */
class PasswordResetBroker implements PasswordResetBrokerPort
{
    /**
     * Sends reset link to a given email.
     */
    /**
     * Executa a rotina principal do metodo sendResetLink.
     */
    public function sendResetLink(string $email): string
    {
        return Password::sendResetLink(['email' => $email]);
    }

    /**
     * Performs password reset with broker callback.
     *
     * @param  array{
     *     token: string,
     *     email: string,
     *     password: string,
     *     password_confirmation: string
     * }  $payload
     */
    /**
     * Executa a rotina principal do metodo reset.
     */
    public function reset(array $payload, string $hashedPassword, string $rememberToken): PasswordResetResult
    {
        $resolvedUserId = null;
        $status = Password::reset($payload, function (object $user) use (
            $hashedPassword,
            $rememberToken,
            &$resolvedUserId
        ): void {
            $user->forceFill([
                'password' => $hashedPassword,
                'remember_token' => $rememberToken,
            ])->save();

            if (is_numeric($user->id ?? null)) {
                $resolvedUserId = (int) $user->id;

                return;
            }

            if (method_exists($user, 'getKey') && is_numeric($user->getKey())) {
                $resolvedUserId = (int) $user->getKey();
            }
        });

        return new PasswordResetResult(
            status: $status,
            userIdentity: $resolvedUserId !== null ? new UserIdentity($resolvedUserId) : null
        );
    }

    /**
     * Returns broker status for successful reset-link dispatch.
     */
    /**
     * Executa a rotina principal do metodo resetLinkSentStatus.
     */
    public function resetLinkSentStatus(): string
    {
        return Password::RESET_LINK_SENT;
    }

    /**
     * Returns broker status for successful password reset.
     */
    /**
     * Executa a rotina principal do metodo passwordResetStatus.
     */
    public function passwordResetStatus(): string
    {
        return Password::PASSWORD_RESET;
    }
}
