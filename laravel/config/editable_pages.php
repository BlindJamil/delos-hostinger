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
                    ['key' => 'about.hero.heading_1', 'label' => 'Heading line 1', 'type' => 'text'],
                    ['key' => 'about.hero.heading_2', 'label' => 'Heading line 2', 'type' => 'text'],
                    ['key' => 'about.hero.heading_accent', 'label' => 'Heading accent', 'type' => 'text'],
                    ['key' => 'about.hero.sub', 'label' => 'Subheading', 'type' => 'textarea'],
                    ['key' => 'about.hero.stats.0.value', 'label' => 'Hero stat 1 · value', 'type' => 'text'],
                    ['key' => 'about.hero.stats.0.label', 'label' => 'Hero stat 1 · label', 'type' => 'text'],
                    ['key' => 'about.hero.stats.1.value', 'label' => 'Hero stat 2 · value', 'type' => 'text'],
                    ['key' => 'about.hero.stats.1.label', 'label' => 'Hero stat 2 · label', 'type' => 'text'],
                    ['key' => 'about.hero.stats.2.value', 'label' => 'Hero stat 3 · value', 'type' => 'text'],
                    ['key' => 'about.hero.stats.2.label', 'label' => 'Hero stat 3 · label', 'type' => 'text'],
                ],
            ],
            'story' => [
                'label' => 'Story section',
                'fields' => [
                    ['key' => 'about.story.year_badge', 'label' => 'Year badge', 'type' => 'text'],
                    ['key' => 'about.story.year_label', 'label' => 'Year label', 'type' => 'text'],
                    ['key' => 'about.story.overline', 'label' => 'Overline', 'type' => 'text'],
                    ['key' => 'about.story.heading_1', 'label' => 'Heading line 1', 'type' => 'text'],
                    ['key' => 'about.story.heading_2', 'label' => 'Heading line 2', 'type' => 'text'],
                    ['key' => 'about.story.heading_accent', 'label' => 'Heading accent', 'type' => 'text'],
                    ['key' => 'about.story.paragraph_1', 'label' => 'Paragraph 1', 'type' => 'textarea'],
                    ['key' => 'about.story.paragraph_2', 'label' => 'Paragraph 2', 'type' => 'textarea'],
                    ['key' => 'about.story.paragraph_3', 'label' => 'Paragraph 3', 'type' => 'textarea'],
                ],
            ],
            'direction' => [
                'label' => 'Vision / Mission / Goal cards',
                'fields' => [
                    ['key' => 'about.direction.overline', 'label' => 'Overline', 'type' => 'text'],
                    ['key' => 'about.direction.heading_1', 'label' => 'Heading line 1', 'type' => 'text'],
                    ['key' => 'about.direction.heading_2', 'label' => 'Heading line 2', 'type' => 'text'],
                    ['key' => 'about.direction.items.vision.label', 'label' => 'Vision · label', 'type' => 'text'],
                    ['key' => 'about.direction.items.vision.title', 'label' => 'Vision · title', 'type' => 'text'],
                    ['key' => 'about.direction.items.vision.body', 'label' => 'Vision · body', 'type' => 'textarea'],
                    ['key' => 'about.direction.items.mission.label', 'label' => 'Mission · label', 'type' => 'text'],
                    ['key' => 'about.direction.items.mission.title', 'label' => 'Mission · title', 'type' => 'text'],
                    ['key' => 'about.direction.items.mission.body', 'label' => 'Mission · body', 'type' => 'textarea'],
                    ['key' => 'about.direction.items.goal.label', 'label' => 'Goal · label', 'type' => 'text'],
                    ['key' => 'about.direction.items.goal.title', 'label' => 'Goal · title', 'type' => 'text'],
                    ['key' => 'about.direction.items.goal.body', 'label' => 'Goal · body', 'type' => 'textarea'],
                ],
            ],
            'philosophy' => [
                'label' => 'Philosophy',
                'fields' => [
                    ['key' => 'about.philosophy.overline', 'label' => 'Overline', 'type' => 'text'],
                    ['key' => 'about.philosophy.heading_1', 'label' => 'Heading line 1', 'type' => 'text'],
                    ['key' => 'about.philosophy.heading_2', 'label' => 'Heading line 2', 'type' => 'text'],
                    ['key' => 'about.philosophy.heading_accent', 'label' => 'Heading accent', 'type' => 'text'],
                    ['key' => 'about.philosophy.paragraph_1', 'label' => 'Paragraph 1', 'type' => 'textarea'],
                    ['key' => 'about.philosophy.paragraph_2', 'label' => 'Paragraph 2', 'type' => 'textarea'],
                ],
            ],
            'materials' => [
                'label' => 'Materials section',
                'fields' => [
                    ['key' => 'about.materials.overline', 'label' => 'Overline', 'type' => 'text'],
                    ['key' => 'about.materials.heading_1', 'label' => 'Heading line 1', 'type' => 'text'],
                    ['key' => 'about.materials.heading_2', 'label' => 'Heading line 2', 'type' => 'text'],
                    ['key' => 'about.materials.heading_accent_1', 'label' => 'Heading accent 1', 'type' => 'text'],
                    ['key' => 'about.materials.heading_accent_2', 'label' => 'Heading accent 2', 'type' => 'text'],
                    ['key' => 'about.materials.heading_3', 'label' => 'Heading "and"', 'type' => 'text'],
                    ['key' => 'about.materials.heading_4', 'label' => 'Heading last line', 'type' => 'text'],
                    ['key' => 'about.materials.body', 'label' => 'Body', 'type' => 'textarea'],
                ],
            ],
            'pillars' => [
                'label' => 'The Delos Difference',
                'fields' => [
                    ['key' => 'about.pillars.overline', 'label' => 'Overline', 'type' => 'text'],
                    ['key' => 'about.pillars.heading', 'label' => 'Heading', 'type' => 'text'],
                    ['key' => 'about.pillars.items.authentic.num', 'label' => 'Pillar 1 · number', 'type' => 'text'],
                    ['key' => 'about.pillars.items.authentic.title', 'label' => 'Pillar 1 · title', 'type' => 'text'],
                    ['key' => 'about.pillars.items.authentic.desc', 'label' => 'Pillar 1 · description', 'type' => 'textarea'],
                    ['key' => 'about.pillars.items.turnkey.num', 'label' => 'Pillar 2 · number', 'type' => 'text'],
                    ['key' => 'about.pillars.items.turnkey.title', 'label' => 'Pillar 2 · title', 'type' => 'text'],
                    ['key' => 'about.pillars.items.turnkey.desc', 'label' => 'Pillar 2 · description', 'type' => 'textarea'],
                    ['key' => 'about.pillars.items.premium.num', 'label' => 'Pillar 3 · number', 'type' => 'text'],
                    ['key' => 'about.pillars.items.premium.title', 'label' => 'Pillar 3 · title', 'type' => 'text'],
                    ['key' => 'about.pillars.items.premium.desc', 'label' => 'Pillar 3 · description', 'type' => 'textarea'],
                    ['key' => 'about.pillars.items.trusted.num', 'label' => 'Pillar 4 · number', 'type' => 'text'],
                    ['key' => 'about.pillars.items.trusted.title', 'label' => 'Pillar 4 · title', 'type' => 'text'],
                    ['key' => 'about.pillars.items.trusted.desc', 'label' => 'Pillar 4 · description', 'type' => 'textarea'],
                ],
            ],
            'quote' => [
                'label' => 'Pull quote',
                'fields' => [
                    ['key' => 'about.quote.overline', 'label' => 'Overline', 'type' => 'text'],
                    ['key' => 'about.quote.text_before_accent', 'label' => 'Text before accent', 'type' => 'text'],
                    ['key' => 'about.quote.text_accent', 'label' => 'Text accent (gold)', 'type' => 'text'],
                    ['key' => 'about.quote.text_after_accent', 'label' => 'Text after accent', 'type' => 'textarea'],
                    ['key' => 'about.quote.signature', 'label' => 'Signature', 'type' => 'text'],
                ],
            ],
            'about_stats' => [
                'label' => 'Stats section',
                'fields' => [
                    ['key' => 'about.about_stats.items.0.value', 'label' => 'Stat 1 · number', 'type' => 'text'],
                    ['key' => 'about.about_stats.items.0.suffix', 'label' => 'Stat 1 · suffix', 'type' => 'text'],
                    ['key' => 'about.about_stats.items.0.label', 'label' => 'Stat 1 · label', 'type' => 'text'],
                    ['key' => 'about.about_stats.items.1.value', 'label' => 'Stat 2 · number', 'type' => 'text'],
                    ['key' => 'about.about_stats.items.1.suffix', 'label' => 'Stat 2 · suffix', 'type' => 'text'],
                    ['key' => 'about.about_stats.items.1.label', 'label' => 'Stat 2 · label', 'type' => 'text'],
                    ['key' => 'about.about_stats.items.2.value', 'label' => 'Stat 3 · number', 'type' => 'text'],
                    ['key' => 'about.about_stats.items.2.suffix', 'label' => 'Stat 3 · suffix', 'type' => 'text'],
                    ['key' => 'about.about_stats.items.2.label', 'label' => 'Stat 3 · label', 'type' => 'text'],
                    ['key' => 'about.about_stats.items.3.value', 'label' => 'Stat 4 · number', 'type' => 'text'],
                    ['key' => 'about.about_stats.items.3.suffix', 'label' => 'Stat 4 · suffix', 'type' => 'text'],
                    ['key' => 'about.about_stats.items.3.label', 'label' => 'Stat 4 · label', 'type' => 'text'],
                ],
            ],
            'workflow' => [
                'label' => 'How We Work',
                'fields' => [
                    ['key' => 'about.workflow.overline', 'label' => 'Overline', 'type' => 'text'],
                    ['key' => 'about.workflow.heading_1', 'label' => 'Heading line 1', 'type' => 'text'],
                    ['key' => 'about.workflow.heading_2', 'label' => 'Heading line 2', 'type' => 'text'],
                    ['key' => 'about.workflow.steps.consultation.step', 'label' => 'Step 1 · number', 'type' => 'text'],
                    ['key' => 'about.workflow.steps.consultation.title', 'label' => 'Step 1 · title', 'type' => 'text'],
                    ['key' => 'about.workflow.steps.consultation.desc', 'label' => 'Step 1 · description', 'type' => 'textarea'],
                    ['key' => 'about.workflow.steps.design.step', 'label' => 'Step 2 · number', 'type' => 'text'],
                    ['key' => 'about.workflow.steps.design.title', 'label' => 'Step 2 · title', 'type' => 'text'],
                    ['key' => 'about.workflow.steps.design.desc', 'label' => 'Step 2 · description', 'type' => 'textarea'],
                    ['key' => 'about.workflow.steps.sourcing.step', 'label' => 'Step 3 · number', 'type' => 'text'],
                    ['key' => 'about.workflow.steps.sourcing.title', 'label' => 'Step 3 · title', 'type' => 'text'],
                    ['key' => 'about.workflow.steps.sourcing.desc', 'label' => 'Step 3 · description', 'type' => 'textarea'],
                    ['key' => 'about.workflow.steps.installation.step', 'label' => 'Step 4 · number', 'type' => 'text'],
                    ['key' => 'about.workflow.steps.installation.title', 'label' => 'Step 4 · title', 'type' => 'text'],
                    ['key' => 'about.workflow.steps.installation.desc', 'label' => 'Step 4 · description', 'type' => 'textarea'],
                ],
            ],
            'branches' => [
                'label' => 'Branches mention',
                'fields' => [
                    ['key' => 'about.branches.overline', 'label' => 'Overline', 'type' => 'text'],
                    ['key' => 'about.branches.heading_1', 'label' => 'Heading line 1', 'type' => 'text'],
                    ['key' => 'about.branches.heading_2', 'label' => 'Heading line 2', 'type' => 'text'],
                    ['key' => 'about.branches.heading_3', 'label' => 'Heading line 3', 'type' => 'text'],
                    ['key' => 'about.branches.heading_accent', 'label' => 'Heading accent', 'type' => 'text'],
                    ['key' => 'about.branches.items.erbil.city', 'label' => 'Erbil · city', 'type' => 'text'],
                    ['key' => 'about.branches.items.erbil.year', 'label' => 'Erbil · year', 'type' => 'text'],
                    ['key' => 'about.branches.items.erbil.address', 'label' => 'Erbil · address', 'type' => 'text'],
                    ['key' => 'about.branches.items.erbil.phone', 'label' => 'Erbil · phone', 'type' => 'text'],
                    ['key' => 'about.branches.items.kirkuk.city', 'label' => 'Kirkuk · city', 'type' => 'text'],
                    ['key' => 'about.branches.items.kirkuk.year', 'label' => 'Kirkuk · year', 'type' => 'text'],
                    ['key' => 'about.branches.items.kirkuk.address', 'label' => 'Kirkuk · address', 'type' => 'text'],
                    ['key' => 'about.branches.items.kirkuk.phone', 'label' => 'Kirkuk · phone', 'type' => 'text'],
                    ['key' => 'about.branches.items.sulaymaniyah.city', 'label' => 'Sulaymaniyah · city', 'type' => 'text'],
                    ['key' => 'about.branches.items.sulaymaniyah.year', 'label' => 'Sulaymaniyah · year', 'type' => 'text'],
                    ['key' => 'about.branches.items.sulaymaniyah.address', 'label' => 'Sulaymaniyah · address', 'type' => 'text'],
                    ['key' => 'about.branches.items.sulaymaniyah.phone', 'label' => 'Sulaymaniyah · phone', 'type' => 'text'],
                    ['key' => 'about.branches.items.baghdad.city', 'label' => 'Baghdad · city', 'type' => 'text'],
                    ['key' => 'about.branches.items.baghdad.year', 'label' => 'Baghdad · year', 'type' => 'text'],
                    ['key' => 'about.branches.items.baghdad.address', 'label' => 'Baghdad · address', 'type' => 'text'],
                    ['key' => 'about.branches.items.baghdad.phone', 'label' => 'Baghdad · phone', 'type' => 'text'],
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
                    ['key' => 'services.hero.image', 'label' => 'Hero background image', 'type' => 'image'],
                    ['key' => 'services.hero.overline', 'label' => 'Overline', 'type' => 'text'],
                    ['key' => 'services.hero.heading_1_before', 'label' => 'Heading prefix', 'type' => 'text'],
                    ['key' => 'services.hero.heading_1_accent', 'label' => 'Heading accent', 'type' => 'text'],
                    ['key' => 'services.hero.heading_2', 'label' => 'Heading line 2', 'type' => 'text'],
                    ['key' => 'services.hero.categories', 'label' => 'Service categories list', 'type' => 'text'],
                ],
            ],
            'arch_carousel' => [
                'label' => 'Architecture carousel',
                'fields' => [
                    ['key' => 'services.arch_carousel.default_label', 'label' => 'Default slide label', 'type' => 'text'],
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
        'description' => 'Hero + stats + CTA only — projects are managed in the Projects admin section.',
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
            'stats' => [
                'label' => 'Stats section',
                'fields' => [
                    ['key' => 'projects.stats.0.value', 'label' => 'Stat 1 · value', 'type' => 'text'],
                    ['key' => 'projects.stats.0.label', 'label' => 'Stat 1 · label', 'type' => 'text'],
                    ['key' => 'projects.stats.1.value', 'label' => 'Stat 2 · value', 'type' => 'text'],
                    ['key' => 'projects.stats.1.label', 'label' => 'Stat 2 · label', 'type' => 'text'],
                    ['key' => 'projects.stats.2.value', 'label' => 'Stat 3 · value', 'type' => 'text'],
                    ['key' => 'projects.stats.2.label', 'label' => 'Stat 3 · label', 'type' => 'text'],
                    ['key' => 'projects.stats.3.value', 'label' => 'Stat 4 · value', 'type' => 'text'],
                    ['key' => 'projects.stats.3.label', 'label' => 'Stat 4 · label', 'type' => 'text'],
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
                ],
            ],
            'intro' => [
                'label' => 'Intro section',
                'fields' => [
                    ['key' => 'contact.intro.overline', 'label' => 'Overline', 'type' => 'text'],
                    ['key' => 'contact.intro.heading_1', 'label' => 'Heading line 1', 'type' => 'text'],
                    ['key' => 'contact.intro.heading_accent', 'label' => 'Heading accent', 'type' => 'text'],
                    ['key' => 'contact.intro.heading_2', 'label' => 'Heading line 2', 'type' => 'text'],
                    ['key' => 'contact.intro.body', 'label' => 'Body', 'type' => 'textarea'],
                ],
            ],
            'form' => [
                'label' => 'Contact form',
                'fields' => [
                    ['key' => 'contact.form.first_name', 'label' => 'First name label', 'type' => 'text'],
                    ['key' => 'contact.form.first_name_placeholder', 'label' => 'First name placeholder', 'type' => 'text'],
                    ['key' => 'contact.form.last_name', 'label' => 'Last name label', 'type' => 'text'],
                    ['key' => 'contact.form.last_name_placeholder', 'label' => 'Last name placeholder', 'type' => 'text'],
                    ['key' => 'contact.form.email', 'label' => 'Email label', 'type' => 'text'],
                    ['key' => 'contact.form.email_placeholder', 'label' => 'Email placeholder', 'type' => 'text'],
                    ['key' => 'contact.form.phone', 'label' => 'Phone label', 'type' => 'text'],
                    ['key' => 'contact.form.phone_placeholder', 'label' => 'Phone placeholder', 'type' => 'text'],
                    ['key' => 'contact.form.branch', 'label' => 'Branch label', 'type' => 'text'],
                    ['key' => 'contact.form.branch_placeholder', 'label' => 'Branch placeholder', 'type' => 'text'],
                    ['key' => 'contact.form.service', 'label' => 'Service label', 'type' => 'text'],
                    ['key' => 'contact.form.service_placeholder', 'label' => 'Service placeholder', 'type' => 'text'],
                    ['key' => 'contact.form.message', 'label' => 'Message label', 'type' => 'text'],
                    ['key' => 'contact.form.message_placeholder', 'label' => 'Message placeholder', 'type' => 'text'],
                    ['key' => 'contact.form.submit', 'label' => 'Submit button', 'type' => 'text'],
                    ['key' => 'contact.form.cities.0', 'label' => 'City option 1', 'type' => 'text'],
                    ['key' => 'contact.form.cities.1', 'label' => 'City option 2', 'type' => 'text'],
                    ['key' => 'contact.form.cities.2', 'label' => 'City option 3', 'type' => 'text'],
                    ['key' => 'contact.form.cities.3', 'label' => 'City option 4', 'type' => 'text'],
                    ['key' => 'contact.form.service_options.0', 'label' => 'Service option 1', 'type' => 'text'],
                    ['key' => 'contact.form.service_options.1', 'label' => 'Service option 2', 'type' => 'text'],
                    ['key' => 'contact.form.service_options.2', 'label' => 'Service option 3', 'type' => 'text'],
                    ['key' => 'contact.form.service_options.3', 'label' => 'Service option 4', 'type' => 'text'],
                    ['key' => 'contact.form.service_options.4', 'label' => 'Service option 5', 'type' => 'text'],
                    ['key' => 'contact.form.service_options.5', 'label' => 'Service option 6', 'type' => 'text'],
                ],
            ],
        ],
    ],

    // ─── SEO META ──────────────────────────────────────────
    'seo' => [
        'label' => 'SEO Titles & Descriptions',
        'description' => 'Browser tab titles and meta descriptions for every page.',
        'sections' => [
            'defaults' => [
                'label' => 'Default fallback',
                'fields' => [
                    ['key' => 'seo.default.title', 'label' => 'Default title', 'type' => 'text'],
                    ['key' => 'seo.default.description', 'label' => 'Default description', 'type' => 'textarea'],
                ],
            ],
            'pages' => [
                'label' => 'Per-page',
                'fields' => [
                    ['key' => 'seo.home.title', 'label' => 'Home · title', 'type' => 'text'],
                    ['key' => 'seo.home.description', 'label' => 'Home · description', 'type' => 'textarea'],
                    ['key' => 'seo.about.title', 'label' => 'About · title', 'type' => 'text'],
                    ['key' => 'seo.about.description', 'label' => 'About · description', 'type' => 'textarea'],
                    ['key' => 'seo.services.title', 'label' => 'Services · title', 'type' => 'text'],
                    ['key' => 'seo.services.description', 'label' => 'Services · description', 'type' => 'textarea'],
                    ['key' => 'seo.brands.title', 'label' => 'Brands · title', 'type' => 'text'],
                    ['key' => 'seo.brands.description', 'label' => 'Brands · description', 'type' => 'textarea'],
                    ['key' => 'seo.projects.title', 'label' => 'Projects · title', 'type' => 'text'],
                    ['key' => 'seo.projects.description', 'label' => 'Projects · description', 'type' => 'textarea'],
                    ['key' => 'seo.branches.title', 'label' => 'Branches · title', 'type' => 'text'],
                    ['key' => 'seo.branches.description', 'label' => 'Branches · description', 'type' => 'textarea'],
                    ['key' => 'seo.contact.title', 'label' => 'Contact · title', 'type' => 'text'],
                    ['key' => 'seo.contact.description', 'label' => 'Contact · description', 'type' => 'textarea'],
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
                    ['key' => 'common.nav.menu_toggle', 'label' => 'Menu toggle aria label', 'type' => 'text'],
                ],
            ],
            'marquee' => [
                'label' => 'Top marquee strip',
                'fields' => [
                    ['key' => 'common.marquee.tagline', 'label' => 'Tagline', 'type' => 'text'],
                    ['key' => 'common.marquee.showrooms', 'label' => '"Showrooms across Iraq"', 'type' => 'text'],
                    ['key' => 'common.marquee.lion', 'label' => '"Trust the lion"', 'type' => 'text'],
                    ['key' => 'common.marquee.turnkey', 'label' => '"Concept to completion"', 'type' => 'text'],
                    ['key' => 'common.marquee.cities', 'label' => 'City list', 'type' => 'text'],
                ],
            ],
            'footer' => [
                'label' => 'Footer',
                'fields' => [
                    ['key' => 'common.footer.tagline_long', 'label' => 'Brand description', 'type' => 'textarea'],
                    ['key' => 'common.footer.lion_motto', 'label' => 'Lion motto', 'type' => 'text'],
                    ['key' => 'common.footer.nav_heading', 'label' => '"Navigation" heading', 'type' => 'text'],
                    ['key' => 'common.footer.showrooms_heading', 'label' => '"Showrooms" heading', 'type' => 'text'],
                    ['key' => 'common.footer.showroom_erbil_soran.title', 'label' => 'Erbil Soran · title', 'type' => 'text'],
                    ['key' => 'common.footer.showroom_erbil_soran.address', 'label' => 'Erbil Soran · address', 'type' => 'text'],
                    ['key' => 'common.footer.showroom_erbil_soran.phone', 'label' => 'Erbil Soran · phone', 'type' => 'text'],
                    ['key' => 'common.footer.showroom_erbil_gulan.title', 'label' => 'Erbil Gulan · title', 'type' => 'text'],
                    ['key' => 'common.footer.showroom_erbil_gulan.address', 'label' => 'Erbil Gulan · address', 'type' => 'text'],
                    ['key' => 'common.footer.showroom_erbil_gulan.phone', 'label' => 'Erbil Gulan · phone', 'type' => 'text'],
                    ['key' => 'common.footer.other_cities', 'label' => 'Other cities line', 'type' => 'text'],
                    ['key' => 'common.footer.partners_heading', 'label' => '"Partners" heading', 'type' => 'text'],
                    ['key' => 'common.footer.copyright', 'label' => 'Copyright line', 'type' => 'text'],
                    ['key' => 'common.footer.bottom_tagline', 'label' => 'Bottom tagline', 'type' => 'text'],
                ],
            ],
            'ctas' => [
                'label' => 'Shared button labels',
                'fields' => [
                    ['key' => 'common.ctas.view_all_projects', 'label' => 'View all projects', 'type' => 'text'],
                    ['key' => 'common.ctas.explore_partners', 'label' => 'Explore our partners', 'type' => 'text'],
                    ['key' => 'common.ctas.book_consultation', 'label' => 'Book consultation', 'type' => 'text'],
                    ['key' => 'common.ctas.visit_showroom', 'label' => 'Visit showroom', 'type' => 'text'],
                    ['key' => 'common.ctas.visit_official_website', 'label' => 'Visit official website', 'type' => 'text'],
                    ['key' => 'common.ctas.request_consultation', 'label' => 'Request consultation', 'type' => 'text'],
                    ['key' => 'common.ctas.learn_more', 'label' => 'Learn more', 'type' => 'text'],
                    ['key' => 'common.ctas.call_us', 'label' => 'Call us', 'type' => 'text'],
                    ['key' => 'common.ctas.start_project', 'label' => 'Start your project', 'type' => 'text'],
                    ['key' => 'common.ctas.find_nearest_showroom', 'label' => 'Find nearest showroom', 'type' => 'text'],
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
