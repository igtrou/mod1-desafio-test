<?php

namespace App\Services\Dashboard;

use App\Application\Ports\Out\RuntimeStateStorePort;
use Carbon\CarbonImmutable;
use Throwable;

/**
 * Mantem o marco temporal usado para reiniciar os indicadores de saúde.
 */
class AutoCollectHealthBaselineService
{
    private const CACHE_KEY = 'dashboard:auto_collect:health_baseline_started_at';

    public function __construct(
        private readonly RuntimeStateStorePort $runtimeStateStore,
    ) {}

    /**
     * Salva o marco em UTC e retorna o valor persistido.
     */
    public function resetNow(): string
    {
        $startedAt = CarbonImmutable::now('UTC')->toIso8601String();
        $this->runtimeStateStore->forever(self::CACHE_KEY, $startedAt);

        return $startedAt;
    }

    /**
     * Retorna o marco atual, quando disponível.
     */
    public function current(): ?CarbonImmutable
    {
        $rawValue = $this->runtimeStateStore->get(self::CACHE_KEY);

        if (! is_string($rawValue) || $rawValue === '') {
            return null;
        }

        try {
            return CarbonImmutable::parse($rawValue)->utc();
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Limpa marco persistido.
     */
    public function clear(): void
    {
        $this->runtimeStateStore->forget(self::CACHE_KEY);
    }
}
