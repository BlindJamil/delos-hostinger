@extends('layouts.app')

@section('title', pcontent('seo.home.title'))
@section('description', pcontent('seo.home.description'))

@section('content')

{{-- PAGE LOADER --}}
<div data-page-loader class="page-loader">
    <div class="flex flex-col items-center gap-6">
        <div class="loader-logo flex flex-col items-center gap-3">
            <div class="w-14 h-14 overflow-hidden rounded-full border border-delos-gold/20">
                <img src="{{ asset('images/delos-logo.jpg') }}" alt="" class="w-full h-full object-cover">
            </div>
            <div class="flex flex-col items-center">
                <span class="font-serif text-xl tracking-[0.3em] text-delos-cream font-semibold">{{ pcontent('common.brand.name_primary') }}</span>
                <span class="text-overline-sm text-delos-gold/70">{{ pcontent('common.brand.name_secondary') }}</span>
            </div>
        </div>
        <div class="loader-bar"></div>
    </div>
</div>

{{-- 1. HERO SLIDESHOW --}}
<section id="hero" data-motion-hero class="relative min-h-dvh flex flex-col overflow-hidden bg-delos-dark">
    <x-admin-edit-pill page="home" label="Edit hero slides (images & copy)" />
    <div id="hero-slideshow" class="absolute inset-0">
        @foreach([0,1,2,3,4] as $i)
            {{-- Hero slide alt intentionally empty. Some browsers render
                 the alt text as an inline box even on opacity:0 /
                 visibility:hidden <img>s, which caused the 5 slides to
                 leak overlapping garbled text at the bottom-left. Hero
                 images here are decorative; the page title + overlines
                 carry the accessible description. --}}
            <x-responsive-image
                :src="pcontent('home.hero.slides.' . $i . '.img')"
                :mobile-src="pcontent_optional('home.hero.slides.' . $i . '.img_mobile')"
                :focal="pcontent_optional('home.hero.slides.' . $i . '.img_focal')"
                alt=""
                sizes="100vw"
                class="hero-slide absolute inset-0 w-full h-full object-cover {{ $i === 0 ? '' : 'opacity-0' }}"
                :loading="$i === 0 ? 'eager' : 'lazy'"
                :fetchpriority="$i === 0 ? 'high' : null" />
        @endforeach
    </div>

    <div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-delos-dark/50 to-transparent z-[1] pointer-events-none"></div>

    <div class="absolute bottom-24 left-1/2 -translate-x-1/2 z-10 flex items-center gap-3">
        @for($i = 0; $i < 5; $i++)
            <button class="hero-dot w-2 h-2 rounded-full transition-all duration-500 {{ $i === 0 ? 'bg-delos-gold w-6' : 'bg-delos-cream/40 hover:bg-delos-cream/60' }}"
                    aria-label="Slide {{ $i + 1 }}" data-slide="{{ $i }}"></button>
        @endfor
    </div>

    <div data-motion="fade" data-hero-fade-out class="absolute bottom-8 left-1/2 -translate-x-1/2 z-10 flex flex-col items-center gap-3">
        <span class="text-overline-sm text-delos-cream/50">{{ pcontent('home.hero.scroll_label') }}</span>
        <div class="w-px h-10 bg-delos-cream/25 relative overflow-hidden">
            <div class="scroll-line absolute top-0 left-0 w-full bg-delos-gold/60" style="height:30%"></div>
        </div>
    </div>
</section>

{{-- 2. ABOUT --}}
<section class="section-padding bg-delos-cream">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="grid lg:grid-cols-12 gap-10 lg:gap-20 items-start">
            <div data-motion="slide-left" class="lg:col-span-5">
                <div data-motion-line class="w-16 h-px bg-delos-gold mb-6"></div>
                <p class="text-overline text-delos-gold mb-5">{{ pcontent('home.about.overline') }}</p>
                <h2 class="text-heading-2 text-delos-dark leading-tight">
                    {{ pcontent('home.about.heading_1') }}<br>
                    {{ pcontent('home.about.heading_2') }} <em class="text-delos-gold not-italic">{{ pcontent('home.about.heading_accent') }}</em>
                </h2>
            </div>
            <div data-motion="slide-right" class="lg:col-span-7 lg:border-l lg:border-delos-gold/25 lg:pl-12">
                <p class="scroll-text text-body text-delos-muted mb-8">{{ pcontent('home.about.body') }}</p>
                <blockquote class="pull-quote mb-10">{{ pcontent('home.about.quote') }}</blockquote>
                <a href="{{ lroute('about') }}" class="btn-ripple inline-flex items-center gap-2 text-delos-dark text-btn hover:text-delos-gold transition-colors duration-300 group">
                    {{ pcontent('common.ctas.learn_more') }} <svg class="w-3.5 h-3.5 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>
    </div>
