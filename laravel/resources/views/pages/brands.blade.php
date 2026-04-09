@extends('layouts.app')

@section('title', 'Italian Brand Partners — Delos International Iraq')
@section('description', 'Delos International exclusively partners with Italy\'s leading luxury brands — LUBE, Frigerio, Vittoria Frigerio, CANTORI, and SKEMA. Authentic Italian quality delivered to Iraq.')

@section('content')

{{-- Hero --}}
<section data-motion-hero class="relative min-h-[55vh] flex items-end overflow-hidden bg-delos-dark">
    <div class="absolute inset-0">
        <img src="{{ asset('images/delos-wall-branding.jpg') }}" alt=""
             class="w-full h-full object-cover opacity-25">
        <div class="absolute inset-0 bg-gradient-to-t from-delos-dark to-transparent"></div>
    </div>
    <div data-motion-group="hero" class="relative z-10 max-w-[1400px] mx-auto px-6 lg:px-12 pt-40 pb-20 w-full">
        <div data-motion="fade-up" class="inline-flex items-center gap-3 mb-6">
            <span class="w-8 h-px bg-delos-gold"></span>
            <span class="text-delos-gold text-[11px] tracking-[0.4em] uppercase font-medium" style="font-family: 'Inter', sans-serif;">Made in Italy</span>
        </div>
        <h1 data-motion="fade-up" class="font-serif text-delos-cream text-5xl lg:text-7xl font-light leading-tight">
            Italy's finest.<br>
            <em class="text-delos-gold not-italic">Delivered to Iraq.</em>
        </h1>
    </div>
</section>

