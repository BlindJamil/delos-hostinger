<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ensures public pages never get served stale by a shared cache after an
 * admin edits content. Two reasons:
 *
 *   1. Hostinger LiteSpeed Cache (enabled by default on shared plans) will
 *      store rendered HTML for hours. When an admin updates page content
 *      through /verify-admin-panel-7k3m, the DB and the pcontent cache
 *      both flip immediately — but LiteSpeed keeps serving the old copy
 *      until its TTL expires or it's explicitly purged. The admin's
 *      "my edits don't show" complaint is usually this.
 *
 *   2. Admin-authenticated responses embed the hidden admin URL in every
 *      edit-pill href. If a CDN ever cached that HTML and served it to an
 *      anonymous visitor, the admin URL would leak.
 *
 * Strategy: always send no-store headers on pages running this middleware
 * (i.e. the locale-prefixed public routes). The X-LiteSpeed-Cache-Control
 * header is the explicit opt-out LSCache respects on Hostinger. Admin
 * responses additionally get `Vary: Cookie` and noindex metadata.
 */
class PreventAdminCaching
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // Always: prevent any shared cache from storing public-page HTML.
        // Admin edits need to reflect on the next request, not after TTL.
        $response->headers->set('Cache-Control', 'private, no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        // LiteSpeed-specific opt-out for Hostinger's default page cache.
        $response->headers->set('X-LiteSpeed-Cache-Control', 'no-cache');

        if (auth('admin')->check()) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
            // Key by session cookie so if a future shared cache is added,
            // admin responses never collide with anonymous ones.
            $existingVary = $response->headers->get('Vary');
            $response->headers->set('Vary', trim(($existingVary ? $existingVary . ', ' : '') . 'Cookie', ', '));
        }

        return $response;
    }
}
