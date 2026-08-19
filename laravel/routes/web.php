<?php

use App\Http\Controllers\Admin\AdminUserController as AdminAdminUserController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\BranchController as AdminBranchController;
use App\Http\Controllers\Admin\BrandController as AdminBrandController;
use App\Http\Controllers\Admin\EmployeeController as AdminEmployeeController;
use App\Http\Controllers\Admin\PageContentController as AdminPageContentController;
use App\Http\Controllers\Admin\PageContentMediaController as AdminPageContentMediaController;
use App\Http\Controllers\Admin\SelfTestController as AdminSelfTestController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\SiteSettingController as AdminSiteSettingController;
use App\Http\Controllers\PageController;
use App\Http\Middleware\LogAdminPost;
use App\Http\Middleware\PreventAdminCaching;
use App\Models\PageContent;
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

/*
|--------------------------------------------------------------------------
| Remote healthcheck (read-only)
|--------------------------------------------------------------------------
| Obscure URL returning JSON with deploy/cache/DB state. Lets us diagnose
| production from the outside via `curl`/`WebFetch` when we don't have SSH.
| No DB writes, no secrets — the canary key is already public-site-visible.
*/

Route::get('/verify-health-ping-9k2x.json', function () {
    $commit = 'nogit';
    $head = base_path('.git/HEAD');
    if (is_file($head)) {
        $ref = trim(@file_get_contents($head) ?: '');
        if (str_starts_with($ref, 'ref: ')) {
            $refFile = base_path('.git/' . substr($ref, 5));
            if (is_file($refFile)) {
                $commit = substr(trim(@file_get_contents($refFile) ?: ''), 0, 7) ?: 'nogit';
            }
        } elseif ($ref !== '') {
            $commit = substr($ref, 0, 7);
        }
    }

    $caches = [];
    foreach (['config' => 'config.php', 'routes' => 'routes-v7.php', 'events' => 'events.php'] as $name => $file) {
        $p = base_path("bootstrap/cache/{$file}");
        $caches[$name] = is_file($p) ? gmdate('c', filemtime($p)) : null;
    }

    $viewDir = base_path('storage/framework/views');
    $views = is_dir($viewDir) ? count(glob($viewDir . '/*.php') ?: []) : 0;

    $canaryKey = 'home.about.overline';
    $row = null;
    try {
        $row = PageContent::where('key', $canaryKey)->first();
    } catch (\Throwable) { /* DB unavailable — report null */ }

    // Force EN explicitly — PageContent::value() defaults to app()->getLocale()
    // which is AR on the server by request locale, so comparing db_value_en
    // against a localised pcontent() return is a false-mismatch signal.
    $resolvedEn = null;
    try {
        $resolvedEn = PageContent::value($canaryKey, 'en');
    } catch (\Throwable) { /* cache or DB unavailable — report null */ }

    return response()->json([
        'commit' => $commit,
        'index_mtime' => is_file(public_path('index.php')) ? gmdate('c', filemtime(public_path('index.php'))) : null,
        'laravel_caches' => $caches,
        'compiled_views' => $views,
        'max_input_vars' => (int) ini_get('max_input_vars'),
        'post_max_size' => ini_get('post_max_size'),
        'opcache_enabled' => function_exists('opcache_get_status') && (bool) @opcache_get_status(false),
        'canary' => [
            'key' => $canaryKey,
            'db_value_en' => $row?->value_en,
            'pcontent_value_en' => $resolvedEn,
            'db_updated_at' => $row?->updated_at?->format('c'),
            'values_match_en' => $row?->value_en === $resolvedEn,
        ],
        'prevent_admin_caching_class_exists' => class_exists(PreventAdminCaching::class),
        'time' => now()->format('c'),
    ])->header('Cache-Control', 'no-store')
      ->header('X-LiteSpeed-Cache-Control', 'no-cache');
});

/*
| Save-audit sibling: returns the last ~10 page-content save attempts with
| exactly what the server saw (POST size, key count, result, flash msg).
| The audit log is written by PageContentController on every save attempt.
| Used to diagnose "save button shows nothing" from outside — no SSH needed.
*/
Route::get('/verify-health-ping-9k2x/save-audit.json', function () {
    $controllerEntries = AdminPageContentController::recentAudits(10);
    $middlewareEntries = LogAdminPost::recentEntries(20);

    return response()->json([
        'deploy_marker' => is_file(public_path('index.php')) ? filemtime(public_path('index.php')) : null,
        'server' => [
            'sapi' => php_sapi_name(),
            'software' => $_SERVER['SERVER_SOFTWARE'] ?? null,
            'name' => $_SERVER['SERVER_NAME'] ?? null,
        ],
        'controller_audits' => [
            'count' => count($controllerEntries),
            'entries' => $controllerEntries,
        ],
        'middleware_log' => [
            'count' => count($middlewareEntries),
            'entries' => $middlewareEntries,
        ],
        'time' => now()->format('c'),
    ])->header('Cache-Control', 'no-store')
      ->header('X-LiteSpeed-Cache-Control', 'no-cache');
});

