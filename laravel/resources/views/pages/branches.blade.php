@extends('layouts.app')

@section('title', 'Our Branches — Delos International Showrooms Across Iraq')
@section('description', 'Visit Delos International showrooms in Erbil, Kirkuk, Sulaymaniyah, and Baghdad. Experience Italian luxury interior design in person.')

@section('content')

{{-- Hero --}}
<section data-motion-hero class="relative min-h-[50vh] flex items-end overflow-hidden bg-delos-dark">
    <div class="absolute inset-0">
        <img src="{{ asset('images/delos-erbil-showroom-3.jpg') }}" alt="Delos LUBE Showroom Interior"
             class="w-full h-full object-cover opacity-25">
        <div class="absolute inset-0 bg-gradient-to-t from-delos-dark to-transparent"></div>
    </div>
    <div data-motion-group="hero" class="relative z-10 max-w-[1400px] mx-auto px-6 lg:px-12 pt-40 pb-20 w-full">
        <div data-motion="fade-up" class="inline-flex items-center gap-3 mb-6">
            <span class="w-8 h-px bg-delos-gold"></span>
            <span class="text-overline text-delos-gold">Our Presence</span>
        </div>
        <h1 data-motion="fade-up" class="font-serif text-delos-cream text-5xl lg:text-7xl font-light leading-tight">
            Four cities.<br>
            <em class="text-delos-gold not-italic">One standard of excellence.</em>
        </h1>
    </div>
</section>

{{-- Showrooms Grid --}}
<section class="section-padding bg-delos-cream">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">

        <div data-motion-group="branches-heading" class="mb-16">
            <div data-motion-line class="w-16 h-px bg-delos-gold mb-5"></div>
            <p data-motion="fade-up" class="text-overline text-delos-gold mb-4">Our Showrooms</p>
            <h2 data-motion="fade-up" class="text-heading-2 text-delos-dark">Four cities across Iraq.</h2>
        </div>

        <div data-motion-group="branches-cards" class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-20">
            @php
                $branches = [
                    ['city' => 'Erbil', 'est' => 'Est. 2020', 'address' => 'Gulan Street, Opposite the Chamber of Commerce', 'phone' => '0750 200 1003', 'open' => 'Sat – Thu: 10:00 – 20:00'],
                    ['city' => 'Kirkuk', 'est' => 'Est. 2021', 'address' => 'Kirkuk Showroom — contact for address', 'phone' => 'Contact us', 'open' => 'Sat – Thu: 10:00 – 20:00'],
                    ['city' => 'Sulaymaniyah', 'est' => 'Est. 2022', 'address' => 'Sulaymaniyah Showroom — contact for address', 'phone' => 'Contact us', 'open' => 'Sat – Thu: 10:00 – 20:00'],
                    ['city' => 'Baghdad', 'est' => 'Est. 2024', 'address' => 'Baghdad Showroom — contact for address', 'phone' => 'Contact us', 'open' => 'Sat – Thu: 10:00 – 20:00'],
                ];
            @endphp
            @foreach($branches as $i => $b)
                <div data-motion="fade-up" class="group p-8 lg:p-10 border border-delos-dark/10 hover:border-delos-gold/40 hover:bg-delos-ivory transition-all duration-400">
                    <div class="flex items-start justify-between mb-6">
                        <div>
                            <p class="font-sans text-delos-gold text-[10px] tracking-[0.4em] uppercase font-medium mb-2">{{ $b['est'] }}</p>
                            <h3 class="font-serif text-delos-dark text-2xl font-medium group-hover:text-delos-gold transition-colors duration-300">{{ $b['city'] }}</h3>
                        </div>
                        <div class="w-10 h-10 border border-delos-gold/30 flex items-center justify-center flex-shrink-0 group-hover:bg-delos-gold group-hover:border-delos-gold transition-all duration-300">
                            <svg class="w-4 h-4 text-delos-gold group-hover:text-delos-dark transition-colors duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="space-y-3 font-sans">
                        <p class="text-delos-muted text-sm flex items-start gap-3">
                            <svg class="w-4 h-4 text-delos-gold flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                            {{ $b['address'] }}
                        </p>
                        <p class="text-delos-muted text-sm flex items-center gap-3">
                            <svg class="w-4 h-4 text-delos-gold flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            {{ $b['phone'] }}
                        </p>
                        <p class="text-delos-muted text-sm flex items-center gap-3">
                            <svg class="w-4 h-4 text-delos-gold flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $b['open'] }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</section>

@endsection
