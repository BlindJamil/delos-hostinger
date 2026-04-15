import './bootstrap';
import Alpine from 'alpinejs';
import { initSite } from './site/index.js';

// Expose Alpine globally so Blade components (admin bar, language globe,
// rich-text editor, admin forms) with x-data/x-show/x-on can hydrate. Must
// be started before initSite() since some Alpine-driven elements render
// at page load.
window.Alpine = Alpine;
Alpine.start();

initSite();
