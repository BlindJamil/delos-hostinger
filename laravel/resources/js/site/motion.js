import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import Lenis from 'lenis';
import { qs, qsa } from './runtime.js';

const GROUP_STAGGER_MS = 90;
const EMPLOYEE_PIN_MIN_TRAVEL = 60;
const LINE_DURATION = 1.2;

const MOTION_VARIANTS = {
    fade: {
        duration: 0.95,
        ease: 'power2.out',
        from: {
            opacity: 0,
            filter: 'blur(4px)',
        },
    },
    'fade-up': {
        duration: 1,
        ease: 'power3.out',
        from: {
            y: 32,
            opacity: 0,
            scale: 0.97,
            filter: 'blur(8px)',
        },
    },
    'slide-left': {
        duration: 1.1,
        ease: 'power3.out',
        from: {
            x: -80,
            y: 24,
            opacity: 0,
            rotation: -3,
            scale: 0.95,
            filter: 'blur(8px)',
        },
    },
    'slide-right': {
        duration: 1.1,
        ease: 'power3.out',
        from: {
            x: 80,
            y: 24,
            opacity: 0,
            rotation: 3,
            scale: 0.95,
            filter: 'blur(8px)',
        },
    },
    'slide-up': {
        duration: 1.1,
        ease: 'power3.out',
        from: {
            y: 60,
            opacity: 0,
            scale: 0.9,
            filter: 'blur(10px)',
        },
    },
    'scale-in': {
        duration: 1.05,
        ease: 'power3.out',
        from: {
            y: 20,
            opacity: 0,
            scale: 0.88,
            filter: 'blur(10px)',
        },
    },
};

gsap.registerPlugin(ScrollTrigger);

export function initMotion(context) {
    context.documentElement.classList.toggle('reduced-motion', context.prefersReducedMotion);
    context.body.classList.toggle('reduced-motion', context.prefersReducedMotion);
    prepareMotionGroups();

    if (context.prefersReducedMotion) {
        applyReducedMotionState(context);
        return;
    }

    const lenis = initSmoothScroll(context);
    initHeroMotion();
    initHeroSlideshow(context);
    initProjectSlideshow(context);
    initBrandShowcase(context);
    initServiceArchCarousel(context);
    initRevealMotion();
    initCounters(context);
    initEmployeeShowcase(context);

    requestAnimationFrame(() => {
        ScrollTrigger.refresh();
    });

    if (!lenis) {
        context.documentElement.dataset.scrollMode = 'native';
    }
}

function applyReducedMotionState(context) {
    context.documentElement.dataset.scrollMode = 'native';
    ScrollTrigger.getAll().forEach((trigger) => trigger.kill());

    qsa('[data-motion], [data-motion-line]').forEach((element) => {
        element.classList.add('is-visible');
    });

    qsa('[data-motion-counter]').forEach((counter) => {
        setCounterValue(counter, Number(counter.dataset.motionCounter));
        counter.classList.remove('is-counting');

        const suffix = counter.nextElementSibling;
        if (suffix?.classList.contains('stat-suffix')) {
            suffix.classList.add('is-visible');
        }
    });
}

function initSmoothScroll(context) {
    if (context.prefersReducedMotion || context.isTouchDevice || window.innerWidth < 1024) {
        context.documentElement.dataset.scrollMode = 'native';
        return null;
    }

    const lenis = new Lenis({
        duration: 1.25,
        easing: (value) => Math.min(1, 1.001 - Math.pow(2, -10 * value)),
        smoothWheel: true,
        wheelMultiplier: 0.9,
    });

    context.documentElement.dataset.scrollMode = 'smooth';
    lenis.on('scroll', ScrollTrigger.update);

    gsap.ticker.add((time) => {
        lenis.raf(time * 1000);
    });
    gsap.ticker.lagSmoothing(0);

    return lenis;
}

