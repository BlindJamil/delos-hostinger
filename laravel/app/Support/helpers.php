<?php

use App\Models\PageContent;
use App\Support\LocaleResolver;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

if (!function_exists('lroute')) {
    /**
     * Generate a URL for a locale-prefixed named route.
     * Automatically injects the current locale into the {locale} parameter,
     * so blade can call lroute('services') without worrying about it.
     *
     * @param  string  $name  The base route name, e.g. 'services' (without 'l.' prefix)
     * @param  array   $parameters
     * @param  string|null  $locale  Override locale; defaults to current app locale
     * @param  bool  $absolute
     */
    function lroute(string $name, array $parameters = [], ?string $locale = null, bool $absolute = true): string
    {
        $locale ??= app()->getLocale();
        if (!in_array($locale, LocaleResolver::SUPPORTED, true)) {
            $locale = LocaleResolver::DEFAULT_LOCALE;
        }

        return route('l.' . $name, array_merge(['locale' => $locale], $parameters), $absolute);
    }
}

if (!function_exists('locale_url')) {
    /**
     * Build a URL to the current page in a different locale.
     * Used by the language switcher.
     */
    function locale_url(string $locale): string
    {
        $urls = app(LocaleResolver::class)->urlsForPath(request()->path());
        return url($urls[$locale] ?? '/');
    }
}

if (!function_exists('pcontent')) {
    /**
     * Resolve an admin-editable page content override for the given lang key,
     * falling back to the lang file value if no override exists.
     *
     * Blade usage:
     *     {{ pcontent('home.hero.heading_accent') }}
     *
     * The helper is exception-safe at every layer:
     *   - If the `page_contents` table doesn't exist yet (fresh install,
     *     pre-migration, console commands), fall back to __($key).
     *   - If a DB lookup throws (connection lost, etc.), fall back to __($key).
     *   - The Schema::hasTable() check is memoized in a static so we don't
     *     hit information_schema on every call.
     *
     * @param  string  $key
     * @param  string|null  $default  Optional explicit fallback. If null,
     *                                falls through to __($key) which returns
     *                                the lang file value or the raw key.
     */
    function pcontent(string $key, ?string $default = null): string
    {
        static $tableExists = null;

        if ($tableExists === null) {
            try {
                $tableExists = Schema::hasTable('page_contents');
            } catch (\Throwable) {
                $tableExists = false;
            }
        }

        if (!$tableExists) {
            return $default ?? __($key);
        }

        try {
            $value = PageContent::value($key);
            if ($value !== null && $value !== '') {
                return $value;
            }
        } catch (\Throwable) {
            // Fall through to default/lang fallback on any DB failure.
        }

        return $default ?? __($key);
    }
}

if (!function_exists('pcontent_url')) {
    /**
     * Resolve an admin-uploaded media asset (image or video) to a public URL.
     *
     * If the stored override starts with "uploads/", it's an admin-uploaded
     * file in the public storage disk — map to the storage URL. Otherwise
     * treat the value as a free-form URL (e.g. a YouTube embed URL) and
     * return it verbatim.
     *
     * The $fallback is used when no override exists AT ALL (no DB row) —
     * typically the original hardcoded path like asset('videos/delos-brand.mp4').
     *
     * Blade usage:
     *     <video src="{{ pcontent_url('home.video.source', asset('videos/delos-brand.mp4')) }}">
     *
     * @param  string       $key
     * @param  string|null  $fallback  Fallback URL if no override is set.
     */
    function pcontent_url(string $key, ?string $fallback = null): ?string
    {
        $value = pcontent($key, '');

        if ($value === '' || $value === $key) {
            // pcontent() returns the raw key when nothing resolved (no DB
            // row AND no matching lang key). Use the caller's fallback.
            return $fallback;
        }

        if (str_starts_with($value, 'uploads/')) {
            // asset() resolves off the current request host, which works in
            // dev where APP_URL is "http://localhost" but the server runs
            // on a non-80 port. Storage::url() hardcodes APP_URL's host.
            return asset('storage/' . $value);
        }

        if (preg_match('#^https?://#i', $value)) {
            return $value;
        }

        // Anything else (raw filename, legacy path) — let the caller's
        // fallback handle it so we don't produce a broken URL.
        return $fallback;
    }
}
