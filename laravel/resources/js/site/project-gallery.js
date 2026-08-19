import gsap from 'gsap';
import { qs, qsa } from './runtime.js';
import { getLenis } from './motion.js';

const OVERLAY_FADE_S = 0.4;
const IMAGE_CROSSFADE_S = 0.35;
const SCROLL_LOCK_CLASS = 'lightbox-open';

/**
 * Fullscreen gallery lightbox for the project detail page.
 *
 * Deliberately NOT registered inside initMotion(): that function returns
 * early under reduced motion, which would leave reduced-motion users unable
 * to open the gallery at all. The motion spec requires reduced motion to be
 * functional, not cosmetic — so this module owns its own reduced-motion
 * branch and is wired independently in index.js.
 *
 * No-ops on every page that lacks #project-gallery.
 */
export function initProjectGallery(context) {
    const gallery = qs('#project-gallery');
    const lightbox = qs('[data-project-lightbox]');
    if (!gallery || !lightbox) {
        return;
    }

    const triggers = qsa('[data-gallery-index]', gallery);
    const image = qs('[data-lightbox-image]', lightbox);
    const counter = qs('[data-lightbox-counter]', lightbox);
    const closeButton = qs('[data-lightbox-close]', lightbox);
    const prevButton = qs('[data-lightbox-prev]', lightbox);
    const nextButton = qs('[data-lightbox-next]', lightbox);
    if (!triggers.length || !image) {
        return;
    }

    const sources = triggers.map((trigger) => trigger.dataset.galleryFull);
    const isSingle = sources.length < 2;
    let currentIndex = 0;
    let lastFocused = null;

    // A single image needs no navigation affordances.
    if (isSingle) {
        prevButton?.setAttribute('hidden', '');
        nextButton?.setAttribute('hidden', '');
    }

    const setScrollLock = (locked) => {
        document.documentElement.classList.toggle(SCROLL_LOCK_CLASS, locked);
        // Lenis drives desktop scrolling; overflow:hidden alone won't stop it.
        // getLenis() returns null on mobile/reduced-motion, where the CSS
        // class alone is enough.
        const lenis = getLenis();
        if (locked) {
            lenis?.stop();
        } else {
            lenis?.start();
        }
    };

    const render = (index) => {
        currentIndex = (index + sources.length) % sources.length;
        image.src = sources[currentIndex];
        if (counter) {
            counter.textContent = isSingle ? '' : `${currentIndex + 1} / ${sources.length}`;
        }
    };

    const show = (index) => {
        if (context.prefersReducedMotion) {
            render(index);
            return;
        }
        gsap.to(image, {
            opacity: 0,
            duration: IMAGE_CROSSFADE_S / 2,
            ease: 'power2.in',
            onComplete: () => {
                render(index);
                gsap.to(image, { opacity: 1, duration: IMAGE_CROSSFADE_S, ease: 'power2.out' });
            },
        });
    };

    const open = (index) => {
        lastFocused = document.activeElement;
        render(index);
        lightbox.hidden = false;
        setScrollLock(true);

        if (context.prefersReducedMotion) {
            gsap.set(lightbox, { opacity: 1 });
            gsap.set(image, { opacity: 1, scale: 1 });
        } else {
            gsap.fromTo(lightbox, { opacity: 0 }, { opacity: 1, duration: OVERLAY_FADE_S, ease: 'power2.out' });
            gsap.fromTo(image, { opacity: 0, scale: 1.04 }, { opacity: 1, scale: 1, duration: OVERLAY_FADE_S, ease: 'power2.out' });
        }

        closeButton?.focus();
    };

    const finishClose = () => {
        lightbox.hidden = true;
        image.src = '';
        setScrollLock(false);
        lastFocused?.focus();
    };

    const close = () => {
        if (lightbox.hidden) {
            return;
        }
        if (context.prefersReducedMotion) {
            finishClose();
            return;
        }
        gsap.to(lightbox, { opacity: 0, duration: OVERLAY_FADE_S, ease: 'power2.in', onComplete: finishClose });
    };

    triggers.forEach((trigger, index) => {
        trigger.addEventListener('click', () => open(index));
    });

    closeButton?.addEventListener('click', close);
    prevButton?.addEventListener('click', () => show(currentIndex - 1));
    nextButton?.addEventListener('click', () => show(currentIndex + 1));

    // Click-outside: only the backdrop itself, never the image or the controls.
    lightbox.addEventListener('click', (event) => {
        if (event.target === lightbox) {
            close();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (lightbox.hidden) {
            return;
        }
        if (event.key === 'Escape') {
            close();
            return;
        }
        if (isSingle) {
            return;
        }
        // Arrow semantics follow the visual layout, which RTL mirrors.
        const isRtl = document.documentElement.dir === 'rtl';
        if (event.key === 'ArrowLeft') {
            show(currentIndex + (isRtl ? 1 : -1));
        } else if (event.key === 'ArrowRight') {
            show(currentIndex + (isRtl ? -1 : 1));
        }
    });
}
