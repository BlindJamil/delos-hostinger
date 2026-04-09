import { qsa } from './runtime.js';

export function initInteractiveEffects(context) {
    initButtonRipples(context);

    if (context.prefersReducedMotion || !context.hasFinePointer || !context.supportsHover) {
        return;
    }

    if (window.innerWidth >= 1024) {
        initCustomCursor(context);
    }

    initMagneticButtons();
    initTiltCards();
}

function initButtonRipples(context) {
    if (context.prefersReducedMotion || !context.hasFinePointer) {
        return;
    }

    qsa('.btn-ripple').forEach((button) => {
        button.addEventListener('mousemove', (event) => {
            const bounds = button.getBoundingClientRect();
            const x = ((event.clientX - bounds.left) / bounds.width) * 100;
            const y = ((event.clientY - bounds.top) / bounds.height) * 100;

            button.style.setProperty('--ripple-x', `${x}%`);
            button.style.setProperty('--ripple-y', `${y}%`);
        }, { passive: true });
    });
}

function initCustomCursor(context) {
    context.body.classList.add('has-custom-cursor');

    const cursor = document.createElement('div');
    cursor.className = 'custom-cursor';
    cursor.setAttribute('aria-hidden', 'true');
    context.body.appendChild(cursor);

    let pointerX = 0;
    let pointerY = 0;
    let cursorX = 0;
    let cursorY = 0;
    let cursorFrame = null;

    const hoverTargets = 'a, button, .card-tilt, .img-hover-zoom, .employee-card, .brand-strip-item';

    const renderCursor = () => {
        cursorX += (pointerX - cursorX) * 0.18;
        cursorY += (pointerY - cursorY) * 0.18;
        cursor.style.left = `${cursorX}px`;
        cursor.style.top = `${cursorY}px`;

        if (Math.abs(pointerX - cursorX) > 0.5 || Math.abs(pointerY - cursorY) > 0.5) {
            cursorFrame = requestAnimationFrame(renderCursor);
            return;
        }

        cursorFrame = null;
    };

    document.addEventListener('mousemove', (event) => {
        pointerX = event.clientX;
        pointerY = event.clientY;
        cursor.classList.add('is-visible');

        if (!cursorFrame) {
            cursorFrame = requestAnimationFrame(renderCursor);
        }
    }, { passive: true });

    document.addEventListener('mouseover', (event) => {
        if (event.target.closest(hoverTargets)) {
            cursor.classList.add('is-hover');
        }
    });

    document.addEventListener('mouseout', (event) => {
        if (event.target.closest(hoverTargets)) {
            cursor.classList.remove('is-hover');
        }
    });

    document.addEventListener('mousedown', () => cursor.classList.add('is-click'));
    document.addEventListener('mouseup', () => cursor.classList.remove('is-click'));
}

function initMagneticButtons() {
    qsa('.magnetic-btn').forEach((button) => {
        let targetX = 0;
        let targetY = 0;
        let currentX = 0;
        let currentY = 0;
        let hovering = false;
        let frame = null;

        const lerp = (from, to, factor) => from + (to - from) * factor;

        const animate = () => {
            const factor = hovering ? 0.15 : 0.08;
            currentX = lerp(currentX, targetX, factor);
            currentY = lerp(currentY, targetY, factor);

            button.style.transform = `translate(${currentX}px, ${currentY}px)`;

            if (
                Math.abs(currentX - targetX) > 0.1 ||
                Math.abs(currentY - targetY) > 0.1 ||
                hovering ||
                Math.abs(currentX) > 0.1 ||
                Math.abs(currentY) > 0.1
            ) {
                frame = requestAnimationFrame(animate);
                return;
            }

            button.style.transform = '';
            frame = null;
        };

        button.addEventListener('mouseenter', () => {
            hovering = true;
            if (!frame) {
                frame = requestAnimationFrame(animate);
            }
        });

        button.addEventListener('mousemove', (event) => {
            const bounds = button.getBoundingClientRect();
            targetX = (event.clientX - bounds.left - bounds.width / 2) * 0.25;
            targetY = (event.clientY - bounds.top - bounds.height / 2) * 0.25;
        });

        button.addEventListener('mouseleave', () => {
            hovering = false;
            targetX = 0;
            targetY = 0;

            if (!frame) {
                frame = requestAnimationFrame(animate);
            }
        });
    });
}

function initTiltCards() {
    qsa('.card-tilt').forEach((card) => {
        let frame = null;

        card.addEventListener('mousemove', (event) => {
            if (frame) {
                return;
            }

            frame = requestAnimationFrame(() => {
                const bounds = card.getBoundingClientRect();
                const rotateX = ((event.clientY - bounds.top - bounds.height / 2) / bounds.height) * -8;
                const rotateY = ((event.clientX - bounds.left - bounds.width / 2) / bounds.width) * 8;

                card.style.transform = `perspective(800px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-4px)`;
                card.style.transition = 'transform var(--motion-duration-fast) ease-out, box-shadow var(--motion-duration-base) var(--motion-ease-standard)';
                frame = null;
            });
        }, { passive: true });

        card.addEventListener('mouseleave', () => {
            if (frame) {
                cancelAnimationFrame(frame);
                frame = null;
            }

            card.style.transform = '';
            card.style.transition = 'transform var(--motion-duration-slow) var(--motion-ease-emphatic), box-shadow var(--motion-duration-slow) var(--motion-ease-emphatic)';
        });
    });
}
