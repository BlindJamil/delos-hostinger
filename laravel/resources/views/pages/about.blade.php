@extends('layouts.app')

@section('title', pcontent('seo.about.title'))
@section('description', pcontent('seo.about.description'))

@section('content')

{{-- ABOUT HERO --}}
<section data-motion-hero class="bg-delos-cream overflow-hidden">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="pt-28 lg:pt-36 pb-0">
            <div class="grid md:grid-cols-12 gap-5 md:gap-6 lg:gap-7 md:items-stretch">

                <div class="md:col-span-5 flex flex-col order-2 md:order-none md:h-full">
                    <div data-motion-group="hero" class="pt-10 lg:pt-6 lg:pr-4">
                        <div data-motion="fade-up" class="inline-flex items-center gap-3 mb-6">
                            <span class="w-8 h-px bg-delos-gold"></span>
                            <span class="text-delos-gold text-[11px] tracking-[0.4em] uppercase font-medium" style="font-family: 'Inter', sans-serif;">{{ pcontent('about.hero.overline') }}</span>
                        </div>

                        <h1 data-motion="fade-up" class="about-hero-h1 font-serif text-delos-dark font-light leading-[1.08] mb-5">
                            {{ pcontent('about.hero.heading_1') }}<br>
                            {{ pcontent('about.hero.heading_2') }}<br>
                            <em class="text-delos-gold not-italic">{{ pcontent('about.hero.heading_accent') }}</em>
                        </h1>

                        <p data-motion="fade-up" class="text-delos-muted text-base lg:text-lg leading-relaxed max-w-md mb-8 lg:mb-10" style="font-family: 'Inter', sans-serif;">
                            {{ pcontent('about.hero.sub') }}
                        </p>
                    </div>
                </div>

                <div class="md:col-span-7 order-1 md:order-none">
                    <div data-motion="fade" class="about-hero-img relative overflow-hidden aspect-[4/5] md:aspect-auto md:h-full">
                        <x-admin-edit-pill page="about" label="Edit About hero image" />
                        <x-responsive-image :src="pcontent('about.hero.image', 'about-hero.jpg')"
                            :mobile-src="pcontent_optional('about.hero.image_mobile')"
                            :focal="pcontent_optional('about.hero.image_focal')"
                            alt="Delos International Showroom"
                            sizes="(min-width: 768px) 58vw, 100vw"
                            loading="eager"
                            fetchpriority="high"
                            data-hero-parallax
                            class="w-full h-full object-cover hero-parallax-img" />
                    </div>
                </div>

            </div>
        </div>

        {{-- Stats row --}}
        <div data-motion="fade-up" class="border-t border-delos-gold/15 mt-12 lg:mt-16 py-10 lg:py-12">
            <div class="grid grid-cols-3 gap-4">
                @foreach([0, 1, 2] as $i)
                    <div class="text-center{{ $i === 1 ? ' border-x border-delos-gold/15' : '' }}">
                        <span class="block font-serif text-delos-dark text-3xl lg:text-4xl font-light">{{ pcontent("about.hero.stats.{$i}.value") }}</span>
                        <span class="text-delos-muted text-[9px] lg:text-[10px] tracking-[0.3em] uppercase mt-1.5 block" style="font-family: 'Inter', sans-serif;">{{ pcontent("about.hero.stats.{$i}.label") }}</span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</section>

