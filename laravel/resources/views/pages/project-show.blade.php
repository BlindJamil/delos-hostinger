@extends('layouts.app')

@php
    $projTitle = $project->localized('title');
    $projTypeLabel = $project->localized('type_label') ?: ucfirst($project->type ?? '');
    $projDescription = $project->localized('description');
    $projOverline = trim(collect([$projTypeLabel, $project->brand])->filter()->join(' — '));
    $projMeta = trim(collect([$project->city, $project->year])->filter()->join(' · '));
    $metaDescription = \Illuminate\Support\Str::limit(trim(strip_tags((string) $projDescription)), 160);
@endphp

@section('title', $projTitle . ' — ' . ($project->city ?: 'Delos International'))
@section('description', $metaDescription ?: pcontent('seo.projects.description'))

@section('content')

{{-- 1 · HERO — full-bleed magazine cover, reusing the Services hero classes.
     data-hero-frame is the signature scroll effect: on desktop, the image
     scroll-scrubs from full-bleed into a matted, gold-bordered frame as the
     visitor scrolls past — see initHeroFrame() in motion.js. It's additive:
     with no JS (or on touch/narrow screens where it's intentionally skipped)
     the hero simply stays full-bleed, so nothing here is load-bearing. --}}
<section data-motion-hero class="svc-hero relative min-h-[70vh] lg:min-h-[85vh] overflow-hidden bg-delos-cream">
    <x-admin-edit-pill :href="route('admin.projects.edit', $project)" :label="'Edit ' . $projTitle" />

    @if($project->image)
        <div class="svc-hero__image absolute inset-0" data-hero-frame>
            <x-responsive-image :src="$project->image"
                :mobile-src="$project->image_mobile"
                :focal="$project->focal_point"
                :alt="$projTitle"
                sizes="100vw"
                loading="eager"
                fetchpriority="high"
                class="w-full h-full object-cover" />
        </div>
    @else
        <div class="absolute inset-0 bg-delos-dark"></div>
    @endif
    <div class="svc-hero__overlay absolute inset-0 pointer-events-none"></div>

    <div class="absolute inset-0 z-[2] flex items-end">
        <div class="max-w-[1400px] mx-auto w-full px-6 lg:px-12 pb-14 lg:pb-20">
            @if($projOverline)
                <div data-motion="fade" class="flex items-center gap-3 mb-5">
                    <span class="w-8 h-px bg-delos-gold/70"></span>
                    <span class="text-delos-gold text-[10px] lg:text-[11px] tracking-[0.5em] uppercase font-semibold" style="font-family: 'Inter', sans-serif;">{{ $projOverline }}</span>
                </div>
            @endif
            <h1 data-motion="fade-up" class="project-show__title font-serif text-delos-cream font-light leading-[1.08] max-w-3xl [text-shadow:0_2px_30px_rgba(0,0,0,0.35)]">
                {{ $projTitle }}
            </h1>
            @if($projMeta)
                <p data-motion="fade-up" class="text-delos-cream/70 text-[11px] tracking-[0.35em] uppercase mt-5" style="font-family: 'Inter', sans-serif;">{{ $projMeta }}</p>
            @endif
        </div>
    </div>
</section>

{{-- 2 · OVERVIEW — back link, fact list, description --}}
<section class="py-24 lg:py-32 bg-delos-cream">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">

        <div data-motion="fade" class="mb-12 lg:mb-16">
            <a href="{{ lroute('projects') }}"
               class="inline-flex items-center gap-3 text-delos-muted hover:text-delos-gold text-[11px] tracking-[0.3em] uppercase font-medium transition-colors duration-300 group"
               style="font-family: 'Inter', sans-serif;">
                <svg class="w-4 h-4 transition-transform duration-300 group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>{{ pcontent('common.ctas.back_to_projects') }}</span>
            </a>
        </div>

        <div class="grid lg:grid-cols-12 gap-10 lg:gap-16 lg:items-start">

            {{-- Fact list --}}
            <div class="lg:col-span-4">
                <p data-motion="fade-up" class="text-delos-gold text-[10px] tracking-[0.5em] uppercase font-semibold mb-5" style="font-family: 'Inter', sans-serif;">
                    {{ pcontent('projects.detail.overline') }}
                </p>
                <div data-motion-line class="w-12 h-px bg-delos-gold/60 mb-8"></div>
                <dl data-motion-group="project-facts" class="space-y-5">
                    @foreach([
                        'projects.detail.meta.city' => $project->city,
                        'projects.detail.meta.type' => $projTypeLabel,
                        'projects.detail.meta.brand' => $project->brand,
                        'projects.detail.meta.year' => $project->year,
                    ] as $factKey => $factValue)
                        @if($factValue)
                            <div data-motion="fade-up" class="border-b border-delos-dark/10 pb-4">
                                <dt class="text-delos-muted text-[10px] tracking-[0.3em] uppercase mb-1.5" style="font-family: 'Inter', sans-serif;">{{ pcontent($factKey) }}</dt>
                                <dd class="font-serif text-delos-dark text-lg font-light">{{ $factValue }}</dd>
                            </div>
                        @endif
                    @endforeach
                </dl>
            </div>

            {{-- Description --}}
            <div class="lg:col-span-8">
                @if($project->hasDescriptionText())
                    <h2 data-motion="fade-up" class="font-serif text-delos-dark text-3xl lg:text-4xl font-light mb-8">
                        {{ pcontent('projects.detail.heading') }}
                    </h2>
                    {{-- Rich text from the admin's WYSIWYG editor (same contract as employee-show). --}}
                    <div data-motion="fade-up" class="project-show__body text-delos-muted text-sm lg:text-base leading-relaxed max-w-2xl" style="font-family: 'Inter', sans-serif;">
                        {!! $projDescription !!}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- 3 · GALLERY --}}
@if($project->images->isNotEmpty())
<section class="pb-24 lg:pb-32 bg-delos-cream">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="mb-12">
            <p data-motion="fade-up" class="text-delos-gold text-[10px] tracking-[0.5em] uppercase font-semibold mb-5" style="font-family: 'Inter', sans-serif;">
                {{ pcontent('projects.detail.gallery') }}
            </p>
            <div data-motion-line class="w-12 h-px bg-delos-gold/60"></div>
        </div>

        <div id="project-gallery" data-motion-group="project-gallery"
             class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-5">
            @foreach($project->images as $galleryIndex => $galleryImage)
                <button type="button"
                        data-motion="fade-up"
                        class="project-gallery__item img-hover-zoom relative aspect-[4/3] overflow-hidden bg-delos-dark-2 w-full"
                        data-gallery-index="{{ $galleryIndex }}"
                        data-gallery-full="{{ $galleryImage->image_url }}"
                        aria-label="{{ pcontent('projects.detail.open_image') }} {{ $galleryIndex + 1 }}">
                    <x-responsive-image :src="$galleryImage->image"
                        :mobile-src="$galleryImage->image_mobile"
                        :focal="$galleryImage->focal_point"
                        :alt="$projTitle"
                        sizes="(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw"
                        class="absolute inset-0 w-full h-full object-cover" />
                </button>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- 4 · MORE PROJECTS --}}
