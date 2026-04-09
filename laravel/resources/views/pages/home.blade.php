@extends('layouts.app')

@section('title', 'Delos International — Italian Luxury Solutions in Iraq')
@section('description', 'Delos International brings the finest Italian luxury interior design to Iraq. Premium kitchens, furniture, wardrobes, and turnkey solutions across Erbil, Kirkuk, Sulaymaniyah, and Baghdad.')

@section('content')

{{-- PAGE LOADER --}}
<div data-page-loader class="page-loader">
    <div class="flex flex-col items-center gap-6">
        <div class="loader-logo flex flex-col items-center gap-3">
            <div class="w-14 h-14 overflow-hidden rounded-full border border-delos-gold/20">
                <img src="{{ asset('images/delos-logo.jpg') }}" alt="" class="w-full h-full object-cover">
            </div>
            <div class="flex flex-col items-center">
                <span class="font-serif text-xl tracking-[0.3em] text-delos-cream font-semibold">DELOS</span>
                <span class="text-overline-sm text-delos-gold/70">INTERNATIONAL</span>
            </div>
        </div>
        <div class="loader-bar"></div>
    </div>
</div>

{{-- 1. HERO SLIDESHOW --}}
<section id="hero" data-motion-hero class="relative min-h-dvh flex flex-col overflow-hidden bg-delos-dark">
    {{-- Slideshow images --}}
    <div id="hero-slideshow" class="absolute inset-0">
        @php
            $heroSlides = [
                ['img' => 'cantori-4.jpg', 'alt' => 'Cantori Italian Outdoor Living — Luxury Terrace Design'],
                ['img' => 'collection-lube-classic.jpg', 'alt' => 'LUBE Agnese Style Classic Kitchen — Italian Luxury'],
                ['img' => 'collection-vittoria.jpg',     'alt' => 'Vittoria Frigerio Luxury Living Room — Italian Elegance'],
                ['img' => 'cantori-1.jpg',               'alt' => 'CANTORI Italian Luxury Furniture Collection'],
                ['img' => 'lube-kitchen-2.jpg',          'alt' => 'LUBE Modern Italian Kitchen Design'],
            ];
        @endphp
        @foreach($heroSlides as $i => $slide)
            <img src="{{ asset('images/' . $slide['img']) }}"
                 alt="{{ $slide['alt'] }}"
                 class="hero-slide absolute inset-0 w-full h-full object-cover {{ $i === 0 ? '' : 'opacity-0' }}"
                 {{ $i === 0 ? 'fetchpriority=high' : 'loading=lazy' }}>
        @endforeach
    </div>

    {{-- Bottom vignette for scroll indicator readability --}}
    <div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-delos-dark/50 to-transparent z-[1] pointer-events-none"></div>

    {{-- Slide indicator dots --}}
    <div class="absolute bottom-24 left-1/2 -translate-x-1/2 z-10 flex items-center gap-3">
        @for($i = 0; $i < count($heroSlides); $i++)
            <button class="hero-dot w-2 h-2 rounded-full transition-all duration-500 {{ $i === 0 ? 'bg-delos-gold w-6' : 'bg-delos-cream/40 hover:bg-delos-cream/60' }}"
                    aria-label="Go to slide {{ $i + 1 }}" data-slide="{{ $i }}"></button>
        @endfor
    </div>

    {{-- Scroll indicator --}}
    <div data-motion="fade" data-hero-fade-out class="absolute bottom-8 left-1/2 -translate-x-1/2 z-10 flex flex-col items-center gap-3">
        <span class="text-overline-sm text-delos-cream/50">Scroll</span>
        <div class="w-px h-10 bg-delos-cream/25 relative overflow-hidden">
            <div class="scroll-line absolute top-0 left-0 w-full bg-delos-gold/60" style="height:30%"></div>
        </div>
    </div>
</section>

