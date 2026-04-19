<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $appends = ['image_url', 'mobile_image_url'];

    protected $fillable = [
        'title_en', 'title_ar', 'title_it',
        'city',
        'type',
        'type_label_en', 'type_label_ar', 'type_label_it',
        'brand',
        'year',
        'image',
        'image_mobile',
        'focal_point',
        'featured',
        'sort_order',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'featured' => 'boolean',
            'active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('active', true);
    }

    public function scopeFeatured(Builder $q): Builder
    {
        return $q->where('featured', true);
    }

    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort_order')->orderBy('id');
    }

    public function localized(string $field, ?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();
        return $this->{$field . '_' . $locale} ?: $this->{$field . '_en'};
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

    public function imageIsLegacy(): bool
    {
        return $this->image && !str_contains($this->image, '/') && !preg_match('#^https?://#i', $this->image);
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
