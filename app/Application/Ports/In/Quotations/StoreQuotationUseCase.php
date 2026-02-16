<?php

namespace App\Application\Ports\In\Quotations;

use App\Data\StoredQuotationData;

interface StoreQuotationUseCase
{
    public function __invoke(array $validatedPayload): StoredQuotationData;
}
