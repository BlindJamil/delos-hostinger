# Frontend Motion Spec

## Canonical contract
- `data-page` on `<body>` is the page identity source for frontend modules.
- `data-motion` is the only reveal hook for load and scroll entrances.
- Allowed reveal values: `fade`, `fade-up`, `slide-left`, `slide-right`, `slide-up`, `scale-in`, `split-line`.
- `data-motion-group` applies deterministic stagger to sibling motion targets.
- `data-motion-line` is the only line-draw hook.
- `data-motion-counter` is the only counter hook.
- `data-page-transition` marks the transition overlay.
- `data-motion-hero`, `data-hero-parallax`, and `data-hero-fade-out` scope hero-only behavior.

## Standards
- New motion must be opt-in and scoped to an explicit root. No module may warn when its root is absent.
- Motion must degrade to a fully readable final state when JavaScript is unavailable.
- Reduced-motion mode is functional, not cosmetic: no marquee, loader, parallax, cursor, magnetic motion, tilt, transition gating, smooth scroll, or counter animation.
- Same-origin absolute links are transition-eligible; hash-only, modified, external, download, `mailto:`, and `tel:` links are not.
- Scroll-driven motion should prefer CSS transitions plus `IntersectionObserver`; use GSAP only where native/CSS approaches are materially worse.

## Timing tokens
- Standard easing: `--motion-ease-standard`
- Emphasized easing: `--motion-ease-emphatic`
- Overlay easing: `--motion-ease-overlay`
- Durations: `--motion-duration-fast`, `--motion-duration-base`, `--motion-duration-slow`, `--motion-duration-xl`
- Distances: `--motion-distance-xs`, `--motion-distance-sm`, `--motion-distance-md`, `--motion-distance-lg`

## Review checklist
- No legacy `.gsap-*`, `.reveal`, `data-reveal`, or `data-counter` hooks remain in templates or scripts.
- Public routes produce zero console warnings/errors and zero missing assets.
- Every motion target has a clear owner: hero, grouped reveal, line, counter, or interaction effect.
- Mobile uses native scrolling for heavy sections and never enables desktop-only cursor affordances.
