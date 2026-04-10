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
    $sourcePath = $publicDir . '/images/' . $src;
    $basename = pathinfo($src, PATHINFO_FILENAME);

    // Only include widths that actually exist on disk (skip upscales)
    $availableWidths = [];
    foreach ($widths as $w) {
        if (file_exists($publicDir . "/images/responsive/{$basename}-{$w}.webp")) {
            $availableWidths[] = $w;
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

    // Smallest available variant as the fallback src (not the original full-size file)
    $fallbackSrc = $hasResponsive
        ? asset("images/responsive/{$basename}-" . min($availableWidths) . '.jpg')
        : asset('images/' . $src);

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
<img src="{{ asset('images/' . $src) }}"
     alt="{{ $alt }}"
     loading="{{ $loading }}"
     decoding="{{ $decoding }}"
     @if($fetchpriority) fetchpriority="{{ $fetchpriority }}" @endif
     {{ $imgAttrs }}>
@endif
