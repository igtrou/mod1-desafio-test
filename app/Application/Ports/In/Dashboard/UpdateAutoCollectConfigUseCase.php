<?php

namespace App\Application\Ports\In\Dashboard;

use App\Data\AutoCollectSettingsUpdateResponseData;

interface UpdateAutoCollectConfigUseCase
{
    public function __invoke(array $validated): AutoCollectSettingsUpdateResponseData;
}
