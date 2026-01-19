<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistrarUsuarioRequest extends FormRequest
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
            'nome'  => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email', 'max:150'],
            'password' => ['required', 'string', 'min:6', 'max:64'],
            'papeis_ids'   => ['required', 'array', 'min:1'],
            'papeis_ids.*' => ['integer', 'distinct', 'exists:papeis,id'],
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
