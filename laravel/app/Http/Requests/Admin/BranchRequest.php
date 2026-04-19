<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('admin')->check();
    }

    public function rules(): array
    {
        $id = $this->route('branch')?->id;

        return [
            'city_key' => [
                'required', 'string', 'max:40',
                'regex:/^[a-z0-9]+(?:[-_][a-z0-9]+)*$/',
                Rule::unique('branches', 'city_key')->ignore($id),
            ],
            'slug' => [
                'required', 'string', 'max:60',
                'regex:/^[a-z0-9]+(?:[-_][a-z0-9]+)*$/',
                Rule::unique('branches', 'slug')->ignore($id),
            ],
            'name_en' => ['required', 'string', 'max:120'],
            'name_ar' => ['nullable', 'string', 'max:120'],
            'name_it' => ['nullable', 'string', 'max:120'],
            'address_en' => ['nullable', 'string', 'max:255'],
            'address_ar' => ['nullable', 'string', 'max:255'],
            'address_it' => ['nullable', 'string', 'max:255'],
            'hours_en' => ['nullable', 'string', 'max:120'],
            'hours_ar' => ['nullable', 'string', 'max:120'],
            'hours_it' => ['nullable', 'string', 'max:120'],
            'established_en' => ['nullable', 'string', 'max:40'],
            'established_ar' => ['nullable', 'string', 'max:40'],
            'established_it' => ['nullable', 'string', 'max:40'],
            'phone' => ['nullable', 'string', 'max:60'],
            'whatsapp' => ['nullable', 'string', 'max:60'],
            'email' => ['nullable', 'email', 'max:120'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'directions_url' => ['nullable', 'url', 'max:500'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'image_mobile' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'focal_point' => ['nullable', 'string', 'regex:/^\d{1,3}%\s+\d{1,3}%$/'],
            'clear_image_mobile' => ['nullable'],
            'is_flagship' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'active' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_flagship' => $this->boolean('is_flagship'),
            'active' => $this->boolean('active'),
            'sort_order' => $this->input('sort_order', 0),
            'city_key' => Str::slug($this->input('city_key', '')),
            'slug' => Str::slug($this->input('slug', '')),
        ]);
    }
}
