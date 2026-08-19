@extends('layouts.app')

@section('title', pcontent('seo.projects.title'))
@section('description', pcontent('seo.projects.description'))

@section('content')

{{-- Hero: Magazine Editorial --}}
<section data-motion-hero class="relative min-h-dvh overflow-hidden bg-delos-cream">
    {{-- Admin: jump to the featured-projects filter in the admin panel --}}
    <x-admin-edit-pill :href="route('admin.projects.index', ['featured' => 'yes'])" label="Manage featured" />
    @php
        // $heroProjects comes from PageController::projects() — DB-driven,
        // filtered to active + featured. Falls back to empty if none are
        // flagged, so the page still renders (just without a hero carousel).
        $heroProjects = ($heroProjects ?? collect())->values();
    @endphp

    {{-- Full-bleed project image --}}
    <div id="project-slideshow" class="absolute inset-0">
        @foreach($heroProjects as $i => $p)
            @php
                $pTitle = $p->localized('title');
                $pBrand = $p->brand;
                $pCity = $p->city;
                $pYear = $p->year;
            @endphp
            @if($p->image)
                <x-responsive-image
                    :src="$p->image"
                    :mobile-src="$p->image_mobile"
                    :focal="$p->focal_point"
                    :alt="$pTitle"
                    sizes="100vw"
                    class="hero-slide absolute inset-0 w-full h-full object-cover {{ $i === 0 ? '' : 'opacity-0' }}"
                :loading="$i === 0 ? 'eager' : 'lazy'"
                :fetchpriority="$i === 0 ? 'high' : null"
                data-meta="{{ trim(collect([$pBrand, $pCity, $pYear])->filter()->join(' · ')) }}" />
            @endif
        @endforeach
    </div>

    {{-- Bottom strip: compact glass bar --}}
    <div class="absolute bottom-0 inset-x-0 z-10">
        <div class="bg-delos-cream/50 backdrop-blur-2xl border-t border-delos-gold/10 py-5 lg:py-6 px-6 lg:px-12" style="-webkit-backdrop-filter: blur(30px);">
            <div class="max-w-[1400px] mx-auto flex items-center justify-between gap-6">

                <div data-motion="fade-up" class="flex items-center gap-6 lg:gap-10">
                    <div>
                        @php
                            $firstHero = $heroProjects->first();
                            $firstMeta = $firstHero ? trim(collect([$firstHero->brand, $firstHero->city, $firstHero->year])->filter()->join(' · ')) : '';
                        @endphp
                        <p id="project-hero-title" class="font-serif text-delos-dark text-lg lg:text-xl font-light leading-tight">{{ $firstHero?->localized('title') }}</p>
                        <p id="project-hero-meta" class="text-delos-muted text-[9px] tracking-[0.3em] uppercase mt-0.5" style="font-family: 'Inter', sans-serif;">{{ $firstMeta }}</p>
                    </div>
                </div>

                <div data-motion="fade" class="flex items-center gap-3">
                    @for($i = 0; $i < $heroProjects->count(); $i++)
                        <button class="hero-dot w-2 h-2 rounded-full transition-all duration-500 {{ $i === 0 ? 'bg-delos-gold w-6' : 'bg-delos-dark/20 hover:bg-delos-dark/40' }}"
                                aria-label="Slide {{ $i + 1 }}" data-slide="{{ $i }}"></button>
                    @endfor
                </div>

                <div data-motion="fade-up" class="hidden sm:block">
                    <span class="text-delos-muted text-[9px] tracking-[0.4em] uppercase" style="font-family: 'Inter', sans-serif;">{{ pcontent('projects.hero.counter') }}</span>
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
            // $filters comes from the controller: ['all' => <localized>, '<type_key>' => <localized label>, ...]
            $filters = $filters ?? collect(['all' => pcontent('projects.filters.all')]);
            // DB type values are the data-filter values themselves, so the map
            // is a no-op for everything except the virtual "all" entry.
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
            @foreach($projects as $project)
                @php
                    $i = $loop->index;
                    $projTitle = $project->localized('title');
                    $projTypeLabel = $project->localized('type_label') ?: ucfirst($project->type ?? '');
                    // Gate: only projects the admin gave a description or a
                    // gallery image to become clickable. Everything else
                    // keeps today's markup minus the hover cues that imply
                    // interactivity — a non-clickable card shouldn't look
                    // clickable.
                    $projHasDetail = $project->hasDetailContent();
                @endphp
                <div data-motion="fade-up" class="project-item group relative aspect-[4/3] overflow-hidden bg-delos-dark {{ $projHasDetail ? 'cursor-pointer' : '' }}"
                     data-type="{{ $project->type }}"
                     style="--motion-delay: {{ ($i % 3) * 100 }}ms;">
                    <x-admin-edit-pill :href="route('admin.projects.edit', $project)" :label="'Edit ' . $projTitle" />

                    @if($projHasDetail)
                        {{-- Full-bleed link under the edit pill (z-5) and above the
                             image, mirroring .employee-card__link on the home page. --}}
                        <a href="{{ lroute('project-show', ['project' => $project->id]) }}"
                           class="project-item__link absolute inset-0 z-[4]"
                           aria-label="{{ pcontent('common.ctas.view_project') }} — {{ $projTitle }}">
                            <span class="sr-only">{{ $projTitle }}</span>
                        </a>
                    @endif

                    @if($project->image)
                        <x-responsive-image :src="$project->image"
                            :mobile-src="$project->image_mobile"
                            :focal="$project->focal_point"
                            :alt="$projTitle"
                            sizes="(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw"
                            class="absolute inset-0 w-full h-full object-cover opacity-60 transition-all duration-700 {{ $projHasDetail ? 'group-hover:opacity-75 group-hover:scale-105' : '' }}" />
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-delos-dark via-delos-dark/20 to-transparent"></div>

                    <div class="absolute bottom-0 left-0 right-0 p-6 lg:p-8 z-[3] pointer-events-none transition-transform duration-300 {{ $projHasDetail ? 'translate-y-2 group-hover:translate-y-0' : '' }}">
                        @if($project->city || $project->year)
                            <p class="text-delos-gold text-[10px] tracking-[0.4em] uppercase mb-1" style="font-family: 'Inter', sans-serif;">
                                {{ trim(collect([$project->city, $project->year])->filter()->join(' · ')) }}
                            </p>
                        @endif
                        <h3 class="font-serif text-delos-cream text-xl font-light transition-colors duration-300 {{ $projHasDetail ? 'group-hover:text-delos-gold' : '' }}">
                            {{ $projTitle }}
                        </h3>
                        <p class="text-delos-cream/50 text-[11px] tracking-[0.2em] uppercase mt-2 opacity-0 group-hover:opacity-100 transition-all duration-300" style="font-family: 'Inter', sans-serif;">
                            {{ $projTypeLabel }}
                        </p>
                        @if($projHasDetail)
                            <span class="project-item__cue inline-flex items-center gap-2 text-delos-gold text-[10px] tracking-[0.3em] uppercase mt-3 transition-all duration-300" style="font-family: 'Inter', sans-serif;">
                                {{ pcontent('common.ctas.view_project') }}
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </span>
                        @endif
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
            @foreach([0,1,2,3] as $i)
                @php
                    $statValue = pcontent("projects.stats.{$i}.value");
                    $statLabel = pcontent("projects.stats.{$i}.label");
                    // Split values like "500+", "100%", "4" into the numeric
                    // part that animates (via data-motion-counter) and the
                    // trailing suffix that fades in after the count completes.
                    preg_match('/^(\d+)(.*)$/u', (string) $statValue, $m);
                    $statNumber = $m[1] ?? $statValue;
                    $statSuffix = trim($m[2] ?? '');
                @endphp
                <div data-motion="fade-up">
                    <p class="font-serif text-delos-gold text-5xl lg:text-6xl font-light mb-3">
                        <span data-motion-counter="{{ $statNumber }}">0</span>@if($statSuffix)<span class="stat-suffix">{{ $statSuffix }}</span>@endif
                    </p>
                    <p class="text-delos-cream/50 text-[11px] tracking-[0.3em] uppercase" style="font-family: 'Inter', sans-serif;">{{ $statLabel }}</p>
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
                {{ pcontent('projects.cta.heading_1') }}<br>
                <em class="text-delos-gold not-italic">{{ pcontent('projects.cta.heading_accent') }}</em>
            </h2>
            <a href="{{ lroute('contact') }}"
               data-motion="fade-up"
               class="inline-flex items-center gap-3 px-10 py-4 bg-delos-dark text-delos-cream text-[12px] tracking-[0.25em] uppercase font-medium hover:bg-delos-gold hover:text-delos-dark transition-all duration-300 group">
                {{ pcontent('common.ctas.start_project') }}
                <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
    </div>
</section>

@endsection
