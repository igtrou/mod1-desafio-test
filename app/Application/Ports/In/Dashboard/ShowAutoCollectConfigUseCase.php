<?php

namespace App\Application\Ports\In\Dashboard;

use App\Data\AutoCollectSettingsData;

interface ShowAutoCollectConfigUseCase
{
    public function __invoke(): AutoCollectSettingsData;
}
