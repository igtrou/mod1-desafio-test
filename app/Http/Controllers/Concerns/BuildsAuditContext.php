<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;

/**
 * Fornece montagem padronizada de contexto para trilhas de auditoria.
 */
trait BuildsAuditContext
{
    /**
     * Extrai metadados da requisicao atual para anexar em eventos auditados.
     *
     * @return array<string, mixed>
     */
    protected function buildAuditContext(Request $request): array
    {
        $subjectHeader = (string) config('gateway.jwt_subject_header', 'X-Auth-Subject');

        return array_filter([
            'request_id' => $request->attributes->get('request_id'),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'method' => $request->method(),
            'path' => $request->path(),
            'route' => $request->route()?->getName(),
            'route_uri' => $request->route()?->uri(),
            'gateway_request_verified' => $request->attributes->get('gateway_request_verified') === true ? true : null,
            'gateway_admin_authorized' => $request->attributes->get('gateway_admin_authorized') === true ? true : null,
            'gateway_subject' => $this->normalizeOptionalString((string) $request->header($subjectHeader, '')),
            'gateway_roles' => $this->extractGatewayRoles($request),
        ], static fn (mixed $value): bool => $value !== null && $value !== '');
    }

    /**
     * Normaliza roles do gateway para payload auditavel.
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
     * Evita salvar strings vazias no payload de auditoria.
     */
    private function normalizeOptionalString(string $value): ?string
    {
        $normalizedValue = trim($value);

        return $normalizedValue === '' ? null : $normalizedValue;
    }
}
