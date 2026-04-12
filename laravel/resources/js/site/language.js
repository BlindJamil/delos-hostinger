/**
 * Language picker + switcher client-side behavior.
 *
 * Responsibilities:
 *   - First-visit modal: focus trap, body scroll lock, click-to-navigate
 *     with cookie+localStorage persistence BEFORE navigation.
 *   - Persistent switcher surfaces (desktop dropdown / mobile drawer /
 *     footer links): set cookies on click, then navigate.
 *
 * The cookie is also set server-side by SetLocale middleware on every
 * request, but we set it client-side too so the choice survives the
 * immediate click even if the browser hasn't yet processed the Set-Cookie
 * header from the subsequent navigation.
 */

import { trapFocus } from './focus-trap.js';

const COOKIE_NAME = 'delos_locale';
const SEEN_COOKIE_NAME = 'delos_locale_seen';
const STORAGE_KEY = 'delos_locale';
const ONE_YEAR_DAYS = 365;

function setCookie(name, value, days) {
    const maxAge = days * 24 * 60 * 60;
    const secure = window.location.protocol === 'https:' ? '; Secure' : '';
    document.cookie = `${name}=${encodeURIComponent(value)}; Max-Age=${maxAge}; Path=/; SameSite=Lax${secure}`;
}

function persistLocaleChoice(locale) {
    try {
        setCookie(COOKIE_NAME, locale, ONE_YEAR_DAYS);
        setCookie(SEEN_COOKIE_NAME, '1', ONE_YEAR_DAYS);
        window.localStorage?.setItem(STORAGE_KEY, locale);
    } catch (_) {
        // Ignore storage errors (cookies disabled, private mode, etc.)
    }
}

export function initLanguagePicker() {
    const picker = document.getElementById('lang-picker');
    if (!picker) return;

    // Lock body scroll while picker is open
    const previousOverflow = document.body.style.overflow;
    document.body.style.overflow = 'hidden';

    // Focus trap
    const releaseFocusTrap = trapFocus(picker);

    // Intercept card clicks to persist locale before navigation
    picker.querySelectorAll('[data-locale]').forEach((card) => {
        card.addEventListener('click', (event) => {
            const locale = card.getAttribute('data-locale');
            if (!locale) return;
            persistLocaleChoice(locale);
            // Let the click propagate normally — the <a href="..."> will navigate.
            // We don't preventDefault; we just made sure cookies are set first.
        });
    });

    // Block ESC and click-outside-to-dismiss per design decision:
    // "No close button, must choose" (user confirmed in plan).
    picker.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            event.preventDefault();
        }
    });

    // Clean up on page hide (for back/forward cache)
    window.addEventListener('pagehide', () => {
        document.body.style.overflow = previousOverflow;
        releaseFocusTrap();
    }, { once: true });
}

export function initLanguageSwitcher() {
    document.querySelectorAll('[data-language-switch]').forEach((link) => {
        link.addEventListener('click', () => {
            const locale = link.getAttribute('data-language-switch');
            if (!locale) return;
            persistLocaleChoice(locale);
            // Let the anchor navigate
        });
    });

    // Desktop dropdown toggle
    const dropdownToggle = document.querySelector('[data-lang-dropdown-toggle]');
    const dropdownMenu = document.querySelector('[data-lang-dropdown-menu]');
    if (dropdownToggle && dropdownMenu) {
        const setOpen = (open) => {
            dropdownMenu.classList.toggle('is-open', open);
            dropdownToggle.setAttribute('aria-expanded', String(open));
        };

        dropdownToggle.addEventListener('click', (event) => {
            event.stopPropagation();
            const isOpen = dropdownMenu.classList.contains('is-open');
            setOpen(!isOpen);
        });

        // Close on outside click or ESC
        document.addEventListener('click', (event) => {
            if (!dropdownMenu.contains(event.target) && event.target !== dropdownToggle) {
                setOpen(false);
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                setOpen(false);
            }
        });
    }
}