{{-- OUR STORY --}}
<section class="py-24 lg:py-32 bg-delos-cream">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="grid lg:grid-cols-2 gap-16 lg:gap-24 items-center">
            <div data-motion="slide-left" class="relative aspect-[3/4] overflow-hidden bg-delos-dark">
                <x-admin-edit-pill page="about" label="Edit Our Story image" />
                <x-responsive-image :src="pcontent('about.story.image', 'about-story.jpg')"
                    :mobile-src="pcontent_optional('about.story.image_mobile')"
                    :focal="pcontent_optional('about.story.image_focal')"
                    alt="Delos International Showroom"
                    sizes="(min-width: 1024px) 50vw, 100vw"
                    class="w-full h-full object-cover opacity-80" />
            </div>

            <div data-motion-group="story">
                <div data-motion-line class="w-16 h-px bg-delos-gold mb-5"></div>
                <p data-motion="fade-up" class="text-delos-gold text-[11px] tracking-[0.4em] uppercase font-medium mb-6" style="font-family: 'Inter', sans-serif;">{{ pcontent('about.story.overline') }}</p>
                <h2 data-motion="fade-up" class="font-serif text-4xl lg:text-5xl font-light text-delos-dark leading-tight mb-8">
                    {{ pcontent('about.story.heading_1') }}<br>
                    {{ pcontent('about.story.heading_2') }}<br>
                    <em class="text-delos-gold not-italic">{{ pcontent('about.story.heading_accent') }}</em>
                </h2>
                <div class="space-y-5">
                    <p data-motion="fade-up" class="text-delos-muted text-base leading-relaxed" style="font-family: 'Inter', sans-serif;">{{ pcontent('about.story.paragraph_1') }}</p>
                    <p data-motion="fade-up" class="text-delos-muted text-base leading-relaxed" style="font-family: 'Inter', sans-serif;">{{ pcontent('about.story.paragraph_2') }}</p>
                    <p data-motion="fade-up" class="text-delos-muted text-base leading-relaxed" style="font-family: 'Inter', sans-serif;">{{ pcontent('about.story.paragraph_3') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- VISION, MISSION & GOAL --}}
<section class="py-24 lg:py-32 bg-delos-ivory">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div data-motion-group="direction-header" class="text-center mb-16">
            <p data-motion="fade-up" class="text-delos-gold text-[11px] tracking-[0.4em] uppercase font-medium mb-4" style="font-family: 'Inter', sans-serif;">{{ pcontent('about.direction.overline') }}</p>
            <h2 data-motion="fade-up" class="font-serif text-4xl lg:text-5xl font-light text-delos-dark max-w-2xl mx-auto leading-tight">
                {{ pcontent('about.direction.heading_1') }}<br>{{ pcontent('about.direction.heading_2') }}
            </h2>
        </div>

        <div data-motion-group="direction-cards" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            @foreach(['vision', 'mission', 'goal'] as $slug)
                <div data-motion="fade-up" class="group">
                    <div class="p-8 lg:p-10 border border-delos-dark/10 h-full hover:border-delos-gold/30 hover:bg-delos-cream transition-colors duration-400">
                        <span class="inline-block text-delos-gold text-[10px] tracking-[0.4em] uppercase font-medium mb-6 border border-delos-gold/30 px-3 py-1" style="font-family: 'Inter', sans-serif;">{{ pcontent("about.direction.items.{$slug}.label") }}</span>
                        <h3 class="font-serif text-delos-dark text-2xl font-medium mb-4 group-hover:text-delos-gold transition-colors duration-300">
                            {{ pcontent("about.direction.items.{$slug}.title") }}
                        </h3>
                        <p class="text-delos-muted text-sm leading-relaxed" style="font-family: 'Inter', sans-serif;">
                            {{ pcontent("about.direction.items.{$slug}.body") }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- PHILOSOPHY --}}