{{-- 2. ABOUT --}}
<section class="section-padding bg-delos-cream">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="grid lg:grid-cols-2 gap-16 lg:gap-24 items-center">
            <div data-motion="slide-left" class="relative aspect-[4/3] overflow-hidden bg-delos-dark rounded-sm">
                <img src="{{ asset('images/delos-building.jpg') }}" alt="Delos International Building — Erbil"
                     class="w-full h-full object-cover object-[70%_30%] opacity-50" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-delos-dark via-delos-dark/30 to-transparent z-[1]"></div>
                <div class="absolute inset-0 bg-gradient-to-r from-delos-dark/40 to-transparent z-[1]"></div>
                <div class="absolute bottom-6 left-7 z-[2]">
                    <p class="text-overline-sm text-delos-gold mb-1">Established 2020</p>
                    <p class="font-serif text-delos-cream/60 text-sm">Erbil, Kurdistan Region</p>
                </div>
            </div>
            <div data-motion="slide-right" class="about-text-col border-l-2 border-delos-gold pl-8">
                <div data-motion-line class="w-16 h-px bg-delos-gold mb-6"></div>
                <p class="text-overline text-delos-gold mb-5">About Delos</p>
                <h2 class="text-heading-2 text-delos-dark mb-6">Where Italian craft<br>meets Iraqi homes.</h2>
                <p class="scroll-text text-body text-delos-muted mb-6">Delos International is a distinguished company based in Iraq, dedicated to bringing the finest in Italian luxury design to homes across the region. Representing an exclusive portfolio of premium Italian brands, Delos curates a complete lifestyle experience defined by elegance, craftsmanship, and precision.</p>
                <blockquote class="pull-quote mb-8">The name "Delos" draws inspiration from the Greek island — a historic symbol of civilization and wisdom. Our lion emblem represents strength, protection, and trust.</blockquote>
                <a href="{{ route('about') }}" class="btn-ripple inline-flex items-center gap-2 text-delos-dark text-btn hover:text-delos-gold transition-colors duration-300 group">
                    Learn More <svg class="w-3.5 h-3.5 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>
    </div>
</section>

