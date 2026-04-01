// ============================================================
// REAL VIEWPORT HEIGHT — accounts for mobile browser chrome
// ============================================================
function setRealVH() {
    document.documentElement.style.setProperty('--vh', window.innerHeight + 'px');
}
setRealVH();
window.addEventListener('resize', setRealVH);

// ============================================================
// PAGE LOADER — only gate pages that have a loader element
// ============================================================
const pageLoader = document.querySelector('.page-loader');

if (pageLoader) {
    document.documentElement.classList.add('has-loader');
    window.addEventListener('load', () => {
        setTimeout(() => {
            pageLoader.classList.add('loaded');
            document.documentElement.classList.remove('has-loader');
            setTimeout(initAnimations, 100);
        }, 300);
    });
} else {
    // No loader — start animations as soon as DOM + GSAP are ready
    if (document.readyState === 'complete') {
        setTimeout(initAnimations, 50);
    } else {
        window.addEventListener('load', () => setTimeout(initAnimations, 50));
    }
}

// ============================================================
// PAGE TRANSITIONS
// ============================================================
const transitionOverlay = document.querySelector('.page-transition-overlay');

if (transitionOverlay) {
    if (sessionStorage.getItem('page-transition') === 'true') {
        sessionStorage.removeItem('page-transition');
        transitionOverlay.style.transform = 'translateY(0)';
        transitionOverlay.style.transition = 'none';
        requestAnimationFrame(() => {
            transitionOverlay.style.transition = '';
            transitionOverlay.classList.add('exit');
            setTimeout(() => {
                transitionOverlay.classList.remove('exit');
                transitionOverlay.style.transform = '';
            }, 700);
        });
    }

    document.addEventListener('click', (e) => {
        const link = e.target.closest('a');
        if (!link) return;
        const href = link.getAttribute('href');
        if (!href) return;
        if (href.startsWith('#') || href.startsWith('tel:') || href.startsWith('mailto:') ||
            href.startsWith('http') || link.target === '_blank') return;
        if (href === window.location.pathname) return;

        e.preventDefault();
        sessionStorage.setItem('page-transition', 'true');
        transitionOverlay.classList.add('active');
        setTimeout(() => { window.location.href = href; }, 600);
    });
}

// ============================================================
// REDUCED MOTION
// ============================================================
const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// ============================================================
// CUSTOM CURSOR
// ============================================================
if (!prefersReducedMotion && window.matchMedia('(pointer: fine)').matches && window.innerWidth > 1024) {
    document.body.classList.add('has-custom-cursor');
    const cursor = document.createElement('div');
    cursor.className = 'custom-cursor';
    cursor.setAttribute('aria-hidden', 'true');
    document.body.appendChild(cursor);

    let mx = 0, my = 0, cx = 0, cy = 0;
    let cursorRaf = null;
    function moveCursor() {
        cx += (mx - cx) * 0.18; cy += (my - cy) * 0.18;
        cursor.style.left = cx + 'px'; cursor.style.top = cy + 'px';
        if (Math.abs(mx - cx) > 0.5 || Math.abs(my - cy) > 0.5) {
            cursorRaf = requestAnimationFrame(moveCursor);
        } else { cursorRaf = null; }
    }
    document.addEventListener('mousemove', e => {
        mx = e.clientX; my = e.clientY;
        if (!cursor.classList.contains('cursor-visible')) cursor.classList.add('cursor-visible');
        if (!cursorRaf) cursorRaf = requestAnimationFrame(moveCursor);
    }, { passive: true });

    const hoverTargets = 'a, button, .card-tilt, .img-hover-zoom, .employee-card, .brand-strip-item';
    document.addEventListener('mouseover', e => { if (e.target.closest(hoverTargets)) cursor.classList.add('cursor-hover'); });
    document.addEventListener('mouseout', e => { if (e.target.closest(hoverTargets)) cursor.classList.remove('cursor-hover'); });
    document.addEventListener('mousedown', () => cursor.classList.add('cursor-click'));
    document.addEventListener('mouseup', () => cursor.classList.remove('cursor-click'));
}

