<?php

namespace App\Application\Ports\In\Auth;

use App\Data\AuthTokenStoreResponseData;

interface IssueAuthTokenUseCase
{
    public function __invoke(array $credentials, array $auditContext = []): AuthTokenStoreResponseData;
}
