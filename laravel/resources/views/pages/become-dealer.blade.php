@extends('layouts.app')

@section('title', pcontent('seo.become_dealer.title'))
@section('description', pcontent('seo.become_dealer.description'))

@section('content')

{{-- HERO --}}
<section class="py-24 lg:py-32 bg-delos-cream relative">
    <x-admin-edit-pill page="become_dealer" label="Edit Become a Dealer hero" />
    <div class="max-w-3xl mx-auto px-6 lg:px-12 text-center">
        <div data-motion-group="dealer-hero">
            <p data-motion="fade-up" class="text-delos-gold text-[11px] tracking-[0.4em] uppercase font-medium mb-6" style="font-family: 'Inter', sans-serif;">{{ pcontent('become_dealer.hero.overline') }}</p>

            <h1 data-motion="fade-up" class="font-serif text-4xl lg:text-6xl font-light text-delos-dark leading-tight mb-8">
                {{ pcontent('become_dealer.hero.heading_1') }}<br>
                <em class="text-delos-gold not-italic">{{ pcontent('become_dealer.hero.heading_accent') }}</em>
            </h1>

            <div data-motion-line class="w-16 h-px bg-delos-gold mx-auto mb-8"></div>

            <p data-motion="fade-up" class="text-delos-muted text-base lg:text-lg leading-relaxed max-w-2xl mx-auto" style="font-family: 'Inter', sans-serif;">{{ pcontent('become_dealer.hero.sub') }}</p>
        </div>
    </div>
</section>

{{-- BENEFITS --}}
<section class="py-20 lg:py-24 bg-delos-ivory">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div data-motion-group="dealer-benefits-header" class="text-center mb-14 lg:mb-16">
            <div data-motion-line class="w-12 h-px bg-delos-gold mx-auto mb-5"></div>
            <h2 data-motion="fade-up" class="font-serif text-3xl lg:text-4xl font-light text-delos-dark">{{ pcontent('become_dealer.benefits.heading') }}</h2>
        </div>

        <div data-motion-group="dealer-benefits" class="grid md:grid-cols-3 gap-10 lg:gap-12">
            @foreach([0, 1, 2] as $i)
                <div data-motion="fade-up" class="text-center md:text-left">
                    <p class="font-serif text-delos-gold text-2xl lg:text-3xl font-light mb-3">0{{ $i + 1 }}</p>
                    <h3 class="font-serif text-delos-dark text-xl lg:text-2xl font-light mb-3 leading-snug">{{ pcontent("become_dealer.benefits.items.{$i}.title") }}</h3>
                    <p class="text-delos-muted text-sm lg:text-base leading-relaxed" style="font-family: 'Inter', sans-serif;">{{ pcontent("become_dealer.benefits.items.{$i}.desc") }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- FORM --}}