function initHeroMotion() {
    qsa('[data-motion-hero]').forEach((hero) => {
        const splitLines = qsa('[data-motion="split-line"]', hero);
        const heroItems = qsa('[data-motion]', hero).filter(
            (element) => element.dataset.motion !== 'split-line',
        );
        const timeline = gsap.timeline({ defaults: { overwrite: 'auto' } });

        if (splitLines.length) {
            timeline.fromTo(splitLines, {
                yPercent: 110,
                rotation: 3.5,
            }, {
                yPercent: 0,
                rotation: 0,
                duration: 1.2,
                ease: 'power4.out',
                stagger: 0.12,
                onComplete: () => splitLines.forEach((line) => line.classList.add('is-visible')),
                clearProps: 'transform',
            });
        }

        if (heroItems.length) {
            timeline.fromTo(heroItems, {
                y: 30,
                opacity: 0,
                scale: 0.95,
                filter: 'blur(8px)',
            }, {
                y: 0,
                opacity: 1,
                scale: 1,
                filter: 'blur(0px)',
                duration: 1,
                ease: 'power3.out',
                stagger: 0.15,
                onComplete: () => heroItems.forEach((item) => item.classList.add('is-visible')),
                clearProps: 'opacity,transform,filter',
            }, splitLines.length ? '-=0.6' : 0);
        }

        const heroParallax = qs('[data-hero-parallax]', hero);
        if (heroParallax) {
            gsap.to(heroParallax, {
                yPercent: 18,
                scale: 1.14,
                ease: 'none',
                scrollTrigger: {
                    trigger: hero,
                    start: 'top top',
                    end: 'bottom top',
                    scrub: 1.2,
                },
            });
        }

        const fadeOutTarget = qs('[data-hero-fade-out]', hero);
        if (fadeOutTarget) {
            gsap.to(fadeOutTarget, {
                opacity: 0,
                scrollTrigger: {
                    trigger: hero,
                    start: '30% top',
                    end: '60% top',
                    scrub: true,
                },
            });
        }
    });
}

const SLIDESHOW_INTERVAL_MS = 15000;
const SLIDESHOW_CROSSFADE_S = 1.4;

function initHeroSlideshow(context) {
    const container = qs('#hero-slideshow');
    if (!container) {
        return;
    }

    const slides = qsa('.hero-slide', container);
    const dots = qsa('.hero-dot');
    if (slides.length < 2) {
        return;
    }

    let current = 0;
    let timer = null;

    const goTo = (index) => {
        if (index === current) {
            return;
        }

        const from = slides[current];
        const to = slides[index];

        gsap.to(from, {
            opacity: 0,
            scale: 1.04,
            duration: SLIDESHOW_CROSSFADE_S,
            ease: 'power2.inOut',
        });

        gsap.fromTo(to, {
            opacity: 0,
            scale: 1.06,
        }, {
            opacity: 1,
            scale: 1,
            duration: SLIDESHOW_CROSSFADE_S,
            ease: 'power2.inOut',
        });

        dots.forEach((dot, i) => {
            dot.classList.toggle('bg-delos-gold', i === index);
            dot.classList.toggle('w-6', i === index);
            dot.classList.toggle('bg-delos-cream/40', i !== index);
            dot.classList.toggle('w-2', i !== index);
        });

        current = index;
    };

    const next = () => {
        goTo((current + 1) % slides.length);
    };

    const resetTimer = () => {
        if (timer) {
            clearInterval(timer);
        }
        timer = setInterval(next, SLIDESHOW_INTERVAL_MS);
    };

    const prev = () => {
        goTo((current - 1 + slides.length) % slides.length);
    };

    dots.forEach((dot) => {
        dot.addEventListener('click', () => {
            const index = Number(dot.dataset.slide);
            goTo(index);
            resetTimer();
        });
    });

    const hero = qs('#hero');
    if (hero) {
        let startX = 0;
        let startY = 0;
        let tracking = false;

        hero.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
            tracking = true;
        }, { passive: true });

        hero.addEventListener('touchend', (e) => {
            if (!tracking) {
                return;
            }
            tracking = false;

            const dx = e.changedTouches[0].clientX - startX;
            const dy = e.changedTouches[0].clientY - startY;

            if (Math.abs(dx) < 50 || Math.abs(dy) > Math.abs(dx)) {
                return;
            }

            if (dx < 0) {
                next();
            } else {
                prev();
            }
            resetTimer();
        }, { passive: true });
    }

    gsap.fromTo(slides[0], { scale: 1.06 }, {
        scale: 1,
        duration: 1.8,
        ease: 'power2.out',
    });

    resetTimer();
}