</section>

{{-- 2.5 VIDEO --}}
<section class="section-padding bg-delos-cream">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div data-motion="scale-in" class="relative w-full rounded-xl overflow-hidden shadow-2xl" style="aspect-ratio: 16 / 9;">
            <x-admin-edit-pill page="home" label="Edit brand video (file & poster)" />
            <video id="delos-video" class="absolute inset-0 w-full h-full object-cover"
                   preload="metadata" playsinline muted
                   poster="{{ pcontent_url('home.video.poster', asset('images/video-poster.jpg')) }}">
                <source src="{{ pcontent_url('home.video.source', asset('videos/delos-brand.mp4')) }}" type="video/mp4">
            </video>
            <div id="video-overlay" class="absolute inset-0 z-[1] transition-opacity duration-700 cursor-pointer">
                {{-- Soft radial vignette — darkens only around the play CTA
                     so text stays legible, while the poster edges stay
                     luminous. Matches the "less darkness" brand direction. --}}
                <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(26,26,30,0.45)_0%,rgba(26,26,30,0.15)_45%,rgba(26,26,30,0)_75%)]"></div>
                <div class="absolute inset-0 flex flex-col items-center justify-center z-[2]">
                    <p class="text-overline text-delos-gold mb-6 [text-shadow:0_1px_12px_rgba(0,0,0,0.45)]">{{ pcontent('home.video.overline') }}</p>
                    <button id="video-play-btn" class="group relative w-20 h-20 lg:w-24 lg:h-24 rounded-full border-2 border-delos-gold/70 flex items-center justify-center hover:border-delos-gold hover:scale-110 transition-all duration-500 bg-delos-dark/30 backdrop-blur-md shadow-[0_8px_40px_rgba(0,0,0,0.35)]">
                        <svg class="w-7 h-7 lg:w-8 lg:h-8 text-delos-gold ml-1 group-hover:scale-110 transition-transform duration-300" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                        <span class="absolute inset-0 rounded-full border border-delos-gold/20 animate-ping"></span>
                    </button>
                    <p class="font-serif text-delos-cream/80 text-sm mt-6 tracking-wide [text-shadow:0_1px_12px_rgba(0,0,0,0.45)]">{{ pcontent('home.video.watch') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 3. NEW COLLECTION --}}
