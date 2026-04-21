<?php

namespace App\Http\Controllers\Admin\Concerns;

use Illuminate\Support\Facades\Storage;

/**
 * Generates responsive WebP + JPEG variants beside every admin-uploaded image.
 *
 * Why every admin controller wants this:
 *   Without a responsive ladder, every viewport — phone, laptop, retina —
 *   downloads the same full-resolution file and the browser resamples on
 *   paint. On high-DPR screens this produces visibly soft heroes even when
 *   the source file is sharp, and on phones it wastes bandwidth.
 *
 *   This trait emits a WebP+JPEG set at widths [768, 1200, 1600, 2000,
 *   2560, 3200] (capped at source) in a sibling `responsive/` folder next
 *   to the upload. `<x-responsive-image>` picks those up automatically via
 *   its admin-upload probe, so callers get a proper `<picture>` + srcset
 *   with zero template changes.
 *
 *   WebP-first delivery also dodges Hostinger CDN's JPEG re-compression
 *   (confirmed on this site: /images/responsive/*-2000.jpg gets re-sampled
 *   server-side to 1600×829 while WebP siblings stay intact).
 *
 * Why GD and not sharp/Intervention:
 *   GD ships with PHP — no new composer dep, no new npm dep. The legacy
 *   `scripts/generate-responsive-images.mjs` pipeline uses sharp + mozjpeg
 *   for asset-repo images, which is the right tool there. For per-upload
 *   runtime generation, GD is good enough and avoids a dependency surface.
 */
trait GeneratesResponsiveVariants
{
    /**
     * Generate responsive WebP + JPEG variants next to an admin-uploaded image.
     *
     * Best-effort by design: a single-width encode failure does not throw —
     * the primary upload is already saved and <x-responsive-image> falls
     * back to the full-res file when no variants are found.
     */
    protected function generateResponsiveVariants(string $storagePath): void
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
     * Safe to call on a path whose variants never existed — the directory
     * listing just comes back empty.
     */
    protected function deleteResponsiveVariants(string $storagePath): void
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
