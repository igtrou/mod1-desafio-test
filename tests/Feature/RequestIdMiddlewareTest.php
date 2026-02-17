<?php

namespace Tests\Feature;

use Tests\TestCase;

class RequestIdMiddlewareTest extends TestCase
{
    /**
     * Valida o comportamento esperado deste cenario de teste.
     */
    public function test_keeps_valid_request_id_header_in_response_and_error_payload(): void
    {
        $requestId = 'req-kraken-2026_02_17';

        $response = $this->withHeaders([
            'X-Request-Id' => $requestId,
        ])->postJson('/api/auth/token', []);

        $response->assertStatus(422)
            ->assertHeader('X-Request-Id', $requestId)
            ->assertJsonPath('request_id', $requestId);
    }

    /**
     * Valida o comportamento esperado deste cenario de teste.
     */
    public function test_replaces_invalid_request_id_header_with_generated_uuid(): void
    {
        $response = $this->withHeaders([
            'X-Request-Id' => "invalid id\nwith-break",
        ])->postJson('/api/auth/token', []);

        $assignedRequestId = (string) $response->headers->get('X-Request-Id');

        $response->assertStatus(422)
            ->assertJsonPath('request_id', $assignedRequestId);

        $this->assertNotSame("invalid id\nwith-break", $assignedRequestId);
        $this->assertMatchesRegularExpression(
            '/\A[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}\z/i',
            $assignedRequestId
        );
    }
}
