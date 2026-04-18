@extends('layouts.app')

@section('title', pcontent('seo.services.title'))
@section('description', pcontent('seo.services.description'))

@section('content')

{{-- Hero — magazine-cover full-bleed editorial layout --}}
<section data-motion-hero class="svc-hero relative min-h-dvh overflow-hidden bg-delos-dark">

    {{-- Full-bleed background image with slow Ken-Burns parallax zoom --}}
    <div class="svc-hero__image absolute inset-0">
        <img src="{{ pcontent_url('services.hero.image', asset('images/collection-lube-classic.jpg')) }}"
             alt=""
             class="w-full h-full object-cover"
             fetchpriority="high"
             decoding="async">
    </div>

    {{-- Bottom-anchored gradient — keeps image vivid at top, text legible at bottom --}}
    <div class="svc-hero__overlay absolute inset-0 pointer-events-none"></div>

    {{-- Top-left overline --}}
    <div data-motion="fade" class="absolute top-28 lg:top-32 left-6 lg:left-12 z-[2] flex items-center gap-3">
        <span class="w-8 h-px bg-delos-gold/70"></span>
        <span class="text-delos-gold text-[10px] lg:text-[11px] tracking-[0.5em] uppercase font-semibold" style="font-family: 'Inter', sans-serif;">{{ pcontent('services.hero.overline') }}</span>
    </div>

    {{-- Vertically centered heading, inset from the left edge with
         a constrained max-width so the text feels like an editorial
         caption rather than spanning the whole viewport. --}}
    <div class="absolute inset-0 z-[2] flex items-center px-6 sm:px-12 lg:px-20 xl:px-28">
        <div class="max-w-[1400px] mx-auto w-full">
            <h1 class="font-serif text-delos-cream font-light leading-[1.1] text-4xl sm:text-5xl lg:text-6xl xl:text-7xl max-w-xl lg:max-w-3xl [text-shadow:0_2px_30px_rgba(0,0,0,0.35)]">
                <span data-motion="fade-up" class="block">{{ pcontent('services.hero.heading_1_before') }}</span>
                <span data-motion="fade-up" class="block"><em class="text-delos-gold italic font-light">{{ pcontent('services.hero.heading_1_accent') }}</em></span>
                <span data-motion="fade-up" class="block">{{ pcontent('services.hero.heading_2') }}</span>
            </h1>
        </div>
    </div>

    {{-- Scroll cue --}}
    <div data-motion="fade" class="absolute bottom-6 left-1/2 -translate-x-1/2 z-[2] flex flex-col items-center gap-3">
        <span class="text-delos-cream/50 text-[9px] tracking-[0.5em] uppercase" style="font-family: 'Inter', sans-serif;">{{ pcontent('home.hero.scroll_label') }}</span>
        <div class="w-px h-10 bg-delos-cream/25 relative overflow-hidden">
            <div class="scroll-line absolute top-0 left-0 w-full bg-delos-gold/60" style="height:30%"></div>
        </div>
    </div>

</section>

