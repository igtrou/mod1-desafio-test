<?php

namespace App\Application\Ports\In\Quotations;

use App\Data\CollectedQuotationsResultData;

interface CollectConfiguredQuotationsUseCase
{
    public function __invoke(array $symbols, ?string $provider = null, bool $dryRun = false, ?string $runId = null): CollectedQuotationsResultData;
}
