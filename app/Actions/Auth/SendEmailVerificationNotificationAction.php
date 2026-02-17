<?php

namespace App\Actions\Auth;

use App\Application\Ports\In\Auth\SendEmailVerificationNotificationUseCase;

use App\Services\Auth\EmailVerificationService;

/**
 * Dispara o envio do e-mail de verificacao para o usuario autenticado.
 */
class SendEmailVerificationNotificationAction implements SendEmailVerificationNotificationUseCase
{
    /**
     * Injeta o servico que orquestra notificacoes de verificacao de e-mail.
     */
    public function __construct(
        private readonly EmailVerificationService $emailVerificationService
    ) {}

    /**
     * Solicita envio da notificacao de verificacao e indica se houve envio.
     *
     * @param  int|null  $userId Identificador do usuario autenticado.
     */
    public function __invoke(?int $userId): bool
    {
        return $this->emailVerificationService->sendNotification($userId);
    }
}
