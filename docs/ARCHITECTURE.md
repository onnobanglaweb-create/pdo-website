# Architecture

## The split

```
          ┌───────────────────────────────┐
          │  PDO Website Manager (plugin) │   owns CONTENT
          │                               │
          │  options  pdo_sm_{group}      │
          │  CPTs     pdo_program …       │
          │  table    {prefix}pdo_submissions
          └───────────────┬───────────────┘
                          │  ~30 guarded global functions
                          │  pdo_setting() pdo_rows() pdo_get_posts() …
                          ▼
          ┌───────────────────────────────┐
          │  PDO Modern (theme)           │   owns PRESENTATION
          │                               │
          │  templates + template-parts   │
          │  Customizer → CSS variables   │
          └───────────────────────────────┘
```

**Rule 1 — the theme never touches plugin classes.** Templates only call the
global template-API functions. Each one is wrapped in `function_exists()` by
the plugin, and `pdo-modern/inc/compatibility.php` declares a fallback for
every single one. Deactivating the plugin therefore cannot fatal the site.

**Rule 2 — the plugin never emits markup for the front end.** It stores and
returns data. The only HTML it renders is the admin UI and inline SVG icons.

## Plugin

### Settings

Nine option groups, one row in `wp_options` each:

```
pdo_sm_org   pdo_sm_home   pdo_sm_about  pdo_sm_impact  pdo_sm_transparency
pdo_sm_media pdo_sm_involved pdo_sm_contact pdo_sm_i18n
```

`PDO_SM_Schema` is the single source of truth. It declares, for every group:
tabs → panels → fields. A field has a `key`, a `type`
(`text`, `textarea`, `richtext`, `number`, `checkbox`, `select`, `color`,
`icon`, `image`, `url`, `email`, `repeater`), an optional `bilingual` flag and,
for repeaters, a nested field list.

Everything else is generated from that declaration — the admin screens
(`PDO_SM_Dashboard` + `admin/views/settings.php`), sanitisation
(`PDO_SM_Settings::sanitize_group()`), defaults, the export format and the
Translations table. **Add a field to the schema and it appears everywhere.**

Reads go through `PDO_SM_Settings::get()` / `::text()`, which apply the
bilingual fallback: when the active language is Bengali the `{key}_bn` value is
returned if it is non-empty, otherwise the English value.

### Content types

| Post type | Archive | Key meta (prefix `_pdo_`) |
| --- | --- | --- |
| `pdo_program` | `/programs/` | `short_name`, `icon`, `sdgs`, `results`, `cta_*`, `accent`, `featured`, `stats[]`, `achievements[]`, `gallery[]`, `documents[]` |
| `pdo_team` | — | `designation`, `photo_url`, `email`, `phone`, `socials[]` |
| `pdo_partner` | — | `description`, `website`, `icon`, `logo_url`, `accent` |
| `pdo_story` | `/impact-stories/` | `beneficiary`, `quote`, `photo_url`, `location`, `story_date`, `tag`, `program` |
| `pdo_document` | — | `description`, `file`, `external_url`, `icon`, `year`, `download_label`, `preview_enabled` |
| `pdo_news` | `/media/` | `image_url`, `kicker` |
| `pdo_section` | — | the full dynamic-section payload |

Every text meta key has a `_bn` twin. All types share a `display` meta box
(order, visibility, accent).

Taxonomies: `pdo_program_cat`, `pdo_team_group`, `pdo_partner_cat`,
`pdo_story_cat`, `pdo_doc_cat`, `pdo_news_cat`, `pdo_news_tag`.

### Dynamic sections

A `pdo_section` post is a reusable band of content. `PDO_SM_Sections::prepare()`
normalises it into a flat array the theme can render blindly:

```php
[
  'id', 'title', 'type', 'location', 'placement',
  'eyebrow', 'heading', 'content', 'image', 'icon',
  'btn_label', 'btn_url', 'background', 'text_color',
  'columns', 'align', 'padding', 'limit',
  'hidden', 'hide_desktop', 'hide_tablet', 'hide_mobile',
  'items' => [ [ 'title', 'subtitle', 'description', 'image', 'image_url',
                 'alt', 'icon', 'value', 'color', 'link_label', 'link_url' ] ],
]
```

* **21 types** → `template-parts/sections/dynamic-{type}.php`, with
  `dynamic-fallback.php` catching anything unknown.
* **9 locations** → `home`, `about`, `impact`, `transparency`, `media`,
  `involved`, `contact`, `global_top`, `global_bottom`.
* **2 placements** → `before` / `after` the page body.

The theme renders them with `pdo_modern_dynamic_sections( $location, $placement )`.
The filter `pdo_modern_pre_render_section` can short-circuit any one of them.

### Forms & submissions

```
<form method="post" action="admin-post.php" data-pdo-form="{type}">
  action        = pdo_sm_submit
  pdo_form      = contact | volunteer | partner
  _pdo_nonce    = wp_nonce_field( 'pdo_sm_form_{type}', '_pdo_nonce' )
  _pdo_redirect = current URL
  pdo_website   = honeypot (must stay empty)
  pdo_ts        = time()      (≥ 3 s must elapse)
  pdo_field[…]  = the actual values
```