{{-- 2.5 VIDEO --}}
<section class="section-padding bg-delos-cream">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        {{-- 16:9 video container --}}
        <div data-motion="scale-in" class="relative w-full rounded-xl overflow-hidden shadow-2xl" style="aspect-ratio: 16 / 9;">
            <video id="delos-video" class="absolute inset-0 w-full h-full object-cover"
                   preload="metadata" playsinline muted
                   poster="{{ asset('images/video-poster.jpg') }}">
                <source src="{{ asset('videos/delos-brand.mp4') }}" type="video/mp4">
            </video>
            <div id="video-overlay" class="absolute inset-0 bg-delos-dark/65 z-[1] transition-opacity duration-700 cursor-pointer">
                <div class="absolute inset-0 bg-gradient-to-t from-delos-dark/80 via-delos-dark/30 to-delos-dark/50"></div>
                <div class="absolute inset-0 flex flex-col items-center justify-center z-[2]">
                    <p class="text-overline text-delos-gold mb-6">Discover Delos</p>
                    <button id="video-play-btn" class="group relative w-20 h-20 lg:w-24 lg:h-24 rounded-full border-2 border-delos-gold/50 flex items-center justify-center hover:border-delos-gold hover:scale-110 transition-all duration-500 bg-delos-dark/40 backdrop-blur-md">
                        <svg class="w-7 h-7 lg:w-8 lg:h-8 text-delos-gold ml-1 group-hover:scale-110 transition-transform duration-300" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                        <span class="absolute inset-0 rounded-full border border-delos-gold/20 animate-ping"></span>
                    </button>
                    <p class="font-serif text-delos-cream/60 text-sm mt-6 tracking-wide">Watch Our Story</p>
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
                <p data-motion="fade-up" class="text-overline text-delos-gold mb-5">Featured</p>
                <h2 data-motion="fade-up" class="text-heading-2 text-delos-dark leading-tight">New Collection</h2>
            </div>
            <a href="{{ route('projects') }}" data-motion="fade-up" class="btn-ripple self-start inline-flex items-center gap-2 text-delos-muted text-btn font-medium hover:text-delos-gold transition-colors duration-300 group">
                View All Projects <svg class="w-3.5 h-3.5 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <a href="{{ route('projects') }}" data-motion="slide-left" class="collection-card group relative aspect-[4/3] overflow-hidden bg-delos-dark block">
                <img src="{{ asset('images/collection-lube-classic.jpg') }}" alt="LUBE Agnese Style Classic Kitchen — Italian Luxury" class="w-full h-full object-cover opacity-50 group-hover:opacity-70 transition-all duration-700" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-delos-dark via-delos-dark/30 to-transparent z-[3]"></div>
                <span class="absolute top-6 left-7 font-serif text-delos-gold/20 text-6xl lg:text-7xl font-light z-[4] group-hover:text-delos-gold/40 transition-colors duration-500">01</span>
                <div class="absolute bottom-0 left-0 right-0 p-7 lg:p-9 z-[4] translate-y-2 group-hover:translate-y-0 transition-transform duration-500">
                    <p class="text-overline-sm text-delos-gold mb-2">LUBE Kitchens</p>
                    <h3 class="font-serif text-delos-cream text-2xl lg:text-3xl font-light mb-3 group-hover:text-delos-gold transition-colors duration-300">Villa Classica Kitchen</h3>
                    <p class="text-body-sm text-delos-cream/0 group-hover:text-delos-cream/60 transition-all duration-500 max-w-sm leading-relaxed">A masterpiece of Italian craftsmanship, blending classic elegance with modern functionality.</p>
                </div>
                <div class="absolute bottom-0 left-0 w-0 h-[2px] bg-delos-gold z-[5] group-hover:w-full transition-all duration-700 ease-out"></div>
            </a>
            <a href="{{ route('projects') }}" data-motion="slide-right" class="collection-card group relative aspect-[4/3] overflow-hidden bg-delos-dark block">
                <img src="{{ asset('images/collection-lube-modern.jpg') }}" alt="LUBE Clover Modern Kitchen — Contemporary Italian Design" class="w-full h-full object-cover opacity-50 group-hover:opacity-70 transition-all duration-700" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-delos-dark via-delos-dark/30 to-transparent z-[3]"></div>
                <span class="absolute top-6 left-7 font-serif text-delos-gold/20 text-6xl lg:text-7xl font-light z-[4] group-hover:text-delos-gold/40 transition-colors duration-500">02</span>
                <div class="absolute bottom-0 left-0 right-0 p-7 lg:p-9 z-[4] translate-y-2 group-hover:translate-y-0 transition-transform duration-500">
                    <p class="text-overline-sm text-delos-gold mb-2">CREO Kitchens</p>
                    <h3 class="font-serif text-delos-cream text-2xl lg:text-3xl font-light mb-3 group-hover:text-delos-gold transition-colors duration-300">Nero Contemporary</h3>
                    <p class="text-body-sm text-delos-cream/0 group-hover:text-delos-cream/60 transition-all duration-500 max-w-sm leading-relaxed">Sleek minimalism meets Italian precision in this modern kitchen collection.</p>
                </div>
                <div class="absolute bottom-0 left-0 w-0 h-[2px] bg-delos-gold z-[5] group-hover:w-full transition-all duration-700 ease-out"></div>
            </a>
            <a href="{{ route('projects') }}" data-motion="slide-left" class="collection-card group relative aspect-[4/3] overflow-hidden bg-delos-dark block">
                <img src="{{ asset('images/collection-vittoria.jpg') }}" alt="Vittoria Frigerio Luxury Living Room — Italian Elegance" class="w-full h-full object-cover opacity-50 group-hover:opacity-70 transition-all duration-700" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-delos-dark via-delos-dark/30 to-transparent z-[3]"></div>
                <span class="absolute top-6 left-7 font-serif text-delos-gold/20 text-6xl lg:text-7xl font-light z-[4] group-hover:text-delos-gold/40 transition-colors duration-500">03</span>
                <div class="absolute bottom-0 left-0 right-0 p-7 lg:p-9 z-[4] translate-y-2 group-hover:translate-y-0 transition-transform duration-500">
                    <p class="text-overline-sm text-delos-gold mb-2">Frigerio</p>
                    <h3 class="font-serif text-delos-cream text-2xl lg:text-3xl font-light mb-3 group-hover:text-delos-gold transition-colors duration-300">Eleganza Living</h3>
                    <p class="text-body-sm text-delos-cream/0 group-hover:text-delos-cream/60 transition-all duration-500 max-w-sm leading-relaxed">Refined Italian furniture that elevates every living space with timeless sophistication.</p>
                </div>
                <div class="absolute bottom-0 left-0 w-0 h-[2px] bg-delos-gold z-[5] group-hover:w-full transition-all duration-700 ease-out"></div>
            </a>
            <a href="{{ route('projects') }}" data-motion="slide-up" class="collection-card group relative aspect-[4/3] overflow-hidden bg-delos-dark block">
                <img src="{{ asset('images/collection-cantori.jpg') }}" alt="CANTORI Outdoor Dining — Mediterranean Poolside Living" class="w-full h-full object-cover opacity-50 group-hover:opacity-70 transition-all duration-700" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-delos-dark via-delos-dark/30 to-transparent z-[3]"></div>
                <span class="absolute top-6 left-7 font-serif text-delos-gold/20 text-6xl lg:text-7xl font-light z-[4] group-hover:text-delos-gold/40 transition-colors duration-500">04</span>
                <div class="absolute bottom-0 left-0 right-0 p-7 lg:p-9 z-[4] translate-y-2 group-hover:translate-y-0 transition-transform duration-500">
                    <p class="text-overline-sm text-delos-gold mb-2">CANTORI</p>
                    <h3 class="font-serif text-delos-cream text-2xl lg:text-3xl font-light mb-3 group-hover:text-delos-gold transition-colors duration-300">Mediterranean Living</h3>
                    <p class="text-body-sm text-delos-cream/0 group-hover:text-delos-cream/60 transition-all duration-500 max-w-sm leading-relaxed">Timeless outdoor elegance inspired by the warmth of the Mediterranean coast.</p>
                </div>
                <div class="absolute bottom-0 left-0 w-0 h-[2px] bg-delos-gold z-[5] group-hover:w-full transition-all duration-700 ease-out"></div>
            </a>
        </div>
    </div>
