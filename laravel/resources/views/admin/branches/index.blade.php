@extends('admin.layout')

@section('title', 'Branches')
@section('page-title', 'Branches')
@section('page-subtitle', 'Showrooms across Iraq')

@section('page-actions')
    <a href="{{ route('admin.branches.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-delos-gold hover:bg-delos-gold-light text-delos-dark-2 rounded-lg text-xs font-semibold tracking-[0.15em] uppercase transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Branch
    </a>
@endsection

@section('content')
    <div class="bg-white rounded-xl border border-delos-dark/5 shadow-card overflow-hidden">

        <div class="px-5 py-4 border-b border-delos-dark/5 flex flex-col sm:flex-row sm:items-center gap-3">
            <form method="GET" action="{{ route('admin.branches.index') }}" class="flex-1 flex items-center gap-2">
                <div class="relative flex-1 max-w-md">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-delos-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search branches..."
                        class="w-full pl-10 pr-4 py-2 bg-delos-ivory/40 border border-delos-dark/8 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                </div>
                <select name="status" onchange="this.form.submit()"
                        class="px-3 py-2 bg-delos-ivory/40 border border-delos-dark/8 rounded-lg text-sm focus:outline-none focus:border-delos-gold cursor-pointer">
                    <option value="">All status</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>
                @if(request('q') || request('status'))
                    <a href="{{ route('admin.branches.index') }}" class="text-xs text-delos-muted hover:text-delos-dark-2 transition-colors">Clear</a>
                @endif
            </form>
            <div class="text-xs text-delos-muted">{{ $branches->total() }} total</div>
        </div>

        @if($branches->isEmpty())
            <div class="px-8 py-20 text-center">
                <div class="w-14 h-14 mx-auto bg-delos-gold/10 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-delos-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="font-serif text-lg text-delos-dark-2 mb-1">No branches yet</h3>
                <p class="text-sm text-delos-muted mb-5">{{ request('q') ? 'No results for your search.' : 'Add your first showroom.' }}</p>
                @unless(request('q'))
                    <a href="{{ route('admin.branches.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-delos-gold hover:bg-delos-gold-light text-delos-dark-2 rounded-lg text-xs font-semibold tracking-[0.15em] uppercase transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Branch
                    </a>
                @endunless
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-delos-dark/5 text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium">
                            <th class="px-5 py-3 text-left font-medium w-16">Order</th>
                            <th class="px-5 py-3 text-left font-medium">Branch</th>
                            <th class="px-5 py-3 text-left font-medium">Address</th>
                            <th class="px-5 py-3 text-left font-medium w-36">Coordinates</th>
                            <th class="px-5 py-3 text-left font-medium w-32">Phone</th>
                            <th class="px-5 py-3 text-center font-medium w-24">Status</th>
                            <th class="px-5 py-3 text-right font-medium w-32">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-delos-dark/5">
                        @foreach($branches as $b)
                            <tr class="hover:bg-delos-ivory/30 transition-colors">
                                <td class="px-5 py-3 text-delos-muted text-xs">{{ $b->sort_order }}</td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="font-medium text-delos-dark-2">{{ $b->name_en }}</div>
                                        @if($b->is_flagship)
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] tracking-[0.15em] uppercase font-semibold text-delos-gold bg-delos-gold/10">
                                                <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                                Flagship
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-delos-muted mt-0.5 font-mono">{{ $b->city_key }}</div>
                                </td>
                                <td class="px-5 py-3 text-delos-dark-2/80 text-xs">{{ Str::limit($b->address_en, 60) ?: '—' }}</td>
                                <td class="px-5 py-3 text-delos-muted text-xs font-mono">
                                    @if($b->latitude && $b->longitude)
                                        {{ number_format($b->latitude, 4) }}, {{ number_format($b->longitude, 4) }}
                                    @else
                                        <span class="text-red-600/80">Missing</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-delos-muted text-xs">{{ $b->phone ?: '—' }}</td>
                                <td class="px-5 py-3 text-center">
                                    <form method="POST" action="{{ route('admin.branches.toggle', $b) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-medium {{ $b->active ? 'bg-green-50 text-green-700 hover:bg-green-100' : 'bg-delos-dark/5 text-delos-muted hover:bg-delos-dark/10' }} transition-colors">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $b->active ? 'bg-green-500' : 'bg-delos-muted' }}"></span>
                                            {{ $b->active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <div class="inline-flex items-center gap-1">
                                        <a href="{{ route('admin.branches.edit', $b) }}" class="p-2 text-delos-muted hover:text-delos-gold hover:bg-delos-gold/10 rounded-lg transition-colors" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.branches.destroy', $b) }}" class="inline" onsubmit="return confirm('Delete {{ addslashes($b->name_en) }}? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-delos-muted hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($branches->hasPages())
                <div class="px-5 py-4 border-t border-delos-dark/5">{{ $branches->links() }}</div>
            @endif
        @endif
    </div>
@endsection
