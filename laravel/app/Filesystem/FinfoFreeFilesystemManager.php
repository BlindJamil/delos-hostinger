<?php

namespace App\Filesystem;

use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Filesystem\LocalFilesystemAdapter as LaravelLocalFilesystemAdapter;
use League\Flysystem\Local\LocalFilesystemAdapter as LocalAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;
use League\Flysystem\Visibility;
use League\MimeTypeDetection\ExtensionMimeTypeDetector;

/**
 * Drop-in replacement for Laravel's FilesystemManager that avoids the
 * ext-fileinfo dependency for local disks (used by 'local' and 'public').
 *
 * Flysystem's LocalFilesystemAdapter defaults to a finfo-based MIME
 * detector. Some shared-hosting PHP builds (this app's production host)
 * ship without ext-fileinfo installed, which throws a fatal "Class finfo
 * not found" the moment any file is written to a local disk — 500ing
 * every admin image upload. Every upload path in this app is already
 * restricted to a known extension whitelist by FormRequest validation
 * (mimes:jpg,jpeg,png,webp), so detecting MIME type from the file
 * extension alone is sufficient and needs no PHP extension at all.
 */
class FinfoFreeFilesystemManager extends FilesystemManager
{
    public function createLocalDriver(array $config, string $name = 'local')
    {
        $visibility = PortableVisibilityConverter::fromArray(
            $config['permissions'] ?? [],
            $config['directory_visibility'] ?? $config['visibility'] ?? Visibility::PRIVATE
        );

        $links = ($config['links'] ?? null) === 'skip'
            ? LocalAdapter::SKIP_LINKS
            : LocalAdapter::DISALLOW_LINKS;

        $adapter = new LocalAdapter(
            $config['root'], $visibility, $config['lock'] ?? LOCK_EX, $links,
            new ExtensionMimeTypeDetector(),
        );

        return (new LaravelLocalFilesystemAdapter(
            $this->createFlysystem($adapter, $config), $adapter, $config
        ))->diskName(
            $name
        )->shouldServeSignedUrls(
            $config['serve'] ?? false,
            fn () => $this->app['url'],
        );
    }
}
