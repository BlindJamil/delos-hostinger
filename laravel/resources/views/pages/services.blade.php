@extends('layouts.app')

@section('title', 'Services — Delos International Italian Luxury Solutions Iraq')
@section('description', 'Delos International offers luxury Italian kitchens, living room furniture, bedrooms, wardrobes, flooring systems, and complete turnkey project execution across Iraq.')

@section('content')

{{-- Hero --}}
<section data-motion-hero class="relative min-h-dvh overflow-hidden bg-delos-cream">

    {{-- Subtle vertical stripe texture --}}
    <div class="absolute inset-0 opacity-[0.04] pointer-events-none" style="background: repeating-linear-gradient(90deg, transparent, transparent 50px, rgba(61,46,42,0.2) 50px, rgba(61,46,42,0.2) 51px);"></div>

    {{-- Giant scrolling marquee text — behind the image --}}
    <div class="absolute inset-0 flex items-center z-[1] overflow-hidden">
        <div class="brand-marquee flex whitespace-nowrap">
            @for($i = 0; $i < 6; $i++)
                <span class="font-serif text-[clamp(6rem,15vw,14rem)] font-light text-delos-dark/[0.05] tracking-wide leading-none flex-shrink-0 px-4 select-none">Our Services</span>
                <span class="font-serif text-[clamp(6rem,15vw,14rem)] font-light text-delos-gold/[0.07] tracking-wide leading-none flex-shrink-0 px-8 select-none">—</span>
            @endfor
        </div>
    </div>

    {{-- Center composition --}}
    <div class="relative z-[2] flex flex-col items-center min-h-dvh px-6 pt-28 lg:pt-36 pb-16">

        {{-- Top heading --}}
        <div class="text-center mb-10 lg:mb-14">
            <div data-motion="fade" class="inline-flex items-center gap-3 mb-5">
                <span class="w-8 h-px bg-delos-gold/50"></span>
                <span class="text-delos-gold text-[10px] tracking-[0.5em] uppercase font-semibold" style="font-family: 'Inter', sans-serif;">What We Offer</span>
                <span class="w-8 h-px bg-delos-gold/50"></span>
            </div>
            <h1 data-motion="fade-up" class="font-serif text-delos-dark text-4xl lg:text-6xl xl:text-7xl font-light leading-[1.08] mb-2">
                Italian luxury, <em class="text-delos-gold not-italic">crafted</em>
            </h1>
            <h1 data-motion="fade-up" class="font-serif text-delos-dark text-4xl lg:text-6xl xl:text-7xl font-light leading-[1.08]">
                for every room.
            </h1>
        </div>

        {{-- Arch carousel --}}
        @php
            $archSlides = [
                ['img' => 'collection-lube-classic.jpg', 'label' => 'LUBE · Luxury Kitchens'],
                ['img' => 'cantori-1.jpg',               'label' => 'CANTORI · Living Room'],
                ['img' => 'collection-vittoria.jpg',     'label' => 'Vittoria Frigerio · Bedroom'],
                ['img' => 'delos-erbil-showroom-3.jpg',  'label' => 'LUBE · Wardrobes'],
                ['img' => 'italian-materials.jpg',       'label' => 'SKEMA · Flooring & Walls'],
                ['img' => 'delos-erbil-showroom-6.jpg',  'label' => 'Delos · Turnkey Projects'],
            ];
        @endphp

        <div data-motion="scale-in" id="svc-arch-carousel" class="relative mb-10 lg:mb-14">
            {{-- Each arch frame is a complete unit: glow + image + label --}}
            @foreach($archSlides as $i => $slide)
                <div class="svc-arch-frame {{ $i > 0 ? 'absolute inset-0 opacity-0 pointer-events-none' : 'relative' }}" data-label="{{ $slide['label'] }}">
                    {{-- Gold arch glow --}}
                    <div class="absolute -inset-4 lg:-inset-8 rounded-t-full bg-gradient-to-b from-delos-gold/15 via-delos-ivory to-delos-cream z-[0]"></div>

                    {{-- Arch image --}}
                    <div class="relative z-[1] w-[300px] sm:w-[400px] lg:w-[480px] xl:w-[540px] overflow-hidden rounded-t-full shadow-2xl">
                        <div class="aspect-[3/4] overflow-hidden">
                            <img src="{{ asset('images/' . $slide['img']) }}" alt="{{ $slide['label'] }}"
                                 class="w-full h-full object-cover"
                                 {{ $i === 0 ? 'fetchpriority=high' : 'loading=lazy' }}>
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Brand label --}}
            <div class="relative z-[2] text-center mt-5">
                <p id="svc-arch-label" class="text-delos-gold text-[10px] tracking-[0.5em] uppercase font-medium" style="font-family: 'Inter', sans-serif;">LUBE · Luxury Kitchens</p>
            </div>
        </div>

    </div>
