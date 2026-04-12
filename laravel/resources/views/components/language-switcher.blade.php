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
    {{-- Desktop: simple inline links — no JS dropdown needed --}}
    <div class="flex items-center gap-1">
        @foreach ($localeChoices as $code => $labels)
            <a href="{{ $localeUrls[$code] }}"
               data-language-switch="{{ $code }}"
               data-page-transition="false"
               style="cursor: pointer;"
               class="px-2 py-1 text-[11px] tracking-[0.18em] uppercase font-medium transition-colors duration-300
                      {{ $code === $locale
                         ? 'text-delos-gold'
                         : 'nav-link-text hover:text-delos-gold' }}"
               style="font-family: 'Inter', sans-serif;">
                {{ $labels['code'] }}
            </a>
            @if (!$loop->last)
                <span class="nav-link-text text-[8px] opacity-30 select-none">&middot;</span>
            @endif
        @endforeach
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
