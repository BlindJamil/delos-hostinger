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
</body>
</html>
