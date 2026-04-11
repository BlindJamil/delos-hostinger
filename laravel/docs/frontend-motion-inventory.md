# Frontend Motion Inventory

## Page roots
- `home`: hero load motion, video overlay, grouped reveals, counters, employee showcase, marquee, CTA.
- `about`: hero load motion, grouped text/image reveals, counters, workflow cards, quote block.
- `services`: hero load motion, service rows, CTA.
- `projects`: hero load motion, project filters, grid reveals, stats, CTA.
- `brands`: hero load motion, intro reveal, brand rows, CTA.
- `branches`: hero load motion, showroom heading and card reveals.
- `contact`: hero load motion, consultation copy, form reveal.

## Runtime modules
- `viewport.js`: real viewport height variable for mobile browser chrome.
- `navigation.js`: page loader gate, header scroll state, mobile menu, same-page anchors, project filters, absolute-link page transitions.
- `interactions.js`: ripple, custom cursor, magnetic buttons, tilt cards.
- `media.js`: home-page video overlay and playback controls.
- `motion.js`: reduced-motion gate, grouped reveal observer, hero motion, counters, desktop employee showcase pin, Lenis + ScrollTrigger integration.

## Current asset policy
- Runtime asset integrity is validated against rendered HTML for all public routes.
- Source pages must reference real files already present in `public/images` or `public/videos`.
- The Hostinger mirror receives synced `images`, `videos`, and built assets from the source repo.

## Removed legacy contracts
- `.gsap-el`
- `.gsap-line`
- `.gsap-section`
- `.reveal`
- `data-reveal`
- `data-counter`
