const REDUCED_MOTION_QUERY = '(prefers-reduced-motion: reduce)';
const TRANSITION_STORAGE_KEY = 'delos:page-transition';

export function qs(selector, root = document) {
    return root.querySelector(selector);
}

export function qsa(selector, root = document) {
    return Array.from(root.querySelectorAll(selector));
}

export function createSiteContext() {
    const reducedMotionMedia = window.matchMedia(REDUCED_MOTION_QUERY);
    const hoverMedia = window.matchMedia('(hover: hover)');
    const finePointerMedia = window.matchMedia('(pointer: fine)');
    const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;

    return {
        body: document.body,
        documentElement: document.documentElement,
        finePointerMedia,
        hoverMedia,
        isTouchDevice,
        page: document.body.dataset.page ?? 'unknown',
        reducedMotionMedia,
        transitionStorageKey: TRANSITION_STORAGE_KEY,
        get hasFinePointer() {
            return this.finePointerMedia.matches;
        },
        get prefersReducedMotion() {
            return this.reducedMotionMedia.matches;
        },
        get supportsHover() {
            return this.hoverMedia.matches;
        },
    };
}
