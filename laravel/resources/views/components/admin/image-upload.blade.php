@props([
    // Display label shown above the widget (e.g. "Showroom photo").
    'label' => 'Image',

    // Base field name. The desktop upload posts to this name ("image"),
    // the mobile variant to "{fieldName}_mobile", and the focal point
    // to "focal_point" (which is a fixed column name on all 5 models).
    'fieldName' => 'image',

    // Controllers already check a hidden input named "remove_image" to
    // null out the main image column when the admin hits Remove. Expose
    // its name so non-default fieldNames can use a matching clear flag.
    'clearMainFieldName' => 'remove_image',

    // Currently-saved image URLs shown in the preview area. Either may be
    // null to indicate "nothing uploaded yet" — the widget still renders.
    'currentUrl' => null,
    'mobileCurrentUrl' => null,

    // Currently-saved focal point string like "30% 60%", or null → center.
    'focal' => null,

    // File-size and accept constraints surfaced to the user.
    // Backend FormRequest actually allows max:20480 KB (20MB) and php.ini
    // allows 60M, but the live Hostinger proxy/WAF drops large uploads
    // around 5-6MB in practice, so we surface 5MB as the honest cap the
    // admin can rely on. The component also auto-compresses raster images
    // on pick (resize to 2560px long edge + JPEG 0.82) so a typical DSLR
    // photo gets down to ~1-2MB long before it hits this gate.
    'accept' => 'image/jpeg,image/png,image/webp',
    'maxMb' => 5,

    // Optional helper text under the desktop dropzone.
    'hint' => null,
])

@php
    // Parse the current focal into {x,y} integer percent for the Alpine
    // picker. Default to centered.
    $focalX = 50; $focalY = 50;
    if ($focal && preg_match('/^(\d{1,3})%\s+(\d{1,3})%$/', trim($focal), $m)) {
        $focalX = max(0, min(100, (int) $m[1]));
        $focalY = max(0, min(100, (int) $m[2]));
    }
    $mobileFieldName = $fieldName . '_mobile';
@endphp