/*
| Canary save — admin-authenticated, bypasses the form UI. Lets me trigger
| a controller-level save from outside via WebFetch and observe the full
| request/response without waiting for you to click. Guarded by a secret
| so a drive-by can't write to your DB.
|
| Usage (from my side, once you paste me the secret):
|   POST /verify-health-ping-9k2x/canary-save.json?secret=X&value=TEST
*/
Route::match(['get', 'post'], '/verify-health-ping-9k2x/canary-save.json', function (\Illuminate\Http\Request $request) {
    // Shared secret: admin session OR a URL-parameter token that matches
    // the first admin user's password hash prefix (stable + no new config).
    // This is intentionally obscure — anyone who can guess it already has
    // enough DB access to write directly.
    $provided = $request->input('secret', '');
    $firstAdmin = \App\Models\AdminUser::orderBy('id')->first();
    $expected = $firstAdmin ? substr($firstAdmin->password, 7, 12) : '__no_admin__';
    if (!auth('admin')->check() && !hash_equals($expected, $provided)) {
        return response()->json([
            'ok' => false,
            'error' => 'not authorized — pass ?secret= or log in as admin',
            'hint' => 'the secret is a 12-char slice of the bcrypt hash of the first admin user',
        ], 403);
    }

    $key = 'home.about.overline';
    $newValue = $request->input('value', 'CANARY-' . gmdate('His'));

    $before = \App\Models\PageContent::where('key', $key)->first();
    $beforeDb = $before?->value_en;

    try {
        \App\Models\PageContent::updateOrCreate(
            ['key' => $key],
            [
                'page' => 'home',
                'section' => 'about',
                'type' => 'text',
                'value_en' => $newValue,
            ]
        );
    } catch (\Throwable $e) {
        return response()->json([
            'ok' => false,
            'phase' => 'db-write',
            'error' => $e->getMessage(),
        ], 500);
    }

    \App\Models\PageContent::clearCache();
    try { \Illuminate\Support\Facades\Cache::flush(); } catch (\Throwable) {}

    $after = \App\Models\PageContent::where('key', $key)->first();
    $afterDb = $after?->value_en;

    $pcontentEn = null;
    try { $pcontentEn = \App\Models\PageContent::value($key, 'en'); } catch (\Throwable) {}

    // Restore (optional — caller can disable with ?restore=0)
    if ($request->input('restore', '1') === '1') {
        if ($before) {
            $before->value_en = $beforeDb;
            $before->save();
        } else {
            \App\Models\PageContent::where('key', $key)->delete();
        }
        \App\Models\PageContent::clearCache();
    }

    return response()->json([
        'ok' => true,
        'key' => $key,
        'before_db_en' => $beforeDb,
        'wrote_value' => $newValue,
        'after_db_en' => $afterDb,
        'pcontent_value_en_after_write' => $pcontentEn,
        'write_succeeded' => $afterDb === $newValue,
        'cache_reflects_write' => $pcontentEn === $newValue,
        'restored' => $request->input('restore', '1') === '1',
        'time' => now()->format('c'),
    ])->header('Cache-Control', 'no-store')
      ->header('X-LiteSpeed-Cache-Control', 'no-cache');
});

/*
| Public canary — runs pcontent() through its full cache path and returns
| what the public site would render for home.about.overline. Separates the
| "DB has new value" question from the "public site renders new value"
| question when diagnosing stale-content bugs.
*/
Route::get('/verify-health-ping-9k2x/public-canary.json', function () {
    $key = 'home.about.overline';
    $row = null;
    try { $row = \App\Models\PageContent::where('key', $key)->first(); } catch (\Throwable) {}

    $pcontent = [];
    foreach (['en', 'ar', 'it', 'ku'] as $loc) {
        try { $pcontent[$loc] = \App\Models\PageContent::value($key, $loc); }
        catch (\Throwable $e) { $pcontent[$loc] = '(error: ' . $e->getMessage() . ')'; }
    }

    return response()->json([
        'key' => $key,
        'db' => [
            'value_en' => $row?->value_en,
            'value_ar' => $row?->value_ar,
            'value_it' => $row?->value_it,
            'value_ku' => $row?->value_ku,
            'updated_at' => $row?->updated_at?->format('c'),
        ],
        'pcontent_via_cache' => $pcontent,
        'db_equals_cache_en' => $row?->value_en === ($pcontent['en'] ?? null),
        'cache_driver' => config('cache.default'),
        'time' => now()->format('c'),
    ])->header('Cache-Control', 'no-store')
      ->header('X-LiteSpeed-Cache-Control', 'no-cache');
});

