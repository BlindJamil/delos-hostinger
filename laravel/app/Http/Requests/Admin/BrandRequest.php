<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        $brandId = $this->route('brand')?->id;

        return [
            'name' => ['required', 'string', 'max:120'],
            'slug' => [
                'required', 'string', 'max:120',
                'regex:/^[a-z0-9]+(?:[-_][a-z0-9]+)*$/',
                Rule::unique('brands', 'slug')->ignore($brandId),
            ],
            'category_en' => ['nullable', 'string', 'max:160'],
            'category_ar' => ['nullable', 'string', 'max:160'],
            'category_it' => ['nullable', 'string', 'max:160'],
            'origin_en' => ['nullable', 'string', 'max:160'],
            'origin_ar' => ['nullable', 'string', 'max:160'],
            'origin_it' => ['nullable', 'string', 'max:160'],
            'since' => ['nullable', 'string', 'max:80'],
            'description_en' => ['nullable', 'string', 'max:2000'],
            'description_ar' => ['nullable', 'string', 'max:2000'],
            'description_it' => ['nullable', 'string', 'max:2000'],
            'specialties_en' => ['nullable', 'array'],
            'specialties_en.*' => ['nullable', 'string', 'max:160'],
            'specialties_ar' => ['nullable', 'array'],
            'specialties_ar.*' => ['nullable', 'string', 'max:160'],
            'specialties_it' => ['nullable', 'array'],
            'specialties_it.*' => ['nullable', 'string', 'max:160'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'image_mobile' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'focal_point' => ['nullable', 'string', 'regex:/^\d{1,3}%\s+\d{1,3}%$/'],
            'clear_image_mobile' => ['nullable'],
            'url' => ['nullable', 'url', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'active' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // Strip empty rows from specialty arrays so "null|array" validation
        // doesn't persist a list of nulls.
        foreach (['specialties_en', 'specialties_ar', 'specialties_it'] as $key) {
            $val = $this->input($key);
            if (is_array($val)) {
                $clean = array_values(array_filter(array_map(
                    fn ($v) => is_string($v) ? trim($v) : $v,
                    $val
                ), fn ($v) => $v !== null && $v !== ''));
                $this->merge([$key => $clean ?: null]);
            }
        }

        $this->merge([
            'active' => $this->boolean('active'),
            'sort_order' => $this->input('sort_order', 0),
            'slug' => \Illuminate\Support\Str::slug($this->input('slug', '')),
        ]);
    }
}
