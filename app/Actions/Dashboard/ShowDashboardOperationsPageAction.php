<?php

namespace App\Actions\Dashboard;

use App\Application\Ports\In\Dashboard\ShowDashboardOperationsPageUseCase;

use App\Data\DashboardOperationsPageData;
use App\Services\Dashboard\DashboardOperationsAuthorizationService;

/**
 * Exibe a pagina de operacoes do dashboard com gate de ambiente aplicado.
 */
class ShowDashboardOperationsPageAction implements ShowDashboardOperationsPageUseCase
{
    /**
     * Injeta o servico de autorizacao para operacoes administrativas do dashboard.
     */
    public function __construct(
        private readonly DashboardOperationsAuthorizationService $authorization,
    ) {}

    /**
     * Autoriza o ambiente e retorna a view principal de operacoes.
     */
    public function __invoke(): DashboardOperationsPageData
    {
        $this->authorization->ensureLocalOrTesting();

        return new DashboardOperationsPageData(
            viewName: 'operations',
        );
    }
}
