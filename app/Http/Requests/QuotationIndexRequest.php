<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\NormalizesRequestInput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Request para listagem paginada e filtrada do historico de cotacoes.
 */
class QuotationIndexRequest extends FormRequest
{
    use NormalizesRequestInput;

    /**
     * Indica se a requisicao pode ser processada.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Define regras de validacao para filtros do historico.
     */
    public function rules(): array
    {
        $configuredProviders = array_keys(config('market-data.providers', []));
        $assetTypes = (array) config('market-data.asset_types', ['stock', 'crypto', 'currency']);
        $quotationStatuses = (array) config('quotations.statuses', ['valid', 'invalid']);

        return [
            'symbol' => $this->symbolRules('nullable'),
            'type' => ['nullable', Rule::in($assetTypes)],
            'source' => ['nullable', Rule::in($configuredProviders)],
            'status' => ['nullable', Rule::in($quotationStatuses)],
            'include_invalid' => ['nullable', 'boolean'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * Normaliza campos opcionais antes da validacao.
     */
    protected function prepareForValidation(): void
    {
        $this->normalizeOptionalSymbol();
        $this->normalizeBooleanInput('include_invalid');
    }
}
