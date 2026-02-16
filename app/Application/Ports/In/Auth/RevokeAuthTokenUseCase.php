<?php

namespace App\Application\Ports\In\Auth;

use App\Data\AuthTokenRevocationInputData;
use App\Data\AuthTokenRevokeResponseData;

interface RevokeAuthTokenUseCase
{
    public function __invoke(AuthTokenRevocationInputData $input, array $auditContext = []): AuthTokenRevokeResponseData ;
}