// ============================================================
// HEADER SCROLL
// ============================================================
const siteHeader = document.getElementById('site-header');
window.addEventListener('scroll', () => {
    if (siteHeader) siteHeader.classList.toggle('header-scrolled', window.scrollY > 50);
}, { passive: true });

// ============================================================
// MOBILE MENU
// ============================================================
const mobileBtn = document.getElementById('mobile-menu-btn');
const mobileMenu = document.getElementById('mobile-menu');
if (mobileBtn && mobileMenu) {
    mobileMenu.classList.remove('hidden');
    mobileBtn.addEventListener('click', () => {
        const isOpen = mobileMenu.classList.contains('menu-open');
        mobileMenu.classList.toggle('menu-open');
        mobileBtn.setAttribute('aria-expanded', String(!isOpen));
        if (!isOpen) {
            mobileMenu.querySelectorAll('a').forEach((link, i) => {
                link.style.transitionDelay = `${0.1 + i * 0.08}s`;
            });
        }
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && mobileMenu.classList.contains('menu-open')) {
            mobileMenu.classList.remove('menu-open');
            mobileBtn.setAttribute('aria-expanded', 'false');
            mobileBtn.focus();
        }
    });
}

// ============================================================
// BUTTON RIPPLE
// ============================================================
document.querySelectorAll('.btn-ripple').forEach(btn => {
    btn.addEventListener('mousemove', e => {
        const rect = btn.getBoundingClientRect();
        btn.style.setProperty('--ripple-x', ((e.clientX - rect.left) / rect.width * 100) + '%');
        btn.style.setProperty('--ripple-y', ((e.clientY - rect.top) / rect.height * 100) + '%');
    }, { passive: true });
});

// ============================================================
// MAGNETIC BUTTONS (lerp-based)
// ============================================================
if (!prefersReducedMotion) {
    document.querySelectorAll('.magnetic-btn').forEach(btn => {
        let tx = 0, ty = 0, cx = 0, cy = 0, hovering = false, raf = null;
        const lerp = (a, b, f) => a + (b - a) * f;

        function animate() {
            const f = hovering ? 0.15 : 0.08;
            cx = lerp(cx, tx, f); cy = lerp(cy, ty, f);
            btn.style.transform = `translate(${cx}px, ${cy}px)`;
            if (Math.abs(cx - tx) > 0.1 || Math.abs(cy - ty) > 0.1 || hovering || Math.abs(cx) > 0.1 || Math.abs(cy) > 0.1) {
                raf = requestAnimationFrame(animate);
            } else { btn.style.transform = ''; raf = null; }
        }

        btn.addEventListener('mouseenter', () => { hovering = true; btn.style.transition = 'none'; if (!raf) raf = requestAnimationFrame(animate); });
        btn.addEventListener('mousemove', e => { const r = btn.getBoundingClientRect(); tx = (e.clientX - r.left - r.width/2) * 0.25; ty = (e.clientY - r.top - r.height/2) * 0.25; });
        btn.addEventListener('mouseleave', () => { hovering = false; tx = 0; ty = 0; if (!raf) raf = requestAnimationFrame(animate); });
    });
}

// ============================================================
// 3D CARD TILT
// ============================================================
if (!prefersReducedMotion) {
    document.querySelectorAll('.card-tilt').forEach(card => {
        let tiltRaf = null;
        card.addEventListener('mousemove', e => {
            if (tiltRaf) return;
            tiltRaf = requestAnimationFrame(() => {
                const r = card.getBoundingClientRect();
                const rx = ((e.clientY - r.top - r.height/2) / r.height) * -8;
                const ry = ((e.clientX - r.left - r.width/2) / r.width) * 8;
                card.style.transform = `perspective(800px) rotateX(${rx}deg) rotateY(${ry}deg) translateY(-4px)`;
                card.style.transition = 'transform 0.15s ease-out, box-shadow 0.3s ease-out';
                tiltRaf = null;
            });
        }, { passive: true });
        card.addEventListener('mouseleave', () => {
            if (tiltRaf) { cancelAnimationFrame(tiltRaf); tiltRaf = null; }
            card.style.transform = '';
            card.style.transition = 'transform 0.6s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.6s cubic-bezier(0.22, 1, 0.36, 1)';
        });
    });
}

