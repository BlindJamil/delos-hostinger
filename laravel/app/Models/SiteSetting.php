<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

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

        // Guard against a fresh deploy where the site_settings table
        // hasn't been migrated yet — the view composer calls value() on
        // every rendered view, so an unhandled exception here 500s the
        // whole public site until the admin runs migrations.
        static $tableExists = null;
        if ($tableExists === null) {
            try {
                $tableExists = Schema::hasTable('site_settings');
            } catch (\Throwable) {
                $tableExists = false;
            }
        }
        if (!$tableExists) {
            return $default;
        }

        $values = Cache::remember(
            "site_setting:{$key}",
            now()->addMinutes(10),
            function () use ($key) {
                try {
                    $setting = static::where('key', $key)->first();
                } catch (\Throwable) {
                    return null;
                }
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
