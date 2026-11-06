# Customizer reference

**Appearance → Customize.** Eight PDO panels sit above the WordPress defaults.
Every setting ends up as a CSS custom property on `:root`, so nothing in
`assets/css/main.css` hardcodes a brand colour or a size an editor can change.

Colour settings use `postMessage` (live preview, no refresh). Structural
settings use selective refresh against `#pdo-section-{key}`.

---

## 1. Identity (`pdo_identity`)

| Setting | Default | Notes |
| --- | --- | --- |
| `pdo_logo_width` | 180 | px, desktop |
| `pdo_logo_height` | 56 | px, max |
| `pdo_logo_mobile` | 140 | px |
| `pdo_logo_footer` | 160 | px |
| `pdo_gradient_enabled` | on | emerald → teal brand gradient |

The logo image itself is the core **Site Identity → Logo** setting.

## 2. Colours (`pdo_colors`)

~40 tokens, each exposed as `--pdo-{token}` plus `--pdo-{token}-rgb`:

```
emerald  dark  light  accent  gold  teal  navy  slate
body-bg  text  heading  muted  link  link-hover
surface  surface-alt  surface-tint  border  border-strong  focus
header-bg  header-text  topbar-bg  topbar-text  dropdown-bg  nav-hover
footer-bg  footer-text  footer-heading  footer-link-hover
success  error  info  warning  text-on-dark
```

Derived automatically: `--pdo-emerald-soft`, `--pdo-emerald-hover`,
`--pdo-accent-hover`, `--pdo-overlay`, `--pdo-gradient`.

Brand defaults: emerald `#0d7a53`, dark `#084c34`, light `#e8f5ee`,
accent `#d97706`, gold `#f59e0b`, teal `#14b8a6`, navy `#0f172a`,
slate `#334155`.

## 3. Typography (`pdo_typography`)

| Setting | Default | CSS variable |
| --- | --- | --- |
| `pdo_font_body` | Inter | `--pdo-font-body` |
| `pdo_font_heading` | Inter | `--pdo-font-heading` |
| `pdo_font_bengali` | Hind Siliguri | `--pdo-font-bengali` |
| `pdo_font_size_base` | 16 | `--pdo-font-size` |
| `pdo_font_scale` | 1.2 | drives `--pdo-text-xs … --pdo-text-4xl` |
| `pdo_line_height` | 1.65 | `--pdo-line-height` |
| `pdo_heading_weight` | 800 | `--pdo-heading-weight` |
| `pdo_heading_tracking` | −2 | `--pdo-heading-track` (÷100 em) |
| `pdo_bengali_scale` | 1.05 | `--pdo-bengali-scale` |
| `pdo_uppercase_eyebrows` | on | `--pdo-eyebrow-case` |

`pdo_use_bundled_fonts` (on by default) controls the self-hosted webfont
stylesheet. The repository ships `assets/fonts/fonts.css` but **not** the
`.woff2` binaries, so the theme detects that none are present and skips the
stylesheet and the preload hints entirely — the browser never chases a 404 and
the site renders in the system font stack. Drop the files into
`pdo-modern/assets/fonts/` (names are listed in `assets/fonts/README.md`) and
they are picked up automatically. Nothing is ever requested from Google Fonts.

## 4. Layout (`pdo_layout`)

| Setting | Values | CSS variable |
| --- | --- | --- |
| `pdo_layout_width` | `narrow` 1100 / `wide` 1280 / `xwide` 1440 / `full` 100% | `--pdo-container` |
| `pdo_container_padding` | px | `--pdo-gutter` |
| `pdo_section_spacing` | px | `--pdo-section-space` |
| `pdo_card_gap` | px | `--pdo-gap` |
| `pdo_radius` | px | `--pdo-radius` (+`-sm`, `-lg`, `-pill`) |
| `pdo_shadow_strength` | 0–100 | `--pdo-shadow-sm`, `--pdo-shadow`, `--pdo-shadow-lg`, `--pdo-shadow-brand` |

Toggles: `pdo_rounded_corners`, `pdo_animations`, `pdo_back_to_top`,
`pdo_breadcrumbs`, `pdo_disable_emojis`.

Blog: `pdo_blog_layout`, `pdo_blog_sidebar` (`left` / `right` / `none`),
`pdo_blog_columns`, `pdo_excerpt_length`.

## 5. Header (`pdo_header`)

Top bar: `pdo_topbar_enabled`, `pdo_topbar_text`, `pdo_topbar_phone`,
`pdo_topbar_email`, `pdo_topbar_socials`, `pdo_topbar_language`.

Header: `pdo_header_layout`, `pdo_header_sticky`, `pdo_header_shrink`,
`pdo_header_search`, `pdo_header_border`, `pdo_header_cta_enabled`,
`pdo_header_cta_label`, `pdo_header_cta_url`, `pdo_header_cta_style`.

## 6. Navigation (`pdo_navigation`)

`pdo_nav_style`, `pdo_nav_font_size` (`--pdo-nav-size`), `pdo_nav_gap`
(`--pdo-nav-gap`), `pdo_nav_uppercase` (`--pdo-nav-case`), `pdo_nav_icons`,
`pdo_nav_caret`, `pdo_nav_mega`, `pdo_mobile_breakpoint`
(`--pdo-breakpoint-nav`), `pdo_mobile_style`, `pdo_mobile_cta`.

Menu locations: `primary`, `topbar`, `footer`, `footer_programs`, `legal`.

## 7. Footer (`pdo_footer`)

`pdo_footer_columns` (1–5) and one toggle per block: `pdo_footer_about`,
`pdo_footer_badge`, `pdo_footer_nav`, `pdo_footer_programs`,
`pdo_footer_contact`, `pdo_footer_socials`, `pdo_footer_newsletter`,
`pdo_footer_credit`.

Widget area: **Footer Extra** (`footer-extra`) renders in the last column.

## 8. Homepage sections (`pdo_sections`)

Built-in sections, in default order:

```
hero  stats  pillars  toc  president  gallery  partners  cta
```

* `pdo_home_order` — a comma-separated list. Drag the rows in the custom
  sortable control; a leading `_` marks a section hidden.
* `pdo_section_hide_{key}` — `''` | `mobile` | `tablet` | `desktop` | `all`,
  rendered as `.pdo-hide-{…}` on the section wrapper.

Each section's **content** comes from **PDO Manager → Homepage**, not from the
Customizer. The Customizer only controls order, visibility and styling.

---

## Custom controls

`inc/customizer/` adds three controls beyond the core set:

| Control | DOM | Behaviour |
| --- | --- | --- |
| Range | `.pdo-range` → `__slider`, `__output`, `__reset[data-default]` | numeric slider with a live read-out and a reset button |
| Sortable | `ul.pdo-sortable[data-pdo-sortable]` → `li.pdo-sortable__item[data-slug]` with `__handle`, `__toggle`, `__value` | drag or ArrowUp/ArrowDown to reorder, checkbox to hide |
| Heading | `.pdo-customize-heading` | a non-setting section divider |

Sanitisers: `pdo_modern_sanitize_checkbox`, `…_float`, `…_font`, `…_order`,
`…_visibility`.

## Reading the variables from your own CSS

```css
.my-block {
    background: var(--pdo-surface-tint);
    color: var(--pdo-text);
    border-radius: var(--pdo-radius);
    padding: var(--pdo-gutter);
    box-shadow: var(--pdo-shadow);
    font-family: var(--pdo-font-body);
}
```

A child theme that only overrides variables will track every Customizer change
automatically.
