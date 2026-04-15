<aside
    class="fixed inset-y-0 left-0 z-50 w-72 bg-delos-dark transition-transform duration-300 transform lg:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
>
    <div class="h-full flex flex-col sidebar-scroll overflow-y-auto">

        {{-- Brand --}}
        <div class="px-6 py-6 border-b border-white/5">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 overflow-hidden rounded-full border border-delos-gold/30 flex-shrink-0">
                    <img src="{{ asset('images/delos-logo.jpg') }}" alt="Delos" class="w-full h-full object-cover">
                </div>
                <div class="flex flex-col">
                    <span class="font-serif text-lg font-semibold tracking-[0.25em] text-delos-cream leading-none">DELOS</span>
                    <span class="text-[9px] tracking-[0.4em] uppercase text-delos-gold font-medium mt-1">Admin Panel</span>
                </div>
            </a>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-6">
            @php
                $navSections = [
                    [
                        'label' => 'Overview',
                        'items' => [
                            ['route' => 'admin.dashboard', 'icon' => 'home', 'label' => 'Dashboard'],
                        ],
                    ],
                    [
                        'label' => 'Content',
                        'items' => [
                            ['route' => 'admin.page-content.index', 'icon' => 'document', 'label' => 'Page Content'],
                            ['route' => 'admin.employees.index', 'icon' => 'users', 'label' => 'Employees'],
                            ['route' => 'admin.projects.index', 'icon' => 'briefcase', 'label' => 'Projects'],
                            ['route' => 'admin.brands.index', 'icon' => 'tag', 'label' => 'Brands'],
                            ['route' => 'admin.services.index', 'icon' => 'layers', 'label' => 'Services'],
                            ['route' => 'admin.branches.index', 'icon' => 'pin', 'label' => 'Branches'],
                        ],
                    ],
                    [
                        'label' => 'Configuration',
                        'items' => [
                            ['route' => 'admin.site-settings.index', 'icon' => 'settings', 'label' => 'Site Settings'],
                            ['route' => 'admin.admin-users.index', 'icon' => 'shield', 'label' => 'Admin Users'],
                        ],
                    ],
                ];

                $icons = [
                    'home' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7m-9 2v8a1 1 0 001 1h3m10-11l2 2m-2-2v8a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />',
                    'users' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />',
                    'briefcase' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />',
                    'tag' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />',
                    'layers' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />',
                    'pin' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />',
                    'document' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
                    'settings' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />',
                    'shield' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />',
                ];

                $currentRoute = request()->route()?->getName();
            @endphp

            @foreach($navSections as $section)
                <div class="mb-6">
                    <p class="px-4 text-[10px] tracking-[0.3em] uppercase text-delos-cream/30 font-medium mb-2">
                        {{ $section['label'] }}
                    </p>
                    <div class="space-y-0.5">
                        @foreach($section['items'] as $item)
                            @php
                                $routePrefix = \Illuminate\Support\Str::beforeLast($item['route'], '.');
                                $isActive = empty($item['disabled']) && (
                                    $currentRoute === $item['route']
                                    || ($routePrefix && $currentRoute && \Illuminate\Support\Str::startsWith($currentRoute, $routePrefix . '.'))
                                );
                                $isDisabled = !empty($item['disabled']);
                            @endphp
                            <a
                                @if($isDisabled)
                                    href="#"
                                    onclick="event.preventDefault()"
                                    class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-delos-cream/40 cursor-not-allowed accent-line"
                                @else
                                    href="{{ route($item['route']) }}"
                                    class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-colors duration-200 accent-line {{ $isActive ? 'bg-delos-gold/10 text-delos-gold active' : 'text-delos-cream/70 hover:text-delos-cream hover:bg-white/3' }}"
                                @endif
                            >
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    {!! $icons[$item['icon']] ?? '' !!}
                                </svg>
                                <span class="text-sm font-medium flex-1">{{ $item['label'] }}</span>
                                @if(!empty($item['soon']))
                                    <span class="text-[9px] tracking-wider uppercase text-delos-gold/60 bg-delos-gold/10 px-1.5 py-0.5 rounded">Soon</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </nav>

        {{-- Footer — view site link --}}
        <div class="px-3 py-4 border-t border-white/5">
            <a
                href="{{ url('/') }}"
                target="_blank"
                rel="noopener"
                class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-delos-cream/60 hover:text-delos-gold hover:bg-white/3 transition-colors duration-200 text-sm"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                </svg>
                <span class="font-medium">View Site</span>
                <svg class="w-3 h-3 ml-auto opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
            </a>
        </div>

    </div>
</aside>
