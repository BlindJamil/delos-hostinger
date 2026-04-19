@extends('admin.layout')

@section('title', 'Apply Iraqi Arabic Copy')
@section('page-title', 'Apply Iraqi Arabic Copy')
@section('page-subtitle', 'One-click preview + apply for the curated Arabic copy refresh')

@section('content')
    <div class="space-y-6">

        {{-- Intro + apply action --}}
        <form method="POST" action="{{ route('admin.apply-iraqi-copy.apply') }}"
              onsubmit="return confirm('Apply this Iraqi Arabic copy set? Any admin edits made to these fields since the last apply will be OVERWRITTEN.');"
              class="bg-white rounded-xl border border-delos-dark/5 shadow-card p-6">
            @csrf

            <div class="flex items-start gap-4 mb-4">
                <div class="flex-1">
                    <h2 class="font-serif text-lg text-delos-dark-2 font-medium mb-1">What this does</h2>
                    <p class="text-sm text-delos-muted leading-relaxed">
                        Writes the curated Iraqi-flavored Arabic copy (defined in
                        <code class="text-xs">IraqiArabicContentSeeder</code>) to the page content table
                        and the LUBE brand row. A snapshot of current values is saved before any write
                        so the change is reversible. English and Italian copy are untouched.
                    </p>
                    <p class="text-xs text-red-700 mt-3 font-medium">
                        ⚠ Re-applying will OVERWRITE any admin edits made to these fields since the last apply.
                    </p>
                </div>
                <button type="submit"
                        class="flex-none inline-flex items-center gap-2 px-5 py-2.5 bg-delos-gold hover:bg-delos-gold-light text-delos-dark-2 rounded-lg text-xs font-semibold tracking-[0.2em] uppercase transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Apply all
                </button>
            </div>
        </form>

        {{-- Result banner (after an apply) --}}
        @if($result)
            <div class="rounded-xl border p-5
                        {{ $result['fail'] === 0 ? 'border-green-200 bg-green-50' : 'border-red-300 bg-red-50' }}">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <div class="font-serif text-2xl {{ $result['fail'] === 0 ? 'text-green-900' : 'text-red-900' }}">
                            {{ $result['fail'] === 0 ? 'Applied successfully' : ($result['fail'] . ' failure' . ($result['fail'] === 1 ? '' : 's')) }}
                        </div>
                        <div class="text-sm mt-1 {{ $result['fail'] === 0 ? 'text-green-800' : 'text-red-800' }}">
                            {{ $result['pass'] }} writes passed, {{ $result['fail'] }} failed.
                        </div>
                        @if($result['snapshot_path'])
                            <div class="text-xs text-delos-muted mt-2 font-mono">
                                Snapshot: {{ basename($result['snapshot_path']) }}
                            </div>
                        @endif
                    </div>
                    <div class="text-right text-xs text-delos-muted">
                        Ran: {{ $result['ran_at'] }}
                    </div>
                </div>

                @if($result['fail'] > 0)
                    <div class="mt-4 space-y-1">
                        @foreach($result['page_content_results'] as $r)
                            @if($r['status'] !== 'pass')
                                <div class="text-xs font-mono text-red-700">✗ {{ $r['key'] }} — {{ $r['reason'] }}</div>
                            @endif
                        @endforeach
                        @foreach($result['brand_results'] as $r)
                            @if($r['status'] !== 'pass')
                                <div class="text-xs font-mono text-red-700">✗ brand:{{ $r['slug'] }} — {{ $r['reason'] }}</div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        {{-- Page content diff --}}
        <div class="bg-white rounded-xl border border-delos-dark/5 shadow-card overflow-hidden">
            <div class="px-5 py-4 border-b border-delos-dark/5 flex items-center justify-between">
                <div>
                    <h2 class="font-serif text-lg text-delos-dark-2 font-medium">Page content · {{ count($diff['page_content']) }} keys</h2>
                    <p class="text-xs text-delos-muted mt-0.5">Current vs new Arabic value for each target key.</p>
                </div>
                <span class="text-xs text-delos-muted">
                    {{ collect($diff['page_content'])->where('will_change', true)->count() }} will change
                </span>
            </div>
            <div class="divide-y divide-delos-dark/5">
                @foreach($diff['page_content'] as $row)
                    <div class="px-5 py-4 {{ $row['will_change'] ? '' : 'opacity-60' }}">
                        <div class="flex items-start justify-between gap-4 mb-2">
                            <div>
                                <div class="font-mono text-[11px] tracking-wide text-delos-dark">{{ $row['key'] }}</div>
                                <div class="text-[10px] tracking-[0.15em] uppercase text-delos-muted mt-0.5">{{ $row['page'] }} · {{ $row['section'] }} · {{ $row['type'] }}</div>
                            </div>
                            @if($row['will_change'])
                                <span class="flex-none text-[10px] tracking-[0.15em] uppercase font-semibold text-delos-gold bg-delos-gold/10 px-2 py-0.5 rounded">Will change</span>
                            @else
                                <span class="flex-none text-[10px] tracking-[0.15em] uppercase font-semibold text-delos-muted">No change</span>
                            @endif
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <div class="text-[10px] tracking-[0.15em] uppercase font-semibold text-delos-muted mb-1">Current (AR)</div>
                                <div class="text-sm rounded-lg bg-delos-ivory/60 border border-delos-dark/8 px-3 py-2 whitespace-pre-wrap" dir="rtl" style="font-family: 'Cairo', sans-serif;">{{ $row['current_ar'] ?? '—' }}</div>
                            </div>
                            <div>
                                <div class="text-[10px] tracking-[0.15em] uppercase font-semibold text-delos-muted mb-1">New (AR)</div>
                                <div class="text-sm rounded-lg bg-white border {{ $row['will_change'] ? 'border-delos-gold/60' : 'border-delos-dark/8' }} px-3 py-2 whitespace-pre-wrap" dir="rtl" style="font-family: 'Cairo', sans-serif;">{{ $row['new_ar'] }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Brand row diff --}}
        <div class="bg-white rounded-xl border border-delos-dark/5 shadow-card overflow-hidden">
            <div class="px-5 py-4 border-b border-delos-dark/5 flex items-center justify-between">
                <div>
                    <h2 class="font-serif text-lg text-delos-dark-2 font-medium">Brand rows</h2>
                    <p class="text-xs text-delos-muted mt-0.5">Direct column updates on the brands table.</p>
                </div>
                <span class="text-xs text-delos-muted">
                    {{ collect($diff['brands'])->where('will_change', true)->count() }} columns will change
                </span>
            </div>
            <div class="divide-y divide-delos-dark/5">
                @foreach($diff['brands'] as $row)
                    <div class="px-5 py-4 {{ $row['will_change'] ? '' : 'opacity-60' }}">
                        <div class="flex items-start justify-between gap-4 mb-2">
                            <div class="font-mono text-[11px] text-delos-dark">brand:{{ $row['slug'] }}.{{ $row['column'] }}</div>
                            @if(!$row['row_exists'])
                                <span class="flex-none text-[10px] tracking-[0.15em] uppercase font-semibold text-red-700 bg-red-100 px-2 py-0.5 rounded">Row not found</span>
                            @elseif($row['will_change'])
                                <span class="flex-none text-[10px] tracking-[0.15em] uppercase font-semibold text-delos-gold bg-delos-gold/10 px-2 py-0.5 rounded">Will change</span>
                            @else
                                <span class="flex-none text-[10px] tracking-[0.15em] uppercase font-semibold text-delos-muted">No change</span>
                            @endif
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <div class="text-[10px] tracking-[0.15em] uppercase font-semibold text-delos-muted mb-1">Current</div>
                                <div class="text-sm rounded-lg bg-delos-ivory/60 border border-delos-dark/8 px-3 py-2 whitespace-pre-wrap" dir="rtl" style="font-family: 'Cairo', sans-serif;">{{ $row['current'] ?? '—' }}</div>
                            </div>
                            <div>
                                <div class="text-[10px] tracking-[0.15em] uppercase font-semibold text-delos-muted mb-1">New</div>
                                <div class="text-sm rounded-lg bg-white border {{ $row['will_change'] ? 'border-delos-gold/60' : 'border-delos-dark/8' }} px-3 py-2 whitespace-pre-wrap" dir="rtl" style="font-family: 'Cairo', sans-serif;">{{ $row['new'] }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
