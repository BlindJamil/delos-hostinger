@extends('layouts.app')

@section('title', 'Services — Delos International Italian Luxury Solutions Iraq')
@section('description', 'Delos International offers luxury Italian kitchens, living room furniture, bedrooms, wardrobes, flooring systems, and complete turnkey project execution across Iraq.')

@section('content')

{{-- Hero --}}
<section class="relative min-h-[60vh] flex items-end overflow-hidden bg-delos-dark">
    <div class="absolute inset-0">
        <img src="{{ asset('images/services-hero.jpg') }}" alt=""
             class="w-full h-full object-cover opacity-30" onerror="this.style.display='none'">
        <div class="absolute inset-0 bg-gradient-to-t from-delos-dark to-transparent"></div>
    </div>
    <div class="relative z-10 max-w-[1400px] mx-auto px-6 lg:px-12 pt-40 pb-20 w-full">
        <div class="hero-fade inline-flex items-center gap-3 mb-6 opacity-0 translate-y-4">
            <span class="w-8 h-px bg-delos-gold"></span>
            <span class="text-delos-gold text-[11px] tracking-[0.4em] uppercase font-medium" style="font-family: 'Inter', sans-serif;">What We Offer</span>
        </div>
        <h1 class="hero-fade font-serif text-delos-cream text-5xl lg:text-7xl font-light leading-tight opacity-0 translate-y-4">
            Italian luxury<br>
            <em class="text-delos-gold not-italic">for every room.</em>
        </h1>
    </div>
</section>

