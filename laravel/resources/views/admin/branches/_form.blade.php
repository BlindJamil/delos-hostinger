@php
    $isEdit = $branch->exists;
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <div class="lg:col-span-2 space-y-6">

        {{-- Identity --}}
        <div class="bg-white rounded-xl border border-delos-dark/5 shadow-card p-5 space-y-4">
            <h3 class="font-serif text-base text-delos-dark-2 font-medium">Identity</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">City key <span class="text-red-500">*</span></label>
                    <input type="text" name="city_key" value="{{ old('city_key', $branch->city_key) }}" required placeholder="erbil"
                           class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm font-mono focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                    <p class="text-xs text-delos-muted mt-1">Lowercase identifier used for CSS + anchor links. Don't change after launch.</p>
                    @error('city_key')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Slug <span class="text-red-500">*</span></label>
                    <input type="text" name="slug" value="{{ old('slug', $branch->slug) }}" required placeholder="erbil"
                           class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm font-mono focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                    <p class="text-xs text-delos-muted mt-1">URL-safe identifier.</p>
                    @error('slug')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
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
                </div>
            </div>

            @foreach([
                'en' => ['City name', 'Address', 'Hours', 'Established', 'ltr', '', true],
                'ar' => ['اسم المدينة', 'العنوان', 'ساعات العمل', 'تأسّس', 'rtl', "font-family: 'Cairo', sans-serif;", false],
                'it' => ['Nome città', 'Indirizzo', 'Orari', 'Dal', 'ltr', '', false],
            ] as $lang => $labels)
                @php
                    [$nameLbl, $addrLbl, $hoursLbl, $estLbl, $dir, $style, $required] = $labels;
                    $showAttr = $lang === 'en' ? '' : 'x-cloak';
                @endphp
                <div x-show="tab === '{{ $lang }}'" {{ $showAttr }} class="p-5 space-y-4">
                    <div>
                        <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">{{ $nameLbl }} @if($required)<span class="text-red-500">*</span>@endif</label>
                        <input type="text" name="name_{{ $lang }}" dir="{{ $dir }}" value="{{ old("name_{$lang}", $branch->{"name_{$lang}"}) }}" style="{{ $style }}" @if($required) required @endif
                               class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                        @error("name_{$lang}")<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">{{ $addrLbl }}</label>
                        <input type="text" name="address_{{ $lang }}" dir="{{ $dir }}" value="{{ old("address_{$lang}", $branch->{"address_{$lang}"}) }}" style="{{ $style }}"
                               class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                        @error("address_{$lang}")<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">{{ $hoursLbl }}</label>
                            <input type="text" name="hours_{{ $lang }}" dir="{{ $dir }}" value="{{ old("hours_{$lang}", $branch->{"hours_{$lang}"}) }}" style="{{ $style }}"
                                   class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                            @error("hours_{$lang}")<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">{{ $estLbl }}</label>
                            <input type="text" name="established_{{ $lang }}" dir="{{ $dir }}" value="{{ old("established_{$lang}", $branch->{"established_{$lang}"}) }}" style="{{ $style }}" placeholder="Est. 2020"
                                   class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                            @error("established_{$lang}")<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Contact + Location --}}
        <div class="bg-white rounded-xl border border-delos-dark/5 shadow-card p-5 space-y-4">
            <h3 class="font-serif text-base text-delos-dark-2 font-medium">Contact &amp; Location</h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $branch->phone) }}" placeholder="0750 200 1003"
                           class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                    @error('phone')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">WhatsApp</label>
                    <input type="text" name="whatsapp" value="{{ old('whatsapp', $branch->whatsapp) }}" placeholder="+964..."
                           class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                    @error('whatsapp')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $branch->email) }}" placeholder="erbil@delos.com"
                           class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                    @error('email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Latitude</label>
                    <input type="number" step="0.0000001" name="latitude" value="{{ old('latitude', $branch->latitude) }}" placeholder="36.1911"
                           class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm font-mono focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                    <p class="text-xs text-delos-muted mt-1">Decimal degrees (e.g. 36.1911)</p>
                    @error('latitude')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Longitude</label>
                    <input type="number" step="0.0000001" name="longitude" value="{{ old('longitude', $branch->longitude) }}" placeholder="44.0094"
                           class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm font-mono focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                    <p class="text-xs text-delos-muted mt-1">Decimal degrees (e.g. 44.0094)</p>
                    @error('longitude')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Directions URL (optional)</label>
                <input type="url" name="directions_url" value="{{ old('directions_url', $branch->directions_url) }}" placeholder="https://maps.google.com/?cid=..."
                       class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm font-mono focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                <p class="text-xs text-delos-muted mt-1">If set, overrides the auto-generated Google Maps URL for the "Get Directions" button.</p>
                @error('directions_url')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-delos-dark/8">
                <div>
                    <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Facebook URL (optional)</label>
                    <input type="url" name="facebook_url" value="{{ old('facebook_url', $branch->facebook_url) }}" placeholder="https://facebook.com/yourpage"
                           class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm font-mono focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                    <p class="text-xs text-delos-muted mt-1">Public Facebook page for this branch. Shows a Facebook icon button on the branch card if set.</p>
                    @error('facebook_url')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Instagram URL (optional)</label>
                    <input type="url" name="instagram_url" value="{{ old('instagram_url', $branch->instagram_url) }}" placeholder="https://instagram.com/yourhandle"
                           class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm font-mono focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                    <p class="text-xs text-delos-muted mt-1">Public Instagram profile for this branch. Shows an Instagram icon button on the branch card if set.</p>
                    @error('instagram_url')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            @if($branch->latitude && $branch->longitude)
                <div class="pt-3 border-t border-delos-dark/8">
                    <p class="text-xs text-delos-muted mb-2">Preview on OpenStreetMap:</p>
                    <a href="https://www.openstreetmap.org/?mlat={{ $branch->latitude }}&mlon={{ $branch->longitude }}&zoom=15" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-xs text-delos-gold hover:text-delos-gold-dark">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        Open in OpenStreetMap
                    </a>
                </div>
            @endif
        </div>

    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">

        <div class="bg-white rounded-xl border border-delos-dark/5 shadow-card p-5 space-y-4">
            <h3 class="font-serif text-base text-delos-dark-2 font-medium">Settings</h3>

            <div>
                <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Display order</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $branch->sort_order ?? 0) }}" min="0" max="9999"
                       class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                <p class="text-xs text-delos-muted mt-1">Lower numbers appear first</p>
            </div>

            <label class="flex items-center justify-between cursor-pointer p-3 bg-delos-ivory/40 rounded-lg">
                <div>
                    <div class="text-sm font-medium text-delos-dark-2 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-delos-gold" fill="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                        Flagship
                    </div>
                    <div class="text-xs text-delos-muted">Larger pin on the map. Only one at a time.</div>
                </div>
                <input type="hidden" name="is_flagship" value="0">
                <div x-data="{ on: {{ old('is_flagship', $branch->is_flagship ?? false) ? 'true' : 'false' }} }" class="relative">
                    <input type="checkbox" name="is_flagship" value="1" x-model="on" class="sr-only peer">
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
                <div x-data="{ on: {{ old('active', $branch->active ?? true) ? 'true' : 'false' }} }" class="relative">
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
            <h3 class="font-serif text-base text-delos-dark-2 font-medium mb-3">Showroom photo</h3>
            <p class="text-xs text-delos-muted mb-3">Optional. Public cards use a letter watermark when no photo is set.</p>
            <x-admin.image-upload
                label="Showroom photo"
                :current-url="$branch->image_url"
                :mobile-current-url="$branch->mobile_image_url"
                :focal="$branch->focal_point"
            />
            @error('image')<p class="text-xs text-red-600 mt-2">{{ $message }}</p>@enderror
            @error('image_mobile')<p class="text-xs text-red-600 mt-2">{{ $message }}</p>@enderror
            @error('focal_point')<p class="text-xs text-red-600 mt-2">{{ $message }}</p>@enderror
        </div>

        <div class="flex flex-col gap-2">
            <button type="submit" class="w-full py-3 bg-delos-gold hover:bg-delos-gold-light text-delos-dark-2 rounded-lg text-xs font-semibold tracking-[0.2em] uppercase transition-colors">
                {{ $isEdit ? 'Save Changes' : 'Create Branch' }}
            </button>
            <a href="{{ route('admin.branches.index') }}" class="w-full py-3 text-center text-delos-muted hover:text-delos-dark-2 text-xs font-medium tracking-[0.15em] uppercase transition-colors">
                Cancel
            </a>
        </div>
    </div>
</div>

<style>[x-cloak] { display: none !important; }</style>
