<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $appends = ['image_url', 'mobile_image_url', 'directions_href'];

    protected $fillable = [
        'slug',
        'city_key',
        'name_en', 'name_ar', 'name_it',
        'address_en', 'address_ar', 'address_it',
        'hours_en', 'hours_ar', 'hours_it',
        'established_en', 'established_ar', 'established_it',
        'phone', 'whatsapp', 'email',
        'latitude', 'longitude',
        'directions_url',
        'image',
        'image_mobile',
        'focal_point',
        'is_flagship',
        'sort_order',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'is_flagship' => 'boolean',
            'active' => 'boolean',
            'latitude' => 'float',
            'longitude' => 'float',
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

    public function scopeWithCoordinates(Builder $q): Builder
    {
        return $q->whereNotNull('latitude')->whereNotNull('longitude');
    }

    /** Return a field in the current locale with fallback to EN. */
    public function localized(string $field, ?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();
        return $this->{$field . '_' . $locale} ?: $this->{$field . '_en'};
    }

    /**
     * Resolve the showroom photo path — same pattern as Employee/Project.
     * Supports: admin-uploaded (uploads/branches/xxx.jpg), legacy bare filename
     * in public/images, and absolute URLs. Returns null when no image set so
     * the public card can fall back to the letter-watermark design.
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

    /**
     * Deep link for the "Get Directions" button. Order of precedence:
     *   1. If admin set a curated directions_url, use it verbatim.
     *   2. Else build a geo: URI from lat/lng — iOS/Android respect it and
     *      open the user's preferred maps app. Desktop browsers fall through
     *      to Google Maps via the https fallback.
     *   3. Else return null (the button won't render).
     */
    protected function directionsHref(): Attribute
    {
        return Attribute::get(function () {
            if ($this->directions_url) {
                return $this->directions_url;
            }
            if ($this->latitude && $this->longitude) {
                $query = urlencode($this->localized('name') . ', ' . $this->localized('address'));
                // https:// opens Google Maps on desktop; iOS Safari deep-links
                // into Apple Maps for "maps.google.com" too.
                return "https://www.google.com/maps/search/?api=1&query={$this->latitude},{$this->longitude}&query_place_id={$query}";
            }
            return null;
        });
    }
}
