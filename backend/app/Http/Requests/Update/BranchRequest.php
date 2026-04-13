<?php

namespace App\Http\Requests\Update;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('branch'));
    }

    public function rules(): array
    {
        $branchId = $this->route('branch')->id;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug_name' => ['sometimes', 'required', 'string', 'max:255'],
            'corporate_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'document' => ['sometimes', 'required', 'string', 'max:20', Rule::unique('branches', 'document')->ignore($branchId)],
            'state_registration' => ['sometimes', 'nullable', 'string', 'max:50'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:20'],
            'zip_code' => ['sometimes', 'nullable', 'string', 'max:10'],
            'street' => ['sometimes', 'nullable', 'string', 'max:255'],
            'number' => ['sometimes', 'nullable', 'string', 'max:20'],
            'complement' => ['sometimes', 'nullable', 'string', 'max:255'],
            'district' => ['sometimes', 'nullable', 'string', 'max:255'],
            'city' => ['sometimes', 'nullable', 'string', 'max:255'],
            'state' => ['sometimes', 'nullable', 'string', 'size:2'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'string' => 'O campo :attribute deve ser um texto.',
            'max' => 'O campo :attribute não pode ter mais de :max caracteres.',
            'email' => 'O campo :attribute deve ser um e-mail válido.',
            'unique' => 'O campo :attribute já está em uso.',
            'size' => 'O campo :attribute deve ter :size caracteres.',
            'boolean' => 'O campo :attribute deve ser verdadeiro ou falso.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('name')) {
            $name = $this->input('name');

            if (is_string($name)) {
                $this->merge([
                    'slug_name' => Str::slug($name),
                ]);
            }
        }
    }
}
