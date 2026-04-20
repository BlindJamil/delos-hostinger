@extends('admin.layout')

@section('title', 'Admin Users')
@section('page-title', 'Admin Users')
@section('page-subtitle', 'People who can access this panel')

@section('page-actions')
    <a href="{{ route('admin.admin-users.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-delos-gold hover:bg-delos-gold-light text-delos-dark-2 rounded-lg text-xs font-semibold tracking-[0.15em] uppercase transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New Admin
    </a>
@endsection

@section('content')
    <div class="bg-white rounded-xl border border-delos-dark/5 shadow-card overflow-hidden">

        <div class="px-5 py-3 bg-delos-ivory/40 border-b border-delos-dark/5 text-xs text-delos-muted">
            <strong class="text-delos-dark-2/80">{{ $admins->count() }}</strong> admin{{ $admins->count() === 1 ? '' : 's' }} ·
            <strong class="text-delos-dark-2/80">{{ $admins->where('is_super', true)->count() }}</strong> super
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-delos-dark/5 text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium">
                        <th class="px-5 py-3 text-left font-medium">Admin</th>
                        <th class="px-5 py-3 text-left font-medium">Email</th>
                        <th class="px-5 py-3 text-center font-medium w-24">Role</th>
                        <th class="px-5 py-3 text-left font-medium">Last login</th>
                        <th class="px-5 py-3 text-right font-medium w-32">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-delos-dark/5">
                    @foreach($admins as $admin)
                        @php
                            $isSelf = $admin->id === auth('admin')->id();
                            $initials = collect(explode(' ', trim($admin->name)))->take(2)->map(fn($w) => mb_substr($w, 0, 1))->join('');
                        @endphp
                        <tr class="hover:bg-delos-ivory/30 transition-colors">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-delos-gold/15 flex items-center justify-center flex-shrink-0">
                                        <span class="text-xs font-semibold text-delos-gold">{{ strtoupper($initials ?: substr($admin->email, 0, 2)) }}</span>
                                    </div>
                                    <div>
                                        <div class="font-medium text-delos-dark-2 flex items-center gap-2">
                                            {{ $admin->name }}
                                            @if($isSelf)
                                                <span class="text-[9px] tracking-[0.15em] uppercase text-delos-gold bg-delos-gold/10 px-1.5 py-0.5 rounded">You</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-delos-muted">Joined {{ $admin->created_at?->format('M j, Y') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-delos-dark-2/80 font-mono text-xs">{{ $admin->email }}</td>
                            <td class="px-5 py-3 text-center">
                                @if($admin->is_super)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-medium bg-delos-gold/10 text-delos-gold">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                        Super
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-medium bg-delos-dark/5 text-delos-muted">Admin</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-delos-muted text-xs">
                                @if($admin->last_login_at)
                                    <div>{{ $admin->last_login_at->diffForHumans() }}</div>
                                    @if($admin->last_login_ip)
                                        <div class="font-mono mt-0.5">{{ $admin->last_login_ip }}</div>
                                    @endif
                                @else
                                    <span class="text-delos-muted/60">Never</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('admin.admin-users.edit', $admin) }}" class="p-2 text-delos-muted hover:text-delos-gold hover:bg-delos-gold/10 rounded-lg transition-colors" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    @unless($isSelf)
                                        <form method="POST" action="{{ route('admin.admin-users.destroy', $admin) }}" class="inline" onsubmit="return confirm('Delete {{ addslashes($admin->email) }}? They will lose access immediately.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-delos-muted hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @endunless
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6 p-4 bg-delos-ivory/30 border border-delos-dark/5 rounded-xl text-xs text-delos-muted leading-relaxed">
        <strong class="text-delos-dark-2/80">Password policy:</strong> at least 12 characters, including letters and symbols.
        <strong class="text-delos-dark-2/80 ml-4">Super admin</strong> can manage other admins; regular admins can edit content only.
        <strong class="text-delos-dark-2/80 ml-4">Lockout:</strong> 5 failed login attempts from one IP triggers a 15-minute ban.
    </div>
@endsection
