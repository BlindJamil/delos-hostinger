<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Async per-field media upload for page content.
 *
 * The page editor's main form stays text-only to keep POST size under
 * PHP's max_input_vars (default 1000). Image/video uploads hit THIS
 * controller via AJAX as soon as the admin picks a file, the server
 * stores it in storage/app/public/uploads/page-content/, and writes the
 * storage path to the PageContent row immediately.
 *
 * The JSON response returns the new URL so the field preview can update
 * in place without a page reload.
 */
class PageContentMediaController extends Controller
{
    public function store(Request $request, string $key): JsonResponse
    {
        // Admins upload either the default image or an optional mobile-only
        // variant. The variant lives in its own sibling key ("<key>_mobile")
        // so it can carry independent file lifecycle (delete/replace) without
        // touching the primary row.
        $variant = $request->query('variant');
        $targetKey = $variant === 'mobile' ? "{$key}_mobile" : $key;

        // Metadata (page/section/type) always comes from the PRIMARY key
        // entry in the registry, even when we're writing a sibling row.
        $context = $this->findKeyInRegistry($key);
        if (!$context) {
            return response()->json(['error' => "Unknown content key: {$key}"], 422);
        }

        $row = PageContent::firstOrNew(['key' => $targetKey]);
        if (!$row->exists) {
            $row->page = $context['page'];
            $row->section = $context['section'];
            $row->sort_order = $context['sort_order'];
            $row->type = $context['type']; // same as primary (image or video)
        }

        $isVideo = $row->type === 'video';

        // Build rules as discrete array entries — the `image|mimes:...`
        // pipe syntax only works in a single rule string, not mixed into
        // an array (Laravel tries to resolve the pipe as one rule name).
        $rules = ['required', 'file'];
        if ($isVideo) {
            $rules[] = 'mimes:mp4,webm,mov';
            $rules[] = 'max:51200'; // 50MB
        } else {
            $rules[] = 'image';
            $rules[] = 'mimes:jpg,jpeg,png,webp';
            $rules[] = 'max:5120'; // 5MB
        }
        $request->validate(['file' => $rules]);

        // Delete old uploaded file if it exists (only if admin-uploaded path).
        $old = $row->value_en;
        if ($old && str_starts_with($old, 'uploads/') && Storage::disk('public')->exists($old)) {
            Storage::disk('public')->delete($old);
        }

        $path = $request->file('file')->store('uploads/page-content', 'public');

        // Same media asset across locales — store path on all three so the
        // public site serves it regardless of current locale.
        $row->value_en = $path;
        $row->value_ar = $path;
        $row->value_it = $path;
        $row->save(); // fires saved() → busts cache

        // Use asset() so the URL honors APP_URL + any reverse-proxy host
        // the request came in on. Storage::url() hardcodes APP_URL which
        // in local dev points at the wrong port.
        return response()->json([
            'path' => $path,
            'url' => asset('storage/' . $path),
        ]);
    }

    public function destroy(Request $request, string $key): JsonResponse
    {
        $variant = $request->query('variant');
        $targetKey = $variant === 'mobile' ? "{$key}_mobile" : $key;

        $row = PageContent::where('key', $targetKey)->first();
        if (!$row) {
            return response()->json(['status' => 'already-empty']);
        }

        if ($row->value_en && str_starts_with($row->value_en, 'uploads/')
            && Storage::disk('public')->exists($row->value_en)) {
            Storage::disk('public')->delete($row->value_en);
        }

        $row->delete();
        return response()->json(['status' => 'reset-to-default']);
    }

    /**
     * Save a focal-point coordinate against a primary image key. Stored as
     * a plain text row with key "<key>_focal" and value "X% Y%" so the
     * public <x-responsive-image :focal="..."/> prop picks it up without
     * any extra resolution logic.
     */
    public function focal(Request $request, string $key): JsonResponse
    {
        $validated = $request->validate([
            'x' => 'required|integer|min:0|max:100',
            'y' => 'required|integer|min:0|max:100',
        ]);

        $context = $this->findKeyInRegistry($key);
        if (!$context) {
            return response()->json(['error' => "Unknown content key: {$key}"], 422);
        }

        $focalKey = "{$key}_focal";
        $row = PageContent::firstOrNew(['key' => $focalKey]);
        $row->page = $context['page'];
        $row->section = $context['section'];
        $row->sort_order = $context['sort_order'];
        $row->type = 'text';
        $focalValue = "{$validated['x']}% {$validated['y']}%";
        $row->value_en = $focalValue;
        $row->value_ar = $focalValue;
        $row->value_it = $focalValue;
        $row->save(); // busts PageContent cache

        return response()->json([
            'focal' => $focalValue,
            'x' => (int) $validated['x'],
            'y' => (int) $validated['y'],
        ]);
    }

    /**
     * Delete the focal-point override so the image reverts to centered.
     */
    public function focalReset(string $key): JsonResponse
    {
        $row = PageContent::where('key', "{$key}_focal")->first();
        if ($row) {
            $row->delete();
        }
        return response()->json(['status' => 'reset-to-default']);
    }

    private function findKeyInRegistry(string $key): ?array
    {
        foreach (config('editable_pages', []) as $pageSlug => $page) {
            foreach ($page['sections'] ?? [] as $sectionSlug => $section) {
                foreach ($section['fields'] ?? [] as $index => $field) {
                    if ($field['key'] === $key) {
                        return [
                            'page' => $pageSlug,
                            'section' => $sectionSlug,
                            'sort_order' => $index,
                            'type' => $field['type'] ?? 'text',
                        ];
                    }
                }
            }
        }
        return null;
    }
}