@if($relatedProjects->isNotEmpty())
<section class="py-20 lg:py-24 bg-delos-dark">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <h2 data-motion="fade-up" class="font-serif text-delos-cream text-3xl lg:text-4xl font-light mb-12">
            {{ pcontent('projects.detail.more') }}
        </h2>
        <div data-motion-group="project-related" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($relatedProjects as $related)
                @php $relatedTitle = $related->localized('title'); @endphp
                <div data-motion="fade-up" class="group relative aspect-[4/3] overflow-hidden bg-delos-dark-2 cursor-pointer">
                    <a href="{{ lroute('project-show', ['project' => $related->id]) }}"
                       class="absolute inset-0 z-[4]"
                       aria-label="{{ pcontent('common.ctas.view_project') }} — {{ $relatedTitle }}">
                        <span class="sr-only">{{ $relatedTitle }}</span>
                    </a>
                    @if($related->image)
                        <x-responsive-image :src="$related->image"
                            :mobile-src="$related->image_mobile"
                            :focal="$related->focal_point"
                            :alt="$relatedTitle"
                            sizes="(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw"
                            class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:opacity-80 group-hover:scale-105 transition-all duration-700" />
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-delos-dark via-delos-dark/20 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 p-6 z-[3] pointer-events-none">
                        <p class="text-delos-gold text-[10px] tracking-[0.4em] uppercase mb-1" style="font-family: 'Inter', sans-serif;">
                            {{ trim(collect([$related->city, $related->year])->filter()->join(' · ')) }}
                        </p>
                        <h3 class="font-serif text-delos-cream text-xl font-light group-hover:text-delos-gold transition-colors duration-300">{{ $relatedTitle }}</h3>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- 5 · CTA — distinct copy from /projects' own closing CTA so a visitor
     arriving here from that page doesn't see the same line twice. --}}
