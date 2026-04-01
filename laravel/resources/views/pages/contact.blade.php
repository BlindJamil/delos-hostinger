@extends('layouts.app')

@section('title', 'Contact Us — Delos International')
@section('description', 'Book a complimentary design consultation with Delos International. Our specialists will help you create your perfect Italian luxury interior.')

@section('content')

{{-- Hero --}}
<section class="relative min-h-[50vh] flex items-end overflow-hidden bg-delos-dark">
    <div class="absolute inset-0">
        <img src="{{ asset('images/delos-wall-branding.jpg') }}" alt="Delos International Wall"
             class="w-full h-full object-cover opacity-30">
        <div class="absolute inset-0 bg-gradient-to-t from-delos-dark to-transparent"></div>
    </div>
    <div class="relative z-10 max-w-[1400px] mx-auto px-6 lg:px-12 pt-40 pb-20 w-full">
        <div class="gsap-el inline-flex items-center gap-3 mb-6">
            <span class="w-8 h-px bg-delos-gold"></span>
            <span class="text-overline text-delos-gold">Get in Touch</span>
        </div>
        <h1 class="gsap-el font-serif text-delos-cream text-5xl lg:text-7xl font-light leading-tight">
            Contact us.<br>
            <em class="text-delos-gold not-italic">We're here for you.</em>
        </h1>
    </div>
</section>

{{-- Contact Form --}}
<section class="section-padding bg-delos-cream gsap-section">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">

        <div class="grid lg:grid-cols-2 gap-16 lg:gap-24 items-start">
            <div>
                <div class="gsap-line w-0 h-px bg-delos-gold mb-5"></div>
                <p class="gsap-el text-overline text-delos-gold mb-4">Book a Consultation</p>
                <h2 class="gsap-el text-heading-2 text-delos-dark leading-tight mb-6">
                    Start your<br>
                    <em class="text-delos-gold not-italic">Italian luxury</em><br>
                    journey.
                </h2>
                <p class="gsap-el text-body text-delos-muted">
                    Our specialist will contact you within 24 hours to arrange a complimentary in-home consultation and 3D design concept.
                </p>
            </div>

            <form class="gsap-el space-y-5 font-sans" method="POST" action="#">
                @csrf
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-[11px] tracking-[0.2em] uppercase text-delos-muted mb-2">First Name</label>
                        <input type="text" name="first_name" required class="w-full px-4 py-3 bg-transparent border border-delos-dark/20 text-delos-dark text-sm focus:border-delos-gold focus:outline-none transition-colors duration-300 placeholder-delos-muted/50" placeholder="Ahmad">
                    </div>
                    <div>
                        <label class="block text-[11px] tracking-[0.2em] uppercase text-delos-muted mb-2">Last Name</label>
                        <input type="text" name="last_name" required class="w-full px-4 py-3 bg-transparent border border-delos-dark/20 text-delos-dark text-sm focus:border-delos-gold focus:outline-none transition-colors duration-300 placeholder-delos-muted/50" placeholder="Al-Rashid">
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] tracking-[0.2em] uppercase text-delos-muted mb-2">Email</label>
                    <input type="email" name="email" class="w-full px-4 py-3 bg-transparent border border-delos-dark/20 text-delos-dark text-sm focus:border-delos-gold focus:outline-none transition-colors duration-300 placeholder-delos-muted/50" placeholder="your@email.com">
                </div>
                <div>
                    <label class="block text-[11px] tracking-[0.2em] uppercase text-delos-muted mb-2">Phone Number</label>
                    <input type="tel" name="phone" required class="w-full px-4 py-3 bg-transparent border border-delos-dark/20 text-delos-dark text-sm focus:border-delos-gold focus:outline-none transition-colors duration-300 placeholder-delos-muted/50" placeholder="07XX XXX XXXX">
                </div>
                <div>
                    <label class="block text-[11px] tracking-[0.2em] uppercase text-delos-muted mb-2">Branch</label>
                    <div style="position: relative;">
                        <select name="showroom" required class="w-full px-4 py-3 bg-transparent border border-delos-dark/20 text-delos-dark text-sm focus:border-delos-gold focus:outline-none transition-colors duration-300 cursor-pointer" style="-webkit-appearance: none; -moz-appearance: none; appearance: none; border-radius: 0; padding-right: 2.5rem;">
                            <option value="">Select a city</option>
                            <option>Erbil</option>
                            <option>Kirkuk</option>
                            <option>Sulaymaniyah</option>
                            <option>Baghdad</option>
                        </select>
                        <svg style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); pointer-events: none;" class="w-4 h-4 text-delos-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] tracking-[0.2em] uppercase text-delos-muted mb-2">Service Interest</label>
                    <div style="position: relative;">
                        <select name="service" class="w-full px-4 py-3 bg-transparent border border-delos-dark/20 text-delos-dark text-sm focus:border-delos-gold focus:outline-none transition-colors duration-300 cursor-pointer" style="-webkit-appearance: none; -moz-appearance: none; appearance: none; border-radius: 0; padding-right: 2.5rem;">
                            <option value="">Select a service</option>
                            <option>Luxury Kitchen</option>
                            <option>Living Room</option>
                            <option>Bedroom</option>
                            <option>Wardrobes</option>
                            <option>Flooring & Walls</option>
                            <option>Complete Turnkey Project</option>
                        </select>
                        <svg style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); pointer-events: none;" class="w-4 h-4 text-delos-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 9l-7 7-7-7"/></svg>
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] tracking-[0.2em] uppercase text-delos-muted mb-2">Message (Optional)</label>
                    <textarea name="message" rows="4" class="w-full px-4 py-3 bg-transparent border border-delos-dark/20 text-delos-dark text-sm focus:border-delos-gold focus:outline-none transition-colors duration-300 resize-none placeholder-delos-muted/50" placeholder="Tell us about your project..."></textarea>
                </div>
                <button type="submit" class="btn-ripple w-full py-4 bg-delos-dark text-delos-cream text-[12px] tracking-[0.3em] uppercase font-medium hover:bg-delos-gold hover:text-delos-dark transition-all duration-300">
                    Request Consultation
                </button>
            </form>
        </div>

    </div>
</section>

@endsection
