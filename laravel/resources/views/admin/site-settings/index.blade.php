@extends('admin.layout')

@section('title', 'Site Settings')
@section('page-title', 'Site Settings')
@section('page-subtitle', 'Global values used across the public site')

@section('content')
    @php
        $groupLabels = [
            'general' => 'General',
            'contact' => 'Contact',
            'social' => 'Social Media',
            'seo' => 'SEO',
            'custom' => 'Custom',
        ];
        $typeIcons = [
            'text' => 'T',
            'textarea' => '¶',
            'url' => '🔗',
            'email' => '@',
            'phone' => '☎',
        ];
    @endphp

    {{-- Bulk edit form --}}
    <form method="POST" action="{{ route('admin.site-settings.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        @foreach($orderedGroups as $group)
            <div class="bg-white rounded-xl border border-delos-dark/5 shadow-card overflow-hidden">
                <div class="px-5 py-4 border-b border-delos-dark/5 flex items-center justify-between">
                    <div>
                        <h2 class="font-serif text-lg text-delos-dark-2 font-medium">{{ $groupLabels[$group] ?? ucfirst($group) }}</h2>
                        <p class="text-xs text-delos-muted mt-0.5">{{ $settings[$group]->count() }} setting{{ $settings[$group]->count() === 1 ? '' : 's' }}</p>
                    </div>
                    <span class="text-[10px] tracking-[0.2em] uppercase text-delos-muted font-mono">{{ $group }}</span>
                </div>

                <div class="divide-y divide-delos-dark/5">
                    @foreach($settings[$group] as $setting)
                        @php
                            $isLocalized = !in_array($setting->type, $nonLocalizedTypes, true);
                            $inputTag = $setting->type === 'textarea' ? 'textarea' : 'input';
                            $inputType = match($setting->type) {
                                'email' => 'email',
                                'url' => 'url',
                                'phone' => 'tel',
                                default => 'text',
                            };
                        @endphp
                        <div class="px-5 py-4" x-data="{ expanded: {{ $isLocalized ? 'false' : 'true' }} }">
                            <div class="flex items-start gap-4">
                                {{-- Label column --}}
                                <div class="flex-shrink-0 w-56 pt-2">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="inline-flex items-center justify-center w-6 h-6 text-[11px] font-semibold text-delos-gold bg-delos-gold/10 rounded">{{ $typeIcons[$setting->type] ?? '•' }}</span>
                                        <label class="text-sm font-medium text-delos-dark-2">{{ $setting->label ?: $setting->key }}</label>
                                    </div>
                                    <p class="text-xs text-delos-muted font-mono pl-8">{{ $setting->key }}</p>
                                    <p class="text-[10px] tracking-[0.15em] uppercase text-delos-muted/70 mt-1 pl-8">
                                        {{ $setting->type }}{{ $isLocalized ? ' · multilingual' : '' }}
                                    </p>
                                </div>

                                {{-- Value column --}}
                                <div class="flex-1 space-y-2">
                                    @if($isLocalized)
                                        {{-- EN / AR / IT triplet --}}
                                        @foreach(['en' => ['EN', 'ltr', ''], 'ar' => ['AR', 'rtl', "font-family: 'Cairo', sans-serif;"], 'it' => ['IT', 'ltr', ''], 'ku' => ['KU', 'rtl', "font-family: 'Cairo', sans-serif;"]] as $lang => $meta)
                                            @php
                                                [$flag, $dir, $style] = $meta;
                                                $val = $setting->{"value_{$lang}"};
                                            @endphp
                                            <div class="flex items-start gap-2">
                                                <span class="flex-shrink-0 inline-flex items-center justify-center w-10 h-9 text-[10px] tracking-[0.15em] font-semibold text-delos-muted bg-delos-ivory/60 rounded">{{ $flag }}</span>
                                                @if($setting->type === 'textarea')
                                                    <textarea name="settings[{{ $setting->id }}][value_{{ $lang }}]" dir="{{ $dir }}" rows="3" style="{{ $style }}"
                                                        class="flex-1 px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all resize-y">{{ old("settings.{$setting->id}.value_{$lang}", $val) }}</textarea>
                                                @else
                                                    <input type="text" name="settings[{{ $setting->id }}][value_{{ $lang }}]" dir="{{ $dir }}" value="{{ old("settings.{$setting->id}.value_{$lang}", $val) }}" style="{{ $style }}"
                                                        class="flex-1 px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                                                @endif
                                            </div>
                                        @endforeach
                                    @else
                                        {{-- Single value (URL / email / phone) --}}
                                        <input type="{{ $inputType }}" name="settings[{{ $setting->id }}][value_en]" value="{{ old("settings.{$setting->id}.value_en", $setting->value_en) }}"
                                            @if($setting->type === 'url') placeholder="https://..." @endif
                                            @if($setting->type === 'email') placeholder="you@delos.com" @endif
                                            @if($setting->type === 'phone') placeholder="0750 100 1701" @endif
                                            class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                                    @endif
                                </div>

                                {{-- Delete button --}}
                                <div class="flex-shrink-0 pt-2">
                                    <button type="button"
                                        onclick="event.preventDefault(); if(confirm('Remove setting &quot;{{ addslashes($setting->key) }}&quot;?')) document.getElementById('delete-setting-{{ $setting->id }}').submit();"
                                        class="p-2 text-delos-muted hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Remove this setting">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="flex items-center justify-end gap-3 sticky bottom-4 bg-delos-ivory/95 backdrop-blur rounded-xl border border-delos-dark/5 shadow-lg px-5 py-3">
            <p class="text-xs text-delos-muted flex-1">Changes apply instantly to the public site after saving.</p>
            <button type="submit" class="px-6 py-2.5 bg-delos-gold hover:bg-delos-gold-light text-delos-dark-2 rounded-lg text-xs font-semibold tracking-[0.2em] uppercase transition-colors">
                Save Settings
            </button>
        </div>
    </form>

    {{-- Separate delete forms (sent via the trash-can button above) --}}
    @foreach($settings->flatten(1) as $setting)
        <form id="delete-setting-{{ $setting->id }}" method="POST" action="{{ route('admin.site-settings.destroy', $setting) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endforeach

    {{-- Add custom setting card --}}
    <div class="bg-white rounded-xl border border-delos-dark/5 shadow-card overflow-hidden mt-6" x-data="{ open: false }">
        <button type="button" @click="open = !open" class="w-full flex items-center justify-between px-5 py-4 text-left hover:bg-delos-ivory/30 transition-colors">
            <div>
                <h2 class="font-serif text-lg text-delos-dark-2 font-medium">Add custom setting</h2>
                <p class="text-xs text-delos-muted mt-0.5">Define a new key for any value you need (e.g. <span class="font-mono">google_analytics_id</span>, <span class="font-mono">footer_copyright</span>)</p>
            </div>
            <svg class="w-5 h-5 text-delos-muted transition-transform" :class="open ? 'rotate-45' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
        </button>

        <form x-show="open" x-cloak method="POST" action="{{ route('admin.site-settings.store') }}" class="px-5 py-5 border-t border-delos-dark/5 space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Key <span class="text-red-500">*</span></label>
                    <input type="text" name="key" required placeholder="my_new_key" value="{{ old('key') }}"
                        class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm font-mono focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                    <p class="text-xs text-delos-muted mt-1">Lowercase letters, numbers, underscores</p>
                    @error('key')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Label <span class="text-red-500">*</span></label>
                    <input type="text" name="label" required placeholder="My New Setting" value="{{ old('label') }}"
                        class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                    @error('label')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Type <span class="text-red-500">*</span></label>
                    <select name="type" required
                        class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 cursor-pointer">
                        <option value="text" @selected(old('type') === 'text')>Text (short, multilingual)</option>
                        <option value="textarea" @selected(old('type') === 'textarea')>Textarea (long, multilingual)</option>
                        <option value="url" @selected(old('type') === 'url')>URL (single)</option>
                        <option value="email" @selected(old('type') === 'email')>Email (single)</option>
                        <option value="phone" @selected(old('type') === 'phone')>Phone (single)</option>
                    </select>
                    @error('type')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Group <span class="text-red-500">*</span></label>
                    <input type="text" name="group" required list="group-options" placeholder="general" value="{{ old('group', 'custom') }}"
                        class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm font-mono focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                    <datalist id="group-options">
                        <option value="general">
                        <option value="contact">
                        <option value="social">
                        <option value="seo">
                        <option value="custom">
                    </datalist>
                    @error('group')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Initial value (optional)</label>
                <input type="text" name="value_en" value="{{ old('value_en') }}" placeholder="Leave blank — edit after creating"
                    class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                @error('value_en')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="px-5 py-2 bg-delos-gold hover:bg-delos-gold-light text-delos-dark-2 rounded-lg text-xs font-semibold tracking-[0.2em] uppercase transition-colors">
                    Add setting
                </button>
                <button type="button" @click="open = false" class="px-5 py-2 text-delos-muted hover:text-delos-dark-2 text-xs font-medium tracking-[0.15em] uppercase transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>

    <style>[x-cloak] { display: none !important; }</style>
@endsection
