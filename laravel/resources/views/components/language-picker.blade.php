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

        {{-- Language cards --}}
        <div class="grid gap-5 lg:grid-cols-3 max-w-2xl mx-auto">

            {{-- English --}}
            <a href="{{ $localeUrls['en'] }}"
               data-locale="en"
               data-language-switch="en"
               data-page-transition="false"
               onclick="(function(l){try{var d=365*24*60*60;var s=location.protocol==='https:'?';Secure':'';document.cookie='delos_locale='+l+';Max-Age='+d+';Path=/;SameSite=Lax'+s;document.cookie='delos_locale_seen=1;Max-Age='+d+';Path=/;SameSite=Lax'+s;localStorage.setItem('delos_locale',l)}catch(e){}})('en')"
               style="cursor: pointer;"
               class="group block p-8 lg:p-10 bg-white border-2 border-delos-gold/15 text-center
                      hover:border-delos-gold hover:shadow-xl hover:shadow-delos-gold/10
                      focus:border-delos-gold focus:shadow-xl focus:outline-none
                      active:scale-[0.98] transition-all duration-300">
                <span class="block text-delos-gold text-[10px] tracking-[0.5em] uppercase font-semibold mb-4"
                      style="font-family: 'Inter', sans-serif;">
                    {{ $picker['label_en'] }}
                </span>
                <span class="block font-serif text-xl lg:text-2xl text-delos-dark font-light mb-5">
                    {{ $picker['continue_en'] }}
                </span>
                <span class="block text-delos-gold text-sm opacity-50 group-hover:opacity-100 transition-opacity duration-300">
                    &rarr;
                </span>
            </a>

            {{-- Arabic --}}
            <a href="{{ $localeUrls['ar'] }}"
               data-locale="ar"
               data-language-switch="ar"
               data-page-transition="false"
               onclick="(function(l){try{var d=365*24*60*60;var s=location.protocol==='https:'?';Secure':'';document.cookie='delos_locale='+l+';Max-Age='+d+';Path=/;SameSite=Lax'+s;document.cookie='delos_locale_seen=1;Max-Age='+d+';Path=/;SameSite=Lax'+s;localStorage.setItem('delos_locale',l)}catch(e){}})('ar')"
               dir="rtl"
               style="cursor: pointer; font-family: 'Amiri', 'Cairo', serif;"
               class="group block p-8 lg:p-10 bg-white border-2 border-delos-gold/15 text-center
                      hover:border-delos-gold hover:shadow-xl hover:shadow-delos-gold/10
                      focus:border-delos-gold focus:shadow-xl focus:outline-none
                      active:scale-[0.98] transition-all duration-300">
                <span class="block text-delos-gold text-xs font-medium mb-4"
                      style="font-family: 'Cairo', 'Inter', sans-serif;">
                    {{ $picker['label_ar'] }}
                </span>
                <span class="block text-xl lg:text-2xl text-delos-dark font-normal mb-5">
                    {{ $picker['continue_ar'] }}
                </span>
                <span class="block text-delos-gold text-sm opacity-50 group-hover:opacity-100 transition-opacity duration-300">
                    &larr;
                </span>
            </a>

            {{-- Italian --}}
            <a href="{{ $localeUrls['it'] }}"
               data-locale="it"
               data-language-switch="it"
               data-page-transition="false"
               onclick="(function(l){try{var d=365*24*60*60;var s=location.protocol==='https:'?';Secure':'';document.cookie='delos_locale='+l+';Max-Age='+d+';Path=/;SameSite=Lax'+s;document.cookie='delos_locale_seen=1;Max-Age='+d+';Path=/;SameSite=Lax'+s;localStorage.setItem('delos_locale',l)}catch(e){}})('it')"
               style="cursor: pointer;"
               class="group block p-8 lg:p-10 bg-white border-2 border-delos-gold/15 text-center
                      hover:border-delos-gold hover:shadow-xl hover:shadow-delos-gold/10
                      focus:border-delos-gold focus:shadow-xl focus:outline-none
                      active:scale-[0.98] transition-all duration-300">
                <span class="block text-delos-gold text-[10px] tracking-[0.5em] uppercase font-semibold italic mb-4"
                      style="font-family: 'Inter', sans-serif;">
                    {{ $picker['label_it'] }}
                </span>
                <span class="block font-serif text-xl lg:text-2xl text-delos-dark italic font-light mb-5">
                    {{ $picker['continue_it'] }}
                </span>
                <span class="block text-delos-gold text-sm opacity-50 group-hover:opacity-100 transition-opacity duration-300">
                    &rarr;
                </span>
            </a>

        </div>

        {{-- Bottom tagline --}}
        <p class="text-center text-delos-muted text-[10px] tracking-[0.4em] uppercase mt-12" style="font-family: 'Inter', sans-serif;">
            Italian Luxury Solutions
        </p>

    </div>
</div>
