=== PDO Website Manager ===
Contributors: peoplesdevelopmentorganisation
Tags: nonprofit, ngo, content management, bilingual, bangladesh
Requires at least: 6.4
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

All of the content, settings and bilingual copy behind the Peoples Development
Organisation website — managed from one dashboard, with no page builder.

== Description ==

PDO Website Manager is the content engine for the Peoples Development Organisation
(PDO) website. It owns the data; the companion **PDO Modern** theme owns the
presentation. Either one can be replaced without losing the other.

Everything an editor needs lives under a single **PDO Manager** menu:

* **Organisation** – legal identity, registration, head office, contact
  channels, social profiles, map coordinates and the bilingual switch.
* **Homepage, About, Impact, Transparency, Media, Get Involved, Contact** –
  one screen per public page, with section toggles, headings, rich text,
  repeaters (stats, pillars, milestones, values, tiers, stages, FAQs …) and
  per-section visibility.
* **Translations** – every interface string in English and Bengali.
* **Submissions** – every form entry, filterable, exportable to CSV.
* **Reorder** – drag the homepage sections and custom sections into place.
* **Setup Wizard** – seeds a complete, realistic PDO site in one click.
* **Tools** – import/export the whole configuration as JSON, reset a group,
  rebuild pages and menus.

= Content types =

Seven custom post types carry the structured records:

* `pdo_program` – flagship programmes (stats, achievements, gallery, documents)
* `pdo_team` – people (designation, photo, contact, socials)
* `pdo_partner` – partners and networks (logo, website, category)
* `pdo_story` – human impact stories (beneficiary, quote, location)
* `pdo_document` – policies, reports and audits (file or external URL, year)
* `pdo_news` – news, field action and thought leadership
* `pdo_section` – reusable custom sections placed anywhere on the site

Each has its own taxonomy for grouping, and every text field has a Bengali twin.

= Bilingual =

The plugin ships a lightweight runtime bilingual layer. Every field stored by
the plugin can hold an English value and a `_bn` value; the active language is
resolved from `?lang=bn`, then a `pdo_lang` cookie, then the configured default.
`hreflang` and `x-default` tags are emitted automatically. No second site, no
duplicate posts, no external translation plugin.

= Forms =

Three built-in form types (contact, volunteer, partner) post to
`admin-post.php`, are nonce-protected, honeypot-protected, timing-protected and
rate-limited (5 submissions per 10 minutes per visitor). Entries are stored in a
dedicated `{prefix}pdo_submissions` table and optionally emailed to the
notification address. If mail delivery fails the entry is still saved and the
visitor is told so.

= Template API =

Themes read plugin data through ~30 guarded global functions
(`pdo_setting()`, `pdo_text()`, `pdo_rows()`, `pdo_get_posts()`, `pdo_meta()`,
`pdo_document_file()`, `pdo_share_links()`, `pdo_t()` …). Every one of them is
wrapped in `function_exists()`, so a theme that calls them still renders if the
plugin is deactivated.

== Installation ==

1. Upload the `pdo-site-manager` folder to `/wp-content/plugins/`, or install
   the ZIP through **Plugins → Add New → Upload Plugin**.
2. Activate the plugin through the **Plugins** screen.
3. Go to **PDO Manager → Setup Wizard** and run the seeder. It creates the
   seven pages, the navigation menus, the demo records and the default
   settings, and sets the static front page.
4. Install and activate the **PDO Modern** theme.
5. Visit **Appearance → Customize** to adjust colours, typography and layout.

== Frequently Asked Questions ==

= Does it work with any theme? =

Yes, but you only get the full PDO layout with the PDO Modern theme. With any
other theme the custom post types, taxonomies, settings and forms still work;
you would call the template API from your own templates.

= Where are form submissions stored? =

In a custom database table, `{prefix}pdo_submissions`, not in the posts table.
This keeps thousands of enquiries out of `wp_posts`, makes the Submissions
screen fast, and keeps personal data in one place that can be exported or
purged in a single operation. The table is removed on uninstall only when
**Delete all data on uninstall** is enabled under **Tools**.

= How do I translate the interface? =

**PDO Manager → Translations**. Every string used by the plugin and the theme
is listed with an English and a Bengali column. Add new keys with the repeater;
themes read them with `pdo_t( 'your_key', 'English fallback' )`.

= Can I move the site to another install? =

**PDO Manager → Tools → Export** produces a single JSON file containing every
settings group. Import it on the new install, then run the Setup Wizard to
recreate pages and menus.

= What happens if I deactivate the plugin? =

Content stays in the database. The front end falls back to the theme's own
defaults because every template API call is guarded. Nothing fatals.

== Screenshots ==

1. The PDO Manager overview with content counts and quick actions.
2. The Homepage settings screen with section toggles and repeaters.
3. The Translations screen – every string in English and Bengali.
4. The Submissions screen with filtering and CSV export.

== Changelog ==

= 1.0.0 =
* Initial release.
* Nine settings groups with 37 tabs covering organisation, all six inner pages
  and the homepage.
* Seven custom post types, seven taxonomies, per-type meta boxes.
* Reusable custom sections with 21 section types and 9 placement locations.
* Bilingual runtime (English / Bengali) with hreflang output.
* Three hardened form types with a dedicated submissions table and CSV export.
* Setup wizard, JSON import/export, REST read endpoints.
* ~30 guarded template API functions and ~80 inline SVG icons.

== Upgrade Notice ==

= 1.0.0 =
First public release.
