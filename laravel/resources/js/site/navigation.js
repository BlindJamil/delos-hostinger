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

const PROJECTS_PAGE_SIZE = 15;

/**
 * Type filter + pagination for the projects grid, sharing one `hidden`
 * state machine since both act on the same .project-item elements.
 *
 * Pagination only ever applies to the unfiltered "All" tab — the numbered
 * pager (rendered server-side in projects.blade.php, present only when
 * there's more than one page) is hidden whenever a specific type is
 * selected, and every match for that type is shown at once. This avoids
 * a type filter appearing broken/empty just because its matches happen to
 * fall on a page the visitor isn't currently viewing.
 */
export function initProjectFilters() {
    const buttons = qsa('.project-filter');
    const items = qsa('.project-item');
    if (!buttons.length || !items.length) {
        return;
    }

    const pager = qs('#projects-pager');
    const pageButtons = pager ? qsa('[data-pager-page]', pager) : [];
    const prevButton = pager ? qs('[data-pager-prev]', pager) : null;
    const nextButton = pager ? qs('[data-pager-next]', pager) : null;
    const pageCount = pageButtons.length;

    let currentFilter = 'all';
    let currentPage = 1;

    const render = () => {
        const isAllFilter = currentFilter === 'all';
        let matchIndex = 0;

        items.forEach((item) => {
            const matchesFilter = isAllFilter || item.dataset.type === currentFilter;
            if (!matchesFilter) {
                item.hidden = true;
                item.setAttribute('aria-hidden', 'true');
                return;
            }

            const matchesPage = !isAllFilter
                || Math.floor(matchIndex / PROJECTS_PAGE_SIZE) + 1 === currentPage;
            if (isAllFilter) {
                matchIndex++;
            }

            const wasHidden = item.hidden;
            item.hidden = !matchesPage;
            item.setAttribute('aria-hidden', String(!matchesPage));

            // An item that was hidden (by the filter or a different page)
            // and is now being shown may never get GSAP's scroll-triggered
            // fade-in — motion.js deliberately skips creating a trigger for
            // elements that are hidden when it runs. Apply the same "fully
            // revealed" class its own reveal animation would end on, so it
            // never gets stuck at the CSS-level opacity:0 starting state.
            if (wasHidden && matchesPage) {
                item.classList.add('is-visible');
            }
        });

        if (pager) {
            pager.hidden = !isAllFilter || pageCount <= 1;
        }
        pageButtons.forEach((button) => {
            const isCurrent = Number(button.dataset.pagerPage) === currentPage;
            button.classList.toggle('is-active', isCurrent);
            button.setAttribute('aria-current', isCurrent ? 'page' : 'false');
        });
        if (prevButton) {
            prevButton.disabled = currentPage <= 1;
        }
        if (nextButton) {
            nextButton.disabled = currentPage >= pageCount;
        }
    };

    const setFilter = (value) => {
        currentFilter = value;
        currentPage = 1;

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

        render();
    };

    const goToPage = (page) => {
        if (page < 1 || page > pageCount || page === currentPage) {
            return;
        }
        currentPage = page;
        render();
        qs('#projects-grid')?.scrollIntoView({ block: 'start' });
    };

    buttons.forEach((button) => {
        button.addEventListener('click', () => setFilter(button.dataset.filter ?? 'all'));
    });

    pageButtons.forEach((button) => {
        button.addEventListener('click', () => goToPage(Number(button.dataset.pagerPage)));
    });
    prevButton?.addEventListener('click', () => goToPage(currentPage - 1));
    nextButton?.addEventListener('click', () => goToPage(currentPage + 1));

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
