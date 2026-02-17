<?php

use App\Infrastructure\MarketData\Providers\AlphaVantageProvider;
use App\Infrastructure\MarketData\Providers\AwesomeApiProvider;
use App\Infrastructure\MarketData\Providers\StooqProvider;
use App\Infrastructure\MarketData\Providers\YahooFinanceProvider;

return [
    'default' => env('MARKET_DATA_PROVIDER', 'awesome_api'),

    'providers' => [
        'alpha_vantage' => [
            'class' => AlphaVantageProvider::class,
            'api_key' => env('ALPHA_VANTAGE_KEY'),
            'base_uri' => env('ALPHA_VANTAGE_URL', 'https://www.alphavantage.co'),
            'currency' => env('ALPHA_VANTAGE_CURRENCY', 'USD'),
            'timezone' => env('ALPHA_VANTAGE_TIMEZONE', 'UTC'),
            'timeout_seconds' => (float) env('ALPHA_VANTAGE_TIMEOUT_SECONDS', 3.0),
        ],
        'awesome_api' => [
            'class' => AwesomeApiProvider::class,
            'base_uri' => env('AWESOME_API_URL', 'https://economia.awesomeapi.com.br/json/last'),
            'quote_currency' => env('AWESOME_QUOTE_CURRENCY', 'USD'),
            'timezone' => env('AWESOME_API_TIMEZONE', 'America/Sao_Paulo'),
            'timeout_seconds' => (float) env('AWESOME_API_TIMEOUT_SECONDS', 3.0),
        ],
        'yahoo_finance' => [
            'class' => YahooFinanceProvider::class,
            'base_uri' => env('YAHOO_FINANCE_URL', 'https://query1.finance.yahoo.com'),
            'currency' => env('YAHOO_FINANCE_CURRENCY', 'USD'),
            'timeout_seconds' => (float) env('YAHOO_FINANCE_TIMEOUT_SECONDS', 3.0),
        ],
        'stooq' => [
            'class' => StooqProvider::class,
            'base_uri' => env('STOOQ_URL', 'https://stooq.com'),
            'currency' => env('STOOQ_CURRENCY', 'USD'),
            'timeout_seconds' => (float) env('STOOQ_TIMEOUT_SECONDS', 3.0),
        ],
    ],

    'fallbacks' => [
        'stock' => ['alpha_vantage', 'yahoo_finance', 'awesome_api'],
        'crypto' => ['awesome_api', 'alpha_vantage'],
        'currency' => ['awesome_api', 'alpha_vantage'],
    ],

    'asset_types' => [
        'stock',
        'crypto',
        'currency',
    ],

    'crypto_symbols' => [
        'BTC', 'ETH', 'SOL', 'ADA', 'LTC', 'XRP', 'DOGE', 'BNB', 'AVAX', 'DOT', 'LINK',
    ],

    'currency_codes' => [
        'USD', 'BRL', 'EUR', 'GBP', 'JPY', 'CHF', 'CAD', 'AUD', 'NZD', 'CNY', 'MXN', 'ARS',
    ],
];
