@extends('layouts.app')

@section('title', pcontent('seo.contact.title'))
@section('description', pcontent('seo.contact.description'))

@section('content')

{{-- Hero Image --}}
<section class="relative h-[55vh] lg:h-[60vh] overflow-hidden">
    <x-responsive-image :src="pcontent('contact.hero.image', 'delos-wall-branding.jpg')"
        :mobile-src="pcontent_optional('contact.hero.image_mobile')"
        :focal="pcontent_optional('contact.hero.image_focal')"
        alt="Delos International Wall"
        sizes="100vw"
        loading="eager"
        fetchpriority="high"
        class="w-full h-full object-cover" />
</section>

{{-- Page Title --}}
<section class="bg-delos-cream pt-16 lg:pt-20 pb-8 lg:pb-10">
    <div data-motion-group="hero" class="max-w-[1400px] mx-auto px-6 lg:px-12 text-center">
        <div data-motion="fade-up" class="inline-flex items-center gap-3 mb-5">
            <span class="w-8 h-px bg-delos-gold"></span>
            <span class="text-overline text-delos-gold">{{ pcontent('contact.hero.overline') }}</span>
            <span class="w-8 h-px bg-delos-gold"></span>
        </div>
        <h1 data-motion="fade-up" class="font-serif text-delos-dark text-5xl lg:text-7xl font-light leading-tight">
            {{ pcontent('contact.hero.heading_1') }}<br>
            <em class="text-delos-gold not-italic">{{ pcontent('contact.hero.heading_accent') }}</em>
        </h1>
    </div>
</section>

