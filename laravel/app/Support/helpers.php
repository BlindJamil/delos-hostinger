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

if (!function_exists('brand_count')) {
    /**
     * Live count of active brands, used to derive the "brands" stat counter
     * on the homepage and about page. Single source of truth is the Brands
     * admin CRUD — adding/removing or toggling `active` updates every
     * counter site-wide automatically.
     *
     * Defensive: returns 0 if the brands table doesn't exist yet (fresh
     * install, console commands pre-migration).
     */
    function brand_count(): int
    {
        try {
            return \App\Models\Brand::active()->count();
        } catch (\Throwable) {
            return 0;
        }
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
            return $default ?? pcontent_lang_resolve($key);
        }

        try {
            $value = PageContent::value($key);
            if ($value !== null && $value !== '') {
                return $value;
            }
        } catch (\Throwable) {
            // Fall through to default/lang fallback on any DB failure.
        }

        return $default ?? pcontent_lang_resolve($key);
    }
}

if (!function_exists('pcontent_optional')) {
    /**
     * Like pcontent() but returns null (instead of the raw key string)
     * when nothing is configured for this key in the DB OR lang files.
     *
     * Useful for optional sibling fields — e.g. "home.hero.slides.0.img_mobile"
     * — where the caller wants to conditionally render only when a value
     * actually exists, not when the resolver fell through to echoing the
     * key itself as a placeholder.
     */
    function pcontent_optional(string $key): ?string
    {
        $value = pcontent($key, null);
        // If pcontent() returned the raw key, the resolver found nothing
        // — treat that as "unset".
        if ($value === $key || $value === '') {
            return null;
        }
        return $value;
    }
}

if (!function_exists('pcontent_lang_resolve')) {
    /**
     * Resolve a dotted lang key using direct file inclusion + data_get().
     *
     * Laravel's __() / Lang::get() cannot resolve numeric array indices
     * in dot paths (e.g. 'home.stats.items.0.value' returns the key
     * itself). This helper loads the raw PHP array from the lang file
     * and uses data_get() which handles numeric indices correctly.
     *
     * Falls back to __($key) for non-numeric keys (which work fine in
     * Laravel's translator and benefit from its caching).
     */
    function pcontent_lang_resolve(string $key): string
    {
        // Fast path: try __() first. If it returns something other than
        // the key, it resolved correctly — no need for the file dance.
        $translated = __($key);
        if ($translated !== $key) {
            return is_string($translated) ? $translated : $key;
        }

        // __() returned the key itself. Likely a numeric index issue.
        // Load the raw lang array and try data_get().
        $dotPos = strpos($key, '.');
        if ($dotPos === false) {
            return $key;
        }

        $group = substr($key, 0, $dotPos);
        $path = substr($key, $dotPos + 1);
        $locale = app()->getLocale();

        static $fileCache = [];
        $cacheKey = "{$locale}/{$group}";
        if (!isset($fileCache[$cacheKey])) {
            $file = lang_path("{$locale}/{$group}.php");
            $fileCache[$cacheKey] = file_exists($file) ? include $file : [];
        }

        $value = data_get($fileCache[$cacheKey], $path);

        if ($value !== null && !is_array($value)) {
            return (string) $value;
        }

        return $key;
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

if (!function_exists('delos_commit_marker')) {
    /**
     * Resolve the current deploy's commit hash + build timestamp as a short
     * HTML-safe string, e.g. "commit:a3f0c21 built:2026-04-19T08:30:00Z".
     *
     * Emitted as an HTML comment at the top of every rendered page so we can
     * `curl` the public site and immediately know which code is live. Without
     * this marker there's no way to tell whether a Hostinger deploy has
     * actually landed — every cache-bust attempt becomes a guess.
     *
     * Memoized per request. Reads .git/HEAD when available (dev); falls back
     * to public/index.php mtime on shared hosting where .git is pruned.
     */
    function delos_commit_marker(): string
    {
        static $cached = null;
        if ($cached !== null) return $cached;

        $commit = 'nogit';
        $head = base_path('.git/HEAD');
        if (is_file($head)) {
            $ref = trim(@file_get_contents($head) ?: '');
            if (str_starts_with($ref, 'ref: ')) {
                $refFile = base_path('.git/' . substr($ref, 5));
                if (is_file($refFile)) {
                    $commit = substr(trim(@file_get_contents($refFile) ?: ''), 0, 7) ?: 'nogit';
                }
            } elseif ($ref !== '') {
                $commit = substr($ref, 0, 7);
            }
        }

        $indexPhp = public_path('index.php');
        $built = is_file($indexPhp) ? gmdate('Y-m-d\TH:i:s\Z', filemtime($indexPhp)) : 'unknown';

        return $cached = "commit:{$commit} built:{$built}";
    }
}

if (!function_exists('wa_digits')) {
    /**
     * Normalise an Iraqi phone number to the digit-only form wa.me expects.
     * Examples:
     *   "+964 750 200 1003"  → "9647502001003"
     *   "00964 750 200 1003" → "9647502001003"
     *   "0750 200 1003"      → "9647502001003"
     *   "750-200-1003"       → "9647502001003"
     * Returns null on empty/obviously-too-short input so the caller can
     * detect a branch that hasn't had its WhatsApp number set yet.
     */
    function wa_digits(?string $phone): ?string
    {
        if (!$phone) return null;
        $d = preg_replace('/\D+/', '', $phone);
        if (strlen($d) < 8) return null;
        if (str_starts_with($d, '00')) $d = substr($d, 2);      // drop int'l "00" prefix
        if (str_starts_with($d, '0'))  $d = '964' . substr($d, 1);
        if (!str_starts_with($d, '964')) $d = '964' . $d;
        return $d;
    }
}
