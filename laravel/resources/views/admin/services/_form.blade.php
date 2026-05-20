@php
    $isEdit = $service->exists;
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-2 space-y-6">

        {{-- Service identity --}}
        <div class="bg-white rounded-xl border border-delos-dark/5 shadow-card p-5 space-y-4">
            <h3 class="font-serif text-base text-delos-dark-2 font-medium">Service identity</h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Number</label>
                    <input type="text" name="num" value="{{ old('num', $service->num) }}" placeholder="01" maxlength="10"
                           class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm font-mono focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                    <p class="text-xs text-delos-muted mt-1">Big serif "01" shown on cards</p>
                    @error('num')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Slug <span class="text-red-500">*</span></label>
                    <input type="text" name="slug" value="{{ old('slug', $service->slug) }}" required placeholder="italian-kitchens"
                           class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm font-mono focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                    <p class="text-xs text-delos-muted mt-1">Lowercase letters, numbers, dashes. Unique across services.</p>
                    @error('slug')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Brand attribution</label>
                <input type="text" name="brand" value="{{ old('brand', $service->brand) }}" placeholder="LUBE Kitchens"
                       class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                <p class="text-xs text-delos-muted mt-1">Free-form. e.g. "LUBE · FAER AMBIENTI", "All Delos Partners".</p>
                @error('brand')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Multilingual content --}}
        <div class="bg-white rounded-xl border border-delos-dark/5 shadow-card" x-data="{ tab: 'en' }">
            <div class="px-5 py-3 border-b border-delos-dark/5 flex items-center gap-1">
                <h3 class="font-serif text-base text-delos-dark-2 font-medium flex-1">Content</h3>
                <div class="flex items-center gap-1 p-1 bg-delos-ivory/50 rounded-lg">
                    <button type="button" @click="tab = 'en'" :class="tab === 'en' ? 'bg-white shadow-sm text-delos-dark-2' : 'text-delos-muted hover:text-delos-dark-2'" class="px-3 py-1 rounded-md text-[11px] tracking-[0.15em] uppercase font-semibold transition-all">EN</button>
                    <button type="button" @click="tab = 'ar'" :class="tab === 'ar' ? 'bg-white shadow-sm text-delos-dark-2' : 'text-delos-muted hover:text-delos-dark-2'" class="px-3 py-1 rounded-md text-[11px] tracking-[0.15em] uppercase font-semibold transition-all">AR</button>
                    <button type="button" @click="tab = 'it'" :class="tab === 'it' ? 'bg-white shadow-sm text-delos-dark-2' : 'text-delos-muted hover:text-delos-dark-2'" class="px-3 py-1 rounded-md text-[11px] tracking-[0.15em] uppercase font-semibold transition-all">IT</button>
                    <button type="button" @click="tab = 'ku'" :class="tab === 'ku' ? 'bg-white shadow-sm text-delos-dark-2' : 'text-delos-muted hover:text-delos-dark-2'" class="px-3 py-1 rounded-md text-[11px] tracking-[0.15em] uppercase font-semibold transition-all">KU</button>
                </div>
            </div>

            @foreach([
                'en' => ['Name', 'Description', 'Features (one per line)', 'ltr', '', true],
                'ar' => ['الاسم', 'الوصف', 'الميزات (واحدة في كلّ سطر)', 'rtl', "font-family: 'Cairo', sans-serif;", false],
                'it' => ['Nome', 'Descrizione', 'Caratteristiche (una per riga)', 'ltr', '', false],
                'ku' => ['ناو', 'وەسف', 'تایبەتمەندییەکان (هەرکامێ لە دێڕێکدا)', 'rtl', "font-family: 'Cairo', sans-serif;", false],
            ] as $lang => $labels)
                @php
                    [$nameLbl, $descLbl, $featLbl, $dir, $style, $required] = $labels;
                    $showAttr = $lang === 'en' ? '' : 'x-cloak';
                @endphp
                <div x-show="tab === '{{ $lang }}'" {{ $showAttr }} class="p-5 space-y-4">
                    <div>
                        <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">{{ $nameLbl }} @if($required)<span class="text-red-500">*</span>@endif</label>
                        <input type="text" name="name_{{ $lang }}" dir="{{ $dir }}" value="{{ old("name_{$lang}", $service->{"name_{$lang}"}) }}" style="{{ $style }}" @if($required) required @endif
                               class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                        @error("name_{$lang}")<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">{{ $descLbl }}</label>
                        <textarea name="description_{{ $lang }}" dir="{{ $dir }}" rows="5" style="{{ $style }}"
                                  class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all resize-y">{{ old("description_{$lang}", $service->{"description_{$lang}"}) }}</textarea>
                        @error("description_{$lang}")<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div x-data="{
                        items: @js($service->{"features_{$lang}"} ?? []),
                        add() { this.items.push(''); },
                        remove(i) { this.items.splice(i, 1); },
                    }">
                        <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">{{ $featLbl }}</label>
                        <div class="space-y-2">
                            <template x-for="(item, i) in items" :key="i">
                                <div class="flex items-center gap-2">
                                    <input type="text" :name="`features_{{ $lang }}[]`" x-model="items[i]" dir="{{ $dir }}" style="{{ $style }}"
                                           class="flex-1 px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                                    <button type="button" @click="remove(i)" class="p-2 text-delos-muted hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Remove">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                        <button type="button" @click="add()" class="mt-2 inline-flex items-center gap-1 px-3 py-1.5 text-xs text-delos-gold hover:bg-delos-gold/10 rounded-lg transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Add feature
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">

        <div class="bg-white rounded-xl border border-delos-dark/5 shadow-card p-5 space-y-4">
            <h3 class="font-serif text-base text-delos-dark-2 font-medium">Settings</h3>

            <div>
                <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Display order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $service->sort_order ?? 0) }}" min="0" max="9999"
                       class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                <p class="text-xs text-delos-muted mt-1">Lower numbers appear first</p>
            </div>

            <label class="flex items-center justify-between cursor-pointer p-3 bg-delos-ivory/40 rounded-lg">
                <div>
                    <div class="text-sm font-medium text-delos-dark-2">Active</div>
                    <div class="text-xs text-delos-muted">Show on public site</div>
                </div>
                <input type="hidden" name="active" value="0">
                <div x-data="{ on: {{ old('active', $service->active ?? true) ? 'true' : 'false' }} }" class="relative">
                    <input type="checkbox" name="active" value="1" x-model="on" class="sr-only peer">
                    <div @click="on = !on" class="w-10 h-5 rounded-full bg-delos-dark/15 transition-colors cursor-pointer relative"
                         :class="on ? 'bg-delos-gold' : 'bg-delos-dark/15'">
                        <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full transition-transform"
                             :class="on ? 'translate-x-5' : 'translate-x-0'"></div>
                    </div>
                </div>
            </label>
        </div>

        <div class="bg-white rounded-xl border border-delos-dark/5 shadow-card p-5">
            <h3 class="font-serif text-base text-delos-dark-2 font-medium mb-3">Image</h3>
            <x-admin.image-upload
                label="Service image"
                :current-url="$service->image_url"
                :mobile-current-url="$service->mobile_image_url"
                :focal="$service->focal_point"
            />
            @error('image')<p class="text-xs text-red-600 mt-2">{{ $message }}</p>@enderror
            @error('image_mobile')<p class="text-xs text-red-600 mt-2">{{ $message }}</p>@enderror
            @error('focal_point')<p class="text-xs text-red-600 mt-2">{{ $message }}</p>@enderror
        </div>

        <div class="flex flex-col gap-2">
            <button type="submit" class="w-full py-3 bg-delos-gold hover:bg-delos-gold-light text-delos-dark-2 rounded-lg text-xs font-semibold tracking-[0.2em] uppercase transition-colors">
                {{ $isEdit ? 'Save Changes' : 'Create Service' }}
            </button>
            <a href="{{ route('admin.services.index') }}" class="w-full py-3 text-center text-delos-muted hover:text-delos-dark-2 text-xs font-medium tracking-[0.15em] uppercase transition-colors">
                Cancel
            </a>
        </div>
    </div>
</div>

<style>[x-cloak] { display: none !important; }</style>
