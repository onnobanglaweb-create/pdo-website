# Template API

Everything a theme may call. All of these are plain global functions declared
by the plugin inside `function_exists()` guards, so a theme can call them
unconditionally. `pdo-modern/inc/compatibility.php` declares a safe fallback
for every one of them, which is what keeps the front end alive when the plugin
is deactivated.

Source: `pdo-site-manager/includes/template-api.php`,
`…/class-i18n.php`, `…/class-icons.php`.

---

## Settings

### `pdo_settings( string $group ): array`
The whole settings group as a raw array. Groups: `org`, `home`, `about`,
`impact`, `transparency`, `media`, `involved`, `contact`, `i18n`.

### `pdo_setting( string $group, string $field, mixed $default = '' ): mixed`
One raw value. No bilingual fallback — use for numbers, checkboxes, IDs,
colours, URLs.

```php
$columns = (int) pdo_setting( 'about', 'leadership_columns', 4 );
$enabled = pdo_setting( 'org', 'map_enabled', true );
```

### `pdo_text( string $group, string $field, string $default = '' ): string`
One value **with the bilingual fallback applied**. Use for anything a visitor
reads. In Bengali mode it returns `{field}_bn` when that is non-empty,
otherwise the English value.

```php
echo esc_html( pdo_text( 'home', 'hero_title', '' ) );
```

### `pdo_org( string $field = '', mixed $default = '' ): mixed`
Shortcut for the `org` group. With no argument returns the whole group.

```php
echo esc_html( pdo_org( 'name', get_bloginfo( 'name' ) ) );
```

### `pdo_section_enabled( string $group, string $prefix ): bool`
Reads the `{prefix}_enabled` toggle in a group. Returns `true` when the key
does not exist, so new sections are visible by default.

```php
if ( pdo_section_enabled( 'about', 'president' ) ) { … }
```

### `pdo_page_url( string $key ): string`
Permalink of one of the seven managed pages. Keys: `home`, `about`, `impact`,
`transparency`, `media`, `involved`, `contact`. Returns `''` if the page is
missing.

---

## Repeaters

### `pdo_rows( string $group, string $field, int $limit = 0 ): array`
Visible rows of a repeater, sorted by their `order` value. Rows flagged
invisible are dropped. `$limit = 0` means all.

```php
foreach ( pdo_rows( 'home', 'stats', 4 ) as $row ) { … }
```

### `pdo_row_text( array $row, string $field, string $default = '' ): string`
One field of a repeater row with the bilingual fallback.

```php
echo esc_html( pdo_row_text( $row, 'label' ) );
```

### `pdo_visible_rows( array $rows ): array`
Filter + sort an arbitrary array of rows the same way `pdo_rows()` does. Useful
for repeaters you fetched yourself (for example from post meta).

---

## Posts

### `pdo_get_posts( string $post_type, array $args = [] ): WP_Post[]`
Published records of a PDO post type, ordered by the `_pdo_order` meta then by
menu order. `$args` is merged into the `WP_Query` arguments.

```php
$programs = pdo_get_posts( 'pdo_program', [ 'posts_per_page' => 6 ] );
```

### `pdo_post_title( int|WP_Post $post ): string`
### `pdo_post_content( int|WP_Post $post ): string`
### `pdo_post_excerpt( int|WP_Post $post ): string`
Title / content / excerpt with the bilingual fallback. `pdo_post_content()`
runs the `the_content` filters, so it is safe to echo unescaped.

### `pdo_meta( int|WP_Post $post, string $key, mixed $default = '' ): mixed`
A `_pdo_{key}` meta value **with** the bilingual fallback.

### `pdo_raw_meta( int|WP_Post $post, string $key, mixed $default = '' ): mixed`
The same value **without** the fallback — for URLs, colours, booleans, IDs.

### `pdo_meta_rows( int|WP_Post $post, string $key ): array`
A repeater stored in post meta (`stats`, `achievements`, `gallery`,
`documents`, `socials`), already filtered and sorted.

---

## Media

### `pdo_image( int $id, string $fallback_url = '', array $args = [] ): string`
A complete `<img>` tag, or `''` when nothing can be resolved — never a broken
image. `$args`: `size` (default `pdo-card`), `class`, `alt`, `loading`
(default `lazy`), `style`.

```php
echo pdo_image( (int) pdo_setting( 'about', 'history_image', 0 ), '', [
    'size' => 'full',
    'alt'  => pdo_text( 'about', 'history_title' ),
] );
```

Registered sizes: `pdo-card` (768×512), `pdo-thumb` (320×320),
`pdo-portrait` (480×640).

