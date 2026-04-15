@extends('admin.layout')

@section('title', 'Page Content')
@section('page-title', 'Page Content')
@section('page-subtitle', 'Edit every string + media across the public site')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($pages as $page)
            <a href="{{ route('admin.page-content.edit', $page['slug']) }}"
               class="group block bg-white rounded-xl border border-delos-dark/5 shadow-card hover:shadow-card-hover hover:border-delos-gold/40 transition-all duration-200 overflow-hidden">
                <div class="p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-9 h-9 rounded-lg bg-delos-gold/10 flex items-center justify-center text-delos-gold flex-shrink-0 group-hover:bg-delos-gold/20 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <span class="text-[10px] tracking-[0.15em] uppercase text-delos-muted font-mono">{{ $page['slug'] }}</span>
                    </div>
                    <h3 class="font-serif text-lg text-delos-dark-2 font-medium group-hover:text-delos-gold-dark transition-colors mb-1">
                        {{ $page['label'] }}
                    </h3>
                    @if($page['description'])
                        <p class="text-xs text-delos-muted leading-relaxed mb-4">{{ $page['description'] }}</p>
                    @endif
                    <div class="flex items-center gap-3 text-xs text-delos-muted">
                        <span class="inline-flex items-center gap-1">
                            <span class="font-medium text-delos-dark-2/80">{{ $page['field_count'] }}</span>
                            <span>fields</span>
                        </span>
                        @if($page['filled'] > 0)
                            <span class="w-1 h-1 rounded-full bg-delos-dark/15"></span>
                            <span class="inline-flex items-center gap-1 text-delos-gold-dark">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                {{ $page['filled'] }} filled
                            </span>
                        @endif
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    <div class="mt-8 p-5 bg-delos-ivory/40 border border-delos-dark/5 rounded-xl">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-delos-gold flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="text-xs text-delos-muted leading-relaxed">
                <p class="mb-2"><strong class="text-delos-dark-2/80">How this works.</strong> Each page lists every editable text and media block. Changes you save here override the default content immediately on the public site. Records like Employees, Projects, Brands, Services, and Branches are managed in their own admin sections.</p>
                <p><strong class="text-delos-dark-2/80">Reset to default.</strong> Each field has a small reset button that restores the original language-file value if you want to undo a specific edit.</p>
            </div>
        </div>
    </div>
@endsection
