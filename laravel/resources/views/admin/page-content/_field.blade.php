@php
    $type = $field['type'] ?? 'text';
    $key = $field['key'];

    $defaults = [
        'en' => $langDefault['en'] ?? '',
        'ar' => $langDefault['ar'] ?? '',
        'it' => $langDefault['it'] ?? '',
    ];

    // Return the admin-editable value for this locale ONLY — never fall
    // back to a different locale's value. Falling back to English for an
    // empty AR input caused a silent pollution bug: on save, the English
    // text got written into value_ar, and the next editor load showed
    // English inside the Arabic textarea (now persisted).
    //
    // Instead: textarea/input `value=` is strictly this-locale DB → this-
    // locale lang file → empty. The visual cue for "this locale is empty
    // and will fall back to English on the public site" lives in the
    // `placeholder=` attribute on the rendered input (already set to
    // $defaults[$locale] in the textarea/input markup below), AND in the
    // "fills" state logic that marks unfilled locales.
    $effective = function (string $locale) use ($row, $defaults, $key) {
        $dbValue = $row?->{"value_{$locale}"};
        if ($dbValue !== null && $dbValue !== '') return (string) $dbValue;

        $langValue = $defaults[$locale] ?? null;
        if ($langValue !== null && $langValue !== '') return (string) $langValue;

        // Lang file lookup with data_get() for numeric-index-dot paths
        // (e.g. 'home.stats.items.0.value') that Laravel's translator
        // otherwise can't resolve.
        $dotPos = strpos($key, '.');
        if ($dotPos !== false) {
            $group = substr($key, 0, $dotPos);
            $path = substr($key, $dotPos + 1);
            $file = lang_path("{$locale}/{$group}.php");
            if (file_exists($file)) {
                $val = data_get(include $file, $path);
                if ($val !== null && !is_array($val)) return (string) $val;
            }
        }
        return '';
    };

    $values = [
        'en' => $effective('en'),
        'ar' => $effective('ar'),
        'it' => $effective('it'),
    ];

    // A field is "overridden" (differs from its lang default) if the DB row
    // exists and any locale's stored value diverges from the lang file.
    // Distinguishes admin-customized copy from seeded-but-unchanged copy.
    $isOverridden = $row && (
        ($row->value_en !== null && $row->value_en !== ($defaults['en'] ?? null))
        || ($row->value_ar !== null && $row->value_ar !== ($defaults['ar'] ?? null))
        || ($row->value_it !== null && $row->value_it !== ($defaults['it'] ?? null))
        || ($row->value_ku !== null && $row->value_ku !== ($defaults['ku'] ?? null))
    );

    $isMedia = in_array($type, ['image', 'video'], true);
    $isLocalized = !$isMedia && $type !== 'url';

    // For media: resolve the default URL (lang file → public asset) and
    // the effective URL (DB upload if one exists, else default). We pass
    // both to the Alpine component so "Reset to default" can restore the
    // preview instead of blanking it.
    $resolveAssetUrl = function (?string $value, string $type) {
        if (!$value) return null;
        if (preg_match('#^https?://#i', $value)) return $value;
        if (str_contains($value, '/')) return asset($value);
        // Bare filename — route to the right public folder by field type.
        $folder = $type === 'video' ? 'videos' : 'images';
        return asset("{$folder}/{$value}");
    };

    $currentMedia = $row?->value_en;
    $mediaIsPath = $currentMedia && str_starts_with($currentMedia, 'uploads/');
    $defaultMediaUrl = $resolveAssetUrl($defaults['en'] ?? null, $type);
    $effectiveMediaUrl = $mediaIsPath
        ? asset('storage/' . $currentMedia)
        : $defaultMediaUrl;

    // Hybrid-image sibling rows — only relevant to image fields. Mobile
    // variant lives at "<key>_mobile"; focal point at "<key>_focal" stored
    // as "X% Y%". Both are nullable — if neither is set the image renders
    // identically to today (centered, single file).
    $mobileKey = $type === 'image' ? $key . '_mobile' : null;
    $focalKey  = $type === 'image' ? $key . '_focal'  : null;
    $mobileRow = $mobileKey ? \App\Models\PageContent::where('key', $mobileKey)->first() : null;
    $focalRow  = $focalKey  ? \App\Models\PageContent::where('key', $focalKey)->first()  : null;

    $mobileStored = $mobileRow?->value_en;
    $mobileIsCustom = $mobileStored && str_starts_with($mobileStored, 'uploads/');
    $mobileEffectiveUrl = $mobileIsCustom ? asset('storage/' . $mobileStored) : null;

    // Parse focal into {x, y} integer percent. Default to centered.
    $focalX = 50; $focalY = 50;
    if ($focalRow && preg_match('/^(\d{1,3})%\s+(\d{1,3})%$/', trim((string) $focalRow->value_en), $fm)) {
        $focalX = max(0, min(100, (int) $fm[1]));
        $focalY = max(0, min(100, (int) $fm[2]));
    }

    // If this image field has a sibling video field (like the homepage
    // video poster), resolve that video's effective URL so we can render
    // a "Capture frame" button that pulls a poster from any timestamp.
    $videoSourceUrl = null;
    if ($type === 'image' && !empty($field['video_source_key'])) {
        $vKey = $field['video_source_key'];
        $vRow = \App\Models\PageContent::where('key', $vKey)->first();
        $vStored = $vRow?->value_en;
        if ($vStored && str_starts_with($vStored, 'uploads/')) {
            $videoSourceUrl = asset('storage/' . $vStored);
        } else {
            $vLang = \Illuminate\Support\Facades\Lang::get($vKey, [], 'en');
            if (is_string($vLang) && $vLang !== '' && $vLang !== $vKey) {
                $videoSourceUrl = $resolveAssetUrl($vLang, 'video');
            }
        }
    }
