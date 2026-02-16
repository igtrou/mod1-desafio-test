<?php

namespace App\Application\Ports\In\Quotations;

use App\Data\DeleteQuotationBatchResponseData;

interface DeleteQuotationBatchUseCase
{
    public function __invoke(array $validatedPayload, bool $canDelete, ?int $userId = null, array $auditContext = []): DeleteQuotationBatchResponseData;
}
