{{--
    Reusable rich text editor using TipTap (CDN) + Alpine.js.

    Usage:
        @include('admin.partials.rich-text-editor', [
            'name' => 'description_en',
            'value' => old('description_en', $model->description_en ?? ''),
            'id' => 'description_en',
            'dir' => 'ltr',
            'minHeight' => 140,
        ])

    Injects a hidden <input> named "name" that's updated whenever the
    editor content changes. Falls back gracefully to a <textarea> if
    TipTap fails to load.
--}}

@once
    @push('head')
        <style>
            .prose-editor {
                font-family: 'Inter', sans-serif;
                font-size: 14px;
                line-height: 1.6;
                color: #2C2220;
            }
            .prose-editor p { margin: 0 0 0.75em; }
            .prose-editor p:last-child { margin-bottom: 0; }
            .prose-editor strong { font-weight: 600; color: #1a1412; }
            .prose-editor em { font-style: italic; }
            .prose-editor u { text-decoration: underline; }
            .prose-editor h1, .prose-editor h2, .prose-editor h3 {
                font-family: 'Cormorant Garamond', serif;
                color: #1a1412;
                margin: 1em 0 0.5em;
                line-height: 1.25;
                font-weight: 500;
            }
            .prose-editor h1 { font-size: 1.75em; }
            .prose-editor h2 { font-size: 1.4em; }
            .prose-editor h3 { font-size: 1.15em; }
            .prose-editor ul, .prose-editor ol { margin: 0 0 0.75em 1.5em; padding: 0; }
            .prose-editor ul { list-style: disc; }
            .prose-editor ol { list-style: decimal; }
            .prose-editor li { margin-bottom: 0.25em; }
            .prose-editor a { color: #C49A7A; text-decoration: underline; }
            .prose-editor blockquote {
                border-left: 3px solid #C49A7A;
                padding-left: 1em;
                margin: 0.75em 0;
                font-style: italic;
                color: #4a3e3a;
            }
            .prose-editor:focus { outline: none; }
            .prose-editor[dir="rtl"] {
                text-align: right;
                font-family: 'Cairo', 'Inter', sans-serif;
            }
            .prose-editor[dir="rtl"] ul,
            .prose-editor[dir="rtl"] ol { margin: 0 1.5em 0.75em 0; }
            .prose-editor[dir="rtl"] blockquote {
                border-left: none;
                border-right: 3px solid #C49A7A;
                padding-left: 0;
                padding-right: 1em;
            }

            .rte-toolbar-btn {
                padding: 6px 8px;
                border-radius: 4px;
                color: #7A6B65;
                transition: all 0.15s;
                cursor: pointer;
                background: transparent;
                border: none;
                font-size: 13px;
                min-width: 28px;
                line-height: 1;
            }
            .rte-toolbar-btn:hover { background: rgba(196,154,122,0.1); color: #2C2220; }
            .rte-toolbar-btn.is-active { background: rgba(196,154,122,0.15); color: #A07E5F; }
            .rte-toolbar-divider {
                width: 1px;
                height: 18px;
                background: rgba(26,20,18,0.1);
                margin: 0 2px;
            }
        </style>
        <script src="https://cdn.jsdelivr.net/npm/@tiptap/core@2.10.3/dist/index.umd.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@tiptap/pm@2.10.3/dist/index.umd.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@tiptap/starter-kit@2.10.3/dist/index.umd.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@tiptap/extension-link@2.10.3/dist/index.umd.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@tiptap/extension-underline@2.10.3/dist/index.umd.min.js"></script>
        <script>
            function richTextEditor(initialValue, name, dir) {
                return {
                    editor: null,
                    value: initialValue,
                    name: name,
                    dir: dir || 'ltr',
                    activeMarks: {},

                    init() {
                        const Editor = window.tiptap?.Editor || window.TiptapCore?.Editor;
                        const StarterKit = window.TiptapStarterKit?.default || window.TiptapStarterKit;
                        const Link = window.TiptapExtensionLink?.default || window.TiptapExtensionLink;
                        const Underline = window.TiptapExtensionUnderline?.default || window.TiptapExtensionUnderline;

                        if (!Editor || !StarterKit) {
                            console.warn('TipTap failed to load — falling back to textarea');
                            return;
                        }

                        const extensions = [StarterKit];
                        if (Link) extensions.push(Link.configure({ openOnClick: false, HTMLAttributes: { rel: 'noopener noreferrer' } }));
                        if (Underline) extensions.push(Underline);

                        this.editor = new Editor({
                            element: this.$refs.editor,
                            extensions,
                            content: this.value,
                            editorProps: {
                                attributes: {
                                    class: 'prose-editor px-4 py-3 min-h-[140px]',
                                    dir: this.dir,
                                },
                            },
                            onUpdate: ({ editor }) => {
                                this.value = editor.getHTML();
                                this.$refs.hidden.value = this.value;
                                this.updateActiveMarks();
                            },
                            onSelectionUpdate: () => this.updateActiveMarks(),
                        });
                    },

                    updateActiveMarks() {
                        if (!this.editor) return;
                        this.activeMarks = {
                            bold: this.editor.isActive('bold'),
                            italic: this.editor.isActive('italic'),
                            underline: this.editor.isActive('underline'),
                            h2: this.editor.isActive('heading', { level: 2 }),
                            h3: this.editor.isActive('heading', { level: 3 }),
                            bulletList: this.editor.isActive('bulletList'),
                            orderedList: this.editor.isActive('orderedList'),
                            blockquote: this.editor.isActive('blockquote'),
                            link: this.editor.isActive('link'),
                        };
                    },

                    run(cmd, arg = null) {
                        if (!this.editor) return;
                        const chain = this.editor.chain().focus();
                        switch (cmd) {
                            case 'bold': chain.toggleBold().run(); break;
                            case 'italic': chain.toggleItalic().run(); break;
                            case 'underline': chain.toggleUnderline().run(); break;
                            case 'h2': chain.toggleHeading({ level: 2 }).run(); break;
                            case 'h3': chain.toggleHeading({ level: 3 }).run(); break;
                            case 'ul': chain.toggleBulletList().run(); break;
                            case 'ol': chain.toggleOrderedList().run(); break;
                            case 'quote': chain.toggleBlockquote().run(); break;
                            case 'link':
                                const url = prompt('Enter URL:', this.editor.getAttributes('link').href || 'https://');
                                if (url === null) return;
                                if (url === '') { chain.unsetLink().run(); }
                                else { chain.extendMarkRange('link').setLink({ href: url }).run(); }
                                break;
                            case 'unlink': chain.unsetLink().run(); break;
                            case 'clear': chain.clearNodes().unsetAllMarks().run(); break;
                        }
                    },
                };
            }
        </script>
    @endpush
@endonce

@php
    $rteName = $name;
    $rteId = $id ?? $name;
    $rteValue = $value ?? '';
    $rteDir = $dir ?? 'ltr';
    $rteMinHeight = $minHeight ?? 140;
@endphp

<div
    x-data="richTextEditor(@js($rteValue), @js($rteName), @js($rteDir))"
    class="border border-delos-dark/15 rounded-lg bg-white overflow-hidden focus-within:border-delos-gold focus-within:ring-2 focus-within:ring-delos-gold/15 transition-all"
>
    {{-- Toolbar --}}
    <div class="flex items-center flex-wrap gap-0.5 px-2 py-1.5 border-b border-delos-dark/8 bg-delos-ivory/40">
        <button type="button" class="rte-toolbar-btn" :class="{'is-active': activeMarks.bold}" @click="run('bold')" title="Bold">
            <strong>B</strong>
        </button>
        <button type="button" class="rte-toolbar-btn" :class="{'is-active': activeMarks.italic}" @click="run('italic')" title="Italic">
            <em>I</em>
        </button>
        <button type="button" class="rte-toolbar-btn" :class="{'is-active': activeMarks.underline}" @click="run('underline')" title="Underline">
            <u>U</u>
        </button>
        <div class="rte-toolbar-divider"></div>
        <button type="button" class="rte-toolbar-btn" :class="{'is-active': activeMarks.h2}" @click="run('h2')" title="Heading 2">H2</button>
        <button type="button" class="rte-toolbar-btn" :class="{'is-active': activeMarks.h3}" @click="run('h3')" title="Heading 3">H3</button>
        <div class="rte-toolbar-divider"></div>
        <button type="button" class="rte-toolbar-btn" :class="{'is-active': activeMarks.bulletList}" @click="run('ul')" title="Bullet list">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <button type="button" class="rte-toolbar-btn" :class="{'is-active': activeMarks.orderedList}" @click="run('ol')" title="Numbered list">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20h14M7 12h14M7 4h14M3 20h.01M3 12h.01M3 4h.01"/></svg>
        </button>
        <button type="button" class="rte-toolbar-btn" :class="{'is-active': activeMarks.blockquote}" @click="run('quote')" title="Quote">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M7 7h4v10H7zM13 7h4v10h-4z" opacity=".3"/><path stroke="currentColor" stroke-width="1.5" fill="none" d="M9 7c0 4-2 6-2 6M15 7c0 4-2 6-2 6"/></svg>
        </button>
        <div class="rte-toolbar-divider"></div>
        <button type="button" class="rte-toolbar-btn" :class="{'is-active': activeMarks.link}" @click="run('link')" title="Add/edit link">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
        </button>
        <div class="ml-auto flex items-center">
            <button type="button" class="rte-toolbar-btn text-[11px] tracking-wider uppercase text-delos-muted/70" @click="run('clear')" title="Clear formatting">
                Clear
            </button>
        </div>
    </div>

    {{-- Editor area --}}
    <div x-ref="editor" style="min-height: {{ $rteMinHeight }}px;"></div>

    {{-- Fallback textarea shown if TipTap fails. Also provides form value. --}}
    <textarea
        x-show="!editor"
        x-model="value"
        class="w-full px-4 py-3 text-sm font-sans resize-vertical border-0 focus:outline-none"
        dir="{{ $rteDir }}"
        style="min-height: {{ $rteMinHeight }}px; display: none;"
    ></textarea>

    {{-- Hidden input that posts with the form --}}
    <input type="hidden" name="{{ $rteName }}" id="{{ $rteId }}" x-ref="hidden" :value="value">
</div>
