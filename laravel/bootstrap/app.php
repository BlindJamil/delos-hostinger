<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // LogAdminPost PREPENDED to web group so it runs BEFORE
        // VerifyCsrfToken. This is the only way to capture 419 Page Expired
        // failures (token mismatch) — which is the leading theory for the
        // "home page save does nothing" bug on production. Without this,
        // CSRF-failed POSTs leave no trace to diagnose.
        $middleware->web(prepend: [
            \App\Http\Middleware\LogAdminPost::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);

        // Exempt the locale cookie from Laravel's automatic cookie encryption.
        // Both the server (SetLocale middleware) and the client (language.js
        // floating switcher) write this cookie as plaintext — they must
        // agree on format without encryption in between.
        $middleware->encryptCookies(except: [
            \App\Support\LocaleResolver::COOKIE_NAME,
        ]);

        // Exempt diagnostic endpoints from CSRF — they're called from
        // outside the admin UI for remote diagnosis (canary save, etc.)
        // and have their own secret-based guard instead.
        $middleware->validateCsrfTokens(except: [
            'verify-health-ping-9k2x/*',
        ]);

        // Register admin auth middleware alias
        $middleware->alias([
            'admin.auth' => \App\Http\Middleware\AdminAuth::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Log CSRF (TokenMismatch) and auth exceptions on admin dashboard
        // POSTs to the save audit so 419s / auth redirects leave a trail
        // even though they short-circuit before the controller.
        $exceptions->report(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            if (!str_contains($request->path(), 'verify-admin-panel-7k3m/dashboard/')) return;
            try {
                $path = storage_path('app/' . \App\Http\Middleware\LogAdminPost::LOG_FILE);
                $entry = [
                    'ts' => now()->format('c'),
                    'request_id' => $request->attributes->get('request_id'),
                    'method' => $request->method(),
                    'uri' => $request->getRequestUri(),
                    'exception' => 'TokenMismatchException (419 Page Expired)',
                    'content_length' => (int) ($request->header('Content-Length') ?? 0),
                    'raw_body_length' => strlen($request->getContent() ?? ''),
                    'has_csrf_field' => $request->has('_token'),
                    'has_csrf_header' => $request->header('X-CSRF-TOKEN') !== null
                                         || $request->header('X-XSRF-TOKEN') !== null,
                    'session_id_present' => $request->hasSession() && $request->session()->getId() !== '',
                    'admin_authed' => auth('admin')->check(),
                    'server' => [
                        'sapi' => php_sapi_name(),
                        'software' => $_SERVER['SERVER_SOFTWARE'] ?? null,
                        'remote_addr' => $request->ip(),
                    ],
                ];
                $lines = is_file($path) ? (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: []) : [];
                $lines[] = json_encode($entry, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                if (count($lines) > 50) $lines = array_slice($lines, -50);
                @file_put_contents($path, implode(PHP_EOL, $lines) . PHP_EOL, LOCK_EX);
            } catch (\Throwable) { /* swallow */ }
        });
    })->create();
