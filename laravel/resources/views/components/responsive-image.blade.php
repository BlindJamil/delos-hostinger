@props([
    'src',
    'mobileSrc' => null,           // optional — alternate image for phones (<= 767px)
    'focal' => null,               // optional — CSS object-position string e.g. '30% 60%'
    'alt' => '',
    'sizes' => '100vw',
    'widths' => [480, 768, 1200, 1600, 2000, 2560, 3200],
    'loading' => 'lazy',
    'fetchpriority' => null,
    'decoding' => 'async',
])

@php
    // Mobile breakpoint — matches Tailwind's md: threshold (md starts at 768px,
    // so the phone media query is max-width: 767px). If you ever retune the
    // design system's breakpoints, update this in lockstep.
    $mobileMediaQuery = '(max-width: 767px)';

    // Treat "50% 50%" as "no focal" so un-tuned images emit byte-identical
    // HTML to the pre-hybrid version. Any non-centered value is applied.
    $normalizedFocal = $focal !== null ? trim((string) $focal) : null;
    $hasFocal = $normalizedFocal !== null
        && $normalizedFocal !== ''
        && preg_replace('/\s+/', ' ', $normalizedFocal) !== '50% 50%';

    /**
     * Resolve one of the three accepted source kinds into a concrete URL +
     * optional responsive srcset. Legacy filenames living in public/images/
     * get the pre-generated WebP + JPEG variants. Admin uploads and absolute
     * URLs are served as a single 1x image (no pre-generation pipeline).
     */
    $resolveSrc = function (string $srcStr) use ($widths) {
        $publicDir = public_path();
        $basename  = pathinfo($srcStr, PATHINFO_FILENAME);
        $isAbsoluteUrl = (bool) preg_match('#^https?://#i', $srcStr);
        $isAdminUpload = str_starts_with($srcStr, 'uploads/');
        $isLegacy      = !$isAbsoluteUrl && !$isAdminUpload;

        $availableWidths = [];
        if ($isLegacy) {
            // Scan disk for any generated variant widths (lets the pipeline
            // expose native-resolution sources like 1878, 1920, 1765 without
            // requiring them to be listed in the $widths prop).
            $pattern = $publicDir . "/images/responsive/{$basename}-*.webp";
            foreach (glob($pattern) ?: [] as $match) {
                if (preg_match('/-(\d+)\.webp$/', basename($match), $m)) {
                    $availableWidths[] = (int) $m[1];
                }
            }
            sort($availableWidths);
            $availableWidths = array_values(array_unique($availableWidths));
        }

        $hasResponsive = count($availableWidths) > 0;
        $webpSrcset = $jpegSrcset = '';
        if ($hasResponsive) {
            $webpParts = $jpegParts = [];
            foreach ($availableWidths as $w) {
                $webpParts[] = asset("images/responsive/{$basename}-{$w}.webp") . " {$w}w";
                $jpegParts[] = asset("images/responsive/{$basename}-{$w}.jpg") . " {$w}w";
            }
            $webpSrcset = implode(', ', $webpParts);
            $jpegSrcset = implode(', ', $jpegParts);
        }

        if ($hasResponsive) {
            $fallbackSrc = asset("images/responsive/{$basename}-" . min($availableWidths) . '.jpg');
        } elseif ($isAbsoluteUrl) {
            $fallbackSrc = $srcStr;
        } elseif ($isAdminUpload) {
            $fallbackSrc = asset('storage/' . $srcStr);
        } else {
            $fallbackSrc = asset('images/' . $srcStr);
        }

        return [
            'webpSrcset' => $webpSrcset,
            'jpegSrcset' => $jpegSrcset,
            'fallbackSrc' => $fallbackSrc,
            'hasResponsive' => $hasResponsive,
        ];
    };

    $desktop = $resolveSrc((string) $src);
    $mobile  = $mobileSrc ? $resolveSrc((string) $mobileSrc) : null;

    $imgAttrs = $attributes->except(['src', 'alt', 'sizes', 'widths', 'loading', 'fetchpriority', 'decoding', 'mobile-src', 'focal', 'mobileSrc']);
    $styleAttr = $hasFocal ? sprintf(' style="object-position: %s"', e($normalizedFocal)) : '';
    $priorityAttr = $fetchpriority ? ' fetchpriority="' . e($fetchpriority) . '"' : '';
@endphp

{{--
    Three rendering shapes, in order of decreasing specificity:

      1. Mobile variant set → <picture> with a phone-first <source media>
         entry, then the normal desktop srcset / fallback. Any image whose
         md: breakpoint needs a different crop uses this path.
      2. No mobile variant but legacy filename with pre-generated variants
         → <picture> with webp + jpeg srcsets (current behavior).
      3. Absolute URL or admin upload with no responsive variants → plain
         <img>. Used for uploads/, http://, etc.

    The focal point (object-position) is applied to the final <img> element
    in every shape so it follows regardless of which branch renders.
--}}

@if ($mobile)
<picture>
    @if ($mobile['hasResponsive'])
        <source type="image/webp"
                media="{{ $mobileMediaQuery }}"
                srcset="{{ $mobile['webpSrcset'] }}"
                sizes="{{ $sizes }}">
        <source type="image/jpeg"
                media="{{ $mobileMediaQuery }}"
                srcset="{{ $mobile['jpegSrcset'] }}"
                sizes="{{ $sizes }}">
    @else
        <source media="{{ $mobileMediaQuery }}"
                srcset="{{ $mobile['fallbackSrc'] }}">
    @endif

    @if ($desktop['hasResponsive'])
        <source type="image/webp"
                srcset="{{ $desktop['webpSrcset'] }}"
                sizes="{{ $sizes }}">
        <img src="{{ $desktop['fallbackSrc'] }}"
             srcset="{{ $desktop['jpegSrcset'] }}"
             sizes="{{ $sizes }}"
             alt="{{ $alt }}"
             loading="{{ $loading }}"
             decoding="{{ $decoding }}"{!! $priorityAttr !!}{!! $styleAttr !!}
             {{ $imgAttrs }}>
    @else
        <img src="{{ $desktop['fallbackSrc'] }}"
             alt="{{ $alt }}"
             loading="{{ $loading }}"
             decoding="{{ $decoding }}"{!! $priorityAttr !!}{!! $styleAttr !!}
             {{ $imgAttrs }}>
    @endif
</picture>
@elseif ($desktop['hasResponsive'])
<picture>
    <source type="image/webp" srcset="{{ $desktop['webpSrcset'] }}" sizes="{{ $sizes }}">
    <img src="{{ $desktop['fallbackSrc'] }}"
         srcset="{{ $desktop['jpegSrcset'] }}"
         sizes="{{ $sizes }}"
         alt="{{ $alt }}"
         loading="{{ $loading }}"
         decoding="{{ $decoding }}"{!! $priorityAttr !!}{!! $styleAttr !!}
         {{ $imgAttrs }}>
</picture>
@else
<img src="{{ $desktop['fallbackSrc'] }}"
     alt="{{ $alt }}"
     loading="{{ $loading }}"
     decoding="{{ $decoding }}"{!! $priorityAttr !!}{!! $styleAttr !!}
     {{ $imgAttrs }}>
@endif