{{-- Services Detail --}}
<section class="py-24 lg:py-32 bg-delos-cream">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">

        @php
            $services = [
                [
                    'num'   => '01',
                    'name'  => 'Luxury Kitchens',
                    'desc'  => 'Your kitchen is the heart of your home. We bring Italy\'s finest kitchen manufacturers directly to your door — precision-engineered, beautifully finished, and designed around your lifestyle. From classic to ultra-contemporary, every Delos kitchen is a masterpiece of Italian craft.',
                    'feat'  => ['Custom Italian cabinetry', 'Premium hardware and finishes', 'Integrated appliance solutions', 'Ergonomic spatial design', '3D planning service'],
                    'brand' => 'LUBE Kitchens',
                    'img'   => 'service-kitchens.jpg',
                ],
                [
                    'num'   => '02',
                    'name'  => 'Living Room',
                    'desc'  => 'Create a living room that reflects who you are. Our Italian living collections offer sofas, armchairs, coffee tables, and statement pieces that combine sculptural beauty with enduring comfort — crafted to elevate your most-lived-in space.',
                    'feat'  => ['Italian sofas and sectionals', 'Coffee and side tables', 'Statement accent chairs', 'TV unit systems', 'Complete room styling'],
                    'brand' => 'Frigerio · CANTORI',
                    'img'   => 'service-living.jpg',
                ],
                [
                    'num'   => '03',
                    'name'  => 'Bedroom',
                    'desc'  => 'Your bedroom should be a sanctuary. Delos delivers complete Italian bedroom collections — from beds and headboards to wardrobes and bedside tables — where every piece is designed to create a space of calm, beauty, and rest.',
                    'feat'  => ['Italian bed frames and headboards', 'Premium mattress bases', 'Bedside tables and dressers', 'Full bedroom coordination', 'Custom finish options'],
                    'brand' => 'Vittoria Frigerio · Frigerio',
                    'img'   => 'service-bedroom.jpg',
                ],
                [
                    'num'   => '04',
                    'name'  => 'Wardrobes & Dressing Systems',
                    'desc'  => 'Italian wardrobes are an art form. Our dressing systems are precision-designed with integrated lighting, custom interior fittings, and flawless hardware — transforming your storage into an experience of everyday luxury.',
                    'feat'  => ['Walk-in wardrobe design', 'Hinged and sliding door systems', 'Custom interior organization', 'Integrated LED lighting', 'Mirror and glass options'],
                    'brand' => 'LUBE · Frigerio',
                    'img'   => 'service-wardrobes.jpg',
                ],
                [
                    'num'   => '05',
                    'name'  => 'Flooring & Wall Systems',
                    'desc'  => 'The foundation of great design. SKEMA\'s premium Italian flooring and wall finishing systems provide the perfect base for your Delos interior — from rich natural wood to contemporary engineered surfaces.',
                    'feat'  => ['Premium Italian wood flooring', 'Engineered hardwood systems', 'Wall panelling and cladding', 'Technical flooring solutions', 'Full installation service'],
                    'brand' => 'SKEMA',
                    'img'   => 'service-flooring.jpg',
                ],
                [
                    'num'   => '06',
                    'name'  => 'Turnkey Projects',
                    'desc'  => 'Our most comprehensive service. From your first consultation to the final delivery and installation — Delos manages every element of your project with the same precision and care as Italy\'s finest interior studios.',
                    'feat'  => ['On-site measurement and consultation', 'Personalized 3D design concepts', 'Italian sourcing and logistics', 'Professional installation team', 'Project management end-to-end'],
                    'brand' => 'All Delos Partners',
                    'img'   => 'service-turnkey.jpg',
                ],
            ];
        @endphp

        <div class="space-y-24">
            @foreach($services as $i => $s)
                <div class="reveal grid lg:grid-cols-2 gap-12 lg:gap-20 items-center {{ $i % 2 === 1 ? 'lg:[&>*:first-child]:order-2' : '' }}">

                    {{-- Image --}}
                    <div class="relative aspect-[4/3] overflow-hidden bg-delos-dark">
                        <img src="{{ asset('images/' . $s['img']) }}" alt="{{ $s['name'] }}"
                             class="w-full h-full object-cover opacity-70 hover:scale-105 transition-transform duration-700"
                             onerror="this.style.display='none'">
                        <div class="absolute inset-0 bg-gradient-to-t from-delos-dark/40 to-transparent"></div>
                        <div class="absolute top-6 left-6">
                            <span class="text-delos-gold text-[11px] tracking-[0.3em] uppercase font-medium bg-delos-dark/70 backdrop-blur-sm px-3 py-1.5" style="font-family: 'Inter', sans-serif;">
                                {{ $s['brand'] }}
                            </span>
                        </div>
                    </div>

                    {{-- Text --}}
                    <div>
                        <span class="text-delos-gold text-[11px] tracking-[0.3em] font-medium mb-4 block" style="font-family: 'Inter', sans-serif;">{{ $s['num'] }}</span>
                        <h2 class="font-serif text-delos-dark text-3xl lg:text-4xl font-light leading-tight mb-6">
                            {{ $s['name'] }}
                        </h2>
                        <p class="text-delos-muted text-base leading-relaxed mb-8" style="font-family: 'Inter', sans-serif;">
                            {{ $s['desc'] }}
                        </p>
                        <ul class="space-y-3 mb-8">
                            @foreach($s['feat'] as $feat)
                                <li class="flex items-center gap-3 text-delos-muted text-sm" style="font-family: 'Inter', sans-serif;">
                                    <span class="w-1 h-1 rounded-full bg-delos-gold flex-shrink-0"></span>
                                    {{ $feat }}
                                </li>
                            @endforeach
                        </ul>
                        <a href="{{ route('contact') }}"
                           class="inline-flex items-center gap-2 text-delos-dark text-[12px] tracking-[0.2em] uppercase font-medium hover:text-delos-gold transition-colors duration-300 group border-b border-delos-dark/20 pb-1 hover:border-delos-gold" style="font-family: 'Inter', sans-serif;">
                            Book a Consultation
                            <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                    </div>

                </div>
                @if($i < count($services) - 1)
                    <div class="border-t border-delos-dark/10"></div>
                @endif
            @endforeach
        </div>

    </div>
</section>

{{-- CTA --}}
<section class="py-24 lg:py-32 bg-delos-dark text-center">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <p class="reveal text-delos-gold text-[11px] tracking-[0.4em] uppercase font-medium mb-6" style="font-family: 'Inter', sans-serif;">Start Today</p>
        <h2 class="reveal font-serif text-delos-cream text-4xl lg:text-6xl font-light leading-tight mb-8">
            Ready to transform<br>
            <em class="text-delos-gold not-italic">your space?</em>
        </h2>
        <a href="{{ route('contact') }}"
           class="reveal inline-flex items-center gap-3 px-10 py-4 bg-delos-gold text-delos-dark text-[12px] tracking-[0.25em] uppercase font-medium hover:bg-delos-gold-light transition-all duration-300 group">
            Book Free Consultation
            <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
        </a>
    </div>
</section>

@endsection
