<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AtualizarUsuarioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nome' => 'string|max:255',
            'email' => 'string|email|max:255',
            'password' => 'string|min:6|confirmed',
            'password_confirmation' => 'string|min:6',
            'status' => 'required|integer|in:0,1',
            'papeis_ids'   => ['required', 'array', 'min:1'],
            'papeis_ids.*' => ['integer', 'distinct', 'exists:papeis,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.string' => 'O nome deve ser uma string.',
            'nome.max' => 'O nome não pode exceder 255 caracteres.',
            'email.string' => 'O email deve ser uma string.',
            'email.email' => 'O email deve ser um endereço de email válido.',
            'email.max' => 'O email não pode exceder 255 caracteres.',
            'password.string' => 'A senha deve ser uma string.',
            'password.min' => 'A senha deve ter no mínimo 6 caracteres.',
            'password.confirmed' => 'A confirmação da senha não corresponde.',
            'required' => 'O campo :attribute é obrigatório',
            'status.in' => 'Valor inválido. Use ATIVO ou INATIVO.',
        ];
    }
}
