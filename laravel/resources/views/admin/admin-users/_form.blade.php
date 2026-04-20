@php
    $isEdit = $adminUser->exists;
    $isSelf = $isEdit && $adminUser->id === auth('admin')->id();
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-2 space-y-6">

        {{-- Identity --}}
        <div class="bg-white rounded-xl border border-delos-dark/5 shadow-card p-5 space-y-4">
            <h3 class="font-serif text-base text-delos-dark-2 font-medium">Identity</h3>

            <div>
                <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $adminUser->name) }}" required placeholder="Ada Lovelace"
                       class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email', $adminUser->email) }}" required placeholder="name@delos.com"
                       class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm font-mono focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                <p class="text-xs text-delos-muted mt-1">Used to sign in</p>
                @error('email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Password --}}
        <div class="bg-white rounded-xl border border-delos-dark/5 shadow-card p-5 space-y-4">
            <h3 class="font-serif text-base text-delos-dark-2 font-medium">
                {{ $isEdit ? 'Change password' : 'Set password' }}
            </h3>
            @if($isEdit)
                <p class="text-xs text-delos-muted">Leave blank to keep the current password. A dedicated reset form is also available from the sidebar on this page.</p>
            @endif

            <div>
                <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Password @if(!$isEdit)<span class="text-red-500">*</span>@endif</label>
                <input type="password" name="password" autocomplete="new-password" @if(!$isEdit) required @endif
                       class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm font-mono focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                <p class="text-xs text-delos-muted mt-1">Min 12 chars · letters · symbols</p>
                @error('password')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Confirm password @if(!$isEdit)<span class="text-red-500">*</span>@endif</label>
                <input type="password" name="password_confirmation" autocomplete="new-password" @if(!$isEdit) required @endif
                       class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm font-mono focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">

        {{-- Role --}}
        <div class="bg-white rounded-xl border border-delos-dark/5 shadow-card p-5 space-y-4">
            <h3 class="font-serif text-base text-delos-dark-2 font-medium">Role</h3>

            <label class="flex items-start gap-3 cursor-pointer p-3 bg-delos-ivory/40 rounded-lg {{ $isSelf ? 'opacity-70 cursor-not-allowed' : '' }}">
                <input type="hidden" name="is_super" value="0">
                <input type="checkbox" name="is_super" value="1"
                       {{ old('is_super', $adminUser->is_super ?? false) ? 'checked' : '' }}
                       {{ $isSelf ? 'disabled' : '' }}
                       class="mt-0.5 w-4 h-4 text-delos-gold border-delos-dark/20 rounded focus:ring-delos-gold">
                <div>
                    <div class="text-sm font-medium text-delos-dark-2 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-delos-gold" fill="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Super admin
                    </div>
                    <div class="text-xs text-delos-muted mt-0.5">
                        Can manage admin users, reset passwords, and delete other admins.
                        @if($isSelf)
                            <br><span class="text-delos-gold">You cannot remove this from your own account.</span>
                        @endif
                    </div>
                </div>
            </label>

            @error('is_super')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Meta (edit only) --}}
        @if($isEdit)
            <div class="bg-white rounded-xl border border-delos-dark/5 shadow-card p-5 space-y-2 text-xs">
                <h3 class="font-serif text-base text-delos-dark-2 font-medium mb-3">Activity</h3>
                <div class="flex items-center justify-between text-delos-muted">
                    <span>Last login</span>
                    <span class="text-delos-dark-2/80">
                        {{ $adminUser->last_login_at?->diffForHumans() ?? 'Never' }}
                    </span>
                </div>
                @if($adminUser->last_login_ip)
                    <div class="flex items-center justify-between text-delos-muted">
                        <span>Last IP</span>
                        <span class="font-mono text-delos-dark-2/80">{{ $adminUser->last_login_ip }}</span>
                    </div>
                @endif
                <div class="flex items-center justify-between text-delos-muted">
                    <span>Created</span>
                    <span class="text-delos-dark-2/80">{{ $adminUser->created_at?->format('M j, Y') }}</span>
                </div>
            </div>
        @endif

        {{-- Actions --}}
        <div class="flex flex-col gap-2">
            <button type="submit" class="w-full py-3 bg-delos-gold hover:bg-delos-gold-light text-delos-dark-2 rounded-lg text-xs font-semibold tracking-[0.2em] uppercase transition-colors">
                {{ $isEdit ? 'Save Changes' : 'Create Admin' }}
            </button>
            <a href="{{ route('admin.admin-users.index') }}" class="w-full py-3 text-center text-delos-muted hover:text-delos-dark-2 text-xs font-medium tracking-[0.15em] uppercase transition-colors">
                Cancel
            </a>
        </div>
    </div>
</div>

{{-- Standalone password-reset form (outside the main form) --}}
@if($isEdit)
    @push('after-content')
        <form action="{{ route('admin.admin-users.reset-password', $adminUser) }}" method="POST" class="mt-6 bg-white rounded-xl border border-delos-dark/5 shadow-card p-5 max-w-2xl">
            @csrf
            <h3 class="font-serif text-base text-delos-dark-2 font-medium mb-1">Quick password reset</h3>
            <p class="text-xs text-delos-muted mb-4">Force a new password without touching any other fields.</p>
            <div class="flex gap-3">
                <input type="password" name="password" required placeholder="New password"
                       class="flex-1 px-3 py-2 border border-delos-dark/15 rounded-lg text-sm font-mono focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                <input type="password" name="password_confirmation" required placeholder="Confirm"
                       class="flex-1 px-3 py-2 border border-delos-dark/15 rounded-lg text-sm font-mono focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                <button type="submit" class="px-5 py-2 bg-delos-dark-2 hover:bg-delos-dark text-delos-cream rounded-lg text-xs font-semibold tracking-[0.15em] uppercase transition-colors whitespace-nowrap">
                    Reset
                </button>
            </div>
            @error('password')<p class="text-xs text-red-600 mt-2">{{ $message }}</p>@enderror
        </form>
    @endpush
@endif
