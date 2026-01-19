<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class PaginateTarefaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page'         => ['nullable', 'integer', 'min:1'],
            'per_page'     => ['nullable', 'integer', 'min:1', 'max:100'],
            'busca'        => ['nullable', 'string'],
            'completed'    => ['nullable', 'in:0,1'],
            'created_from' => ['nullable', 'date_format:Y-m-d'],
            'created_to'   => ['nullable', 'date_format:Y-m-d', 'after_or_equal:created_from'],
        ];
    }

    public function messages()
    {
        return [
            'page.integer' => 'O campo page deve ser um número inteiro.',
            'page.min'     => 'O campo page deve ser no mínimo 1.',

            'per_page.integer' => 'O campo per_page deve ser um número inteiro.',
            'per_page.min'     => 'O campo per_page deve ser no mínimo 1.',
            'per_page.max'     => 'O campo per_page não pode ser maior que 100.',

            'completed.in' => 'O campo completed deve ser 0 ou 1.',

            'created_from.date_format' => 'A data inicial deve estar no formato YYYY-MM-DD.',
            'created_to.date_format'   => 'A data final deve estar no formato YYYY-MM-DD.',
            'created_to.after_or_equal' => 'A data final deve ser maior ou igual à data inicial.',
        ];
    }
}
