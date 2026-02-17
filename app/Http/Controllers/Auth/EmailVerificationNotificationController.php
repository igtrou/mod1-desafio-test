<?php

namespace App\Http\Controllers\Auth;

use App\Application\Ports\In\Auth\SendEmailVerificationNotificationUseCase;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Reenvia notificacao de verificacao de e-mail para o usuario autenticado.
 */
class EmailVerificationNotificationController extends Controller
{
    /**
     * Dispara novo envio de verificacao quando aplicavel.
     */
    public function store(
        Request $request,
        SendEmailVerificationNotificationUseCase $sendEmailVerificationNotification
    ): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        $sent = $sendEmailVerificationNotification(
            is_numeric($user?->id) ? (int) $user->id : null
        );

        if (! $sent) {
            return redirect()->intended('/dashboard/quotations');
        }

        return response()->json(['status' => 'verification-link-sent']);
    }
}
