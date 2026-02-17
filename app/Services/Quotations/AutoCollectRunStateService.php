<?php

namespace App\Services\Quotations;

use App\Application\Ports\Out\RuntimeStateStorePort;
use DateInterval;
use DateTimeImmutable;
use DateTimeZone;

/**
 * Mantem estado volátil de execucao corrente do auto-collect.
 */
class AutoCollectRunStateService
{
    private const CACHE_KEY = 'quotations:auto_collect:current_run';
    private const TTL_MINUTES = 120;

    public function __construct(
        private readonly RuntimeStateStorePort $runtimeStateStore,
    ) {}

    /**
     * Registra dados da execucao em andamento.
     *
     * @param  array<string, mixed>  $context
     */
    public function markRunning(array $context): void
    {
        $expiresAt = (new DateTimeImmutable('now', new DateTimeZone('UTC')))
            ->add(new DateInterval(sprintf('PT%dM', self::TTL_MINUTES)));

        $this->runtimeStateStore->put(self::CACHE_KEY, $context, $expiresAt);
    }

    /**
     * Limpa flag de execucao corrente.
     */
    public function clear(): void
    {
        $this->runtimeStateStore->forget(self::CACHE_KEY);
    }

    /**
     * Retorna execucao corrente, se existir.
     *
     * @return array<string, mixed>|null
     */
    public function current(): ?array
    {
        $payload = $this->runtimeStateStore->get(self::CACHE_KEY);

        return is_array($payload) ? $payload : null;
    }
}
