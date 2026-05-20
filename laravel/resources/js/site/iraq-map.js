import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

import { qs, qsa } from './runtime.js';

/**
 * Animates the editorial Iraq map on the /branches page:
 *   1. Country outline draws itself along its path (GSAP stroke-dashoffset).
 *   2. Pins pop in with a micro-bounce, staggered.
 *   3. Hovering a pin highlights the matching branch card below (and v.v.).
 *
 * No-ops gracefully on pages without the map, on touch devices (animations
 * stay but interactions degrade to taps), and when reduced-motion is set.
 */
export function initIraqMap(context) {
    const map = qs('[data-iraq-map]');
    if (!map) {
        return; // not on the branches page
    }

    const outline = qs('.iraq-map__outline', map);
    const pinGroups = qsa('.iraq-map__pin-group', map);
    // Only the actual branch cards below the map — must NOT include the
    // pin groups themselves (they also carry data-branch-key, and a naive
    // [data-branch-key] selector would match them first since they appear
    // earlier in the DOM, causing pin-click to "scroll" to the pin's own
    // position rather than the matching card below).
    const cards = qsa('.branch-card[data-branch-key]', document);

    // --- Card ↔ pin two-way hover linking ----------------------
    const pairings = new Map(); // branchKey -> { pin, card }
    pinGroups.forEach((pin) => {
        const key = pin.dataset.branchKey;
        if (!key) return;
        const card = cards.find((c) => c.dataset.branchKey === key);
        if (!card) return;
        pairings.set(key, { pin, card });

        // Hover pin → highlight card AND smooth-scroll into view
        pin.addEventListener('mouseenter', () => {
            pin.classList.add('is-active');
            card.classList.add('is-active');
        });
        pin.addEventListener('mouseleave', () => {
            pin.classList.remove('is-active');
            card.classList.remove('is-active');
        });

        // Pin <a> click → scroll to the matching card below. We do the
        // tween ourselves (instead of scrollIntoView({behavior:'smooth'}))
        // because native smooth-scroll has no duration knob and finishes
        // in ~300ms regardless of distance — too snappy for the feel of
        // a luxury site. ~1.4s with an ease-out cubic gives a deliberate
        // pace that lets the eye follow the page.
        const link = qs('.iraq-map__pin-link', pin);
        if (link) {
            link.addEventListener('click', (event) => {
                event.preventDefault();

                const rect = card.getBoundingClientRect();
                // scroll-margin-top on .branch-card handles the fixed-nav
                // offset visually, but for the actual scroll target we
                // subtract a small offset so the heading sits a little
                // below the nav rather than glued to it.
                const headerOffset = 100;
                const targetY = rect.top + window.scrollY - headerOffset;

                if (context.prefersReducedMotion) {
                    window.scrollTo(0, targetY);
                } else {
                    smoothScrollTo(targetY, 1400);
                }

                // Match URL hash without triggering jump
                if (history.replaceState) {
                    history.replaceState(null, '', `#branch-${key}`);
                }
                // Flash the card briefly so the user sees confirmation.
                card.classList.add('is-active');
                window.setTimeout(() => card.classList.remove('is-active'), 1500);
            });
        }

        // Hover card → highlight pin (no scroll — would loop)
        card.addEventListener('mouseenter', () => pin.classList.add('is-active'));
        card.addEventListener('mouseleave', () => pin.classList.remove('is-active'));
    });

    // --- Reveal animations (skipped under prefers-reduced-motion) ---

    if (context.prefersReducedMotion) {
        // Static reveal: just expose the cartouche/compass/rivers via the
        // is-revealed class. Outline is already drawn (no dashoffset set).
        map.classList.add('is-revealed');
        return;
    }

    // Prep the outline for the draw-on animation. Path length is computed
    // here (after the SVG has laid out) so we get an accurate value.
    if (outline) {
        const len = outline.getTotalLength();
        gsap.set(outline, {
            strokeDasharray: len,
            strokeDashoffset: len,
        });
    }

    // Prep pins as invisible — GSAP will pop them in.
    pinGroups.forEach((g) => {
        gsap.set(g, { opacity: 0, scale: 0, transformOrigin: 'center' });
    });

    // Trigger when ~30% of the map is in view. Once only — luxury sites
    // don't replay an entrance animation each scroll.
    ScrollTrigger.create({
        trigger: map,
        start: 'top 70%',
        once: true,
        onEnter: () => {
            const tl = gsap.timeline();

            // 1. Outline draws
            if (outline) {
                tl.to(outline, {
                    strokeDashoffset: 0,
                    duration: 2.2,
                    ease: 'power2.inOut',
                });
            }

            // 2. Halo + cartouche reveal via class (CSS handles transition)
            tl.add(() => map.classList.add('is-revealed'), '-=1.2');

            // 3. Pins pop in, staggered — flagship first then by sort order
            const sortedPins = [...pinGroups].sort((a, b) => {
                const aFlag = qs('.iraq-map__pin--flagship', a) ? 1 : 0;
                const bFlag = qs('.iraq-map__pin--flagship', b) ? 1 : 0;
                if (aFlag !== bFlag) return bFlag - aFlag;
                return Number(a.dataset.pinIndex) - Number(b.dataset.pinIndex);
            });

            tl.to(sortedPins, {
                opacity: 1,
                scale: 1,
                duration: 0.7,
                stagger: 0.18,
                ease: 'back.out(1.7)',
            }, '-=0.6');
        },
    });
}

/**
 * Animate window scroll to a target Y over a given duration with
 * an ease-out-cubic curve. Cancels any in-flight scroll if the user
 * triggers a new one.
 */
let pendingScrollFrame = null;
function smoothScrollTo(targetY, duration) {
    if (pendingScrollFrame !== null) {
        cancelAnimationFrame(pendingScrollFrame);
    }
    const startY = window.scrollY;
    const distance = targetY - startY;
    const startTime = performance.now();

    function step(now) {
        const elapsed = now - startTime;
        const progress = Math.min(elapsed / duration, 1);
        // ease-out cubic — starts fast, glides to a stop
        const eased = 1 - Math.pow(1 - progress, 3);
        window.scrollTo(0, startY + distance * eased);
        if (progress < 1) {
            pendingScrollFrame = requestAnimationFrame(step);
        } else {
            pendingScrollFrame = null;
        }
    }
    pendingScrollFrame = requestAnimationFrame(step);
}