<section class="py-24 lg:py-32 bg-delos-cream">
    <div class="max-w-2xl mx-auto px-6 lg:px-12">

        <div data-motion-group="dealer-form-header" class="text-center mb-12 lg:mb-14">
            <div data-motion-line class="w-12 h-px bg-delos-gold mx-auto mb-5"></div>
            <h2 data-motion="fade-up" class="font-serif text-3xl lg:text-4xl font-light text-delos-dark leading-tight mb-4">{{ pcontent('become_dealer.form.heading') }}</h2>
            <p data-motion="fade-up" class="text-delos-muted text-sm lg:text-base" style="font-family: 'Inter', sans-serif;">{{ pcontent('become_dealer.form.sub') }}</p>
        </div>

        {{-- Dealer inquiry form — no server round-trip. Same convention as
             the Contact page: Alpine.js builds a prefilled WhatsApp message
             or mailto: URL and opens the visitor's own app on submit. --}}
        <form data-motion="fade-up" x-data="dealerForm()" @submit.prevent
              class="space-y-5 font-sans" novalidate>
            @csrf

            <div>
                <label for="dealer-business" class="block text-[11px] tracking-[0.2em] uppercase text-delos-muted mb-2">{{ pcontent('become_dealer.form.fields.business') }}</label>
                <input type="text" id="dealer-business" x-model="business" required
                       class="w-full px-4 py-3 bg-transparent border border-delos-dark/20 text-delos-dark text-sm focus:border-delos-gold focus:outline-none transition-colors duration-300 placeholder-delos-muted/50"
                       placeholder="{{ pcontent('become_dealer.form.fields.business_placeholder') }}">
            </div>

            <div>
                <label for="dealer-name" class="block text-[11px] tracking-[0.2em] uppercase text-delos-muted mb-2">{{ pcontent('become_dealer.form.fields.name') }}</label>
                <input type="text" id="dealer-name" x-model="name" required
                       class="w-full px-4 py-3 bg-transparent border border-delos-dark/20 text-delos-dark text-sm focus:border-delos-gold focus:outline-none transition-colors duration-300 placeholder-delos-muted/50"
                       placeholder="{{ pcontent('become_dealer.form.fields.name_placeholder') }}">
            </div>

            <div class="grid sm:grid-cols-2 gap-5">
                <div>
                    <label for="dealer-city" class="block text-[11px] tracking-[0.2em] uppercase text-delos-muted mb-2">{{ pcontent('become_dealer.form.fields.city') }}</label>
                    <input type="text" id="dealer-city" x-model="city" required
                           class="w-full px-4 py-3 bg-transparent border border-delos-dark/20 text-delos-dark text-sm focus:border-delos-gold focus:outline-none transition-colors duration-300 placeholder-delos-muted/50"
                           placeholder="{{ pcontent('become_dealer.form.fields.city_placeholder') }}">
                </div>

                <div>
                    <label for="dealer-phone" class="block text-[11px] tracking-[0.2em] uppercase text-delos-muted mb-2">{{ pcontent('become_dealer.form.fields.phone') }}</label>
                    <input type="tel" id="dealer-phone" x-model="phone" required dir="ltr"
                           class="w-full px-4 py-3 bg-transparent border border-delos-dark/20 text-delos-dark text-sm focus:border-delos-gold focus:outline-none transition-colors duration-300 placeholder-delos-muted/50"
                           placeholder="{{ pcontent('become_dealer.form.fields.phone_placeholder') }}">
                </div>
            </div>

            <div>
                <label for="dealer-message" class="block text-[11px] tracking-[0.2em] uppercase text-delos-muted mb-2">{{ pcontent('become_dealer.form.fields.message') }}</label>
                <textarea id="dealer-message" x-model="message" rows="4" maxlength="1500"
                          class="w-full px-4 py-3 bg-transparent border border-delos-dark/20 text-delos-dark text-sm focus:border-delos-gold focus:outline-none transition-colors duration-300 resize-none placeholder-delos-muted/50"
                          placeholder="{{ pcontent('become_dealer.form.fields.message_placeholder') }}"></textarea>
            </div>

            {{-- Two submit buttons — WhatsApp (primary, gold) and Email (secondary, bordered).
                 Both are disabled until required fields are filled. --}}
            <div class="grid sm:grid-cols-2 gap-3 pt-2">
                <button type="button" @click="send('whatsapp')" :disabled="!canSubmit"
                        class="btn-ripple py-4 bg-delos-dark text-delos-cream text-[12px] tracking-[0.3em] uppercase font-medium hover:bg-delos-gold hover:text-delos-dark disabled:opacity-40 disabled:cursor-not-allowed transition-all duration-300">
                    {{ pcontent('become_dealer.form.submit_whatsapp') }}
                </button>
                <button type="button" @click="send('email')" :disabled="!canSubmit"
                        class="btn-ripple py-4 border border-delos-dark text-delos-dark text-[12px] tracking-[0.3em] uppercase font-medium hover:border-delos-gold hover:text-delos-gold disabled:opacity-40 disabled:cursor-not-allowed transition-all duration-300">
                    {{ pcontent('become_dealer.form.submit_email') }}
                </button>
            </div>

            {{-- Always-visible fallback — same defensive pattern as Contact: if
                 the visitor's mail/WhatsApp app fails to open silently, give
                 them the destination so they can copy it manually. --}}
            <p class="text-[11px] text-delos-muted text-center leading-relaxed pt-4">
                <span>{{ pcontent('become_dealer.form.fallback_hint') }}</span>
                <a href="mailto:{{ pcontent('become_dealer.contact.email') }}"
                   class="text-delos-dark hover:text-delos-gold underline underline-offset-2 break-words transition-colors duration-300">{{ pcontent('become_dealer.contact.email') }}</a>
            </p>
        </form>

        <script>
            (function () {
                const dest = {
                    whatsapp: @json(pcontent('become_dealer.contact.whatsapp')),
                    email:    @json(pcontent('become_dealer.contact.email')),
                };
                const labels = {
                    business: @json(pcontent('become_dealer.form.fields.business')),
                    name:     @json(pcontent('become_dealer.form.fields.name')),
                    city:     @json(pcontent('become_dealer.form.fields.city')),
                    phone:    @json(pcontent('become_dealer.form.fields.phone')),
                    message:  @json(pcontent('become_dealer.form.fields.message')),
                };

                function initDealerForm() {
                    if (typeof Alpine === 'undefined' || Alpine.$data === undefined) {
                        return document.addEventListener('alpine:init', registerComponent);
                    }
                    registerComponent();
                }
                function registerComponent() {
                    Alpine.data('dealerForm', () => ({
                        business: '', name: '', city: '', phone: '', message: '',

                        get canSubmit() {
                            return this.business.trim()
                                && this.name.trim()
                                && this.city.trim()
                                && this.phone.trim();
                        },

                        send(method) {
                            if (!this.canSubmit) return;
                            const msg = this.message.trim() || '—';

                            if (method === 'whatsapp') {
                                const body = [
                                    '*Delos International — Dealer Inquiry*',
                                    '',
                                    '*' + labels.business + ':* ' + this.business.trim(),
                                    '*' + labels.name + ':* ' + this.name.trim(),
                                    '*' + labels.city + ':* ' + this.city.trim(),
                                    '*' + labels.phone + ':* ' + this.phone.trim(),
                                    '',
                                    '*' + labels.message + ':*',
                                    msg,
                                    '',
                                    '— Sent from delos-international.net',
                                ].join('\n');
                                const url = 'https://wa.me/' + dest.whatsapp + '?text=' + encodeURIComponent(body);
                                window.open(url, '_blank', 'noopener');
                                return;
                            }

                            const subject = 'Dealer Inquiry — ' + this.business.trim();
                            const body = [
                                labels.business + ': ' + this.business.trim(),
                                labels.name + ': ' + this.name.trim(),
                                labels.city + ': ' + this.city.trim(),
                                labels.phone + ': ' + this.phone.trim(),
                                '',
                                labels.message + ':',
                                msg,
                                '',
                                '— Sent from delos-international.net',
                            ].join('\n');
                            const url = 'mailto:' + dest.email
                                + '?subject=' + encodeURIComponent(subject)
                                + '&body=' + encodeURIComponent(body);
                            window.location.href = url;
                        },
                    }));
                }
                initDealerForm();
            })();
        </script>

    </div>
</section>

@endsection
