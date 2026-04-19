<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $appends = ['image_url', 'mobile_image_url'];

    protected $fillable = [
        'name_en', 'name_ar', 'name_it',
        'role_en', 'role_ar', 'role_it',
        'branch',
        'achievement_en', 'achievement_ar', 'achievement_it',
        'image',
        'image_mobile',
        'focal_point',
        'sort_order',
        'active',
    ];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('active', true);
    }

    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort_order')->orderBy('id');
    }

    /** Return a field in the current locale with fallback to EN. */
    public function localized(string $field, ?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();
        return $this->{$field . '_' . $locale} ?: $this->{$field . '_en'};
    }

    /**
     * Resolve the image path to a full public URL, supporting both:
     *   - Admin-uploaded files  → stored as "uploads/employees/xxx.jpg" (under storage/public)
     *   - Legacy seeded images  → stored as bare "employee-1.jpg"       (under public/images)
     *   - Absolute URLs         → passed through unchanged
     */
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

    /** True if this employee's image is a legacy file in public/images/ (has responsive variants). */
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
