<?php

namespace App\Application\Ports\In\Quotations;

use App\Data\QuotationHistoryPageData;

interface IndexQuotationHistoryUseCase
{
    public function __invoke(array $validatedPayload): QuotationHistoryPageData;
}
