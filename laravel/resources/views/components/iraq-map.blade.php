@props([
    /** Collection|array of Branch models with latitude + longitude set. */
    'branches' => [],
])

@php
    use App\Support\Cartography;

    // Load the SVG inline so we can animate individual paths via JS.
    // The file is tiny (~1.5KB) and only read once per render.
    $svgPath = resource_path('svg/iraq-map.svg');
    $svgMarkup = file_exists($svgPath) ? file_get_contents($svgPath) : '';

    // Pre-project each branch to viewBox coordinates server-side. Keeps the
    // blade loop free of math and means no JS is needed for positioning.
    $pins = collect($branches)
        ->filter(fn ($b) => $b->latitude && $b->longitude)
        ->map(function ($branch) {
            $projected = Cartography::projectLatLng((float) $branch->latitude, (float) $branch->longitude);
            return [
                'branch' => $branch,
                'x' => $projected['x'] ?? null,
                'y' => $projected['y'] ?? null,
            ];
        })
        ->filter(fn ($p) => $p['x'] !== null)
        ->values();
@endphp

<figure class="iraq-map" data-iraq-map>
    <figcaption class="sr-only">
        {{ __('branches.map_caption', ['count' => $pins->count()]) }}
    </figcaption>

    {{-- Editorial cartouche: top-left --}}
    <div class="iraq-map__cartouche iraq-map__cartouche--top-left" aria-hidden="true">
        <span class="iraq-map__cartouche-rule"></span>
        <span class="iraq-map__cartouche-text">
            <span class="iraq-map__cartouche-brand">Delos</span>
            <span class="iraq-map__cartouche-dot">·</span>
            <span class="iraq-map__cartouche-meta">{{ $pins->count() }} {{ trans_choice('branches.cartouche_showroom', $pins->count()) }}</span>
        </span>
    </div>

    {{-- Compass rose: bottom-right --}}
    <svg class="iraq-map__compass" viewBox="0 0 48 48" aria-hidden="true">
        <circle cx="24" cy="24" r="18" fill="none" stroke="currentColor" stroke-width="0.6" opacity="0.35"/>
        <circle cx="24" cy="24" r="12" fill="none" stroke="currentColor" stroke-width="0.4" opacity="0.2"/>
        <path d="M24 5 L27 24 L24 21 L21 24 Z" fill="currentColor" opacity="0.9"/>
        <path d="M24 43 L27 24 L24 27 L21 24 Z" fill="currentColor" opacity="0.25"/>
        <text x="24" y="3" text-anchor="middle" font-size="4" fill="currentColor" opacity="0.6" font-family="'Cormorant Garamond', serif" letter-spacing="0.3">N</text>
    </svg>

    <div class="iraq-map__canvas">
        {{-- Pure geography layer (inlined SVG) --}}
        {!! $svgMarkup !!}

        {{-- Pins + labels overlay — uses the SAME viewBox so coordinates line up --}}
        <svg
            class="iraq-map__pins"
            viewBox="0 0 {{ Cartography::VIEWBOX_WIDTH }} {{ Cartography::VIEWBOX_HEIGHT }}"
            preserveAspectRatio="xMidYMid meet"
            aria-hidden="true"
        >
            @foreach($pins as $i => $pin)
                @php
                    $b = $pin['branch'];
                    $name = $b->localized('name');
                    $radius = 8;
                    // Place labels to the right of pins for most; flip to left for
                    // the far-east pins so text doesn't spill off the viewBox.
                    $labelAnchor = $pin['x'] > 780 ? 'end' : 'start';
                    $labelDx = $pin['x'] > 780 ? -14 : 14;
                    // Default: label sits beside the pin at y=4.
                    $labelDy = 4;
                    $sublabelDy = 20;
                    $connectorY2 = 0;
                    // Collision check — if any earlier pin sits within 14 SVG units
                    // vertically AND points its label the same direction, push this
                    // label below the pin so Kirkuk/Sulaymaniyah (Δy ≈ 9) stop overlapping.
                    foreach ($pins->take($i) as $prior) {
                        $priorSide = $prior['x'] > 780 ? 'end' : 'start';
                        if (abs($pin['y'] - $prior['y']) < 14 && $priorSide === $labelAnchor) {
                            $labelDy = 26;
                            $sublabelDy = 42;
                            $connectorY2 = 16;
                            break;
                        }
                    }
                @endphp

                <g
                    class="iraq-map__pin-group"
                    data-branch-key="{{ $b->city_key }}"
                    data-pin-index="{{ $i }}"
                    transform="translate({{ $pin['x'] }} {{ $pin['y'] }})"
                >
                    {{-- Pulse halos — CSS animation handles timing --}}
                    <circle class="iraq-map__pin-halo iraq-map__pin-halo--outer" r="{{ $radius * 3.5 }}" />
                    <circle class="iraq-map__pin-halo iraq-map__pin-halo--inner" r="{{ $radius * 2 }}" />

                    {{-- Connector line from pin to label (thin, decorative) --}}
                    <line
                        class="iraq-map__pin-connector"
                        x1="0" y1="0"
                        x2="{{ $labelDx * 0.7 }}" y2="{{ $connectorY2 }}"
                    />

                    {{-- The pin dot itself, wrapped in <a> for keyboard nav --}}
                    <a
                        href="#branch-{{ $b->city_key }}"
                        class="iraq-map__pin-link"
                        aria-label="{{ $name }} — jump to branch details"
                    >
                        <circle class="iraq-map__pin" r="{{ $radius }}" />
                    </a>

                    {{-- City label + year, rendered as HTML inside <foreignObject>
                         so Arabic text gets full browser text-shaping instead
                         of SVG's brittle Arabic rendering. `dir` + `lang` on
                         the inner div let the browser handle RTL/LTR naturally. --}}
                    @php
                        // HTML labels need a bounding box. Width 160 covers the
                        // longest city name at 22px Cairo/Cormorant. Height 36
                        // gives the glyphs vertical room (Arabic has tall marks).
                        // When labelAnchor is 'end', the text must sit to the
                        // LEFT of the anchor point → shift the foreignObject
                        // left by its width. Otherwise it sits to the right.
                        $boxWidth = 160;
                        $boxHeight = 36;
                        $boxX = $labelAnchor === 'end' ? $labelDx - $boxWidth : $labelDx;
                        // Vertically center on the old SVG baseline: SVG text y
                        // was the baseline, foreignObject y is the top edge, so
                        // shift up by ~half font-size.
                        $boxY = $labelDy - ($boxHeight / 2) + 2;
                        $boxYSub = $sublabelDy - ($boxHeight / 2) + 2;
                        $htmlAlign = $labelAnchor === 'end' ? 'right' : 'left';
                    @endphp
                    <foreignObject
                        x="{{ $boxX }}"
                        y="{{ $boxY }}"
                        width="{{ $boxWidth }}"
                        height="{{ $boxHeight }}"
                        style="overflow: visible;"
                    >
                        <div xmlns="http://www.w3.org/1999/xhtml"
                             class="iraq-map__pin-label"
                             lang="{{ app()->getLocale() }}"
                             dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
                             style="text-align: {{ $htmlAlign }};">{{ $name }}</div>
                    </foreignObject>

                    @if($b->localized('established'))
                        <foreignObject
                            x="{{ $boxX }}"
                            y="{{ $boxYSub }}"
                            width="{{ $boxWidth }}"
                            height="{{ $boxHeight }}"
                            style="overflow: visible;"
                        >
                            <div xmlns="http://www.w3.org/1999/xhtml"
                                 class="iraq-map__pin-sublabel"
                                 style="text-align: {{ $htmlAlign }};">{{ $b->localized('established') }}</div>
                        </foreignObject>
                    @endif
                </g>
            @endforeach
        </svg>
    </div>
</figure>
