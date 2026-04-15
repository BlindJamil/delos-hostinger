<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Prevents any shared cache (browser, CDN, proxy) from storing public-page
 * responses that were rendered for a logged-in admin. Admin-authenticated
 * responses contain the hidden admin URL (/verify-admin-panel-7k3m/...) in
 * every edit-pill href — if a CDN ever cached that HTML and served it to
 * an anonymous visitor, the admin URL would leak.
 *
 * Also adds `X-Robots-Tag: noindex, nofollow` as belt-and-suspenders against
 * search engines that might somehow index the admin-logged-in variant, and
 * `Vary: Cookie` so any future shared cache correctly keys by session.
 */
class PreventAdminCaching
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        if (auth('admin')->check()) {
            $response->headers->set('Cache-Control', 'private, no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
            // If a future shared cache is added, key by session so admin
            // responses never collide with anonymous ones.
            $existingVary = $response->headers->get('Vary');
            $response->headers->set('Vary', trim(($existingVary ? $existingVary . ', ' : '') . 'Cookie', ', '));
        }

        return $response;
    }
}
