import { createSiteContext } from './runtime.js';
import { initInteractiveEffects } from './interactions.js';
import { initMediaControls } from './media.js';
import {
    initAnchorNavigation,
    initHeader,
    initMobileMenu,
    initPageLoader,
    initPageTransitions,
    initProjectFilters,
} from './navigation.js';
import { initMotion } from './motion.js';
import { initViewportHeight } from './viewport.js';

export function initSite() {
    const context = createSiteContext();

    context.documentElement.classList.toggle('reduced-motion', context.prefersReducedMotion);
    context.body.classList.toggle('reduced-motion', context.prefersReducedMotion);

    if (context.prefersReducedMotion) {
        context.documentElement.dataset.scrollMode = 'native';
    }

    initViewportHeight(context);
    initHeader();
    initMobileMenu();
    initAnchorNavigation(context);
    initPageTransitions(context);
    initProjectFilters();
    initMediaControls(context);

    initPageLoader(context, () => {
        initInteractiveEffects(context);
        initMotion(context);
    });
}
