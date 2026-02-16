<?php

namespace App\Application\Ports\In\Quotations;

interface RecordQuotationCollectionStartedUseCase
{
    public function __invoke(array $context): void;
}
