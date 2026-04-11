<?php

namespace App\Http\Middleware;

use App\Support\LocaleResolver;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Cookie;

class SetLocale
{
    public function __construct(private readonly LocaleResolver $resolver) {}

    public function handle(Request $request, Closure $next)
    {
        $locale = $this->resolver->resolve($request);
        App::setLocale($locale);

        $localeUrls = $this->resolver->urlsForPath($request->path());
        $hasLocaleCookie = $request->cookie(LocaleResolver::COOKIE_NAME) !== null;
        $hasSeenCookie = $request->cookie(LocaleResolver::SEEN_COOKIE_NAME) !== null;

        View::share([
            'locale' => $locale,
            'isRtl' => $this->resolver->isRtl($locale),
            'dir' => $this->resolver->dir($locale),
            'supportedLocales' => LocaleResolver::SUPPORTED,
            'localeUrls' => $localeUrls,
            'showLangPicker' => !$hasSeenCookie,
        ]);

        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $next($request);

        // Slide the locale cookie forward on every request (1-year sliding window).
        // Not HttpOnly — client JS reads it to sync localStorage.
        $oneYearMinutes = 60 * 24 * 365;
        $response->headers->setCookie(
            Cookie::create(
                name: LocaleResolver::COOKIE_NAME,
                value: $locale,
                expire: time() + ($oneYearMinutes * 60),
                path: '/',
                domain: null,
                secure: $request->isSecure(),
                httpOnly: false,
                raw: false,
                sameSite: Cookie::SAMESITE_LAX
            )
        );

        return $response;
    }
}
