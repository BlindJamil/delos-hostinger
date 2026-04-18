<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $dir }}" class="locale-{{ $locale }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', pcontent('seo.default.title'))</title>
    <meta name="description" content="@yield('description', pcontent('seo.default.description'))">

    {{-- hreflang + canonical --}}
    <link rel="canonical" href="{{ url()->current() }}">
    @foreach ($supportedLocales as $alt)
        <link rel="alternate" hreflang="{{ $alt === 'ar' ? 'ar-IQ' : $alt }}" href="{{ url($localeUrls[$alt]) }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ url($localeUrls['en']) }}">

    {{-- Open Graph locale --}}
    @php
        $ogLocales = ['en' => 'en_US', 'ar' => 'ar_IQ', 'it' => 'it_IT'];
    @endphp
    <meta property="og:locale" content="{{ $ogLocales[$locale] }}">
    @foreach (array_diff($supportedLocales, [$locale]) as $alt)
        <meta property="og:locale:alternate" content="{{ $ogLocales[$alt] }}">
    @endforeach

    @if ($isAdmin ?? false)
        {{-- Admin-authenticated responses must never be indexed or leak admin
             URLs via Referer. The PreventAdminCaching middleware sets the
             response headers; these meta tags provide in-HTML backup. --}}
        <meta name="robots" content="noindex, nofollow">
        <meta name="referrer" content="same-origin">
    @endif

    <script>
        document.documentElement.classList.add('js');
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            document.documentElement.classList.add('reduced-motion');
        }

        // Always start the page at the top on reload. Scroll-triggered animations
        // initialise from scroll position 0 — restoring a mid-page position leaves
        // sections stuck at opacity:0 because their triggers never fire. Anchor
        // links (e.g. /en/#brands-section) still work because we only force-reset
        // when there is no hash in the URL.
        if ('scrollRestoration' in history) {
            history.scrollRestoration = 'manual';
        }
        if (!window.location.hash) {
            // Belt-and-suspenders: also reset scroll once the DOM is ready, in
            // case the browser restored before scrollRestoration took effect.
            document.addEventListener('DOMContentLoaded', function () {
                window.scrollTo(0, 0);
            });
        }
    </script>

    {{-- Latin Google Fonts (Cormorant Garamond + Inter) — always loaded --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400;1,500&family=Inter:wght@300;400;500;600&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400;1,500&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet"></noscript>

    {{-- Arabic fonts (Amiri + Cairo) — only when locale is ar --}}
    @if ($locale === 'ar')
        <link rel="preload" href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Cairo:wght@300;400;500;600;700&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
        <noscript><link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet"></noscript>
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Inline overrides — guarantees these take effect even if OPcache
         serves a stale Vite CSS bundle, since this is in the HTML itself --}}
    <style>
        #site-header {
            -webkit-backdrop-filter: blur(14px) !important;
            backdrop-filter: blur(14px) !important;
            background: rgba(44, 34, 32, 0.5) !important;
        }
        #site-header.header-scrolled {
            -webkit-backdrop-filter: blur(24px) saturate(1.2) !important;
            backdrop-filter: blur(24px) saturate(1.2) !important;
            background: rgba(44, 34, 32, 0.82) !important;
        }
    </style>
