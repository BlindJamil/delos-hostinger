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
            $rules[] = 'max:20480'; // 20MB
        }
        $request->validate(['file' => $rules]);

        // Delete old uploaded file if it exists (only if admin-uploaded path).
        $old = $row->value_en;
        if ($old && str_starts_with($old, 'uploads/') && Storage::disk('public')->exists($old)) {
            Storage::disk('public')->delete($old);
            $this->deleteResponsiveVariants($old);
        }

        // Auto-compress oversized images in place (>5MB). Full pixel dimensions
        // are preserved — only the encoding quality is stepped down (starts at
        // 92, drops to 85 if still over budget). At q92 the difference is
        // imperceptible on any normal screen. Videos and small images are
        // stored as-is.
        $upload = $request->file('file');
        if (!$isVideo && $upload->getSize() > 5 * 1024 * 1024) {
            $this->compressInPlace($upload);
        }

        $path = $upload->store('uploads/page-content', 'public');

        // Generate responsive WebP + JPEG variants so <x-responsive-image>
        // can serve retina-scale sources to retina displays and small ones
        // to phones. Without this, every device downloads the full-res file
        // and heroes on retina screens render soft (browser upscales on paint).
        $this->generateResponsiveVariants($path);

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
            $this->deleteResponsiveVariants($row->value_en);
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

    /**
     * Re-encode the uploaded image in place to get under 5 MB without
     * visible quality loss. Starts at quality 92 (JPEG/WebP) and only
     * steps down to 85 if the first pass still exceeds the budget.
     * Full pixel dimensions are preserved — we only trade EXIF/profile
     * bloat and excess JPEG quality for size. GD handles JPEG/PNG/WebP.
     *
     * If GD can't decode the source (corrupt or exotic format) the
     * method is a no-op — the original file still gets stored and the
     * 20 MB validator remains the hard ceiling.
     */
    private function compressInPlace(\Illuminate\Http\UploadedFile $file): void
    {
        $path = $file->getRealPath();
        $mime = $file->getMimeType();

        $img = match ($mime) {
            'image/jpeg'           => @imagecreatefromjpeg($path),
            'image/png'            => @imagecreatefrompng($path),
            'image/webp'           => @imagecreatefromwebp($path),
            default                => null,
        };
        if (!$img) {
            return; // decode failed — keep original
        }

        // PNG is lossless-by-default — re-encode as high-quality JPEG
        // instead (PNG at q9 often stays huge for photographs).
        $encodeMime = $mime === 'image/png' ? 'image/jpeg' : $mime;
        $budget = 5 * 1024 * 1024;

        // Tight ladder — photographic hero content bands visibly at q=85.
        // If a 5MB ceiling still isn't enough at q=88, the source is absurdly
        // large and the admin should resize before uploading.
        foreach ([92, 90, 88] as $quality) {
            $ok = match ($encodeMime) {
                'image/jpeg' => imagejpeg($img, $path, $quality),
                'image/webp' => imagewebp($img, $path, $quality),
                default      => false,
            };
            if (!$ok) { break; }
            clearstatcache(true, $path);
            if (filesize($path) <= $budget) { break; }
        }

        imagedestroy($img);
    }

    /**
     * Generate responsive WebP + JPEG variants next to an admin-uploaded image.
     *
     * Mirrors what `scripts/generate-responsive-images.mjs` does for legacy
     * assets in public/images/, but runs per-upload for CMS files living
     * under storage/uploads/page-content/. Output lives in a sibling
     * `responsive/` folder so the upload and its variants can be cleaned up
     * together.
     *
     * Widths match the legacy generator. Each width caps at the source's
     * native dimensions — we never upscale, since that just inflates bytes
     * without adding detail.
     *
     * Runs best-effort: a GD failure on one width doesn't take out the whole
     * upload. The primary file is still saved; <x-responsive-image> falls
     * back to the full-res fallback if the probe finds no variants.
     */
    private function generateResponsiveVariants(string $storagePath): void
    {
        static $widths = [768, 1200, 1600, 2000, 2560, 3200];

        $disk = Storage::disk('public');
        $absolute = $disk->path($storagePath);
        if (!is_file($absolute)) {
            return;
        }

        $mime = mime_content_type($absolute) ?: '';
        $img = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($absolute),
            'image/png'  => @imagecreatefrompng($absolute),
            'image/webp' => @imagecreatefromwebp($absolute),
            default      => null,
        };
        if (!$img) {
            return;
        }

        $srcW = imagesx($img);
        $srcH = imagesy($img);

        $basename = pathinfo($storagePath, PATHINFO_FILENAME);
        $dir = dirname($storagePath) . '/responsive';
        $disk->makeDirectory($dir);
        $outDir = $disk->path($dir);

        foreach ($widths as $w) {
            if ($w > $srcW) continue;
            $h = (int) round($srcH * ($w / $srcW));

            $resized = imagecreatetruecolor($w, $h);
            // Flatten PNG/WebP alpha onto white so JPEG encoders don't emit
            // grey halos on transparent pixels.
            imagefilledrectangle($resized, 0, 0, $w, $h, imagecolorallocate($resized, 255, 255, 255));
            imagecopyresampled($resized, $img, 0, 0, 0, 0, $w, $h, $srcW, $srcH);

            @imagewebp($resized, "{$outDir}/{$basename}-{$w}.webp", 85);
            @imagejpeg($resized, "{$outDir}/{$basename}-{$w}.jpg", 88);

            imagedestroy($resized);
        }

        imagedestroy($img);
    }

    /**
     * Remove the responsive ladder that accompanies an admin-uploaded image.
     * Called whenever the primary file is deleted so we never leave orphans
     * ballooning the public disk.
     */
    private function deleteResponsiveVariants(string $storagePath): void
    {
        $basename = pathinfo($storagePath, PATHINFO_FILENAME);
        $dir = dirname($storagePath) . '/responsive';
        $disk = Storage::disk('public');

        foreach ($disk->files($dir) as $file) {
            if (str_starts_with(basename($file), $basename . '-')) {
                $disk->delete($file);
            }
        }
    }
}
