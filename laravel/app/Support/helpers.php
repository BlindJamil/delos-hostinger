<?php

use App\Support\LocaleResolver;

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
