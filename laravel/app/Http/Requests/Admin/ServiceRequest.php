<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        $serviceId = $this->route('service')?->id;

        return [
            'num' => ['nullable', 'string', 'max:10'],
            'slug' => [
                'required', 'string', 'max:120',
                'regex:/^[a-z0-9]+(?:[-_][a-z0-9]+)*$/',
                Rule::unique('services', 'slug')->ignore($serviceId),
            ],
            'name_en' => ['required', 'string', 'max:160'],
            'name_ar' => ['nullable', 'string', 'max:160'],
            'name_it' => ['nullable', 'string', 'max:160'],
            'description_en' => ['nullable', 'string', 'max:2000'],
            'description_ar' => ['nullable', 'string', 'max:2000'],
            'description_it' => ['nullable', 'string', 'max:2000'],
            'features_en' => ['nullable', 'array'],
            'features_en.*' => ['nullable', 'string', 'max:200'],
            'features_ar' => ['nullable', 'array'],
            'features_ar.*' => ['nullable', 'string', 'max:200'],
            'features_it' => ['nullable', 'array'],
            'features_it.*' => ['nullable', 'string', 'max:200'],
            'brand' => ['nullable', 'string', 'max:160'],
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
        foreach (['features_en', 'features_ar', 'features_it'] as $key) {
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
