<?php

namespace App\Application\Ports\In\Quotations;

use App\Data\QuoteData;

interface ShowQuotationUseCase
{
    public function __invoke(array $validatedPayload): QuoteData;
}
