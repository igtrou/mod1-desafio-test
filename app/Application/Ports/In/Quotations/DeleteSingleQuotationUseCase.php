<?php

namespace App\Application\Ports\In\Quotations;

use App\Data\DeleteQuotationResponseData;

interface DeleteSingleQuotationUseCase
{
    public function __invoke(int $quotationId, bool $canDelete, ?int $userId = null, array $auditContext = []): DeleteQuotationResponseData;
}
