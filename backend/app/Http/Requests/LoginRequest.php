<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email:rfc,dns|string|min:10|max:255',
            'password' => 'required|string|min:8',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'O email é obrigatório.',
            'email.email' => 'O email deve ser um endereço de email válido.',
            'email.min' => 'O email deve ter no mínimo 10 caracteres.',
            'email.max' => 'O email não pode exceder 255 caracteres.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
        ];
    }
}
