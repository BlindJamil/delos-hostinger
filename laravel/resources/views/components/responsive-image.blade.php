@props([
    'src',
    'alt' => '',
    'sizes' => '100vw',
    'widths' => [480, 768, 1200, 1600, 2000],
    'loading' => 'lazy',
    'fetchpriority' => null,
    'decoding' => 'async',
])

@php
    $publicDir = public_path();
    $srcStr = (string) $src;

    // Three source kinds:
    //   1. Absolute URL (http/https) — pass through untouched.
    //   2. Admin-uploaded path ("uploads/...") — served from public/storage/
    //      via the storage symlink. No pre-generated responsive variants.
    //   3. Legacy filename (no slashes, lives in public/images/) — has
    //      pre-generated responsive WebP/JPEG variants in public/images/responsive/.
    $isAbsoluteUrl = (bool) preg_match('#^https?://#i', $srcStr);
    $isAdminUpload = str_starts_with($srcStr, 'uploads/');
    $isLegacy = !$isAbsoluteUrl && !$isAdminUpload;

    $basename = pathinfo($srcStr, PATHINFO_FILENAME);

    // Only legacy filenames have pre-generated responsive variants on disk.
    $availableWidths = [];
    if ($isLegacy) {
        foreach ($widths as $w) {
            if (file_exists($publicDir . "/images/responsive/{$basename}-{$w}.webp")) {
                $availableWidths[] = $w;
            }
        }
    }

    $hasResponsive = count($availableWidths) > 0;

    $webpSrcset = '';
    $jpegSrcset = '';
    if ($hasResponsive) {
        $webpParts = [];
        $jpegParts = [];
        foreach ($availableWidths as $w) {
            $webpParts[] = asset("images/responsive/{$basename}-{$w}.webp") . " {$w}w";
            $jpegParts[] = asset("images/responsive/{$basename}-{$w}.jpg") . " {$w}w";
        }
        $webpSrcset = implode(', ', $webpParts);
        $jpegSrcset = implode(', ', $jpegParts);
    }

    // Resolve a concrete URL for the <img src="..."> fallback.
    if ($hasResponsive) {
        $fallbackSrc = asset("images/responsive/{$basename}-" . min($availableWidths) . '.jpg');
    } elseif ($isAbsoluteUrl) {
        $fallbackSrc = $srcStr;
    } elseif ($isAdminUpload) {
        $fallbackSrc = asset('storage/' . $srcStr);
    } else {
        $fallbackSrc = asset('images/' . $srcStr);
    }

    $imgAttrs = $attributes->except(['src', 'alt', 'sizes', 'widths', 'loading', 'fetchpriority', 'decoding']);
@endphp

@if ($hasResponsive)
<picture>
    <source type="image/webp" srcset="{{ $webpSrcset }}" sizes="{{ $sizes }}">
    <img src="{{ $fallbackSrc }}"
         srcset="{{ $jpegSrcset }}"
         sizes="{{ $sizes }}"
         alt="{{ $alt }}"
         loading="{{ $loading }}"
         decoding="{{ $decoding }}"
         @if($fetchpriority) fetchpriority="{{ $fetchpriority }}" @endif
         {{ $imgAttrs }}>
</picture>
@else
<img src="{{ $fallbackSrc }}"
     alt="{{ $alt }}"
     loading="{{ $loading }}"
     decoding="{{ $decoding }}"
     @if($fetchpriority) fetchpriority="{{ $fetchpriority }}" @endif
     {{ $imgAttrs }}>
@endif
