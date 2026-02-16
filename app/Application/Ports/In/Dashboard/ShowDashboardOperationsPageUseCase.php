<?php

namespace App\Application\Ports\In\Dashboard;

use App\Data\DashboardOperationsPageData;

interface ShowDashboardOperationsPageUseCase
{
    public function __invoke(): DashboardOperationsPageData;
}