</head>
<body data-page="{{ request()->route()?->getName() ?? 'unknown' }}" class="bg-delos-cream text-delos-text antialiased">

    {{-- ============================================================
         FIXED HEADER (Marquee + Nav combined)
    ============================================================ --}}
    <header id="site-header" class="fixed top-0 left-0 right-0 z-50">

        {{-- Marquee Banner --}}
        <div id="marquee-bar" class="bg-delos-dark-2 overflow-hidden py-2.5 border-b border-white/5 transition-all duration-500">
            <div class="marquee-track flex gap-12 whitespace-nowrap">
                @for($j = 0; $j < 3; $j++)
                    <span class="text-overline text-delos-gold/60 font-medium">{{ pcontent('common.marquee.tagline') }}
                        <span class="italian-flag">
                            <span class="flag-green"></span>
                            <span class="flag-white"></span>
                            <span class="flag-red"></span>
                        </span>
                    </span>
                    <span class="text-delos-gold/25 text-[8px]">◆</span>
                    <span class="text-overline text-delos-gold/60 font-medium">{{ pcontent('common.marquee.showrooms') }}</span>
                    <span class="text-delos-gold/25 text-[8px]">◆</span>
                    <span class="text-overline text-delos-gold/60 font-medium">{{ pcontent('common.marquee.lion') }}</span>
                    <span class="text-delos-gold/25 text-[8px]">◆</span>
                    <span class="text-overline text-delos-gold/60 font-medium">{{ pcontent('common.marquee.turnkey') }}</span>
                    <span class="text-delos-gold/25 text-[8px]">◆</span>
                    <span class="text-overline text-delos-gold/60 font-medium">{{ pcontent('common.marquee.cities') }}</span>
                    <span class="text-delos-gold/25 text-[8px]">◆</span>
                @endfor
            </div>
        </div>

        {{-- Navigation --}}
        <nav id="navbar" class="transition-all duration-500">
            <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
                <div class="flex items-center justify-between h-18 lg:h-20">

                    {{-- Logo --}}
                    <a href="{{ lroute('home') }}" class="flex items-center gap-3 group">
                        <div class="w-9 h-9 lg:w-10 lg:h-10 overflow-hidden rounded-full flex-shrink-0 border border-delos-gold/20">
                            <img src="{{ asset('images/delos-logo.jpg') }}" alt="Delos International" class="w-full h-full object-cover">
                        </div>
                        <div class="flex flex-col">
                            <span class="font-serif text-lg lg:text-xl font-semibold tracking-[0.2em] nav-logo-text leading-none transition-colors duration-300">{{ pcontent('common.brand.name_primary') }}</span>
                            <span class="text-overline-sm nav-logo-sub transition-colors duration-300">{{ pcontent('common.brand.name_secondary') }}</span>
                        </div>
                    </a>

                    {{-- Desktop Nav Links --}}
                    <div class="hidden lg:flex items-center gap-7">
                        @php
                            $links = [
                                ['label' => pcontent('common.nav.home'),     'route' => 'home'],
                                ['label' => pcontent('common.nav.brands'),   'route' => 'brands'],
                                ['label' => pcontent('common.nav.services'), 'route' => 'services'],
                                ['label' => pcontent('common.nav.projects'), 'route' => 'projects'],
                                ['label' => pcontent('common.nav.about'),    'route' => 'about'],
                                ['label' => pcontent('common.nav.branches'), 'route' => 'branches'],
                                ['label' => pcontent('common.nav.contact'),  'route' => 'contact'],
                            ];
                        @endphp
                        @foreach($links as $link)
                            <a href="{{ lroute($link['route']) }}"
                               class="nav-link font-sans text-[11px] tracking-[0.18em] uppercase font-medium transition-all duration-300 relative group {{ request()->routeIs('l.' . $link['route']) ? 'text-delos-gold' : '' }}">
                                {{ $link['label'] }}
                                @if(request()->routeIs('l.' . $link['route']))
                                    <span class="nav-underline absolute -bottom-1 left-0 h-px bg-delos-gold" style="transform: scaleX(1); transform-origin: left;"></span>
                                @else
                                    <span class="nav-underline absolute -bottom-1 left-0 h-px bg-delos-gold"></span>
                                @endif
                            </a>
                        @endforeach
                    </div>

                    {{-- Social Media --}}
                    <div class="hidden lg:flex items-center gap-3">
                        <a href="{{ $settingInstagram ?: '#' }}" target="_blank" rel="noopener"
                           class="nav-social w-9 h-9 flex items-center justify-center border rounded-full hover:bg-delos-gold hover:border-delos-gold group transition-all duration-300" aria-label="Instagram">
                            <svg class="w-4 h-4 nav-social-icon group-hover:text-delos-dark transition-colors duration-300" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                        <a href="{{ $settingFacebook ?: '#' }}" target="_blank" rel="noopener"
                           class="nav-social w-9 h-9 flex items-center justify-center border rounded-full hover:bg-delos-gold hover:border-delos-gold group transition-all duration-300" aria-label="Facebook">
                            <svg class="w-4 h-4 nav-social-icon group-hover:text-delos-dark transition-colors duration-300" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="{{ $settingPinterest ?: '#' }}" target="_blank" rel="noopener"
                           class="nav-social w-9 h-9 flex items-center justify-center border rounded-full hover:bg-delos-gold hover:border-delos-gold group transition-all duration-300" aria-label="Pinterest">
                            <svg class="w-4 h-4 nav-social-icon group-hover:text-delos-dark transition-colors duration-300" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.372 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 01.083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.631-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12 0-6.628-5.373-12-12-12z"/></svg>
                        </a>
                        <a href="{{ $settingTiktok ?: '#' }}" target="_blank" rel="noopener"
                           class="nav-social w-9 h-9 flex items-center justify-center border rounded-full hover:bg-delos-gold hover:border-delos-gold group transition-all duration-300" aria-label="TikTok">
                            <svg class="w-4 h-4 nav-social-icon group-hover:text-delos-dark transition-colors duration-300" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                        </a>
                    </div>

                    {{-- Mobile Hamburger --}}
                    <button id="mobile-menu-btn" class="lg:hidden flex flex-col gap-1.5 p-2"
                            aria-label="{{ pcontent('common.nav.menu_toggle') }}"
                            aria-expanded="false"
                            aria-controls="mobile-menu">
                        <span class="hamburger-line block w-6 h-px transition-all duration-300"></span>
                        <span class="hamburger-line block w-6 h-px transition-all duration-300"></span>
                        <span class="hamburger-line block w-4 h-px transition-all duration-300"></span>
                    </button>

                </div>
            </div>

            {{-- Mobile Menu --}}
            <div id="mobile-menu" class="lg:hidden bg-delos-dark border-t border-white/10">
                <div class="px-6 py-8 flex flex-col gap-6">
                    @foreach($links as $link)
                        <a href="{{ lroute($link['route']) }}"
                           class="font-sans text-delos-cream text-[13px] tracking-[0.2em] uppercase font-medium hover:text-delos-gold transition-colors duration-300">
                            {{ $link['label'] }}
                        </a>
                    @endforeach

                    <div class="flex items-center gap-3 mt-4 pt-4 border-t border-white/10">
                        <a href="{{ $settingInstagram ?: '#' }}" target="_blank" rel="noopener"
                           class="w-10 h-10 flex items-center justify-center border border-delos-gold/30 rounded-full hover:bg-delos-gold group transition-all duration-300" aria-label="Instagram">
                            <svg class="w-4.5 h-4.5 text-delos-gold group-hover:text-delos-dark transition-colors duration-300" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                        <a href="{{ $settingFacebook ?: '#' }}" target="_blank" rel="noopener"
                           class="w-10 h-10 flex items-center justify-center border border-delos-gold/30 rounded-full hover:bg-delos-gold group transition-all duration-300" aria-label="Facebook">
                            <svg class="w-4.5 h-4.5 text-delos-gold group-hover:text-delos-dark transition-colors duration-300" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="{{ $settingPinterest ?: '#' }}" target="_blank" rel="noopener"
                           class="w-10 h-10 flex items-center justify-center border border-delos-gold/30 rounded-full hover:bg-delos-gold group transition-all duration-300" aria-label="Pinterest">
                            <svg class="w-4.5 h-4.5 text-delos-gold group-hover:text-delos-dark transition-colors duration-300" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.372 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 01.083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.631-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12 0-6.628-5.373-12-12-12z"/></svg>
                        </a>
                        <a href="{{ $settingTiktok ?: '#' }}" target="_blank" rel="noopener"
                           class="w-10 h-10 flex items-center justify-center border border-delos-gold/30 rounded-full hover:bg-delos-gold group transition-all duration-300" aria-label="TikTok">
                            <svg class="w-4.5 h-4.5 text-delos-gold group-hover:text-delos-dark transition-colors duration-300" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </nav>

    </header>

    {{-- ============================================================
         PAGE CONTENT
    ============================================================ --}}
    <main>
        @yield('content')
    </main>

    {{-- ============================================================
         FOOTER
    ============================================================ --}}
    <footer class="bg-delos-dark text-delos-cream">

        {{-- Top Footer --}}
        <div class="max-w-[1400px] mx-auto px-6 lg:px-12 pt-20 pb-12">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-12 lg:gap-8" data-motion-group="footer">

                {{-- Brand Column --}}
                <div data-motion="fade-up" class="lg:col-span-1">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-9 h-9 overflow-hidden rounded-full flex-shrink-0 border border-delos-gold/20">
                            <img src="{{ asset('images/delos-logo.jpg') }}" alt="Delos International" class="w-full h-full object-cover">
                        </div>
                        <div class="flex flex-col">
                            <span class="font-serif text-lg font-semibold tracking-[0.2em] text-delos-cream leading-none">{{ pcontent('common.brand.name_primary') }}</span>
                            <span class="text-overline-sm text-delos-gold">{{ pcontent('common.brand.name_secondary') }}</span>
                        </div>
                    </div>
                    <p class="font-sans text-delos-muted text-sm leading-relaxed mb-6">
                        {{ pcontent('common.footer.tagline_long') }}
                    </p>
                    <p class="text-overline text-delos-gold text-[10px]">
                        {{ pcontent('common.footer.lion_motto') }}
                    </p>
                    <div class="flex items-center gap-4 mt-5">
                        <a href="{{ $settingInstagram ?: '#' }}" target="_blank" rel="noopener" class="text-delos-muted hover:text-delos-gold transition-colors duration-300" aria-label="Instagram">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                        <a href="{{ $settingFacebook ?: '#' }}" target="_blank" rel="noopener" class="text-delos-muted hover:text-delos-gold transition-colors duration-300" aria-label="Facebook">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="{{ $settingPinterest ?: '#' }}" target="_blank" rel="noopener" class="text-delos-muted hover:text-delos-gold transition-colors duration-300" aria-label="Pinterest">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.372 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 01.083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.631-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12 0-6.628-5.373-12-12-12z"/></svg>
                        </a>
                        <a href="{{ $settingTiktok ?: '#' }}" target="_blank" rel="noopener" class="text-delos-muted hover:text-delos-gold transition-colors duration-300" aria-label="TikTok">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
                        </a>
                    </div>
                </div>

                {{-- Navigation Column --}}
                <div data-motion="fade-up">
                    <h4 class="text-overline text-delos-cream text-[10px] mb-6">{{ pcontent('common.footer.nav_heading') }}</h4>
                    <ul class="space-y-3">
                        @foreach($links as $link)
                        <li>
                            <a href="{{ lroute($link['route']) }}" class="font-sans text-delos-muted text-sm hover:text-delos-gold transition-colors duration-300">
                                {{ $link['label'] }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Branches Column — DB-driven from the admin /admin/branches editor.
                     Each active branch row renders city + address + phone (phone
                     only when set, so we don't show an empty placeholder line). --}}
                <div data-motion="fade-up">
                    <h4 class="text-overline text-delos-cream text-[10px] mb-6">{{ pcontent('common.footer.showrooms_heading') }}</h4>
                    <ul class="space-y-4">
                        @forelse(($footerBranches ?? collect()) as $footerBranch)
                            <li>
                                <p class="font-sans text-delos-gold text-[10px] tracking-[0.2em] uppercase mb-1">{{ $footerBranch->localized('name') }}</p>
                                @if($footerBranch->localized('address'))
                                    <p class="font-sans text-delos-muted text-xs leading-relaxed">{{ $footerBranch->localized('address') }}</p>
                                @endif
                                @if($footerBranch->phone)
                                    <p class="font-sans text-delos-muted text-xs">{{ $footerBranch->phone }}</p>
                                @endif
                            </li>
                        @empty
                            {{-- Legacy fallback — lang-file entries used before the
                                 branches table was populated. Keeps the footer
                                 meaningful on a fresh install. --}}
                            <li>
                                <p class="font-sans text-delos-gold text-[10px] tracking-[0.2em] uppercase mb-1">{{ pcontent('common.footer.showroom_erbil_soran.title') }}</p>
                                <p class="font-sans text-delos-muted text-xs leading-relaxed">{{ pcontent('common.footer.showroom_erbil_soran.address') }}</p>
                                <p class="font-sans text-delos-muted text-xs">{{ pcontent('common.footer.showroom_erbil_soran.phone') }}</p>
                            </li>
                            <li>
                                <p class="font-sans text-delos-gold text-[10px] tracking-[0.2em] uppercase mb-1">{{ pcontent('common.footer.showroom_erbil_gulan.title') }}</p>
                                <p class="font-sans text-delos-muted text-xs leading-relaxed">{{ pcontent('common.footer.showroom_erbil_gulan.address') }}</p>
                                <p class="font-sans text-delos-muted text-xs">{{ pcontent('common.footer.showroom_erbil_gulan.phone') }}</p>
                            </li>
                            <li>
                                <p class="font-sans text-delos-gold text-[10px] tracking-[0.2em] uppercase mb-1">{{ pcontent('common.footer.other_cities') }}</p>
                            </li>
                        @endforelse
                    </ul>
                </div>

                {{-- Italian Partners Column --}}
                <div data-motion="fade-up">
                    <h4 class="text-overline text-delos-cream text-[10px] mb-6">{{ pcontent('common.footer.partners_heading') }}</h4>
                    <ul class="space-y-3">
                        @foreach(['LUBE','Frigerio','Vittoria Frigerio','CANTORI','SKEMA'] as $brand)
                        <li class="font-sans text-delos-muted text-sm hover:text-delos-gold transition-colors duration-300 cursor-default">
                            {{ $brand }}
                        </li>
                        @endforeach
                    </ul>
                </div>

            </div>
        </div>

        {{-- Bottom Footer --}}
        <div class="border-t border-white/8">
            <div class="max-w-[1400px] mx-auto px-6 lg:px-12 py-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="font-sans text-delos-muted/60 text-[11px] tracking-wide">
                    &copy; {{ date('Y') }} {{ pcontent('common.footer.copyright') }}
                </p>

                {{-- Footer language switcher --}}
                <x-language-switcher variant="footer" />

                <p class="font-sans text-delos-muted/60 text-[11px] tracking-[0.2em] uppercase">
                    {{ pcontent('common.footer.bottom_tagline') }}
                </p>
            </div>
        </div>

    </footer>

    {{-- Page Transition Overlay --}}
    <div data-page-transition class="page-transition-overlay"></div>

    {{-- First-visit language picker modal --}}
    {{-- First-visit language picker removed — locale is resolved from
         Accept-Language on the root redirect, and the floating globe
         switcher below handles every subsequent change. --}}

    {{-- Floating admin bar — only for logged-in admins. Fixed at bottom-left. --}}
    @if ($isAdmin ?? false)
        <x-admin-bar />
    @endif

</body>
</html>
