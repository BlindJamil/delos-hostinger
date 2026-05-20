@extends('admin.layout')

@section('title', 'Editing ' . $pageConfig['label'])
@section('page-title', $pageConfig['label'])
@section('page-subtitle', 'Page content · ' . ($pageConfig['description'] ?? 'Edit all fields below'))

@section('page-actions')
    @php
        // Map the editable-page slug to its public route name (l.*).
        // Slugs not in the map (e.g. `common`, `seo`) are global/shared and
        // don't have a dedicated public URL — hide the preview link for them.
        $publicRouteMap = [
            'home' => 'home',
            'about' => 'about',
            'services' => 'services',
            'projects' => 'projects',
            'brands' => 'brands',
            'branches' => 'branches',
            'contact' => 'contact',
        ];
        $publicRoute = $publicRouteMap[$pageSlug] ?? null;
    @endphp

    @if($publicRoute)
        {{-- ?_t cache-buster: forces LiteSpeed/browser to fetch fresh HTML
             so the admin can confirm their edit rendered, not a cached copy. --}}
        <a href="{{ lroute($publicRoute, ['_t' => time()], app()->getLocale()) }}"
           target="_blank" rel="noopener"
           class="inline-flex items-center gap-2 px-4 py-2 text-delos-muted hover:text-delos-gold text-xs font-medium tracking-[0.15em] uppercase transition-colors">
            View on public site
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
        </a>
    @endif

    <a href="{{ route('admin.page-content.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2 text-delos-muted hover:text-delos-dark-2 text-xs font-medium tracking-[0.15em] uppercase transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        All pages
    </a>
@endsection

@section('content')
    {{--
        Inline save-result banner — rendered in-form rather than relying
        only on the layout's dismissible toast. Admins editing long forms
        missed the toast and thought saves had silently failed; this
        always-visible banner removes that ambiguity.
    --}}
    @if(session('success') || session('error'))
        <div class="mb-4 rounded-xl border px-4 py-3 text-sm flex items-start gap-3
                    {{ session('success') ? 'border-green-200 bg-green-50 text-green-900' : 'border-red-200 bg-red-50 text-red-900' }}">
            <svg class="w-5 h-5 flex-none mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                @if(session('success'))
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                @else
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/>
                @endif
            </svg>
            <div class="flex-1">
                <div class="font-semibold tracking-wide uppercase text-[11px] mb-1">
                    {{ session('success') ? 'Saved' : 'Save failed' }}
                </div>
                <div>{{ session('success') ?? session('error') }}</div>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.page-content.update', $pageSlug) }}"
          method="POST"
          class="space-y-4"
          x-data="{ activeLocale: 'en' }">
        @csrf
        @method('PUT')

        {{-- Locale tabs — sticky so they stay visible while scrolling --}}
        <div class="sticky top-4 z-20 bg-delos-ivory/95 backdrop-blur border border-delos-dark/8 rounded-xl shadow-sm p-3 flex items-center justify-between gap-3">
            <div class="flex items-center gap-1">
                @foreach(['en' => 'English', 'ar' => 'العربية', 'it' => 'Italiano', 'ku' => 'کوردی'] as $code => $label)
                    <button type="button"
                            @click="activeLocale = '{{ $code }}'"
                            :class="activeLocale === '{{ $code }}' ? 'bg-delos-dark-2 text-delos-cream shadow-sm' : 'text-delos-muted hover:text-delos-dark-2 hover:bg-delos-ivory'"
                            class="px-4 py-2 rounded-lg text-[11px] tracking-[0.15em] uppercase font-semibold transition-all">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
            <button type="submit"
                    class="px-5 py-2 bg-delos-gold hover:bg-delos-gold-light text-delos-dark-2 rounded-lg text-xs font-semibold tracking-[0.2em] uppercase transition-colors">
                Save all
            </button>
        </div>

        {{-- Sections list --}}
        @foreach($pageConfig['sections'] ?? [] as $sectionKey => $section)
            @include('admin.page-content._section', [
                'sectionKey' => $sectionKey,
                'section' => $section,
                'rows' => $rows,
                'langDefaults' => $langDefaults,
                'pageSlug' => $pageSlug,
            ])
        @endforeach

        {{-- Floating save bar (bottom) --}}
        <div class="sticky bottom-4 z-20 bg-delos-ivory/95 backdrop-blur border border-delos-dark/8 rounded-xl shadow-lg p-3 flex items-center justify-between gap-3">
            <p class="text-xs text-delos-muted pl-2">Changes apply instantly to the public site after saving.</p>
            <button type="submit"
                    class="px-6 py-2.5 bg-delos-gold hover:bg-delos-gold-light text-delos-dark-2 rounded-lg text-xs font-semibold tracking-[0.2em] uppercase transition-colors">
                Save all
            </button>
        </div>
    </form>

    <style>[x-cloak] { display: none !important; }</style>
@endsection
