<?php

/**
 * Page Content Registry
 * ─────────────────────
 * Declares which lang-file keys can be overridden by admins from the
 * /admin/page-content/{page}/edit editor. Organized as:
 *
 *   page → sections → fields
 *
 * Each field has:
 *   - key    : the dotted lang-file path (matches __('key') in blades)
 *   - label  : admin-friendly human label
 *   - type   : text | textarea | rich | url | image | video
 *
 * Pages are listed in the order they appear on the public site.
 * Sections within a page group conceptually-related fields.
 *
 * Entries that correspond to CRUD-managed models (employees, projects,
 * brands, services, branches, specific detail records) are NOT included
 * here — they're managed via their own admin sections. This registry
 * only covers static copy and media that don't have a dedicated model.
 */

return [

    // ─── HOMEPAGE ───────────────────────────────────────────
    'home' => [
        'label' => 'Homepage',
        'description' => 'Hero, about, video, featured collection, stats, and CTAs',
        'sections' => [
            'hero' => [
                'label' => 'Hero slideshow',
                'fields' => [
                    ['key' => 'home.hero.scroll_label', 'label' => 'Scroll hint label', 'type' => 'text'],
                    ['key' => 'home.hero.slides.0.img', 'label' => 'Slide 1 image', 'type' => 'image'],
                    ['key' => 'home.hero.slides.0.alt', 'label' => 'Slide 1 alt text', 'type' => 'text'],
                    ['key' => 'home.hero.slides.1.img', 'label' => 'Slide 2 image', 'type' => 'image'],
                    ['key' => 'home.hero.slides.1.alt', 'label' => 'Slide 2 alt text', 'type' => 'text'],
                    ['key' => 'home.hero.slides.2.img', 'label' => 'Slide 3 image', 'type' => 'image'],
                    ['key' => 'home.hero.slides.2.alt', 'label' => 'Slide 3 alt text', 'type' => 'text'],
                    ['key' => 'home.hero.slides.3.img', 'label' => 'Slide 4 image', 'type' => 'image'],
                    ['key' => 'home.hero.slides.3.alt', 'label' => 'Slide 4 alt text', 'type' => 'text'],
                    ['key' => 'home.hero.slides.4.img', 'label' => 'Slide 5 image', 'type' => 'image'],
                    ['key' => 'home.hero.slides.4.alt', 'label' => 'Slide 5 alt text', 'type' => 'text'],
                ],
            ],
            'about' => [
                'label' => 'About section',
                'fields' => [
                    ['key' => 'home.about.overline', 'label' => 'Overline', 'type' => 'text'],
                    ['key' => 'home.about.heading_1', 'label' => 'Heading line 1', 'type' => 'text'],
                    ['key' => 'home.about.heading_2', 'label' => 'Heading line 2', 'type' => 'text'],
                    ['key' => 'home.about.heading_accent', 'label' => 'Heading accent (gold)', 'type' => 'text'],
                    ['key' => 'home.about.body', 'label' => 'Body paragraph', 'type' => 'textarea'],
                    ['key' => 'home.about.quote', 'label' => 'Quote', 'type' => 'textarea'],
                ],
            ],
            'video' => [
                'label' => 'Brand video',
                'fields' => [
                    ['key' => 'home.video.source', 'label' => 'Video file (MP4)', 'type' => 'video'],
                    // video_source_key lets the editor render a "Capture from
                    // video" button that grabs a frame from the sibling video
                    // field at any timestamp the admin picks.
                    ['key' => 'home.video.poster', 'label' => 'Poster image (shown before play)', 'type' => 'image', 'video_source_key' => 'home.video.source'],
                    ['key' => 'home.video.overline', 'label' => 'Overline', 'type' => 'text'],
                    ['key' => 'home.video.watch', 'label' => 'Watch button label', 'type' => 'text'],
                ],
            ],
            'collection' => [
                'label' => 'Featured Collection',
                'fields' => [
                    ['key' => 'home.collection.overline', 'label' => 'Overline', 'type' => 'text'],
                    ['key' => 'home.collection.heading', 'label' => 'Heading', 'type' => 'text'],
                    // Collection items — 4 static items (titles, brands, descriptions, images)
                    ['key' => 'home.collection.items.villa_classica.num', 'label' => 'Item 1 · number', 'type' => 'text'],
                    ['key' => 'home.collection.items.villa_classica.brand', 'label' => 'Item 1 · brand', 'type' => 'text'],
                    ['key' => 'home.collection.items.villa_classica.title', 'label' => 'Item 1 · title', 'type' => 'text'],
                    ['key' => 'home.collection.items.villa_classica.desc', 'label' => 'Item 1 · description', 'type' => 'textarea'],
                    ['key' => 'home.collection.items.villa_classica.img', 'label' => 'Item 1 · image', 'type' => 'image'],
                    ['key' => 'home.collection.items.nero_contemporary.num', 'label' => 'Item 2 · number', 'type' => 'text'],
                    ['key' => 'home.collection.items.nero_contemporary.brand', 'label' => 'Item 2 · brand', 'type' => 'text'],
                    ['key' => 'home.collection.items.nero_contemporary.title', 'label' => 'Item 2 · title', 'type' => 'text'],
                    ['key' => 'home.collection.items.nero_contemporary.desc', 'label' => 'Item 2 · description', 'type' => 'textarea'],
                    ['key' => 'home.collection.items.nero_contemporary.img', 'label' => 'Item 2 · image', 'type' => 'image'],
                    ['key' => 'home.collection.items.eleganza_living.num', 'label' => 'Item 3 · number', 'type' => 'text'],
                    ['key' => 'home.collection.items.eleganza_living.brand', 'label' => 'Item 3 · brand', 'type' => 'text'],
                    ['key' => 'home.collection.items.eleganza_living.title', 'label' => 'Item 3 · title', 'type' => 'text'],
                    ['key' => 'home.collection.items.eleganza_living.desc', 'label' => 'Item 3 · description', 'type' => 'textarea'],
                    ['key' => 'home.collection.items.eleganza_living.img', 'label' => 'Item 3 · image', 'type' => 'image'],
                    ['key' => 'home.collection.items.mediterranean_living.num', 'label' => 'Item 4 · number', 'type' => 'text'],
                    ['key' => 'home.collection.items.mediterranean_living.brand', 'label' => 'Item 4 · brand', 'type' => 'text'],
                    ['key' => 'home.collection.items.mediterranean_living.title', 'label' => 'Item 4 · title', 'type' => 'text'],
                    ['key' => 'home.collection.items.mediterranean_living.desc', 'label' => 'Item 4 · description', 'type' => 'textarea'],
                    ['key' => 'home.collection.items.mediterranean_living.img', 'label' => 'Item 4 · image', 'type' => 'image'],
                ],
            ],
            'employees' => [
                'label' => 'Employees section',
                'description' => 'Section title only — manage the actual team in the Employees admin section.',
                'fields' => [
                    ['key' => 'home.employees.overline', 'label' => 'Overline', 'type' => 'text'],
                    ['key' => 'home.employees.heading', 'label' => 'Heading', 'type' => 'text'],
                    ['key' => 'home.employees.sub', 'label' => 'Subheading', 'type' => 'textarea'],
                ],
            ],
            'stats' => [
                'label' => 'Stats section',
                'fields' => [
                    ['key' => 'home.stats.overline', 'label' => 'Overline', 'type' => 'text'],
                    ['key' => 'home.stats.heading', 'label' => 'Heading', 'type' => 'text'],
                    ['key' => 'home.stats.items.0.value', 'label' => 'Stat 1 · number', 'type' => 'text'],
                    ['key' => 'home.stats.items.0.suffix', 'label' => 'Stat 1 · suffix', 'type' => 'text'],
                    ['key' => 'home.stats.items.0.label', 'label' => 'Stat 1 · label', 'type' => 'text'],
                    ['key' => 'home.stats.items.1.value', 'label' => 'Stat 2 · number', 'type' => 'text'],
                    ['key' => 'home.stats.items.1.suffix', 'label' => 'Stat 2 · suffix', 'type' => 'text'],
                    ['key' => 'home.stats.items.1.label', 'label' => 'Stat 2 · label', 'type' => 'text'],
                    ['key' => 'home.stats.items.2.value', 'label' => 'Stat 3 · number', 'type' => 'text'],
                    ['key' => 'home.stats.items.2.suffix', 'label' => 'Stat 3 · suffix', 'type' => 'text'],
                    ['key' => 'home.stats.items.2.label', 'label' => 'Stat 3 · label', 'type' => 'text'],
                    ['key' => 'home.stats.items.3.value', 'label' => 'Stat 4 · number', 'type' => 'text'],
                    ['key' => 'home.stats.items.3.suffix', 'label' => 'Stat 4 · suffix', 'type' => 'text'],
                    ['key' => 'home.stats.items.3.label', 'label' => 'Stat 4 · label', 'type' => 'text'],
                ],
            ],
            'brands_intro' => [
                'label' => 'Brands section intro',
                'description' => 'Section title only — manage the brand records in the Brands admin section.',
                'fields' => [
                    ['key' => 'home.brands.overline', 'label' => 'Overline', 'type' => 'text'],
                    ['key' => 'home.brands.heading', 'label' => 'Heading', 'type' => 'text'],
                    ['key' => 'home.brands.sub', 'label' => 'Subheading', 'type' => 'textarea'],
                ],
            ],
            'cta' => [
                'label' => 'Final CTA',
                'fields' => [
                    ['key' => 'home.cta.overline', 'label' => 'Overline', 'type' => 'text'],
                    ['key' => 'home.cta.heading', 'label' => 'Heading', 'type' => 'text'],
                ],
            ],
        ],
    ],

    // ─── ABOUT ─────────────────────────────────────────────
    'about' => [
        'label' => 'About page',
        'description' => 'Company story, philosophy, workflow, and branches mention',
        'sections' => [
            'hero' => [
                'label' => 'Hero',
                'fields' => [
                    ['key' => 'about.hero.overline', 'label' => 'Overline', 'type' => 'text'],
                    ['key' => 'about.hero.heading', 'label' => 'Heading', 'type' => 'text'],
                    ['key' => 'about.hero.heading_accent', 'label' => 'Heading accent', 'type' => 'text'],
                    ['key' => 'about.hero.sub', 'label' => 'Subheading', 'type' => 'textarea'],
                ],
            ],
            'story' => [
                'label' => 'Story section',
                'fields' => [
                    ['key' => 'about.story.overline', 'label' => 'Overline', 'type' => 'text'],
                    ['key' => 'about.story.heading', 'label' => 'Heading', 'type' => 'text'],
                    ['key' => 'about.story.paragraph_1', 'label' => 'Paragraph 1', 'type' => 'textarea'],
                    ['key' => 'about.story.paragraph_2', 'label' => 'Paragraph 2', 'type' => 'textarea'],
                ],
            ],
            'philosophy' => [
                'label' => 'Philosophy / Vision / Mission',
                'fields' => [
                    ['key' => 'about.philosophy.overline', 'label' => 'Overline', 'type' => 'text'],
                    ['key' => 'about.philosophy.heading', 'label' => 'Heading', 'type' => 'text'],
                    ['key' => 'about.philosophy.body', 'label' => 'Body', 'type' => 'textarea'],
                ],
            ],
            'direction' => [
                'label' => 'Vision / Mission / Goal cards',
                'fields' => [
                    ['key' => 'about.direction.vision.label', 'label' => 'Vision · label', 'type' => 'text'],
                    ['key' => 'about.direction.vision.heading', 'label' => 'Vision · heading', 'type' => 'text'],
                    ['key' => 'about.direction.vision.body', 'label' => 'Vision · body', 'type' => 'textarea'],
                    ['key' => 'about.direction.mission.label', 'label' => 'Mission · label', 'type' => 'text'],
                    ['key' => 'about.direction.mission.heading', 'label' => 'Mission · heading', 'type' => 'text'],
                    ['key' => 'about.direction.mission.body', 'label' => 'Mission · body', 'type' => 'textarea'],
                    ['key' => 'about.direction.goal.label', 'label' => 'Goal · label', 'type' => 'text'],
                    ['key' => 'about.direction.goal.heading', 'label' => 'Goal · heading', 'type' => 'text'],
                    ['key' => 'about.direction.goal.body', 'label' => 'Goal · body', 'type' => 'textarea'],
                ],
            ],
            'quote' => [
                'label' => 'Pull quote',
                'fields' => [
                    ['key' => 'about.quote', 'label' => 'Quote text', 'type' => 'textarea'],
                ],
            ],
        ],
    ],

    // ─── SERVICES ──────────────────────────────────────────
    'services' => [
        'label' => 'Services page',
        'description' => 'Hero + intro text only — service items are managed in the Services admin section.',
        'sections' => [
            'hero' => [
                'label' => 'Hero',
                'fields' => [
                    ['key' => 'services.hero.overline', 'label' => 'Overline', 'type' => 'text'],
                    ['key' => 'services.hero.heading_1_before', 'label' => 'Heading prefix', 'type' => 'text'],
                    ['key' => 'services.hero.heading_1_accent', 'label' => 'Heading accent', 'type' => 'text'],
                    ['key' => 'services.hero.heading_2', 'label' => 'Heading line 2', 'type' => 'text'],
                    ['key' => 'services.hero.marquee_bg_label', 'label' => 'Marquee background label', 'type' => 'text'],
                ],
            ],
            'info' => [
                'label' => 'Info row',
                'fields' => [
                    ['key' => 'services.info_row.blurb', 'label' => 'Blurb', 'type' => 'textarea'],
                    ['key' => 'services.info_row.est', 'label' => 'Est. label', 'type' => 'text'],
                ],
            ],
            'intro' => [
                'label' => 'Section intro',
                'fields' => [
                    ['key' => 'services.intro.overline', 'label' => 'Overline', 'type' => 'text'],
                    ['key' => 'services.intro.heading_1', 'label' => 'Heading line 1', 'type' => 'text'],
                    ['key' => 'services.intro.heading_accent', 'label' => 'Heading accent', 'type' => 'text'],
                ],
            ],
            'item_cta' => [
                'label' => 'Item CTA',
                'fields' => [
                    ['key' => 'services.item_cta', 'label' => 'Book consultation button', 'type' => 'text'],
                ],
            ],
            'cta' => [
                'label' => 'Final CTA',
                'fields' => [
                    ['key' => 'services.cta.overline', 'label' => 'Overline', 'type' => 'text'],
                    ['key' => 'services.cta.heading_1', 'label' => 'Heading prefix', 'type' => 'text'],
                    ['key' => 'services.cta.heading_accent', 'label' => 'Heading accent', 'type' => 'text'],
                    ['key' => 'services.cta.button', 'label' => 'Button label', 'type' => 'text'],
                ],
            ],
        ],
    ],

    // ─── PROJECTS ──────────────────────────────────────────
    'projects' => [
        'label' => 'Projects page',
        'description' => 'Hero + CTA only — projects are managed in the Projects admin section.',
        'sections' => [
            'hero' => [
                'label' => 'Hero',
                'fields' => [
                    ['key' => 'projects.hero.counter', 'label' => 'Projects counter label', 'type' => 'text'],
                ],
            ],
            'filters' => [
                'label' => 'Filter labels',
                'fields' => [
                    ['key' => 'projects.filters.all', 'label' => 'All filter', 'type' => 'text'],
                ],
            ],
            'cta' => [
                'label' => 'Final CTA',
                'fields' => [
                    ['key' => 'projects.cta.heading_1', 'label' => 'Heading prefix', 'type' => 'text'],
                    ['key' => 'projects.cta.heading_accent', 'label' => 'Heading accent', 'type' => 'text'],
                ],
            ],
        ],
    ],

    // ─── BRANDS ────────────────────────────────────────────
    'brands' => [
        'label' => 'Brands page',
        'description' => 'Hero + intro only — brand records are managed in the Brands admin section.',
        'sections' => [
            'hero' => [
                'label' => 'Hero',
                'fields' => [
                    ['key' => 'brands.hero.overline', 'label' => 'Overline', 'type' => 'text'],
                    ['key' => 'brands.hero.heading_1', 'label' => 'Heading line 1', 'type' => 'text'],
                    ['key' => 'brands.hero.heading_2', 'label' => 'Heading line 2', 'type' => 'text'],
                    ['key' => 'brands.hero.heading_accent', 'label' => 'Heading accent', 'type' => 'text'],
                    ['key' => 'brands.hero.sub', 'label' => 'Subheading', 'type' => 'textarea'],
                ],
            ],
            'intro' => [
                'label' => 'Intro section',
                'fields' => [
                    ['key' => 'brands.intro.overline', 'label' => 'Overline', 'type' => 'text'],
                    ['key' => 'brands.intro.heading_1', 'label' => 'Heading line 1', 'type' => 'text'],
                    ['key' => 'brands.intro.heading_2', 'label' => 'Heading line 2', 'type' => 'text'],
                    ['key' => 'brands.intro.heading_accent', 'label' => 'Heading accent', 'type' => 'text'],
                    ['key' => 'brands.intro.body', 'label' => 'Body', 'type' => 'textarea'],
                    ['key' => 'brands.intro.quote', 'label' => 'Quote overlay text', 'type' => 'text'],
                ],
            ],
            'cta' => [
                'label' => 'Final CTA',
                'fields' => [
                    ['key' => 'brands.cta.heading_1', 'label' => 'Heading prefix', 'type' => 'text'],
                    ['key' => 'brands.cta.heading_accent', 'label' => 'Heading accent', 'type' => 'text'],
                ],
            ],
        ],
    ],

    // ─── BRANCHES ──────────────────────────────────────────
    'branches' => [
        'label' => 'Branches page',
        'description' => 'Heading + CTA labels only — branch records are managed in the Branches admin section.',
        'sections' => [
            'hero' => [
                'label' => 'Hero',
                'fields' => [
                    ['key' => 'branches.hero.overline', 'label' => 'Overline', 'type' => 'text'],
                    ['key' => 'branches.hero.heading_1', 'label' => 'Heading line 1', 'type' => 'text'],
                    ['key' => 'branches.hero.heading_accent', 'label' => 'Heading accent', 'type' => 'text'],
                ],
            ],
            'ctas' => [
                'label' => 'Button labels',
                'fields' => [
                    ['key' => 'branches.directions_cta', 'label' => 'Get Directions', 'type' => 'text'],
                    ['key' => 'branches.call_cta', 'label' => 'Call', 'type' => 'text'],
                    ['key' => 'branches.whatsapp_cta', 'label' => 'WhatsApp', 'type' => 'text'],
                    ['key' => 'branches.photo_pending', 'label' => '"Photos forthcoming" placeholder', 'type' => 'text'],
                ],
            ],
        ],
    ],

    // ─── CONTACT ───────────────────────────────────────────
    'contact' => [
        'label' => 'Contact page',
        'sections' => [
            'hero' => [
                'label' => 'Hero',
                'fields' => [
                    ['key' => 'contact.hero.overline', 'label' => 'Overline', 'type' => 'text'],
                    ['key' => 'contact.hero.heading_1', 'label' => 'Heading line 1', 'type' => 'text'],
                    ['key' => 'contact.hero.heading_accent', 'label' => 'Heading accent', 'type' => 'text'],
                    ['key' => 'contact.hero.sub', 'label' => 'Subheading', 'type' => 'textarea'],
                ],
            ],
            'intro' => [
                'label' => 'Intro section',
                'fields' => [
                    ['key' => 'contact.intro.overline', 'label' => 'Overline', 'type' => 'text'],
                    ['key' => 'contact.intro.heading', 'label' => 'Heading', 'type' => 'text'],
                    ['key' => 'contact.intro.body', 'label' => 'Body', 'type' => 'textarea'],
                ],
            ],
            'form' => [
                'label' => 'Contact form',
                'fields' => [
                    ['key' => 'contact.form.name', 'label' => 'Name label', 'type' => 'text'],
                    ['key' => 'contact.form.name_placeholder', 'label' => 'Name placeholder', 'type' => 'text'],
                    ['key' => 'contact.form.email', 'label' => 'Email label', 'type' => 'text'],
                    ['key' => 'contact.form.email_placeholder', 'label' => 'Email placeholder', 'type' => 'text'],
                    ['key' => 'contact.form.phone', 'label' => 'Phone label', 'type' => 'text'],
                    ['key' => 'contact.form.phone_placeholder', 'label' => 'Phone placeholder', 'type' => 'text'],
                    ['key' => 'contact.form.message', 'label' => 'Message label', 'type' => 'text'],
                    ['key' => 'contact.form.message_placeholder', 'label' => 'Message placeholder', 'type' => 'text'],
                    ['key' => 'contact.form.submit', 'label' => 'Submit button', 'type' => 'text'],
                ],
            ],
        ],
    ],

    // ─── LAYOUT (nav + footer + marquee) ──────────────────
    'layout' => [
        'label' => 'Navigation & Footer',
        'description' => 'Labels shown on every page — nav menu, footer, top marquee, language switcher.',
        'sections' => [
            'brand' => [
                'label' => 'Brand name',
                'fields' => [
                    ['key' => 'common.brand.name_primary', 'label' => 'Primary name (DELOS)', 'type' => 'text'],
                    ['key' => 'common.brand.name_secondary', 'label' => 'Secondary name (INTERNATIONAL)', 'type' => 'text'],
                    ['key' => 'common.brand.tagline', 'label' => 'Tagline', 'type' => 'text'],
                ],
            ],
            'nav' => [
                'label' => 'Navigation menu',
                'fields' => [
                    ['key' => 'common.nav.home', 'label' => 'Home', 'type' => 'text'],
                    ['key' => 'common.nav.brands', 'label' => 'Brands', 'type' => 'text'],
                    ['key' => 'common.nav.services', 'label' => 'Services', 'type' => 'text'],
                    ['key' => 'common.nav.projects', 'label' => 'Projects', 'type' => 'text'],
                    ['key' => 'common.nav.about', 'label' => 'About / Company Profile', 'type' => 'text'],
                    ['key' => 'common.nav.branches', 'label' => 'Branches', 'type' => 'text'],
                    ['key' => 'common.nav.contact', 'label' => 'Contact Us', 'type' => 'text'],
                ],
            ],
            'marquee' => [
                'label' => 'Top marquee strip',
                'fields' => [
                    ['key' => 'common.marquee.tagline', 'label' => 'Tagline', 'type' => 'text'],
                    ['key' => 'common.marquee.showrooms_across_iraq', 'label' => '"Showrooms across Iraq"', 'type' => 'text'],
                    ['key' => 'common.marquee.trust_the_lion', 'label' => '"Trust the lion"', 'type' => 'text'],
                    ['key' => 'common.marquee.concept_to_completion', 'label' => '"Concept to completion"', 'type' => 'text'],
                ],
            ],
            'footer' => [
                'label' => 'Footer',
                'fields' => [
                    ['key' => 'common.footer.tagline_long', 'label' => 'Brand description', 'type' => 'textarea'],
                    ['key' => 'common.footer.lion_motto', 'label' => 'Lion motto', 'type' => 'text'],
                    ['key' => 'common.footer.nav_heading', 'label' => '"Navigation" heading', 'type' => 'text'],
                    ['key' => 'common.footer.showrooms_heading', 'label' => '"Showrooms" heading', 'type' => 'text'],
                    ['key' => 'common.footer.partners_heading', 'label' => '"Partners" heading', 'type' => 'text'],
                    ['key' => 'common.footer.copyright', 'label' => 'Copyright line', 'type' => 'text'],
                    ['key' => 'common.footer.bottom_tagline', 'label' => 'Bottom tagline', 'type' => 'text'],
                ],
            ],
            'ctas' => [
                'label' => 'Shared button labels',
                'fields' => [
                    ['key' => 'common.ctas.view_projects', 'label' => 'View projects', 'type' => 'text'],
                    ['key' => 'common.ctas.explore_brands', 'label' => 'Explore brands', 'type' => 'text'],
                    ['key' => 'common.ctas.book_consultation', 'label' => 'Book consultation', 'type' => 'text'],
                    ['key' => 'common.ctas.visit_showroom', 'label' => 'Visit showroom', 'type' => 'text'],
                    ['key' => 'common.ctas.visit_official_website', 'label' => 'Visit official website', 'type' => 'text'],
                    ['key' => 'common.ctas.get_in_touch', 'label' => 'Get in touch', 'type' => 'text'],
                    ['key' => 'common.ctas.learn_more', 'label' => 'Learn more', 'type' => 'text'],
                ],
            ],
            'language_switcher' => [
                'label' => 'Language switcher',
                'fields' => [
                    ['key' => 'common.language_switcher.aria_label', 'label' => 'Accessibility label', 'type' => 'text'],
                    ['key' => 'common.language_switcher.switch_to', 'label' => '"Switch language" heading', 'type' => 'text'],
                ],
            ],
        ],
    ],
];