</section>

{{-- 4. EMPLOYEES --}}
<section id="employees-section" class="section-padding bg-delos-dark overflow-hidden">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div data-motion-group="employee-header" class="text-center mb-12 lg:mb-16">
            <div data-motion-line class="w-16 h-px bg-delos-gold mx-auto mb-5"></div>
            <p data-motion="fade-up" class="text-overline text-delos-gold mb-5">Our Team</p>
            <h2 data-motion="fade-up" class="text-heading-2 text-delos-cream">Best Employees of the Month</h2>
            <p data-motion="fade-up" class="text-body text-delos-cream/40 mt-5 max-w-xl mx-auto">Recognizing the dedicated professionals behind every exceptional Delos project.</p>
        </div>
    </div>
    <div id="employee-track" class="flex gap-6 px-6 lg:px-12 lg:justify-center lg:mx-auto lg:max-w-[1400px]">
        @php
            $employees = [
                ['name' => 'Ahmed K.', 'role' => 'Lead Interior Designer', 'branch' => 'Erbil', 'img' => 'employee-1.jpg', 'achievement' => 'Led the design of 12 luxury villa transformations across Erbil this quarter.'],
                ['name' => 'Sara M.',  'role' => 'Senior Engineer',        'branch' => 'Sulaymaniyah', 'img' => 'employee-2.jpg', 'achievement' => 'Revolutionized our 3D concept workflow, cutting design timelines by 40%.'],
                ['name' => 'Omar R.',  'role' => 'Project Manager',        'branch' => 'Baghdad', 'img' => 'employee-3.jpg', 'achievement' => 'Spearheaded the successful launch of our new Baghdad showroom.'],
            ];
        @endphp
        @foreach($employees as $emp)
            <div data-motion="fade-up" class="employee-slide employee-card card-tilt group relative w-[350px] lg:w-[420px] aspect-[3/4] overflow-hidden bg-delos-dark-2 flex-shrink-0">
                <img src="{{ asset('images/' . $emp['img']) }}" alt="{{ $emp['name'] }} — {{ $emp['role'] }}" class="absolute inset-0 w-full h-full object-cover opacity-60" loading="lazy">
                <div class="employee-overlay absolute inset-0 flex flex-col justify-end p-6 lg:p-8 z-[3]">
                    <p class="text-overline-sm text-delos-gold mb-3">{{ $emp['branch'] }}</p>
                    <h3 class="font-serif text-delos-cream text-2xl font-light mb-1">{{ $emp['name'] }}</h3>
                    <p class="text-overline-sm text-delos-cream/50 mb-4">{{ $emp['role'] }}</p>
                    <p class="text-body-sm text-delos-cream/40 leading-relaxed group-hover:text-delos-cream/60 transition-colors duration-500">"{{ $emp['achievement'] }}"</p>
                </div>
            </div>
        @endforeach
    </div>
</section>

{{-- 5. STATS --}}
<section class="section-padding bg-delos-cream">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div data-motion-group="stats-header" class="text-center mb-16">
            <div data-motion-line class="w-16 h-px bg-delos-gold mx-auto mb-5"></div>
            <p data-motion="fade-up" class="text-overline text-delos-gold mb-5">By The Numbers</p>
            <h2 data-motion="fade-up" class="text-heading-2 text-delos-dark">A Legacy in Numbers</h2>
        </div>
        <div data-motion-group="stats-grid" class="flex flex-col lg:flex-row items-center justify-center gap-12 lg:gap-16">
            <div data-motion="fade-up" class="text-center">
                <span class="stat-number" data-motion-counter="500">500</span>
                <span class="stat-suffix font-serif text-2xl text-delos-gold font-light">+</span>
                <p class="text-overline text-delos-muted mt-3">Projects Completed</p>
            </div>
            <div class="stat-divider"></div>
            <div data-motion="fade-up" class="text-center">
                <span class="stat-number" data-motion-counter="5">5</span>
                <p class="text-overline text-delos-muted mt-3">Exclusive Italian Houses</p>
            </div>
            <div class="stat-divider"></div>
            <div data-motion="fade-up" class="text-center">
                <span class="stat-number" data-motion-counter="4">4</span>
                <p class="text-overline text-delos-muted mt-3">Showrooms in Iraq</p>
            </div>
            <div class="stat-divider"></div>
            <div data-motion="fade-up" class="text-center">
                <span class="stat-number" data-motion-counter="6">6</span>
                <p class="text-overline text-delos-muted mt-3">Years of Excellence</p>
            </div>
        </div>
    </div>
