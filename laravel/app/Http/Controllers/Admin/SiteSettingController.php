<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    /** Types that hold the same value across all locales. */
    private const NON_LOCALIZED_TYPES = ['url', 'email', 'phone'];

    /** All known groups in the order they should render. */
    private const GROUP_ORDER = ['general', 'contact', 'social', 'seo', 'custom'];

    public function index(): View
    {
        $settings = SiteSetting::query()
            ->orderBy('group')
            ->orderBy('sort_order')
            ->orderBy('key')
            ->get()
            ->groupBy('group');

        // Preserve explicit group ordering but append any unknown groups at the end.
        $orderedGroups = collect(self::GROUP_ORDER)
            ->merge($settings->keys())
            ->unique()
            ->filter(fn ($g) => $settings->has($g))
            ->values();

        return view('admin.site-settings.index', [
            'settings' => $settings,
            'orderedGroups' => $orderedGroups,
            'nonLocalizedTypes' => self::NON_LOCALIZED_TYPES,
        ]);
    }

    /**
     * Bulk-update all existing settings in a single submit. Inputs are keyed
     * by setting id → { value_en, value_ar, value_it }.
     */
    public function update(Request $request): RedirectResponse
    {
        $payload = $request->input('settings', []);
        if (!is_array($payload)) {
            return back()->with('error', 'Invalid settings payload.');
        }

        foreach ($payload as $id => $fields) {
            $setting = SiteSetting::find($id);
            if (!$setting) {
                continue;
            }

            $isLocalized = !in_array($setting->type, self::NON_LOCALIZED_TYPES, true);

            $data = [
                'value_en' => $fields['value_en'] ?? null,
            ];

            if ($isLocalized) {
                $data['value_ar'] = $fields['value_ar'] ?? null;
                $data['value_it'] = $fields['value_it'] ?? null;
            } else {
                // Mirror EN across AR/IT for non-localized types so
                // SiteSetting::value() returns a consistent result in any locale.
                $data['value_ar'] = $data['value_en'];
                $data['value_it'] = $data['value_en'];
            }

            $setting->update($data);
        }

        return redirect()
            ->route('admin.site-settings.index')
            ->with('success', 'Settings saved.');
    }

    /**
     * Add a brand-new custom setting row. Admins can use this for arbitrary
     * keys like `footer_copyright` or `google_analytics_id` without having to
     * ship a migration.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'key' => ['required', 'string', 'max:80', 'regex:/^[a-z0-9_]+$/', 'unique:site_settings,key'],
            'label' => ['required', 'string', 'max:120'],
            'type' => ['required', 'string', 'in:text,textarea,url,email,phone'],
            'group' => ['required', 'string', 'max:40', 'regex:/^[a-z0-9_]+$/'],
            'value_en' => ['nullable', 'string', 'max:2000'],
        ], [
            'key.regex' => 'Key must be lowercase letters, numbers, or underscores only.',
            'group.regex' => 'Group must be lowercase letters, numbers, or underscores only.',
        ]);

        $data['sort_order'] = (SiteSetting::where('group', $data['group'])->max('sort_order') ?? 0) + 10;
        $data['value_ar'] = $data['value_en'] ?? '';
        $data['value_it'] = $data['value_en'] ?? '';

        SiteSetting::create($data);

        return redirect()
            ->route('admin.site-settings.index')
            ->with('success', 'Custom setting added. Edit its values above.');
    }

    public function destroy(SiteSetting $siteSetting): RedirectResponse
    {
        $key = $siteSetting->key;
        $siteSetting->delete();

        return redirect()
            ->route('admin.site-settings.index')
            ->with('success', "Setting \"{$key}\" removed.");
    }
}