// ============================================================
// COUNTER ANIMATION
// ============================================================
function easeOutQuart(t) { return 1 - Math.pow(1 - t, 4); }

function animateCounter(el) {
    const target = parseInt(el.dataset.counter, 10);
    const duration = Math.min(3000, Math.max(1600, target * 4));
    const start = performance.now();
    const suffix = el.nextElementSibling;
    el.classList.add('counting');

    function step(now) {
        const p = Math.min((now - start) / duration, 1);
        el.textContent = Math.floor(easeOutQuart(p) * target).toLocaleString();
        if (p < 1) { requestAnimationFrame(step); }
        else { el.textContent = target.toLocaleString(); el.classList.remove('counting'); if (suffix && suffix.classList.contains('stat-suffix')) suffix.classList.add('visible'); }
    }
    requestAnimationFrame(step);
}

// ============================================================
// GSAP ANIMATIONS — The main show
// ============================================================
let _initRetries = 0;
function initAnimations() {
    if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') {
        if (++_initRetries > 30) return; // Stop after 3 seconds
        setTimeout(initAnimations, 100);
        return;
    }

    gsap.registerPlugin(ScrollTrigger);

    // --- LENIS SMOOTH SCROLL (desktop only) ---
    const isTouch = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
    if (typeof Lenis !== 'undefined' && !prefersReducedMotion && !isTouch) {
        const lenis = new Lenis({
            duration: 1.4,
            easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
            smoothWheel: true,
            wheelMultiplier: 0.9
        });

        lenis.on('scroll', ScrollTrigger.update);
        gsap.ticker.add((time) => lenis.raf(time * 1000));
    }

    // --- HERO ENTRANCE ---
    const heroTl = gsap.timeline();
    const hasSplitLines = document.querySelectorAll('.split-line-inner').length > 0;

    if (hasSplitLines) {
        heroTl.to('.split-line-inner', {
            y: 0, rotation: 0,
            duration: 1.4,
            ease: 'power4.out',
            stagger: 0.14
        });
    }

    heroTl.to('.hero-fade', {
        opacity: 1, y: 0, scale: 1,
        duration: 1.2,
        ease: 'power3.out',
        stagger: 0.18
    }, hasSplitLines ? '-=0.6' : 0);

    heroTl.to('.hero-fade-no-translate', {
        opacity: 1,
        duration: 1.2,
        ease: 'power2.out'
    }, '-=0.3');

    // --- HERO PARALLAX ---
    gsap.to('.hero-parallax-img', {
        yPercent: 20, scale: 1.15, ease: 'none',
        scrollTrigger: { trigger: '#hero', start: 'top top', end: 'bottom top', scrub: 1.5 }
    });

    gsap.to('.hero-fade-no-translate', {
        opacity: 0,
        scrollTrigger: { trigger: '#hero', start: '30% top', end: '60% top', scrub: true }
    });

    // --- .gsap-el REVEALS — pure IntersectionObserver + CSS transitions ---
    // No GSAP involvement = no library conflict, no frame-drop glitch
    const elObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const delay = parseFloat(el.dataset.staggerDelay || 0);
                if (delay) {
                    el.style.transitionDelay = delay + 's';
                }
                el.classList.add('in-view');
                elObserver.unobserve(el);
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -10% 0px' });

    // Assign stagger delays per section, then observe
    document.querySelectorAll('.gsap-section, #employees-section').forEach(section => {
        section.querySelectorAll('.gsap-el').forEach((el, i) => {
            el.dataset.staggerDelay = (i * 0.08).toFixed(2);
            elObserver.observe(el);
        });
    });

    // Observe any .gsap-el not inside a .gsap-section (standalone)
    document.querySelectorAll('.gsap-el').forEach(el => {
        if (!el.closest('.gsap-section') && !el.closest('#employees-section')) {
            elObserver.observe(el);
        }
    });

    // --- SECTION ANIMATIONS (GSAP only for complex stuff: entrances, images, lines) ---
    const revealConfigs = {
        'slide-left':   { x: -60, y: 20, opacity: 0, rotation: -2, scale: 0.97 },
        'slide-right':  { x: 60, y: 20, opacity: 0, rotation: 2, scale: 0.97 },
        'slide-up':     { y: 50, opacity: 0, scale: 0.95 },
        'scale-rotate': { scale: 0.8, opacity: 0, rotation: -3 },
        'fade-blur':    { opacity: 0, scale: 0.97, y: 30 }
    };

    document.querySelectorAll('.gsap-section:not(#brands-section):not(#cta-section), #employees-section').forEach(section => {
        const hasEntrance = section.hasAttribute('data-section-enter');
        const dir = section.dataset.sectionEnter;

        // Only create a GSAP timeline if the section has entrance animations, lines, or image reveals
        const lines = section.querySelectorAll('.gsap-line');
        const reveals = section.querySelectorAll('[data-reveal]:not(.collection-card):not(.about-text-col)');
        const brandMarquee = section.querySelector('.brand-marquee-wrapper');

        if (!hasEntrance && !lines.length && !reveals.length && !brandMarquee) return;

        const tl = gsap.timeline({
            scrollTrigger: {
                trigger: section,
                start: 'top 80%',
                toggleActions: 'play none none none'
            }
        });

        // Section entrance slide-in
        if (hasEntrance) {
            const fromVars = {
                left:   { x: -100 },
                right:  { x: 100 },
                bottom: { y: 60 }
            }[dir] || { y: 50 };

            tl.fromTo(section,
                { ...fromVars, opacity: 0 },
                { x: 0, y: 0, opacity: 1, duration: 1.4, ease: 'power3.out',
                  onComplete: () => section.classList.add('section-entered') }
            );
        }

        // Gold accent lines
        if (lines.length) {
            tl.fromTo(lines,
                { scaleX: 0, transformOrigin: 'left center' },
                { scaleX: 1, duration: 1.4, ease: 'power4.out', stagger: 0.12 },
                hasEntrance ? 0.6 : 0);
        }

        // Image reveals
        reveals.forEach((el, i) => {
            const type = el.dataset.reveal;
            const from = revealConfigs[type] || revealConfigs['fade-blur'];
            tl.fromTo(el, from,
                { x: 0, y: 0, opacity: 1, scale: 1, rotation: 0,
                  duration: 1.3, ease: 'power3.out',
                  onComplete: () => el.classList.add('revealed')
                }, hasEntrance ? (0.6 + i * 0.15) : (0.15 + i * 0.15));
        });

        // Brand marquee entrance
        if (brandMarquee) {
            tl.fromTo(brandMarquee,
                { opacity: 0 },
                { opacity: 1, duration: 1.3, ease: 'power3.out' },
                hasEntrance ? 0.8 : 0.25);
        }
    });

    // --- INDIVIDUAL SCROLL REVEALS (collection cards + about text) ---
    document.querySelectorAll('.collection-card[data-reveal], .about-text-col[data-reveal]').forEach(el => {
        const type = el.dataset.reveal;
        const from = revealConfigs[type] || revealConfigs['fade-blur'];
        gsap.fromTo(el, from,
            { x: 0, y: 0, opacity: 1, scale: 1, rotation: 0,
              duration: 1.3, ease: 'power3.out',
              onComplete: () => el.classList.add('revealed'),
              scrollTrigger: {
                  trigger: el,
                  start: 'top 82%',
                  toggleActions: 'play none none none'
              }
            }
        );
    });

    // --- EMPLOYEE CARDS ---
    const employeeTrack = document.getElementById('employee-track');
    const employeeSection = document.getElementById('employees-section');

    if (employeeTrack && employeeSection) {
        const slides = employeeTrack.querySelectorAll('.employee-slide');
        const isMobile = window.innerWidth < 1024;

        // Card fade-in — trigger early so cards are visible before pin activates
        slides.forEach((slide, i) => {
            gsap.fromTo(slide,
                { opacity: 0, scale: 0.9 },
                {
                    opacity: 1, scale: 1,
                    duration: isMobile ? 0.6 : 0.8,
                    ease: 'power2.out',
                    delay: i * 0.15,
                    scrollTrigger: {
                        trigger: employeeSection,
                        start: 'top 80%',
                        toggleActions: 'play none none none'
                    }
                }
            );
        });

        // Horizontal scroll — only when cards overflow the container
        const totalScroll = employeeTrack.scrollWidth - employeeSection.offsetWidth;
        if (totalScroll > 50) {
            gsap.to(employeeTrack, {
                x: -totalScroll,
                ease: 'none',
                scrollTrigger: {
                    trigger: employeeSection,
                    start: 'top top',
                    end: () => `+=${totalScroll + (isMobile ? 300 : 400)}`,
                    scrub: isMobile ? 1.5 : 2.5,
                    pin: true,
                    anticipatePin: 1,
                    invalidateOnRefresh: true
                }
            });
        }
    }

    // --- BRANDS & CTA — animated after pin recalculation ---
    ScrollTrigger.refresh();

    ['#brands-section', '#cta-section'].forEach(id => {
        const section = document.querySelector(id);
        if (!section) return;

        const dir = section.dataset.sectionEnter;
        const fromVars = {
            left: { x: -100 }, right: { x: 100 }, bottom: { y: 60 }
        }[dir] || { y: 50 };

        const tl = gsap.timeline({
            scrollTrigger: {
                trigger: section,
                start: 'top 80%',
                toggleActions: 'play none none none'
            }
        });

        tl.fromTo(section,
            { ...fromVars, opacity: 0 },
            { x: 0, y: 0, opacity: 1, duration: 1.4, ease: 'power3.out',
              onComplete: () => section.classList.add('section-entered') }
        );

        const lines = section.querySelectorAll('.gsap-line');
        if (lines.length) {
            tl.fromTo(lines,
                { scaleX: 0, transformOrigin: 'left center' },
                { scaleX: 1, duration: 1.4, ease: 'power4.out', stagger: 0.12 }, 0.6);
        }

        // .gsap-el handled by IntersectionObserver — not GSAP

        const brandMarquee = section.querySelector('.brand-marquee-wrapper');
        if (brandMarquee) {
            tl.fromTo(brandMarquee, { opacity: 0 }, { opacity: 1, duration: 1.3, ease: 'power3.out' }, 0.8);
        }
    });

    // --- COUNTER TRIGGER ---
    document.querySelectorAll('[data-counter]').forEach(el => {
        el.textContent = '0';
        ScrollTrigger.create({
            trigger: el,
            start: 'top 80%',
            onEnter: () => animateCounter(el),
            once: true
        });
    });

    // --- FOOTER ENTRANCE ---
    const footerCols = document.querySelectorAll('footer .grid > div');
    if (footerCols.length) {
        gsap.fromTo(footerCols,
            { y: 30, opacity: 0 },
            { y: 0, opacity: 1, duration: 1.1, ease: 'power3.out', stagger: 0.13,
              scrollTrigger: {
                  trigger: 'footer',
                  start: 'top 80%',
                  toggleActions: 'play none none none'
              }
            });
    }
}

// ============================================================
// SMOOTH ANCHORS
// ============================================================
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
        const target = document.querySelector(a.getAttribute('href'));
        if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
    });
});

// ============================================================
// VIDEO PLAY/PAUSE
// ============================================================
const delosVideo = document.getElementById('delos-video');
const videoOverlay = document.getElementById('video-overlay');

if (delosVideo && videoOverlay) {
    const playVideo = () => {
        delosVideo.muted = false;
        delosVideo.play();
        videoOverlay.style.opacity = '0';
        videoOverlay.style.pointerEvents = 'none';
        delosVideo.setAttribute('controls', '');
    };

    const pauseVideo = () => {
        delosVideo.pause();
        delosVideo.muted = true;
        videoOverlay.style.opacity = '1';
        videoOverlay.style.pointerEvents = 'auto';
        delosVideo.removeAttribute('controls');
    };

    videoOverlay.addEventListener('click', playVideo);
    delosVideo.addEventListener('ended', pauseVideo);
}
