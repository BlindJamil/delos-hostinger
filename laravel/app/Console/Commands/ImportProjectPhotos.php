<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Bulk-imports pre-staged project photo folders into the Projects section,
 * mirroring exactly what the admin "Add Project" form does: first image
 * becomes the cover, the rest become the gallery, files land on the
 * 'public' disk under the same uploads/projects paths the admin uses.
 *
 * Expects a directory of subfolders, one per project, already resized and
 * re-compressed for the web (this does NOT touch source resolution/quality
 * — that happens client-side before upload, see the import runbook).
 *
 *   php artisan projects:import /home/USER/import-staging
 *   php artisan projects:import /home/USER/import-staging --dry-run
 */
class ImportProjectPhotos extends Command
{
    protected $signature = 'projects:import
        {path : Absolute path to the folder of project subfolders}
        {--dry-run : Preview what would be created without writing anything}';

    protected $description = 'Bulk-import project folders (cover + gallery images) into the Projects section';

    public function handle(): int
    {
        $basePath = rtrim((string) $this->argument('path'), '/');
        $dryRun = (bool) $this->option('dry-run');

        if (!is_dir($basePath)) {
            $this->error("Not a directory: {$basePath}");
            return self::FAILURE;
        }

        $projectDirs = collect(scandir($basePath))
            ->filter(fn ($name) => $name !== '.' && $name !== '..' && is_dir("{$basePath}/{$name}"))
            ->sort()
            ->values();

        if ($projectDirs->isEmpty()) {
            $this->warn('No subfolders found — nothing to import.');
            return self::SUCCESS;
        }

        $nextSortOrder = (int) (Project::max('sort_order') ?? 0) + 10;

        foreach ($projectDirs as $folderName) {
            $title = $folderName;
            $dir = "{$basePath}/{$folderName}";

            $existing = Project::whereRaw('LOWER(title_en) = ?', [mb_strtolower($title)])->first();
            if ($existing) {
                $this->warn("SKIP \"{$title}\" — a project with this title already exists (id {$existing->id}). Delete it first if you want to re-import.");
                continue;
            }

            $images = collect(scandir($dir))
                ->filter(fn ($f) => preg_match('/\.(jpe?g|png|webp)$/i', $f))
                ->sort(SORT_NATURAL | SORT_FLAG_CASE)
                ->values();

            if ($images->isEmpty()) {
                $this->warn("SKIP \"{$title}\" — folder has no images.");
                continue;
            }

            $coverFile = $images->first();
            $galleryFiles = $images->slice(1)->values();

            $this->line("\"{$title}\" — cover: {$coverFile}, gallery: {$galleryFiles->count()} image(s)");

            if ($dryRun) {
                continue;
            }

            DB::transaction(function () use ($dir, $title, $coverFile, $galleryFiles, &$nextSortOrder) {
                $coverPath = $this->storeImage("{$dir}/{$coverFile}", 'uploads/projects');

                $project = Project::create([
                    'title_en' => $title,
                    'image' => $coverPath,
                    'active' => true,
                    'featured' => false,
                    'sort_order' => $nextSortOrder,
                ]);
                $nextSortOrder += 10;

                $gallerySortOrder = 10;
                foreach ($galleryFiles as $file) {
                    $path = $this->storeImage("{$dir}/{$file}", 'uploads/projects/gallery');
                    ProjectImage::create([
                        'project_id' => $project->id,
                        'image' => $path,
                        'sort_order' => $gallerySortOrder,
                    ]);
                    $gallerySortOrder += 10;
                }
            });

            $this->info("  created project id " . Project::whereRaw('LOWER(title_en) = ?', [mb_strtolower($title)])->value('id'));
        }

        if ($dryRun) {
            $this->comment('Dry run only — nothing was written. Re-run without --dry-run to commit.');
        }

        return self::SUCCESS;
    }

    private function storeImage(string $sourcePath, string $uploadDir): string
    {
        $extension = pathinfo($sourcePath, PATHINFO_EXTENSION) ?: 'jpg';
        $filename = uniqid('', true) . '.' . $extension;
        $destination = "{$uploadDir}/{$filename}";

        Storage::disk('public')->put($destination, file_get_contents($sourcePath));

        return $destination;
    }
}
