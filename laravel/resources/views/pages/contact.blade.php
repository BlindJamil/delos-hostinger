@extends('layouts.app')

@section('title', __('seo.contact.title'))
@section('description', __('seo.contact.description'))

@section('content')

{{-- Hero Image --}}
<section class="relative h-[55vh] lg:h-[60vh] overflow-hidden">
    <x-responsive-image src="delos-wall-branding.jpg" alt="Delos International Wall"
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
            <span class="text-overline text-delos-gold">{{ __('contact.hero.overline') }}</span>
            <span class="w-8 h-px bg-delos-gold"></span>
        </div>
        <h1 data-motion="fade-up" class="font-serif text-delos-dark text-5xl lg:text-7xl font-light leading-tight">
            {{ __('contact.hero.heading_1') }}<br>
            <em class="text-delos-gold not-italic">{{ __('contact.hero.heading_accent') }}</em>
        </h1>
    </div>
</section>

{{-- Contact Form --}}
<section class="section-padding bg-delos-cream">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">

        <div class="grid lg:grid-cols-2 gap-16 lg:gap-24 items-start">
            <div data-motion-group="contact-copy">
                <div data-motion-line class="w-16 h-px bg-delos-gold mb-5"></div>
                <p data-motion="fade-up" class="text-overline text-delos-gold mb-4">{{ __('contact.intro.overline') }}</p>
                <h2 data-motion="fade-up" class="text-heading-2 text-delos-dark leading-tight mb-6">
                    {{ __('contact.intro.heading_1') }}<br>
                    <em class="text-delos-gold not-italic">{{ __('contact.intro.heading_accent') }}</em>
                    @if(__('contact.intro.heading_2'))
                        <br>{{ __('contact.intro.heading_2') }}
                    @endif
                </h2>
                <p data-motion="fade-up" class="text-body text-delos-muted">
                    {{ __('contact.intro.body') }}
                </p>
            </div>

            <form data-motion="fade-up" class="space-y-5 font-sans" method="POST" action="#">
                @csrf
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[11px] tracking-[0.2em] uppercase text-delos-muted mb-2">{{ __('contact.form.first_name') }}</label>
                        <input type="text" name="first_name" required class="w-full px-4 py-3 bg-transparent border border-delos-dark/20 text-delos-dark text-sm focus:border-delos-gold focus:outline-none transition-colors duration-300 placeholder-delos-muted/50" placeholder="{{ __('contact.form.first_name_placeholder') }}">
                    </div>
                    <div>
                        <label class="block text-[11px] tracking-[0.2em] uppercase text-delos-muted mb-2">{{ __('contact.form.last_name') }}</label>
                        <input type="text" name="last_name" required class="w-full px-4 py-3 bg-transparent border border-delos-dark/20 text-delos-dark text-sm focus:border-delos-gold focus:outline-none transition-colors duration-300 placeholder-delos-muted/50" placeholder="{{ __('contact.form.last_name_placeholder') }}">
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] tracking-[0.2em] uppercase text-delos-muted mb-2">{{ __('contact.form.email') }}</label>
                    <input type="email" name="email" class="w-full px-4 py-3 bg-transparent border border-delos-dark/20 text-delos-dark text-sm focus:border-delos-gold focus:outline-none transition-colors duration-300 placeholder-delos-muted/50" placeholder="{{ __('contact.form.email_placeholder') }}">
                </div>
                <div>
                    <label class="block text-[11px] tracking-[0.2em] uppercase text-delos-muted mb-2">{{ __('contact.form.phone') }}</label>
                    <input type="tel" name="phone" required class="w-full px-4 py-3 bg-transparent border border-delos-dark/20 text-delos-dark text-sm focus:border-delos-gold focus:outline-none transition-colors duration-300 placeholder-delos-muted/50" placeholder="{{ __('contact.form.phone_placeholder') }}">
                </div>
                <div>
                    <label class="block text-[11px] tracking-[0.2em] uppercase text-delos-muted mb-2">{{ __('contact.form.branch') }}</label>
                    <div style="position: relative;">
                        <select name="showroom" required class="w-full px-4 py-3 bg-transparent border border-delos-dark/20 text-delos-dark text-sm focus:border-delos-gold focus:outline-none transition-colors duration-300 cursor-pointer" style="-webkit-appearance: none; -moz-appearance: none; appearance: none; border-radius: 0; padding-right: 2.5rem;">
                            <option value="">{{ __('contact.form.branch_placeholder') }}</option>
                            @foreach(__('contact.form.cities') as $city)
                                <option>{{ $city }}</option>
                            @endforeach
                        </select>
                        <svg style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); pointer-events: none;" class="w-4 h-4 text-delos-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] tracking-[0.2em] uppercase text-delos-muted mb-2">{{ __('contact.form.service') }}</label>
                    <div style="position: relative;">
                        <select name="service" class="w-full px-4 py-3 bg-transparent border border-delos-dark/20 text-delos-dark text-sm focus:border-delos-gold focus:outline-none transition-colors duration-300 cursor-pointer" style="-webkit-appearance: none; -moz-appearance: none; appearance: none; border-radius: 0; padding-right: 2.5rem;">
                            <option value="">{{ __('contact.form.service_placeholder') }}</option>
                            @foreach(__('contact.form.service_options') as $opt)
                                <option>{{ $opt }}</option>
                            @endforeach
                        </select>
                        <svg style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); pointer-events: none;" class="w-4 h-4 text-delos-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] tracking-[0.2em] uppercase text-delos-muted mb-2">{{ __('contact.form.message') }}</label>
                    <textarea name="message" rows="4" class="w-full px-4 py-3 bg-transparent border border-delos-dark/20 text-delos-dark text-sm focus:border-delos-gold focus:outline-none transition-colors duration-300 resize-none placeholder-delos-muted/50" placeholder="{{ __('contact.form.message_placeholder') }}"></textarea>
                </div>
                <button type="submit" class="btn-ripple w-full py-4 bg-delos-dark text-delos-cream text-[12px] tracking-[0.3em] uppercase font-medium hover:bg-delos-gold hover:text-delos-dark transition-all duration-300">
                    {{ __('contact.form.submit') }}
                </button>
            </form>
        </div>

    </div>
</section>

@endsection
