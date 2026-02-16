<?php

namespace App\Actions\Auth;

use App\Application\Ports\In\Auth\LogoutSessionUseCase;

use App\Services\Auth\WebSessionService;

/**
 * Encerra a sessao autenticada da aplicacao web.
 */
class LogoutSessionAction implements LogoutSessionUseCase
{
    /**
     * Injeta o servico que executa o fluxo de logout da sessao.
     */
    public function __construct(
        private readonly WebSessionService $webSessionService
    ) {}

    /**
     * Finaliza a sessao atual e limpa credenciais de autenticacao persistidas.
     */
    public function __invoke(): void
    {
        $this->webSessionService->logout();
    }
}
