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
            @if($p->image)
                {{-- Hero slide alt intentionally empty — avoids the
                     5-slide alt-text-leak overlap. The visible title +
                     meta strip underneath carries the actual label. --}}
                <x-responsive-image
                    :src="$p->image"
                    :mobile-src="$p->image_mobile"
                    :focal="$p->focal_point"
                    alt=""
                    sizes="100vw"
                    class="hero-slide absolute inset-0 w-full h-full object-cover {{ $i === 0 ? '' : 'opacity-0' }}"
                :loading="$i === 0 ? 'eager' : 'lazy'"
                :fetchpriority="$i === 0 ? 'high' : null" />
            @endif
        @endforeach
    </div>

    {{-- Bottom strip: compact glass bar. Only the dot navigation is kept —
         no title, meta, or counter text, since projects no longer carry
         per-card copy. --}}
    <div class="absolute bottom-0 inset-x-0 z-10">
        <div class="bg-delos-cream/50 backdrop-blur-2xl border-t border-delos-gold/10 py-5 lg:py-6 px-6 lg:px-12" style="-webkit-backdrop-filter: blur(30px);">
            <div class="max-w-[1400px] mx-auto flex items-center justify-center gap-6">

                <div data-motion="fade" class="flex items-center gap-3">
                    @for($i = 0; $i < $heroProjects->count(); $i++)
                        <button class="hero-dot w-2 h-2 rounded-full transition-all duration-500 {{ $i === 0 ? 'bg-delos-gold w-6' : 'bg-delos-dark/20 hover:bg-delos-dark/40' }}"
                                aria-label="Slide {{ $i + 1 }}" data-slide="{{ $i }}"></button>
                    @endfor
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
                    // Title is no longer rendered on the card — kept here
                    // purely to power the <img alt> + admin edit pill label.
                    $projTitle = $project->localized('title');
                @endphp
                <div data-motion="fade-up" class="project-item group relative aspect-[4/3] overflow-hidden bg-delos-dark cursor-pointer"
                     data-type="{{ $project->type }}"
                     style="--motion-delay: {{ ($i % 3) * 100 }}ms;">
                    <x-admin-edit-pill :href="route('admin.projects.edit', $project)" :label="'Edit ' . $projTitle" />
                    @if($project->image)
                        <x-responsive-image :src="$project->image"
                            :mobile-src="$project->image_mobile"
                            :focal="$project->focal_point"
                            :alt="$projTitle"
                            sizes="(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw"
                            class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
                    @endif

                    {{-- Category label only. No title, city, year, or brand. --}}
                    <div class="absolute bottom-5 left-6">
                        <p class="text-delos-cream text-[10px] tracking-[0.4em] uppercase [text-shadow:0_2px_10px_rgba(0,0,0,0.6)]" style="font-family: 'Inter', sans-serif;">
                            {{ $project->localized('type_label') ?: ucfirst($project->type ?? '') }}
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
