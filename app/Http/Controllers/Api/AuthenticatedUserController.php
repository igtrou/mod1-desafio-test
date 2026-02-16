<?php

namespace App\Http\Controllers\Api;

use App\Application\Ports\In\Auth\GetAuthenticatedUserProfileUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Exibe o perfil do usuario autenticado no contexto da API.
 */
class AuthenticatedUserController extends Controller
{
    /**
     * Retorna payload de perfil e permissoes derivadas do usuario logado.
     */
    public function show(
        Request $request,
        GetAuthenticatedUserProfileUseCase $getAuthenticatedUserProfile
    ): JsonResponse {
        $profile = $getAuthenticatedUserProfile($request->user());

        return response()->json([
            'data' => $profile->toArray(),
        ]);
    }
}
