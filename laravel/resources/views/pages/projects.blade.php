@extends('layouts.app')

@section('title', 'Projects — Delos International Italian Luxury Interior Design Iraq')
@section('description', 'Explore completed Delos International luxury interior design projects across Erbil, Kirkuk, Sulaymaniyah, and Baghdad. Italian luxury, Iraqi homes.')

@section('content')

{{-- Hero: Magazine Editorial --}}
<section data-motion-hero class="relative min-h-dvh overflow-hidden bg-delos-cream">
    @php
        $heroProjects = [
            ['title' => 'Villa Moderna Kitchen',    'city' => 'Erbil',        'year' => '2024', 'brand' => 'LUBE',              'img' => 'collection-lube-classic.jpg'],
            ['title' => 'Penthouse Living Suite',   'city' => 'Kirkuk',       'year' => '2024', 'brand' => 'CANTORI',           'img' => 'cantori-1.jpg'],
            ['title' => 'Presidential Bedroom',     'city' => 'Baghdad',      'year' => '2023', 'brand' => 'Vittoria Frigerio', 'img' => 'collection-vittoria.jpg'],
            ['title' => 'Luxury Turnkey Residence', 'city' => 'Erbil',        'year' => '2024', 'brand' => 'Delos',             'img' => 'delos-erbil-showroom-6.jpg'],
            ['title' => 'Bespoke Open Kitchen',     'city' => 'Erbil',        'year' => '2023', 'brand' => 'LUBE',              'img' => 'lube-kitchen-3.jpg'],
        ];
    @endphp

    {{-- Full-bleed project image --}}
    <div id="project-slideshow" class="absolute inset-0">
        @foreach($heroProjects as $i => $p)
            <img src="{{ asset('images/' . $p['img']) }}" alt="{{ $p['title'] }}"
                 data-meta="{{ $p['brand'] }} · {{ $p['city'] }} · {{ $p['year'] }}"
                 class="hero-slide absolute inset-0 w-full h-full object-cover {{ $i === 0 ? '' : 'opacity-0' }}"
                 {{ $i === 0 ? 'fetchpriority=high' : 'loading=lazy' }}>
        @endforeach
    </div>

    {{-- Bottom strip: compact glass bar --}}
    <div class="absolute bottom-0 inset-x-0 z-10">
        <div class="bg-delos-cream/50 backdrop-blur-2xl border-t border-delos-gold/10 py-5 lg:py-6 px-6 lg:px-12" style="-webkit-backdrop-filter: blur(30px);">
            <div class="max-w-[1400px] mx-auto flex items-center justify-between gap-6">

                {{-- Left: project info --}}
                <div data-motion="fade-up" class="flex items-center gap-6 lg:gap-10">
                    <div>
                        <p id="project-hero-title" class="font-serif text-delos-dark text-lg lg:text-xl font-light leading-tight">{{ $heroProjects[0]['title'] }}</p>
                        <p id="project-hero-meta" class="text-delos-muted text-[9px] tracking-[0.3em] uppercase mt-0.5" style="font-family: 'Inter', sans-serif;">{{ $heroProjects[0]['brand'] }} · {{ $heroProjects[0]['city'] }} · {{ $heroProjects[0]['year'] }}</p>
                    </div>
                </div>

                {{-- Center: dots --}}
                <div data-motion="fade" class="flex items-center gap-3">
                    @for($i = 0; $i < count($heroProjects); $i++)
                        <button class="hero-dot w-2 h-2 rounded-full transition-all duration-500 {{ $i === 0 ? 'bg-delos-gold w-6' : 'bg-delos-dark/20 hover:bg-delos-dark/40' }}"
                                aria-label="Go to project {{ $i + 1 }}" data-slide="{{ $i }}"></button>
                    @endfor
                </div>

                {{-- Right: counter --}}
                <div data-motion="fade-up" class="hidden sm:block">
                    <span class="text-delos-muted text-[9px] tracking-[0.4em] uppercase" style="font-family: 'Inter', sans-serif;">500+ Projects Completed</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Top overlay: page title --}}
    <div class="absolute top-0 inset-x-0 z-10 pt-28 lg:pt-36 text-center pointer-events-none">
        <div data-motion="fade" class="inline-flex items-center gap-3 mb-3">
            <span class="w-8 h-px bg-delos-gold/60"></span>
            <span class="text-delos-gold text-[10px] tracking-[0.5em] uppercase font-semibold drop-shadow-sm" style="font-family: 'Inter', sans-serif;">Our Portfolio</span>
            <span class="w-8 h-px bg-delos-gold/60"></span>
        </div>
        <h1 data-motion="fade-up" class="font-serif text-delos-dark text-4xl lg:text-5xl font-light leading-tight drop-shadow-sm">
            One standard of <em class="text-delos-gold not-italic">excellence.</em>
        </h1>
    </div>
</section>

