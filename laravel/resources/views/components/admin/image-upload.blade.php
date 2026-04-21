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
        <p x-show="desktopError" x-cloak class="mt-2 text-xs text-red-600" x-text="desktopError"></p>
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
        <p x-show="mobileError" x-cloak class="mt-2 text-xs text-red-600" x-text="mobileError"></p>
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
                        let file = event.target.files[0];
                        if (!file) return;

                        // Source-resolution guard. Runs first — no point
                        // auto-compressing a 1200px image we're going to reject
                        // for the full-width / hero row.
                        const minWidth = 2000;
                        const width = await this._imageWidth(file);
                        if (width && width < minWidth) {
                            this.desktopError =
                                `This image is only ${width}px wide. Full-width / hero images need at least ${minWidth}px ` +
                                `(2560×1440+ recommended for retina sharpness). Please upload a higher-resolution original.`;
                            event.target.value = '';
                            return;
                        }

                        // Auto-compress if the upload busts the server's 5MB
                        // ceiling. High-res originals from manufacturers are
                        // routinely 10–30MB; without this, the admin would
                        // have to re-export JPEGs by hand. Full pixel
                        // dimensions preserved — only quality steps down.
                        if (file.size > this.maxBytes) {
                            try {
                                const compressed = await this._compressImage(file, this.maxBytes);
                                if (compressed.size > this.maxBytes) {
                                    this.desktopError =
                                        `Can't get this image under ${(this.maxBytes/1024/1024).toFixed(0)}MB even at q=0.82. ` +
                                        `The source is probably 8K+ — please downscale to ~3200px before uploading.`;
                                    event.target.value = '';
                                    return;
                                }
                                file = compressed;
                                this._swapInputFile(event.target, file);
                            } catch (e) {
                                this.desktopError = 'Could not compress image: ' + (e.message || e);
                                event.target.value = '';
                                return;
                            }
                        }

                        this.desktopError = '';
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
                        let file = event.target.files[0];
                        if (!file) return;

                        // Mobile variant — lower threshold, phone viewports top
                        // out around 430 CSS px (~1290 physical px on DPR-3
                        // iPhone Pro Max). 1200px is the smallest width that
                        // still renders sharp on those displays.
                        const minWidth = 1200;
                        const width = await this._imageWidth(file);
                        if (width && width < minWidth) {
                            this.mobileError =
                                `This mobile image is only ${width}px wide. Phone screens need at least ${minWidth}px ` +
                                `for retina sharpness. Please upload a higher-resolution original.`;
                            event.target.value = '';
                            return;
                        }

                        // Same auto-compression path as desktop.
                        if (file.size > this.maxBytes) {
                            try {
                                const compressed = await this._compressImage(file, this.maxBytes);
                                if (compressed.size > this.maxBytes) {
                                    this.mobileError =
                                        `Can't get this mobile image under ${(this.maxBytes/1024/1024).toFixed(0)}MB at q=0.82. ` +
                                        `Please downscale the source before uploading.`;
                                    event.target.value = '';
                                    return;
                                }
                                file = compressed;
                                this._swapInputFile(event.target, file);
                            } catch (e) {
                                this.mobileError = 'Could not compress mobile image: ' + (e.message || e);
                                event.target.value = '';
                                return;
                            }
                        }

                        this.mobileError = '';
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

                    // Decode just enough of the picked file to read its
                    // natural pixel dimensions — no canvas, no re-encode —
                    // so the dimension guard can run before anything else.
                    // Returns 0 on decode failure (non-image, corrupt) so the
                    // caller treats those as "can't verify, let through".
                    _imageWidth(file) {
                        if (!file || !file.type || !file.type.startsWith('image/')) return Promise.resolve(0);
                        const url = URL.createObjectURL(file);
                        return new Promise((resolve) => {
                            const img = new Image();
                            img.onload = () => { URL.revokeObjectURL(url); resolve(img.naturalWidth || 0); };
                            img.onerror = () => { URL.revokeObjectURL(url); resolve(0); };
                            img.src = url;
                        });
                    },

                    // Browser-side re-encode to hit the server's 5MB ceiling
                    // without bouncing the upload back to the admin. Full
                    // pixel dimensions are preserved — only JPEG quality is
                    // stepped down. Ladder floors at q=0.82 because below
                    // that photographic content bands visibly. Same contract
                    // as page-content/_field.blade.php::_compressImage so the
                    // two forms behave identically.
                    async _compressImage(file, maxBytes) {
                        if (!file.type || !file.type.startsWith('image/')) return file;
                        if (file.size <= maxBytes) return file;

                        const url = URL.createObjectURL(file);
                        const img = await new Promise((resolve, reject) => {
                            const i = new Image();
                            i.onload = () => resolve(i);
                            i.onerror = () => reject(new Error('could not decode image'));
                            i.src = url;
                        });

                        const canvas = document.createElement('canvas');
                        canvas.width = img.naturalWidth;
                        canvas.height = img.naturalHeight;
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0);
                        URL.revokeObjectURL(url);

                        // Walk quality down. Early-exit the first time the
                        // blob fits; otherwise return the last (smallest)
                        // attempt so the caller can decide whether it's good
                        // enough or whether to show an error.
                        let bestBlob = null;
                        for (const q of [0.92, 0.90, 0.88, 0.85, 0.82]) {
                            const blob = await new Promise(r => canvas.toBlob(r, 'image/jpeg', q));
                            if (!blob) continue;
                            bestBlob = blob;
                            if (blob.size <= maxBytes) break;
                        }
                        if (!bestBlob) throw new Error('encoder returned no blob');

                        const newName = (file.name || 'image').replace(/\.\w+$/, '') + '.jpg';
                        return new File([bestBlob], newName, { type: 'image/jpeg' });
                    },

                    // Replace the File object on a native <input type=file>
                    // so a regular form submit POSTs the compressed blob.
                    // DataTransfer is the only cross-browser path for this;
                    // Safari/WebKit doesn't support the older input.files
                    // assignment otherwise.
                    _swapInputFile(inputEl, file) {
                        const dt = new DataTransfer();
                        dt.items.add(file);
                        inputEl.files = dt.files;
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
                }));
            });
        </script>
    @endpush
@endonce
