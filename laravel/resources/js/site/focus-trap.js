/**
 * Minimal focus trap. Constrains Tab / Shift+Tab cycling to focusable
 * descendants of `container`. Returns a release function.
 *
 * Usage:
 *   const release = trapFocus(modalElement);
 *   // later, on close:
 *   release();
 */

const FOCUSABLE_SELECTOR = [
    'a[href]',
    'button:not([disabled])',
    'textarea:not([disabled])',
    'input:not([disabled])',
    'select:not([disabled])',
    '[tabindex]:not([tabindex="-1"])',
].join(', ');

export function trapFocus(container) {
    if (!container) return () => {};

    const getFocusable = () =>
        Array.from(container.querySelectorAll(FOCUSABLE_SELECTOR))
            .filter((el) => !el.hasAttribute('inert') && el.offsetParent !== null);

    const handleKeydown = (event) => {
        if (event.key !== 'Tab') return;
        const focusable = getFocusable();
        if (focusable.length === 0) {
            event.preventDefault();
            return;
        }

        const first = focusable[0];
        const last = focusable[focusable.length - 1];
        const active = document.activeElement;

        if (event.shiftKey) {
            if (active === first || !container.contains(active)) {
                event.preventDefault();
                last.focus();
            }
        } else {
            if (active === last || !container.contains(active)) {
                event.preventDefault();
                first.focus();
            }
        }
    };

    // Auto-focus first focusable element on trap start
    queueMicrotask(() => {
        const focusable = getFocusable();
        if (focusable.length > 0 && !container.contains(document.activeElement)) {
            focusable[0].focus();
        }
    });

    container.addEventListener('keydown', handleKeydown);

    return () => {
        container.removeEventListener('keydown', handleKeydown);
    };
}