const PROJECT_SLIDESHOW_MS = 10000;
const PROJECT_CROSSFADE_S = 1.6;

function initProjectSlideshow(context) {
    const container = qs('#project-slideshow');
    if (!container) {
        return;
    }

    const slides = qsa('.hero-slide', container);
    const dots = qsa('.hero-dot');
    const titleEl = qs('#project-hero-title');
    const metaEl = qs('#project-hero-meta');
    if (slides.length < 2) {
        return;
    }

    let current = 0;
    let timer = null;

    const goTo = (index) => {
        if (index === current) {
            return;
        }

        const from = slides[current];
        const to = slides[index];

        gsap.to(from, {
            opacity: 0,
            scale: 1.04,
            duration: PROJECT_CROSSFADE_S,
            ease: 'power2.inOut',
        });

        gsap.fromTo(to, {
            opacity: 0,
            scale: 1.06,
        }, {
            opacity: 1,
            scale: 1,
            duration: PROJECT_CROSSFADE_S,
            ease: 'power2.inOut',
        });

        dots.forEach((dot, i) => {
            dot.classList.toggle('bg-delos-gold', i === index);
            dot.classList.toggle('w-6', i === index);
            dot.classList.toggle('bg-delos-dark/20', i !== index);
            dot.classList.toggle('w-2', i !== index);
        });

        if (titleEl && metaEl) {
            gsap.to([titleEl, metaEl], {
                opacity: 0,
                duration: 0.4,
                ease: 'power2.in',
                onComplete: () => {
                    titleEl.textContent = to.alt || '';
                    metaEl.textContent = to.dataset.meta || '';
                    gsap.to([titleEl, metaEl], {
                        opacity: 1,
                        duration: 0.5,
                        ease: 'power2.out',
                    });
                },
            });
        }

        current = index;
    };

    const next = () => {
        goTo((current + 1) % slides.length);
    };

    const resetTimer = () => {
        if (timer) {
            clearInterval(timer);
        }
        timer = setInterval(next, PROJECT_SLIDESHOW_MS);
    };

    dots.forEach((dot) => {
        dot.addEventListener('click', () => {
            const index = Number(dot.dataset.slide);
            goTo(index);
            resetTimer();
        });
    });

    const hero = container.closest('section');
    if (hero) {
        let startX = 0;
        let startY = 0;
        let tracking = false;

        hero.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
            tracking = true;
        }, { passive: true });

        hero.addEventListener('touchend', (e) => {
            if (!tracking) {
                return;
            }
            tracking = false;
            const dx = e.changedTouches[0].clientX - startX;
            const dy = e.changedTouches[0].clientY - startY;
            if (Math.abs(dx) < 50 || Math.abs(dy) > Math.abs(dx)) {
                return;
            }
            if (dx < 0) {
                next();
            } else {
                goTo((current - 1 + slides.length) % slides.length);
            }
            resetTimer();
        }, { passive: true });
    }

    gsap.fromTo(slides[0], { scale: 1.06 }, {
        scale: 1,
        duration: 1.8,
        ease: 'power2.out',
    });

    resetTimer();
}

const BRAND_FEATURE_MS = 5000;

