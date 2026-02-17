<?php

namespace App\Actions\Auth;

use App\Application\Ports\In\Auth\GetAuthenticatedUserProfileUseCase;

use App\Data\AuthenticatedUserData;
use App\Services\Auth\AuthenticatedUserProfileService;

/**
 * Monta o perfil do usuario autenticado para respostas da API.
 */
class GetAuthenticatedUserProfileAction implements GetAuthenticatedUserProfileUseCase
{
    /**
     * Injeta o servico que monta o DTO de perfil do usuario autenticado.
     */
    public function __construct(
        private readonly AuthenticatedUserProfileService $profileService,
    ) {}

    /**
     * Gera o perfil serializavel do usuario autenticado atual.
     *
     * @param  int|null  $userId Identificador do usuario autenticado.
     */
    public function __invoke(?int $userId): AuthenticatedUserData
    {
        $profile = $this->profileService->build($userId);

        return new AuthenticatedUserData(
            id: $profile['id'],
            name: $profile['name'],
            email: $profile['email'],
            isAdmin: $profile['is_admin'],
            permissions: $profile['permissions']
        );
    }
}
