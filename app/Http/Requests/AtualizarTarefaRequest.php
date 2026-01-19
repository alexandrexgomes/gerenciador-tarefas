<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class AtualizarTarefaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'completed' => ['sometimes', 'boolean'],
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Este campo é obrigatório',
            'email' => 'O campo :attribute deve conter um endereço de e-mail válido.',
            "email.unique" => "Este endereço de e-mail já esta em uso",
            'max' => 'O :attribute não pode ser maior que :max.',
            'min' => 'O :attribute deve conter no mínimo :min caracteres',
            'confirmed' => 'A confirmação da senha não corresponde.',
            'accepted' => 'Você deve concordar com os termos e condições antes de enviar.'
        ];
    }
}
