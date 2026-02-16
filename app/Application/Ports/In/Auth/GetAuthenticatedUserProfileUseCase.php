<?php

namespace App\Application\Ports\In\Auth;

use App\Data\AuthenticatedUserData;

interface GetAuthenticatedUserProfileUseCase
{
    public function __invoke(?object $user): AuthenticatedUserData;
}