<section class="py-24 lg:py-32 bg-delos-cream">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="grid lg:grid-cols-2 gap-16 lg:gap-24 items-center">
            <div data-motion-group="philosophy">
                <div data-motion-line class="w-16 h-px bg-delos-gold mb-5"></div>
                <p data-motion="fade-up" class="text-delos-gold text-[11px] tracking-[0.4em] uppercase font-medium mb-6" style="font-family: 'Inter', sans-serif;">{{ pcontent('about.philosophy.overline') }}</p>
                <h2 data-motion="fade-up" class="font-serif text-4xl lg:text-5xl font-light text-delos-dark leading-tight mb-8">
                    {{ pcontent('about.philosophy.heading_1') }}<br>
                    {{ pcontent('about.philosophy.heading_2') }}<br>
                    <em class="text-delos-gold not-italic">{{ pcontent('about.philosophy.heading_accent') }}</em>
                </h2>
                <div class="space-y-5">
                    <p data-motion="fade-up" class="text-delos-muted text-base leading-relaxed" style="font-family: 'Inter', sans-serif;">{{ pcontent('about.philosophy.paragraph_1') }}</p>
                    <p data-motion="fade-up" class="text-delos-muted text-base leading-relaxed" style="font-family: 'Inter', sans-serif;">{{ pcontent('about.philosophy.paragraph_2') }}</p>
                </div>
            </div>

            <div data-motion="slide-right" class="relative aspect-[4/3] overflow-hidden bg-delos-dark">
                <x-admin-edit-pill page="about" label="Edit Philosophy image" />
                <x-responsive-image :src="pcontent('about.philosophy.image', 'about-philosophy.jpg')"
                    :mobile-src="pcontent_optional('about.philosophy.image_mobile')"
                    :focal="pcontent_optional('about.philosophy.image_focal')"
                    alt="Italian Design Philosophy"
                    sizes="(min-width: 1024px) 50vw, 100vw"
                    class="w-full h-full object-cover" />
            </div>
        </div>
    </div>
</section>

{{-- ITALIAN MATERIALS --}}
<section class="relative py-24 lg:py-40 overflow-hidden bg-delos-dark">
    <x-admin-edit-pill page="about" label="Edit Italian Materials section & image" />
    <div class="absolute inset-0">
        <x-responsive-image :src="pcontent('about.materials.image', 'italian-materials.jpg')"
            :mobile-src="pcontent_optional('about.materials.image_mobile')"
            :focal="pcontent_optional('about.materials.image_focal')"
            alt="Italian Materials"
            sizes="100vw"
            class="w-full h-full object-cover opacity-25" />
        <div class="absolute inset-0 bg-gradient-to-r from-delos-dark via-delos-dark/70 to-transparent"></div>
    </div>
    <div class="relative z-10 max-w-[1400px] mx-auto px-6 lg:px-12">
        <div data-motion-group="italian-materials" class="max-w-2xl">
            <div data-motion-line class="w-16 h-px bg-delos-gold mb-5"></div>
            <p data-motion="fade-up" class="text-delos-gold text-[11px] tracking-[0.4em] uppercase font-medium mb-6" style="font-family: 'Inter', sans-serif;">{{ pcontent('about.materials.overline') }}</p>
            <h2 data-motion="fade-up" class="font-serif text-delos-cream text-4xl lg:text-5xl xl:text-6xl font-light leading-tight mb-8">
                {{ pcontent('about.materials.heading_1') }}<br>
                {{ pcontent('about.materials.heading_2') }} <em class="text-delos-gold not-italic">{{ pcontent('about.materials.heading_accent_1') }}<br>
                {{ pcontent('about.materials.heading_accent_2') }}</em> {{ pcontent('about.materials.heading_3') }}<br>
                {{ pcontent('about.materials.heading_4') }}
            </h2>
            <p data-motion="fade-up" class="text-delos-cream/60 text-base leading-relaxed max-w-md" style="font-family: 'Inter', sans-serif;">
                {{ pcontent('about.materials.body') }}
            </p>
        </div>
    </div>
</section>

