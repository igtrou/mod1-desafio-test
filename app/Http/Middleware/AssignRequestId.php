<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Atribui um request ID por requisicao e propaga em contexto/log/resposta.
 */
class AssignRequestId
{
    private const REQUEST_ID_MAX_LENGTH = 120;

    private const REQUEST_ID_PATTERN = '/\A[a-zA-Z0-9][a-zA-Z0-9._:-]{0,119}\z/';

    /**
     * Garante que toda requisicao tenha identificador correlacionavel.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $requestId = $this->resolveRequestId((string) $request->header('X-Request-Id', ''));

        $request->attributes->set('request_id', $requestId);
        Log::withContext(['request_id' => $requestId]);

        $response = $next($request);
        $response->headers->set('X-Request-Id', $requestId);

        return $response;
    }

    /**
     * Normaliza o request id recebido; gera UUID quando o valor informado e invalido.
     */
    private function resolveRequestId(string $requestId): string
    {
        $normalizedValue = trim($requestId);

        if ($normalizedValue !== ''
            && strlen($normalizedValue) <= self::REQUEST_ID_MAX_LENGTH
            && preg_match(self::REQUEST_ID_PATTERN, $normalizedValue) === 1) {
            return $normalizedValue;
        }

        return (string) Str::uuid();
    }
}
