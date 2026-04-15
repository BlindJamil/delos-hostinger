<?php

namespace App\Providers;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
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
            $adminUser = null;
            try {
                if (config('auth.guards.admin')) {
                    $isAdmin = auth('admin')->check();
                    $adminUser = auth('admin')->user();
                }
            } catch (\Throwable) {
                // Swallow — fall through to the anonymous view.
            }

            $view->with([
                // Admin presence — drives the edit pills and floating admin
                // bar on public pages. Cheap check (cookie + session read)
                // via the admin guard.
                'isAdmin' => $isAdmin,
                'adminUser' => $adminUser,

                'settingInstagram' => SiteSetting::value('social_instagram'),
                'settingFacebook' => SiteSetting::value('social_facebook'),
                'settingYoutube' => SiteSetting::value('social_youtube'),
                'settingTiktok' => SiteSetting::value('social_tiktok'),
                'settingPhone' => SiteSetting::value('contact_phone'),
                'settingWhatsapp' => SiteSetting::value('contact_whatsapp'),
                'settingEmail' => SiteSetting::value('contact_email'),
                'settingTagline' => SiteSetting::value('site_tagline'),
                'settingAddress' => SiteSetting::value('company_address_primary'),
            ]);
        });
    }
}
