# Development

## Local setup

Any WordPress install works. The fastest route:

```bash
# from a clean WordPress checkout
ln -s /path/to/pdo-website/pdo-site-manager wp-content/plugins/pdo-site-manager
ln -s /path/to/pdo-website/pdo-modern       wp-content/themes/pdo-modern
```

> Symlinks are convenient but some PHP runtimes (and the php-wasm harness used
> while building this project) do not follow them. If templates or assets
> appear to be missing, copy the folders instead of linking them.

Then: activate the plugin, activate the theme, run **PDO Manager → Setup
Wizard**, set permalinks to **Post name**.

Useful `wp-config.php` flags while developing:

```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_DISPLAY', true );
define( 'SCRIPT_DEBUG', true );
```

## Coding standards

* **WordPress Coding Standards** for PHP. Tabs for indentation, Yoda
  conditions, `snake_case` functions, one class per file named
  `class-{slug}.php`.
* Every file starts with `defined( 'ABSPATH' ) || exit;`.
* Prefixes are mandatory: `pdo_sm_` / `PDO_SM_` in the plugin,
  `pdo_modern_` in the theme. Template-API functions are the deliberate
  exception and use the bare `pdo_` prefix.
* Escape at output: `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()`.
  Where a helper has already escaped its return value, add a
  `// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped` with a
  reason.
* Sanitise at input: `sanitize_text_field()`, `sanitize_key()`,
  `absint()`, `esc_url_raw()`, `wp_kses_post()`.
* Nonces on every write, capability checks on every admin screen
  (`pdo_manage_content`, `pdo_manage_submissions`).
* Translate every user-facing string with the right text domain
  (`pdo-site-manager` / `pdo-modern`).

### Front-end rules

* No jQuery, no framework, no CDN, no build step.
* One stylesheet (`assets/css/main.css`) plus `print.css`; one script
  (`assets/js/main.js`).
* CSS reads design tokens from custom properties only. If you find yourself
  typing `#0d7a53`, add a Customizer setting instead.
* JavaScript is attribute-driven (`data-pdo-*`) and must degrade: the page has
  to work with scripting disabled.

## Adding things

### A settings field

1. Add it to the right tab/panel in `includes/class-schema.php`.
2. Nothing else. The admin UI, sanitisation, defaults, export and the
   Translations table are all generated from the schema.
3. Read it in a template with `pdo_text()` (visitor-facing) or
   `pdo_setting()` (structural).

### A dynamic-section type

1. Register the type in `includes/class-sections.php`.
2. Create `pdo-modern/template-parts/sections/dynamic-{type}.php`.
3. Start it with:

```php
$pdo_section = pdo_modern_section_args( isset( $args ) ? $args : array() );
if ( ! $pdo_section ) {
    return;
}
pdo_modern_dynamic_open( $pdo_section );
pdo_modern_dynamic_header( $pdo_section );
// … your markup …
pdo_modern_section_close();
```

Unknown types fall through to `dynamic-fallback.php`, so a half-finished type
never breaks a page.

### A custom post type

Add it to `includes/class-content-types.php`, its meta box to
`class-meta-boxes.php`, a card at
`pdo-modern/template-parts/components/card-{slug}.php`, and register the
mapping in `pdo_modern_card_slug()` (or hook the `pdo_modern_card_map` filter
from a child theme). The single template must be named after the **post-type
slug**, underscores included: `single-pdo_thing.php`.

### A template-API function

Declare it inside `function_exists()` in
`includes/template-api.php` **and** add a matching fallback in
`pdo-modern/inc/compatibility.php`. Templates must never call a `PDO_SM_*`
class directly.

## Hooks

### Plugin

| Hook | Type | Signature |
| --- | --- | --- |
| `pdo_sm_settings_schema` | filter | `( array $schema )` — add or change settings groups, tabs, panels and fields |
| `pdo_sm_get_group` | filter | `( array $values, string $group )` — intercept a whole settings group on read |
| `pdo_sm_icons` | filter | `( array $icons )` — register extra inline SVG icons |
| `pdo_sm_form_fields` | filter | `( array $fields, string $type )` — change the fields of a form type |
| `pdo_sm_submission_data` | filter | `( array $row, string $type, array $values )` — change the row before it is stored |
| `pdo_sm_submission_stored` | action | `( int $id, string $type, array $pretty )` — fires after a successful save |
| `pdo_sm_manage_capability` | filter | `( string $cap )` — capability required for the content screens |
| `pdo_sm_submissions_capability` | filter | `( string $cap )` — capability required for the inbox |