<section class="section-padding bg-delos-ivory">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div data-motion-group="featured-header" class="flex flex-col lg:flex-row lg:items-end lg:justify-between mb-16 gap-6">
            <div>
                <div data-motion-line class="w-16 h-px bg-delos-gold mb-5"></div>
                <p data-motion="fade-up" class="text-overline text-delos-gold mb-5">{{ pcontent('home.collection.overline') }}</p>
                <h2 data-motion="fade-up" class="text-heading-2 text-delos-dark leading-tight">{{ pcontent('home.collection.heading') }}</h2>
            </div>
            <a href="{{ lroute('projects') }}" data-motion="fade-up" class="btn-ripple self-start inline-flex items-center gap-2 text-delos-muted text-btn font-medium hover:text-delos-gold transition-colors duration-300 group">
                {{ pcontent('common.ctas.view_all_projects') }} <svg class="w-3.5 h-3.5 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @php
                $collectionSlugs = ['villa_classica', 'nero_contemporary', 'eleganza_living', 'mediterranean_living'];
                $motionVariants = ['slide-left', 'slide-right', 'slide-left', 'slide-up'];
            @endphp
            @foreach($collectionSlugs as $idx => $slug)
                <a href="{{ lroute('projects') }}" data-motion="{{ $motionVariants[$idx] }}" class="collection-card group relative aspect-[4/3] overflow-hidden bg-delos-dark block">
                    <x-admin-edit-pill page="home" :label="'Edit collection card ' . ($idx + 1)" />
                    <x-responsive-image
                        :src="pcontent('home.collection.items.' . $slug . '.img')"
                        :mobile-src="pcontent_optional('home.collection.items.' . $slug . '.img_mobile')"
                        :focal="pcontent_optional('home.collection.items.' . $slug . '.img_focal')"
                        :alt="pcontent('home.collection.items.' . $slug . '.title')"
                        sizes="(min-width: 768px) 50vw, 100vw"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" />
                    <span class="absolute top-6 left-7 font-serif text-delos-gold/20 text-6xl lg:text-7xl font-light z-[4] group-hover:text-delos-gold/40 transition-colors duration-500">{{ pcontent("home.collection.items.{$slug}.num") }}</span>
                    <div class="absolute bottom-0 left-0 right-0 p-7 lg:p-9 z-[4] translate-y-2 group-hover:translate-y-0 transition-transform duration-500">
                        <p class="text-overline-sm text-delos-gold mb-2">{{ pcontent("home.collection.items.{$slug}.brand") }}</p>
                        <h3 class="font-serif text-delos-cream text-2xl lg:text-3xl font-light mb-3 group-hover:text-delos-gold transition-colors duration-300">{{ pcontent("home.collection.items.{$slug}.title") }}</h3>
                        <p class="text-body-sm text-delos-cream/0 group-hover:text-delos-cream/60 transition-all duration-500 max-w-sm leading-relaxed">{{ pcontent("home.collection.items.{$slug}.desc") }}</p>
                    </div>
                    <div class="absolute bottom-0 left-0 w-0 h-[2px] bg-delos-gold z-[5] group-hover:w-full transition-all duration-700 ease-out"></div>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- 4. EMPLOYEES --}}
