@extends('layouts.app')

@section('title', pcontent('seo.brands.title'))
@section('description', pcontent('seo.brands.description'))

@section('content')

{{-- Hero: Brand Gallery --}}
<section id="brand-showcase" class="bg-delos-cream pt-28 lg:pt-32 pb-16 lg:pb-24">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        {{-- Header --}}
        <div data-motion-group="brands-hero-header" class="flex flex-col lg:flex-row lg:items-end lg:justify-between mb-12 lg:mb-16 gap-6">
            <div>
                <div data-motion="fade-up" class="inline-flex items-center gap-3 mb-5">
                    <span class="w-8 h-px bg-delos-gold"></span>
                    <span class="text-delos-gold text-[11px] tracking-[0.4em] uppercase font-medium" style="font-family: 'Inter', sans-serif;">{{ pcontent('brands.hero.overline') }}
                        <span class="italian-flag ml-2"><span class="flag-green"></span><span class="flag-white"></span><span class="flag-red"></span></span>
                    </span>
                </div>
                <h1 data-motion="fade-up" class="font-serif text-delos-dark text-4xl lg:text-6xl font-light leading-tight">
                    {{ pcontent('brands.hero.heading_1') }}<br>
                    {{ pcontent('brands.hero.heading_2') }}<br>
                    <em class="text-delos-gold not-italic">{{ pcontent('brands.hero.heading_accent') }}</em>
                </h1>
            </div>
            <p data-motion="fade-up" class="text-delos-muted text-sm leading-relaxed max-w-sm lg:text-right lg:pb-1" style="font-family: 'Inter', sans-serif;">
                {{ pcontent('brands.hero.sub') }}
            </p>
        </div>

        {{-- Brand Grid --}}
        <div id="brand-gallery-grid" class="brand-gallery-grid">
            @foreach($brands as $b)
                @php
                    $i = $loop->index;
                    $bName = $b->name;
                    $bCategory = $b->localized('category');
                    $bSince = $b->since;
                @endphp
                <div class="brand-card card-tilt relative" data-brand-index="{{ $i }}">
                    <x-admin-edit-pill :href="route('admin.brands.edit', $b)" :label="'Edit ' . $bName" />
                    @if($b->url)
                        {{-- Full-card overlay link: lets the whole card act
                             as a clickable link to the brand's official site
                             without nesting anchors (which conflicts with
                             the admin-edit pill that's also an anchor).
                             z-1 sits above the card content, while the admin
                             pill's higher z-index keeps it clickable. --}}
                        <a href="{{ $b->url }}" target="_blank" rel="noopener"
                           aria-label="{{ $bName }} — visit official website"
                           class="absolute inset-0 z-[1]"></a>
                    @endif
                    <div class="brand-card-inner">
                        <div class="brand-card-image">
                            @if($b->imageIsLegacy())
                                <x-responsive-image :src="$b->image" :alt="$bName"
                                    sizes="(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw"
                                    class="w-full h-full object-cover"
                                    :loading="$i === 0 ? 'eager' : 'lazy'"
                                    :fetchpriority="$i === 0 ? 'high' : null" />
                            @elseif($b->image_url)
                                <img src="{{ $b->image_url }}" alt="{{ $bName }}"
                                    sizes="(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw"
                                    class="w-full h-full object-cover"
                                    loading="{{ $i === 0 ? 'eager' : 'lazy' }}"
                                    @if($i === 0) fetchpriority="high" @endif
                                    decoding="async">
                            @endif
                        </div>
                        <div class="brand-card-info">
                            <div class="flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <h2 class="brand-card-name font-serif text-delos-cream text-base md:text-lg lg:text-xl font-light leading-tight truncate">{{ $bName }}</h2>
                                    <p class="brand-card-category text-delos-cream/40 text-[9px] tracking-[0.2em] uppercase mt-0.5 truncate" style="font-family: 'Inter', sans-serif;">{{ $bCategory }}</p>
                                </div>
                                <span class="brand-card-since text-delos-gold/60 text-[9px] tracking-[0.3em] uppercase flex-shrink-0" style="font-family: 'Inter', sans-serif;">{{ $bSince }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Intro --}}
<section class="py-24 lg:py-32 bg-delos-cream">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="grid lg:grid-cols-2 gap-16 items-center mb-24">
            <div data-motion-group="brands-intro">
                <p data-motion="fade-up" class="text-delos-gold text-[11px] tracking-[0.4em] uppercase font-medium mb-6" style="font-family: 'Inter', sans-serif;">{{ pcontent('brands.intro.overline') }}</p>
                <h2 data-motion="fade-up" class="font-serif text-4xl lg:text-5xl font-light text-delos-dark leading-tight mb-8">
                    {{ pcontent('brands.intro.heading_1') }}<br>
                    {{ pcontent('brands.intro.heading_2') }}<br>
                    <em class="text-delos-gold not-italic">{{ pcontent('brands.intro.heading_accent') }}</em>
                </h2>
                <p data-motion="fade-up" class="text-delos-muted text-base leading-relaxed" style="font-family: 'Inter', sans-serif;">
                    {{ pcontent('brands.intro.body') }}
                </p>
            </div>
            <div data-motion="scale-in" class="relative aspect-square overflow-hidden bg-delos-dark">
                <x-responsive-image src="italian-materials.jpg" alt="Italian Craftsmanship"
                    sizes="(min-width: 1024px) 50vw, 100vw"
                    class="w-full h-full object-cover opacity-60" />
                <div class="absolute inset-0 flex items-center justify-center">
                    <p class="font-serif text-delos-cream text-2xl lg:text-3xl font-light italic text-center px-8">
                        &ldquo;{{ pcontent('brands.intro.quote') }}&rdquo;
                    </p>
                </div>
            </div>
        </div>

        {{-- Brand Cards --}}
        <div class="space-y-20">
            @foreach($brands as $brand)
                @php
                    $i = $loop->index;
                    $brandName = $brand->name;
                    $brandCategory = $brand->localized('category');
                    $brandOrigin = $brand->localized('origin');
                    $brandSince = $brand->since;
                    $brandDesc = $brand->localized('description');
                    $brandSpecialties = $brand->{"specialties_" . app()->getLocale()} ?: $brand->specialties_en ?: [];
                @endphp
                <div data-motion="fade-up" class="relative grid lg:grid-cols-2 gap-12 lg:gap-20 items-center {{ $i % 2 === 1 ? 'lg:[&>*:first-child]:order-2' : '' }}">
                    <x-admin-edit-pill :href="route('admin.brands.edit', $brand)" :label="'Edit ' . $brandName" />
                    {{-- Image --}}
                    <div class="relative aspect-[4/3] overflow-hidden bg-delos-dark">
                        @if($brand->imageIsLegacy())
                            <x-responsive-image :src="$brand->image" :alt="$brandName"
                                sizes="(min-width: 1024px) 50vw, 100vw"
                                class="w-full h-full object-cover opacity-65 hover:scale-105 transition-transform duration-700" />
                        @elseif($brand->image_url)
                            <img src="{{ $brand->image_url }}" alt="{{ $brandName }}" loading="lazy" decoding="async"
                                sizes="(min-width: 1024px) 50vw, 100vw"
                                class="w-full h-full object-cover opacity-65 hover:scale-105 transition-transform duration-700">
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-delos-dark/60 to-transparent"></div>
                        <div class="absolute bottom-6 left-6">
                            <p class="font-serif text-delos-cream text-4xl font-semibold tracking-widest">{{ $brandName }}</p>
                        </div>
                    </div>
                    {{-- Content --}}
                    <div>
                        @if($brandCategory)
                            <div class="flex items-center gap-3 mb-6">
                                <span class="text-delos-gold text-[10px] tracking-[0.4em] uppercase px-3 py-1 border border-delos-gold/30" style="font-family: 'Inter', sans-serif;">{{ $brandCategory }}</span>
                            </div>
                        @endif
                        <h2 class="font-serif text-delos-dark text-3xl lg:text-4xl font-light mb-2">{{ $brandName }}</h2>
                        @if($brandOrigin || $brandSince)
                            <p class="text-delos-muted text-xs tracking-wide mb-6" style="font-family: 'Inter', sans-serif;">{{ trim(collect([$brandOrigin, $brandSince])->filter()->join(' · ')) }}</p>
                        @endif
                        @if($brandDesc)
                            <p class="text-delos-muted text-base leading-relaxed mb-8" style="font-family: 'Inter', sans-serif;">{{ $brandDesc }}</p>
                        @endif
                        @if(!empty($brandSpecialties))
                            <ul class="grid grid-cols-2 gap-3">
                                @foreach($brandSpecialties as $spec)
                                    <li class="flex items-center gap-2 text-delos-muted text-sm" style="font-family: 'Inter', sans-serif;">
                                        <span class="w-1 h-1 rounded-full bg-delos-gold flex-shrink-0"></span>
                                        {{ $spec }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                        @if($brand->url)
                            <a href="{{ $brand->url }}" target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center gap-2 mt-8 text-delos-gold text-[11px] tracking-[0.3em] uppercase font-medium border-b border-delos-gold/30 pb-1 hover:border-delos-gold hover:gap-3 transition-all duration-300" style="font-family: 'Inter', sans-serif;">
                                {{ pcontent('common.ctas.visit_official_website') }}
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 17L17 7M17 7H7M17 7v10"/></svg>
                            </a>
                        @endif
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
<section class="py-24 bg-delos-dark text-center">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div data-motion-group="brands-cta">
            <h2 data-motion="fade-up" class="font-serif text-delos-cream text-4xl lg:text-5xl font-light mb-8">
                {{ pcontent('brands.cta.heading_1') }}<br>
                <em class="text-delos-gold not-italic">{{ pcontent('brands.cta.heading_accent') }}</em>
            </h2>
            <a href="{{ lroute('contact') }}"
               data-motion="fade-up"
               class="inline-flex items-center gap-3 px-10 py-4 bg-delos-gold text-delos-dark text-[12px] tracking-[0.25em] uppercase font-medium hover:bg-delos-gold-light transition-all duration-300">
                {{ pcontent('common.ctas.visit_showroom') }}
            </a>
        </div>
    </div>
</section>

@endsection
