<header class="sticky top-0 z-30 bg-white/80 backdrop-blur-md border-b border-delos-dark/8">
    <div class="px-6 lg:px-10 h-16 flex items-center justify-between gap-4">

        {{-- Left: Mobile menu toggle + page title --}}
        <div class="flex items-center gap-3">
            <button
                @click="sidebarOpen = !sidebarOpen"
                class="lg:hidden w-10 h-10 flex items-center justify-center rounded-lg hover:bg-delos-dark/5 transition-colors"
                aria-label="Toggle menu"
            >
                <svg class="w-5 h-5 text-delos-dark-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <div>
                <h1 class="font-serif text-xl text-delos-dark-2 font-medium leading-none">@yield('page-title', 'Dashboard')</h1>
                @hasSection('page-subtitle')
                    <p class="text-[11px] tracking-[0.15em] uppercase text-delos-muted font-medium mt-1">@yield('page-subtitle')</p>
                @endif
            </div>
        </div>

        {{-- Right: Actions + User --}}
        <div class="flex items-center gap-3">

            @hasSection('page-actions')
                <div class="hidden sm:flex items-center gap-2">
                    @yield('page-actions')
                </div>
            @endif

            {{-- User dropdown --}}
            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                <button
                    @click="open = !open"
                    class="flex items-center gap-2.5 pl-2 pr-3 py-1.5 rounded-full hover:bg-delos-dark/5 transition-colors"
                >
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-delos-gold to-delos-gold-dark flex items-center justify-center text-white text-xs font-semibold">
                        {{ strtoupper(substr(auth('admin')->user()->name, 0, 1)) }}
                    </div>
                    <div class="hidden sm:flex flex-col items-start leading-tight">
                        <span class="text-xs font-medium text-delos-dark-2">{{ auth('admin')->user()->name }}</span>
                        <span class="text-[10px] text-delos-muted">{{ auth('admin')->user()->is_super ? 'Super Admin' : 'Admin' }}</span>
                    </div>
                    <svg class="w-3.5 h-3.5 text-delos-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-64 rounded-xl bg-white border border-delos-dark/8 shadow-card overflow-hidden"
                    style="display: none;"
                >
                    <div class="px-4 py-3 border-b border-delos-dark/5">
                        <p class="text-sm font-medium text-delos-dark-2 truncate">{{ auth('admin')->user()->name }}</p>
                        <p class="text-xs text-delos-muted truncate mt-0.5">{{ auth('admin')->user()->email }}</p>
                        @if(auth('admin')->user()->last_login_at)
                            <p class="text-[11px] text-delos-muted/70 mt-2">
                                Last login: {{ auth('admin')->user()->last_login_at->format('M j, H:i') }}
                            </p>
                        @endif
                    </div>

                    <div class="py-1">
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Sign out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