<section id="employees-section" class="section-padding bg-delos-dark overflow-hidden">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div data-motion-group="employee-header" class="text-center mb-12 lg:mb-16">
            <div data-motion-line class="w-16 h-px bg-delos-gold mx-auto mb-5"></div>
            <p data-motion="fade-up" class="text-overline text-delos-gold mb-5">{{ pcontent('home.employees.overline') }}</p>
            <h2 data-motion="fade-up" class="text-heading-2 text-delos-cream">{{ pcontent('home.employees.heading') }}</h2>
            <p data-motion="fade-up" class="text-body text-delos-cream/40 mt-5 max-w-xl mx-auto">{{ pcontent('home.employees.sub') }}</p>
        </div>
    </div>
    <div id="employee-viewport">
    <div id="employee-track" class="flex gap-6 px-6 lg:px-12">
        @forelse(($featuredEmployees ?? collect()) as $emp)
            @php
                $empName = $emp->localized('name');
                $empRole = $emp->localized('role');
                $empBranch = $emp->branch;
                // Card teaser: first sentence of the bio, trimmed to its first
                // clause if long. Keeps the card tight (~1 line) while the full
                // bio lives on the /team/{id} detail page.
                $empPlain = trim(strip_tags((string) $emp->localized('achievement')));
                $empFirstSentence = $empPlain === ''
                    ? ''
                    : (preg_split('/(?<=[.!?؟])\s+/u', $empPlain, 2)[0] ?? $empPlain);
                if (mb_strlen($empFirstSentence) > 85) {
                    $empFirstSentence = preg_split('/\s*[,،—–]\s*/u', $empFirstSentence, 2)[0] ?? $empFirstSentence;
                }
                $empAchievement = rtrim($empFirstSentence, " .!?؟,");
            @endphp
            <div data-motion="fade-up" class="employee-slide employee-card card-tilt group relative w-[350px] lg:w-[420px] aspect-[3/4] overflow-hidden bg-delos-dark-2 flex-shrink-0">
                <x-admin-edit-pill :href="route('admin.employees.edit', $emp)" :label="'Edit ' . $empName" />
                <a href="{{ lroute('employee-show', ['employee' => $emp->id]) }}"
                   class="employee-card__link absolute inset-0 z-[4]"
                   aria-label="View profile of {{ $empName }}">
                    <span class="sr-only">{{ $empName }}</span>
                </a>
                @if($emp->image)
                    <x-responsive-image :src="$emp->image"
                        :mobile-src="$emp->image_mobile"
                        :focal="$emp->focal_point"
                        :alt="$empName . ' — ' . $empRole" sizes="(min-width: 1024px) 420px, 350px"
                        class="absolute inset-0 w-full h-full object-cover opacity-60" />
                @endif
                <div class="employee-overlay absolute inset-0 flex flex-col justify-end p-6 lg:p-8 z-[3] pointer-events-none">
                    @if($empBranch)
                        <p class="text-overline-sm text-delos-gold mb-3">{{ $empBranch }}</p>
                    @endif
                    <h3 class="font-serif text-delos-cream text-2xl font-light mb-1">{{ $empName }}</h3>
                    <p class="text-overline-sm text-delos-cream/50 mb-4">{{ $empRole }}</p>
                    @if($empAchievement)
                        <p class="text-body-sm text-delos-cream/40 leading-relaxed group-hover:text-delos-cream/60 transition-colors duration-500 employee-achievement">{{ $empAchievement }}</p>
                    @endif
                </div>
            </div>
        @empty
            {{-- Fallback to legacy lang-file entries if DB is empty (safety net) --}}
            @foreach(pcontent('home.employees.items') as $slug => $emp)
                <div data-motion="fade-up" class="employee-slide employee-card card-tilt group relative w-[350px] lg:w-[420px] aspect-[3/4] overflow-hidden bg-delos-dark-2 flex-shrink-0">
                    <x-responsive-image :src="$emp['img']" :alt="$emp['name'] . ' — ' . $emp['role']" sizes="(min-width: 1024px) 420px, 350px" class="absolute inset-0 w-full h-full object-cover opacity-60" />
                    <div class="employee-overlay absolute inset-0 flex flex-col justify-end p-6 lg:p-8 z-[3]">
                        <p class="text-overline-sm text-delos-gold mb-3">{{ $emp['branch'] }}</p>
                        <h3 class="font-serif text-delos-cream text-2xl font-light mb-1">{{ $emp['name'] }}</h3>
                        <p class="text-overline-sm text-delos-cream/50 mb-4">{{ $emp['role'] }}</p>
                        <p class="text-body-sm text-delos-cream/40 leading-relaxed group-hover:text-delos-cream/60 transition-colors duration-500">&ldquo;{{ $emp['achievement'] }}&rdquo;</p>
                    </div>
                </div>
            @endforeach
        @endforelse
    </div>
    </div>
</section>

{{-- 5. STATS --}}
<section class="section-padding bg-delos-cream">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div data-motion-group="stats-header" class="text-center mb-16">
            <div data-motion-line class="w-16 h-px bg-delos-gold mx-auto mb-5"></div>
            <p data-motion="fade-up" class="text-overline text-delos-gold mb-5">{{ pcontent('home.stats.overline') }}</p>
            <h2 data-motion="fade-up" class="text-heading-2 text-delos-dark">{{ pcontent('home.stats.heading') }}</h2>
        </div>
        <div data-motion-group="stats-grid" class="flex flex-col lg:flex-row items-center justify-center gap-12 lg:gap-16">
            @foreach([0,1,2,3] as $i)
                <div data-motion="fade-up" class="text-center">
                    <span class="stat-number" data-motion-counter="{{ pcontent("home.stats.items.{$i}.value") }}">{{ pcontent("home.stats.items.{$i}.value") }}</span>
                    @if(pcontent("home.stats.items.{$i}.suffix"))
                        <span class="stat-suffix font-serif text-2xl text-delos-gold font-light">{{ pcontent("home.stats.items.{$i}.suffix") }}</span>
                    @endif
                    <p class="text-overline text-delos-muted mt-3">{{ pcontent("home.stats.items.{$i}.label") }}</p>
                </div>
                @if($i < 3)
                    <div class="stat-divider"></div>
                @endif
            @endforeach
        </div>
    </div>
</section>

