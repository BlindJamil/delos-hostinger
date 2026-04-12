@props(['variant' => 'desktop'])

@php
    // Shared locale labels (English label, native label, URL)
    $localeChoices = [
        'en' => ['code' => 'EN', 'native' => 'English'],
        'ar' => ['code' => 'AR', 'native' => 'العربية'],
        'it' => ['code' => 'IT', 'native' => 'Italiano'],
    ];
    $currentCode = $localeChoices[$locale]['code'] ?? 'EN';
    $switchAria = __('common.language_switcher.aria_label');
@endphp

@if ($variant === 'desktop')
    {{-- Desktop dropdown in nav social area --}}
    <div class="relative">
        <button type="button"
                data-lang-dropdown-toggle
                aria-expanded="false"
                aria-label="{{ $switchAria }}"
                class="flex items-center gap-1.5 h-9 px-3 border border-delos-gold/30 rounded-full hover:border-delos-gold transition-colors duration-300 group">
            {{-- Globe icon --}}
            <svg class="w-4 h-4 text-delos-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.6 9h16.8M3.6 15h16.8M11.5 3a17 17 0 000 18M12.5 3a17 17 0 010 18"/>
            </svg>
            <span class="text-[11px] tracking-[0.18em] uppercase font-medium text-delos-cream/90 group-hover:text-delos-gold transition-colors duration-300"
                  style="font-family: 'Inter', sans-serif;">{{ $currentCode }}</span>
            <svg class="w-3 h-3 text-delos-gold/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div data-lang-dropdown-menu
             class="lang-dropdown-menu absolute end-0 mt-2 min-w-[160px] bg-delos-dark-2 border border-delos-gold/20 opacity-0 invisible transition-all duration-300 -translate-y-2 z-50"
             role="menu">
            @foreach ($localeChoices as $code => $labels)
                @if ($code !== $locale)
                    <a href="{{ $localeUrls[$code] }}"
                       data-language-switch="{{ $code }}"
                       data-page-transition="false"
                       class="block px-4 py-3 text-xs tracking-[0.18em] uppercase text-delos-cream/80 hover:bg-delos-gold/10 hover:text-delos-gold transition-colors duration-200"
                       style="font-family: 'Inter', sans-serif;"
                       role="menuitem">
                        <span class="font-medium">{{ $labels['code'] }}</span>
                        <span class="text-delos-muted normal-case tracking-normal ms-2">{{ $labels['native'] }}</span>
                    </a>
                @endif
            @endforeach
        </div>
    </div>

    <style>
        .lang-dropdown-menu.is-open {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
    </style>

@elseif ($variant === 'mobile')
    {{-- Mobile drawer variant — three text links stacked --}}
    <div class="flex flex-col gap-3 mt-6 pt-4 border-t border-white/10">
        <p class="text-delos-gold text-[9px] tracking-[0.4em] uppercase font-medium"
           style="font-family: 'Inter', sans-serif;">{{ __('common.language_switcher.switch_to') }}</p>
        <div class="flex items-center gap-3 flex-wrap">
            @foreach ($localeChoices as $code => $labels)
                <a href="{{ $localeUrls[$code] }}"
                   data-language-switch="{{ $code }}"
                   data-page-transition="false"
                   class="inline-flex items-center gap-1.5 text-[11px] tracking-[0.2em] uppercase font-medium transition-colors duration-300 {{ $code === $locale ? 'text-delos-gold' : 'text-delos-cream/70 hover:text-delos-gold' }}"
                   style="font-family: 'Inter', sans-serif;">
                    <span>{{ $labels['code'] }}</span>
                    <span class="normal-case tracking-normal text-delos-muted/70 text-[10px]">{{ $labels['native'] }}</span>
                </a>
                @if (!$loop->last)
                    <span class="text-delos-gold/30 text-[8px]">◆</span>
                @endif
            @endforeach
        </div>
    </div>

@elseif ($variant === 'footer')
    {{-- Footer variant — inline row --}}
    <div class="flex items-center gap-3 flex-wrap">
        @foreach ($localeChoices as $code => $labels)
            <a href="{{ $localeUrls[$code] }}"
               data-language-switch="{{ $code }}"
               data-page-transition="false"
               class="text-[11px] tracking-[0.15em] uppercase font-medium transition-colors duration-300 {{ $code === $locale ? 'text-delos-gold' : 'text-delos-muted hover:text-delos-gold' }}"
               style="font-family: 'Inter', sans-serif;">
                {{ $labels['code'] }}
            </a>
            @if (!$loop->last)
                <span class="text-delos-muted/40 text-[9px]">·</span>
            @endif
        @endforeach
    </div>
@endif
