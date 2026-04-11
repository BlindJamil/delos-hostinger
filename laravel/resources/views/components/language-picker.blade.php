{{--
    First-visit language picker modal.
    Rendered by layouts/app.blade.php only when $showLangPicker is true
    (i.e. the user does not yet have a delos_locale_seen cookie).

    Design decisions locked in plan:
    - No close button, no ESC dismissal — user MUST choose to proceed.
    - Full-screen overlay, z-index 10000 (above page transition at 9999).
    - Trilingual heading so every visitor recognizes their language.
    - Three language cards side-by-side on desktop, stacked on mobile.
    - Works without JS — anchors navigate directly. JS enhances with
      focus trap, body scroll lock, and cookie persistence before nav.
--}}

@php
    $picker = __('common.language_picker');
@endphp

<div id="lang-picker"
     role="dialog"
     aria-modal="true"
     aria-labelledby="lp-title"
     aria-describedby="lp-sub"
     aria-label="{{ $picker['aria_label'] }}"
     class="fixed inset-0 z-[10000] flex items-center justify-center p-6 bg-delos-dark-2/95 backdrop-blur-md"
     style="-webkit-backdrop-filter: blur(12px);">

    <div class="relative w-full max-w-2xl lg:max-w-4xl bg-delos-cream border border-delos-gold/30 shadow-2xl"
         style="animation: lp-fade-in 0.6s cubic-bezier(0.16, 1, 0.3, 1);">

        {{-- Delos logo accent --}}
        <div class="flex flex-col items-center pt-10 lg:pt-12">
            <div class="w-14 h-14 lg:w-16 lg:h-16 overflow-hidden rounded-full border border-delos-gold/30 mb-6">
                <img src="{{ asset('images/delos-logo.jpg') }}" alt="Delos International" class="w-full h-full object-cover">
            </div>
            <div class="w-16 h-px bg-delos-gold mb-6"></div>
        </div>

        {{-- Trilingual heading --}}
        <div class="text-center px-6 lg:px-12 mb-8 lg:mb-10 space-y-3">
            <p id="lp-title"
               class="font-serif text-2xl lg:text-3xl text-delos-dark font-light leading-tight">
                {{ $picker['heading_en'] }}
            </p>
            <p dir="rtl"
               class="font-serif text-2xl lg:text-3xl text-delos-dark font-light leading-tight"
               style="font-family: 'Amiri', 'Cormorant Garamond', serif;">
                {{ $picker['heading_ar'] }}
            </p>
            <p class="font-serif text-2xl lg:text-3xl text-delos-dark italic font-light leading-tight">
                {{ $picker['heading_it'] }}
            </p>
        </div>

        {{-- Language cards --}}
        <div id="lp-sub" class="sr-only">{{ $picker['sub_en'] }}</div>

        <div class="grid gap-4 lg:grid-cols-3 px-6 lg:px-12 pb-10 lg:pb-12">

            {{-- English --}}
            <a href="{{ $localeUrls['en'] }}"
               data-locale="en"
               data-language-switch="en"
               class="lang-picker-card group flex flex-col items-center gap-3 p-6 lg:p-8 border border-delos-gold/20 hover:border-delos-gold hover:bg-delos-ivory focus:outline-none focus:border-delos-gold focus:bg-delos-ivory transition-all duration-300">
                <span class="text-delos-gold text-[10px] tracking-[0.4em] uppercase font-medium"
                      style="font-family: 'Inter', sans-serif;">
                    {{ $picker['label_en'] }}
                </span>
                <span class="font-serif text-xl text-delos-dark font-light">
                    {{ $picker['continue_en'] }}
                </span>
                <span class="text-delos-gold text-lg opacity-0 group-hover:opacity-100 group-focus:opacity-100 transition-opacity duration-300">
                    &rarr;
                </span>
            </a>

            {{-- Arabic --}}
            <a href="{{ $localeUrls['ar'] }}"
               data-locale="ar"
               data-language-switch="ar"
               dir="rtl"
               class="lang-picker-card group flex flex-col items-center gap-3 p-6 lg:p-8 border border-delos-gold/20 hover:border-delos-gold hover:bg-delos-ivory focus:outline-none focus:border-delos-gold focus:bg-delos-ivory transition-all duration-300"
               style="font-family: 'Amiri', 'Cairo', 'Cormorant Garamond', serif;">
                <span class="text-delos-gold text-xs font-medium"
                      style="font-family: 'Cairo', 'Inter', sans-serif;">
                    {{ $picker['label_ar'] }}
                </span>
                <span class="text-xl text-delos-dark font-normal">
                    {{ $picker['continue_ar'] }}
                </span>
                <span class="text-delos-gold text-lg opacity-0 group-hover:opacity-100 group-focus:opacity-100 transition-opacity duration-300">
                    &larr;
                </span>
            </a>

            {{-- Italian --}}
            <a href="{{ $localeUrls['it'] }}"
               data-locale="it"
               data-language-switch="it"
               class="lang-picker-card group flex flex-col items-center gap-3 p-6 lg:p-8 border border-delos-gold/20 hover:border-delos-gold hover:bg-delos-ivory focus:outline-none focus:border-delos-gold focus:bg-delos-ivory transition-all duration-300">
                <span class="text-delos-gold text-[10px] tracking-[0.4em] uppercase font-medium italic"
                      style="font-family: 'Inter', sans-serif;">
                    {{ $picker['label_it'] }}
                </span>
                <span class="font-serif text-xl text-delos-dark italic font-light">
                    {{ $picker['continue_it'] }}
                </span>
                <span class="text-delos-gold text-lg opacity-0 group-hover:opacity-100 group-focus:opacity-100 transition-opacity duration-300">
                    &rarr;
                </span>
            </a>

        </div>

    </div>
</div>

<style>
    @keyframes lp-fade-in {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .reduced-motion #lang-picker > div {
        animation: none !important;
    }
</style>
