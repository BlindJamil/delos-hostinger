<?php

namespace App\Providers;

use App\Models\Branch;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Force reliable session config for shared hosting (Hostinger LiteSpeed).
        // We cannot trust the production .env to have these set — file-based
        // sessions on LiteSpeed get garbage-collected between workers, which
        // causes silent CSRF mismatches (419 Page Expired) that look to the
        // admin user like "save didn't work." Database sessions are atomic
        // and survive worker rotation. 8-hour lifetime = a full workday of
        // editing without being booted for CSRF reasons.
        config([
            'session.driver' => 'database',
            'session.lifetime' => 480,
            'session.expire_on_close' => false,
        ]);
    }

    public function boot(): void
    {
        // Share site-wide settings with every view. Keys are pulled lazily so
        // we never execute a DB query if the key doesn't exist. SiteSetting
        // caches each key for 10 min and busts on save.
        View::composer('*', function ($view) {
            // Every piece below is wrapped in its own guard because this
            // composer fires on every rendered view — a single failure
            // here 500s the entire public site. Common culprits on a
            // fresh deploy: missing auth guard (admin) in config, or
            // missing DB tables (site_settings) before migrations run.
            $isAdmin = false;
            // Intentionally named `$currentAdmin` (not `$adminUser`) to avoid
            // shadowing the `$adminUser` variable that AdminUserController's
            // create/edit actions pass to the admin-users form. Using the
            // same name here made the composer overwrite the controller's
            // fresh-model with the authed user, causing "New Admin" to
            // render pre-filled with the current user's data and a
            // reset-password form pointing at their own ID.
            $currentAdmin = null;
            try {
                if (config('auth.guards.admin')) {
                    $isAdmin = auth('admin')->check();
                    $currentAdmin = auth('admin')->user();
                }
            } catch (\Throwable) {
                // Swallow — fall through to the anonymous view.
            }

            // Active branches shared with every view so the footer's
            // "Showrooms" column stays in sync with whatever the admin
            // has saved in /admin/branches — no lang-file duplication.
            // Wrapped in try/catch because the footer renders on every
            // page including 404s where DB tables may not exist yet.
            $footerBranches = collect();
            try {
                $footerBranches = Branch::query()->active()->ordered()->get();
            } catch (\Throwable) {
                // Empty collection → footer falls back to lang strings.
            }

            $view->with([
                // Admin presence — drives the edit pills and floating admin
                // bar on public pages. Cheap check (cookie + session read)
                // via the admin guard.
                'isAdmin' => $isAdmin,
                'currentAdmin' => $currentAdmin,
                'footerBranches' => $footerBranches,

                'settingInstagram' => SiteSetting::value('social_instagram'),
                'settingFacebook' => SiteSetting::value('social_facebook'),
                'settingYoutube' => SiteSetting::value('social_youtube'),
                'settingTiktok' => SiteSetting::value('social_tiktok'),
                'settingPinterest' => SiteSetting::value('social_pinterest'),
                'settingPhone' => SiteSetting::value('contact_phone'),
                'settingWhatsapp' => SiteSetting::value('contact_whatsapp'),
                'settingEmail' => SiteSetting::value('contact_email'),
                'settingTagline' => SiteSetting::value('site_tagline'),
                'settingAddress' => SiteSetting::value('company_address_primary'),
            ]);
        });
    }
}