### `pdo_row_image( array $row, array $args = [], string $prefix = 'image' ): string`
Image of a repeater row; reads `{prefix}` (attachment ID), falls back to
`{prefix}_url`, then to `alt`.

### `pdo_post_image( int|WP_Post $post, array $args = [], string $prefix = 'image' ): string`
Image of a post; reads the featured image, then `_pdo_{prefix}`, then
`_pdo_{prefix}_url`. Pass `'photo'` for team members and stories, `'logo'` for
partners.

---

## Domain helpers

### `pdo_document_file( int|WP_Post $post ): array`
`[ 'id', 'url', 'size', 'mime', 'ext' ]` for an uploaded file, or the external
URL when `_pdo_external_url` is set. Empty array when there is no file.

### `pdo_scholarship_chart(): array`
`[ 'rows' => [ [ 'label', 'percentage', 'count_label', 'description', 'value',
'color', 'share' ] ], 'total' => int ]`, built from the `impact.tiers`
repeater. Feed it straight to `pdo_modern_donut_chart()`.

### `pdo_team_groups(): array`
Team members grouped by the `pdo_team_group` taxonomy, in term order:
`[ [ 'term' => WP_Term, 'members' => WP_Post[] ] ]`.

### `pdo_news_categories(): WP_Term[]`
Non-empty `pdo_news_cat` terms, for the news filter bar.

### `pdo_related_news( int $post_id, int $number = 3 ): WP_Post[]`
News items sharing a category with the given post.

### `pdo_share_links( int $post_id ): array`
`[ 'facebook' => [ 'url', 'icon', 'label' ], 'x' => …, 'linkedin' => …,
'whatsapp' => …, 'email' => … ]`.

### `pdo_sections( string $location, string $placement = 'after' ): array`
Prepared dynamic sections for a location. See
[`ARCHITECTURE.md`](ARCHITECTURE.md#dynamic-sections) for the payload shape.

---

## Forms

### `pdo_form_config( string $type ): array`
The configured fields for `contact`, `volunteer` or `partner`. Each row:
`name`, `label` (+`_bn`), `type`, `placeholder` (+`_bn`), `required`,
`options` (+`_bn`), `width` (`full` | `half`).

### `pdo_form_field_name( array $field, int $index ): string`
The `name` to put inside `pdo_field[…]`. Falls back to a stable
`field_{index}` when the editor left the name blank.

### `pdo_form_field_choices( array $field ): array`
`value => label` pairs for `select` / `radio` fields, already translated.

### `pdo_form_result( string $type ): array`
After a redirect back from `admin-post.php`:
`[ 'form', 'success', 'message', 'errors' => [ name => message ],
'values' => [ name => value ] ]`. Empty array when there is nothing to show.

---

## Language & icons

### `pdo_lang(): string`
The active language code, `en` or `bn`.

### `pdo_t( string $key, string $fallback = '' ): string`
An interface string from **PDO Manager → Translations**, in the active
language. Always pass an English fallback so the theme works before the strings
are seeded.

```php
echo esc_html( pdo_t( 'read_more', __( 'Read more', 'pdo-modern' ) ) );
```

Keys shipped by default include `nav_home`, `nav_about`, `nav_impact`,
`nav_transparency`, `nav_media`, `nav_involved`, `nav_contact`, `cta_join`,
`read_more`, `read_policy`, `download`, `preview`, `close`, `all`, `search`,
`search_placeholder`, `quick_links`, `flagship_programs`, `connect`,
`required_field`, `invalid_email`, `form_error`, `spam_error`, `rate_error`,
`no_results`, `posted_on`, `share`, `related`, `back_to_news`, `view_program`,
`skip_to_content`, `menu`, `language`, `no_document`, `load_more`,
`back_to_top`.

### `pdo_icon( string $name, array $args = [] ): void`
Echo an inline SVG from the ~80-icon registry. `$args`: `size` (default 20),
`class`, `stroke`, `title`.

### `pdo_get_icon( string $name, array $args = [] ): string`
The same markup returned instead of echoed. Returns `''` for an unknown name.

---

## Adding your own

Declare the function in the plugin inside a guard, then mirror it with a
fallback in the theme:

```php
// pdo-site-manager/includes/template-api.php
if ( ! function_exists( 'pdo_my_thing' ) ) {
    function pdo_my_thing( $arg = '' ) {
        return PDO_SM_Whatever::my_thing( $arg );
    }
}

// pdo-modern/inc/compatibility.php
if ( ! function_exists( 'pdo_my_thing' ) ) {
    function pdo_my_thing( $arg = '' ) {
        return '';
    }
}
```

Never call a `PDO_SM_*` class directly from a template — that is the one thing
that will fatal the site when the plugin is off.
