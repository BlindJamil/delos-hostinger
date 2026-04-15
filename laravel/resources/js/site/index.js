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
import { initIraqMap } from './iraq-map.js';
import { initLanguageGlobe } from './language-globe.js';
import { initViewportHeight } from './viewport.js';
import { initLanguageSwitcher } from './language.js';

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
    initLanguageSwitcher();
    initLanguageGlobe();

    initPageLoader(context, () => {
        initInteractiveEffects(context);
        initMotion(context);
        // Iraq map is a no-op outside /branches; safe to call universally.
        initIraqMap(context);
    });
}
