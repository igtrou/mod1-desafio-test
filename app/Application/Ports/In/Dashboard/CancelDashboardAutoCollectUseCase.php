<?php

namespace App\Application\Ports\In\Dashboard;

interface CancelDashboardAutoCollectUseCase
{
    public function __invoke(?string $runId = null): array;
}
