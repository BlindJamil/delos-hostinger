<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Admin-editable overrides for lang-file strings and media paths.
 *
 * Runtime lookup goes through a SINGLE cache key holding the entire table
 * as a plain `[key => [value_en, value_ar, value_it]]` array. The payload
 * is ~600 small strings (200 keys × 3 locales) — well under 100KB
 * serialized. File-cache-driver safe, no tags needed.
 *
 * The cache busts on any save/delete via the booted() hook, so admin
 * edits are reflected on the public site immediately.
 *
 * Design rationale (mirrors SiteSetting::value()):
 * - One cache read per request covers ALL page-content lookups.
 * - No per-key Redis GETs or file reads.
 * - Cache stores array, NOT Eloquent models — avoids serialization bugs
 *   when the model class shape changes between deploys.
 */
class PageContent extends Model
{
    protected $fillable = [
        'key', 'page', 'section', 'sort_order', 'type',
        'value_en', 'value_ar', 'value_it',
    ];

    protected function casts(): array
    {
        return ['sort_order' => 'integer'];
    }

    public const CACHE_KEY = 'page_content.all';
    public const CACHE_TTL_MINUTES = 60;

    /**
     * Fetch the override value for a key in the given locale.
     *
     * Returns null if no DB row exists. Callers (the `pcontent()` helper)
     * are responsible for the lang-file fallback.
     *
     * Fallback chain within a row:
     *   value_{locale} → value_en → null
     */
    public static function value(string $key, ?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();

        $rows = Cache::remember(
            self::CACHE_KEY,
            now()->addMinutes(self::CACHE_TTL_MINUTES),
            fn () => static::query()->get(['key', 'value_en', 'value_ar', 'value_it'])
                ->mapWithKeys(fn ($r) => [$r->key => [
                    'en' => $r->value_en,
                    'ar' => $r->value_ar,
                    'it' => $r->value_it,
                ]])->all()
        );

        if (!isset($rows[$key])) {
            return null;
        }

        return $rows[$key][$locale]
            ?? $rows[$key]['en']
            ?? null;
    }

    /**
     * Bust the bulk cache. Called on every save/delete automatically via
     * the booted() hook, but also exposed publicly so CLI commands or
     * the seeder can bust manually after bulk inserts.
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    protected static function booted(): void
    {
        static::saved(fn () => static::clearCache());
        static::deleted(fn () => static::clearCache());
    }
}
