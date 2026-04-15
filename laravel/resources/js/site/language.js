/**
 * Language switcher client-side behavior.
 *
 * Persists the user's language choice to a cookie and localStorage the
 * moment they click a switcher surface (desktop dropdown, mobile drawer,
 * footer link, floating globe), so the choice survives the immediate
 * navigation even before the server's Set-Cookie header is processed.
 *
 * The server-side SetLocale middleware refreshes the same cookie on every
 * request; client + server agree on format (plaintext, see bootstrap/app.php
 * encryptCookies exception list).
 */

const COOKIE_NAME = 'delos_locale';
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
        window.localStorage?.setItem(STORAGE_KEY, locale);
    } catch (_) {
        // Ignore storage errors (cookies disabled, private mode, etc.)
    }
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

    // Desktop dropdown toggle — uses inline style manipulation instead of
    // CSS class toggling to avoid Tailwind specificity conflicts.
    const dropdownToggle = document.querySelector('[data-lang-dropdown-toggle]');
    const dropdownMenu = document.querySelector('[data-lang-dropdown-menu]');
    if (dropdownToggle && dropdownMenu) {
        let isOpen = false;

        const setOpen = (open) => {
            isOpen = open;
            dropdownToggle.setAttribute('aria-expanded', String(open));

            // Position the dropdown using fixed positioning + getBoundingClientRect
            // to bypass the containing-block issue caused by backdrop-filter on #site-header
            if (open) {
                const rect = dropdownToggle.getBoundingClientRect();
                dropdownMenu.style.top = (rect.bottom + 8) + 'px';
                dropdownMenu.style.right = (window.innerWidth - rect.right) + 'px';
                dropdownMenu.style.left = 'auto';
            }

            dropdownMenu.style.opacity = open ? '1' : '0';
            dropdownMenu.style.visibility = open ? 'visible' : 'hidden';
            dropdownMenu.style.transform = open ? 'translateY(0)' : 'translateY(-4px)';
            dropdownMenu.style.pointerEvents = open ? 'auto' : 'none';
        };

        dropdownToggle.addEventListener('click', (event) => {
            event.stopPropagation();
            setOpen(!isOpen);
        });

        document.addEventListener('click', (event) => {
            if (isOpen && !dropdownMenu.contains(event.target) && event.target !== dropdownToggle) {
                setOpen(false);
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && isOpen) {
                setOpen(false);
            }
        });
    }
}
