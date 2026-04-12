<?php

declare(strict_types=1);

namespace App\Http\Requests\Update;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('user'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'string',
                'email:rfc',
                Rule::unique('users')->ignore($user->id),
                'max:255'
            ],
            'password' => 'sometimes|nullable|string|min:8|confirmed:password_confirmation',
        ];
    }

    public function messages()
    {
        return [
            'first_name.string' => 'O campo nome deve ser um texto.',
            'last_name.string' => 'O campo sobrenome deve ser um texto.',
            'email.string' => 'O campo email deve ser um texto.',
            'first_name.required' => 'O campo nome é obrigatório.',
            'last_name.required' => 'O campo sobrenome é obrigatório.',
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'O campo email deve ser um endereço de email válido.',
            'email.unique' => 'O email já está em uso.',
            'password.required' => 'O campo senha é obrigatório.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'password.confirmed' => 'A confirmação da senha não corresponde.',
            'password_confirmation.required' => 'O campo de confirmação de senha é obrigatório.',
            'password_confirmation.confirmed' => 'A confirmação da senha não corresponde.',
        ];
    }
}
