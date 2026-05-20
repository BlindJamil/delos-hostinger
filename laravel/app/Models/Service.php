<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $appends = ['image_url', 'mobile_image_url'];

    protected $fillable = [
        'num',
        'slug',
        'name_en', 'name_ar', 'name_it', 'name_ku',
        'description_en', 'description_ar', 'description_it', 'description_ku',
        'features_en', 'features_ar', 'features_it', 'features_ku',
        'brand',
        'image',
        'image_mobile',
        'focal_point',
        'sort_order',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'features_en' => 'array',
            'features_ar' => 'array',
            'features_it' => 'array',
            'features_ku' => 'array',
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