</section>

{{-- 6. BRANDS --}}
<section id="brands-section" class="section-padding bg-delos-dark">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div data-motion-group="brands-intro" class="text-center mb-16">
            <div data-motion-line class="w-16 h-px bg-delos-gold mx-auto mb-5"></div>
            <p data-motion="fade-up" class="text-overline text-delos-gold mb-5">Exclusive Partnerships</p>
            <h2 data-motion="fade-up" class="text-heading-2 text-delos-cream">Italian Brand Partners</h2>
            <p data-motion="fade-up" class="text-body text-delos-cream/40 mt-5 max-w-2xl mx-auto">Delos proudly partners with leading Italian brands in kitchens, furniture, wardrobes, and flooring — delivering authentic Italian design, premium materials, and world-class craftsmanship to the Iraqi market.</p>
        </div>
        {{-- Infinite marquee ticker (Furnexa "Trusted by Homeowners" style) --}}
        <div data-motion="fade-up" class="brand-marquee-wrapper overflow-hidden mb-12 border-t border-b border-delos-cream/5 py-8">
            <div class="brand-marquee flex whitespace-nowrap">
                @for($i = 0; $i < 4; $i++)
                    <div class="brand-marquee-inner flex items-center gap-10 lg:gap-16 px-5 lg:px-8">
                        @foreach(['LUBE', 'Frigerio', 'Vittoria Frigerio', 'CANTORI', 'SKEMA'] as $brand)
                            <a href="{{ route('brands') }}" class="brand-strip-item font-serif text-delos-cream/40 text-2xl lg:text-4xl font-light tracking-wider hover:text-delos-gold transition-colors duration-400 pb-1">{{ $brand }}</a>
                            <span class="text-delos-gold/20 text-xs select-none">&#9670;</span>
                        @endforeach
                    </div>
                @endfor
            </div>
        </div>
        <div data-motion="fade-up" class="text-center">
            <a href="{{ route('brands') }}" class="btn-ripple inline-flex items-center gap-2 text-delos-gold text-btn font-medium hover:text-delos-gold-light transition-colors duration-300 group">
                Explore Our Partners <svg class="w-3.5 h-3.5 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </div>
</section>

{{-- 7. CTA --}}
<section id="cta-section" class="relative section-padding overflow-hidden bg-delos-dark">
    <div class="absolute inset-0">
        <img src="{{ asset('images/delos-erbil-showroom-5.jpg') }}" alt="" class="w-full h-full object-cover opacity-22" loading="lazy">
    </div>
    <div data-motion-group="cta" class="relative z-10 max-w-[1400px] mx-auto px-6 lg:px-12 text-center">
        <div data-motion-line class="w-16 h-px bg-delos-gold mx-auto mb-8"></div>
        <p data-motion="fade-up" class="text-overline text-delos-gold/50 mb-8">Your Home Awaits
            <span class="italian-flag"><span class="flag-green"></span><span class="flag-white"></span><span class="flag-red"></span></span>
        </p>
        <h2 data-motion="fade-up" class="font-serif text-delos-cream text-[clamp(2rem,4.5vw,3.8rem)] font-light leading-tight mb-10 max-w-2xl mx-auto">Begin your Italian luxury journey today.</h2>
        <div data-motion="fade-up" class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('contact') }}" class="magnetic-btn btn-ripple inline-flex items-center gap-3 px-9 py-4 bg-delos-gold text-delos-dark text-btn hover:bg-delos-gold-light transition-colors duration-300 group">
                Book Consultation <svg class="w-3.5 h-3.5 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
            <a href="tel:07501001701" class="magnetic-btn btn-ripple inline-flex items-center gap-3 px-9 py-4 border border-delos-cream/20 text-delos-cream text-btn font-medium hover:border-delos-gold hover:text-delos-gold transition-colors duration-300">
                Call 0750 100 1701
            </a>
        </div>
    </div>
</section>

@endsection
