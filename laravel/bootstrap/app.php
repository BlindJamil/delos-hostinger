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

        // Register admin auth middleware alias
        $middleware->alias([
            'admin.auth' => \App\Http\Middleware\AdminAuth::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
