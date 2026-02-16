<?php

namespace App\Application\Ports\In\Quotations;

interface RecordQuotationCollectionFinishedUseCase
{
    public function __invoke(array $context): void;
}
