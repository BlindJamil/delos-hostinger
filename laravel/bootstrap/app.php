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

        // Exempt locale cookies from Laravel's automatic cookie encryption.
        // We set them as plaintext both server-side (middleware) and
        // client-side (language.js picker + switcher) so the values must
        // match without encryption.
        $middleware->encryptCookies(except: [
            \App\Support\LocaleResolver::COOKIE_NAME,
            \App\Support\LocaleResolver::SEEN_COOKIE_NAME,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