function initBrandShowcase(context) {
    const grid = qs('#brand-gallery-grid');
    if (!grid) {
        return;
    }

    const cards = qsa('.brand-card', grid);
    if (cards.length < 2) {
        return;
    }

    const total = cards.length;
    let current = total - 1;
    let timer = null;

    const entranceFrom = [
        { x: -80, y: 50, rotation: -5, scale: 0.8 },
        { x: 0, y: 80, rotation: 0, scale: 0.75 },
        { x: 80, y: 50, rotation: 5, scale: 0.8 },
        { x: -60, y: 60, rotation: -3, scale: 0.82 },
        { x: 60, y: 60, rotation: 3, scale: 0.82 },
    ];

    cards.forEach((card, i) => {
        const from = entranceFrom[i] || entranceFrom[0];
        gsap.set(card, {
            opacity: 0,
            y: from.y,
            x: from.x,
            scale: from.scale,
            rotation: from.rotation,
            filter: 'blur(10px)',
        });
    });

    ScrollTrigger.create({
        trigger: grid,
        start: 'top 90%',
        once: true,
        onEnter: () => {
            const tl = gsap.timeline({ onComplete: () => startFeatureCycle() });

            tl.to(cards[1], {
                opacity: 1, y: 0, x: 0, scale: 1, rotation: 0, filter: 'blur(0px)',
                duration: 1.2, ease: 'power4.out', clearProps: 'transform,filter',
            }, 0);

            tl.to([cards[0], cards[2]], {
                opacity: 1, y: 0, x: 0, scale: 1, rotation: 0, filter: 'blur(0px)',
                duration: 1.1, ease: 'power4.out', stagger: 0.08, clearProps: 'transform,filter',
            }, 0.15);

            tl.to([cards[3], cards[4]], {
                opacity: 1, y: 0, x: 0, scale: 1, rotation: 0, filter: 'blur(0px)',
                duration: 1, ease: 'power4.out', stagger: 0.08, clearProps: 'transform,filter',
            }, 0.3);
        },
    });

    const setFeatured = (index) => {
        cards.forEach((card, i) => {
            const isFeatured = i === index;
            if (isFeatured && !card.classList.contains('is-featured')) {
                card.classList.add('is-featured');
                gsap.fromTo(card, { scale: 1 }, {
                    scale: 1.03,
                    duration: 0.4,
                    ease: 'power2.out',
                    yoyo: true,
                    repeat: 1,
                });
            } else if (!isFeatured) {
                card.classList.remove('is-featured');
            }
        });
        current = index;
    };

    const startFeatureCycle = () => {
        if (timer) {
            clearInterval(timer);
        }
        timer = setInterval(() => {
            setFeatured((current + 1) % total);
        }, BRAND_FEATURE_MS);
    };

    cards.forEach((card) => {
        card.addEventListener('mouseenter', () => {
            const index = Number(card.dataset.brandIndex);
            setFeatured(index);
            if (timer) {
                clearInterval(timer);
                timer = null;
            }
        });
    });

    grid.addEventListener('mouseleave', () => {
        startFeatureCycle();
    });
}

const SVC_ARCH_INTERVAL_MS = 8000;
const SVC_ARCH_EXIT_S = 1.6;
const SVC_ARCH_ENTER_S = 1.6;

function initServiceArchCarousel(context) {
    const carousel = qs('#svc-arch-carousel');
    if (!carousel) {
        return;
    }

    const frames = qsa('.svc-arch-frame', carousel);
    const label = qs('#svc-arch-label', carousel);
    if (frames.length < 2) {
        return;
    }

    let current = 0;
    let animating = false;

    const goTo = (index) => {
        if (index === current || animating) {
            return;
        }
        animating = true;

        const from = frames[current];
        const to = frames[index];

        const tl = gsap.timeline({
            onComplete: () => {
                from.classList.add('pointer-events-none');
                from.style.position = 'absolute';
                from.style.inset = '0';
                gsap.set(from, { x: 0, rotation: 0, scale: 1, opacity: 0 });
                current = index;
                animating = false;
            },
        });

        tl.to(from, {
            x: '-110%',
            rotation: -4,
            scale: 0.95,
            opacity: 0,
            duration: SVC_ARCH_EXIT_S,
            ease: 'power2.inOut',
        }, 0);

        if (label) {
            tl.to(label, {
                opacity: 0,
                y: -8,
                duration: 0.5,
                ease: 'power2.in',
            }, 0.3);

            tl.call(() => {
                label.textContent = to.dataset.label || '';
            }, null, 0.9);

            tl.to(label, {
                opacity: 1,
                y: 0,
                duration: 0.6,
                ease: 'power2.out',
            }, 1.0);
        }

        to.style.position = 'absolute';
        to.style.inset = '0';
        to.classList.remove('pointer-events-none');

        tl.fromTo(to, {
            x: '110%',
            rotation: 4,
            scale: 0.95,
            opacity: 0,
        }, {
            x: '0%',
            rotation: 0,
            scale: 1,
            opacity: 1,
            duration: SVC_ARCH_ENTER_S,
            ease: 'power2.out',
            onComplete: () => {
                to.style.position = 'relative';
                to.style.inset = '';
            },
        }, 0.6);
    };

    const next = () => {
        goTo((current + 1) % frames.length);
    };

    setInterval(next, SVC_ARCH_INTERVAL_MS);
}

