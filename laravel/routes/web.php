<?php

use App\Http\Controllers\PageController;
use App\Support\LocaleResolver;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Locale-prefixed routes
|--------------------------------------------------------------------------
| All pages live under /{locale}/ where locale is en|ar|it.
| The middleware (SetLocale) resolves the locale from the URL segment and
| shares it to all views. Route names are prefixed with "l." — use the
| lroute() helper in blades to avoid needing to pass {locale} every call.
*/

Route::prefix('{locale}')
    ->where(['locale' => 'en|ar|it'])
    ->name('l.')
    ->group(function () {
        Route::get('/', [PageController::class, 'home'])->name('home');
        Route::get('/about', [PageController::class, 'about'])->name('about');
        Route::get('/services', [PageController::class, 'services'])->name('services');
        Route::get('/projects', [PageController::class, 'projects'])->name('projects');
        Route::get('/brands', [PageController::class, 'brands'])->name('brands');
        Route::get('/branches', [PageController::class, 'branches'])->name('branches');
        Route::get('/contact', [PageController::class, 'contact'])->name('contact');
    });

/*
|--------------------------------------------------------------------------
| Root + legacy redirects
|--------------------------------------------------------------------------
| `/` resolves the user's locale (cookie → Accept-Language → fallback) and
| redirects to `/{locale}`. Old unprefixed URLs like `/services` redirect
| to `/{locale}/services` so existing shared links keep working.
*/

Route::get('/', function () {
    $locale = app(LocaleResolver::class)->resolve(request());
    return redirect('/' . $locale);
});

foreach (['about', 'services', 'projects', 'brands', 'branches', 'contact'] as $legacyPage) {
    Route::get("/{$legacyPage}", function () use ($legacyPage) {
        $locale = app(LocaleResolver::class)->resolve(request());
        return redirect("/{$locale}/{$legacyPage}");
    });
}
