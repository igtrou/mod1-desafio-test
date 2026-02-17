<?php

namespace App\Services\Auth;

use App\Application\Ports\Out\UserRepositoryPort;

/**
 * Construi o payload padrao de perfil do usuario autenticado.
 */
class AuthenticatedUserProfileService
{
    public function __construct(
        private readonly UserRepositoryPort $userRepository,
    ) {}

    /**
     * Mapeia dados basicos e permissoes derivadas para DTO de resposta.
     *
     * @return array{
     *     id: int|null,
     *     name: string|null,
     *     email: string|null,
     *     is_admin: bool,
     *     permissions: array{delete_quotations: bool}
     * }
     */
    public function build(?int $userId): array
    {
        $user = $userId !== null
            ? $this->userRepository->findById($userId)
            : null;
        $isAdmin = $user?->isAdmin ?? false;

        return [
            'id' => $user?->id,
            'name' => $user?->name,
            'email' => $user?->email,
            'is_admin' => $isAdmin,
            'permissions' => [
                'delete_quotations' => $isAdmin,
            ],
        ];
    }
}
