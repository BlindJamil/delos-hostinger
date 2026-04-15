<details class="group bg-white rounded-xl border border-delos-dark/5 shadow-card overflow-hidden" open>
    <summary class="flex items-center justify-between px-5 py-4 cursor-pointer list-none hover:bg-delos-ivory/30 transition-colors">
        <div>
            <h3 class="font-serif text-base text-delos-dark-2 font-medium">{{ $section['label'] }}</h3>
            @if(!empty($section['description']))
                <p class="text-xs text-delos-muted mt-0.5">{{ $section['description'] }}</p>
            @endif
        </div>
        <div class="flex items-center gap-3 text-xs text-delos-muted">
            <span>{{ count($section['fields'] ?? []) }} field{{ count($section['fields'] ?? []) === 1 ? '' : 's' }}</span>
            <svg class="w-4 h-4 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </summary>

    <div class="p-5 pt-0 space-y-5 border-t border-delos-dark/5">
        @foreach($section['fields'] ?? [] as $field)
            @include('admin.page-content._field', [
                'field' => $field,
                'row' => $rows[$field['key']] ?? null,
                'langDefault' => $langDefaults[$field['key']] ?? null,
                'pageSlug' => $pageSlug,
            ])
        @endforeach
    </div>
</details>