{{-- 4 PILLARS --}}
<section class="py-24 lg:py-32 bg-delos-cream">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div data-motion-group="pillars-header" class="text-center mb-16">
            <p data-motion="fade-up" class="text-delos-gold text-[11px] tracking-[0.4em] uppercase font-medium mb-4" style="font-family: 'Inter', sans-serif;">{{ pcontent('about.pillars.overline') }}</p>
            <h2 data-motion="fade-up" class="font-serif text-4xl lg:text-5xl font-light text-delos-dark">{{ pcontent('about.pillars.heading') }}</h2>
        </div>

        <div data-motion-group="pillars" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach(['authentic', 'turnkey', 'premium', 'trusted'] as $slug)
                <div data-motion="fade-up" class="group text-center p-8 border border-delos-dark/10 hover:border-delos-gold/40 hover:bg-delos-ivory transition-colors duration-400">
                    <div class="w-12 h-12 border border-delos-gold/30 flex items-center justify-center mx-auto mb-6 group-hover:bg-delos-gold group-hover:border-delos-gold transition-all duration-300">
                        <span class="text-delos-gold text-[11px] tracking-wide font-medium group-hover:text-delos-dark transition-colors duration-300" style="font-family: 'Inter', sans-serif;">{{ pcontent("about.pillars.items.{$slug}.num") }}</span>
                    </div>
                    <h3 class="font-serif text-delos-dark text-xl font-medium mb-4 group-hover:text-delos-gold transition-colors duration-300">
                        {{ pcontent("about.pillars.items.{$slug}.title") }}
                    </h3>
                    <p class="text-delos-muted text-sm leading-relaxed" style="font-family: 'Inter', sans-serif;">
                        {{ pcontent("about.pillars.items.{$slug}.desc") }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- LARGE QUOTE STATEMENT --}}
<section class="py-24 lg:py-40 bg-delos-dark text-center">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div data-motion-group="quote">
            <p data-motion="fade-up" class="text-delos-gold/50 text-[11px] tracking-[0.5em] uppercase font-medium mb-10" style="font-family: 'Inter', sans-serif;">{{ pcontent('about.quote.overline') }}</p>
            <blockquote data-motion="fade-up" class="font-serif text-delos-cream text-3xl lg:text-5xl xl:text-6xl font-light leading-tight max-w-5xl mx-auto">
                {{ pcontent('about.quote.text_before_accent') }} <em class="text-delos-gold not-italic">{{ pcontent('about.quote.text_accent') }}</em> {{ pcontent('about.quote.text_after_accent') }}
            </blockquote>
            <div data-motion="fade-up" class="mt-16 flex items-center justify-center gap-6">
                <span class="w-16 h-px bg-delos-gold/30"></span>
                <p class="font-serif text-delos-gold text-2xl italic font-light">{{ pcontent('about.quote.signature') }}</p>
                <span class="w-16 h-px bg-delos-gold/30"></span>
            </div>
        </div>
    </div>
</section>

{{-- STATS --}}
<section class="py-20 lg:py-24 bg-delos-ivory">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div data-motion-group="about-stats" class="grid grid-cols-2 lg:grid-cols-4 gap-8 text-center">
            @foreach([0, 1, 2, 3] as $i)
                <div data-motion="fade-up">
                    <div class="flex items-baseline justify-center gap-1 mb-3">
                        <span class="font-serif text-delos-dark text-5xl lg:text-6xl font-light"
                              data-motion-counter="{{ pcontent("about.about_stats.items.{$i}.value") }}">{{ pcontent("about.about_stats.items.{$i}.value") }}</span>
                        @if(pcontent("about.about_stats.items.{$i}.suffix"))
                            <span class="stat-suffix font-serif text-delos-gold text-3xl font-light">{{ pcontent("about.about_stats.items.{$i}.suffix") }}</span>
                        @endif
                    </div>
                    <p class="text-delos-muted text-[11px] tracking-[0.3em] uppercase" style="font-family: 'Inter', sans-serif;">{{ pcontent("about.about_stats.items.{$i}.label") }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- OUR WORKFLOW --}}
