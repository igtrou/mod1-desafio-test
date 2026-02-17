<?php

namespace App\Infrastructure\Auth;

use App\Application\Ports\Out\EmailVerificationPort;
use App\Models\User;

/**
 * Encapsula verificacao de e-mail de usuarios via modelo persistido.
 */
class EmailVerificationGateway implements EmailVerificationPort
{
    public function isVerified(int $userId): bool
    {
        $user = $this->resolveUser($userId);

        return $user?->hasVerifiedEmail() ?? false;
    }

    public function sendNotification(int $userId): bool
    {
        $user = $this->resolveUser($userId);

        if ($user === null) {
            return false;
        }

        $user->sendEmailVerificationNotification();

        return true;
    }

    public function markAsVerified(int $userId): bool
    {
        $user = $this->resolveUser($userId);

        if ($user === null) {
            return false;
        }

        return (bool) $user->markEmailAsVerified();
    }

    private function resolveUser(int $userId): ?User
    {
        return User::query()->find($userId);
    }
}
