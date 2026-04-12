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
                style="cursor: pointer;"
                class="flex items-center gap-1.5 h-9 px-4 bg-delos-gold/15 border border-delos-gold/50 rounded-full hover:bg-delos-gold/25 hover:border-delos-gold transition-all duration-300 group backdrop-blur-sm">
            {{-- Globe icon --}}
            <svg class="w-4 h-4 text-delos-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.6 9h16.8M3.6 15h16.8M11.5 3a17 17 0 000 18M12.5 3a17 17 0 010 18"/>
            </svg>
            <span class="text-[11px] tracking-[0.18em] uppercase font-semibold text-delos-gold group-hover:text-delos-cream transition-colors duration-300"
                  style="font-family: 'Inter', sans-serif;">{{ $currentCode }}</span>
            <svg class="w-3 h-3 text-delos-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div data-lang-dropdown-menu
             class="lang-dropdown-menu absolute right-0 top-full mt-2 min-w-[190px] rounded-lg overflow-hidden shadow-2xl transition-all duration-200"
             style="opacity:0; visibility:hidden; transform:translateY(-4px); z-index:9999; background:rgba(44,34,32,0.92); -webkit-backdrop-filter:blur(20px); backdrop-filter:blur(20px); border:1px solid rgba(196,154,122,0.25);"
             role="menu">
            @foreach ($localeChoices as $code => $labels)
                @if ($code !== $locale)
                    <a href="{{ $localeUrls[$code] }}"
                       data-language-switch="{{ $code }}"
                       data-page-transition="false"
                       style="cursor:pointer; font-family:'Inter',sans-serif; display:block; padding:0.875rem 1.25rem; font-size:12px; letter-spacing:0.18em; text-transform:uppercase; color:rgba(248,244,239,0.8); text-decoration:none; border-bottom:1px solid rgba(255,255,255,0.05); transition:all 0.2s;"
                       onmouseover="this.style.background='rgba(196,154,122,0.15)';this.style.color='#C49A7A'"
                       onmouseout="this.style.background='transparent';this.style.color='rgba(248,244,239,0.8)'"
                       role="menuitem">
                        <span style="font-weight:600;">{{ $labels['code'] }}</span>
                        <span style="color:rgba(248,244,239,0.45); text-transform:none; letter-spacing:normal; margin-left:0.5rem;">{{ $labels['native'] }}</span>
                    </a>
                @endif
            @endforeach
        </div>
    </div>

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
