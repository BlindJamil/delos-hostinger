@extends('layouts.app')

@section('title', 'About Delos International — Italian Luxury Interior Design in Iraq')
@section('description', 'Delos International was established in Erbil, Iraq in 2020, bringing authentic Italian luxury interior design to Iraqi homes. 4 branches, 5 Italian brand partners, 500+ completed projects.')

@section('content')

{{-- ============================================================
     ABOUT HERO
============================================================ --}}
<section data-motion-hero class="relative min-h-[80vh] flex flex-col overflow-hidden bg-delos-dark">
    {{-- Background --}}
    <div class="absolute inset-0">
        <img src="{{ asset('images/about-hero.jpg') }}" alt="Delos International Showroom"
             data-hero-parallax
             class="w-full h-full object-cover opacity-35 hero-parallax-img">
        <div class="absolute inset-0 bg-gradient-to-t from-delos-dark via-delos-dark/50 to-transparent"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-delos-dark/50 to-transparent"></div>
    </div>

    {{-- Content --}}
    <div class="relative z-10 max-w-[1400px] mx-auto px-6 lg:px-12 pt-28 pb-16 lg:pt-32 lg:pb-32 w-full flex-1 flex flex-col justify-center">
        <div data-motion-group="hero" class="max-w-3xl">
            {{-- Badge --}}
            <div data-motion="fade-up" class="inline-flex items-center gap-3 mb-6">
                <span class="w-8 h-px bg-delos-gold"></span>
                <span class="text-delos-gold text-[11px] tracking-[0.4em] uppercase font-medium" style="font-family: 'Inter', sans-serif;">Est. 2020 · Erbil, Iraq</span>
            </div>

            <h1 data-motion="fade-up" class="font-serif text-delos-cream text-5xl lg:text-7xl font-light leading-[1.05] mb-6">
                Bringing Italian<br>
                luxury to every<br>
                <em class="text-delos-gold not-italic">Iraqi home.</em>
            </h1>

            <p data-motion="fade-up" class="text-delos-cream/60 text-base lg:text-lg leading-relaxed max-w-lg" style="font-family: 'Inter', sans-serif;">
                Delos International is Iraq's trusted destination for authentic Italian luxury interior solutions.
            </p>
        </div>
    </div>
</section>