{{-- Intro --}}
<section class="py-24 lg:py-32 bg-delos-cream">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="grid lg:grid-cols-2 gap-16 items-center mb-24">
            <div data-motion-group="brands-intro">
                <p data-motion="fade-up" class="text-delos-gold text-[11px] tracking-[0.4em] uppercase font-medium mb-6" style="font-family: 'Inter', sans-serif;">Exclusive Partnerships</p>
                <h2 data-motion="fade-up" class="font-serif text-4xl lg:text-5xl font-light text-delos-dark leading-tight mb-8">
                    We only partner<br>
                    with the<br>
                    <em class="text-delos-gold not-italic">best of Italy.</em>
                </h2>
                <p data-motion="fade-up" class="text-delos-muted text-base leading-relaxed" style="font-family: 'Inter', sans-serif;">
                    Delos International holds exclusive partnerships with five of Italy's most prestigious interior design manufacturers. Each brand was selected for its heritage, craftsmanship, and alignment with our uncompromising standard of luxury. Through these strategic alliances, we deliver authentic Italian design, premium materials, and world-class craftsmanship directly to Iraqi homes.
                </p>
            </div>
            <div data-motion="scale-in" class="relative aspect-square overflow-hidden bg-delos-dark">
                <img src="{{ asset('images/italian-materials.jpg') }}" alt="Italian Craftsmanship"
                     class="w-full h-full object-cover opacity-60">
                <div class="absolute inset-0 flex items-center justify-center">
                    <p class="font-serif text-delos-cream text-2xl lg:text-3xl font-light italic text-center px-8">
                        "Where Italian<br>craft meets<br>Iraqi homes."
                    </p>
                </div>
            </div>
        </div>

        {{-- Brand Cards --}}
        @php
            $brands = [
                [
                    'name' => 'LUBE',
                    'category' => 'Italian Kitchen Excellence',
                    'origin' => 'Treia, Macerata — Italy',
                    'since' => 'Est. 1967',
                    'desc' => 'LUBE is Italy\'s most awarded kitchen manufacturer — a family-owned company producing over 600,000 kitchens per year. With LUBE, Delos delivers the gold standard in Italian kitchen design: perfect ergonomics, premium materials, and endless customization.',
                    'specialties' => ['Fitted kitchens', 'Open-plan solutions', 'Island kitchens', 'Custom finishes'],
                    'img' => 'lube-kitchen-3.jpg',
                    'url' => 'https://www.lubecucine.it',
                ],
                [
                    'name' => 'Frigerio',
                    'category' => 'Classic Italian Furniture',
                    'origin' => 'Brianza, Lombardy — Italy',
                    'since' => 'Est. 1955',
                    'desc' => 'Frigerio represents the finest tradition of Brianza craftsmanship — Italy\'s historic furniture-making heartland. Known for timeless design and meticulous handcrafting, Frigerio pieces are investments in beauty that endure for generations.',
                    'specialties' => ['Living room collections', 'Dining furniture', 'Classic Italian design', 'Upholstery mastery'],
                    'img' => 'frigerio-living.jpg',
                    'url' => 'https://www.frigeriosalotti.it',
                ],
                [
                    'name' => 'Vittoria Frigerio',
                    'category' => 'Heritage Luxury Design',
                    'origin' => 'Brianza, Lombardy — Italy',
                    'since' => 'Heritage Collection',
                    'desc' => 'The prestige line within the Frigerio family, Vittoria Frigerio represents the absolute pinnacle of Italian interior artistry. Each piece is a statement of heritage, refinement, and extraordinary craftsmanship reserved for the most discerning spaces.',
                    'specialties' => ['Prestige bedroom collections', 'Heritage living suites', 'Bespoke finishes', 'Handcrafted details'],
                    'img' => 'collection-vittoria.jpg',
                    'url' => 'https://www.vittoriafrigerio.it',
                ],
                [
                    'name' => 'CANTORI',
                    'category' => 'Artisan Italian Creations',
                    'origin' => 'Forlì, Emilia-Romagna — Italy',
                    'since' => 'Est. 1948',
                    'desc' => 'CANTORI is synonymous with Italian artisan excellence. Each CANTORI piece is conceived as a work of art — using traditional crafting techniques combined with contemporary design sensibility. Their collections bring a unique, soulful quality to every interior.',
                    'specialties' => ['Accent furniture', 'Statement pieces', 'Metal and glass artistry', 'Custom commissions'],
                    'img' => 'cantori-1.jpg',
                    'url' => 'https://www.cantori.it',
                ],
                [
                    'name' => 'SKEMA',
                    'category' => 'Premium Italian Flooring',
                    'origin' => 'Veneto Region — Italy',
                    'since' => 'Premium Flooring',
                    'desc' => 'SKEMA combines Italian design culture with advanced engineering to produce flooring systems of remarkable beauty and technical excellence. From natural wood to contemporary engineered surfaces, SKEMA creates the foundation upon which great interiors are built.',
                    'specialties' => ['Engineered wood flooring', 'Natural hardwood', 'Contemporary surfaces', 'Wall panel systems'],
                    'img' => 'italian-materials.jpg',
                    'url' => 'https://www.skema.eu',
                ],
            ];
        @endphp

        <div class="space-y-20">
            @foreach($brands as $i => $brand)
                <div data-motion="fade-up" class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center {{ $i % 2 === 1 ? 'lg:[&>*:first-child]:order-2' : '' }}">
                    {{-- Image --}}
                    <div class="relative aspect-[4/3] overflow-hidden bg-delos-dark">
                        <img src="{{ asset('images/' . $brand['img']) }}" alt="{{ $brand['name'] }}"
                             class="w-full h-full object-cover opacity-65 hover:scale-105 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-delos-dark/60 to-transparent"></div>
                        <div class="absolute bottom-6 left-6">
                            <p class="font-serif text-delos-cream text-4xl font-semibold tracking-widest">{{ $brand['name'] }}</p>
                        </div>
                    </div>
                    {{-- Content --}}
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <span class="text-delos-gold text-[10px] tracking-[0.4em] uppercase px-3 py-1 border border-delos-gold/30" style="font-family: 'Inter', sans-serif;">{{ $brand['category'] }}</span>
                        </div>
                        <h2 class="font-serif text-delos-dark text-3xl lg:text-4xl font-light mb-2">{{ $brand['name'] }}</h2>
                        <p class="text-delos-muted text-xs tracking-wide mb-6" style="font-family: 'Inter', sans-serif;">{{ $brand['origin'] }} · {{ $brand['since'] }}</p>
                        <p class="text-delos-muted text-base leading-relaxed mb-8" style="font-family: 'Inter', sans-serif;">{{ $brand['desc'] }}</p>
                        <ul class="grid grid-cols-2 gap-3">
                            @foreach($brand['specialties'] as $spec)
                                <li class="flex items-center gap-2 text-delos-muted text-sm" style="font-family: 'Inter', sans-serif;">
                                    <span class="w-1 h-1 rounded-full bg-delos-gold flex-shrink-0"></span>
                                    {{ $spec }}
                                </li>
                            @endforeach
                        </ul>
                        <a href="{{ $brand['url'] }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-2 mt-8 text-delos-gold text-[11px] tracking-[0.3em] uppercase font-medium border-b border-delos-gold/30 pb-1 hover:border-delos-gold hover:gap-3 transition-all duration-300" style="font-family: 'Inter', sans-serif;">
                            Visit Official Website
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 17L17 7M17 7H7M17 7v10"/></svg>
                        </a>
                    </div>
                </div>
                @if($i < count($brands) - 1)
                    <div class="border-t border-delos-dark/10"></div>
                @endif
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-24 bg-delos-dark text-center">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div data-motion-group="brands-cta">
        <h2 data-motion="fade-up" class="font-serif text-delos-cream text-4xl lg:text-5xl font-light mb-8">
            Experience Italian luxury<br>
            <em class="text-delos-gold not-italic">in person.</em>
        </h2>
        <a href="{{ route('contact') }}"
           data-motion="fade-up"
           class="inline-flex items-center gap-3 px-10 py-4 bg-delos-gold text-delos-dark text-[12px] tracking-[0.25em] uppercase font-medium hover:bg-delos-gold-light transition-all duration-300">
            Visit a Showroom
        </a>
        </div>
    </div>
</section>

@endsection
