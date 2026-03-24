<?php

namespace App\Http\Requests\Store;

use App\Models\Branch;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class BranchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('create', Branch::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'               => ['required', 'string', 'max:255'],
            'slug_name'          => ['required', 'string', 'max:255'],
            'corporate_name'     => ['nullable', 'string', 'max:255'],
            'document'           => ['required', 'string', 'max:20', 'unique:branches,document'],
            'state_registration' => ['nullable', 'string', 'max:50'],
            'email'              => ['nullable', 'email', 'max:255'],
            'phone'              => ['nullable', 'string', 'max:20'],
            'zip_code'           => ['nullable', 'string', 'max:10'],
            'street'             => ['nullable', 'string', 'max:255'],
            'number'             => ['nullable', 'string', 'max:20'],
            'complement'         => ['nullable', 'string', 'max:255'],
            'district'           => ['nullable', 'string', 'max:255'],
            'city'               => ['nullable', 'string', 'max:255'],
            'state'              => ['nullable', 'string', 'size:2'],
            'is_active'          => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'required'    => 'O campo :attribute é obrigatório.',
            'string'      => 'O campo :attribute deve ser um texto.',
            'max'         => 'O campo :attribute não pode ter mais de :max caracteres.',
            'email'       => 'O campo :attribute deve ser um e-mail válido.',
            'unique'      => 'O campo :attribute já está em uso.',
            'size'        => 'O campo :attribute deve ter :size caracteres.',
            'boolean'     => 'O campo :attribute deve ser verdadeiro ou falso.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $name = $this->input('name');

        if (is_string($name)) {
            $slugSource = $name;
        } elseif (is_scalar($name) || is_null($name)) {
            $slugSource = (string) ($name ?? '');
        } else {
            $slugSource = '';
        }

        $this->merge([
            'slug_name' => Str::slug($slugSource),
        ]);
    }
}
