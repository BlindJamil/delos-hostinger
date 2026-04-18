import { qs, qsa } from './runtime.js';

const TRANSITION_DURATION_MS = 600;
const LOADER_HIDE_DELAY_MS = 1400;
const LOADER_BOOT_DELAY_MS = 300;

export function initPageLoader(context, onReady) {
    const pageLoader = qs('[data-page-loader]');
    let booted = false;

    const boot = () => {
        if (booted) {
            return;
        }

        booted = true;
        onReady();
    };

    const scheduleBoot = (delay = 50) => {
        window.setTimeout(boot, delay);
    };

    if (!pageLoader || context.prefersReducedMotion) {
        if (pageLoader) {
            pageLoader.classList.add('is-loaded');
        }

        if (document.readyState === 'complete') {
            scheduleBoot();
        } else {
            window.addEventListener('load', () => scheduleBoot(), { once: true });
        }

        return;
    }

    context.documentElement.classList.add('has-loader');

    const finishLoader = () => {
        window.setTimeout(() => {
            pageLoader.classList.add('is-loaded');
            context.documentElement.classList.remove('has-loader');
            scheduleBoot(LOADER_BOOT_DELAY_MS);
        }, LOADER_HIDE_DELAY_MS);
    };

    if (document.readyState === 'complete') {
        finishLoader();
    } else {
        window.addEventListener('load', finishLoader, { once: true });
    }
}

export function initPageTransitions(context) {
    // Page transition overlay disabled: it caused a brief blank flash
    // when navigating between pages and didn't add real value. Links
    // now use the browser's standard navigation. The overlay element
    // remains in the DOM (used by admin-edit-pill z-index ordering)
    // but is never activated. Stale sessionStorage state is cleared
    // in case a user is mid-navigation when this deploys.
    const overlay = qs('[data-page-transition]');
    if (overlay) {
        overlay.classList.remove('is-active', 'is-exiting');
    }
    sessionStorage.removeItem(context.transitionStorageKey);
}

export function initHeader() {
    const siteHeader = qs('#site-header');
    if (!siteHeader) {
        return;
    }

    let scheduled = false;

    const updateHeader = () => {
        siteHeader.classList.toggle('header-scrolled', window.scrollY > 50);
        scheduled = false;
    };

    const handleScroll = () => {
        if (scheduled) {
            return;
        }

        scheduled = true;
        requestAnimationFrame(updateHeader);
    };

    updateHeader();
    window.addEventListener('scroll', handleScroll, { passive: true });
}

export function initMobileMenu() {
    const mobileButton = qs('#mobile-menu-btn');
    const mobileMenu = qs('#mobile-menu');
    if (!mobileButton || !mobileMenu) {
        return;
    }

    const setMenuState = (open) => {
        mobileMenu.classList.toggle('menu-open', open);
        mobileButton.setAttribute('aria-expanded', String(open));

        qsa('a', mobileMenu).forEach((link, index) => {
            link.style.transitionDelay = open ? `${0.1 + index * 0.08}s` : '';
        });
    };

    mobileButton.addEventListener('click', () => {
        const isOpen = mobileMenu.classList.contains('menu-open');
        setMenuState(!isOpen);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && mobileMenu.classList.contains('menu-open')) {
            setMenuState(false);
            mobileButton.focus();
        }
    });

    qsa('a', mobileMenu).forEach((link) => {
        link.addEventListener('click', () => setMenuState(false));
    });
}

export function initAnchorNavigation(context) {
    qsa('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener('click', (event) => {
            const href = anchor.getAttribute('href');
            if (!href || href === '#') {
                return;
            }

            const target = qs(href);
            if (!target) {
                return;
            }

            event.preventDefault();
            target.scrollIntoView({
                behavior: context.prefersReducedMotion ? 'auto' : 'smooth',
                block: 'start',
            });
        });
    });
}

export function initProjectFilters() {
    const buttons = qsa('.project-filter');
    const items = qsa('.project-item');
    if (!buttons.length || !items.length) {
        return;
    }

    const setFilter = (value) => {
        buttons.forEach((button) => {
            const active = button.dataset.filter === value;
            button.setAttribute('aria-pressed', String(active));
            button.classList.toggle('bg-delos-dark', active);
            button.classList.toggle('text-delos-cream', active);
            button.classList.toggle('border-delos-dark', active);
            button.classList.toggle('bg-transparent', !active);
            button.classList.toggle('text-delos-muted', !active);
            button.classList.toggle('border-delos-dark/20', !active);
        });

        items.forEach((item) => {
            const matches = value === 'all' || item.dataset.type === value;
            item.hidden = !matches;
            item.setAttribute('aria-hidden', String(!matches));
        });
    };

    buttons.forEach((button) => {
        button.addEventListener('click', () => setFilter(button.dataset.filter ?? 'all'));
    });

    const initialFilter = buttons.find((button) => button.getAttribute('aria-pressed') === 'true')?.dataset.filter ?? 'all';
    setFilter(initialFilter);
}

function hasModifierKey(event) {
    return event.metaKey || event.ctrlKey || event.shiftKey || event.altKey;
}

function shouldTransitionLink(link) {
    const href = link.getAttribute('href');
    if (!href) {
        return false;
    }

    if (
        href.startsWith('#') ||
        href.startsWith('mailto:') ||
        href.startsWith('tel:') ||
        link.target === '_blank' ||
        link.hasAttribute('download') ||
        link.dataset.pageTransition === 'false'
    ) {
        return false;
    }

    const destination = new URL(link.href, window.location.href);
    return destination.protocol === 'http:' || destination.protocol === 'https:';
}
