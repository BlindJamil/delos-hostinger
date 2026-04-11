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

        // Slide both locale cookies forward on every request (1-year sliding
        // window). Not HttpOnly — client JS reads them to sync localStorage
        // and to persist the locale choice before navigation.
        $oneYearSeconds = 60 * 60 * 24 * 365;
        $expireAt = time() + $oneYearSeconds;
        $isSecure = $request->isSecure();

        // Always refresh delos_locale to the current resolved value.
        $response->headers->setCookie(
            Cookie::create(
                name: LocaleResolver::COOKIE_NAME,
                value: $locale,
                expire: $expireAt,
                path: '/',
                domain: null,
                secure: $isSecure,
                httpOnly: false,
                raw: false,
                sameSite: Cookie::SAMESITE_LAX
            )
        );

        // Stamp delos_locale_seen the moment the user visits any locale-prefixed
        // page. This is the authoritative "user has committed to a locale" signal
        // and works regardless of whether the client-side JS picker click handler
        // succeeds. Once set, the picker never renders again on subsequent requests.
        // It's only set when the URL already has a locale segment so that hitting
        // the root `/` (which redirects) doesn't bypass the picker prematurely.
        if (is_string($request->route('locale'))) {
            $response->headers->setCookie(
                Cookie::create(
                    name: LocaleResolver::SEEN_COOKIE_NAME,
                    value: '1',
                    expire: $expireAt,
                    path: '/',
                    domain: null,
                    secure: $isSecure,
                    httpOnly: false,
                    raw: false,
                    sameSite: Cookie::SAMESITE_LAX
                )
            );
        }

        return $response;
    }
}
