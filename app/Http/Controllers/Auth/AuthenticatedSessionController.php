<?php

namespace App\Http\Controllers\Auth;

use App\Application\Ports\In\Auth\AuthenticateSessionUseCase;
use App\Application\Ports\In\Auth\LogoutSessionUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Response;

/**
 * Gerencia autenticacao e encerramento de sessao web.
 */
class AuthenticatedSessionController extends Controller
{
    /**
     * Processa login de sessao usando credenciais validadas.
     */
    public function store(
        LoginRequest $request,
        AuthenticateSessionUseCase $authenticateSession
    ): Response
    {
        $validated = $request->validated();

        $authenticateSession(
            email: $validated['email'],
            password: $validated['password'],
            remember: $request->boolean('remember'),
            throttleKey: $request->throttleKey()
        );

        return response()->noContent();
    }

    /**
     * Finaliza a sessao autenticada corrente.
     */
    public function destroy(
        LogoutSessionUseCase $logoutSession
    ): Response
    {
        $logoutSession();

        return response()->noContent();
    }
}
