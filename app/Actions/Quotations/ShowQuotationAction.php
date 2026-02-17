<?php

namespace App\Actions\Quotations;

use App\Application\Ports\In\Quotations\ShowQuotationUseCase;

use App\Data\QuoteData;
use App\Services\Quotations\FetchLatestQuoteService;

/**
 * Consulta a cotacao mais recente sem persistir no historico.
 */
class ShowQuotationAction implements ShowQuotationUseCase
{
    /**
     * Injeta o servico que consulta cotacoes com fallback de providers.
     */
    public function __construct(
        private readonly FetchLatestQuoteService $fetchLatestQuote
    ) {}

    /**
     * Busca a cotacao mais recente a partir dos dados validados da requisicao.
     *
     * @param  array{symbol: string, provider?: string|null, type?: string|null}  $validatedPayload
     */
    public function __invoke(array $validatedPayload): QuoteData
    {
        $quote = $this->fetchLatestQuote->handle(
            $validatedPayload['symbol'],
            $validatedPayload['provider'] ?? null,
            $validatedPayload['type'] ?? null
        );

        return new QuoteData(
            symbol: $quote->symbol,
            name: $quote->name,
            type: $quote->type,
            price: $quote->price,
            currency: $quote->currency,
            source: $quote->source,
            quotedAt: $quote->quotedAt
        );
    }
}
