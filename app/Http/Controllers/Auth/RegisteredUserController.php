<?php

namespace App\Http\Controllers\Auth;

use App\Application\Ports\In\Auth\RegisterUserUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterUserRequest;
use Illuminate\Http\Response;

/**
 * Recebe e processa cadastro de novos usuarios.
 */
class RegisteredUserController extends Controller
{
    /**
     * Processa requisicao de registro com payload validado.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(
        RegisterUserRequest $request,
        RegisterUserUseCase $registerUser
    ): Response
    {
        $registerUser($request->validated());

        return response()->noContent();
    }
}
