@php
    $isEdit = $project->exists;
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Main content --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Multilingual title + type label --}}
        <div class="bg-white rounded-xl border border-delos-dark/5 shadow-card" x-data="{ tab: 'en' }">

            <div class="px-5 py-3 border-b border-delos-dark/5 flex items-center gap-1">
                <h3 class="font-serif text-base text-delos-dark-2 font-medium flex-1">Content</h3>
                <div class="flex items-center gap-1 p-1 bg-delos-ivory/50 rounded-lg">
                    <button type="button" @click="tab = 'en'" :class="tab === 'en' ? 'bg-white shadow-sm text-delos-dark-2' : 'text-delos-muted hover:text-delos-dark-2'" class="px-3 py-1 rounded-md text-[11px] tracking-[0.15em] uppercase font-semibold transition-all">EN</button>
                    <button type="button" @click="tab = 'ar'" :class="tab === 'ar' ? 'bg-white shadow-sm text-delos-dark-2' : 'text-delos-muted hover:text-delos-dark-2'" class="px-3 py-1 rounded-md text-[11px] tracking-[0.15em] uppercase font-semibold transition-all">AR</button>
                    <button type="button" @click="tab = 'it'" :class="tab === 'it' ? 'bg-white shadow-sm text-delos-dark-2' : 'text-delos-muted hover:text-delos-dark-2'" class="px-3 py-1 rounded-md text-[11px] tracking-[0.15em] uppercase font-semibold transition-all">IT</button>
                </div>
            </div>

            {{-- English --}}
            <div x-show="tab === 'en'" class="p-5 space-y-4">
                <div>
                    <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title_en" value="{{ old('title_en', $project->title_en) }}" required
                           class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                    @error('title_en')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Type label</label>
                    <input type="text" name="type_label_en" value="{{ old('type_label_en', $project->type_label_en) }}" placeholder="e.g. Kitchens"
                           class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                    @error('type_label_en')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Arabic --}}
            <div x-show="tab === 'ar'" x-cloak class="p-5 space-y-4">
                <div>
                    <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">العنوان (Title)</label>
                    <input type="text" name="title_ar" dir="rtl" value="{{ old('title_ar', $project->title_ar) }}"
                           class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all"
                           style="font-family: 'Cairo', sans-serif;">
                    @error('title_ar')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">تسمية النوع (Type label)</label>
                    <input type="text" name="type_label_ar" dir="rtl" value="{{ old('type_label_ar', $project->type_label_ar) }}"
                           class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all"
                           style="font-family: 'Cairo', sans-serif;">
                    @error('type_label_ar')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Italian --}}
            <div x-show="tab === 'it'" x-cloak class="p-5 space-y-4">
                <div>
                    <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Titolo (Title)</label>
                    <input type="text" name="title_it" value="{{ old('title_it', $project->title_it) }}"
                           class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                    @error('title_it')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Etichetta tipo (Type label)</label>
                    <input type="text" name="type_label_it" value="{{ old('type_label_it', $project->type_label_it) }}"
                           class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                    @error('type_label_it')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Common metadata --}}
        <div class="bg-white rounded-xl border border-delos-dark/5 shadow-card p-5 space-y-4">
            <h3 class="font-serif text-base text-delos-dark-2 font-medium">Details</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">City</label>
                    <input type="text" name="city" value="{{ old('city', $project->city) }}" placeholder="Erbil, Baghdad, ..."
                           class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                    @error('city')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Year</label>
                    <input type="number" name="year" value="{{ old('year', $project->year) }}" min="1980" max="2100" placeholder="2024"
                           class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                    @error('year')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Type key</label>
                    <input type="text" name="type" value="{{ old('type', $project->type) }}" placeholder="kitchens, bedroom, turnkey, ..."
                           class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                    <p class="text-xs text-delos-muted mt-1">Used by the public filter. Keep consistent across projects.</p>
                    @error('type')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Brand</label>
                    <input type="text" name="brand" value="{{ old('brand', $project->brand) }}" placeholder="LUBE, CANTORI, ..."
                           class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                    @error('brand')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">

        {{-- Settings --}}
        <div class="bg-white rounded-xl border border-delos-dark/5 shadow-card p-5 space-y-4">
            <h3 class="font-serif text-base text-delos-dark-2 font-medium">Settings</h3>

            <div>
                <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Display order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $project->sort_order ?? 0) }}" min="0" max="9999"
                       class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                <p class="text-xs text-delos-muted mt-1">Lower numbers appear first</p>
            </div>

            <label class="flex items-center justify-between cursor-pointer p-3 bg-delos-ivory/40 rounded-lg">
                <div>
                    <div class="text-sm font-medium text-delos-dark-2">Featured</div>
                    <div class="text-xs text-delos-muted">Show in the homepage hero</div>
                </div>
                <input type="hidden" name="featured" value="0">
                <div x-data="{ on: {{ old('featured', $project->featured ?? false) ? 'true' : 'false' }} }" class="relative">
                    <input type="checkbox" name="featured" value="1" x-model="on" class="sr-only peer">
                    <div @click="on = !on" class="w-10 h-5 rounded-full bg-delos-dark/15 transition-colors cursor-pointer relative"
                         :class="on ? 'bg-delos-gold' : 'bg-delos-dark/15'">
                        <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full transition-transform"
                             :class="on ? 'translate-x-5' : 'translate-x-0'"></div>
                    </div>
                </div>
            </label>

            <label class="flex items-center justify-between cursor-pointer p-3 bg-delos-ivory/40 rounded-lg">
                <div>
                    <div class="text-sm font-medium text-delos-dark-2">Active</div>
                    <div class="text-xs text-delos-muted">Show on public site</div>
                </div>
                <input type="hidden" name="active" value="0">
                <div x-data="{ on: {{ old('active', $project->active ?? true) ? 'true' : 'false' }} }" class="relative">
                    <input type="checkbox" name="active" value="1" x-model="on" class="sr-only peer">
                    <div @click="on = !on" class="w-10 h-5 rounded-full bg-delos-dark/15 transition-colors cursor-pointer relative"
                         :class="on ? 'bg-delos-gold' : 'bg-delos-dark/15'">
                        <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full transition-transform"
                             :class="on ? 'translate-x-5' : 'translate-x-0'"></div>
                    </div>
                </div>
            </label>
        </div>

        {{-- Image --}}
        <div class="bg-white rounded-xl border border-delos-dark/5 shadow-card p-5" x-data="{
            preview: {{ $project->image_url ? json_encode($project->image_url) : 'null' }},
            removing: false,
            onFile(event) {
                const file = event.target.files[0];
                if (!file) return;
                this.removing = false;
                const reader = new FileReader();
                reader.onload = e => this.preview = e.target.result;
                reader.readAsDataURL(file);
            },
            clear() {
                this.preview = null;
                this.removing = true;
                this.$refs.file.value = '';
            }
        }">
            <h3 class="font-serif text-base text-delos-dark-2 font-medium mb-3">Photo</h3>
            <input type="hidden" name="remove_image" :value="removing ? '1' : '0'">

            <div x-show="preview" class="mb-3">
                <div class="aspect-[4/3] rounded-lg overflow-hidden bg-delos-ivory/60 border border-delos-dark/8">
                    <img :src="preview" alt="" class="w-full h-full object-cover">
                </div>
                <button type="button" @click="clear()" class="mt-2 text-xs text-red-600 hover:text-red-800 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Remove image
                </button>
            </div>

            <label x-show="!preview" class="block border-2 border-dashed border-delos-dark/15 hover:border-delos-gold rounded-lg p-6 text-center cursor-pointer transition-colors">
                <svg class="w-10 h-10 mx-auto text-delos-muted mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <p class="text-sm text-delos-dark-2 font-medium mb-1">Click to upload</p>
                <p class="text-xs text-delos-muted">JPG, PNG, WebP · max 5MB</p>
                <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="sr-only" @change="onFile" x-ref="file">
            </label>

            @error('image')<p class="text-xs text-red-600 mt-2">{{ $message }}</p>@enderror
        </div>

        {{-- Actions --}}
        <div class="flex flex-col gap-2">
            <button type="submit" class="w-full py-3 bg-delos-gold hover:bg-delos-gold-light text-delos-dark-2 rounded-lg text-xs font-semibold tracking-[0.2em] uppercase transition-colors">
                {{ $isEdit ? 'Save Changes' : 'Create Project' }}
            </button>
            <a href="{{ route('admin.projects.index') }}" class="w-full py-3 text-center text-delos-muted hover:text-delos-dark-2 text-xs font-medium tracking-[0.15em] uppercase transition-colors">
                Cancel
            </a>
        </div>
    </div>
</div>

<style>[x-cloak] { display: none !important; }</style>