<section class="py-24 lg:py-32 bg-delos-cream">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div data-motion-group="workflow-header" class="text-center mb-16">
            <p data-motion="fade-up" class="text-delos-gold text-[11px] tracking-[0.4em] uppercase font-medium mb-4" style="font-family: 'Inter', sans-serif;">{{ pcontent('about.workflow.overline') }}</p>
            <h2 data-motion="fade-up" class="font-serif text-4xl lg:text-5xl font-light text-delos-dark">
                {{ pcontent('about.workflow.heading_1') }}<br>{{ pcontent('about.workflow.heading_2') }}
            </h2>
        </div>

        <div data-motion-group="workflow-steps" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach(['consultation', 'design', 'sourcing', 'installation'] as $slug)
                @php $idx = $loop->index; @endphp
                <div data-motion="fade-up" class="text-center group">
                    <div class="relative mb-8">
                        <div class="w-16 h-16 border border-delos-gold/30 rounded-full flex items-center justify-center mx-auto group-hover:bg-delos-gold group-hover:border-delos-gold transition-all duration-400">
                            <span class="font-serif text-delos-gold text-xl font-light group-hover:text-delos-dark transition-colors duration-400">{{ pcontent("about.workflow.steps.{$slug}.step") }}</span>
                        </div>
                        @if($idx < 3)
                            <div class="hidden lg:block absolute top-8 left-1/2 w-full h-px bg-delos-dark/10" style="left: calc(50% + 32px); width: calc(100% - 64px);"></div>
                        @endif
                    </div>
                    <h3 class="font-serif text-delos-dark text-xl font-medium mb-3 group-hover:text-delos-gold transition-colors duration-300">
                        {{ pcontent("about.workflow.steps.{$slug}.title") }}
                    </h3>
                    <p class="text-delos-muted text-sm leading-relaxed" style="font-family: 'Inter', sans-serif;">
                        {{ pcontent("about.workflow.steps.{$slug}.desc") }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- OUR BRANCHES --}}
<section class="py-24 lg:py-32 bg-delos-dark">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div data-motion-group="branches-header" class="flex flex-col lg:flex-row lg:items-end lg:justify-between mb-16 gap-6">
            <div>
                <div data-motion-line class="w-16 h-px bg-delos-gold mb-5"></div>
                <p data-motion="fade-up" class="text-delos-gold text-[11px] tracking-[0.4em] uppercase font-medium mb-4" style="font-family: 'Inter', sans-serif;">{{ pcontent('about.branches.overline') }}</p>
                <h2 data-motion="fade-up" class="font-serif text-delos-cream text-4xl lg:text-5xl font-light leading-tight">
                    {{ pcontent('about.branches.heading_1') }}<br>{{ pcontent('about.branches.heading_2') }}<br>{{ pcontent('about.branches.heading_3') }} <em class="text-delos-gold not-italic">{{ pcontent('about.branches.heading_accent') }}</em>
                </h2>
            </div>
            <a href="{{ lroute('contact') }}"
               data-motion="fade-up"
               class="self-start inline-flex items-center gap-2 text-delos-gold text-[12px] tracking-[0.2em] uppercase font-medium hover:text-delos-cream transition-colors duration-300 group" style="font-family: 'Inter', sans-serif;">
                {{ pcontent('common.ctas.find_nearest_showroom') }}
                <svg class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>

        <div data-motion-group="branches-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach(['erbil', 'kirkuk', 'sulaymaniyah', 'baghdad'] as $slug)
                <div data-motion="fade-up" class="group flex flex-col h-full border border-white/10 p-6 lg:p-8 hover:border-delos-gold/40 transition-colors duration-400">
                    <p class="text-delos-gold text-[10px] tracking-[0.4em] uppercase font-medium mb-4" style="font-family: 'Inter', sans-serif;">{{ pcontent('about.story.year_label') }} {{ pcontent("about.branches.items.{$slug}.year") }}</p>
                    <h3 class="font-serif text-delos-cream text-2xl font-light mb-4 group-hover:text-delos-gold transition-colors duration-300">
                        {{ pcontent("about.branches.items.{$slug}.city") }}
                    </h3>
                    <p class="text-delos-muted text-xs leading-relaxed mb-2" style="font-family: 'Inter', sans-serif;">{{ pcontent("about.branches.items.{$slug}.address") }}</p>
                    <p class="text-delos-gold/70 text-xs mt-auto" dir="ltr" style="font-family: 'Inter', sans-serif; unicode-bidi: isolate;">{{ pcontent("about.branches.items.{$slug}.phone") }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

@endsection
