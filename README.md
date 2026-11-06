# Peoples Development Organisation — WordPress build

A complete, production-ready WordPress site for **Peoples Development
Organisation (PDO)**, a registered Bangladeshi non-profit working on technical
education, zero-hunger campuses, youth resilience and climate action.

The build is split in two so that neither half traps the other:

| Folder | What it is | Responsibility |
| --- | --- | --- |
| [`pdo-site-manager/`](pdo-site-manager) | Plugin — *PDO Website Manager* | Owns **content**: settings, custom post types, bilingual copy, forms, submissions |
| [`pdo-modern/`](pdo-modern) | Theme — *PDO Modern* | Owns **presentation**: templates, design system, Customizer |

Deactivate the plugin and the theme still renders. Switch the theme and the
content survives. Every template-API call is guarded with `function_exists()`.

---

## Quick start

1. Copy `pdo-site-manager/` into `wp-content/plugins/` and activate it
   (**Plugins → PDO Website Manager**).
2. Copy `pdo-modern/` into `wp-content/themes/` and activate it
   (**Appearance → Themes → PDO Modern**).
3. Run **PDO Manager → Setup Wizard**. It seeds the seven pages, three menus,
   demo records and all default settings, and sets the static front page.
4. Set permalinks to **Post name** (**Settings → Permalinks**).
5. Tune colours, typography and layout in **Appearance → Customize**.

Pre-built ZIPs for both halves live in [`dist/`](dist).

## Requirements

* WordPress 6.4+ (tested to 6.8)
* PHP 7.4+ (tested on 8.2)
* MySQL 5.7+ / MariaDB 10.3+ — or SQLite via the official SQLite drop-in
* No Composer, no npm, no build step, no CDN, no jQuery on the front end

## What you get

**Seven designed pages** — Home, About Us, Our Impact, Transparency,
Media & Insights, Get Involved, Contact Us — plus archives and single views for
programmes, news, stories, documents, team members and partners.

**One dashboard.** Nine settings groups across 37 tabs, a drag-and-drop section
reorder screen, a translations table, a submissions inbox with CSV export, a
setup wizard and JSON import/export.

**Bilingual at runtime.** Every field has an English value and a Bengali twin.
`?lang=bn` (or the `pdo_lang` cookie) flips the site; `hreflang` and
`x-default` are emitted automatically. No duplicate posts, no second site.

**A real design system.** ~40 colour tokens, type scale, spacing, radius and
shadow all flow from the Customizer into CSS custom properties. Nothing in the
stylesheet hardcodes a brand colour.

**Hardened forms.** Nonce + honeypot + minimum fill time + rate limiting, stored
in a dedicated table, with a graceful message when mail delivery fails.

## Documentation

| Document | For whom |
| --- | --- |
| [`docs/INSTALL.md`](docs/INSTALL.md) | Installing, seeding and going live |
| [`docs/EDITOR-GUIDE.md`](docs/EDITOR-GUIDE.md) | Day-to-day content editing |
| [`docs/ARCHITECTURE.md`](docs/ARCHITECTURE.md) | How the two halves fit together |
| [`docs/TEMPLATE-API.md`](docs/TEMPLATE-API.md) | Every function a theme may call |
| [`docs/CUSTOMIZER.md`](docs/CUSTOMIZER.md) | All theme settings and CSS variables |
| [`docs/DEVELOPMENT.md`](docs/DEVELOPMENT.md) | Local setup, conventions, testing |

## Repository layout

```
pdo-website/
├── pdo-site-manager/        # plugin  (30 PHP files)
│   ├── pdo-site-manager.php
│   ├── includes/            # schema, settings, CPTs, forms, i18n, template API
│   ├── admin/               # dashboard views, admin CSS/JS
│   ├── languages/           # pdo-site-manager.pot (724 strings)
│   └── readme.txt
├── pdo-modern/              # theme   (102 PHP files)
│   ├── style.css            # theme header + core WP classes
│   ├── functions.php
│   ├── inc/                 # setup, enqueue, helpers, SEO, Customizer
│   ├── template-parts/      # header, sections, components, pages
│   ├── templates/           # named page templates
│   ├── languages/           # pdo-modern.pot (313 strings)
│   └── assets/              # css, js, fonts
├── docs/                    # the documents listed above
├── tools/makepot.py         # dependency-free .pot generator
├── dist/                    # installable ZIPs
└── peoples_development_organisation_pdo_official_website.html   # source design
```

## Licence

GPL-2.0-or-later, for both the plugin and the theme.
