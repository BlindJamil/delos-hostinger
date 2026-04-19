@extends('layouts.app')

@php
    $empName = $employee->localized('name');
    $empRole = $employee->localized('role');
    $empBranch = $employee->branch;
    $empAchievement = $employee->localized('achievement');
    $metaDescription = \Illuminate\Support\Str::limit(trim(strip_tags((string) $empAchievement)), 160);
@endphp

@section('title', $empName . ' — ' . ($empRole ?: 'Delos International'))
@section('description', $metaDescription)

@section('content')

{{-- TEAM MEMBER SHOW — editorial, light-themed profile page --}}
<section data-motion-hero class="employee-show bg-delos-cream overflow-hidden">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-12">
        <div class="pt-28 lg:pt-36 pb-20 lg:pb-32">

            {{-- Back to team link --}}
            <div data-motion="fade" class="mb-10 lg:mb-16">
                <a href="{{ lroute('home') }}#employees-section"
                   class="inline-flex items-center gap-3 text-delos-muted hover:text-delos-gold text-[11px] tracking-[0.3em] uppercase font-medium transition-colors duration-300 group"
                   style="font-family: 'Inter', sans-serif;">
                    <svg class="w-4 h-4 transition-transform duration-300 group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>{{ pcontent('common.ctas.back_to_team', 'Back to Team') }}</span>
                </a>
            </div>

            {{-- 2-column editorial layout --}}
            <div class="grid lg:grid-cols-12 gap-10 lg:gap-16 lg:items-start">

                {{-- Portrait column --}}
                <div class="lg:col-span-5 order-1">
                    <div data-motion="fade" class="employee-show__portrait relative overflow-hidden bg-delos-ivory">
                        <x-admin-edit-pill :href="route('admin.employees.edit', $employee)" :label="'Edit ' . $empName" />
                        @if($employee->image)
                            <x-responsive-image :src="$employee->image"
                                :mobile-src="$employee->image_mobile"
                                :focal="$employee->focal_point"
                                :alt="$empName"
                                sizes="(min-width: 1024px) 540px, 100vw"
                                loading="eager"
                                fetchpriority="high"
                                class="w-full h-full object-cover" />
                        @else
                            <div class="absolute inset-0 flex items-center justify-center text-delos-gold/40 font-serif text-7xl font-light">
                                {{ mb_substr($empName, 0, 1) }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Text column --}}
                <div class="lg:col-span-7 order-2 lg:pt-4">

                    @if($empBranch)
                        <div data-motion="fade-up" class="inline-flex items-center gap-3 mb-6 lg:mb-7">
                            <span class="w-8 h-px bg-delos-gold"></span>
                            <span class="text-delos-gold text-[11px] tracking-[0.4em] uppercase font-medium" style="font-family: 'Inter', sans-serif;">{{ $empBranch }}</span>
                        </div>
                    @endif

                    <h1 data-motion="fade-up" class="employee-show__name font-serif text-delos-dark font-light leading-[1.08] mb-5">
                        {{ $empName }}
                    </h1>

                    @if($empRole)
                        <p data-motion="fade-up" class="text-delos-gold text-[12px] tracking-[0.3em] uppercase font-medium mb-8 lg:mb-10" style="font-family: 'Inter', sans-serif;">
                            {{ $empRole }}
                        </p>
                    @endif

                    <div data-motion-line class="w-12 h-px bg-delos-gold/60 mb-8 lg:mb-10"></div>

                    @if($empAchievement)
                        <div data-motion="fade-up" class="employee-show__bio text-delos-muted text-base lg:text-lg leading-relaxed max-w-xl" style="font-family: 'Inter', sans-serif;">
                            {!! $empAchievement !!}
                        </div>
                    @endif

                    <div data-motion="fade-up" class="mt-12 lg:mt-16 flex flex-col sm:flex-row gap-6 sm:gap-8 items-start sm:items-center">
                        <a href="{{ lroute('contact') }}"
                           class="inline-flex items-center gap-3 px-7 py-3 bg-delos-gold text-delos-dark text-[11px] tracking-[0.25em] uppercase font-medium hover:bg-delos-gold-light transition-all duration-300 group"
                           style="font-family: 'Inter', sans-serif;">
                            {{ pcontent('common.ctas.work_with_team', 'Work with our team') }}
                            <svg class="w-3.5 h-3.5 transition-transform duration-300 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>

                        <a href="{{ lroute('about') }}"
                           class="inline-flex items-center gap-2 text-delos-dark/70 hover:text-delos-gold text-[11px] tracking-[0.25em] uppercase font-medium transition-colors duration-300 border-b border-delos-dark/20 hover:border-delos-gold pb-1"
                           style="font-family: 'Inter', sans-serif;">
                            {{ pcontent('common.ctas.about_company', 'About the company') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
