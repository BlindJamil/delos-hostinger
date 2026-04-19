<?php

namespace App\Http\Controllers\Admin\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Shared helpers for the five model controllers (brands, services, projects,
 * employees, branches) that manage the hybrid responsive image fields:
 *
 *   image_mobile  — optional phone-only variant
 *   focal_point   — optional CSS object-position string ("30% 60%")
 *
 * Each controller already handles the primary `image` upload inline (paths
 * differ per model). These helpers only deal with the two new columns so
 * the existing upload logic stays untouched and we don't accidentally
 * rewire the old, working path.
 */
trait HandlesHybridImages
{
    /**
     * Persist image_mobile upload / removal + focal_point directly onto
     * the given model. Call after the primary image upload has been
     * handled so the model already has an `id` and image state.
     *
     * @param  Model   $model       The saved model instance (brand/service/…)
     * @param  Request $request     The current admin form request
     * @param  string  $uploadDir   "uploads/brands", "uploads/services", etc.
     */
    protected function applyHybridImageFields(Model $model, Request $request, string $uploadDir): void
    {
        // ─── image_mobile ───────────────────────────────────────
        // Replace: a new file was uploaded → delete any existing
        // admin-managed mobile file, store the new one, save the path.
        if ($request->hasFile('image_mobile')) {
            if ($model->image_mobile
                && str_starts_with($model->image_mobile, 'uploads/')
                && Storage::disk('public')->exists($model->image_mobile)) {
                Storage::disk('public')->delete($model->image_mobile);
            }
            $model->image_mobile = $request->file('image_mobile')->store($uploadDir, 'public');
        }
        // Clear: the admin hit "Remove" on the mobile preview → explicit
        // flag. This takes priority over a new upload only if the file
        // input is empty (which it is, since the two actions are mutually
        // exclusive in the UI).
        if ($request->boolean('clear_image_mobile') && !$request->hasFile('image_mobile')) {
            if ($model->image_mobile
                && str_starts_with($model->image_mobile, 'uploads/')
                && Storage::disk('public')->exists($model->image_mobile)) {
                Storage::disk('public')->delete($model->image_mobile);
            }
            $model->image_mobile = null;
        }

        // ─── focal_point ────────────────────────────────────────
        // Accept only well-formed "<0-100>% <0-100>%" strings. Anything
        // else is silently ignored so a typo can't break CSS. The
        // is_string guard covers the bizarre "someone POST'd an array
        // for a string field" case — the FormRequest already rejects
        // that via the 'string' rule, but being defensive keeps this
        // trait safely reusable in other contexts.
        $focalRaw = $request->input('focal_point');
        if (is_string($focalRaw) && $focalRaw !== '') {
            $raw = trim($focalRaw);
            if (preg_match('/^(\d{1,3})%\s+(\d{1,3})%$/', $raw, $m)
                && (int) $m[1] <= 100 && (int) $m[2] <= 100) {
                $normalized = "{$m[1]}% {$m[2]}%";
                // Skip "50% 50%" — centered is the default, no reason to
                // persist it (keeps public HTML byte-identical when the
                // admin simply hasn't tuned the focal yet).
                $model->focal_point = $normalized === '50% 50%' ? null : $normalized;
            }
        }

        // Only save if anything was actually touched. Protects against an
        // unnecessary UPDATE query when the form didn't include any of
        // these fields (e.g. on a legacy brand edit screen that hasn't
        // been rewired yet).
        if ($model->isDirty(['image_mobile', 'focal_point'])) {
            $model->save();
        }
    }

    /**
     * Wipe the image_mobile file from storage during a model delete.
     * Call from the controller's destroy() alongside the existing cleanup
     * for the primary `image`.
     */
    protected function deleteHybridImageFiles(Model $model): void
    {
        if ($model->image_mobile
            && str_starts_with($model->image_mobile, 'uploads/')
            && Storage::disk('public')->exists($model->image_mobile)) {
            Storage::disk('public')->delete($model->image_mobile);
        }
    }
}
