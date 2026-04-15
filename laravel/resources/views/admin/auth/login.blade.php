<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In · Delos Admin</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" type="image/png" href="{{ asset('images/delos-logo.jpg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Cormorant+Garamond:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        serif: ['Cormorant Garamond', 'Georgia', 'serif'],
                    },
                    colors: {
                        delos: {
                            gold: '#C49A7A',
                            'gold-light': '#D4B49A',
                            'gold-dark': '#A07E5F',
                            dark: '#1a1412',
                            'dark-2': '#2C2220',
                            cream: '#F8F4EF',
                        },
                    },
                },
            },
        };
    </script>
    <style>
        body {
            background: radial-gradient(ellipse at top, #3D2E2A 0%, #1a1412 60%, #0a0706 100%);
            min-height: 100vh;
        }
        .gold-shimmer {
            background: linear-gradient(135deg, #C49A7A 0%, #D4B49A 50%, #C49A7A 100%);
            background-size: 200% 100%;
            animation: shimmer 3s linear infinite;
        }
        @keyframes shimmer { 0%{background-position:0% 50%} 100%{background-position:200% 50%} }

        @media (prefers-reduced-motion: reduce) {
            .gold-shimmer { animation: none; }
        }

        input:focus { outline: none; }
        .form-input {
            transition: all 0.2s;
        }
        .form-input:focus {
            border-color: #C49A7A;
            box-shadow: 0 0 0 3px rgba(196,154,122,0.15);
        }
    </style>
</head>
<body class="flex items-center justify-center p-4 font-sans text-delos-cream">

    {{-- Subtle background pattern --}}
    <div class="fixed inset-0 opacity-[0.015] pointer-events-none"
         style="background-image: url('data:image/svg+xml,%3Csvg width=%2240%22 height=%2240%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cpath d=%22M0 20h40M20 0v40%22 stroke=%22%23fff%22 stroke-width=%220.5%22/%3E%3C/svg%3E');"></div>

    <div class="relative w-full max-w-md">

        {{-- Card --}}
        <div class="bg-delos-dark-2/95 backdrop-blur-xl border border-delos-gold/20 rounded-2xl shadow-2xl overflow-hidden">

            {{-- Top accent --}}
            <div class="h-1 gold-shimmer"></div>

            <div class="p-10">
                {{-- Brand --}}
                <div class="flex flex-col items-center mb-10">
                    <div class="w-16 h-16 overflow-hidden rounded-full border-2 border-delos-gold/40 mb-5 ring-4 ring-delos-gold/10">
                        <img src="{{ asset('images/delos-logo.jpg') }}" alt="Delos" class="w-full h-full object-cover">
                    </div>
                    <h1 class="font-serif text-2xl font-semibold tracking-[0.3em] text-delos-cream leading-none">DELOS</h1>
                    <p class="text-[10px] tracking-[0.5em] uppercase text-delos-gold font-medium mt-2">International</p>
                    <div class="w-12 h-px bg-delos-gold/50 mt-5"></div>
                    <p class="text-xs tracking-[0.25em] uppercase text-delos-cream/50 font-medium mt-4">Admin Access</p>
                </div>

                @if (session('status'))
                    <div class="mb-5 px-4 py-3 rounded-lg bg-delos-gold/10 border border-delos-gold/30 text-delos-gold-light text-sm">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 px-4 py-3 rounded-lg bg-red-500/10 border border-red-500/30 text-red-400 text-sm flex items-start gap-2">
                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block text-[10px] tracking-[0.25em] uppercase text-delos-cream/60 font-medium mb-2">
                            Email
                        </label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="email"
                            class="form-input w-full px-4 py-3 bg-delos-dark/70 border border-delos-gold/15 rounded-lg text-delos-cream placeholder-delos-cream/30 text-sm"
                            placeholder="you@example.com"
                        >
                    </div>

                    <div>
                        <label for="password" class="block text-[10px] tracking-[0.25em] uppercase text-delos-cream/60 font-medium mb-2">
                            Password
                        </label>
                        <input
                            type="password"
                            name="password"
                            id="password"
                            required
                            autocomplete="current-password"
                            class="form-input w-full px-4 py-3 bg-delos-dark/70 border border-delos-gold/15 rounded-lg text-delos-cream placeholder-delos-cream/30 text-sm"
                            placeholder="••••••••"
                        >
                    </div>

                    <label class="flex items-center gap-2.5 text-sm text-delos-cream/70 cursor-pointer select-none">
                        <input type="checkbox" name="remember" value="1" class="w-4 h-4 rounded border-delos-gold/30 bg-delos-dark/70 text-delos-gold focus:ring-delos-gold focus:ring-offset-0">
                        <span>Keep me signed in</span>
                    </label>

                    <button
                        type="submit"
                        class="w-full py-3.5 bg-delos-gold hover:bg-delos-gold-light text-delos-dark-2 rounded-lg font-semibold text-xs tracking-[0.25em] uppercase transition-all duration-200 shadow-lg hover:shadow-xl"
                    >
                        Sign In
                    </button>
                </form>
            </div>

            {{-- Footer --}}
            <div class="px-10 py-4 bg-delos-dark/50 border-t border-delos-gold/10 flex items-center justify-between">
                <p class="text-[10px] tracking-[0.2em] uppercase text-delos-cream/40">
                    Restricted Access
                </p>
                <p class="text-[10px] tracking-[0.2em] uppercase text-delos-cream/40">
                    &copy; {{ date('Y') }} Delos
                </p>
            </div>
        </div>

        {{-- Info below card --}}
        <p class="text-center text-xs text-delos-cream/30 mt-6">
            Unauthorized access attempts are logged and monitored.
        </p>

    </div>
</body>
</html>
