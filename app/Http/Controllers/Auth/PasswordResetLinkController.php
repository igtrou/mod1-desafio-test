<?php

namespace App\Http\Controllers\Auth;

use App\Application\Ports\In\Auth\SendPasswordResetLinkUseCase;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SendPasswordResetLinkRequest;
use Illuminate\Http\JsonResponse;

/**
 * Inicia o fluxo de recuperacao enviando link de reset por e-mail.
 */
class PasswordResetLinkController extends Controller
{
    /**
     * Processa solicitacao de envio de link de redefinicao.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(
        SendPasswordResetLinkRequest $request,
        SendPasswordResetLinkUseCase $sendPasswordResetLink
    ): JsonResponse
    {
        $status = $sendPasswordResetLink($request->validated());

        return response()->json(['status' => $status]);
    }
}
