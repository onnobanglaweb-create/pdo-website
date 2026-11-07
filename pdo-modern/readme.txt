=== PDO Modern ===
Contributors: peoplesdevelopmentorganisation
Requires at least: 6.2
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.3
License: GNU General Public License v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Tags: non-profit, education, custom-colors, custom-logo, custom-menu, featured-images, threaded-comments, translation-ready, block-styles, wide-blocks, rtl-language-support, accessibility-ready, two-columns, right-sidebar, full-width-template

A fast, accessible, bilingual classic theme for the Peoples Development
Organisation. Presentation only — all content comes from the PDO Website
Manager plugin.

== Description ==

PDO Modern is the front end for the Peoples Development Organisation website.
It is deliberately a *presentation-only* theme: every headline, statistic,
programme, policy document and form field is owned by the companion **PDO
Website Manager** plugin, so switching themes never destroys your content.

**What it gives you**

* A complete template hierarchy — front page, six named page templates, six
  custom post type singles, archives, taxonomy listings, search, 404 and
  comments.
* Twenty-two renderers for the plugin's dynamic sections, so editors can
  assemble a page from hero, statistics, timeline, testimonials, charts,
  galleries, forms and more without touching code.
* A Customizer design system: eight panels, roughly forty colour tokens,
  typography, layout width, header and footer behaviour, home-section ordering
  and per-breakpoint visibility. Everything is emitted as CSS custom
  properties, so a colour change is one variable, not a stylesheet rebuild.
* Hand-written CSS — no framework, no build step, no CDN. The design system is
  a single 3,900-line stylesheet plus small print, editor and Customizer files.
* Bilingual English / Bengali rendering driven by the plugin's language
  runtime, including a Bengali font stack and a per-language type scale.

**Accessibility**

Landmarks and a skip link on every view, a visible focus ring that respects
`prefers-reduced-motion`, correct heading order (card titles shift between
`h2` and `h3` depending on context), keyboard-operable tabs with roving
`tabindex`, labelled form controls and `role="alert"` on form results.

**Performance**

Between twenty and forty database queries per page. No jQuery on the front
end. Scripts are deferred, images are lazy-loaded below the fold, and the
theme ships no remote requests of any kind.

== Installation ==

1. Install and activate **PDO Website Manager** first. The theme renders
   almost nothing without it.
2. Upload `pdo-modern.zip` through **Appearance → Themes → Add New → Upload
   Theme**, then activate.
3. Run **PDO Manager → Setup** to create the pages, menus and demo content.
4. Go to **Settings → Permalinks** and press *Save Changes* to flush the
   rewrite rules for the custom post types.

== Frequently Asked Questions ==

= What happens if I deactivate the plugin? =

The theme keeps working. `inc/compatibility.php` declares a guarded fallback
for all thirty plugin API functions, so pages render with WordPress core
content instead of fataling. You lose the PDO content, not the site.

= Where do I change colours? =

**Appearance → Customize → PDO Colours.** Every token becomes a CSS custom
property on `:root`; see `docs/CUSTOMIZER.md` for the full list and the
variable each control writes.

= Why are my fonts falling back to the system stack? =

The theme ships `assets/fonts/fonts.css` but not the `.woff2` binaries, for
licensing and repository-size reasons. Drop the files listed in
`assets/fonts/README.md` into that folder and the theme picks them up
automatically. Until then it skips the stylesheet entirely rather than
requesting files that would 404.

= Can I use the block editor? =

Yes. Editor styles, wide and full alignments, responsive embeds and block
styles are all registered, and `assets/css/editor.css` mirrors the front-end
typography so the editor preview matches the published page.

= How do I add a new section type? =

Register it in the plugin's `PDO_SM_Sections::types()`, then add
`template-parts/sections/dynamic-<key>.php` to the theme. The dispatcher picks
it up automatically and falls back to `dynamic-fallback.php` if it is missing.
The full recipe is in `docs/DEVELOPMENT.md`.

== Changelog ==

= 1.0.3 =
* Hardening: Customizer registration no longer assumes the core site-title/tagline settings exist. Assigning a property on a missing setting is a fatal error on PHP 8, so both lookups are now guarded (behaviour unchanged when the settings are present, as in a stock Customizer).

= 1.0.2 =
* Fix: global dynamic sections ("Every page (above content)" and "Every page (below content)") never appeared — the header did not render them at all and the footer rendered only one of the two placements. The header now renders both Global Top placements and the footer both Global Bottom placements, exactly once per page.
* Fix: removed a duplicate Global Top call from the generic page template that would have rendered those sections twice.
* The asset version constant now matches the released theme version (it previously stayed at 1.0.0, which could serve stale CSS/JS after updates).

= 1.0.1 =
* Fix: heading order on archive, taxonomy and listing pages. Card titles now use h2 directly under the page h1 and h3 inside sections, via a shared heading-level helper.
* Fix: duplicate element id when two forms shared a page (Get Involved) — each nonce field now has a unique id while keeping the field name the handler expects.
* Fix: mobile drawer close button referenced a non-existent icon.
* Added readme.txt and the GPL licence text.

= 1.0.0 =
* Initial release.
* Full template hierarchy, 22 dynamic section renderers, 7 card components.
* Customizer design system with 8 panels and ~40 colour tokens.
* Bilingual English / Bengali rendering.
* Guarded fallbacks for every plugin template-API function.
* Translation template with 313 strings.

== Upgrade Notice ==

= 1.0.3 =
Customizer hardening for PHP 8. Safe to update in place.

= 1.0.2 =
Global dynamic sections now display as configured. Safe to update in place.

= 1.0.1 =
Accessibility and markup fixes. Safe to update in place.

= 1.0.0 =
First public release.

== Copyright ==

PDO Modern is distributed under the terms of the GNU General Public License
v2 or later.

PDO Modern bundles no third-party code. The icon set, stylesheets and scripts
are original work released under the same licence as the theme.
