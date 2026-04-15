<?php

use App\Http\Controllers\Admin\AdminUserController as AdminAdminUserController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\BranchController as AdminBranchController;
use App\Http\Controllers\Admin\BrandController as AdminBrandController;
use App\Http\Controllers\Admin\EmployeeController as AdminEmployeeController;
use App\Http\Controllers\Admin\PageContentController as AdminPageContentController;
use App\Http\Controllers\Admin\PageContentMediaController as AdminPageContentMediaController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\SiteSettingController as AdminSiteSettingController;
use App\Http\Controllers\PageController;
use App\Http\Middleware\PreventAdminCaching;
use App\Support\LocaleResolver;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Hidden Admin Panel Routes
|--------------------------------------------------------------------------
| Kept above the locale-prefixed routes so /verify-admin-panel-7k3m is
| resolved before the {locale} catch-all. The locale regex explicitly
| excludes any path that isn't en|ar|it so there is no conflict.
*/

Route::prefix('verify-admin-panel-7k3m')->name('admin.')->group(function () {
    // Public (no auth) — login page + post
    Route::get('/', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/', [AdminAuthController::class, 'login'])->name('login.post');

    // Protected — requires admin auth
    Route::middleware('admin.auth')->prefix('dashboard')->group(function () {
        Route::get('/', fn () => view('admin.dashboard'))->name('dashboard');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        // Employees CRUD
        Route::prefix('employees')->name('employees.')->group(function () {
            Route::get('/', [AdminEmployeeController::class, 'index'])->name('index');
            Route::get('/create', [AdminEmployeeController::class, 'create'])->name('create');
            Route::post('/', [AdminEmployeeController::class, 'store'])->name('store');
            Route::get('/{employee}/edit', [AdminEmployeeController::class, 'edit'])->name('edit');
            Route::put('/{employee}', [AdminEmployeeController::class, 'update'])->name('update');
            Route::delete('/{employee}', [AdminEmployeeController::class, 'destroy'])->name('destroy');
            Route::post('/{employee}/toggle', [AdminEmployeeController::class, 'toggleActive'])->name('toggle');
            Route::post('/reorder', [AdminEmployeeController::class, 'reorder'])->name('reorder');
        });

        // Projects CRUD
        Route::prefix('projects')->name('projects.')->group(function () {
            Route::get('/', [AdminProjectController::class, 'index'])->name('index');
            Route::get('/create', [AdminProjectController::class, 'create'])->name('create');
            Route::post('/', [AdminProjectController::class, 'store'])->name('store');
            Route::get('/{project}/edit', [AdminProjectController::class, 'edit'])->name('edit');
            Route::put('/{project}', [AdminProjectController::class, 'update'])->name('update');
            Route::delete('/{project}', [AdminProjectController::class, 'destroy'])->name('destroy');
            Route::post('/{project}/toggle', [AdminProjectController::class, 'toggleActive'])->name('toggle');
            Route::post('/{project}/featured', [AdminProjectController::class, 'toggleFeatured'])->name('featured');
        });

        // Brands CRUD
        Route::prefix('brands')->name('brands.')->group(function () {
            Route::get('/', [AdminBrandController::class, 'index'])->name('index');
            Route::get('/create', [AdminBrandController::class, 'create'])->name('create');
            Route::post('/', [AdminBrandController::class, 'store'])->name('store');
            Route::get('/{brand}/edit', [AdminBrandController::class, 'edit'])->name('edit');
            Route::put('/{brand}', [AdminBrandController::class, 'update'])->name('update');
            Route::delete('/{brand}', [AdminBrandController::class, 'destroy'])->name('destroy');
            Route::post('/{brand}/toggle', [AdminBrandController::class, 'toggleActive'])->name('toggle');
        });

        // Branches CRUD
        Route::prefix('branches')->name('branches.')->group(function () {
            Route::get('/', [AdminBranchController::class, 'index'])->name('index');
            Route::get('/create', [AdminBranchController::class, 'create'])->name('create');
            Route::post('/', [AdminBranchController::class, 'store'])->name('store');
            Route::get('/{branch}/edit', [AdminBranchController::class, 'edit'])->name('edit');
            Route::put('/{branch}', [AdminBranchController::class, 'update'])->name('update');
            Route::delete('/{branch}', [AdminBranchController::class, 'destroy'])->name('destroy');
            Route::post('/{branch}/toggle', [AdminBranchController::class, 'toggleActive'])->name('toggle');
        });

        // Services CRUD
        Route::prefix('services')->name('services.')->group(function () {
            Route::get('/', [AdminServiceController::class, 'index'])->name('index');
            Route::get('/create', [AdminServiceController::class, 'create'])->name('create');
            Route::post('/', [AdminServiceController::class, 'store'])->name('store');
            Route::get('/{service}/edit', [AdminServiceController::class, 'edit'])->name('edit');
            Route::put('/{service}', [AdminServiceController::class, 'update'])->name('update');
            Route::delete('/{service}', [AdminServiceController::class, 'destroy'])->name('destroy');
            Route::post('/{service}/toggle', [AdminServiceController::class, 'toggleActive'])->name('toggle');
        });

        // Page Content — lang-file overrides + media uploads per page
        Route::prefix('page-content')->name('page-content.')->group(function () {
            Route::get('/', [AdminPageContentController::class, 'index'])->name('index');
            Route::get('/{page}/edit', [AdminPageContentController::class, 'edit'])->name('edit');
            Route::put('/{page}', [AdminPageContentController::class, 'update'])->name('update');
            Route::post('/{page}/reset', [AdminPageContentController::class, 'resetField'])->name('reset');

            // AJAX media upload endpoints (one file per key)
            Route::post('/media/{key}', [AdminPageContentMediaController::class, 'store'])
                ->where('key', '[a-z0-9._-]+')->name('media.store');
            Route::delete('/media/{key}', [AdminPageContentMediaController::class, 'destroy'])
                ->where('key', '[a-z0-9._-]+')->name('media.destroy');
        });

        // Site Settings (single-page bulk edit + add/remove custom keys)
        Route::prefix('site-settings')->name('site-settings.')->group(function () {
            Route::get('/', [AdminSiteSettingController::class, 'index'])->name('index');
            Route::put('/', [AdminSiteSettingController::class, 'update'])->name('update');
            Route::post('/', [AdminSiteSettingController::class, 'store'])->name('store');
            Route::delete('/{siteSetting}', [AdminSiteSettingController::class, 'destroy'])->name('destroy');
        });

        // Admin Users (super-admin-only, guarded in controller)
        Route::prefix('admin-users')->name('admin-users.')->group(function () {
            Route::get('/', [AdminAdminUserController::class, 'index'])->name('index');
            Route::get('/create', [AdminAdminUserController::class, 'create'])->name('create');
            Route::post('/', [AdminAdminUserController::class, 'store'])->name('store');
            Route::get('/{adminUser}/edit', [AdminAdminUserController::class, 'edit'])->name('edit');
            Route::put('/{adminUser}', [AdminAdminUserController::class, 'update'])->name('update');
            Route::delete('/{adminUser}', [AdminAdminUserController::class, 'destroy'])->name('destroy');
            Route::post('/{adminUser}/reset-password', [AdminAdminUserController::class, 'resetPassword'])->name('reset-password');
        });
    });
});

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
    ->middleware(PreventAdminCaching::class)
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
})->middleware(PreventAdminCaching::class);

foreach (['about', 'services', 'projects', 'brands', 'branches', 'contact'] as $legacyPage) {
    Route::get("/{$legacyPage}", function () use ($legacyPage) {
        $locale = app(LocaleResolver::class)->resolve(request());
        return redirect("/{$locale}/{$legacyPage}");
    })->middleware(PreventAdminCaching::class);
}