function initRevealMotion() {
    const animated = new Set();
    const groups = qsa('[data-motion-group]').filter((group) => !group.closest('[data-motion-hero]'));

    groups.forEach((group) => {
        const lines = qsa('[data-motion-line]', group).filter(
            (element) => element.closest('[data-motion-group]') === group,
        );
        const items = qsa('[data-motion]', group).filter(
            (element) => element.closest('[data-motion-group]') === group && !element.closest('[data-motion-hero]'),
        );

        if (!lines.length && !items.length) {
            return;
        }

        lines.forEach((line) => animated.add(line));
        items.forEach((item) => animated.add(item));

        const timeline = gsap.timeline({
            scrollTrigger: {
                trigger: group,
                start: 'top 85%',
                toggleActions: 'play none none none',
                once: true,
            },
        });

        if (lines.length) {
            timeline.fromTo(lines, {
                scaleX: 0,
                transformOrigin: 'left center',
            }, {
                scaleX: 1,
                duration: LINE_DURATION,
                ease: 'power4.out',
                stagger: 0.1,
                onComplete: () => lines.forEach((line) => line.classList.add('is-visible')),
                clearProps: 'transform',
            }, 0);
        }

        items.forEach((item, index) => {
            const staggerSeconds = Number(group.dataset.motionStagger ?? GROUP_STAGGER_MS) / 1000;
            const basePosition = lines.length ? 0.2 : 0;
            addRevealTween(timeline, item, basePosition + (index * staggerSeconds));
        });
    });

    qsa('[data-motion]', document).forEach((item) => {
        if (animated.has(item) || item.closest('[data-motion-hero]') || item.closest('#employee-track')) {
            return;
        }

        const timeline = gsap.timeline({
            scrollTrigger: {
                trigger: item,
                start: 'top 85%',
                toggleActions: 'play none none none',
                once: true,
            },
        });

        addRevealTween(timeline, item, 0);
    });

    qsa('[data-motion-line]', document).forEach((line) => {
        if (animated.has(line) || line.closest('[data-motion-hero]') || line.closest('#employee-track')) {
            return;
        }

        gsap.fromTo(line, {
            scaleX: 0,
            transformOrigin: 'left center',
        }, {
            scaleX: 1,
            duration: LINE_DURATION,
            ease: 'power4.out',
            scrollTrigger: {
                trigger: line,
                start: 'top 85%',
                toggleActions: 'play none none none',
                once: true,
            },
            onComplete: () => line.classList.add('is-visible'),
            clearProps: 'transform',
        });
    });
}

function initCounters(context) {
    qsa('[data-motion-counter]').forEach((counter) => {
        const targetValue = Number(counter.dataset.motionCounter);
        if (!Number.isFinite(targetValue)) {
            return;
        }

        setCounterValue(counter, 0);

        const suffix = counter.nextElementSibling;
        if (suffix?.classList.contains('stat-suffix')) {
            suffix.classList.remove('is-visible');
        }

        ScrollTrigger.create({
            trigger: counter.closest('[data-motion]') ?? counter,
            start: 'top 80%',
            once: true,
            onEnter: () => animateCounter(counter, targetValue, context.prefersReducedMotion),
        });
    });
}

function animateCounter(counter, targetValue, prefersReducedMotion) {
    if (prefersReducedMotion) {
        setCounterValue(counter, targetValue);
        return;
    }

    const suffix = counter.nextElementSibling;
    const duration = Math.min(3000, Math.max(1600, targetValue * 4));
    const startTime = performance.now();
    counter.classList.add('is-counting');

    const step = (timestamp) => {
        const progress = Math.min((timestamp - startTime) / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 4);
        setCounterValue(counter, Math.floor(eased * targetValue));

        if (progress < 1) {
            requestAnimationFrame(step);
            return;
        }

        setCounterValue(counter, targetValue);
        counter.classList.remove('is-counting');

        if (suffix?.classList.contains('stat-suffix')) {
            suffix.classList.add('is-visible');
        }
    };

    requestAnimationFrame(step);
}

