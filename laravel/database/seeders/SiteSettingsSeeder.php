<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

/**
 * Seeds the site_settings table with the keys currently hard-coded across
 * blades (layouts/app.blade.php footer, home.blade.php CTA buttons, etc.).
 * Idempotent: firstOrCreate on key — never overwrites admin edits.
 *
 * Types supported by the admin form:
 *   - text      (single-line, localized EN/AR/IT)
 *   - textarea  (multi-line, localized EN/AR/IT)
 *   - url       (single value, not localized)
 *   - email     (single value, not localized)
 *   - phone     (single value, not localized)
 */
class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // ─── General ─────────────────────────────────────────────
            [
                'key' => 'site_tagline',
                'group' => 'general',
                'type' => 'text',
                'label' => 'Site tagline',
                'value_en' => 'Italian Luxury Solutions',
                'value_ar' => 'حلول الفخامة الإيطالية',
                'value_it' => 'Soluzioni di lusso italiano',
                'sort_order' => 10,
            ],
            [
                'key' => 'company_address_primary',
                'group' => 'general',
                'type' => 'text',
                'label' => 'Primary address (Erbil)',
                'value_en' => '60m Street, Near Soran Hospital',
                'value_ar' => 'شارع الـ60 متر، قرب مستشفى سوران',
                'value_it' => 'Via 60m, vicino all\'Ospedale Soran',
                'sort_order' => 20,
            ],

            // ─── Contact ────────────────────────────────────────────
            [
                'key' => 'contact_phone',
                'group' => 'contact',
                'type' => 'phone',
                'label' => 'Primary phone',
                'value_en' => '0750 100 1701',
                'sort_order' => 10,
            ],
            [
                'key' => 'contact_whatsapp',
                'group' => 'contact',
                'type' => 'phone',
                'label' => 'WhatsApp number',
                'value_en' => '',
                'sort_order' => 20,
            ],
            [
                'key' => 'contact_email',
                'group' => 'contact',
                'type' => 'email',
                'label' => 'Contact email',
                'value_en' => '',
                'sort_order' => 30,
            ],

            // ─── Social ─────────────────────────────────────────────
            [
                'key' => 'social_instagram',
                'group' => 'social',
                'type' => 'url',
                'label' => 'Instagram URL',
                'value_en' => 'https://www.instagram.com/delos.international/',
                'sort_order' => 10,
            ],
            [
                'key' => 'social_facebook',
                'group' => 'social',
                'type' => 'url',
                'label' => 'Facebook URL',
                'value_en' => 'https://www.facebook.com/delos.int.erbil/',
                'sort_order' => 20,
            ],
            [
                'key' => 'social_youtube',
                'group' => 'social',
                'type' => 'url',
                'label' => 'YouTube URL',
                'value_en' => '',
                'sort_order' => 30,
            ],
            [
                'key' => 'social_tiktok',
                'group' => 'social',
                'type' => 'url',
                'label' => 'TikTok URL',
                'value_en' => '',
                'sort_order' => 40,
            ],
        ];

        foreach ($settings as $row) {
            SiteSetting::firstOrCreate(
                ['key' => $row['key']],
                $row
            );
        }
    }
}
