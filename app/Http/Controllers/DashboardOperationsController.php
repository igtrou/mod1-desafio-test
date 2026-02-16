<?php

namespace App\Http\Controllers;

use App\Application\Ports\In\Dashboard\ListAutoCollectHistoryUseCase;
use App\Application\Ports\In\Dashboard\ResetAutoCollectHealthUseCase;
use App\Application\Ports\In\Dashboard\RunDashboardAutoCollectUseCase;
use App\Application\Ports\In\Dashboard\ShowDashboardOperationsPageUseCase;
use App\Application\Ports\In\Dashboard\ShowAutoCollectConfigUseCase;
use App\Application\Ports\In\Dashboard\ShowAutoCollectStatusUseCase;
use App\Application\Ports\In\Dashboard\UpdateAutoCollectConfigUseCase;
use App\Application\Ports\In\Dashboard\CancelDashboardAutoCollectUseCase;
use App\Http\Requests\AutoCollectHistoryRequest;
use App\Http\Requests\CancelAutoCollectRequest;
use App\Http\Requests\RunAutoCollectRequest;
use App\Http\Requests\UpdateAutoCollectSettingsRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

/**
 * Exponibiliza interface HTTP para operacoes de auto-collect no dashboard.
 */
class DashboardOperationsController extends Controller
{
    /**
     * Renderiza a view principal de operacoes.
     */
    public function index(
        ShowDashboardOperationsPageUseCase $showDashboardOperationsPage
    ): View
    {
        $page = $showDashboardOperationsPage();

        return view($page->viewName, $page->viewData);
    }

    /**
     * Retorna configuracao atual do auto-collect.
     */
    public function showAutoCollectConfig(
        ShowAutoCollectConfigUseCase $showAutoCollectConfig
    ): JsonResponse
    {
        $settings = $showAutoCollectConfig();

        return response()->json([
            'data' => $settings->toArray(),
        ]);
    }

    /**
     * Persiste configuracao de auto-collect com payload validado.
     */
    public function updateAutoCollectConfig(
        UpdateAutoCollectSettingsRequest $request,
        UpdateAutoCollectConfigUseCase $updateAutoCollectConfig
    ): JsonResponse {
        $response = $updateAutoCollectConfig($request->validated());

        return response()->json($response->toArray(), 200);
    }

    /**
     * Dispara execucao manual de coleta com parametros opcionais.
     */
    public function runAutoCollect(
        RunAutoCollectRequest $request,
        RunDashboardAutoCollectUseCase $runDashboardAutoCollect
    ): JsonResponse
    {
        $response = $runDashboardAutoCollect($request->validated());

        return response()->json($response->toArray(), 200);
    }

    /**
     * Lista historico recente de execucoes do auto-collect.
     */
    public function listAutoCollectHistory(
        AutoCollectHistoryRequest $request,
        ListAutoCollectHistoryUseCase $listAutoCollectHistory
    ): JsonResponse {
        $limit = (int) ($request->validated('limit') ?? 20);
        $response = $listAutoCollectHistory($limit);

        return response()->json($response->toArray(), 200);
    }

    /**
     * Reinicia o marco de saúde para limpar indicadores no painel.
     */
    public function resetAutoCollectHealth(
        ResetAutoCollectHealthUseCase $resetAutoCollectHealth
    ): JsonResponse {
        $result = $resetAutoCollectHealth();

        return response()->json($result, 200);
    }

    /**
     * Solicita cancelamento da execucao em andamento do auto-collect.
     */
    public function cancelAutoCollect(
        CancelAutoCollectRequest $request,
        CancelDashboardAutoCollectUseCase $cancelDashboardAutoCollect
    ): JsonResponse {
        $result = $cancelDashboardAutoCollect($request->validated('run_id'));

        return response()->json(['message' => $result['message']], 202);
    }

    /**
     * Retorna estado corrente (em andamento) da auto-coleta.
     */
    public function showAutoCollectStatus(
        ShowAutoCollectStatusUseCase $showAutoCollectStatus
    ): JsonResponse {
        $status = $showAutoCollectStatus();

        return response()->json($status, 200);
    }
}
