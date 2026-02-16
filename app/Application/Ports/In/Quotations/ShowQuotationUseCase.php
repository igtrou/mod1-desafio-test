<?php

namespace App\Application\Ports\In\Quotations;

interface ShowQuotationUseCase
{
    public function __invoke(array $validatedPayload);
}
