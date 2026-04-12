{{--
    First-visit language picker — full-page light design.
    Matches Delos' cream/ivory brand aesthetic. No dark overlay.
    No close button — user must pick to proceed.
--}}

@php
    $picker = __('common.language_picker');
@endphp

<div id="lang-picker"
     role="dialog"
     aria-modal="true"
     aria-labelledby="lp-title"
     class="fixed inset-0 z-[10000] flex items-center justify-center bg-delos-cream overflow-auto"
     style="cursor: default;">

    <div class="w-full max-w-3xl mx-auto px-6 py-12 lg:py-16">

        {{-- Logo --}}
        <div class="flex flex-col items-center mb-12">
            <div class="w-16 h-16 lg:w-20 lg:h-20 overflow-hidden rounded-full border-2 border-delos-gold/30 mb-6 shadow-lg">
                <img src="{{ asset('images/delos-logo.jpg') }}" alt="Delos International" class="w-full h-full object-cover">
            </div>
            <div class="flex flex-col items-center">
                <span class="font-serif text-xl lg:text-2xl font-semibold tracking-[0.3em] text-delos-dark">DELOS</span>
                <span class="text-[9px] tracking-[0.5em] uppercase text-delos-gold font-medium mt-1" style="font-family: 'Inter', sans-serif;">INTERNATIONAL</span>
            </div>
        </div>

        {{-- Trilingual heading --}}
        <div class="text-center mb-10 lg:mb-14">
            <p id="lp-title" class="font-serif text-2xl lg:text-3xl text-delos-dark font-light leading-relaxed mb-2">
                {{ $picker['heading_en'] }}
            </p>
            <p dir="rtl"
               class="font-serif text-2xl lg:text-3xl text-delos-dark font-light leading-relaxed mb-2"
               style="font-family: 'Amiri', 'Cormorant Garamond', serif;">
                {{ $picker['heading_ar'] }}
            </p>
            <p class="font-serif text-2xl lg:text-3xl text-delos-dark italic font-light leading-relaxed">
                {{ $picker['heading_it'] }}
            </p>
            <div class="w-12 h-px bg-delos-gold mx-auto mt-8"></div>
        </div>

        {{-- Language cards — plain <a> tags, no inline JS.
             Server middleware stamps cookies on landing. --}}
        <div class="grid gap-5 lg:grid-cols-3 max-w-2xl mx-auto">

            <a href="{{ $localeUrls['en'] }}"
               data-page-transition="false"
               style="cursor: pointer; display: block; padding: 2rem 2.5rem; background: #fff; border: 2px solid rgba(196,154,122,0.15); text-align: center; text-decoration: none; transition: all 0.3s;">
                <span style="display: block; color: #C49A7A; font-size: 10px; letter-spacing: 0.5em; text-transform: uppercase; font-weight: 600; margin-bottom: 1rem; font-family: Inter, sans-serif;">English</span>
                <span style="display: block; font-family: 'Cormorant Garamond', serif; font-size: 1.35rem; color: #3D2E2A; font-weight: 300; margin-bottom: 1.25rem;">Continue in English</span>
                <span style="display: block; color: #C49A7A; font-size: 14px;">&rarr;</span>
            </a>

            <a href="{{ $localeUrls['ar'] }}"
               data-page-transition="false"
               dir="rtl"
               style="cursor: pointer; display: block; padding: 2rem 2.5rem; background: #fff; border: 2px solid rgba(196,154,122,0.15); text-align: center; text-decoration: none; transition: all 0.3s; font-family: 'Amiri', serif;">
                <span style="display: block; color: #C49A7A; font-size: 12px; font-weight: 500; margin-bottom: 1rem; font-family: 'Cairo', Inter, sans-serif;">العربية</span>
                <span style="display: block; font-size: 1.35rem; color: #3D2E2A; margin-bottom: 1.25rem;">المتابعة بالعربية</span>
                <span style="display: block; color: #C49A7A; font-size: 14px;">&larr;</span>
            </a>

            <a href="{{ $localeUrls['it'] }}"
               data-page-transition="false"
               style="cursor: pointer; display: block; padding: 2rem 2.5rem; background: #fff; border: 2px solid rgba(196,154,122,0.15); text-align: center; text-decoration: none; transition: all 0.3s;">
                <span style="display: block; color: #C49A7A; font-size: 10px; letter-spacing: 0.5em; text-transform: uppercase; font-weight: 600; font-style: italic; margin-bottom: 1rem; font-family: Inter, sans-serif;">Italiano</span>
                <span style="display: block; font-family: 'Cormorant Garamond', serif; font-size: 1.35rem; color: #3D2E2A; font-weight: 300; font-style: italic; margin-bottom: 1.25rem;">Continua in italiano</span>
                <span style="display: block; color: #C49A7A; font-size: 14px;">&rarr;</span>
            </a>

        </div>

        {{-- Bottom tagline --}}
        <p class="text-center text-delos-muted text-[10px] tracking-[0.4em] uppercase mt-12" style="font-family: 'Inter', sans-serif;">
            Italian Luxury Solutions
        </p>

    </div>
</div>