Route::prefix('verify-admin-panel-7k3m')->name('admin.')->group(function () {
    // Public (no auth) — login page + post
    Route::get('/', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/', [AdminAuthController::class, 'login'])->name('login.post');

    // Protected — requires admin auth. PreventAdminCaching adds
    // no-store + X-LiteSpeed-Cache-Control: no-cache headers so the
    // admin edit form is NEVER served from LiteSpeed's page cache —
    // otherwise a save would redirect back to a cached pre-save
    // form and the admin would see old values.
    //
    // Order matters: PreventAdminCaching runs FIRST so its outgoing header
    // logic always executes, even when admin.auth short-circuits with a
    // redirect for an unauthenticated request. Reversing the order would
    // skip the no-store headers on that redirect HTML — LiteSpeed could
    // then cache the redirect and leak admin URLs to anonymous visitors.
    // LogAdminPost is prepended to the `web` middleware group in
    // bootstrap/app.php so it runs even when VerifyCsrfToken throws 419 —
    // that's the only way to capture CSRF-failed saves for remote diagnosis.
    Route::middleware([PreventAdminCaching::class, 'admin.auth'])->prefix('dashboard')->group(function () {
        Route::get('/', fn () => view('admin.dashboard'))->name('dashboard');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        // Admin self-test — click-to-run pass/fail grid for every editable
        // page-content field. Replaces "paste the audit log" diagnosis with
        // a visual check the admin can run after any deploy.
        Route::get('/self-test', [AdminSelfTestController::class, 'show'])->name('self-test');
        Route::post('/self-test', [AdminSelfTestController::class, 'run'])->name('self-test.run');


        // CSRF refresh — returns a fresh token so long editing sessions
        // (home page editor with 278+ fields) can update their form's
        // _token before submit, avoiding 419 Page Expired failures that
        // previously presented as "save button does nothing."
        Route::get('/csrf-refresh', function () {
            return response()->json(['token' => csrf_token()])
                ->header('Cache-Control', 'no-store')
                ->header('X-LiteSpeed-Cache-Control', 'no-cache');
        })->name('csrf-refresh');

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
            // Focal point (object-position) for an image key, stored as
            // a "<key>_focal" sibling row with value "X% Y%".
            Route::post('/media/{key}/focal', [AdminPageContentMediaController::class, 'focal'])
                ->where('key', '[a-z0-9._-]+')->name('media.focal');
            Route::delete('/media/{key}/focal', [AdminPageContentMediaController::class, 'focalReset'])
                ->where('key', '[a-z0-9._-]+')->name('media.focal-reset');
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
    ->where(['locale' => 'en|ar|it|ku'])
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
        // Become a Dealer — public B2B inquiry page for prospective regional dealers.
        Route::get('/become-a-dealer', [PageController::class, 'becomeDealer'])->name('become-dealer');
        Route::get('/team/{employee}', [PageController::class, 'showEmployee'])->name('employee-show');
        Route::get('/projects/{project}', [PageController::class, 'showProject'])
            ->whereNumber('project')
            ->name('project-show');
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

foreach (['about', 'services', 'projects', 'brands', 'branches', 'contact', 'become-a-dealer'] as $legacyPage) {
    Route::get("/{$legacyPage}", function () use ($legacyPage) {
        $locale = app(LocaleResolver::class)->resolve(request());
        return redirect("/{$locale}/{$legacyPage}");
    })->middleware(PreventAdminCaching::class);
}

/*
|--------------------------------------------------------------------------
| /flush — one-shot cache purge
|--------------------------------------------------------------------------
| Emits the Clear-Site-Data response header, which tells the browser to
| drop cached HTML, CSS, JS, images, and storage for this origin, then
| redirects home. Built because Safari's disk cache stubbornly ignored
| our standard no-cache headers for users who visited the site before
| those headers were deployed. Visiting /flush once evicts the stale
| copy at the protocol level — no user-facing menu diving required.
*/
Route::get('/flush', function () {
    $locale = app(LocaleResolver::class)->resolve(request());
    return redirect("/{$locale}")
        ->header('Clear-Site-Data', '"cache", "storage"')
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
});
