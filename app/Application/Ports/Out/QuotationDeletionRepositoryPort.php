<?php

namespace App\Application\Ports\Out;

interface QuotationDeletionRepositoryPort
{
    public function deleteByIdOrFail(int $quotationId): void;
}
