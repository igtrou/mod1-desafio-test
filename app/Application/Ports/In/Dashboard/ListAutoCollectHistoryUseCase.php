<?php

namespace App\Application\Ports\In\Dashboard;

use App\Data\AutoCollectHistoryResponseData;

interface ListAutoCollectHistoryUseCase
{
    public function __invoke(int $limit): AutoCollectHistoryResponseData;
}
