<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CriarTarefaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string']
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Este campo é obrigatório',
            'max' => 'O :attribute não pode ser maior que :max.',
            'min' => 'O :attribute deve conter no mínimo :min caracteres',
        ];
    }
}
