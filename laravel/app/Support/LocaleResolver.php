<?php

namespace App\Support;

use Illuminate\Http\Request;

class LocaleResolver
{
    public const SUPPORTED = ['en', 'ar', 'it'];
    public const DEFAULT_LOCALE = 'en';
    public const COOKIE_NAME = 'delos_locale';
    public const SEEN_COOKIE_NAME = 'delos_locale_seen';

    public function resolve(?Request $request = null): string
    {
        $request ??= request();

        $fromRoute = $request->route('locale');
        if (is_string($fromRoute) && in_array($fromRoute, self::SUPPORTED, true)) {
            return $fromRoute;
        }

        $fromCookie = $request->cookie(self::COOKIE_NAME);
        if (is_string($fromCookie) && in_array($fromCookie, self::SUPPORTED, true)) {
            return $fromCookie;
        }

        $fromHeader = $this->matchAcceptLanguage($request->header('Accept-Language'));
        if ($fromHeader !== null) {
            return $fromHeader;
        }

        return self::DEFAULT_LOCALE;
    }

    public function isRtl(string $locale): bool
    {
        return $locale === 'ar';
    }

    public function dir(string $locale): string
    {
        return $this->isRtl($locale) ? 'rtl' : 'ltr';
    }

    /**
     * Build a per-locale URL map for the current request path.
     * Returns ['en' => '/en/services', 'ar' => '/ar/services', 'it' => '/it/services'].
     */
    public function urlsForPath(string $currentPath): array
    {
        $pathWithoutLocale = preg_replace('#^(en|ar|it)(/|$)#', '', $currentPath);
        $pathWithoutLocale = trim((string) $pathWithoutLocale, '/');

        $map = [];
        foreach (self::SUPPORTED as $locale) {
            $map[$locale] = $pathWithoutLocale === ''
                ? "/{$locale}"
                : "/{$locale}/{$pathWithoutLocale}";
        }

        return $map;
    }

    /**
     * Parse Accept-Language header and return the best-matching supported locale.
     * Handles q-values: "ar;q=0.9,en;q=0.8,it;q=0.5".
     */
    private function matchAcceptLanguage(?string $header): ?string
    {
        if (empty($header)) {
            return null;
        }

        $candidates = [];
        foreach (explode(',', $header) as $entry) {
            $entry = trim($entry);
            if ($entry === '') {
                continue;
            }

            $quality = 1.0;
            $tag = $entry;

            if (str_contains($entry, ';')) {
                [$tag, $params] = array_map('trim', explode(';', $entry, 2));
                if (preg_match('/q=([0-9.]+)/', $params, $m)) {
                    $quality = (float) $m[1];
                }
            }

            // Normalize tag: "en-US" -> "en", "ar-IQ" -> "ar"
            $primary = strtolower(substr($tag, 0, 2));
            if (in_array($primary, self::SUPPORTED, true)) {
                // Keep highest quality wins
                if (!isset($candidates[$primary]) || $candidates[$primary] < $quality) {
                    $candidates[$primary] = $quality;
                }
            }
        }

        if (empty($candidates)) {
            return null;
        }

        arsort($candidates);
        return array_key_first($candidates);
    }
}