function initEmployeeShowcase(context) {
    if (context.page !== 'home' || context.prefersReducedMotion || context.isTouchDevice) {
        return;
    }

    const section = qs('#employees-section');
    const track = qs('#employee-track');
    if (!section || !track) {
        return;
    }

    const slides = qsa('.employee-slide', track);
    if (slides.length < 2) {
        return;
    }

    gsap.fromTo(slides, {
        opacity: 0,
        y: 36,
        scale: 0.92,
        rotateY: 8,
        filter: 'blur(8px)',
    }, {
        opacity: 1,
        y: 0,
        scale: 1,
        rotateY: 0,
        filter: 'blur(0px)',
        duration: 0.9,
        ease: 'power3.out',
        stagger: 0.18,
        scrollTrigger: {
            trigger: section,
            start: 'top 82%',
            toggleActions: 'play none none none',
            once: true,
        },
        onComplete: () => slides.forEach((slide) => slide.classList.add('is-visible')),
        clearProps: 'opacity,transform,filter',
    });

    const getMetrics = () => {
        const sectionWidth = section.clientWidth;
        const maxTravel = Math.max(track.scrollWidth - sectionWidth, 0);
        const snapStops = dedupeStops(slides.map((slide) => {
            const slideCenter = slide.offsetLeft + (slide.offsetWidth / 2);
            return clamp(slideCenter - (sectionWidth / 2), 0, maxTravel);
        }));

        return {
            maxTravel,
            snapStops,
        };
    };

    if (getMetrics().maxTravel <= EMPLOYEE_PIN_MIN_TRAVEL) {
        return;
    }

    gsap.to(track, {
        x: () => -getMetrics().maxTravel,
        ease: 'none',
        scrollTrigger: {
            trigger: section,
            start: 'top top',
            end: () => `+=${getMetrics().maxTravel}`,
            scrub: 1.2,
            pin: true,
            anticipatePin: 1,
            invalidateOnRefresh: true,
            snap: {
                snapTo: (progress) => {
                    const { maxTravel, snapStops } = getMetrics();
                    if (maxTravel <= 0 || snapStops.length < 2) {
                        return progress;
                    }

                    return gsap.utils.snap(
                        snapStops.map((stop) => stop / maxTravel),
                        progress,
                    );
                },
                duration: {
                    min: 0.12,
                    max: 0.28,
                },
                ease: 'power1.inOut',
            },
        },
    });
}

function prepareMotionGroups() {
    qsa('[data-motion-group]').forEach((group) => {
        const groupItems = qsa('[data-motion], [data-motion-line]', group).filter(
            (element) => element.closest('[data-motion-group]') === group,
        );

        groupItems.forEach((element, index) => {
            if (element.dataset.motionDelay || element.style.getPropertyValue('--motion-delay')) {
                return;
            }

            const stagger = Number(group.dataset.motionStagger ?? GROUP_STAGGER_MS);
            element.style.setProperty('--motion-delay', `${index * stagger}ms`);
        });
    });
}

function setCounterValue(counter, value) {
    counter.textContent = Number(value).toLocaleString();
}

function addRevealTween(timeline, element, position) {
    const config = getMotionConfig(element);

    timeline.fromTo(element, config.from, {
        x: 0,
        y: 0,
        opacity: 1,
        rotation: 0,
        scale: 1,
        filter: 'blur(0px)',
        duration: config.duration,
        ease: config.ease,
        onComplete: () => element.classList.add('is-visible'),
        clearProps: 'opacity,transform,filter',
    }, position);
}

function getMotionConfig(element) {
    const baseConfig = MOTION_VARIANTS[element.dataset.motion] ?? MOTION_VARIANTS['fade-up'];

    // In RTL (Arabic), flip the x-axis sign on slide-left / slide-right so the
    // animation still enters from the reading direction's leading edge.
    const isRtl = document.documentElement.getAttribute('dir') === 'rtl';
    if (isRtl && baseConfig.from && typeof baseConfig.from.x === 'number') {
        return {
            ...baseConfig,
            from: { ...baseConfig.from, x: -baseConfig.from.x },
        };
    }

    return baseConfig;
}

function clamp(value, min, max) {
    return Math.min(Math.max(value, min), max);
}

function dedupeStops(stops) {
    return stops.filter((stop, index) => index === 0 || Math.abs(stop - stops[index - 1]) > 1);
}
