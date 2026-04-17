@php
    $type = $field['type'] ?? 'text';
    $key = $field['key'];

    $defaults = [
        'en' => $langDefault['en'] ?? '',
        'ar' => $langDefault['ar'] ?? '',
        'it' => $langDefault['it'] ?? '',
    ];

    // Compute the CURRENT effective value for each locale — exactly what a
    // public visitor would see on the site right now. Matches pcontent()'s
    // own fallback chain so the editor is a faithful mirror of live state:
    //
    //     DB override (this locale)
    //   → lang file (this locale)
    //   → DB override (English)
    //   → lang file (English)
    //   → '' (nothing configured)
    //
    // This is the fix the admin asked for: every input shows what the page
    // currently renders, making fields easy to identify at a glance.
    $effective = function (string $locale) use ($row, $defaults, $key) {
        $candidates = [
            $row?->{"value_{$locale}"},
            $defaults[$locale] ?? null,
            $row?->value_en,
            $defaults['en'] ?? null,
        ];
        foreach ($candidates as $v) {
            if ($v !== null && $v !== '') return (string) $v;
        }
        // Last resort: resolve directly from the lang file using
        // data_get() which handles numeric array indices. This
        // guarantees the field always shows a value even when
        // both the DB row and controller-provided defaults are empty.
        $dotPos = strpos($key, '.');
        if ($dotPos !== false) {
            $group = substr($key, 0, $dotPos);
            $path = substr($key, $dotPos + 1);
            $file = lang_path("{$locale}/{$group}.php");
            if (!file_exists($file)) $file = lang_path("en/{$group}.php");
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
            <form action="{{ route('admin.page-content.reset', $pageSlug) }}" method="POST" class="inline flex-shrink-0"
                  onsubmit="return confirm('Reset this field back to the default? Your custom value will be lost.');">
                @csrf
                <input type="hidden" name="key" value="{{ $key }}">
                <button type="submit" class="text-[10px] tracking-[0.15em] uppercase text-delos-muted hover:text-red-600 transition-colors whitespace-nowrap">
                    Reset to default
                </button>
            </form>
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
            'csrf' => csrf_token(),
        ]) }})" class="space-y-3">
            <div x-show="currentUrl" class="relative rounded-lg overflow-hidden bg-delos-ivory/60 border border-delos-dark/8">
                <span x-show="isCustom" class="absolute top-3 left-3 z-10 inline-flex items-center gap-1 px-2 py-1 rounded text-[9px] tracking-[0.15em] uppercase font-semibold text-delos-dark bg-delos-gold">Customized</span>
                <span x-show="!isCustom" class="absolute top-3 left-3 z-10 inline-flex items-center gap-1 px-2 py-1 rounded text-[9px] tracking-[0.15em] uppercase font-medium text-delos-dark-2 bg-white/80">Current default</span>
                @if($type === 'image')
                    <img :src="currentUrl" alt="" class="w-full h-auto max-h-[300px] object-cover">
                @else
                    <video :src="currentUrl" controls class="w-full h-auto max-h-[400px]"></video>
                @endif
                <button type="button" x-show="isCustom" @click="clear()"
                        class="absolute top-3 right-3 z-10 px-3 py-1.5 bg-red-600/90 hover:bg-red-600 text-white text-[10px] tracking-[0.15em] uppercase rounded transition-colors">
                    Reset to default
                </button>
            </div>

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
                    @if($type === 'video') MP4, WebM, MOV · max 50MB @else JPG, PNG, WebP · max 5MB @endif
                </p>
                <input type="file"
                       @change="onFile($event)"
                       accept="@if($type === 'video') video/mp4,video/webm,video/quicktime @else image/jpeg,image/png,image/webp @endif"
                       class="sr-only">
            </label>

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
                    async onFile(event) {
                        const file = event.target.files[0];
                        if (!file) return;
                        // Hard client-side guard: PHP's default post_max_size is
                        // 8M. If we let an oversize file go through, PHP drops
                        // the body, returns HTML, and the user sees the
                        // cryptic "string did not match expected pattern" JSON
                        // parse error. Give them a real message up front.
                        const maxBytes = config.type === 'video' ? 50 * 1024 * 1024 : 5 * 1024 * 1024;
                        if (file.size > maxBytes) {
                            this.status = `File is too large (${(file.size / (1024 * 1024)).toFixed(1)}MB). Max is ${maxBytes / (1024 * 1024)}MB.`;
                            this.statusType = 'error';
                            event.target.value = '';
                            return;
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
                }));
            });
        </script>
    @endpush
@endonce
