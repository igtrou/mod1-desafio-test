<?php

namespace App\Application\Ports\In\Dashboard;

use App\Data\AutoCollectRunResponseData;

interface RunDashboardAutoCollectUseCase
{
    public function __invoke(array $validated): AutoCollectRunResponseData;
}
