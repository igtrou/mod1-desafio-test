<?php

namespace App\Actions\Quotations;

use App\Application\Ports\In\Quotations\ClearAutoCollectCancellationUseCase;
use App\Services\Quotations\AutoCollectCancellationService;

class ClearAutoCollectCancellationAction implements ClearAutoCollectCancellationUseCase
{
    public function __construct(
        private readonly AutoCollectCancellationService $cancellationService
    ) {}

    public function __invoke(): void
    {
        $this->cancellationService->clear();
    }
}