</section>

{{-- Bottom info row — outside hero so it triggers on scroll --}}
<section class="bg-delos-cream pb-16 lg:pb-20">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div data-motion-group="services-info" class="flex flex-col sm:flex-row items-center justify-center gap-6 sm:gap-12 lg:gap-20 text-center sm:text-left">
            <p data-motion="slide-left" class="text-delos-muted text-sm leading-relaxed max-w-xs" style="font-family: 'Inter', sans-serif;">
                Kitchens, living, bedrooms, wardrobes, flooring, and complete turnkey projects — under one roof.
            </p>
            <div data-motion="fade-up" class="flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-delos-gold"></span>
                <span class="text-delos-muted text-[10px] tracking-[0.4em] uppercase font-medium" style="font-family: 'Inter', sans-serif;">Est. 2020</span>
            </div>
            <a data-motion="slide-right" href="{{ route('contact') }}" class="magnetic-btn btn-ripple inline-flex items-center gap-3 px-7 py-3 bg-delos-gold text-delos-dark text-[11px] tracking-[0.25em] uppercase font-medium hover:bg-delos-gold-light transition-all duration-300 group" style="font-family: 'Inter', sans-serif;">
                Book Consultation
                <svg class="w-3.5 h-3.5 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </div>
</section>

{{-- Service Showcase --}}
<section class="section-padding bg-delos-cream">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div data-motion-group="services-intro" class="text-center mb-16">
            <div data-motion-line class="w-16 h-px bg-delos-gold mx-auto mb-5"></div>
            <p data-motion="fade-up" class="text-overline text-delos-gold mb-5">What We Offer</p>
            <h2 data-motion="fade-up" class="text-heading-2 text-delos-dark">Six Italian crafts.<br><em class="text-delos-gold not-italic">One standard of luxury.</em></h2>
        </div>

        @php
            $heroServices = [
                ['num' => '01', 'name' => 'Luxury Kitchens',    'brand' => 'LUBE Kitchens',      'desc' => 'Precision-engineered Italian kitchens — from classic to ultra-contemporary, designed around your lifestyle.',  'img' => 'lube-kitchen-3.jpg'],
                ['num' => '02', 'name' => 'Living Room',        'brand' => 'Frigerio · CANTORI', 'desc' => 'Sofas, armchairs, and statement pieces combining sculptural beauty with enduring Italian comfort.',             'img' => 'cantori-1.jpg'],
                ['num' => '03', 'name' => 'Bedroom',            'brand' => 'Vittoria Frigerio',  'desc' => 'Complete Italian bedroom collections — beds, headboards, and dressers designed as a sanctuary of calm.',        'img' => 'collection-vittoria.jpg'],
                ['num' => '04', 'name' => 'Wardrobes',          'brand' => 'LUBE · Frigerio',    'desc' => 'Precision-designed dressing systems with integrated lighting, custom fittings, and flawless hardware.',         'img' => 'delos-erbil-showroom-3.jpg'],
                ['num' => '05', 'name' => 'Flooring & Walls',   'brand' => 'SKEMA',              'desc' => 'Premium Italian flooring and wall systems — from rich natural wood to contemporary engineered surfaces.',      'img' => 'italian-materials.jpg'],
                ['num' => '06', 'name' => 'Turnkey Projects',   'brand' => 'All Delos Partners', 'desc' => 'From first consultation to final installation — Delos manages every element with Italian-grade precision.',     'img' => 'delos-erbil-showroom-6.jpg'],
            ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @foreach($heroServices as $i => $s)
                <a href="#service-{{ $s['num'] }}"
                   data-motion="{{ $i % 2 === 0 ? 'slide-left' : 'slide-right' }}"
                   class="collection-card group relative aspect-[4/3] overflow-hidden bg-delos-dark block">
                    <img src="{{ asset('images/' . $s['img']) }}" alt="{{ $s['name'] }}"
                         class="w-full h-full object-cover opacity-50 group-hover:opacity-70 transition-all duration-700"
                         {{ $i === 0 ? 'fetchpriority=high' : 'loading=lazy' }}>
                    <div class="absolute inset-0 bg-gradient-to-t from-delos-dark via-delos-dark/30 to-transparent z-[3]"></div>
                    <span class="absolute top-6 left-7 font-serif text-delos-gold/20 text-6xl lg:text-7xl font-light z-[4] group-hover:text-delos-gold/40 transition-colors duration-500">{{ $s['num'] }}</span>
                    <div class="absolute bottom-0 left-0 right-0 p-7 lg:p-9 z-[4] translate-y-2 group-hover:translate-y-0 transition-transform duration-500">
                        <p class="text-overline-sm text-delos-gold mb-2">{{ $s['brand'] }}</p>
                        <h3 class="font-serif text-delos-cream text-2xl lg:text-3xl font-light mb-3 group-hover:text-delos-gold transition-colors duration-300">{{ $s['name'] }}</h3>
                        <p class="text-body-sm text-delos-cream/0 group-hover:text-delos-cream/60 transition-all duration-500 max-w-sm leading-relaxed">{{ $s['desc'] }}</p>
                    </div>
                    <div class="absolute bottom-0 left-0 w-0 h-[2px] bg-delos-gold z-[5] group-hover:w-full transition-all duration-700 ease-out"></div>
                </a>
            @endforeach
        </div>
    </div>
</section>

{{-- Services Detail --}}
<section class="py-24 lg:py-32 bg-delos-cream">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">

        @php
            $services = [
                [
                    'num'   => '01',
                    'name'  => 'Luxury Kitchens',
                    'desc'  => 'Your kitchen is the heart of your home. We bring Italy\'s finest kitchen manufacturers directly to your door — precision-engineered, beautifully finished, and designed around your lifestyle. From classic to ultra-contemporary, every Delos kitchen is a masterpiece of Italian craft.',
                    'feat'  => ['Custom Italian cabinetry', 'Premium hardware and finishes', 'Integrated appliance solutions', 'Ergonomic spatial design', '3D planning service'],
                    'brand' => 'LUBE Kitchens',
                    'img'   => 'lube-kitchen-1.jpeg',
                ],
                [
                    'num'   => '02',
                    'name'  => 'Living Room',
                    'desc'  => 'Create a living room that reflects who you are. Our Italian living collections offer sofas, armchairs, coffee tables, and statement pieces that combine sculptural beauty with enduring comfort — crafted to elevate your most-lived-in space.',
                    'feat'  => ['Italian sofas and sectionals', 'Coffee and side tables', 'Statement accent chairs', 'TV unit systems', 'Complete room styling'],
                    'brand' => 'Frigerio · CANTORI',
                    'img'   => 'frigerio-living.jpg',
                ],
                [
                    'num'   => '03',
                    'name'  => 'Bedroom',
                    'desc'  => 'Your bedroom should be a sanctuary. Delos delivers complete Italian bedroom collections — from beds and headboards to wardrobes and bedside tables — where every piece is designed to create a space of calm, beauty, and rest.',
                    'feat'  => ['Italian bed frames and headboards', 'Premium mattress bases', 'Bedside tables and dressers', 'Full bedroom coordination', 'Custom finish options'],
                    'brand' => 'Vittoria Frigerio · Frigerio',
                    'img'   => 'collection-vittoria.jpg',
                ],
                [
                    'num'   => '04',
                    'name'  => 'Wardrobes & Dressing Systems',
                    'desc'  => 'Italian wardrobes are an art form. Our dressing systems are precision-designed with integrated lighting, custom interior fittings, and flawless hardware — transforming your storage into an experience of everyday luxury.',
                    'feat'  => ['Walk-in wardrobe design', 'Hinged and sliding door systems', 'Custom interior organization', 'Integrated LED lighting', 'Mirror and glass options'],
                    'brand' => 'LUBE · Frigerio',
                    'img'   => 'delos-showroom-card.jpg',
                ],
                [
                    'num'   => '05',
                    'name'  => 'Flooring & Wall Systems',
                    'desc'  => 'The foundation of great design. SKEMA\'s premium Italian flooring and wall finishing systems provide the perfect base for your Delos interior — from rich natural wood to contemporary engineered surfaces.',
                    'feat'  => ['Premium Italian wood flooring', 'Engineered hardwood systems', 'Wall panelling and cladding', 'Technical flooring solutions', 'Full installation service'],
                    'brand' => 'SKEMA',
                    'img'   => 'italian-materials.jpg',
                ],
                [
                    'num'   => '06',
                    'name'  => 'Turnkey Projects',
                    'desc'  => 'Our most comprehensive service. From your first consultation to the final delivery and installation — Delos manages every element of your project with the same precision and care as Italy\'s finest interior studios.',
                    'feat'  => ['On-site measurement and consultation', 'Personalized 3D design concepts', 'Italian sourcing and logistics', 'Professional installation team', 'Project management end-to-end'],
                    'brand' => 'All Delos Partners',
                    'img'   => 'delos-sulaymaniyah-showroom.jpg',
                ],
            ];
        @endphp

        <div class="space-y-24">
            @foreach($services as $i => $s)
                <div id="service-{{ $s['num'] }}" data-motion="fade-up" class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center {{ $i % 2 === 1 ? 'lg:[&>*:first-child]:order-2' : '' }}">

                    {{-- Image --}}
                    <div class="relative aspect-[4/3] overflow-hidden bg-delos-dark">
                        <img src="{{ asset('images/' . $s['img']) }}" alt="{{ $s['name'] }}"
                             class="w-full h-full object-cover opacity-70 hover:scale-105 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-delos-dark/40 to-transparent"></div>
                        <div class="absolute top-6 left-6">
                            <span class="text-delos-gold text-[11px] tracking-[0.3em] uppercase font-medium bg-delos-dark/70 backdrop-blur-sm px-3 py-1.5" style="font-family: 'Inter', sans-serif;">
                                {{ $s['brand'] }}
                            </span>
                        </div>
                    </div>

                    {{-- Text --}}
                    <div>
                        <span class="text-delos-gold text-[11px] tracking-[0.3em] font-medium mb-4 block" style="font-family: 'Inter', sans-serif;">{{ $s['num'] }}</span>
                        <h2 class="font-serif text-delos-dark text-3xl lg:text-4xl font-light leading-tight mb-6">
                            {{ $s['name'] }}
                        </h2>
                        <p class="text-delos-muted text-base leading-relaxed mb-8" style="font-family: 'Inter', sans-serif;">
                            {{ $s['desc'] }}
                        </p>
                        <ul class="space-y-3 mb-8">
                            @foreach($s['feat'] as $feat)
                                <li class="flex items-center gap-3 text-delos-muted text-sm" style="font-family: 'Inter', sans-serif;">
                                    <span class="w-1 h-1 rounded-full bg-delos-gold flex-shrink-0"></span>
                                    {{ $feat }}
                                </li>
                            @endforeach
                        </ul>
                        <a href="{{ route('contact') }}"
                           class="inline-flex items-center gap-2 text-delos-dark text-[12px] tracking-[0.2em] uppercase font-medium hover:text-delos-gold transition-colors duration-300 group border-b border-delos-dark/20 pb-1 hover:border-delos-gold" style="font-family: 'Inter', sans-serif;">
                            Book a Consultation
                            <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                    </div>

                </div>
                @if($i < count($services) - 1)
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
        <p data-motion="fade-up" class="text-delos-gold text-[11px] tracking-[0.4em] uppercase font-medium mb-6" style="font-family: 'Inter', sans-serif;">Start Today</p>
        <h2 data-motion="fade-up" class="font-serif text-delos-cream text-4xl lg:text-6xl font-light leading-tight mb-8">
            Ready to transform<br>
            <em class="text-delos-gold not-italic">your space?</em>
        </h2>
        <a href="{{ route('contact') }}"
           data-motion="fade-up"
           class="inline-flex items-center gap-3 px-10 py-4 bg-delos-gold text-delos-dark text-[12px] tracking-[0.25em] uppercase font-medium hover:bg-delos-gold-light transition-all duration-300 group">
            Book Free Consultation
            <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
        </a>
        </div>
    </div>
</section>

@endsection
