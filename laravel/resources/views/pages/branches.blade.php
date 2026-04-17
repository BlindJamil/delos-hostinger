@extends('layouts.app')

@section('title', pcontent('seo.branches.title'))
@section('description', pcontent('seo.branches.description'))

@section('content')

{{-- ============================================================
     HERO + MAP — single dark section
     The map IS the hero. Title sits above it; map fills the bulk
     of viewport on desktop. Editorial cartographic feel.
============================================================ --}}
<section class="branches-hero bg-delos-cream relative overflow-hidden">

    {{-- Subtle paper-grain texture overlay — barely visible, gives weight to the dark bg --}}
    <div class="branches-hero__grain" aria-hidden="true"></div>

    <div class="max-w-[1400px] mx-auto px-6 lg:px-12 pt-28 lg:pt-32 pb-12 lg:pb-16 relative z-10">

        {{-- Heading --}}
        <div data-motion-group="branches-heading" class="text-center mb-10 lg:mb-14">
            <div data-motion="fade-up" class="inline-flex items-center gap-3 mb-5">
                <span class="w-8 h-px bg-delos-gold/60"></span>
                <span class="text-overline text-delos-gold">{{ pcontent('branches.hero.overline') }}</span>
                <span class="w-8 h-px bg-delos-gold/60"></span>
            </div>
            <h1 data-motion="fade-up" class="font-serif text-delos-dark text-5xl lg:text-7xl font-light leading-tight">
                {{ pcontent('branches.hero.heading_1') }}<br>
                <em class="text-delos-gold not-italic">{{ pcontent('branches.hero.heading_accent') }}</em>
            </h1>
        </div>

        {{-- THE MAP --}}
        <div data-motion="fade-up" class="branches-hero__map-wrapper">
            <x-iraq-map :branches="$branches" />
        </div>
    </div>
</section>

{{-- ============================================================
     BRANCH CARDS
     Each branch gets a full editorial card. When a showroom photo
     is uploaded later it sits on the left; when null, a giant
     letter-watermark fills that space (still beautiful).
============================================================ --}}
<section class="section-padding bg-delos-cream">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div data-motion-group="branches-cards" class="space-y-12 lg:space-y-16">
            @foreach($branches as $branch)
                @php
                    $i = $loop->index;
                    $name = $branch->localized('name');
                    $address = $branch->localized('address');
                    $hours = $branch->localized('hours');
                    $established = $branch->localized('established');
                    $directions = $branch->directions_href;
                    // Whatsapp number stripped of spaces/symbols, prefixed +964 for IQ if local 07xxx
                    $waNumber = $branch->whatsapp ? preg_replace('/\D/', '', $branch->whatsapp) : null;
                    $waUrl = $waNumber ? "https://wa.me/{$waNumber}" : null;
                    // First letter of city for the watermark when no photo is set
                    $watermarkLetter = mb_substr($name, 0, 1);
                @endphp
                <article
                    id="branch-{{ $branch->city_key }}"
                    data-branch-key="{{ $branch->city_key }}"
                    data-motion="fade-up"
                    class="branch-card group relative grid grid-cols-1 lg:grid-cols-5 gap-0 bg-white border border-delos-dark/8 overflow-hidden"
                >
                    <x-admin-edit-pill :href="route('admin.branches.edit', $branch)" :label="'Edit ' . $name" />

                    {{-- Visual side: photo if available, else letter watermark --}}
                    <div class="branch-card__visual lg:col-span-2 relative bg-delos-dark overflow-hidden">
                        @if($branch->image_url)
                            <img
                                src="{{ $branch->image_url }}"
                                alt="{{ $name }} showroom"
                                loading="lazy"
                                decoding="async"
                                class="absolute inset-0 w-full h-full object-cover opacity-90"
                            >
                            <div class="absolute inset-0 bg-gradient-to-t from-delos-dark/60 to-transparent"></div>
                        @else
                            {{-- Letter watermark — luxury catalog look while photos are pending --}}
                            <div class="branch-card__watermark" aria-hidden="true">
                                <span class="branch-card__watermark-letter">{{ $watermarkLetter }}</span>
                                <span class="branch-card__watermark-rule"></span>
                                <span class="branch-card__watermark-meta">{{ pcontent('branches.photo_pending') }}</span>
                            </div>
                        @endif

                    </div>

                    {{-- Information side --}}
                    <div class="branch-card__info lg:col-span-3 p-8 lg:p-12 flex flex-col justify-center">
                        @if($established)
                            <p class="text-delos-gold text-[10px] tracking-[0.4em] uppercase font-medium mb-3">{{ $established }}</p>
                        @endif

                        <h2 class="font-serif text-delos-dark text-4xl lg:text-5xl font-light leading-tight mb-6">
                            {{ $name }}
                        </h2>

                        <div class="space-y-4 mb-8">
                            @if($address)
                                <div class="flex items-start gap-3">
                                    <svg class="w-4 h-4 text-delos-gold flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <p class="text-delos-muted text-sm leading-relaxed">{{ $address }}</p>
                                </div>
                            @endif
                            @if($branch->phone)
                                <div class="flex items-center gap-3">
                                    <svg class="w-4 h-4 text-delos-gold flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    <a href="tel:{{ preg_replace('/\s+/', '', $branch->phone) }}" class="text-delos-muted text-sm hover:text-delos-gold transition-colors">{{ $branch->phone }}</a>
                                </div>
                            @endif
                            @if($hours)
                                <div class="flex items-center gap-3">
                                    <svg class="w-4 h-4 text-delos-gold flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <p class="text-delos-muted text-sm">{{ $hours }}</p>
                                </div>
                            @endif
                        </div>

                        {{-- CTAs --}}
                        <div class="flex flex-wrap items-center gap-3">
                            @if($directions)
                                <a
                                    href="{{ $directions }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center gap-2 px-6 py-3 bg-delos-dark hover:bg-delos-gold text-delos-cream hover:text-delos-dark text-[11px] tracking-[0.25em] uppercase font-medium transition-all duration-300"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                                    {{ pcontent('branches.directions_cta') }}
                                </a>
                            @endif
                            @if($waUrl)
                                <a
                                    href="{{ $waUrl }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex items-center gap-2 px-6 py-3 border border-delos-dark/15 hover:border-delos-gold text-delos-dark hover:text-delos-gold text-[11px] tracking-[0.25em] uppercase font-medium transition-all duration-300"
                                >
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21h.01c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0012.04 2zm0 18.15c-1.48 0-2.93-.4-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.262 8.262 0 01-1.26-4.38c0-4.54 3.7-8.24 8.25-8.24 2.2 0 4.27.86 5.82 2.42a8.183 8.183 0 012.41 5.83c0 4.54-3.7 8.23-8.23 8.23z"/></svg>
                                    {{ pcontent('branches.whatsapp_cta') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

@endsection
