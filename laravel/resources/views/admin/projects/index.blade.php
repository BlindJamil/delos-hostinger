@extends('admin.layout')

@section('title', 'Projects')
@section('page-title', 'Projects')
@section('page-subtitle', 'Portfolio · Completed work across Iraq')

@section('page-actions')
    <a href="{{ route('admin.projects.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-delos-gold hover:bg-delos-gold-light text-delos-dark-2 rounded-lg text-xs font-semibold tracking-[0.15em] uppercase transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Project
    </a>
@endsection

@section('content')
    <div class="bg-white rounded-xl border border-delos-dark/5 shadow-card overflow-hidden">

        {{-- Filters --}}
        <div class="px-5 py-4 border-b border-delos-dark/5 flex flex-col lg:flex-row lg:items-center gap-3">
            <form method="GET" action="{{ route('admin.projects.index') }}" class="flex-1 flex flex-wrap items-center gap-2">
                <div class="relative flex-1 min-w-[220px] max-w-md">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-delos-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search projects..."
                        class="w-full pl-10 pr-4 py-2 bg-delos-ivory/40 border border-delos-dark/8 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                </div>

                <select name="type" onchange="this.form.submit()"
                        class="px-3 py-2 bg-delos-ivory/40 border border-delos-dark/8 rounded-lg text-sm focus:outline-none focus:border-delos-gold cursor-pointer">
                    <option value="">All types</option>
                    @foreach($types as $t)
                        <option value="{{ $t }}" @selected(request('type') === $t)>{{ ucfirst($t) }}</option>
                    @endforeach
                </select>

                <select name="featured" onchange="this.form.submit()"
                        class="px-3 py-2 bg-delos-ivory/40 border border-delos-dark/8 rounded-lg text-sm focus:outline-none focus:border-delos-gold cursor-pointer">
                    <option value="">All</option>
                    <option value="yes" @selected(request('featured') === 'yes')>Featured</option>
                    <option value="no" @selected(request('featured') === 'no')>Not featured</option>
                </select>

                <select name="status" onchange="this.form.submit()"
                        class="px-3 py-2 bg-delos-ivory/40 border border-delos-dark/8 rounded-lg text-sm focus:outline-none focus:border-delos-gold cursor-pointer">
                    <option value="">All status</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>

                @if(request('q') || request('status') || request('type') || request('featured'))
                    <a href="{{ route('admin.projects.index') }}" class="text-xs text-delos-muted hover:text-delos-dark-2 transition-colors">Clear</a>
                @endif
            </form>

            <div class="text-xs text-delos-muted">{{ $projects->total() }} total</div>
        </div>

        {{-- Table --}}
        @if($projects->isEmpty())
            <div class="px-8 py-20 text-center">
                <div class="w-14 h-14 mx-auto bg-delos-gold/10 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-delos-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="font-serif text-lg text-delos-dark-2 mb-1">No projects yet</h3>
                <p class="text-sm text-delos-muted mb-5">{{ request('q') ? 'No results for your search.' : 'Add your first project to showcase your portfolio.' }}</p>
                @unless(request('q'))
                    <a href="{{ route('admin.projects.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-delos-gold hover:bg-delos-gold-light text-delos-dark-2 rounded-lg text-xs font-semibold tracking-[0.15em] uppercase transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Project
                    </a>
                @endunless
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-delos-dark/5 text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium">
                            <th class="px-5 py-3 text-left font-medium w-20">Photo</th>
                            <th class="px-5 py-3 text-left font-medium">Title</th>
                            <th class="px-5 py-3 text-left font-medium">Type</th>
                            <th class="px-5 py-3 text-left font-medium">City</th>
                            <th class="px-5 py-3 text-left font-medium">Brand</th>
                            <th class="px-5 py-3 text-center font-medium w-16">Year</th>
                            <th class="px-5 py-3 text-center font-medium w-20">Featured</th>
                            <th class="px-5 py-3 text-center font-medium w-24">Status</th>
                            <th class="px-5 py-3 text-right font-medium w-32">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-delos-dark/5">
                        @foreach($projects as $proj)
                            <tr class="hover:bg-delos-ivory/30 transition-colors">
                                <td class="px-5 py-3">
                                    <div class="w-14 h-10 rounded overflow-hidden bg-delos-gold/10 flex items-center justify-center">
                                        @if($proj->image_url)
                                            <img src="{{ $proj->image_url }}" alt="" class="w-full h-full object-cover">
                                        @else
                                            <svg class="w-5 h-5 text-delos-gold/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="font-medium text-delos-dark-2">{{ $proj->title_en }}</div>
                                    @if($proj->title_ar || $proj->title_it)
                                        <div class="text-xs text-delos-muted mt-0.5 flex items-center gap-2">
                                            @if($proj->title_ar)<span>🇮🇶 {{ $proj->title_ar }}</span>@endif
                                            @if($proj->title_it)<span>🇮🇹 {{ $proj->title_it }}</span>@endif
                                        </div>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-delos-dark-2/80">{{ $proj->type_label_en ?: ucfirst($proj->type ?: '—') }}</td>
                                <td class="px-5 py-3 text-delos-muted">{{ $proj->city ?: '—' }}</td>
                                <td class="px-5 py-3 text-delos-muted">{{ $proj->brand ?: '—' }}</td>
                                <td class="px-5 py-3 text-center text-delos-muted">{{ $proj->year ?: '—' }}</td>
                                <td class="px-5 py-3 text-center">
                                    <form method="POST" action="{{ route('admin.projects.featured', $proj) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1.5 rounded-lg {{ $proj->featured ? 'text-delos-gold bg-delos-gold/10' : 'text-delos-muted hover:text-delos-gold hover:bg-delos-gold/5' }} transition-colors" title="{{ $proj->featured ? 'Remove from featured' : 'Mark as featured' }}">
                                            <svg class="w-4 h-4" fill="{{ $proj->featured ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                        </button>
                                    </form>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <form method="POST" action="{{ route('admin.projects.toggle', $proj) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-medium {{ $proj->active ? 'bg-green-50 text-green-700 hover:bg-green-100' : 'bg-delos-dark/5 text-delos-muted hover:bg-delos-dark/10' }} transition-colors">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $proj->active ? 'bg-green-500' : 'bg-delos-muted' }}"></span>
                                            {{ $proj->active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <div class="inline-flex items-center gap-1">
                                        <a href="{{ route('admin.projects.edit', $proj) }}" class="p-2 text-delos-muted hover:text-delos-gold hover:bg-delos-gold/10 rounded-lg transition-colors" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.projects.destroy', $proj) }}" class="inline" onsubmit="return confirm('Delete {{ addslashes($proj->title_en) }}? This cannot be undone.');">
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

            @if($projects->hasPages())
                <div class="px-5 py-4 border-t border-delos-dark/5">{{ $projects->links() }}</div>
            @endif
        @endif
    </div>
@endsection
