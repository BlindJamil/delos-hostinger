{{--
    Floating admin bar — mirrors the language globe at bottom-right.
    Only rendered by layouts/app.blade.php when $isAdmin is true, so this
    template can assume admin presence.

    Alpine state:
      collapsed     — gear-only vs expanded pill with menu
      pillsHidden   — toggles the html.no-edit-pills global class

    Both states persist via localStorage so the admin's preference
    carries across page reloads. Keys are namespaced under delos:admin-bar
    to avoid collision with site-wide keys (delos_locale etc.).
--}}
<div
    class="admin-bar"
    x-data="{
        collapsed: (localStorage.getItem('delos:admin-bar:collapsed') ?? 'true') === 'true',
        pillsHidden: (localStorage.getItem('delos:admin-bar:pills-hidden') ?? 'false') === 'true',
        init() {
            this.$watch('collapsed',   v => localStorage.setItem('delos:admin-bar:collapsed', v));
            this.$watch('pillsHidden', v => {
                localStorage.setItem('delos:admin-bar:pills-hidden', v);
                document.documentElement.classList.toggle('no-edit-pills', v);
            });
            // Apply the persisted pillsHidden state on first load.
            document.documentElement.classList.toggle('no-edit-pills', this.pillsHidden);
        }
    }"
    :class="{ 'admin-bar--collapsed': collapsed, 'admin-bar--expanded': !collapsed }"
>
    {{-- Gear trigger (always visible). Small dot on gear when pills are hidden --}}
    <button
        type="button"
        class="admin-bar__trigger"
        @click="collapsed = !collapsed"
        :aria-expanded="!collapsed"
        aria-label="Admin controls"
        title="Admin controls"
    >
        <svg class="admin-bar__gear" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <span x-show="pillsHidden" class="admin-bar__dot" aria-hidden="true"></span>
    </button>

    {{-- Expanded menu --}}
    <div
        class="admin-bar__menu"
        x-show="!collapsed"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="admin-bar__header">
            <span class="admin-bar__label">Viewing as</span>
            <span class="admin-bar__name">{{ $currentAdmin?->name ?? 'admin' }}</span>
        </div>

        <div class="admin-bar__links">
            <a href="{{ route('admin.dashboard') }}" class="admin-bar__link" data-page-transition="false">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 12l2-2m0 0l7-7 7 7m-9 2v8a1 1 0 001 1h3m10-11l2 2m-2-2v8a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span class="admin-bar__link-text">
                    <span class="admin-bar__link-title">Dashboard</span>
                    <span class="admin-bar__link-sub">Manage all content</span>
                </span>
            </a>
            <a href="{{ route('admin.site-settings.index') }}" class="admin-bar__link" data-page-transition="false">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span class="admin-bar__link-text">
                    <span class="admin-bar__link-title">Global settings</span>
                    <span class="admin-bar__link-sub">Phone, email, social URLs, tagline</span>
                </span>
            </a>
        </div>

        <div class="admin-bar__divider"></div>

        <label class="admin-bar__toggle">
            <span class="admin-bar__toggle-label">Hide edit pills</span>
            <span class="admin-bar__switch" :class="{ 'is-on': pillsHidden }">
                <input type="checkbox" x-model="pillsHidden" class="sr-only">
                <span class="admin-bar__switch-thumb" :class="{ 'is-on': pillsHidden }"></span>
            </span>
        </label>

        <div class="admin-bar__divider"></div>

        <form action="{{ route('admin.logout') }}" method="POST" class="admin-bar__logout-form">
            @csrf
            <button type="submit" class="admin-bar__link admin-bar__link--danger">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Sign out
            </button>
        </form>
    </div>
</div>
