<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\PageContent;
use Database\Seeders\IraqiArabicContentSeeder;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Admin one-click Iraqi-flavored Arabic copy apply.
 *
 * show()  — preview page: lists every target key with current DB value vs
 *           the new value from IraqiArabicContentSeeder. No writes.
 * apply() — delegates to IraqiArabicContentSeeder::apply() which wraps all
 *           writes in a DB transaction, snapshots beforehand, and busts
 *           caches afterward. Renders the result grid.
 */
class ApplyIraqiCopyController extends Controller
{
    public function show(): View
    {
        $diff = $this->buildDiff();
        return view('admin.apply-iraqi-copy', [
            'diff' => $diff,
            'result' => null,
        ]);
    }

    public function apply(Request $request): View
    {
        $result = IraqiArabicContentSeeder::apply();
        // After apply, the DB values ARE the new values, so re-computing
        // the diff would show "no changes." Still render it so the user
        // sees the post-apply state side by side with what was intended.
        $diff = $this->buildDiff();
        return view('admin.apply-iraqi-copy', [
            'diff' => $diff,
            'result' => $result,
        ]);
    }

    /**
     * Build a per-key diff of current DB value vs new value from the seeder.
     * Keeps strings bounded at 160 chars each so the preview page is
     * readable even for long textareas.
     *
     * @return array{page_content: array<int,array<string,mixed>>, brands: array<int,array<string,mixed>>}
     */
    private function buildDiff(): array
    {
        $clip = fn (?string $s) => $s === null ? null : (mb_strlen($s) > 160 ? mb_substr($s, 0, 160) . '…' : $s);

        $pageRows = [];
        foreach (IraqiArabicContentSeeder::PAGE_CONTENT_UPDATES as $key => $row) {
            $current = PageContent::where('key', $key)->first();
            $currentAr = $current?->value_ar;
            $newAr = $row['value_ar'];
            $pageRows[] = [
                'key' => $key,
                'page' => $row['page'],
                'section' => $row['section'],
                'type' => $row['type'],
                'current_ar' => $clip($currentAr),
                'current_ar_full' => $currentAr,
                'new_ar' => $clip($newAr),
                'new_ar_full' => $newAr,
                'will_change' => $currentAr !== $newAr,
            ];
        }

        $brandRows = [];
        foreach (IraqiArabicContentSeeder::BRAND_UPDATES as $slug => $cols) {
            $current = Brand::where('slug', $slug)->first();
            foreach ($cols as $colName => $newVal) {
                $currentVal = $current?->{$colName};
                $brandRows[] = [
                    'slug' => $slug,
                    'column' => $colName,
                    'current' => $clip($currentVal),
                    'current_full' => $currentVal,
                    'new' => $clip($newVal),
                    'new_full' => $newVal,
                    'will_change' => $currentVal !== $newVal,
                    'row_exists' => (bool) $current,
                ];
            }
        }

        return [
            'page_content' => $pageRows,
            'brands' => $brandRows,
        ];
    }
}