{{-- ============================================================
     OUR STORY (replaces Furnexa's "Our story began in 2019")
============================================================ --}}
<section class="py-24 lg:py-32 bg-delos-cream">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">

        <div class="grid lg:grid-cols-2 gap-16 lg:gap-24 items-center">

            {{-- Image --}}
            <div data-motion="slide-left" class="relative aspect-[3/4] overflow-hidden bg-delos-dark">
                <img src="{{ asset('images/about-story.jpg') }}" alt="Delos International Showroom"
                     class="w-full h-full object-cover opacity-80">
                {{-- Accent badge --}}
                <div class="absolute bottom-8 right-8 bg-delos-dark/90 backdrop-blur-sm px-6 py-4 border border-delos-gold/20">
                    <p class="font-serif text-delos-gold text-4xl font-light leading-none mb-1">2020</p>
                    <p class="text-delos-cream/60 text-[10px] tracking-[0.3em] uppercase" style="font-family: 'Inter', sans-serif;">Founded in Erbil</p>
                </div>
            </div>

            {{-- Text --}}
            <div data-motion-group="story">
                <div data-motion-line class="w-16 h-px bg-delos-gold mb-5"></div>
                <p data-motion="fade-up" class="text-delos-gold text-[11px] tracking-[0.4em] uppercase font-medium mb-6" style="font-family: 'Inter', sans-serif;">Our Story</p>
                <h2 data-motion="fade-up" class="font-serif text-4xl lg:text-5xl font-light text-delos-dark leading-tight mb-8">
                    Where passion<br>
                    for Italian craft<br>
                    <em class="text-delos-gold not-italic">first took shape.</em>
                </h2>
                <div class="space-y-5">
                    <p data-motion="fade-up" class="text-delos-muted text-base leading-relaxed" style="font-family: 'Inter', sans-serif;">
                        Our story began in 2020, in Erbil, Iraq — where a vision to bring authentic Italian luxury design to Iraqi homes became a reality. What started as a single showroom on 60m Street has grown into Delos International, the most trusted name in Italian luxury interior solutions across Iraq.
                    </p>
                    <p data-motion="fade-up" class="text-delos-muted text-base leading-relaxed" style="font-family: 'Inter', sans-serif;">
                        From day one, we have focused on what truly matters: genuine Italian craftsmanship, responsibly sourced premium materials, and a complete service that takes our clients from first consultation to final installation — with nothing left to chance.
                    </p>
                    <p data-motion="fade-up" class="text-delos-muted text-base leading-relaxed" style="font-family: 'Inter', sans-serif;">
                        Today, with 4 showrooms in Erbil, Kirkuk, Sulaymaniyah, and Baghdad, and exclusive partnerships with Italy's most prestigious brands, Delos International stands as Iraq's destination for Italian luxury living.
                    </p>
                </div>
            </div>

        </div>

    </div>
</section>

{{-- ============================================================
     VISION, MISSION & GOAL
============================================================ --}}
<section class="py-24 lg:py-32 bg-delos-ivory">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">

        <div data-motion-group="direction-header" class="text-center mb-16">
            <p data-motion="fade-up" class="text-delos-gold text-[11px] tracking-[0.4em] uppercase font-medium mb-4" style="font-family: 'Inter', sans-serif;">Our Direction</p>
            <h2 data-motion="fade-up" class="font-serif text-4xl lg:text-5xl font-light text-delos-dark max-w-2xl mx-auto leading-tight">
                Vision, Mission<br>& Purpose
            </h2>
        </div>

        <div data-motion-group="direction-cards" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            @php
                $visions = [
                    [
                        'label' => 'Vision',
                        'title' => 'Market Leader in Iraq',
                        'body'  => 'To become the undisputed market leader in kitchens and furniture across Iraq — where every home in the country knows and trusts the Delos name.'
                    ],
                    [
                        'label' => 'Mission',
                        'title' => 'Trust & Excellence',
                        'body'  => 'To ensure every Delos product is built on trust and excellence — delivering high-quality, responsibly sourced Italian materials with advanced manufacturing precision.'
                    ],
                    [
                        'label' => 'Goal',
                        'title' => 'Tailored to You',
                        'body'  => 'To deliver customized, precisely engineered solutions tailored to each client\'s needs — transforming spaces into living expressions of Italian luxury.'
                    ],
                ];
            @endphp
            @foreach($visions as $i => $v)
                <div data-motion="fade-up" class="group">
                    <div class="p-8 lg:p-10 border border-delos-dark/10 h-full hover:border-delos-gold/30 hover:bg-delos-cream transition-colors duration-400">
                        <span class="inline-block text-delos-gold text-[10px] tracking-[0.4em] uppercase font-medium mb-6 border border-delos-gold/30 px-3 py-1" style="font-family: 'Inter', sans-serif;">{{ $v['label'] }}</span>
                        <h3 class="font-serif text-delos-dark text-2xl font-medium mb-4 group-hover:text-delos-gold transition-colors duration-300">
                            {{ $v['title'] }}
                        </h3>
                        <p class="text-delos-muted text-sm leading-relaxed" style="font-family: 'Inter', sans-serif;">
                            {{ $v['body'] }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</section>

{{-- ============================================================
     PHILOSOPHY (replaces Furnexa "Every piece begins with...")
============================================================ --}}
<section class="py-24 lg:py-32 bg-delos-cream">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">

        <div class="grid lg:grid-cols-2 gap-16 lg:gap-24 items-center">
            <div data-motion-group="philosophy">
                <div data-motion-line class="w-16 h-px bg-delos-gold mb-5"></div>
                <p data-motion="fade-up" class="text-delos-gold text-[11px] tracking-[0.4em] uppercase font-medium mb-6" style="font-family: 'Inter', sans-serif;">Our Philosophy</p>
                <h2 data-motion="fade-up" class="font-serif text-4xl lg:text-5xl font-light text-delos-dark leading-tight mb-8">
                    Every project<br>
                    begins with a<br>
                    <em class="text-delos-gold not-italic">single idea.</em>
                </h2>
                <div class="space-y-5">
                    <p data-motion="fade-up" class="text-delos-muted text-base leading-relaxed" style="font-family: 'Inter', sans-serif;">
                        Every Delos project begins with one idea: create a space that looks stunning, feels perfectly comfortable, and performs seamlessly for your daily life. We obsess over material selection, spatial design, and functional precision — ensuring every installation enhances how you live.
                    </p>
                    <p data-motion="fade-up" class="text-delos-muted text-base leading-relaxed" style="font-family: 'Inter', sans-serif;">
                        Our philosophy blends the aesthetics of Italian luxury with the practicalities of modern Iraqi living. Your space should inspire you every day, support your family, and endure for generations — not simply decorate.
                    </p>
                </div>
            </div>

            {{-- Image --}}
            <div data-motion="slide-right" class="relative aspect-[4/3] overflow-hidden bg-delos-dark">
                <img src="{{ asset('images/about-philosophy.jpg') }}" alt="Italian Design Philosophy"
                     class="w-full h-full object-cover opacity-75">
                <div class="absolute inset-0 bg-gradient-to-t from-delos-dark/30 to-transparent"></div>
            </div>
        </div>

    </div>
</section>

{{-- ============================================================
     ITALIAN MATERIALS (full-width callout)
============================================================ --}}
<section class="relative py-24 lg:py-40 overflow-hidden bg-delos-dark">
    <div class="absolute inset-0">
        <img src="{{ asset('images/italian-materials.jpg') }}" alt="Italian Materials"
             class="w-full h-full object-cover opacity-25">
        <div class="absolute inset-0 bg-gradient-to-r from-delos-dark via-delos-dark/70 to-transparent"></div>
    </div>
    <div class="relative z-10 max-w-[1400px] mx-auto px-6 lg:px-12">
        <div data-motion-group="italian-materials" class="max-w-2xl">
            <div data-motion-line class="w-16 h-px bg-delos-gold mb-5"></div>
            <p data-motion="fade-up" class="text-delos-gold text-[11px] tracking-[0.4em] uppercase font-medium mb-6" style="font-family: 'Inter', sans-serif;">Sourced from Italy</p>
            <h2 data-motion="fade-up" class="font-serif text-delos-cream text-4xl lg:text-5xl xl:text-6xl font-light leading-tight mb-8">
                Materials chosen<br>
                for their <em class="text-delos-gold not-italic">strength,<br>
                character,</em> and<br>
                natural warmth.
            </h2>
            <p data-motion="fade-up" class="text-delos-cream/60 text-base leading-relaxed max-w-md" style="font-family: 'Inter', sans-serif;">
                Every Delos creation begins with the quiet beauty of Italy. We carefully source premium woods, marbles, and textiles — chosen not only for their beauty, but for their longevity and soul.
            </p>
        </div>
    </div>
</section>

{{-- ============================================================
     4 PILLARS (replaces Furnexa's 4 core value cards)
============================================================ --}}
<section class="py-24 lg:py-32 bg-delos-cream">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">

        <div data-motion-group="pillars-header" class="text-center mb-16">
            <p data-motion="fade-up" class="text-delos-gold text-[11px] tracking-[0.4em] uppercase font-medium mb-4" style="font-family: 'Inter', sans-serif;">What Sets Us Apart</p>
            <h2 data-motion="fade-up" class="font-serif text-4xl lg:text-5xl font-light text-delos-dark">The Delos Difference</h2>
        </div>

        <div data-motion-group="pillars" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $pillars = [
                    ['num' => '01', 'title' => 'Authentic Italian Design', 'desc' => 'Exclusively sourced from Italy\'s finest manufacturers — no imitations, no compromises.'],
                    ['num' => '02', 'title' => 'Turnkey Project Execution', 'desc' => 'Concept, 3D design, delivery, and full installation — one team, zero stress.'],
                    ['num' => '03', 'title' => 'Premium Craftsmanship', 'desc' => 'Built with expert Italian manufacturing, refined detailing, and materials that endure.'],
                    ['num' => '04', 'title' => 'Trusted Since 2020', 'desc' => '4 branches, 500+ projects, and thousands of Iraqi families who chose to Trust the Lion.'],
                ];
            @endphp
            @foreach($pillars as $i => $p)
                <div data-motion="fade-up" class="group text-center p-8 border border-delos-dark/10 hover:border-delos-gold/40 hover:bg-delos-ivory transition-colors duration-400">
                    <div class="w-12 h-12 border border-delos-gold/30 flex items-center justify-center mx-auto mb-6 group-hover:bg-delos-gold group-hover:border-delos-gold transition-all duration-300">
                        <span class="text-delos-gold text-[11px] tracking-wide font-medium group-hover:text-delos-dark transition-colors duration-300" style="font-family: 'Inter', sans-serif;">{{ $p['num'] }}</span>
                    </div>
                    <h3 class="font-serif text-delos-dark text-xl font-medium mb-4 group-hover:text-delos-gold transition-colors duration-300">
                        {{ $p['title'] }}
                    </h3>
                    <p class="text-delos-muted text-sm leading-relaxed" style="font-family: 'Inter', sans-serif;">
                        {{ $p['desc'] }}
                    </p>
                </div>
            @endforeach
        </div>

    </div>
</section>

{{-- ============================================================
     LARGE QUOTE STATEMENT (replaces Furnexa's central quote)
============================================================ --}}
<section class="py-24 lg:py-40 bg-delos-dark text-center">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div data-motion-group="quote">
        <p data-motion="fade-up" class="text-delos-gold/50 text-[11px] tracking-[0.5em] uppercase font-medium mb-10" style="font-family: 'Inter', sans-serif;">Our Commitment</p>
        <blockquote data-motion="fade-up" class="font-serif text-delos-cream text-3xl lg:text-5xl xl:text-6xl font-light leading-tight max-w-5xl mx-auto">
            "Italian luxury that <em class="text-delos-gold not-italic">lives with you</em> — design that adapts, furniture that inspires, spaces that tell your story."
        </blockquote>
        <div data-motion="fade-up" class="mt-16 flex items-center justify-center gap-6">
            <span class="w-16 h-px bg-delos-gold/30"></span>
            <p class="font-serif text-delos-gold text-2xl italic font-light">DELOS — TRUST THE LION</p>
            <span class="w-16 h-px bg-delos-gold/30"></span>
        </div>
        </div>
    </div>
</section>

{{-- ============================================================
     STATS (replaces "Happy Homes Furnished")
============================================================ --}}
<section class="py-20 lg:py-24 bg-delos-ivory">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div data-motion-group="about-stats" class="grid grid-cols-2 lg:grid-cols-4 gap-8 text-center">
            @php
                $stats = [
                    ['value' => 500, 'suffix' => '+', 'label' => 'Projects Delivered'],
                    ['value' => 4,   'suffix' => '',  'label' => 'Branches in Iraq'],
                    ['value' => 5,   'suffix' => '',  'label' => 'Italian Brand Partners'],
                    ['value' => 2020,'suffix' => '',  'label' => 'Est. in Erbil'],
                ];
            @endphp
            @foreach($stats as $i => $stat)
                <div data-motion="fade-up">
                    <div class="flex items-baseline justify-center gap-1 mb-3">
                        <span class="font-serif text-delos-dark text-5xl lg:text-6xl font-light"
                              data-motion-counter="{{ $stat['value'] }}">{{ $stat['value'] }}</span>
                        <span class="stat-suffix font-serif text-delos-gold text-3xl font-light">{{ $stat['suffix'] }}</span>
                    </div>
                    <p class="text-delos-muted text-[11px] tracking-[0.3em] uppercase" style="font-family: 'Inter', sans-serif;">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================================================
     OUR WORKFLOW (replaces "Meet the Makers")
============================================================ --}}
<section class="py-24 lg:py-32 bg-delos-cream">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">

        <div data-motion-group="workflow-header" class="text-center mb-16">
            <p data-motion="fade-up" class="text-delos-gold text-[11px] tracking-[0.4em] uppercase font-medium mb-4" style="font-family: 'Inter', sans-serif;">How We Work</p>
            <h2 data-motion="fade-up" class="font-serif text-4xl lg:text-5xl font-light text-delos-dark">
                From concept<br>to completion.
            </h2>
        </div>

        <div data-motion-group="workflow-steps" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @php
                $steps = [
                    ['step' => '01', 'title' => 'Consultation', 'desc' => 'Our specialists visit your space, understand your vision, and take precise measurements.'],
                    ['step' => '02', 'title' => '3D Design', 'desc' => 'Our designers create a personalized 3D concept aligned perfectly with your lifestyle and space.'],
                    ['step' => '03', 'title' => 'Italian Sourcing', 'desc' => 'Your selections are sourced directly from Italy\'s finest manufacturers with full quality assurance.'],
                    ['step' => '04', 'title' => 'Installation', 'desc' => 'Our expert technicians deliver and install with meticulous precision — ensuring exceptional results.'],
                ];
            @endphp
            @foreach($steps as $i => $step)
                <div data-motion="fade-up" class="text-center group">
                    <div class="relative mb-8">
                        <div class="w-16 h-16 border border-delos-gold/30 rounded-full flex items-center justify-center mx-auto group-hover:bg-delos-gold group-hover:border-delos-gold transition-all duration-400">
                            <span class="font-serif text-delos-gold text-xl font-light group-hover:text-delos-dark transition-colors duration-400">{{ $step['step'] }}</span>
                        </div>
                        @if($i < 3)
                            <div class="hidden lg:block absolute top-8 left-1/2 w-full h-px bg-delos-dark/10" style="left: calc(50% + 32px); width: calc(100% - 64px);"></div>
                        @endif
                    </div>
                    <h3 class="font-serif text-delos-dark text-xl font-medium mb-3 group-hover:text-delos-gold transition-colors duration-300">
                        {{ $step['title'] }}
                    </h3>
                    <p class="text-delos-muted text-sm leading-relaxed" style="font-family: 'Inter', sans-serif;">
                        {{ $step['desc'] }}
                    </p>
                </div>
            @endforeach
        </div>

    </div>
</section>

{{-- ============================================================
     OUR BRANCHES
============================================================ --}}
<section class="py-24 lg:py-32 bg-delos-dark">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">

        <div data-motion-group="branches-header" class="flex flex-col lg:flex-row lg:items-end lg:justify-between mb-16 gap-6">
            <div>
                <div data-motion-line class="w-16 h-px bg-delos-gold mb-5"></div>
                <p data-motion="fade-up" class="text-delos-gold text-[11px] tracking-[0.4em] uppercase font-medium mb-4" style="font-family: 'Inter', sans-serif;">Our Presence in Iraq</p>
                <h2 data-motion="fade-up" class="font-serif text-delos-cream text-4xl lg:text-5xl font-light leading-tight">
                    Four cities.<br>One standard<br>of <em class="text-delos-gold not-italic">excellence.</em>
                </h2>
            </div>
            <a href="{{ route('contact') }}"
               data-motion="fade-up"
               class="self-start inline-flex items-center gap-2 text-delos-gold text-[12px] tracking-[0.2em] uppercase font-medium hover:text-delos-cream transition-colors duration-300 group" style="font-family: 'Inter', sans-serif;">
                Find Your Nearest Showroom
                <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>

        <div data-motion-group="branches-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $branches = [
                    ['city' => 'Erbil',         'year' => '2020', 'address' => '60m Street, Near Soran Hospital', 'phone' => '0750 100 1701'],
                    ['city' => 'Kirkuk',        'year' => '2021', 'address' => 'Kirkuk Showroom',                 'phone' => 'Contact for details'],
                    ['city' => 'Sulaymaniyah',  'year' => '2022', 'address' => 'Sulaymaniyah Showroom',          'phone' => 'Contact for details'],
                    ['city' => 'Baghdad',        'year' => '2024', 'address' => 'Baghdad Showroom',               'phone' => 'Contact for details'],
                ];
            @endphp
            @foreach($branches as $i => $branch)
                <div data-motion="fade-up" class="group border border-white/10 p-6 lg:p-8 hover:border-delos-gold/40 transition-colors duration-400">
                    <p class="text-delos-gold text-[10px] tracking-[0.4em] uppercase font-medium mb-4" style="font-family: 'Inter', sans-serif;">Est. {{ $branch['year'] }}</p>
                    <h3 class="font-serif text-delos-cream text-2xl font-light mb-4 group-hover:text-delos-gold transition-colors duration-300">
                        {{ $branch['city'] }}
                    </h3>
                    <p class="text-delos-muted text-xs leading-relaxed mb-2" style="font-family: 'Inter', sans-serif;">{{ $branch['address'] }}</p>
                    <p class="text-delos-gold/70 text-xs" style="font-family: 'Inter', sans-serif;">{{ $branch['phone'] }}</p>
                </div>
            @endforeach
        </div>

    </div>
</section>

@endsection
