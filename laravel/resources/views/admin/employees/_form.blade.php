@php
    $isEdit = $employee->exists;
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Main content: 2 columns on desktop --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Multilingual content card --}}
        <div class="bg-white rounded-xl border border-delos-dark/5 shadow-card" x-data="{ tab: 'en' }">

            <div class="px-5 py-3 border-b border-delos-dark/5 flex items-center gap-1">
                <h3 class="font-serif text-base text-delos-dark-2 font-medium flex-1">Content</h3>
                <div class="flex items-center gap-1 p-1 bg-delos-ivory/50 rounded-lg">
                    <button type="button" @click="tab = 'en'"
                            :class="tab === 'en' ? 'bg-white shadow-sm text-delos-dark-2' : 'text-delos-muted hover:text-delos-dark-2'"
                            class="px-3 py-1 rounded-md text-[11px] tracking-[0.15em] uppercase font-semibold transition-all">EN</button>
                    <button type="button" @click="tab = 'ar'"
                            :class="tab === 'ar' ? 'bg-white shadow-sm text-delos-dark-2' : 'text-delos-muted hover:text-delos-dark-2'"
                            class="px-3 py-1 rounded-md text-[11px] tracking-[0.15em] uppercase font-semibold transition-all">AR</button>
                    <button type="button" @click="tab = 'it'"
                            :class="tab === 'it' ? 'bg-white shadow-sm text-delos-dark-2' : 'text-delos-muted hover:text-delos-dark-2'"
                            class="px-3 py-1 rounded-md text-[11px] tracking-[0.15em] uppercase font-semibold transition-all">IT</button>
                </div>
            </div>

            {{-- English --}}
            <div x-show="tab === 'en'" class="p-5 space-y-4">
                <div>
                    <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name_en" value="{{ old('name_en', $employee->name_en) }}" required
                           class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                    @error('name_en')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Role <span class="text-red-500">*</span></label>
                    <input type="text" name="role_en" value="{{ old('role_en', $employee->role_en) }}" required
                           class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                    @error('role_en')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Achievement</label>
                    @include('admin.partials.rich-text-editor', [
                        'name' => 'achievement_en',
                        'value' => old('achievement_en', $employee->achievement_en ?? ''),
                        'id' => 'achievement_en',
                        'dir' => 'ltr',
                    ])
                    @error('achievement_en')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Arabic --}}
            <div x-show="tab === 'ar'" x-cloak class="p-5 space-y-4">
                <div>
                    <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">الاسم (Name)</label>
                    <input type="text" name="name_ar" dir="rtl" value="{{ old('name_ar', $employee->name_ar) }}"
                           class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all"
                           style="font-family: 'Cairo', sans-serif;">
                    @error('name_ar')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">المسمى الوظيفي (Role)</label>
                    <input type="text" name="role_ar" dir="rtl" value="{{ old('role_ar', $employee->role_ar) }}"
                           class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all"
                           style="font-family: 'Cairo', sans-serif;">
                    @error('role_ar')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">الإنجاز (Achievement)</label>
                    @include('admin.partials.rich-text-editor', [
                        'name' => 'achievement_ar',
                        'value' => old('achievement_ar', $employee->achievement_ar ?? ''),
                        'id' => 'achievement_ar',
                        'dir' => 'rtl',
                    ])
                    @error('achievement_ar')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Italian --}}
            <div x-show="tab === 'it'" x-cloak class="p-5 space-y-4">
                <div>
                    <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Nome (Name)</label>
                    <input type="text" name="name_it" value="{{ old('name_it', $employee->name_it) }}"
                           class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                    @error('name_it')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Ruolo (Role)</label>
                    <input type="text" name="role_it" value="{{ old('role_it', $employee->role_it) }}"
                           class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                    @error('role_it')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Risultato (Achievement)</label>
                    @include('admin.partials.rich-text-editor', [
                        'name' => 'achievement_it',
                        'value' => old('achievement_it', $employee->achievement_it ?? ''),
                        'id' => 'achievement_it',
                        'dir' => 'ltr',
                    ])
                    @error('achievement_it')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

    </div>

    {{-- Sidebar: metadata + image + actions --}}
    <div class="space-y-6">

        {{-- Publish status + sort order --}}
        <div class="bg-white rounded-xl border border-delos-dark/5 shadow-card p-5 space-y-4">
            <h3 class="font-serif text-base text-delos-dark-2 font-medium">Settings</h3>

            <div>
                <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Branch</label>
                <input type="text" name="branch" value="{{ old('branch', $employee->branch) }}" placeholder="Erbil, Kirkuk, ..."
                       class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                @error('branch')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Display order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $employee->sort_order ?? 0) }}" min="0" max="9999"
                       class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                <p class="text-xs text-delos-muted mt-1">Lower numbers appear first</p>
            </div>

            <label class="flex items-center justify-between cursor-pointer p-3 bg-delos-ivory/40 rounded-lg">
                <div>
                    <div class="text-sm font-medium text-delos-dark-2">Active</div>
                    <div class="text-xs text-delos-muted">Show on public site</div>
                </div>
                <input type="hidden" name="active" value="0">
                <div x-data="{ on: {{ old('active', $employee->active ?? true) ? 'true' : 'false' }} }" class="relative">
                    <input type="checkbox" name="active" value="1" x-model="on" class="sr-only peer">
                    <div @click="on = !on"
                         class="w-10 h-5 rounded-full bg-delos-dark/15 peer-checked:bg-delos-gold transition-colors cursor-pointer relative"
                         :class="on ? 'bg-delos-gold' : 'bg-delos-dark/15'">
                        <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full transition-transform"
                             :class="on ? 'translate-x-5' : 'translate-x-0'"></div>
                    </div>
                </div>
            </label>
        </div>

        {{-- Image uploader --}}
        <div class="bg-white rounded-xl border border-delos-dark/5 shadow-card p-5">
            <h3 class="font-serif text-base text-delos-dark-2 font-medium mb-3">Photo</h3>
            <x-admin.image-upload
                label="Employee photo"
                :current-url="$employee->image_url"
                :mobile-current-url="$employee->mobile_image_url"
                :focal="$employee->focal_point"
            />
            @error('image')<p class="text-xs text-red-600 mt-2">{{ $message }}</p>@enderror
            @error('image_mobile')<p class="text-xs text-red-600 mt-2">{{ $message }}</p>@enderror
            @error('focal_point')<p class="text-xs text-red-600 mt-2">{{ $message }}</p>@enderror
        </div>

        {{-- Actions --}}
        <div class="flex flex-col gap-2">
            <button type="submit"
                    class="w-full py-3 bg-delos-gold hover:bg-delos-gold-light text-delos-dark-2 rounded-lg text-xs font-semibold tracking-[0.2em] uppercase transition-colors">
                {{ $isEdit ? 'Save Changes' : 'Create Employee' }}
            </button>
            <a href="{{ route('admin.employees.index') }}"
               class="w-full py-3 text-center text-delos-muted hover:text-delos-dark-2 text-xs font-medium tracking-[0.15em] uppercase transition-colors">
                Cancel
            </a>
        </div>
    </div>
</div>

<style>[x-cloak] { display: none !important; }</style>