{{-- Services Detail --}}
<section class="py-24 lg:py-32 bg-delos-cream">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">

        <div data-motion-group="services-intro" class="text-center mb-20 lg:mb-24">
            <div data-motion-line class="w-16 h-px bg-delos-gold mx-auto mb-5"></div>
            <p data-motion="fade-up" class="text-overline text-delos-gold mb-5">{{ pcontent('services.intro.overline') }}</p>
            <h2 data-motion="fade-up" class="text-heading-2 text-delos-dark">{{ pcontent('services.intro.heading_1') }}<br><em class="text-delos-gold not-italic">{{ pcontent('services.intro.heading_accent') }}</em></h2>
        </div>

        <div class="space-y-24">
            @foreach(($services ?? collect()) as $s)
                @php
                    $i = $loop->index;
                    $sName = $s->localized('name');
                    $sDesc = $s->localized('description');
                    $sFeatures = $s->{"features_" . app()->getLocale()} ?: $s->features_en ?: [];
                @endphp
                <div id="service-{{ $s->num ?: $s->slug }}" data-motion="fade-up" class="relative grid lg:grid-cols-2 gap-12 lg:gap-20 items-center {{ $i % 2 === 1 ? 'lg:[&>*:first-child]:order-2' : '' }}">
                    <x-admin-edit-pill :href="route('admin.services.edit', $s)" :label="'Edit ' . $sName" />

                    {{-- Image --}}
                    <div class="relative aspect-[4/3] overflow-hidden bg-delos-dark">
                        @if($s->imageIsLegacy())
                            <x-responsive-image :src="$s->image" :alt="$sName"
                                sizes="(min-width: 1024px) 50vw, 100vw"
                                class="w-full h-full object-cover hover:scale-105 transition-transform duration-700" />
                        @elseif($s->image_url)
                            <img src="{{ $s->image_url }}" alt="{{ $sName }}" loading="lazy" decoding="async"
                                sizes="(min-width: 1024px) 50vw, 100vw"
                                class="w-full h-full object-cover hover:scale-105 transition-transform duration-700">
                        @endif
                    </div>

                    {{-- Text --}}
                    <div>
                        @if($s->num)
                            <span class="text-delos-gold text-[11px] tracking-[0.3em] font-medium mb-4 block" style="font-family: 'Inter', sans-serif;">{{ $s->num }}</span>
                        @endif
                        <h2 class="font-serif text-delos-dark text-3xl lg:text-4xl font-light leading-tight mb-6">
                            {{ $sName }}
                        </h2>
                        @if($sDesc)
                            <p class="text-delos-muted text-base leading-relaxed mb-8" style="font-family: 'Inter', sans-serif;">
                                {{ $sDesc }}
                            </p>
                        @endif
                        @if(!empty($sFeatures))
                            <ul class="space-y-3 mb-8">
                                @foreach($sFeatures as $feat)
                                    <li class="flex items-center gap-3 text-delos-muted text-sm" style="font-family: 'Inter', sans-serif;">
                                        <span class="w-1 h-1 rounded-full bg-delos-gold flex-shrink-0"></span>
                                        {{ $feat }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                        @if($s->brand)
                            <p class="text-delos-gold text-[11px] tracking-[0.3em] uppercase font-medium mb-4" style="font-family: 'Inter', sans-serif;">{{ $s->brand }}</p>
                        @endif
                        <a href="{{ lroute('contact') }}"
                           class="inline-flex items-center gap-2 text-delos-dark text-[12px] tracking-[0.2em] uppercase font-medium hover:text-delos-gold transition-colors duration-300 group border-b border-delos-dark/20 pb-1 hover:border-delos-gold" style="font-family: 'Inter', sans-serif;">
                            {{ pcontent('services.item_cta') }}
                            <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                    </div>

                </div>
                @if(!$loop->last)
                    <div class="border-t border-delos-dark/10"></div>
                @endif
            @endforeach
        </div>

    </div>
</section>

{{-- CTA --}}
<section class="py-24 lg:py-32 bg-delos-dark text-center">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div data-motion-group="services-cta">
        <p data-motion="fade-up" class="text-delos-gold text-[11px] tracking-[0.4em] uppercase font-medium mb-6" style="font-family: 'Inter', sans-serif;">{{ pcontent('services.cta.overline') }}</p>
        <h2 data-motion="fade-up" class="font-serif text-delos-cream text-4xl lg:text-6xl font-light leading-tight mb-8">
            {{ pcontent('services.cta.heading_1') }}<br>
            <em class="text-delos-gold not-italic">{{ pcontent('services.cta.heading_accent') }}</em>
        </h2>
        <a href="{{ lroute('contact') }}"
           data-motion="fade-up"
           class="inline-flex items-center gap-3 px-10 py-4 bg-delos-gold text-delos-dark text-[12px] tracking-[0.25em] uppercase font-medium hover:bg-delos-gold-light transition-all duration-300 group">
            {{ pcontent('services.cta.button') }}
            <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
        </a>
        </div>
    </div>
</section>

@endsection
