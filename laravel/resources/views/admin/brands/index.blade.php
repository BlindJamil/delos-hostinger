@extends('admin.layout')

@section('title', 'Brands')
@section('page-title', 'Brands')
@section('page-subtitle', 'Italian partner portfolio')

@section('page-actions')
    <a href="{{ route('admin.brands.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-delos-gold hover:bg-delos-gold-light text-delos-dark-2 rounded-lg text-xs font-semibold tracking-[0.15em] uppercase transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Brand
    </a>
@endsection

@section('content')
    <div class="bg-white rounded-xl border border-delos-dark/5 shadow-card overflow-hidden">

        <div class="px-5 py-4 border-b border-delos-dark/5 flex flex-col sm:flex-row sm:items-center gap-3">
            <form method="GET" action="{{ route('admin.brands.index') }}" class="flex-1 flex items-center gap-2">
                <div class="relative flex-1 max-w-md">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-delos-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search brands..."
                        class="w-full pl-10 pr-4 py-2 bg-delos-ivory/40 border border-delos-dark/8 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                </div>
                <select name="status" onchange="this.form.submit()"
                        class="px-3 py-2 bg-delos-ivory/40 border border-delos-dark/8 rounded-lg text-sm focus:outline-none focus:border-delos-gold cursor-pointer">
                    <option value="">All status</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>
                @if(request('q') || request('status'))
                    <a href="{{ route('admin.brands.index') }}" class="text-xs text-delos-muted hover:text-delos-dark-2 transition-colors">Clear</a>
                @endif
            </form>
            <div class="text-xs text-delos-muted">{{ $brands->total() }} total</div>
        </div>

        @if($brands->isEmpty())
            <div class="px-8 py-20 text-center">
                <div class="w-14 h-14 mx-auto bg-delos-gold/10 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-delos-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                </div>
                <h3 class="font-serif text-lg text-delos-dark-2 mb-1">No brands yet</h3>
                <p class="text-sm text-delos-muted mb-5">{{ request('q') ? 'No results for your search.' : 'Add your first brand partner.' }}</p>
                @unless(request('q'))
                    <a href="{{ route('admin.brands.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-delos-gold hover:bg-delos-gold-light text-delos-dark-2 rounded-lg text-xs font-semibold tracking-[0.15em] uppercase transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Brand
                    </a>
                @endunless
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-delos-dark/5 text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium">
                            <th class="px-5 py-3 text-left font-medium w-20">Image</th>
                            <th class="px-5 py-3 text-left font-medium">Brand</th>
                            <th class="px-5 py-3 text-left font-medium">Category</th>
                            <th class="px-5 py-3 text-left font-medium">Origin</th>
                            <th class="px-5 py-3 text-left font-medium w-24">Since</th>
                            <th class="px-5 py-3 text-center font-medium w-24">Status</th>
                            <th class="px-5 py-3 text-right font-medium w-32">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-delos-dark/5">
                        @foreach($brands as $b)
                            <tr class="hover:bg-delos-ivory/30 transition-colors">
                                <td class="px-5 py-3">
                                    <div class="w-14 h-10 rounded overflow-hidden bg-delos-gold/10 flex items-center justify-center">
                                        @if($b->image_url)
                                            <img src="{{ $b->image_url }}" alt="" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-[10px] font-semibold text-delos-gold">{{ strtoupper(substr($b->name, 0, 3)) }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="font-medium text-delos-dark-2">{{ $b->name }}</div>
                                    <div class="text-xs text-delos-muted mt-0.5 font-mono">{{ $b->slug }}</div>
                                </td>
                                <td class="px-5 py-3 text-delos-dark-2/80">{{ $b->category_en ?: '—' }}</td>
                                <td class="px-5 py-3 text-delos-muted">{{ $b->origin_en ?: '—' }}</td>
                                <td class="px-5 py-3 text-delos-muted">{{ $b->since ?: '—' }}</td>
                                <td class="px-5 py-3 text-center">
                                    <form method="POST" action="{{ route('admin.brands.toggle', $b) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-medium {{ $b->active ? 'bg-green-50 text-green-700 hover:bg-green-100' : 'bg-delos-dark/5 text-delos-muted hover:bg-delos-dark/10' }} transition-colors">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $b->active ? 'bg-green-500' : 'bg-delos-muted' }}"></span>
                                            {{ $b->active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <div class="inline-flex items-center gap-1">
                                        <a href="{{ route('admin.brands.edit', $b) }}" class="p-2 text-delos-muted hover:text-delos-gold hover:bg-delos-gold/10 rounded-lg transition-colors" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.brands.destroy', $b) }}" class="inline" onsubmit="return confirm('Delete {{ addslashes($b->name) }}? This cannot be undone.');">
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

            @if($brands->hasPages())
                <div class="px-5 py-4 border-t border-delos-dark/5">{{ $brands->links() }}</div>
            @endif
        @endif
    </div>
@endsection
