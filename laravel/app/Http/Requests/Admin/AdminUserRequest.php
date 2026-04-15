<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only super admins can create/modify other admins.
        return auth('admin')->check() && auth('admin')->user()->is_super;
    }

    public function rules(): array
    {
        $id = $this->route('admin_user')?->id;
        $isCreate = $this->isMethod('POST');

        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => [
                'required', 'email', 'max:191',
                Rule::unique('admin_users', 'email')->ignore($id),
            ],
            'password' => [
                $isCreate ? 'required' : 'nullable',
                'string',
                Password::min(12)
                    ->letters()
                    ->numbers()
                    ->symbols(),
                'confirmed',
            ],
            'is_super' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_super' => $this->boolean('is_super'),
        ]);
    }
}
