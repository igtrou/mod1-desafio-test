<?php

namespace App\Services\Auth;

use App\Domain\Auth\UserIdentity;
use App\Application\Ports\Out\EmailVerificationPort;
use App\Application\Ports\Out\AuthLifecycleEventsPort;

/**
 * Encapsula fluxos de notificacao e confirmacao de verificacao de e-mail.
 */
class EmailVerificationService
{
    /**
     * Injeta dispatcher de eventos do ciclo de autenticacao.
     */
    /**
     * Executa a rotina principal do metodo __construct.
     */
    public function __construct(
        private readonly EmailVerificationPort $emailVerification,
        private readonly AuthLifecycleEventsPort $authLifecycleEvents
    ) {}

    /**
     * Envia notificacao de verificacao quando o usuario ainda nao confirmou o e-mail.
     */
    /**
     * Executa a rotina principal do metodo sendNotification.
     */
    public function sendNotification(?int $userId): bool
    {
        if ($userId === null || $this->emailVerification->isVerified($userId)) {
            return false;
        }

        return $this->emailVerification->sendNotification($userId);
    }

    /**
     * Marca o e-mail como verificado e publica o evento de verificacao.
     */
    /**
     * Executa a rotina principal do metodo verify.
     */
    public function verify(?int $userId): bool
    {
        if ($userId === null || $this->emailVerification->isVerified($userId)) {
            return false;
        }

        $wasVerified = $this->emailVerification->markAsVerified($userId);

        if ($wasVerified) {
            $this->authLifecycleEvents->dispatchVerified(new UserIdentity($userId));
        }

        return $wasVerified;
    }
}
