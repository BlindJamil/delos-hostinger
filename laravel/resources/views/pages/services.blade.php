@extends('layouts.app')

@section('title', 'Services — Delos International Italian Luxury Solutions Iraq')
@section('description', 'Delos International offers Italian kitchens, dressing rooms, laundry spaces, furniture, parquet flooring, and complete custom interior solutions delivered across Iraq.')

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
                ['img' => 'collection-lube-classic.jpg', 'label' => 'LUBE · Italian Kitchens'],
                ['img' => 'delos-erbil-showroom-3.jpg',  'label' => 'LUBE · Dressing Rooms'],
                ['img' => 'lube-laundry-zone.jpg',       'label' => 'LUBE · Laundry Rooms'],
                ['img' => 'cantori-1.jpg',               'label' => 'CANTORI · Italian Furniture'],
                ['img' => 'italian-materials.jpg',       'label' => 'SKEMA · Italian Parquet'],
                ['img' => 'lube-project-mauritius.jpg',  'label' => 'LUBE · Others'],
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
                            <x-responsive-image :src="$slide['img']" :alt="$slide['label']"
                                sizes="(min-width: 1280px) 540px, (min-width: 1024px) 480px, (min-width: 640px) 400px, 300px"
                                class="w-full h-full object-cover"
                                :loading="$i === 0 ? 'eager' : 'lazy'"
                                :fetchpriority="$i === 0 ? 'high' : null" />
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
                Kitchens, dressing rooms, laundry, furniture, parquet, and more — under one roof.
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

{{-- Services Detail --}}
<section class="py-24 lg:py-32 bg-delos-cream">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">

        <div data-motion-group="services-intro" class="text-center mb-20 lg:mb-24">
            <div data-motion-line class="w-16 h-px bg-delos-gold mx-auto mb-5"></div>
            <p data-motion="fade-up" class="text-overline text-delos-gold mb-5">What We Offer</p>
            <h2 data-motion="fade-up" class="text-heading-2 text-delos-dark">Six Italian crafts.<br><em class="text-delos-gold not-italic">One standard of luxury.</em></h2>
        </div>

        @php
            $services = [
                [
                    'num'   => '01',
                    'name'  => 'Italian Kitchens',
                    'desc'  => 'Your kitchen is the heart of your home. We bring Italy\'s finest kitchen manufacturers directly to your door — precision-engineered, beautifully finished, and designed around your lifestyle. From timeless classic to ultra-contemporary, every Delos kitchen is a masterpiece of Italian craft.',
                    'feat'  => ['Custom Italian cabinetry', 'Premium hardware and finishes', 'Integrated appliance solutions', 'Ergonomic spatial design', '3D planning service'],
                    'brand' => 'LUBE Kitchens',
                    'img'   => 'lube-kitchen-1.jpeg',
                ],
                [
                    'num'   => '02',
                    'name'  => 'Dressing Rooms',
                    'desc'  => 'Italian dressing rooms are an art form. Our walk-in wardrobe systems are precision-designed with integrated lighting, custom interior organization, and flawless hardware — transforming your daily routine into an experience of quiet luxury.',
                    'feat'  => ['Walk-in wardrobe design', 'Hinged and sliding door systems', 'Custom interior organization', 'Integrated LED lighting', 'Mirror and glass options'],
                    'brand' => 'LUBE · Frigerio',
                    'img'   => 'delos-erbil-showroom-6.jpg',
                ],
                [
                    'num'   => '03',
                    'name'  => 'Laundry Rooms',
                    'desc'  => 'A well-designed laundry room should feel as refined as the rest of your home. Delos brings Italian-made cabinetry, premium finishes, and smart storage solutions to create laundry spaces that are both beautifully organized and effortlessly practical.',
                    'feat'  => ['Custom cabinetry and storage', 'Integrated appliance housing', 'Premium worktops and sinks', 'Space-efficient layouts', 'Matching finishes throughout your home'],
                    'brand' => 'LUBE · Delos Custom',
                    'img'   => 'lube-laundry-zone.jpg',
                ],
                [
                    'num'   => '04',
                    'name'  => 'Italian Furniture',
                    'desc'  => 'From the living room to the bedroom, Delos delivers complete Italian furniture collections — sofas, dining suites, beds, and statement pieces that combine sculptural beauty with enduring comfort. Every piece is crafted by Italy\'s most celebrated furniture houses.',
                    'feat'  => ['Italian sofas and sectionals', 'Dining and coffee tables', 'Bedroom collections', 'Accent and statement pieces', 'Complete room styling'],
                    'brand' => 'Frigerio · CANTORI',
                    'img'   => 'collection-vittoria.jpg',
                ],
                [
                    'num'   => '05',
                    'name'  => 'Italian Parquet',
                    'desc'  => 'Flooring is the foundation of great design. SKEMA\'s premium Italian parquet and wooden flooring systems provide the perfect base for your Delos interior — rich natural wood grain, advanced engineering, and finishes that age beautifully for generations.',
                    'feat'  => ['Engineered Italian parquet', 'Natural hardwood options', 'Wide-plank and herringbone patterns', 'Prefinished and site-finished', 'Full installation service'],
                    'brand' => 'SKEMA',
                    'img'   => 'italian-materials.jpg',
                ],
                [
                    'num'   => '06',
                    'name'  => 'Others',
                    'desc'  => 'Beyond our core categories, Delos takes on anything you need to complete your space. From single bespoke pieces to complete home transformations, our team manages every element — consultation, 3D concept design, Italian sourcing, logistics, and flawless on-site installation — with the same precision as Italy\'s finest interior studios.',
                    'feat'  => ['On-site measurement and consultation', 'Personalized 3D design concepts', 'Italian sourcing and logistics', 'Professional installation team', 'Project management end-to-end'],
                    'brand' => 'All Delos Partners',
                    'img'   => 'lube-project-mauritius.jpg',
                ],
            ];
        @endphp

        <div class="space-y-24">
            @foreach($services as $i => $s)
                <div id="service-{{ $s['num'] }}" data-motion="fade-up" class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center {{ $i % 2 === 1 ? 'lg:[&>*:first-child]:order-2' : '' }}">

                    {{-- Image --}}
                    <div class="relative aspect-[4/3] overflow-hidden bg-delos-dark">
                        <x-responsive-image :src="$s['img']" :alt="$s['name']"
                            sizes="(min-width: 1024px) 50vw, 100vw"
                            class="w-full h-full object-cover hover:scale-105 transition-transform duration-700" />
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
