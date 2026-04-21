<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        return [
            // Per-project text fields (title, city, year, brand, type_label)
            // are no longer collected by the admin form — the public cards
            // are image-only and filter chips use `type` instead. Columns
            // stay in the schema for back-compat; rules stay permissive so
            // legacy data isn't rejected when an existing record is re-saved.
            'title_en' => ['nullable', 'string', 'max:180'],
            'title_ar' => ['nullable', 'string', 'max:180'],
            'title_it' => ['nullable', 'string', 'max:180'],
            'city' => ['nullable', 'string', 'max:80'],
            'type' => ['required', 'string', 'max:60'],
            'type_label_en' => ['nullable', 'string', 'max:80'],
            'type_label_ar' => ['nullable', 'string', 'max:80'],
            'type_label_it' => ['nullable', 'string', 'max:80'],
            'brand' => ['nullable', 'string', 'max:80'],
            'year' => ['nullable', 'integer', 'min:1980', 'max:2100'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:20480'],
            'image_mobile' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:20480'],
            'focal_point' => ['nullable', 'string', 'regex:/^\d{1,3}%\s+\d{1,3}%$/'],
            'clear_image_mobile' => ['nullable'],
            'featured' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'active' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'featured' => $this->boolean('featured'),
            'active' => $this->boolean('active'),
            'sort_order' => $this->input('sort_order', 0),
        ]);
    }
}
