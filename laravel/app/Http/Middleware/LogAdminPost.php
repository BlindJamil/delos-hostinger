<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pre-controller POST logger for the admin dashboard.
 *
 * Captures every mutating request to /verify-admin-panel-7k3m/dashboard/* at
 * the middleware level — before admin.auth can short-circuit with a redirect
 * and before the controller runs. This is the only way to diagnose
 * "save button does nothing" bugs where the POST is dropped by a CSRF
 * mismatch, session expiry, or LiteSpeed WAF rule — all of which would
 * happen before the controller-level audit runs.
 *
 * Writes to storage/app/admin-request-log.jsonl (separate from the save
 * audit so they can be correlated by request_id). Bounded at 50 entries.
 * Exposed read-only via /verify-health-ping-9k2x/save-audit.json for
 * remote diagnosis on shared hosting without SSH.
 */
class LogAdminPost
{
    public const LOG_FILE = 'admin-request-log.jsonl';
    public const MAX_ENTRIES = 50;

    public function handle(Request $request, Closure $next): Response
    {
        // Only log mutating verbs — we don't care about GET requests for the
        // edit form / dashboard list, only POST/PUT/DELETE that represent an
        // attempt to change state.
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            $requestId = (string) Str::uuid();
            // Attach the request_id to the request so the downstream
            // controller audit can reference the same request.
            $request->attributes->set('request_id', $requestId);

            $this->writeLog([
                'ts' => now()->format('c'),
                'request_id' => $requestId,
                'method' => $request->method(),
                'uri' => $request->getRequestUri(),
                'content_length' => (int) ($request->header('Content-Length') ?? 0),
                'raw_body_length' => strlen($request->getContent() ?? ''),
                'content_type' => $request->header('Content-Type'),
                'referer' => $request->header('Referer'),
                'user_agent' => substr($request->header('User-Agent') ?? '', 0, 200),
                'has_csrf_field' => $request->has('_token'),
                'has_csrf_header' => $request->header('X-CSRF-TOKEN') !== null
                                     || $request->header('X-XSRF-TOKEN') !== null,
                'session_id_present' => $request->hasSession() && $request->session()->getId() !== '',
                'admin_authed' => auth('admin')->check(),
                'admin_user_id' => auth('admin')->id(),
                'all_post_keys' => array_slice(array_keys($request->post()), 0, 20),
                'post_key_count' => count($request->post()),
                'server' => [
                    'sapi' => php_sapi_name(),
                    'software' => $_SERVER['SERVER_SOFTWARE'] ?? null,
                    'name' => $_SERVER['SERVER_NAME'] ?? null,
                    'remote_addr' => $request->ip(),
                    'forwarded_for' => $request->header('X-Forwarded-For'),
                ],
                'max_input_vars' => (int) ini_get('max_input_vars'),
                'deploy_marker' => is_file(public_path('index.php')) ? filemtime(public_path('index.php')) : null,
            ]);
        }

        return $next($request);
    }

    /**
     * Best-effort bounded write. Swallow all errors — a log write must never
     * break the underlying request.
     */
    private function writeLog(array $entry): void
    {
        try {
            $path = storage_path('app/' . self::LOG_FILE);
            if (!is_dir(dirname($path))) {
                @mkdir(dirname($path), 0775, true);
            }
            // Wipe stale pre-deploy lines for the same reason as the controller audit.
            $deployMark = is_file(public_path('index.php')) ? filemtime(public_path('index.php')) : 0;
            $fileMtime = is_file($path) ? filemtime($path) : 0;
            $lines = (is_file($path) && $fileMtime >= $deployMark)
                ? (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [])
                : [];
            $lines[] = json_encode($entry, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            if (count($lines) > self::MAX_ENTRIES) {
                $lines = array_slice($lines, -self::MAX_ENTRIES);
            }
            @file_put_contents($path, implode(PHP_EOL, $lines) . PHP_EOL, LOCK_EX);
        } catch (\Throwable) { /* swallow */ }
    }

    /**
     * Read last N entries, latest first. Used by the diagnostic endpoint.
     */
    public static function recentEntries(int $limit = 20): array
    {
        $path = storage_path('app/' . self::LOG_FILE);
        if (!is_file($path)) return [];
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
        $lines = array_slice($lines, -$limit);
        $out = [];
        foreach (array_reverse($lines) as $line) {
            $decoded = json_decode($line, true);
            if (is_array($decoded)) $out[] = $decoded;
        }
        return $out;
    }
}
