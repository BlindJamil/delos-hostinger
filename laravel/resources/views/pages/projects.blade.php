@extends('layouts.app')

@section('title', __('seo.projects.title'))
@section('description', __('seo.projects.description'))

@section('content')

{{-- Hero: Magazine Editorial --}}
<section data-motion-hero class="relative min-h-dvh overflow-hidden bg-delos-cream">
    @php
        $heroProjectsRaw = __('projects.hero.slides');
        $heroProjects = array_values(is_array($heroProjectsRaw) ? $heroProjectsRaw : []);
    @endphp

    {{-- Full-bleed project image --}}
    <div id="project-slideshow" class="absolute inset-0">
        @foreach($heroProjects as $i => $p)
            <x-responsive-image
                :src="$p['img']"
                :alt="$p['title']"
                sizes="100vw"
                class="hero-slide absolute inset-0 w-full h-full object-cover {{ $i === 0 ? '' : 'opacity-0' }}"
                :loading="$i === 0 ? 'eager' : 'lazy'"
                :fetchpriority="$i === 0 ? 'high' : null"
                data-meta="{{ $p['brand'] }} · {{ $p['city'] }} · {{ $p['year'] }}" />
        @endforeach
    </div>

    {{-- Bottom strip: compact glass bar --}}
    <div class="absolute bottom-0 inset-x-0 z-10">
        <div class="bg-delos-cream/50 backdrop-blur-2xl border-t border-delos-gold/10 py-5 lg:py-6 px-6 lg:px-12" style="-webkit-backdrop-filter: blur(30px);">
            <div class="max-w-[1400px] mx-auto flex items-center justify-between gap-6">

                <div data-motion="fade-up" class="flex items-center gap-6 lg:gap-10">
                    <div>
                        <p id="project-hero-title" class="font-serif text-delos-dark text-lg lg:text-xl font-light leading-tight">{{ $heroProjects[0]['title'] ?? '' }}</p>
                        <p id="project-hero-meta" class="text-delos-muted text-[9px] tracking-[0.3em] uppercase mt-0.5" style="font-family: 'Inter', sans-serif;">{{ ($heroProjects[0]['brand'] ?? '') . ' · ' . ($heroProjects[0]['city'] ?? '') . ' · ' . ($heroProjects[0]['year'] ?? '') }}</p>
                    </div>
                </div>

                <div data-motion="fade" class="flex items-center gap-3">
                    @for($i = 0; $i < count($heroProjects); $i++)
                        <button class="hero-dot w-2 h-2 rounded-full transition-all duration-500 {{ $i === 0 ? 'bg-delos-gold w-6' : 'bg-delos-dark/20 hover:bg-delos-dark/40' }}"
                                aria-label="Slide {{ $i + 1 }}" data-slide="{{ $i }}"></button>
                    @endfor
                </div>

                <div data-motion="fade-up" class="hidden sm:block">
                    <span class="text-delos-muted text-[9px] tracking-[0.4em] uppercase" style="font-family: 'Inter', sans-serif;">{{ __('projects.hero.counter') }}</span>
                </div>
            </div>
        </div>
    </div>

</section>

{{-- Filter + Grid --}}
<section class="py-24 lg:py-32 bg-delos-cream">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">

        {{-- Filter tabs --}}
        @php
            $filters = __('projects.filters');
            // Map filter key to data-filter value for JS compatibility (lowercase English)
            $filterDataMap = [
                'all' => 'all',
                'kitchens' => 'kitchens',
                'living_room' => 'living room',
                'bedroom' => 'bedroom',
                'wardrobes' => 'wardrobes',
                'turnkey' => 'turnkey',
            ];
        @endphp
        <div data-motion="fade-up" class="flex flex-wrap gap-3 mb-16">
            @foreach($filters as $key => $label)
                @php $isAll = $key === 'all'; @endphp
                <button class="project-filter px-5 py-2.5 text-[11px] tracking-[0.2em] uppercase font-medium border transition-all duration-300 {{ $isAll ? 'bg-delos-dark text-delos-cream border-delos-dark' : 'bg-transparent text-delos-muted border-delos-dark/20 hover:border-delos-gold hover:text-delos-gold' }}"
                        style="font-family: 'Inter', sans-serif;"
                        aria-pressed="{{ $isAll ? 'true' : 'false' }}"
                        data-filter="{{ $filterDataMap[$key] ?? $key }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- Projects Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5" id="projects-grid">
            @foreach(__('projects.grid') as $slug => $project)
                @php $i = $loop->index; @endphp
                <div data-motion="fade-up" class="project-item group relative aspect-[4/3] overflow-hidden bg-delos-dark cursor-pointer"
                     data-type="{{ $project['type'] }}"
                     style="--motion-delay: {{ ($i % 3) * 100 }}ms;">
                    <x-responsive-image :src="$project['img']" :alt="$project['title']"
                        sizes="(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw"
                        class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:opacity-75 group-hover:scale-105 transition-all duration-700" />
                    <div class="absolute inset-0 bg-gradient-to-t from-delos-dark via-delos-dark/20 to-transparent"></div>

                    <div class="absolute bottom-0 left-0 right-0 p-6 lg:p-8 translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
                        <p class="text-delos-gold text-[10px] tracking-[0.4em] uppercase mb-1" style="font-family: 'Inter', sans-serif;">
                            {{ $project['city'] }} · {{ $project['year'] }}
                        </p>
                        <h3 class="font-serif text-delos-cream text-xl font-light group-hover:text-delos-gold transition-colors duration-300">
                            {{ $project['title'] }}
                        </h3>
                        <p class="text-delos-cream/50 text-[11px] tracking-[0.2em] uppercase mt-2 opacity-0 group-hover:opacity-100 transition-all duration-300" style="font-family: 'Inter', sans-serif;">
                            {{ $project['type_label'] }}
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
            @foreach(__('projects.stats') as $i => $s)
                <div data-motion="fade-up">
                    <p class="font-serif text-delos-gold text-5xl lg:text-6xl font-light mb-3">{{ $s['value'] }}</p>
                    <p class="text-delos-cream/50 text-[11px] tracking-[0.3em] uppercase" style="font-family: 'Inter', sans-serif;">{{ $s['label'] }}</p>
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
                {{ __('projects.cta.heading_1') }}<br>
                <em class="text-delos-gold not-italic">{{ __('projects.cta.heading_accent') }}</em>
            </h2>
            <a href="{{ lroute('contact') }}"
               data-motion="fade-up"
               class="inline-flex items-center gap-3 px-10 py-4 bg-delos-dark text-delos-cream text-[12px] tracking-[0.25em] uppercase font-medium hover:bg-delos-gold hover:text-delos-dark transition-all duration-300 group">
                {{ __('common.ctas.start_project') }}
                <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
    </div>
</section>

@endsection