{{-- Filter + Grid --}}
<section class="py-24 lg:py-32 bg-delos-cream">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">

        {{-- Filter tabs --}}
        <div data-motion="fade-up" class="flex flex-wrap gap-3 mb-16">
            @foreach(['All', 'Kitchens', 'Living Room', 'Bedroom', 'Wardrobes', 'Turnkey'] as $filter)
                <button class="project-filter px-5 py-2.5 text-[11px] tracking-[0.2em] uppercase font-medium border transition-all duration-300 {{ $filter === 'All' ? 'bg-delos-dark text-delos-cream border-delos-dark' : 'bg-transparent text-delos-muted border-delos-dark/20 hover:border-delos-gold hover:text-delos-gold' }}"
                        style="font-family: 'Inter', sans-serif;"
                        aria-pressed="{{ $filter === 'All' ? 'true' : 'false' }}"
                        data-filter="{{ strtolower($filter) }}">
                    {{ $filter }}
                </button>
            @endforeach
        </div>

        {{-- Projects Grid --}}
        @php
            $projects = [
                ['title' => 'Villa Moderna Kitchen',        'city' => 'Erbil',        'type' => 'kitchens',    'year' => '2024', 'img' => 'collection-lube-classic.jpg'],
                ['title' => 'Penthouse Living Suite',       'city' => 'Kirkuk',       'type' => 'living room', 'year' => '2024', 'img' => 'frigerio-living.jpg'],
                ['title' => 'Presidential Bedroom',         'city' => 'Baghdad',      'type' => 'bedroom',     'year' => '2023', 'img' => 'collection-vittoria.jpg'],
                ['title' => 'Grand Walk-In Wardrobe',       'city' => 'Sulaymaniyah','type' => 'wardrobes',   'year' => '2024', 'img' => 'delos-showroom-card.jpg'],
                ['title' => 'Bespoke Open Kitchen',         'city' => 'Erbil',        'type' => 'kitchens',    'year' => '2023', 'img' => 'lube-kitchen-2.jpg'],
                ['title' => 'Luxury Turnkey Residence',     'city' => 'Erbil',        'type' => 'turnkey',     'year' => '2024', 'img' => 'delos-erbil-showroom-6.jpg'],
                ['title' => 'Contemporary Kitchen Island',  'city' => 'Kirkuk',       'type' => 'kitchens',    'year' => '2023', 'img' => 'lube-kitchen-3.jpg'],
                ['title' => 'Master Suite Bedroom',         'city' => 'Baghdad',      'type' => 'bedroom',     'year' => '2024', 'img' => 'about-philosophy.jpg'],
                ['title' => 'Italian Living Room Suite',    'city' => 'Sulaymaniyah','type' => 'living room', 'year' => '2023', 'img' => 'frigerio-sofa-1.webp'],
            ];
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5" id="projects-grid">
            @foreach($projects as $i => $project)
                <div data-motion="fade-up" class="project-item group relative aspect-[4/3] overflow-hidden bg-delos-dark cursor-pointer"
                     data-type="{{ $project['type'] }}"
                     style="--motion-delay: {{ ($i % 3) * 100 }}ms;">
                    <img src="{{ asset('images/' . $project['img']) }}" alt="{{ $project['title'] }}"
                         class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:opacity-75 group-hover:scale-105 transition-all duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-delos-dark via-delos-dark/20 to-transparent"></div>

                    {{-- Default label --}}
                    <div class="absolute bottom-0 left-0 right-0 p-6 lg:p-8 translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
                        <p class="text-delos-gold text-[10px] tracking-[0.4em] uppercase mb-1" style="font-family: 'Inter', sans-serif;">
                            {{ $project['city'] }} · {{ $project['year'] }}
                        </p>
                        <h3 class="font-serif text-delos-cream text-xl font-light group-hover:text-delos-gold transition-colors duration-300">
                            {{ $project['title'] }}
                        </h3>
                        <p class="text-delos-cream/50 text-[11px] tracking-[0.2em] uppercase mt-2 opacity-0 group-hover:opacity-100 transition-all duration-300" style="font-family: 'Inter', sans-serif;">
                            {{ ucwords($project['type']) }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</section>

{{-- Stats --}}
<section class="py-20 bg-delos-dark">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div data-motion-group="project-stats" class="grid grid-cols-2 lg:grid-cols-4 gap-8 text-center">
            @foreach([['500+','Projects Completed'],['4','Cities in Iraq'],['100%','Italian Sourced'],['5','Brand Partners']] as $i => $s)
                <div data-motion="fade-up">
                    <p class="font-serif text-delos-gold text-5xl lg:text-6xl font-light mb-3">{{ $s[0] }}</p>
                    <p class="text-delos-cream/50 text-[11px] tracking-[0.3em] uppercase" style="font-family: 'Inter', sans-serif;">{{ $s[1] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-24 bg-delos-cream text-center">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div data-motion-group="projects-cta">
        <h2 data-motion="fade-up" class="font-serif text-delos-dark text-4xl lg:text-5xl font-light mb-8">
            Your project could be<br>
            <em class="text-delos-gold not-italic">our next masterpiece.</em>
        </h2>
        <a href="{{ route('contact') }}"
           data-motion="fade-up"
           class="inline-flex items-center gap-3 px-10 py-4 bg-delos-dark text-delos-cream text-[12px] tracking-[0.25em] uppercase font-medium hover:bg-delos-gold hover:text-delos-dark transition-all duration-300 group">
            Start Your Project
            <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
        </a>
        </div>
    </div>
</section>

@endsection
