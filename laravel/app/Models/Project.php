<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $appends = ['image_url', 'mobile_image_url'];

    protected $fillable = [
        'title_en', 'title_ar', 'title_it', 'title_ku',
        'city',
        'type',
        'type_label_en', 'type_label_ar', 'type_label_it', 'type_label_ku',
        'brand',
        'year',
        'image',
        'image_mobile',
        'focal_point',
        'description_en', 'description_ar', 'description_it', 'description_ku',
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

    /**
     * Canonicalize the room-type key on read so legacy rows that drifted in
     * casing / whitespace (e.g. "Living Room " vs "living room") collapse
     * into a single bucket for groupBy + filter matching. Writes remain the
     * admin's responsibility — the admin controller normalizes before save.
     */
    protected function type(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value === null
                ? null
                : (mb_strtolower(trim((string) preg_replace('/\s+/u', ' ', $value))) ?: null),
        );
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

    /** Gallery images for the public detail page, in admin-defined order. */
    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    /**
     * Content gate for the public detail page. True as soon as the admin has
     * added EITHER a description for the current locale OR one gallery image.
     *
     * Computed, never stored, so it can't drift from the actual content. Used
     * in three places: the card link in projects.blade.php, the 404 guard in
     * PageController::showProject(), and the admin index badge.
     */
    public function hasDetailContent(?string $locale = null): bool
    {
        return $this->hasDescriptionText($locale) || $this->galleryImageCount() > 0;
    }

    /**
     * The admin's rich-text editor serialises an empty document as "<p></p>"
     * — truthy, but visually blank. Strip markup and non-breaking spaces so
     * an untouched editor correctly counts as "no description".
     */
    public function hasDescriptionText(?string $locale = null): bool
    {
        $html = (string) $this->localized('description', $locale);
        if ($html === '') {
            return false;
        }
        $plain = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(str_replace("\u{00A0}", ' ', $plain)) !== '';
    }

    /**
     * Gallery size without triggering a query per card. Prefers withCount()'s
     * images_count, then an eager-loaded relation, and only falls back to a
     * COUNT query when neither was primed — which is what keeps the projects
     * grid at one query instead of N.
     */
    private function galleryImageCount(): int
    {
        if (array_key_exists('images_count', $this->attributes)) {
            return (int) $this->attributes['images_count'];
        }
        if ($this->relationLoaded('images')) {
            return $this->images->count();
        }

        return $this->images()->count();
    }
}
