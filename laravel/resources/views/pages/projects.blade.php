@extends('layouts.app')

@section('title', 'Projects — Delos International Italian Luxury Interior Design Iraq')
@section('description', 'Explore completed Delos International luxury interior design projects across Erbil, Kirkuk, Sulaymaniyah, and Baghdad. Italian luxury, Iraqi homes.')

@section('content')

{{-- Hero --}}
<section class="relative min-h-[60vh] flex items-end overflow-hidden bg-delos-dark">
    <div class="absolute inset-0">
        <img src="{{ asset('images/projects-hero.jpg') }}" alt=""
             class="w-full h-full object-cover opacity-35 hero-parallax" onerror="this.style.display='none'">
        <div class="absolute inset-0 bg-gradient-to-t from-delos-dark to-transparent"></div>
    </div>
    <div class="relative z-10 max-w-[1400px] mx-auto px-6 lg:px-12 pt-40 pb-20 w-full">
        <div class="hero-fade inline-flex items-center gap-3 mb-6 opacity-0 translate-y-4">
            <span class="w-8 h-px bg-delos-gold"></span>
            <span class="text-delos-gold text-[11px] tracking-[0.4em] uppercase font-medium" style="font-family: 'Inter', sans-serif;">Our Portfolio</span>
        </div>
        <h1 class="hero-fade font-serif text-delos-cream text-5xl lg:text-7xl font-light leading-tight opacity-0 translate-y-4">
            500+ projects.<br>
            <em class="text-delos-gold not-italic">One standard.</em>
        </h1>
    </div>
</section>

{{-- Filter + Grid --}}
<section class="py-24 lg:py-32 bg-delos-cream">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">

        {{-- Filter tabs --}}
        <div class="reveal flex flex-wrap gap-3 mb-16">
            @foreach(['All', 'Kitchens', 'Living Room', 'Bedroom', 'Wardrobes', 'Turnkey'] as $filter)
                <button class="project-filter px-5 py-2.5 text-[11px] tracking-[0.2em] uppercase font-medium border transition-all duration-300 {{ $filter === 'All' ? 'bg-delos-dark text-delos-cream border-delos-dark' : 'bg-transparent text-delos-muted border-delos-dark/20 hover:border-delos-gold hover:text-delos-gold' }}"
                        style="font-family: 'Inter', sans-serif;"
                        data-filter="{{ strtolower($filter) }}">
                    {{ $filter }}
                </button>
            @endforeach
        </div>

        {{-- Projects Grid --}}
        @php
            $projects = [
                ['title' => 'Villa Moderna Kitchen',        'city' => 'Erbil',        'type' => 'kitchens',    'year' => '2024', 'img' => 'project-1.jpg'],
                ['title' => 'Penthouse Living Suite',       'city' => 'Kirkuk',       'type' => 'living room', 'year' => '2024', 'img' => 'project-2.jpg'],
                ['title' => 'Presidential Bedroom',         'city' => 'Baghdad',      'type' => 'bedroom',     'year' => '2023', 'img' => 'project-3.jpg'],
                ['title' => 'Grand Walk-In Wardrobe',       'city' => 'Sulaymaniyah','type' => 'wardrobes',   'year' => '2024', 'img' => 'project-4.jpg'],
                ['title' => 'Bespoke Open Kitchen',         'city' => 'Erbil',        'type' => 'kitchens',    'year' => '2023', 'img' => 'project-5.jpg'],
                ['title' => 'Luxury Turnkey Residence',     'city' => 'Erbil',        'type' => 'turnkey',     'year' => '2024', 'img' => 'project-6.jpg'],
                ['title' => 'Contemporary Kitchen Island',  'city' => 'Kirkuk',       'type' => 'kitchens',    'year' => '2023', 'img' => 'project-7.jpg'],
                ['title' => 'Master Suite Bedroom',         'city' => 'Baghdad',      'type' => 'bedroom',     'year' => '2024', 'img' => 'project-8.jpg'],
                ['title' => 'Italian Living Room Suite',    'city' => 'Sulaymaniyah','type' => 'living room', 'year' => '2023', 'img' => 'project-9.jpg'],
            ];
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5" id="projects-grid">
            @foreach($projects as $i => $project)
                <div class="reveal project-item group relative aspect-[4/3] overflow-hidden bg-delos-dark cursor-pointer"
                     data-type="{{ $project['type'] }}"
                     style="transition-delay: {{ ($i % 3) * 0.1 }}s">
                    <img src="{{ asset('images/' . $project['img']) }}" alt="{{ $project['title'] }}"
                         class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:opacity-75 group-hover:scale-105 transition-all duration-700"
                         onerror="this.style.display='none'">
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
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 text-center">
            @foreach([['500+','Projects Completed'],['4','Cities in Iraq'],['100%','Italian Sourced'],['5','Brand Partners']] as $i => $s)
                <div class="reveal" style="transition-delay: {{ $i * 0.15 }}s">
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
        <h2 class="reveal font-serif text-delos-dark text-4xl lg:text-5xl font-light mb-8">
            Your project could be<br>
            <em class="text-delos-gold not-italic">our next masterpiece.</em>
        </h2>
        <a href="{{ route('contact') }}"
           class="reveal inline-flex items-center gap-3 px-10 py-4 bg-delos-dark text-delos-cream text-[12px] tracking-[0.25em] uppercase font-medium hover:bg-delos-gold hover:text-delos-dark transition-all duration-300 group">
            Start Your Project
            <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
        </a>
    </div>
</section>

@endsection