### Theme

| Hook | Type | Signature |
| --- | --- | --- |
| `pdo_modern_pre_render_section` | filter | `( bool $skip, array $section, string $type )` — return true to suppress a dynamic section |
| `pdo_modern_card_map` | filter | `( array $map, string $type )` — post type → card component |
| `pdo_modern_dated_post_types` | filter | `( array $types )` — which post types show a publish date |
| `pdo_modern_home_section_order` | filter | `( array $order )` — the home-page section stack |
| `pdo_modern_css_variables` | filter | `( array $vars )` — the `:root` custom properties |
| `pdo_modern_after_setup_theme` | action | fires at the end of the theme's `after_setup_theme` |

## Testing checklist

There is no automated suite; this is the manual pass used for every change.

**Rendering** — all seven pages, the three CPT archives, one single of each of
the six CPTs, the taxonomy archives, search, 404, and `?lang=bn`. None may emit
a PHP notice with `WP_DEBUG` on.

**Admin** — all fourteen screens, every settings tab, a save round-trip
(including a `<script>` payload in a repeater to confirm sanitisation).

**Forms** — submit each of the three forms; confirm the row lands in
`{prefix}pdo_submissions`, the success alert renders, validation errors
repopulate the form, the honeypot rejects a filled `pdo_website`, and the
rate limiter trips after five submissions.

**Responsive** — 360, 768, 1024, 1440 px. Drawer, sticky header, tables.

**Accessibility** — keyboard-only pass over the menu, drawer, tabs, filters and
forms; check focus visibility, `aria-expanded` state and heading order.

**Resilience** — deactivate the plugin: the front end must still render.
Re-activate: everything returns.

A quick syntax gate over every file:

```bash
find pdo-site-manager pdo-modern -name '*.php' -exec php -l {} \; | grep -v 'No syntax errors'
```

## Translation templates

Both packages ship a `.pot` under their own `languages/` folder. Regenerate
them whenever you add or change a translatable string:

```bash
cd /path/to/pdo-website
python3 tools/makepot.py pdo-site-manager pdo-site-manager \
        pdo-site-manager/languages/pdo-site-manager.pot "PDO Website Manager 1.0.0"
python3 tools/makepot.py pdo-modern pdo-modern \
        pdo-modern/languages/pdo-modern.pot "PDO Modern 1.0.0"
```

`tools/makepot.py` is a dependency-free extractor (no WP-CLI needed). It
understands `__ _e _x _ex _n _nx` and their `esc_html`/`esc_attr` variants,
picks up `/* translators: … */` comments and filters by text domain. Current
counts: **724** plugin strings, **313** theme strings.

This is for the **interface chrome** only. Editor-supplied Bengali copy lives
in the plugin's own bilingual repeaters — see `docs/EDITOR-GUIDE.md`.

## Releasing

```bash
cd /path/to/pdo-website
rm -rf dist && mkdir -p dist
zip -rq dist/pdo-site-manager.zip pdo-site-manager -x '*.DS_Store' '*/.git/*'
zip -rq dist/pdo-modern.zip       pdo-modern       -x '*.DS_Store' '*/.git/*'
```

Both archives unpack to a single correctly named folder, so they install
through **Plugins → Add New → Upload** and **Appearance → Themes → Add New →
Upload** without any renaming.

Bump the version in three places and keep them in step:

* `pdo-site-manager/pdo-site-manager.php` → `Version:` and `PDO_SM_VERSION`
* `pdo-site-manager/readme.txt` → `Stable tag:` and the changelog
* `pdo-modern/style.css` → `Version:`

Bump `PDO_SM_DB_VERSION` only when the submissions table changes;
`PDO_SM_Plugin::maybe_upgrade()` runs `dbDelta()` on the next load.
