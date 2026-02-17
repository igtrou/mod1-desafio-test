<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Registra resumo estruturado de requisicoes HTTP da API para investigacao operacional.
 */
class LogApiRequestSummary
{
    /**
     * Captura metadados de request/response sem interferir no fluxo da aplicacao.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->shouldLog($request)) {
            return $next($request);
        }

        $startedAtNs = hrtime(true);
        $response = $next($request);
        $durationMs = round((hrtime(true) - $startedAtNs) / 1_000_000, 3);
        $subjectHeader = (string) config('gateway.jwt_subject_header', 'X-Auth-Subject');

        $context = $this->filterContext([
            'event' => 'api_request_completed',
            'request_id' => $request->attributes->get('request_id'),
            'method' => $request->method(),
            'path' => $request->path(),
            'route_name' => $request->route()?->getName(),
            'route_uri' => $request->route()?->uri(),
            'status_code' => $response->getStatusCode(),
            'duration_ms' => $durationMs,
            'user_id' => is_numeric($request->user()?->id) ? (int) $request->user()?->id : null,
            'gateway_request_verified' => $request->attributes->get('gateway_request_verified') === true ? true : null,
            'gateway_admin_authorized' => $request->attributes->get('gateway_admin_authorized') === true ? true : null,
            'gateway_subject' => $this->normalizeString((string) $request->header($subjectHeader, '')),
            'gateway_roles' => $this->extractGatewayRoles($request),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        try {
            Log::channel((string) config('observability.api_access.channel', 'api_access'))
                ->info('api_request_completed', $context);
        } catch (Throwable $exception) {
            Log::warning('API access summary log failed.', [
                'request_id' => $request->attributes->get('request_id'),
                'exception_class' => $exception::class,
                'exception_message' => $exception->getMessage(),
            ]);
        }

        return $response;
    }

    /**
     * Limita emissao de logs ao trafego de API e remove paths ignorados.
     */
    private function shouldLog(Request $request): bool
    {
        if (! (bool) config('observability.api_access.enabled', true)) {
            return false;
        }

        if (! $request->is('api/*') && ! $request->expectsJson()) {
            return false;
        }

        $path = trim($request->path(), '/');
        $skipPaths = config('observability.api_access.skip_paths', []);

        if (! is_array($skipPaths)) {
            return true;
        }

        return ! in_array($path, $skipPaths, true);
    }

    /**
     * Normaliza roles do gateway para formato array.
     *
     * @return array<int, string>|null
     */
    private function extractGatewayRoles(Request $request): ?array
    {
        $rolesHeader = (string) config('gateway.jwt_roles_header', 'X-Auth-Roles');
        $rawRoles = strtolower((string) $request->header($rolesHeader, ''));

        if ($rawRoles === '') {
            return null;
        }

        $normalizedRoles = preg_replace('/[^a-z0-9,_-]+/', ',', $rawRoles);
        $roles = array_values(array_filter(array_map(
            static fn (string $value): string => trim($value),
            explode(',', (string) $normalizedRoles)
        )));

        return $roles === [] ? null : $roles;
    }

    /**
     * Remove valores vazios mantendo tipos utilitarios como boolean/int/float.
     *
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    private function filterContext(array $values): array
    {
        return array_filter(
            $values,
            static fn (mixed $value): bool => $value !== null && $value !== ''
        );
    }

    /**
     * Limpa strings opcionais para evitar persistencia de valores vazios.
     */
    private function normalizeString(string $value): ?string
    {
        $normalizedValue = trim($value);

        return $normalizedValue === '' ? null : $normalizedValue;
    }
}
