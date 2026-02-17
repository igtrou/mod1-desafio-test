<?php

namespace App\Http\Requests\Concerns;

use App\Http\Exceptions\InvalidSymbolInputException;

/**
 * Reuso de normalizacao de payload para requests de cotacoes e dashboard.
 */
trait NormalizesRequestInput
{
    private const ALLOWED_SYMBOL_PATTERN = '/^[A-Z0-9.\-\/_]+$/';

    /**
     * Retorna regras comuns para validacao de simbolos de ativos.
     *
     * @return array<int, string>
     */
    protected function symbolRules(string ...$prefixRules): array
    {
        return [
            ...$prefixRules,
            'string',
            'max:20',
            'regex:/^[A-Z0-9.\-\/_]+$/',
        ];
    }

    /**
     * Normaliza um campo de simbolo obrigatorio no payload.
     */
    protected function normalizeRequiredSymbol(string $field = 'symbol', mixed $value = null): void
    {
        $rawSymbol = $value ?? $this->input($field, '');

        $this->merge([
            $field => $this->normalizeSymbolValue($rawSymbol),
        ]);
    }

    /**
     * Normaliza um campo de simbolo opcional quando presente.
     */
    protected function normalizeOptionalSymbol(string $field = 'symbol'): void
    {
        $value = $this->input($field);

        if (is_string($value) && $value !== '') {
            $this->merge([
                $field => $this->normalizeSymbolValue($value),
            ]);
        }
    }

    /**
     * Normaliza um campo booleano aceitando representacoes textuais.
     */
    protected function normalizeBooleanInput(string $field): void
    {
        if (! $this->has($field)) {
            return;
        }

        $normalized = filter_var(
            $this->input($field),
            FILTER_VALIDATE_BOOLEAN,
            FILTER_NULL_ON_FAILURE
        );

        if ($normalized !== null) {
            $this->merge([
                $field => $normalized,
            ]);
        }
    }

    /**
     * Normaliza uma lista de campos booleanos.
     *
     * @param  array<int, string>  $fields
     */
    protected function normalizeBooleanInputs(array $fields): void
    {
        foreach ($fields as $field) {
            $this->normalizeBooleanInput($field);
        }
    }

    /**
     * Normaliza valor bruto de simbolo sem depender da camada de dominio.
     *
     * @throws InvalidSymbolInputException
     */
    protected function normalizeSymbolValue(mixed $value): string
    {
        $rawSymbol = (string) $value;
        $candidate = strtoupper(trim($rawSymbol));

        if ($candidate === '' || preg_match(self::ALLOWED_SYMBOL_PATTERN, $candidate) !== 1) {
            throw new InvalidSymbolInputException($rawSymbol);
        }

        if (preg_match('/^[A-Z]{3}[-\/_]?[A-Z]{3}$/', $candidate) === 1) {
            // Pares de moeda sao persistidos sem separadores (ex.: USD-BRL -> USDBRL).
            return (string) preg_replace('/[^A-Z]/', '', $candidate);
        }

        return str_replace('_', '-', $candidate);
    }
}
