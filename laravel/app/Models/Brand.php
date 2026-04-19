<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $appends = ['image_url', 'mobile_image_url'];

    protected $fillable = [
        'name',
        'slug',
        'category_en', 'category_ar', 'category_it',
        'origin_en', 'origin_ar', 'origin_it',
        'since',
        'description_en', 'description_ar', 'description_it',
        'specialties_en', 'specialties_ar', 'specialties_it',
        'image',
        'image_mobile',
        'focal_point',
        'url',
        'sort_order',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'specialties_en' => 'array',
            'specialties_ar' => 'array',
            'specialties_it' => 'array',
        ];
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('active', true);
    }

    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort_order')->orderBy('id');
    }

    public function localized(string $field, ?string $locale = null)
    {
        $locale ??= app()->getLocale();
        $value = $this->{$field . '_' . $locale};
        return $value ?: $this->{$field . '_en'};
    }

    /** Legacy filename in public/images vs admin-uploaded file in storage. */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(function () {
            $path = $this->image;
            if (!$path) {
                return null;
            }
            if (preg_match('#^https?://#i', $path)) {
                return $path;
            }
            if (str_starts_with($path, 'uploads/') || str_contains($path, '/')) {
                return asset('storage/' . ltrim($path, '/'));
            }
            return asset('images/' . $path);
        });
    }

    /** Mobile variant (phones ≤ 767px). Same resolution rules as imageUrl(). */
    protected function mobileImageUrl(): Attribute
    {
        return Attribute::get(function () {
            $path = $this->image_mobile;
            if (!$path) {
                return null;
            }
            if (preg_match('#^https?://#i', $path)) {
                return $path;
            }
            if (str_starts_with($path, 'uploads/') || str_contains($path, '/')) {
                return asset('storage/' . ltrim($path, '/'));
            }
            return asset('images/' . $path);
        });
    }
}