<section class="py-24 bg-delos-cream text-center">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div data-motion-group="project-detail-cta">
            <p data-motion="fade-up" class="text-delos-gold text-[10px] tracking-[0.5em] uppercase font-semibold mb-6" style="font-family: 'Inter', sans-serif;">
                {{ pcontent('projects.detail.cta.overline') }}
            </p>
            <h2 data-motion="fade-up" class="font-serif text-delos-dark text-4xl lg:text-5xl font-light mb-10">
                {{ pcontent('projects.detail.cta.heading') }}
            </h2>
            <div data-motion="fade-up" class="flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-6">
                <a href="{{ lroute('contact') }}"
                   class="inline-flex items-center gap-3 px-10 py-4 bg-delos-dark text-delos-cream text-[12px] tracking-[0.25em] uppercase font-medium hover:bg-delos-gold hover:text-delos-dark transition-all duration-300 group">
                    {{ pcontent('common.ctas.start_project') }}
                    <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
                <a href="{{ lroute('projects') }}"
                   class="inline-flex items-center gap-2 text-delos-dark/70 hover:text-delos-gold text-[11px] tracking-[0.25em] uppercase font-medium transition-colors duration-300 border-b border-delos-dark/20 hover:border-delos-gold pb-1"
                   style="font-family: 'Inter', sans-serif;">
                    {{ pcontent('common.ctas.view_all_projects') }}
                </a>
            </div>
        </div>
    </div>
</section>

{{-- 6 · LIGHTBOX — rendered only when there is a gallery to open --}}
@if($project->images->isNotEmpty())
<div id="project-lightbox" class="project-lightbox" data-project-lightbox hidden
     role="dialog" aria-modal="true" aria-label="{{ pcontent('projects.detail.gallery') }}">
    <button type="button" class="project-lightbox__close" data-lightbox-close
            aria-label="{{ pcontent('projects.detail.lightbox_close') }}">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>

    <button type="button" class="project-lightbox__nav project-lightbox__nav--prev" data-lightbox-prev
            aria-label="{{ pcontent('projects.detail.lightbox_prev') }}">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7"/>
        </svg>
    </button>

    <figure class="project-lightbox__stage">
        <img class="project-lightbox__img" data-lightbox-image src="" alt="{{ $projTitle }}">
    </figure>

    <button type="button" class="project-lightbox__nav project-lightbox__nav--next" data-lightbox-next
            aria-label="{{ pcontent('projects.detail.lightbox_next') }}">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"/>
        </svg>
    </button>

    <p class="project-lightbox__counter" data-lightbox-counter aria-live="polite"></p>
</div>
@endif

@endsection