`PDO_SM_Forms::handle()` validates, `PDO_SM_Submissions::insert()` stores a row
in `{prefix}pdo_submissions`, then the visitor is redirected back to
`?pdo_result={token}&pdo_form={type}#pdo-form-{type}`. The theme reads
`pdo_form_result( $type )` → `{ form, success, message, errors{}, values{} }`
and re-renders the form with the errors and the previously entered values.

> **Deliberate deviation:** submissions live in a custom table rather than a
> CPT. Thousands of enquiries would bloat `wp_posts`, slow every admin query
> and scatter personal data across `wp_postmeta`. One table keeps the inbox
> fast and makes export/purge a single operation.

### Bilingual

`PDO_SM_I18n` resolves the language once per request: `?lang=bn` → `pdo_lang`
cookie → configured default. It then exposes `field()`, `meta()`,
`post_title()`, `post_content()`, `post_excerpt()`, `string()` and
`switch_url()`, adds `hreflang` + `x-default` to `wp_head`, rewrites
`<html lang>` and adds a `pdo-lang-{code}` body class.

`lang` is deliberately **not** registered as a public query var: that would
make `/?lang=bn` a non-empty query and stop WordPress from substituting the
static front page.

## Theme

### Template map

| Request | Template |
| --- | --- |
| Front page | `front-page.php` → `template-parts/sections/home-*.php` |
| The seven pages | `page-{slug}.php` → `templates/page-{slug}.php` → `template-parts/pages/{key}.php` |
| News archive / news taxonomies | `archive-pdo_news.php`, `taxonomy-pdo_news_cat.php`, `taxonomy-pdo_news_tag.php` |
| Other CPT archives & taxonomies | `archive.php` (dispatches to the right card) |
| CPT singles | `single-pdo_{type}.php` |
| Blog, search, 404 | `index.php`, `search.php`, `404.php` |

Note the **underscore** in `single-pdo_news.php` — WordPress builds the
filename from the post-type slug, which is `pdo_news`.

Root `page-{slug}.php` files are three-line wrappers around
`templates/page-{slug}.php`; only the file in `templates/` carries a
`Template Name:` header, so each template appears once in the page-attributes
dropdown while the slug-based route still works for a freshly created page.

### Rendering helpers

`inc/template-functions.php` and `inc/sections.php` provide the vocabulary every
template part uses:

```php
pdo_modern_section_open( [ 'id', 'class', 'background', 'visibility',
                           'tag', 'label', 'container', 'style' ] );
pdo_modern_section_header( [ 'eyebrow', 'title', 'description',
                             'align', 'id', 'icon', 'level' ] );
pdo_modern_button( $label, $url, [ 'style', 'icon', 'class', 'attrs' ] );
pdo_modern_render_card( $post );      // post type → card component
pdo_modern_donut_chart( $data, $args );
pdo_modern_dynamic_sections( $location, $placement );
```

`pdo_modern_button()` and `pdo_modern_section_header()` no-op on empty input, so
templates do not need defensive `if` blocks around optional settings.

### Design system

`inc/customizer/customizer-css.php` turns every Customizer setting into a CSS
custom property on `:root` (`--pdo-emerald`, `--pdo-container`,
`--pdo-section-space`, `--pdo-radius`, `--pdo-font-heading`, …).
`assets/css/main.css` consumes only those variables — it never hardcodes a
brand colour or a pixel size that an editor can change.

Colour changes use `postMessage` so the Customizer preview updates live without
a refresh; structural changes use selective refresh partials targeting
`#pdo-section-{key}`.

### Front-end JavaScript

One file, `assets/js/main.js`, ~700 lines, no dependencies, no jQuery. It is
entirely attribute-driven, so markup and behaviour stay decoupled:

| Attribute | Behaviour |
| --- | --- |
| `[data-pdo-sticky]` | sticky header, toggles `.is-stuck` |
| `[data-pdo-drawer]` / `-toggle` / `-close` | mobile drawer with focus trap |
| `[data-pdo-search-toggle]` | search panel |
| `[data-pdo-reveal]` | IntersectionObserver fade-in |
| `[data-pdo-count]` | animated counters |
| `[data-pdo-bar]` | animated progress bars |
| `[data-pdo-tabs]` | ARIA tablist |
| `[data-pdo-filter-group]` | client-side taxonomy filtering |
| `[data-pdo-prefill]` | "Adopt a project" → pre-fills and focuses the form |
| `[data-pdo-map-src]` | lazy iframe map (loads only on interaction) |
| `[data-pdo-totop]` | back-to-top button |

Everything degrades: without JavaScript the menu is still reachable, the forms
still submit, the filters simply show all items.

## Performance & accessibility notes

* Two CSS files and one JS file on the front end; no framework, no CDN.
* The emoji detection script is dequeued; `wp-block-library` is **not** (that
  would break Gutenberg blocks inside page content).
* Icons are inline SVG from a PHP registry — no icon font, no sprite request.
* Images go through `PDO_SM_Media::image_tag()`, which emits `srcset`,
  `loading="lazy"` and an explicit `alt`, and returns an empty string rather
  than a broken `<img>` when the attachment is gone.
* Landmarks, a skip link, visible focus rings, `aria-expanded` on every
  disclosure, `aria-current` on the active menu item, and a `prefers-reduced-motion`
  guard around all animation.