{{-- 6. BRANDS --}}
<section id="brands-section" class="section-padding bg-delos-dark">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div data-motion-group="brands-intro" class="text-center mb-16">
            <div data-motion-line class="w-16 h-px bg-delos-gold mx-auto mb-5"></div>
            <p data-motion="fade-up" class="text-overline text-delos-gold mb-5">{{ pcontent('home.brands.overline') }}</p>
            <h2 data-motion="fade-up" class="text-heading-2 text-delos-cream">{{ pcontent('home.brands.heading') }}</h2>
            <p data-motion="fade-up" class="text-body text-delos-cream/40 mt-5 max-w-2xl mx-auto">{{ pcontent('home.brands.sub') }}</p>
        </div>
        <div data-motion="fade-up" class="brand-marquee-wrapper overflow-hidden mb-12 border-t border-b border-delos-cream/5 py-8">
            <div class="brand-marquee flex whitespace-nowrap">
                @for($i = 0; $i < 4; $i++)
                    <div class="brand-marquee-inner flex items-center gap-10 lg:gap-16 px-5 lg:px-8">
                        @foreach(['LUBE', 'Frigerio', 'Vittoria Frigerio', 'CANTORI', 'SKEMA'] as $brand)
                            <a href="{{ lroute('brands') }}" class="brand-strip-item font-serif text-delos-cream/40 text-2xl lg:text-4xl font-light tracking-wider hover:text-delos-gold transition-colors duration-400 pb-1">{{ $brand }}</a>
                            <span class="text-delos-gold/20 text-xs select-none">&#9670;</span>
                        @endforeach
                    </div>
                @endfor
            </div>
        </div>
        <div data-motion="fade-up" class="text-center">
            <a href="{{ lroute('brands') }}" class="btn-ripple inline-flex items-center gap-2 text-delos-gold text-btn font-medium hover:text-delos-gold-light transition-colors duration-300 group">
                {{ pcontent('common.ctas.explore_partners') }} <svg class="w-3.5 h-3.5 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </div>
</section>

{{-- 7. CTA --}}
<section id="cta-section" class="relative section-padding overflow-hidden bg-delos-dark">
    <x-admin-edit-pill page="home" label="Edit CTA content & background image" />
    <div class="absolute inset-0">
        <x-responsive-image :src="pcontent('home.cta.image', 'delos-erbil-showroom-5.jpg')"
            :mobile-src="pcontent_optional('home.cta.image_mobile')"
            :focal="pcontent_optional('home.cta.image_focal')"
            alt="" sizes="100vw" class="w-full h-full object-cover opacity-22" />
    </div>
    <div data-motion-group="cta" class="relative z-10 max-w-[1400px] mx-auto px-6 lg:px-12 text-center">
        <div data-motion-line class="w-16 h-px bg-delos-gold mx-auto mb-8"></div>
        <p data-motion="fade-up" class="text-overline text-delos-gold/50 mb-8">{{ pcontent('home.cta.overline') }}
            <span class="italian-flag"><span class="flag-green"></span><span class="flag-white"></span><span class="flag-red"></span></span>
        </p>
        <h2 data-motion="fade-up" class="font-serif text-delos-cream text-[clamp(2rem,4.5vw,3.8rem)] font-light leading-tight mb-10 max-w-2xl mx-auto">{{ pcontent('home.cta.heading') }}</h2>
        <div data-motion="fade-up" class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ lroute('contact') }}" class="magnetic-btn btn-ripple inline-flex items-center gap-3 px-9 py-4 bg-delos-gold text-delos-dark text-btn hover:bg-delos-gold-light transition-colors duration-300 group">
                {{ pcontent('common.ctas.book_consultation') }} <svg class="w-3.5 h-3.5 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
            <a href="tel:{{ preg_replace('/\s+/', '', $settingPhone ?: '07501001701') }}" class="magnetic-btn btn-ripple inline-flex items-center gap-3 px-9 py-4 border border-delos-cream/20 text-delos-cream text-btn font-medium hover:border-delos-gold hover:text-delos-gold transition-colors duration-300">
                {{ pcontent('common.ctas.call_us') }}
            </a>
        </div>
    </div>
</section>

@endsection
