<?php

namespace App\Services\Quotations;

use App\Application\Ports\Out\RuntimeStateStorePort;
use Carbon\Carbon;
use DateInterval;
use DateTimeImmutable;
use DateTimeZone;

/**
 * Permite solicitar e consultar cancelamento de uma execucao de auto-coleta.
 */
class AutoCollectCancellationService
{
    private const CACHE_KEY = 'quotations:auto_collect:cancel_request';

    public function __construct(
        private readonly RuntimeStateStorePort $runtimeStateStore,
    ) {}

    /**
     * Solicita cancelamento da execucao em andamento (opcionalmente atrelada a run_id).
     */
    public function request(?string $runId = null): void
    {
        $expiresAt = (new DateTimeImmutable('now', new DateTimeZone('UTC')))
            ->add(new DateInterval('PT10M'));

        $this->runtimeStateStore->put(self::CACHE_KEY, [
            'run_id' => $runId,
            'requested_at' => Carbon::now('UTC')->timestamp,
        ], $expiresAt);
    }

    /**
     * Verifica se ha cancelamento pendente para a execucao atual.
     */
    public function isRequested(?string $runId = null): bool
    {
        $payload = $this->runtimeStateStore->get(self::CACHE_KEY);

        if (! is_array($payload)) {
            return false;
        }

        if (! isset($payload['run_id']) || $payload['run_id'] === null) {
            return true;
        }

        return $runId !== null && $payload['run_id'] === $runId;
    }

    /**
     * Limpa a flag de cancelamento (usado apos conclusao/cancelamento).
     */
    public function clear(): void
    {
        $this->runtimeStateStore->forget(self::CACHE_KEY);
    }
}
