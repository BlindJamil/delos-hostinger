<?php
/**
 * One-shot browser cache evictor.
 *
 * Safari (and a few other browsers) have a habit of holding stale HTML
 * on disk that ignores `Cache-Control: no-store` on subsequent
 * responses. The `Clear-Site-Data` header — defined in Fetch Living
 * Standard — is the authoritative protocol-level evict signal.
 * Visiting this file once drops everything the browser has cached for
 * this origin and redirects the user to the English homepage.
 *
 * Lives as a raw PHP file at document root (rather than a Laravel
 * route) so it works even when Laravel's route cache is stale after a
 * deploy, which is the exact scenario we're recovering from.
 */

header('Clear-Site-Data: "cache", "storage"');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');
header('Location: /en');
http_response_code(302);
exit;
