@php
    $isEdit = $project->exists;
@endphp

{{--
    Projects admin form — deliberately minimal.

    Cards on /en/projects are image-only; the only per-project text we
    still surface on the public site is the `type` category, which the
    filter chips at the top of the page use to show/hide items. So the
    admin form only needs:

      - Type (drives the filter)
      - Photo + mobile variant + focal point
      - Featured / Active toggles, display order

    Title / city / year / brand / type_label columns are still in the DB
    (nullable) so old data is preserved and this change is reversible,
    but the inputs for them are removed here.
--}}

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Main content --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Category / filter key --}}
        <div class="bg-white rounded-xl border border-delos-dark/5 shadow-card p-5 space-y-4">
            <h3 class="font-serif text-base text-delos-dark-2 font-medium">Category</h3>

            <div>
                <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium mb-2">Type key <span class="text-red-500">*</span></label>
                <input type="text" name="type" value="{{ old('type', $project->type) }}" placeholder="kitchens, living, bedroom, turnkey, ..." required
                       class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                <p class="text-xs text-delos-muted mt-1">Drives the filter on the public /projects page. Keep values consistent across projects (e.g. every kitchen project uses <code>kitchens</code>).</p>
                @error('type')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
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
        <div class="bg-white rounded-xl border border-delos-dark/5 shadow-card p-5">
            <h3 class="font-serif text-base text-delos-dark-2 font-medium mb-3">Photo</h3>
            <x-admin.image-upload
                label="Project photo"
                :current-url="$project->image_url"
                :mobile-current-url="$project->mobile_image_url"
                :focal="$project->focal_point"
            />
            @error('image')<p class="text-xs text-red-600 mt-2">{{ $message }}</p>@enderror
            @error('image_mobile')<p class="text-xs text-red-600 mt-2">{{ $message }}</p>@enderror
            @error('focal_point')<p class="text-xs text-red-600 mt-2">{{ $message }}</p>@enderror
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