<div class="space-y-6"
     x-data="adminImageUpload({
        desktopInitialUrl: @js($currentUrl),
        mobileInitialUrl: @js($mobileCurrentUrl),
        focalX: {{ $focalX }},
        focalY: {{ $focalY }},
        maxBytes: {{ (int) $maxMb * 1024 * 1024 }},
     })">

    {{-- ─── Desktop upload ─────────────────────────────────────── --}}
    <div>
        <div class="flex items-center justify-between mb-2">
            <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium">{{ $label }}</label>
            <button type="button" x-show="displayUrl && desktopInitialUrl" x-cloak @click="clearDesktop()"
                    class="text-[10px] tracking-[0.15em] uppercase text-delos-muted hover:text-red-600 transition-colors">
                Remove
            </button>
        </div>

        {{-- Preview with focal-point picker overlay. Clicking anywhere on
             the image moves the gold crosshair to that point and updates
             the hidden focal_point input. --}}
        <div x-show="displayUrl" x-cloak class="relative rounded-lg overflow-hidden bg-delos-ivory/60 border border-delos-dark/8">
            <div class="relative" x-ref="focalTarget" @click="setFocalFromEvent($event)" style="cursor: crosshair;">
                <img :src="displayUrl"
                     :style="`object-position: ${focalX}% ${focalY}%`"
                     class="w-full max-h-[360px] object-cover block"
                     alt="">
                {{-- Gold crosshair marking the current focal point. --}}
                <div class="absolute pointer-events-none"
                     :style="`left: ${focalX}%; top: ${focalY}%; transform: translate(-50%, -50%);`">
                    <div class="w-6 h-6 rounded-full border-2 border-delos-gold bg-delos-gold/25 shadow-[0_0_0_3px_rgba(196,154,122,0.15)]"></div>
                </div>
            </div>
            <div class="flex items-center justify-between px-3 py-2 bg-white/70 text-[10px] tracking-[0.15em] uppercase">
                <span class="text-delos-muted">Focal point <span class="text-delos-dark font-mono font-semibold normal-case tracking-normal ml-2" x-text="`${focalX}% / ${focalY}%`"></span></span>
                <button type="button" @click="resetFocal()" class="text-delos-muted hover:text-delos-gold normal-case tracking-normal font-sans">Reset to centre</button>
            </div>
        </div>

        {{-- Upload dropzone. A fresh file read replaces the preview via
             FileReader so the admin sees what they're about to submit
             without needing to save the form first. --}}
        <label class="mt-3 block border-2 border-dashed border-delos-dark/15 hover:border-delos-gold rounded-lg p-4 text-center cursor-pointer transition-colors">
            <svg class="w-5 h-5 mx-auto text-delos-muted mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-sm text-delos-dark-2 font-medium mb-0.5" x-text="desktopInitialUrl ? 'Replace image' : 'Upload image'"></p>
            <p class="text-xs text-delos-muted">JPG, PNG, WebP · max {{ $maxMb }}MB</p>
            <input type="file" name="{{ $fieldName }}"
                   accept="{{ $accept }}"
                   @change="onDesktopFile($event)"
                   class="sr-only">
        </label>
        @if($hint)
            <p class="mt-2 text-xs text-delos-muted/80">{{ $hint }}</p>
        @endif
        <div x-show="desktopError" x-cloak class="mt-3 flex items-start gap-2 p-3 bg-red-50 border border-red-200 rounded-lg">
            <svg class="w-4 h-4 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/></svg>
            <p class="text-sm text-red-700 font-medium" x-text="desktopError"></p>
        </div>
    </div>

    {{-- ─── Mobile variant (optional) ──────────────────────────── --}}
    <div class="pt-5 border-t border-delos-dark/5">
        <div class="flex items-center justify-between mb-2">
            <label class="block text-[10px] tracking-[0.2em] uppercase text-delos-muted font-medium">Mobile variant <span class="normal-case tracking-normal text-delos-muted/60">(optional)</span></label>
            <button type="button" x-show="mobileDisplayUrl && mobileInitialUrl" x-cloak @click="clearMobile()"
                    class="text-[10px] tracking-[0.15em] uppercase text-delos-muted hover:text-red-600 transition-colors">
                Remove
            </button>
        </div>
        <p class="text-xs text-delos-muted/80 mb-3">Shown on phones (≤ 767px). Leave blank to use the image above on every screen.</p>

        <div x-show="mobileDisplayUrl" x-cloak class="rounded-lg overflow-hidden bg-delos-ivory/60 border border-delos-dark/8 mb-3">
            <img :src="mobileDisplayUrl" class="w-full max-h-[280px] object-cover block" alt="">
        </div>

        <label class="block border-2 border-dashed border-delos-dark/15 hover:border-delos-gold rounded-lg p-4 text-center cursor-pointer transition-colors">
            <svg class="w-5 h-5 mx-auto text-delos-muted mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            <p class="text-sm text-delos-dark-2 font-medium mb-0.5" x-text="mobileInitialUrl ? 'Replace mobile variant' : 'Upload mobile variant'"></p>
            <p class="text-xs text-delos-muted">JPG, PNG, WebP · max {{ $maxMb }}MB</p>
            <input type="file" name="{{ $mobileFieldName }}"
                   accept="{{ $accept }}"
                   @change="onMobileFile($event)"
                   x-ref="mobileInput"
                   class="sr-only">
        </label>
        <div x-show="mobileError" x-cloak class="mt-3 flex items-start gap-2 p-3 bg-red-50 border border-red-200 rounded-lg">
            <svg class="w-4 h-4 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/></svg>
            <p class="text-sm text-red-700 font-medium" x-text="mobileError"></p>
        </div>
    </div>

    {{-- Hidden state inputs submitted with the parent form. --}}
    <input type="hidden" name="focal_point" x-bind:value="`${focalX}% ${focalY}%`">
    <input type="hidden" name="clear_{{ $mobileFieldName }}" x-bind:value="clearMobileFlag ? '1' : ''">
    <input type="hidden" name="{{ $clearMainFieldName }}" x-bind:value="clearDesktopFlag ? '1' : '0'">

</div>

