@extends('admin.layout')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Overview')

@section('content')
    @php
        $admin = auth('admin')->user();
        $hour = now()->hour;
        $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');

        $stats = [
            ['label' => 'Employees', 'count' => \App\Models\Employee::count(), 'icon' => 'users', 'accent' => 'from-blue-500 to-blue-600'],
            ['label' => 'Projects', 'count' => \App\Models\Project::count(), 'icon' => 'briefcase', 'accent' => 'from-purple-500 to-purple-600'],
            ['label' => 'Brands', 'count' => \App\Models\Brand::count(), 'icon' => 'tag', 'accent' => 'from-amber-500 to-amber-600'],
            ['label' => 'Services', 'count' => \App\Models\Service::count(), 'icon' => 'layers', 'accent' => 'from-emerald-500 to-emerald-600'],
        ];

        $icons = [
            'users' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />',
            'briefcase' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />',
            'tag' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />',
            'layers' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />',
        ];
    @endphp

    {{-- Welcome header --}}
    <div class="mb-8 flex items-start justify-between gap-4">
        <div>
            <h2 class="font-serif text-3xl text-delos-dark-2 font-light mb-1">{{ $greeting }}, <span class="font-medium">{{ explode(' ', $admin->name)[0] }}</span></h2>
            <p class="text-sm text-delos-muted">Here's what's happening with your site today.</p>
        </div>
        <a href="{{ route('admin.self-test') }}"
           class="flex-none inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-delos-gold/10 border border-delos-dark/10 hover:border-delos-gold/50 text-delos-dark-2 rounded-lg text-[11px] font-semibold tracking-[0.2em] uppercase transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            Run Self-Test
        </a>
    </div>

    {{-- Stats grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @foreach($stats as $stat)
            <div class="bg-white rounded-xl p-5 border border-delos-dark/5 shadow-card hover:shadow-card-hover transition-all duration-300 group">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br {{ $stat['accent'] }} flex items-center justify-center text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            {!! $icons[$stat['icon']] !!}
                        </svg>
                    </div>
                    <span class="text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium">Total</span>
                </div>
                <div class="font-serif text-3xl text-delos-dark-2 font-light mb-1">{{ $stat['count'] }}</div>
                <p class="text-sm text-delos-muted">{{ $stat['label'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Quick actions --}}
    <div class="bg-white rounded-xl border border-delos-dark/5 shadow-card overflow-hidden">
        <div class="px-6 py-4 border-b border-delos-dark/5">
            <h3 class="font-serif text-lg text-delos-dark-2 font-medium">Quick Actions</h3>
        </div>
        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            @foreach([
                ['label' => 'Add Employee', 'desc' => 'New team member profile', 'route' => 'admin.employees.create'],
                ['label' => 'Add Project', 'desc' => 'Showcase a completed project', 'route' => 'admin.projects.create'],
                ['label' => 'Add Brand', 'desc' => 'Italian partner brand', 'route' => 'admin.brands.create'],
                ['label' => 'Add Service', 'desc' => 'What Delos offers', 'route' => 'admin.services.create'],
                ['label' => 'Site Settings', 'desc' => 'Phone, email, social links', 'route' => 'admin.site-settings.index'],
            ] as $action)
                <a href="{{ route($action['route']) }}" class="flex items-start gap-3 p-4 rounded-lg border border-delos-dark/8 hover:border-delos-gold/40 hover:bg-delos-gold/5 transition-all duration-200 text-left group">
                    <div class="w-9 h-9 rounded-lg bg-delos-gold/10 flex items-center justify-center text-delos-gold flex-shrink-0 group-hover:bg-delos-gold/20 transition-colors">
                        @if(str_starts_with($action['label'], 'Add'))
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        @else
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-delos-dark-2 group-hover:text-delos-gold-dark transition-colors">{{ $action['label'] }}</p>
                        <p class="text-xs text-delos-muted mt-0.5">{{ $action['desc'] }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endsection
