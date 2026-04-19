<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') · Delos International</title>
    <meta name="robots" content="noindex, nofollow">

    <link rel="icon" type="image/png" href="{{ asset('images/delos-logo.jpg') }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Cormorant+Garamond:wght@400;500;600&display=swap" rel="stylesheet">

    {{-- Tailwind via CDN — reliable delivery, independent of Vite cache --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        serif: ['Cormorant Garamond', 'Georgia', 'serif'],
                    },
                    colors: {
                        delos: {
                            gold: '#C49A7A',
                            'gold-light': '#D4B49A',
                            'gold-dark': '#A07E5F',
                            dark: '#1a1412',
                            'dark-2': '#2C2220',
                            'dark-3': '#3D2E2A',
                            cream: '#F8F4EF',
                            ivory: '#EDE7DF',
                            text: '#1A1614',
                            muted: '#7A6B65',
                        },
                    },
                    boxShadow: {
                        'card': '0 1px 3px rgba(0,0,0,0.04), 0 4px 12px rgba(0,0,0,0.03)',
                        'card-hover': '0 4px 12px rgba(0,0,0,0.06), 0 12px 40px rgba(0,0,0,0.06)',
                    },
                },
            },
        };
    </script>

    {{-- Alpine.js for UI interactions --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        /* Scrollbar styling */
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(196,154,122,0.25); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(196,154,122,0.4); }

        /* Sidebar scroll */
        .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(248,244,239,0.08); }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover { background: rgba(248,244,239,0.15); }

        /* Smooth focus rings */
        *:focus-visible {
            outline: 2px solid #C49A7A;
            outline-offset: 2px;
        }

        /* Animated gold accent line */
        .accent-line {
            position: relative;
        }
        .accent-line::before {
            content: '';
            position: absolute;
            left: 0; top: 50%;
            width: 3px; height: 0;
            background: #C49A7A;
            transform: translateY(-50%);
            transition: height 0.2s ease-out;
        }
        .accent-line.active::before,
        .accent-line:hover::before {
            height: 70%;
        }

        /* Reduced motion */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { transition: none !important; animation: none !important; }
        }
    </style>

    @stack('head')
</head>
<body class="bg-delos-ivory text-delos-text font-sans antialiased" x-data="{ sidebarOpen: false }">

    {{-- Mobile sidebar backdrop --}}
    <div
        x-show="sidebarOpen"
        x-transition:enter="transition-opacity duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm lg:hidden"
        style="display: none;"
    ></div>

    {{-- Sidebar --}}
    @include('admin.partials.sidebar')

    {{-- Main content --}}
    <div class="lg:pl-72 min-h-screen flex flex-col">
        {{-- Topbar --}}
        @include('admin.partials.topbar')

        {{-- Flash messages --}}
        @include('admin.partials.flash-messages')

        {{-- Page content --}}
        <main class="flex-1 px-6 lg:px-10 py-8 lg:py-10">
            @yield('content')
        </main>

        {{-- Footer --}}
        <footer class="px-6 lg:px-10 py-5 border-t border-delos-dark/8 text-xs text-delos-muted flex items-center justify-between">
            <span>&copy; {{ date('Y') }} Delos International · Admin Panel</span>
            <span class="flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                System operational
            </span>
        </footer>
    </div>

    @stack('scripts')

    {{--
        Reset-to-default helper for page-content fields. Previously a nested
        <form> inside the outer "Save all" form — HTML forbids nested forms,
        so the browser silently closed the outer form at the reset button's
        <form> tag. Any field following the first customized (overridden)
        field was then outside the form and never submitted on Save All —
        which is why home page saves only ever wrote the first 6 fields.
    --}}
    <script>
        function delosResetPageContentField(key, url) {
            if (!confirm('Reset this field back to the default? Your custom value will be lost.')) return;
            const tokenEl = document.querySelector('meta[name="csrf-token"]');
            const token = tokenEl ? tokenEl.getAttribute('content') : '';
            const params = new URLSearchParams({ _token: token, key });
            fetch(url, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Accept': 'text/html,application/xhtml+xml',
                    'X-CSRF-TOKEN': token,
                },
                body: params.toString(),
            }).then((r) => {
                if (r.ok || r.redirected || r.status === 302) {
                    window.location.reload();
                } else {
                    alert('Reset failed with HTTP ' + r.status + '. Please reload and try again.');
                }
            }).catch((e) => alert('Network error: ' + e.message));
        }
    </script>

    {{--
        CSRF token refresh. Long admin forms (home page editor has 278+
        fields) can take longer than the session lifetime to fill out,
        causing the embedded _token to go stale. When the admin clicks
        Save with a stale token, Laravel rejects with 419 Page Expired —
        presenting as a silent failure with no flash message.

        Every 10 minutes, fetch a fresh token from /dashboard/csrf-refresh
        and rewrite every input[name="_token"] + the <meta> tag in place.
        Also rotates the token immediately before any admin form submits,
        and on window focus (common pattern: admin switches back to the
        tab after a long break, clicks Save right away).
    --}}
    <script>
        (function () {
            if (typeof window === 'undefined' || !window.fetch) return;
            const refreshUrl = @json(route('admin.csrf-refresh'));
            const meta = document.querySelector('meta[name="csrf-token"]');

            async function refreshCsrf() {
                try {
                    const r = await fetch(refreshUrl, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } });
                    if (!r.ok) return;
                    const { token } = await r.json();
                    if (!token) return;
                    if (meta) meta.setAttribute('content', token);
                    document.querySelectorAll('input[name="_token"]').forEach(i => { i.value = token; });
                } catch (_) { /* network error — silently skip; next cycle will retry */ }
            }

            // Background cadence so idle admins always have a fresh token.
            setInterval(refreshCsrf, 10 * 60 * 1000);
            // Tab re-focus is when stale-token bugs usually bite.
            window.addEventListener('focus', refreshCsrf);
            // Refresh right before any admin form submits — guarantees the
            // token the server validates is one it just issued.
            document.addEventListener('submit', async (e) => {
                const form = e.target;
                if (!(form instanceof HTMLFormElement)) return;
                const tokenInput = form.querySelector('input[name="_token"]');
                if (!tokenInput) return;
                // If we already refreshed in the last 10s, skip to keep save latency down.
                const now = Date.now();
                if (window.__delosLastCsrfRefresh && (now - window.__delosLastCsrfRefresh) < 10_000) return;
                e.preventDefault();
                await refreshCsrf();
                window.__delosLastCsrfRefresh = Date.now();
                form.submit();
            }, true);
        })();
    </script>
</body>
</html>
