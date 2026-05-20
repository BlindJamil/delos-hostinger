@extends('admin.layout')

@section('title', 'Self-Test · Admin')
@section('page-title', 'Admin Self-Test')
@section('page-subtitle', 'Verify every editable page-content field saves + reads back correctly')

@section('content')
    <div class="space-y-6">

        {{-- Run control --}}
        <form method="POST" action="{{ route('admin.self-test.run') }}"
              class="bg-white rounded-xl border border-delos-dark/5 shadow-card p-6">
            @csrf

            <div class="flex items-start gap-4 mb-4">
                <div class="flex-1">
                    <h2 class="font-serif text-lg text-delos-dark-2 font-medium mb-1">How it works</h2>
                    <p class="text-sm text-delos-muted leading-relaxed">
                        Writes a unique canary value to every editable page-content field, reads it
                        back via both the database and the cached <code class="text-xs">pcontent()</code>
                        helper, then restores the original. Any field that fails either check appears
                        below. Zero net database drift — every value is restored.
                    </p>
                </div>
                <button type="submit"
                        class="flex-none inline-flex items-center gap-2 px-5 py-2.5 bg-delos-gold hover:bg-delos-gold-light text-delos-dark-2 rounded-lg text-xs font-semibold tracking-[0.2em] uppercase transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Run Self-Test
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-4">
                <div>
                    <label class="block text-[10px] tracking-[0.15em] uppercase font-semibold text-delos-muted mb-1.5">Page filter (optional)</label>
                    <select name="page"
                            class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold">
                        <option value="">— All pages —</option>
                        @foreach(config('editable_pages', []) as $slug => $config)
                            <option value="{{ $slug }}" @if(($report['filter_page'] ?? null) === $slug) selected @endif>
                                {{ $config['label'] ?? $slug }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] tracking-[0.15em] uppercase font-semibold text-delos-muted mb-1.5">Locale filter (optional)</label>
                    <select name="locale"
                            class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold">
                        <option value="">— All locales —</option>
                        <option value="en" @if(($report['filter_locale'] ?? null) === 'en') selected @endif>English</option>
                        <option value="ar" @if(($report['filter_locale'] ?? null) === 'ar') selected @endif>العربية</option>
                        <option value="it" @if(($report['filter_locale'] ?? null) === 'it') selected @endif>Italiano</option>
                        <option value="ku" @if(($report['filter_locale'] ?? null) === 'ku') selected @endif>کوردی</option>
                    </select>
                </div>
                <div class="flex items-end text-xs text-delos-muted">
                    <p>Full sweep typically takes 10–30 seconds against production.</p>
                </div>
            </div>
        </form>

        @if($report)
            {{-- Summary banner --}}
            <div class="rounded-xl border p-5
                        {{ $report['total_fail'] === 0 ? 'border-green-200 bg-green-50' : 'border-red-300 bg-red-50' }}">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        @if($report['total_fail'] === 0)
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <div>
                                <div class="font-serif text-2xl text-green-900">All clear</div>
                                <div class="text-sm text-green-800">{{ $report['total_pass'] }} field/locale combinations verified — no failures.</div>
                            </div>
                        @else
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/>
                            </svg>
                            <div>
                                <div class="font-serif text-2xl text-red-900">{{ $report['total_fail'] }} failing check{{ $report['total_fail'] === 1 ? '' : 's' }}</div>
                                <div class="text-sm text-red-800">{{ $report['total_pass'] }} passing, {{ $report['total_fail'] }} failing. Details below.</div>
                            </div>
                        @endif
                    </div>
                    <div class="text-right text-xs text-delos-muted">
                        <div>Duration: {{ $report['duration_ms'] }}&nbsp;ms</div>
                        <div>Ran: {{ $report['ran_at'] }}</div>
                    </div>
                </div>
            </div>

            {{-- Per-page grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($report['pages'] as $page)
                    <div class="bg-white rounded-xl border border-delos-dark/5 shadow-card p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-serif text-base text-delos-dark-2 font-medium">{{ $page['label'] }}</h3>
                            <span class="text-xs font-semibold tracking-wider uppercase
                                        {{ $page['fail'] === 0 ? 'text-green-700' : 'text-red-700' }}">
                                {{ $page['pass'] }} pass · {{ $page['fail'] }} fail
                            </span>
                        </div>
                        @if(!empty($page['failures']))
                            <div class="mt-3 space-y-2">
                                @foreach($page['failures'] as $f)
                                    <div class="text-xs border-l-2 border-red-400 pl-3 py-1">
                                        <div class="font-mono text-red-700">{{ $f['key'] }} <span class="text-red-500">[{{ $f['locale'] }}]</span></div>
                                        <div class="text-delos-muted">{{ $f['reason'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            {{-- Machine-readable JSON dump for copy-paste when escalating --}}
            <details class="bg-white rounded-xl border border-delos-dark/5 shadow-card p-4">
                <summary class="cursor-pointer text-xs font-semibold tracking-wider uppercase text-delos-muted hover:text-delos-dark-2">
                    Raw result (JSON) for debugging
                </summary>
                <pre class="mt-3 text-[10px] leading-relaxed text-delos-dark overflow-x-auto max-h-96">{{ json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </details>
        @endif
    </div>
@endsection