{{-- Contact Form --}}
<section class="section-padding bg-delos-cream">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">

        <div class="grid lg:grid-cols-2 gap-16 lg:gap-24 items-start">
            <div data-motion-group="contact-copy">
                <div data-motion-line class="w-16 h-px bg-delos-gold mb-5"></div>
                <p data-motion="fade-up" class="text-overline text-delos-gold mb-4">{{ pcontent('contact.intro.overline') }}</p>
                <h2 data-motion="fade-up" class="text-heading-2 text-delos-dark leading-tight mb-6">
                    {{ pcontent('contact.intro.heading_1') }}<br>
                    <em class="text-delos-gold not-italic">{{ pcontent('contact.intro.heading_accent') }}</em>
                    @if(pcontent('contact.intro.heading_2'))
                        <br>{{ pcontent('contact.intro.heading_2') }}
                    @endif
                </h2>
                <p data-motion="fade-up" class="text-body text-delos-muted">
                    {{ pcontent('contact.intro.body') }}
                </p>
            </div>

            {{-- Contact form — pick WhatsApp or Email, pick a branch, then the
                 submit button hands off to wa.me or mailto: with a nicely
                 formatted message. No server round-trip: the visitor's own
                 WhatsApp / mail app sends from their real identity. --}}
            @php
                $branchesJson = ($contactBranches ?? collect())->map(fn ($b) => [
                    'id'       => $b->id,
                    'city'     => $b->localized('name'),
                    'whatsapp' => wa_digits($b->whatsapp ?: $b->phone),
                    'email'    => $b->email,
                ])->values();
                $formCopy = [
                    'noWhatsapp' => pcontent('contact.form.branch_no_whatsapp'),
                    'noEmail'    => pcontent('contact.form.branch_no_email'),
                ];
            @endphp

            <form data-motion="fade-up" x-data="contactForm()" @submit.prevent="send"
                  class="space-y-5 font-sans" novalidate>
                @csrf

                {{-- Method toggle — WhatsApp vs Email --}}
                <fieldset class="border-0 p-0 m-0">
                    <legend class="block text-[11px] tracking-[0.2em] uppercase text-delos-muted mb-3">
                        {{ pcontent('contact.form.method_legend') }}
                    </legend>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer flex items-center justify-center gap-2 px-4 py-3 border transition-colors"
                               :class="method==='whatsapp' ? 'border-delos-gold bg-delos-gold/5 text-delos-dark' : 'border-delos-dark/20 text-delos-muted hover:border-delos-dark/40'">
                            <input type="radio" name="method" value="whatsapp" x-model="method" class="sr-only">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            <span class="text-sm font-medium">{{ pcontent('contact.form.method_whatsapp') }}</span>
                        </label>
                        <label class="cursor-pointer flex items-center justify-center gap-2 px-4 py-3 border transition-colors"
                               :class="method==='email' ? 'border-delos-gold bg-delos-gold/5 text-delos-dark' : 'border-delos-dark/20 text-delos-muted hover:border-delos-dark/40'">
                            <input type="radio" name="method" value="email" x-model="method" class="sr-only">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l9 6 9-6M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-sm font-medium">{{ pcontent('contact.form.method_email') }}</span>
                        </label>
                    </div>
                </fieldset>

                {{-- Full name --}}
                <div>
                    <label for="contact-name" class="block text-[11px] tracking-[0.2em] uppercase text-delos-muted mb-2">{{ pcontent('contact.form.name') }}</label>
                    <input type="text" id="contact-name" x-model="name" required
                           class="w-full px-4 py-3 bg-transparent border border-delos-dark/20 text-delos-dark text-sm focus:border-delos-gold focus:outline-none transition-colors duration-300 placeholder-delos-muted/50"
                           placeholder="{{ pcontent('contact.form.name_placeholder') }}">
                </div>

                {{-- Contact detail — phone when WhatsApp, email when Email --}}
                <div x-show="method==='whatsapp'" x-cloak>
                    <label for="contact-phone" class="block text-[11px] tracking-[0.2em] uppercase text-delos-muted mb-2">{{ pcontent('contact.form.phone') }}</label>
                    <input type="tel" id="contact-phone" x-model="phone" :required="method==='whatsapp'"
                           class="w-full px-4 py-3 bg-transparent border border-delos-dark/20 text-delos-dark text-sm focus:border-delos-gold focus:outline-none transition-colors duration-300 placeholder-delos-muted/50"
                           placeholder="{{ pcontent('contact.form.phone_placeholder') }}">
                </div>
                <div x-show="method==='email'" x-cloak>
                    <label for="contact-email" class="block text-[11px] tracking-[0.2em] uppercase text-delos-muted mb-2">{{ pcontent('contact.form.email') }}</label>
                    <input type="email" id="contact-email" x-model="email" :required="method==='email'"
                           class="w-full px-4 py-3 bg-transparent border border-delos-dark/20 text-delos-dark text-sm focus:border-delos-gold focus:outline-none transition-colors duration-300 placeholder-delos-muted/50"
                           placeholder="{{ pcontent('contact.form.email_placeholder') }}">
                </div>

                {{-- Branch selector — routes the submission to the chosen branch --}}
                <div>
                    <label for="contact-branch" class="block text-[11px] tracking-[0.2em] uppercase text-delos-muted mb-2">{{ pcontent('contact.form.branch') }}</label>
                    <div style="position: relative;">
                        <select id="contact-branch" x-model="branchId" required
                                class="w-full px-4 py-3 bg-transparent border border-delos-dark/20 text-delos-dark text-sm focus:border-delos-gold focus:outline-none transition-colors duration-300 cursor-pointer"
                                style="-webkit-appearance: none; -moz-appearance: none; appearance: none; border-radius: 0; padding-right: 2.5rem;">
                            <option value="">{{ pcontent('contact.form.branch_placeholder') }}</option>
                            @foreach($contactBranches ?? collect() as $b)
                                <option value="{{ $b->id }}">{{ $b->localized('name') }}</option>
                            @endforeach
                        </select>
                        <svg style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); pointer-events: none;" class="w-4 h-4 text-delos-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                    <p x-show="branchMissingChannel" x-cloak class="mt-2 text-xs text-red-600 leading-relaxed" x-text="branchMissingChannelMessage"></p>
                </div>

                {{-- Service / area of interest --}}
                <div>
                    <label for="contact-service" class="block text-[11px] tracking-[0.2em] uppercase text-delos-muted mb-2">{{ pcontent('contact.form.service') }}</label>
                    <div style="position: relative;">
                        <select id="contact-service" x-model="service"
                                class="w-full px-4 py-3 bg-transparent border border-delos-dark/20 text-delos-dark text-sm focus:border-delos-gold focus:outline-none transition-colors duration-300 cursor-pointer"
                                style="-webkit-appearance: none; -moz-appearance: none; appearance: none; border-radius: 0; padding-right: 2.5rem;">
                            <option value="">{{ pcontent('contact.form.service_placeholder') }}</option>
                            @foreach([0,1,2,3,4,5] as $i)
                                <option>{{ pcontent("contact.form.service_options.{$i}") }}</option>
                            @endforeach
                        </select>
                        <svg style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); pointer-events: none;" class="w-4 h-4 text-delos-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>

                {{-- Message --}}
                <div>
                    <label for="contact-message" class="block text-[11px] tracking-[0.2em] uppercase text-delos-muted mb-2">{{ pcontent('contact.form.message') }}</label>
                    <textarea id="contact-message" x-model="message" rows="4" required maxlength="1500"
                              class="w-full px-4 py-3 bg-transparent border border-delos-dark/20 text-delos-dark text-sm focus:border-delos-gold focus:outline-none transition-colors duration-300 resize-none placeholder-delos-muted/50"
                              placeholder="{{ pcontent('contact.form.message_placeholder') }}"></textarea>
                </div>

                {{-- Submit — label mirrors the chosen method --}}
                <button type="submit" :disabled="!canSubmit"
                        class="btn-ripple w-full py-4 bg-delos-dark text-delos-cream text-[12px] tracking-[0.3em] uppercase font-medium hover:bg-delos-gold hover:text-delos-dark disabled:opacity-40 disabled:cursor-not-allowed transition-all duration-300">
                    <span x-text="method==='whatsapp' ? submitLabels.whatsapp : submitLabels.email"></span>
                </button>
            </form>

            {{-- Alpine state for the form above.
                 Branches are injected as JSON so we don't have to walk the
                 DOM for data-attributes; copy strings come from pcontent()
                 so admins can edit them in the page-content editor. --}}
            <script>
                (function () {
                    const branches = @json($branchesJson);
                    const copy = @json($formCopy);
                    const submitLabels = {
                        whatsapp: @json(pcontent('contact.form.submit_whatsapp')),
                        email:    @json(pcontent('contact.form.submit_email')),
                    };

                    function initContactForm() {
                        if (typeof Alpine === 'undefined' || Alpine.$data === undefined) {
                            return document.addEventListener('alpine:init', registerComponent);
                        }
                        registerComponent();
                    }
                    function registerComponent() {
                        Alpine.data('contactForm', () => ({
                            method: 'whatsapp',
                            name: '', phone: '', email: '',
                            branchId: '', service: '', message: '',
                            submitLabels,

                            get selectedBranch() {
                                if (!this.branchId) return null;
                                return branches.find(b => String(b.id) === String(this.branchId)) || null;
                            },
                            get branchMissingChannel() {
                                const b = this.selectedBranch;
                                if (!b) return false;
                                return this.method === 'whatsapp' ? !b.whatsapp : !b.email;
                            },
                            get branchMissingChannelMessage() {
                                return this.method === 'whatsapp' ? copy.noWhatsapp : copy.noEmail;
                            },
                            get canSubmit() {
                                if (!this.name.trim() || !this.message.trim() || !this.branchId) return false;
                                if (this.method === 'whatsapp' && !this.phone.trim()) return false;
                                if (this.method === 'email' && !/^\S+@\S+\.\S+$/.test(this.email.trim())) return false;
                                return !this.branchMissingChannel;
                            },

                            send() {
                                if (!this.canSubmit) return;
                                const b = this.selectedBranch;
                                const service = this.service || '—';

                                if (this.method === 'whatsapp') {
                                    const body = [
                                        '*Delos International — New consultation request*',
                                        '',
                                        '*Name:* ' + this.name.trim(),
                                        '*Phone:* ' + this.phone.trim(),
                                        '*Showroom:* ' + b.city,
                                        '*Area of interest:* ' + service,
                                        '',
                                        '*Message:*',
                                        this.message.trim(),
                                        '',
                                        '— Sent from delos-international.com',
                                    ].join('\n');
                                    const url = 'https://wa.me/' + b.whatsapp + '?text=' + encodeURIComponent(body);
                                    window.open(url, '_blank', 'noopener');
                                    return;
                                }

                                const subject = 'New consultation request — ' + b.city + ' showroom';
                                const body = [
                                    'Name: ' + this.name.trim(),
                                    'Email: ' + this.email.trim(),
                                    'Showroom: ' + b.city,
                                    'Area of interest: ' + service,
                                    '',
                                    'Message:',
                                    this.message.trim(),
                                    '',
                                    '— Sent from delos-international.com',
                                ].join('\n');
                                const url = 'mailto:' + b.email
                                    + '?subject=' + encodeURIComponent(subject)
                                    + '&body=' + encodeURIComponent(body);
                                window.location.href = url;
                            },
                        }));
                    }
                    initContactForm();
                })();
            </script>
        </div>

    </div>
</section>

@endsection
