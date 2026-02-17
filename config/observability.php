<?php

return [
    'api_access' => [
        'enabled' => (bool) env('API_ACCESS_LOG_ENABLED', true),
        'channel' => (string) env('API_ACCESS_LOG_CHANNEL', 'api_access'),
        'skip_paths' => array_values(array_filter(array_map(
            static fn (string $value): string => trim($value),
            explode(',', (string) env('API_ACCESS_LOG_SKIP_PATHS', ''))
        ))),
    ],
    'audit' => [
        'fallback_channel' => (string) env('AUDIT_FALLBACK_LOG_CHANNEL', 'audit_fallback'),
    ],
];
