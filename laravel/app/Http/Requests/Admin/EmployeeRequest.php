<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        return [
            'name_en' => ['required', 'string', 'max:120'],
            'name_ar' => ['nullable', 'string', 'max:120'],
            'name_it' => ['nullable', 'string', 'max:120'],
            'role_en' => ['required', 'string', 'max:160'],
            'role_ar' => ['nullable', 'string', 'max:160'],
            'role_it' => ['nullable', 'string', 'max:160'],
            'branch' => ['nullable', 'string', 'max:80'],
            'achievement_en' => ['nullable', 'string', 'max:1000'],
            'achievement_ar' => ['nullable', 'string', 'max:1000'],
            'achievement_it' => ['nullable', 'string', 'max:1000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:20480'],
            'image_mobile' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:20480'],
            'focal_point' => ['nullable', 'string', 'regex:/^\d{1,3}%\s+\d{1,3}%$/'],
            'clear_image_mobile' => ['nullable'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'active' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'active' => $this->boolean('active'),
            'sort_order' => $this->input('sort_order', 0),
        ]);
    }
}