@endphp

<div class="pt-5 border-t border-delos-dark/5 first:border-0 first:pt-0" data-field-key="{{ $key }}">
    <div class="flex items-start justify-between mb-2 gap-3">
        <div class="min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <label class="block text-sm font-medium text-delos-dark-2">{{ $field['label'] }}</label>
                @if($isOverridden)
                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] tracking-[0.15em] uppercase font-semibold text-delos-gold bg-delos-gold/10" title="This field has been customized from its default">
                        <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.657 6.343a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L9 13.586l7.243-7.243a1 1 0 011.414 0z" transform="translate(-1 1)"/></svg>
                        Customized
                    </span>
                @endif
            </div>
            <p class="text-[10px] tracking-[0.15em] uppercase text-delos-muted/60 font-mono mt-0.5 truncate">{{ $key }} · {{ $type }}</p>
        </div>
        @if($row && $isOverridden)
            {{--
                JS-fetch instead of a nested <form>. HTML forbids nested forms and
                browsers implicitly close the outer "Save all" form at the inner
                <form> opening tag — that silently killed every admin save on
                pages where any field was customized, because 48 of 54 inputs
                ended up outside the main form and were never submitted.
                delosResetPageContentField() is defined in admin/layout.blade.php.
            --}}
            <button type="button" class="text-[10px] tracking-[0.15em] uppercase text-delos-muted hover:text-red-600 transition-colors whitespace-nowrap flex-shrink-0"
                    onclick="delosResetPageContentField({{ Js::from($key) }}, {{ Js::from(route('admin.page-content.reset', $pageSlug)) }})">
                Reset to default
            </button>
        @endif
    </div>

    @if($type === 'image' || $type === 'video')
        {{-- Media field — shows what the public site currently displays (DB
             upload OR lang-file default asset), with an async upload slot to
             override it. --}}
        <div x-data="pcontentMedia({{ json_encode([
            'key' => $key,
            'type' => $type,
            'currentUrl' => $effectiveMediaUrl,
            'defaultUrl' => $defaultMediaUrl,
            'isCustom' => (bool) $mediaIsPath,
            'videoSourceUrl' => $videoSourceUrl ?? null,
            'uploadUrl' => route('admin.page-content.media.store', $key),
            'deleteUrl' => route('admin.page-content.media.destroy', $key),
            // Hybrid-image fields only — null on video/other types so the
            // Alpine component just ignores the mobile + focal blocks.
            'hybrid' => $type === 'image',
            'mobileUrl' => $mobileEffectiveUrl ?? null,
            'mobileIsCustom' => $type === 'image' ? (bool) $mobileIsCustom : false,
            'mobileUploadUrl' => $type === 'image' ? route('admin.page-content.media.store', $key) . '?variant=mobile' : null,
            'mobileDeleteUrl' => $type === 'image' ? route('admin.page-content.media.destroy', $key) . '?variant=mobile' : null,
            'focalX' => $type === 'image' ? $focalX : 50,
            'focalY' => $type === 'image' ? $focalY : 50,
            'focalUrl' => $type === 'image' ? route('admin.page-content.media.focal', $key) : null,
            'focalResetUrl' => $type === 'image' ? route('admin.page-content.media.focal-reset', $key) : null,
            'csrf' => csrf_token(),
        ]) }})" class="space-y-3">
            <div x-show="currentUrl" class="relative rounded-lg overflow-hidden bg-delos-ivory/60 border border-delos-dark/8">
                <span x-show="isCustom" class="absolute top-3 left-3 z-10 inline-flex items-center gap-1 px-2 py-1 rounded text-[9px] tracking-[0.15em] uppercase font-semibold text-delos-dark bg-delos-gold">Customized</span>
                <span x-show="!isCustom" class="absolute top-3 left-3 z-10 inline-flex items-center gap-1 px-2 py-1 rounded text-[9px] tracking-[0.15em] uppercase font-medium text-delos-dark-2 bg-white/80">Current default</span>
                @if($type === 'image')
                    {{-- The preview is also the focal-point picker: clicking
                         anywhere on the image moves the gold crosshair to
                         that point and persists the coordinate via AJAX. --}}
                    <div class="relative" x-ref="focalTarget"
                         @click="hybrid && setFocalFromEvent($event)"
                         :style="hybrid ? 'cursor: crosshair' : ''">
                        <img :src="currentUrl" alt=""
                             :style="hybrid ? `object-position: ${focalX}% ${focalY}%` : ''"
                             class="w-full max-h-[300px] object-cover block">
                        <div x-show="hybrid" class="absolute pointer-events-none"
                             :style="`left: ${focalX}%; top: ${focalY}%; transform: translate(-50%, -50%);`">
                            <div class="w-6 h-6 rounded-full border-2 border-delos-gold bg-delos-gold/25 shadow-[0_0_0_3px_rgba(196,154,122,0.15)]"></div>
                        </div>
                    </div>
                @else
                    <video :src="currentUrl" controls class="w-full h-auto max-h-[400px]"></video>
                @endif
                <button type="button" x-show="isCustom" @click="clear()"
                        class="absolute top-3 right-3 z-10 px-3 py-1.5 bg-red-600/90 hover:bg-red-600 text-white text-[10px] tracking-[0.15em] uppercase rounded transition-colors">
                    Reset to default
                </button>
            </div>

            {{-- Focal-point readout + reset control (only for image fields). --}}
            @if($type === 'image')
                <div x-show="hybrid && currentUrl" class="flex items-center justify-between px-3 py-2 bg-white/70 rounded-lg border border-delos-dark/5 text-[10px] tracking-[0.15em] uppercase">
                    <span class="text-delos-muted">Focal point
                        <span class="text-delos-dark font-mono font-semibold normal-case tracking-normal ml-2" x-text="`${focalX}% / ${focalY}%`"></span>
                        <span x-show="focalSaving" class="text-delos-gold normal-case tracking-normal ml-2">saving…</span>
                    </span>
                    <button type="button" @click="resetFocal()" class="text-delos-muted hover:text-delos-gold normal-case tracking-normal font-sans">Reset to centre</button>
                </div>
            @endif

            <label class="block border-2 border-dashed border-delos-dark/15 hover:border-delos-gold rounded-lg p-5 text-center cursor-pointer transition-colors">
                <svg class="w-7 h-7 mx-auto text-delos-muted mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    @if($type === 'image')
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    @endif
                </svg>
                <p class="text-sm text-delos-dark-2 font-medium mb-0.5" x-text="isCustom ? 'Replace with new upload' : ('Upload new ' + ('{{ $type }}' === 'video' ? 'video' : 'image'))"></p>
                <p class="text-xs text-delos-muted">
                    @if($type === 'video') MP4, WebM, MOV · any size (auto-compressed) @else JPG, PNG, WebP · any size (auto-compressed) @endif
                </p>
                <input type="file"
                       @change="onFile($event)"
                       accept="@if($type === 'video') video/mp4,video/webm,video/quicktime @else image/jpeg,image/png,image/webp @endif"
                       class="sr-only">
            </label>

            {{-- Mobile variant slot — image fields only. Optional upload
                 that overrides the default photo when the viewport is
                 ≤ 767px (matches Tailwind's md: breakpoint). --}}
            @if($type === 'image')
                <div class="pt-4 border-t border-delos-dark/5 space-y-2">
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium">
                            Mobile variant
                            <span class="normal-case tracking-normal text-delos-muted/60">(optional — shown on phones)</span>
                        </p>
                        <button type="button" x-show="mobileIsCustom" @click="clearMobile()"
                                class="text-[10px] tracking-[0.15em] uppercase text-delos-muted hover:text-red-600 transition-colors">
                            Remove
                        </button>
                    </div>

                    <div x-show="mobileUrl" x-cloak class="rounded-lg overflow-hidden bg-delos-ivory/60 border border-delos-dark/8">
                        <img :src="mobileUrl" alt="" class="w-full max-h-[240px] object-cover block">
                    </div>

                    <label class="block border-2 border-dashed border-delos-dark/15 hover:border-delos-gold rounded-lg p-3 text-center cursor-pointer transition-colors">
                        <p class="text-xs text-delos-dark-2 font-medium" x-text="mobileIsCustom ? 'Replace mobile variant' : 'Upload mobile variant'"></p>
                        <p class="text-[10px] text-delos-muted mt-0.5">JPG, PNG, WebP · any size (auto-compressed)</p>
                        <input type="file"
                               @change="onMobileFile($event)"
                               accept="image/jpeg,image/png,image/webp"
                               class="sr-only">
                    </label>
                </div>
            @endif

            @if($type === 'image' && !empty($videoSourceUrl))
                {{-- "Capture poster from video" — opens an inline player,
                     lets the admin scrub to any frame, then grabs that
                     frame via <canvas> and uploads it as the new poster. --}}
                <button type="button" @click="openCapture()"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 border border-delos-dark/15 hover:border-delos-gold hover:text-delos-gold text-delos-dark-2 text-[11px] tracking-[0.25em] uppercase font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    Capture poster from video
                </button>

                {{-- Modal: scrub-to-frame picker. Uses x-teleport so the
                     backdrop overlays the whole page, not just the card. --}}
                <template x-teleport="body">
                    <div x-show="captureOpen" x-cloak
                         @keydown.escape.window="closeCapture()"
                         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-delos-dark/80 backdrop-blur-sm"
                         x-transition.opacity>
                        <div @click.outside="closeCapture()" class="bg-delos-cream w-full max-w-3xl rounded-xl shadow-2xl overflow-hidden">
                            <div class="flex items-center justify-between px-5 py-3 border-b border-delos-dark/10">
                                <div>
                                    <p class="font-serif text-delos-dark text-lg font-light">Capture poster from video</p>
                                    <p class="text-[10px] tracking-[0.2em] uppercase text-delos-muted font-mono mt-0.5">Pause at any frame, then click "Use this frame"</p>
                                </div>
                                <button type="button" @click="closeCapture()" class="text-delos-muted hover:text-delos-dark text-2xl leading-none px-2">&times;</button>
                            </div>

                            <div class="p-5 space-y-4 bg-delos-dark">
                                <video x-ref="captureVideo"
                                       :src="videoSourceUrl"
                                       @loadedmetadata="captureDuration = $event.target.duration; captureTime = 0"
                                       @timeupdate="captureTime = $event.target.currentTime"
                                       controls
                                       crossorigin="anonymous"
                                       playsinline
                                       class="w-full max-h-[60vh] bg-black rounded"></video>

                                <div class="flex items-center gap-3 text-delos-cream/80 text-xs font-mono">
                                    <span x-text="formatTime(captureTime)"></span>
                                    <input type="range" min="0" :max="captureDuration || 0" step="0.1"
                                           :value="captureTime"
                                           @input="$refs.captureVideo.currentTime = parseFloat($event.target.value); $refs.captureVideo.pause()"
                                           class="flex-1 accent-delos-gold">
                                    <span x-text="formatTime(captureDuration)"></span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <button type="button" @click="$refs.captureVideo.currentTime = Math.max(0, $refs.captureVideo.currentTime - 0.04); $refs.captureVideo.pause()"
                                            class="px-3 py-1.5 text-[10px] tracking-[0.2em] uppercase bg-delos-cream/10 hover:bg-delos-cream/20 text-delos-cream rounded transition-colors">-1 frame</button>
                                    <button type="button" @click="$refs.captureVideo.currentTime = Math.min(captureDuration, $refs.captureVideo.currentTime + 0.04); $refs.captureVideo.pause()"
                                            class="px-3 py-1.5 text-[10px] tracking-[0.2em] uppercase bg-delos-cream/10 hover:bg-delos-cream/20 text-delos-cream rounded transition-colors">+1 frame</button>
                                    <div class="flex-1"></div>
                                    <span x-show="captureBusy" class="text-delos-cream/60 text-xs">Capturing…</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-3 px-5 py-4 border-t border-delos-dark/10">
                                <button type="button" @click="closeCapture()"
                                        class="px-4 py-2 text-[11px] tracking-[0.25em] uppercase text-delos-muted hover:text-delos-dark transition-colors">Cancel</button>
                                <button type="button" @click="captureFrame()" :disabled="captureBusy"
                                        class="px-5 py-2.5 bg-delos-gold hover:bg-delos-gold-light text-delos-dark text-[11px] tracking-[0.25em] uppercase font-semibold rounded transition-colors disabled:opacity-50">
                                    Use this frame
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            @endif

            <div x-show="status" x-text="status"
                 :class="statusType === 'error' ? 'text-red-600' : 'text-delos-gold-dark'"
                 class="text-xs"></div>
        </div>
    @elseif($type === 'url')
        <input type="url" name="values[{{ $key }}][en]" value="{{ $values['en'] }}"
               placeholder="{{ $defaults['en'] }}"
               class="w-full px-3 py-2 border border-delos-dark/15 rounded-lg text-sm font-mono focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
        {{-- URLs aren't localized, but pass empty strings so update() sees them --}}
        <input type="hidden" name="values[{{ $key }}][ar]" value="{{ $values['ar'] }}">
        <input type="hidden" name="values[{{ $key }}][it]" value="{{ $values['it'] }}">
    @else
        {{-- Text / textarea / rich — all 3 locales shown together.
             Each locale gets a labelled input so the admin always sees
             and can edit every language without JS dependency. --}}
        <div class="space-y-2">
            @foreach(['en' => 'EN', 'ar' => 'AR', 'it' => 'IT'] as $code => $localeLabel)
                @php
                    $dir = $code === 'ar' ? 'rtl' : 'ltr';
                    $style = $code === 'ar' ? "font-family: 'Cairo', sans-serif;" : '';
                @endphp
                <div class="flex items-start gap-2">
                    <span class="flex-shrink-0 mt-2 w-7 text-center text-[9px] tracking-[0.1em] uppercase font-bold text-delos-muted/70 select-none">{{ $localeLabel }}</span>
                    @if($type === 'textarea')
                        <textarea name="values[{{ $key }}][{{ $code }}]"
                                  rows="3"
                                  dir="{{ $dir }}"
                                  style="{{ $style }}"
                                  placeholder="{{ $defaults[$code] ?? '' }}"
                                  class="flex-1 px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all resize-y">{{ $values[$code] }}</textarea>
                    @else
                        <input type="text"
                               name="values[{{ $key }}][{{ $code }}]"
                               value="{{ $values[$code] }}"
                               dir="{{ $dir }}"
                               style="{{ $style }}"
                               placeholder="{{ $defaults[$code] ?? '' }}"
                               class="flex-1 px-3 py-2 border border-delos-dark/15 rounded-lg text-sm focus:outline-none focus:border-delos-gold focus:ring-2 focus:ring-delos-gold/15 transition-all">
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>

@once
    @push('head')
        <script>
            // Alpine data factory for async per-field media upload. Registered
            // once per page to keep each field component tiny.
            document.addEventListener('alpine:init', () => {
                Alpine.data('pcontentMedia', (config) => ({
                    currentUrl: config.currentUrl,
                    // Track isCustom reactively so the "Customized" badge and
                    // "Reset to default" button flip immediately after upload
                    // or clear — without this they'd stay stale until reload.
                    isCustom: !!config.isCustom,
                    defaultUrl: config.defaultUrl || null,
                    // Companion video URL — set on image fields that have a
                    // `video_source_key` in the registry (homepage poster).
                    videoSourceUrl: config.videoSourceUrl || null,
                    // Frame-capture modal state
                    captureOpen: false,
                    captureTime: 0,
                    captureDuration: 0,
                    captureBusy: false,
                    status: '',
                    statusType: '',
                    // Hybrid-image state (mobile variant + focal point). When
                    // hybrid is false (video fields etc.) every method below
                    // short-circuits so non-image fields stay unchanged.
                    hybrid: !!config.hybrid,
                    mobileUrl: config.mobileUrl || null,
                    mobileIsCustom: !!config.mobileIsCustom,
                    focalX: typeof config.focalX === 'number' ? config.focalX : 50,
                    focalY: typeof config.focalY === 'number' ? config.focalY : 50,
                    focalSaving: false,
                    _focalTimer: null,
                    async onFile(event) {
                        const picked = event.target.files[0];
                        if (!picked) return;
                        let file = picked;
                        // Video: transcode in the browser via FFmpeg.wasm if
                        // the source is larger than the server limit. Keeps
                        // the upload under 50MB without any server-side work.
                        if (config.type === 'video') {
                            const LIMIT = 50 * 1024 * 1024;
                            if (file.size > LIMIT) {
                                try {
                                    file = await this._compressVideo(picked, LIMIT);
                                } catch (e) {
                                    this.status = `Could not compress video: ${e.message}`;
                                    this.statusType = 'error';
                                    event.target.value = '';
                                    return;
                                }
                            }
                        } else {
                            // Images: auto-compress in the browser to ≤5MB before
                            // upload, so the server's size limit never blocks us
                            // and visitors get a lighter file. Full pixel
                            // dimensions preserved — only JPEG quality steps
                            // down (0.92 → 0.60) until the blob fits the budget.
                            try {
                                file = await this._compressImage(picked, 5 * 1024 * 1024);
                            } catch (e) {
                                this.status = 'Could not read image: ' + e.message;
                                this.statusType = 'error';
                                event.target.value = '';
                                return;
                            }
                        }
                        this.status = 'Uploading…';
                        this.statusType = '';
                        const fd = new FormData();
                        fd.append('file', file);
                        fd.append('_token', config.csrf);
                        try {
                            const r = await fetch(config.uploadUrl, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } });
                            // Parse text first so we can fall back to a real
                            // message when the server returned HTML (413, 419,
                            // 500 all render HTML error pages by default).
                            const raw = await r.text();
                            let data = {};
                            try { data = JSON.parse(raw); } catch (_) { /* HTML response */ }
                            if (!r.ok) {
                                const msg = data.error || data.message
                                    || (r.status === 413 ? 'File is too large for the server. Ask the developer to raise PHP upload limits.' : null)
                                    || (r.status === 419 ? 'Session expired — reload the page and try again.' : null)
                                    || `Upload failed (HTTP ${r.status}).`;
                                throw new Error(msg);
                            }
                            this.currentUrl = data.url;
                            this.isCustom = true;
                            this.status = 'Uploaded.';
                            this.statusType = '';
                            setTimeout(() => this.status = '', 2500);
                        } catch (e) {
                            this.status = 'Failed: ' + e.message;
                            this.statusType = 'error';
                        }
                    },
                    openCapture() {
                        if (!this.videoSourceUrl) return;
                        this.captureOpen = true;
                    },
                    closeCapture() {
                        // Pause the video on close so audio doesn't keep
                        // playing behind the closed modal.
                        if (this.$refs.captureVideo) this.$refs.captureVideo.pause();
                        this.captureOpen = false;
                    },
                    formatTime(seconds) {
                        if (!seconds || isNaN(seconds)) return '0:00';
                        const m = Math.floor(seconds / 60);
                        const s = Math.floor(seconds % 60).toString().padStart(2, '0');
                        return `${m}:${s}`;
                    },
                    async captureFrame() {
                        const video = this.$refs.captureVideo;
                        if (!video || !video.videoWidth) {
                            this.status = 'Video not ready — wait a moment and try again.';
                            this.statusType = 'error';
                            return;
                        }
                        this.captureBusy = true;
                        try {
                            // Render the current frame to a canvas at the
                            // video's native resolution, then encode as JPEG.
                            const canvas = document.createElement('canvas');
                            canvas.width = video.videoWidth;
                            canvas.height = video.videoHeight;
                            const ctx = canvas.getContext('2d');
                            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                            const blob = await new Promise((resolve, reject) => {
                                canvas.toBlob(b => b ? resolve(b) : reject(new Error('Failed to encode frame')), 'image/jpeg', 0.92);
                            });
                            // Reuse the existing upload pipeline.
                            const fd = new FormData();
                            fd.append('file', blob, 'poster-frame.jpg');
                            fd.append('_token', config.csrf);
                            this.status = 'Uploading captured frame…';
                            this.statusType = '';
                            const r = await fetch(config.uploadUrl, {
                                method: 'POST', body: fd,
                                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                            });
                            const raw = await r.text();
                            let data = {};
                            try { data = JSON.parse(raw); } catch (_) {}
                            if (!r.ok) throw new Error(data.error || data.message || `Upload failed (HTTP ${r.status})`);
                            this.currentUrl = data.url;
                            this.isCustom = true;
                            this.status = 'Poster captured.';
                            this.statusType = '';
                            setTimeout(() => this.status = '', 2500);
                            this.closeCapture();
                        } catch (e) {
                            // Canvas exports throw SecurityError when the
                            // video is served from a different origin without
                            // CORS headers — explain that in plain English.
                            const msg = e.name === 'SecurityError'
                                ? 'Cannot capture frame: the video was served without CORS headers. Upload the video through this editor first so it comes from the local server.'
                                : e.message;
                            this.status = 'Failed: ' + msg;
                            this.statusType = 'error';
                        } finally {
                            this.captureBusy = false;
                        }
                    },
                    async clear() {
                        if (!confirm('Remove this media and revert to default?')) return;
                        try {
                            await fetch(config.deleteUrl, {
                                method: 'DELETE',
                                headers: { 'X-CSRF-TOKEN': config.csrf, 'Accept': 'application/json' },
                            });
                            // Swap back to the default asset URL if we know
                            // one; otherwise hide the preview entirely.
                            this.currentUrl = this.defaultUrl || null;
                            this.isCustom = false;
                            this.status = 'Reverted to default.';
                            setTimeout(() => this.status = '', 2500);
                        } catch (e) {
                            this.status = 'Failed to reset.';
                            this.statusType = 'error';
                        }
                    },

                    // ─── Mobile variant upload ────────────────────────
                    async onMobileFile(event) {
                        if (!this.hybrid) return;
                        const picked = event.target.files[0];
                        if (!picked) return;
                        let file;
                        try {
                            file = await this._compressImage(picked, 5 * 1024 * 1024);
                        } catch (e) {
                            this.status = 'Could not read mobile image: ' + e.message;
                            this.statusType = 'error';
                            event.target.value = '';
                            return;
                        }
                        this.status = 'Uploading mobile variant…';
                        this.statusType = '';
                        const fd = new FormData();
                        fd.append('file', file);
                        fd.append('_token', config.csrf);
                        try {
                            const r = await fetch(config.mobileUploadUrl, {
                                method: 'POST', body: fd,
                                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                            });
                            const raw = await r.text();
                            let data = {};
                            try { data = JSON.parse(raw); } catch (_) {}
                            if (!r.ok) throw new Error(data.error || data.message || `Upload failed (HTTP ${r.status})`);
                            this.mobileUrl = data.url;
                            this.mobileIsCustom = true;
                            this.status = 'Mobile variant saved.';
                            setTimeout(() => this.status = '', 2500);
                        } catch (e) {
                            this.status = 'Failed: ' + e.message;
                            this.statusType = 'error';
                        }
                    },
                    async clearMobile() {
                        if (!this.hybrid) return;
                        if (!confirm('Remove the mobile variant? The main image will be shown on phones.')) return;
                        try {
                            await fetch(config.mobileDeleteUrl, {
                                method: 'DELETE',
                                headers: { 'X-CSRF-TOKEN': config.csrf, 'Accept': 'application/json' },
                            });
                            this.mobileUrl = null;
                            this.mobileIsCustom = false;
                            this.status = 'Mobile variant removed.';
                            setTimeout(() => this.status = '', 2500);
                        } catch (e) {
                            this.status = 'Failed to remove mobile variant.';
                            this.statusType = 'error';
                        }
                    },

                    // ─── Focal-point picker ───────────────────────────
                    setFocalFromEvent(event) {
                        if (!this.hybrid) return;
                        const el = this.$refs.focalTarget;
                        if (!el) return;
                        const rect = el.getBoundingClientRect();
                        const x = ((event.clientX - rect.left) / rect.width)  * 100;
                        const y = ((event.clientY - rect.top)  / rect.height) * 100;
                        this.focalX = Math.max(0, Math.min(100, Math.round(x)));
                        this.focalY = Math.max(0, Math.min(100, Math.round(y)));
                        this._persistFocalDebounced();
                    },
                    resetFocal() {
                        if (!this.hybrid) return;
                        this.focalX = 50;
                        this.focalY = 50;
                        // Use the dedicated reset endpoint — it deletes the
                        // sibling row so the image reverts to the default
                        // (no object-position emitted in the public HTML).
                        fetch(config.focalResetUrl, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': config.csrf, 'Accept': 'application/json' },
                        }).then(() => {
                            this.status = 'Focal reset to centre.';
                            setTimeout(() => this.status = '', 2000);
                        }).catch(() => {
                            this.status = 'Failed to reset focal.';
                            this.statusType = 'error';
                        });
                    },
                    // Debounce rapid clicks so we don't spam the server while
                    // the admin is dialing in the perfect spot.
                    _persistFocalDebounced() {
                        if (this._focalTimer) clearTimeout(this._focalTimer);
                        this._focalTimer = setTimeout(() => this._persistFocal(), 400);
                    },
                    // ----------------------------------------------------
                    // Client-side image compression. Runs in the browser so
                    // the server size cap (5MB on older deploys, 20MB now)
                    // never matters — files of any size are re-encoded to
                    // a target budget before upload. Full pixel dimensions
                    // are preserved; only JPEG quality is stepped down.
                    //
                    // At q=0.92 the difference is imperceptible on any
                    // normal screen. The loop only drops lower if a photo
                    // is genuinely huge (20MP+) and won't fit at q=0.92.
                    // ----------------------------------------------------
                    async _compressImage(file, maxBytes) {
                        if (!file.type || !file.type.startsWith('image/')) return file;
                        if (file.size <= maxBytes) return file;

                        const origMB = (file.size / (1024 * 1024)).toFixed(1);
                        this.status = `Compressing ${origMB}MB image…`;
                        this.statusType = '';

                        // Decode the file into an <img> via a blob URL.
                        const url = URL.createObjectURL(file);
                        const img = await new Promise((resolve, reject) => {
                            const i = new Image();
                            i.onload = () => resolve(i);
                            i.onerror = () => reject(new Error('unsupported image format'));
                            i.src = url;
                        }).finally(() => {});

                        // Draw at native resolution.
                        const canvas = document.createElement('canvas');
                        canvas.width = img.naturalWidth;
                        canvas.height = img.naturalHeight;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0);
                        URL.revokeObjectURL(url);

                        // Step quality down only as needed. Early-exit the
                        // first time the blob fits under the budget.
                        let bestBlob = null;
                        for (const q of [0.92, 0.88, 0.85, 0.80, 0.75, 0.70, 0.65, 0.60]) {
                            const blob = await new Promise(r => canvas.toBlob(r, 'image/jpeg', q));
                            if (!blob) continue;
                            bestBlob = blob;
                            if (blob.size <= maxBytes) break;
                        }
                        if (!bestBlob) throw new Error('encoder failed');

                        const newName = (file.name || 'image').replace(/\.\w+$/, '') + '.jpg';
                        const newFile = new File([bestBlob], newName, { type: 'image/jpeg' });
                        const newMB = (newFile.size / (1024 * 1024)).toFixed(1);
                        this.status = `Compressed ${origMB}MB → ${newMB}MB. Uploading…`;
                        return newFile;
                    },

                    // ----------------------------------------------------
                    // Client-side video compression via FFmpeg.wasm.
                    // Target: H.264 + AAC MP4 at 1080p-capped, CRF 26, fast
                    // preset — reliably lands large source videos under the
                    // 50MB server cap without shipping them over the wire
                    // uncompressed. First call lazy-loads the ~30MB WASM
                    // runtime from jsDelivr; subsequent calls reuse it.
                    // ----------------------------------------------------
                    async _compressVideo(file, maxBytes) {
                        const origMB = (file.size / (1024 * 1024)).toFixed(1);
                        this.status = `Loading video compressor (first time only)…`;
                        this.statusType = '';

                        if (!window._ffmpegInstance) {
                            // Self-hosted under /vendor/ffmpeg/ — cross-origin
                            // CDN loads fail when ffmpeg.js tries to spawn its
                            // Worker chunk (browsers block Worker scripts from
                            // a different origin).
                            const base = '{{ asset('vendor/ffmpeg') }}';
                            await new Promise((resolve, reject) => {
                                const s = document.createElement('script');
                                s.src = `${base}/ffmpeg.js`;
                                s.onload = resolve;
                                s.onerror = () => reject(new Error('failed to load compressor runtime'));
                                document.head.appendChild(s);
                            });
                            if (!window.FFmpegWASM || !window.FFmpegWASM.FFmpeg) {
                                throw new Error('compressor runtime not available');
                            }
                            const instance = new window.FFmpegWASM.FFmpeg();
                            await instance.load({
                                coreURL: `${base}/ffmpeg-core.js`,
                                wasmURL: `${base}/ffmpeg-core.wasm`,
                            });
                            window._ffmpegInstance = instance;
                        }
                        const ffmpeg = window._ffmpegInstance;

                        // Progress callback — FFmpeg.wasm emits { progress, time }.
                        const onProgress = ({ progress }) => {
                            const pct = Math.max(0, Math.min(1, progress || 0));
                            this.status = `Compressing video… ${Math.round(pct * 100)}%`;
                        };
                        ffmpeg.on('progress', onProgress);

                        try {
                            const ext = (file.name.match(/\.(\w+)$/)?.[1] || 'mp4').toLowerCase();
                            const inName = `input.${ext}`;
                            const outName = 'output.mp4';

                            this.status = `Reading ${origMB}MB video…`;
                            const buf = new Uint8Array(await file.arrayBuffer());
                            await ffmpeg.writeFile(inName, buf);

                            // Two-pass strategy: start with CRF 26 @ 1080p.
                            // If the result is still over budget, fall back
                            // to CRF 30 @ 1280-wide (smaller frame, more
                            // aggressive compression).
                            const attempts = [
                                ['-vf', `scale='min(1920,iw)':-2`, '-crf', '26'],
                                ['-vf', `scale='min(1280,iw)':-2`, '-crf', '30'],
                            ];
                            let outData = null;
                            for (const extra of attempts) {
                                await ffmpeg.exec([
                                    '-y',
                                    '-i', inName,
                                    ...extra,
                                    '-c:v', 'libx264',
                                    '-preset', 'veryfast',
                                    '-pix_fmt', 'yuv420p',
                                    '-c:a', 'aac',
                                    '-b:a', '128k',
                                    '-ac', '2',
                                    '-movflags', '+faststart',
                                    outName,
                                ]);
                                outData = await ffmpeg.readFile(outName);
                                if (outData.length <= maxBytes) break;
                            }
                            if (!outData) throw new Error('encoder produced no output');

                            // Always clean up the virtual FS so a second run
                            // doesn't trip on stale input/output files.
                            try { await ffmpeg.deleteFile(inName); } catch (_) {}
                            try { await ffmpeg.deleteFile(outName); } catch (_) {}

                            const blob = new Blob([outData.buffer], { type: 'video/mp4' });
                            const newName = (file.name || 'video').replace(/\.\w+$/, '') + '.mp4';
                            const newFile = new File([blob], newName, { type: 'video/mp4' });
                            const newMB = (newFile.size / (1024 * 1024)).toFixed(1);
                            this.status = `Compressed ${origMB}MB → ${newMB}MB. Uploading…`;

                            if (newFile.size > maxBytes) {
                                throw new Error(`still ${newMB}MB after compression — shorten or lower the source resolution`);
                            }
                            return newFile;
                        } finally {
                            ffmpeg.off('progress', onProgress);
                        }
                    },
                    async _persistFocal() {
                        this.focalSaving = true;
                        try {
                            const fd = new FormData();
                            fd.append('x', this.focalX);
                            fd.append('y', this.focalY);
                            fd.append('_token', config.csrf);
                            const r = await fetch(config.focalUrl, {
                                method: 'POST', body: fd,
                                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                            });
                            if (!r.ok) throw new Error(`Focal save failed (${r.status})`);
                        } catch (e) {
                            this.status = 'Failed to save focal point.';
                            this.statusType = 'error';
                        } finally {
                            this.focalSaving = false;
                        }
                    },
                }));
            });
        </script>
    @endpush
@endonce
