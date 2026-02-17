<?php

namespace App\Http\Exceptions;

use RuntimeException;

/**
 * Excecao de borda HTTP para simbolo invalido recebido em requests.
 */
class InvalidSymbolInputException extends RuntimeException
{
    /**
     * @param  string  $rawSymbol  Valor bruto enviado na request.
     */
    public function __construct(
        public readonly string $rawSymbol
    ) {
        parent::__construct(sprintf('Invalid symbol [%s].', $rawSymbol));
    }
}
