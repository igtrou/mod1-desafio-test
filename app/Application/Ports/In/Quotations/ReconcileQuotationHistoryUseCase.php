<?php

namespace App\Application\Ports\In\Quotations;

use App\Data\ReconcileQuotationsResultData;

interface ReconcileQuotationHistoryUseCase
{
    public function __invoke(array $symbols = [], bool $dryRun = false): ReconcileQuotationsResultData;
}