@once
    @push('head')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('adminImageUpload', (config) => ({
                    desktopInitialUrl: config.desktopInitialUrl || null,
                    mobileInitialUrl:  config.mobileInitialUrl  || null,
                    desktopPreviewUrl: null,
                    mobilePreviewUrl:  null,
                    desktopError: '',
                    mobileError:  '',
                    focalX: config.focalX,
                    focalY: config.focalY,
                    clearMobileFlag: false,
                    clearDesktopFlag: false,
                    maxBytes: config.maxBytes,

                    // Either the just-picked file (for instant preview) or
                    // the existing DB URL. Drives the preview <img>.
                    get displayUrl() {
                        if (this.clearDesktopFlag) return null;
                        return this.desktopPreviewUrl || this.desktopInitialUrl || null;
                    },
                    get mobileDisplayUrl() {
                        if (this.clearMobileFlag) return null;
                        return this.mobilePreviewUrl || this.mobileInitialUrl || null;
                    },

                    async onDesktopFile(event) {
                        const input = event.target;
                        const original = input.files[0];
                        if (!original) return;
                        this.desktopError = '';
                        let file = original;
                        try {
                            file = await this.compressImage(original);
                            if (file !== original) this.replaceInputFile(input, file);
                        } catch (_) { /* fall through with original file */ }
                        if (file.size > this.maxBytes) {
                            this.desktopError = `File too large — max ${(this.maxBytes / 1024 / 1024).toFixed(0)}MB. Try a smaller image.`;
                            input.value = '';
                            return;
                        }
                        // Picking a new file cancels any pending clear.
                        this.clearDesktopFlag = false;
                        const reader = new FileReader();
                        reader.onload = (e) => { this.desktopPreviewUrl = e.target.result; };
                        reader.readAsDataURL(file);
                    },
                    clearDesktop() {
                        if (!confirm('Remove this image? The field will fall back to its default on save.')) return;
                        this.desktopPreviewUrl = null;
                        this.desktopInitialUrl = null;
                        this.clearDesktopFlag = true;
                    },

                    async onMobileFile(event) {
                        const input = event.target;
                        const original = input.files[0];
                        if (!original) return;
                        this.mobileError = '';
                        let file = original;
                        try {
                            file = await this.compressImage(original);
                            if (file !== original) this.replaceInputFile(input, file);
                        } catch (_) { /* fall through with original file */ }
                        if (file.size > this.maxBytes) {
                            this.mobileError = `File too large — max ${(this.maxBytes / 1024 / 1024).toFixed(0)}MB. Try a smaller image.`;
                            input.value = '';
                            return;
                        }
                        // Picking a new file cancels any pending "clear" action
                        // so the user doesn't accidentally wipe it on submit.
                        this.clearMobileFlag = false;
                        const reader = new FileReader();
                        reader.onload = (e) => { this.mobilePreviewUrl = e.target.result; };
                        reader.readAsDataURL(file);
                    },

                    clearMobile() {
                        // Flag for the controller to null out the column on
                        // save. We also reset any unsaved preview state so
                        // the UI reflects the pending deletion.
                        if (!confirm('Remove the mobile variant? The desktop image will be shown on phones.')) return;
                        this.mobilePreviewUrl = null;
                        this.mobileInitialUrl = null;
                        this.clearMobileFlag = true;
                        if (this.$refs.mobileInput) this.$refs.mobileInput.value = '';
                    },

                    // Convert a click on the preview into an object-position
                    // percentage pair. clientX/Y are relative to the viewport;
                    // we subtract the element's bounding rect to get a local
                    // coordinate, then divide by its size.
                    setFocalFromEvent(event) {
                        const el = this.$refs.focalTarget;
                        if (!el) return;
                        const rect = el.getBoundingClientRect();
                        const x = ((event.clientX - rect.left) / rect.width)  * 100;
                        const y = ((event.clientY - rect.top)  / rect.height) * 100;
                        this.focalX = Math.max(0, Math.min(100, Math.round(x)));
                        this.focalY = Math.max(0, Math.min(100, Math.round(y)));
                    },
                    resetFocal() {
                        this.focalX = 50;
                        this.focalY = 50;
                    },

                    // Resize + re-encode raster uploads on the client so we
                    // never send a 10MB DSLR shot through Hostinger's proxy
                    // (it silently drops anything over ~5MB on this plan).
                    // Strategy: scale the longer edge to 2560px, draw on a
                    // white canvas (so PNG alpha → JPEG doesn't come out
                    // black), and re-encode as JPEG 0.82 — which is visually
                    // indistinguishable from the original for photography.
                    // Non-raster types (SVG/GIF/HEIC) and anything where the
                    // re-encode didn't help size-wise pass through untouched.
                    async compressImage(file) {
                        if (!/^image\/(jpeg|png|webp)$/i.test(file.type)) return file;
                        const dataUrl = await new Promise((resolve, reject) => {
                            const r = new FileReader();
                            r.onload = () => resolve(r.result);
                            r.onerror = reject;
                            r.readAsDataURL(file);
                        });
                        const img = await new Promise((resolve, reject) => {
                            const i = new Image();
                            i.onload = () => resolve(i);
                            i.onerror = reject;
                            i.src = dataUrl;
                        });
                        const MAX_DIM = 2560;
                        let w = img.naturalWidth || img.width;
                        let h = img.naturalHeight || img.height;
                        if (!w || !h) return file;
                        const longest = Math.max(w, h);
                        if (longest > MAX_DIM) {
                            const scale = MAX_DIM / longest;
                            w = Math.round(w * scale);
                            h = Math.round(h * scale);
                        }
                        const canvas = document.createElement('canvas');
                        canvas.width = w;
                        canvas.height = h;
                        const ctx = canvas.getContext('2d');
                        ctx.fillStyle = '#ffffff';
                        ctx.fillRect(0, 0, w, h);
                        ctx.drawImage(img, 0, 0, w, h);
                        const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/jpeg', 0.82));
                        if (!blob || blob.size >= file.size) return file;
                        const base = file.name.replace(/\.[^.]+$/, '');
                        return new File([blob], base + '.jpg', { type: 'image/jpeg', lastModified: Date.now() });
                    },

                    // After compressImage returns a new File we need to put
                    // it back into the <input type=file> so the browser
                    // submits *that* file, not the one the admin originally
                    // picked. DataTransfer is supported everywhere we care
                    // about (Safari 14.1+, Chrome, Firefox); if a browser
                    // doesn't support it we bail and the original upload
                    // goes through unchanged.
                    replaceInputFile(input, file) {
                        if (typeof DataTransfer === 'undefined') return;
                        try {
                            const dt = new DataTransfer();
                            dt.items.add(file);
                            input.files = dt.files;
                        } catch (_) { /* keep original file */ }
                    },
                }));
            });
        </script>
    @endpush
@endonce
