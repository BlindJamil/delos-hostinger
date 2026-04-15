<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = [
        'key',
        'group',
        'type',
        'label',
        'value_en',
        'value_ar',
        'value_it',
        'sort_order',
    ];

    /**
     * Get a value for the current locale (or specified locale), with fallback
     * to English. Cached as a plain assoc-array so the cache never holds
     * serialized Eloquent models (which can't be unserialized safely if the
     * class shape changes between requests).
     */
    public static function value(string $key, ?string $locale = null, string $default = ''): string
    {
        $locale ??= app()->getLocale();

        $values = Cache::remember(
            "site_setting:{$key}",
            now()->addMinutes(10),
            function () use ($key) {
                $setting = static::where('key', $key)->first();
                return $setting ? [
                    'value_en' => $setting->value_en,
                    'value_ar' => $setting->value_ar,
                    'value_it' => $setting->value_it,
                ] : null;
            }
        );

        if (!$values) {
            return $default;
        }

        return ($values["value_{$locale}"] ?? null) ?: ($values['value_en'] ?: $default);
    }

    public static function clearCache(string $key): void
    {
        Cache::forget("site_setting:{$key}");
    }

    protected static function booted(): void
    {
        static::saved(fn ($setting) => static::clearCache($setting->key));
        static::deleted(fn ($setting) => static::clearCache($setting->key));
    }
}
