# Engineering Audit Report — PDO Website 1.0.4 / Theme 1.0.2

Date: 2026-11-07 · Scope: `pdo-site-manager` plugin + `pdo-modern` theme ·
Environment: WordPress 7.1.4 on PHP 8.2 (php-wasm) + SQLite, demo content seeded

This report separates what was **proven broken and fixed**, what was
**audited and verified correct**, and what was **not executed**. Nothing
below claims a test that did not actually run.

## Addendum — 1.0.5 / theme 1.0.3 (2026-11-07, second pass)

| # | Finding | Original problem | Root cause | Repair | Test | Result |
|---|---------|------------------|------------|--------|------|--------|
| 7 | Homepage restore incomplete | Restoring a homepage backup restored plugin settings but **not** the Customizer theme mods (section order/visibility) that the reset had cleared | Reset cleared mods as a side effect; backups only ever held the settings option | Backups of `home` now record `pdo_home_order` + all `pdo_section_hide_*` mods (`snapshot_home_mods()`); restore re-applies and *verifies* them, captures the current mods into the undo snapshot, and only reports success when settings **and** mods are back | 5 new integration assertions (record → clear → restore → values verified) | **Fixed, verified** |
| 8 | Rate-limit race + not configurable | The counter was only incremented *after* a successful store, so simultaneous requests could all pass an untouched counter; failed attempts were free | Check-then-increment-later flow in `PDO_SM_Forms::handle()` | New `PDO_SM_Forms::rate_limit_exceeded()` counts the attempt up front; limits tunable via the `pdo_sm_rate_limit` filter (max/window) | 3 integration assertions (allow, allow, block at max=2) | **Fixed, verified** |
| 9 | Duplicate form fields lose data | Two configured fields resolving to the same name (or label) silently overwrote each other in the stored payload | `$values`/`$pretty` keyed without collision handling | Storage keys are de-duplicated (`name_index`, `Label (2)`) while the submitted value is still read from the posted name | Code-level fix; runtime shape unchanged for well-formed forms (existing live form probes re-pass) | **Fixed** |
| 10 | Import size cap missing | The 1.0.4 changelog announced a 2 MB import cap; the shipped `import()` had no size check | The cap never made it into the code | Enforced in `PDO_SM_Exporter::import()` (`pdo_error=toobig`) with a clear Tools notice | **Live HTTP**: invalid JSON → `pdo_error=invalid`; 2.3 MB file → `pdo_error=toobig`; valid partial import → `pdo_imported_settings=1`, value applied, pre-import backup created, then restored | **Fixed, verified live** — closes finding #3 |
| 11 | Customizer PHP 8 fatal risk | `$wp_customize->get_setting( 'blogname' )->transport = …` fatals if a plugin unregisters the core setting (property assignment on null) | Unguarded dereferences in `pdo_modern_customize_register()` | Both lookups guarded (theme 1.0.3) | Integration suite boots a real `WP_Customize_Manager`: 118 theme settings registered, **every** theme setting has a callable `sanitize_callback` | **Fixed, verified** |
| 12 | Uninstall (finding #5) | Previously only code-reviewed | — | No code change in this pass | **Executed for real** against a snapshotted database: with the opt-in on, all 24 options (incl. every `pdo_sm_backup_*`), all CPT posts and the submissions table were removed; database snapshot restored and smoke suite re-passed | **Verified live** |

Also verified live in this pass: **theme without plugin** (homepage 200,
no fatals, guarded fallbacks) and **plugin without theme** (switched to
Twenty Twenty-Four: homepage 200, CPT permalinks intact, switched back
cleanly); **JS syntax** — `node --check` passes on `main.js`,
`customizer-preview.js`, `customizer-controls.js` and the plugin's
`admin.js`; **admin asset scoping** — plugin admin CSS/JS only enqueue
on PDO screens (`is_pdo_screen()` guard).

Updated totals: integration suite **49/49 assertions PASS**, smoke
**23/23**, PHP parse scan **132 files, 0 errors**. The "Not executed"
list below shrinks accordingly: settings import and uninstall are now
executed; remaining unexecuted items are real-browser/device and
screen-reader testing, PHP 7.4 runtime execution, inbox mail delivery,
and PHPCS/PHPStan (no Composer registry access in this environment —
the parse scan plus WordPress-API conventions were used instead).

## Findings and repairs

| # | Finding | Original problem | Root cause | Repair | Test | Result |
|---|---------|------------------|------------|--------|------|--------|
| 1 | Global dynamic sections invisible | Sections placed at "Every page (above content)" or "Every page (below content)" never appeared on any page, in either placement | `header.php` never called the section renderer; `footer.php` requested only the `after` placement; `page.php` carried a stray duplicate call for one placement | Header renders both `global_top` placements, footer renders both `global_bottom` placements; duplicate removed from `page.php` (theme 1.0.2) | Created probe sections in all 4 global placements + home before/after; fetched `/`, `/about/` and a generic page over HTTP; each marker present exactly once, home markers only on `/` | **Fixed, verified** |
| 2 | CSV formula injection | Submissions CSV export wrote visitor-supplied cells verbatim; a name/message starting with `=`, `+`, `-`, `@` executes as a formula in Excel/Sheets on the admin's machine | No output neutralisation in `PDO_SM_Submissions::handle_export()` | New `csv_safe()` prefixes dangerous cells (incl. leading-whitespace/tab variants) with an apostrophe; applied to every exported cell | Integration tests: 5 malicious payloads neutralised, normal text untouched | **Fixed, verified** |
| 3 | Settings import unbounded and irreversible | Import accepted any file size and overwrote settings groups with no way back | `PDO_SM_Exporter::import()` read the whole upload and saved group-by-group with no snapshot | 2 MB upload cap; `PDO_SM_Settings::backup_now()` snapshot of every affected group before it is saved, restorable from Tools → "Restore a settings backup" | Integration tests: schema rejects unknown groups; backup/restore round-trip passes. A full malicious-file HTTP import was **not** executed (see Limitations) | **Fixed; partially verified** |
| 4 | `tel` / `date` fields not validated server-side | The theme renders telephone and date inputs, but the handler treated them as plain text, so `abc!!` or `2026-13-40` were stored | The validation `switch` in `PDO_SM_Forms::handle()` had no `tel`/`phone`/`date` cases | Telephone: pattern `^[+0-9][0-9\s\-()]{4,24}$`; date: strict `Y-m-d` via `DateTimeImmutable` with round-trip check; new translatable errors `invalid_phone` / `invalid_date` | Live HTTP probes against the real volunteer form: bad phone rejected with the phone error and **no row stored** (count 5→5); good phone stored (5→6); bad date `2026-13-40` rejected ("valid date" error, no row); good date `2026-12-01` stored verbatim in the payload; form config restored afterwards | **Fixed, verified end-to-end** |
| 5 | Uninstall left backups behind | With data deletion explicitly enabled, uninstall removed content but left `pdo_sm_backup_*` options in the database | `uninstall.php` did not enumerate backup options | Uninstall now deletes `pdo_sm_backup_%` options too (still only under the explicit opt-in) | Code-reviewed; the destructive uninstall run itself was **not** executed (see Limitations) | **Fixed; not executed** |
| 6 | Theme asset version stuck at 1.0.0 | `PDO_MODERN_VERSION` stayed `1.0.0` while the theme shipped 1.0.1, so browsers/CDNs could keep stale CSS/JS after an update | Constant never bumped at release time | Constant now matches `style.css` (1.0.2) | Source inspection; both files bumped together | **Fixed** |

## Audited and verified correct (no change needed)

- **Authorisation**: every privileged `admin_post_*` / AJAX / REST
  handler checks a capability and a nonce before acting; form
  endpoints are intentionally public but layered with nonce +
  honeypot + 3-second time trap + transient rate limit (5/10 min).
- **Submissions admin**: status changes validate against
  `PDO_SM_Submissions::statuses()`; inserts/updates use `$wpdb`
  format specifiers; submissions are stored *before* the notification
  email is attempted, and mail failure is reported honestly.
- **Meta boxes**: autosave, revision, nonce and capability guards all
  present (82-field round-trip in earlier passes, re-verified by
  integration suite boot).
- **Customizer**: 35 settings, 35 with explicit `sanitize_callback`.
- **Settings saves** (1.0.2 behaviour, re-verified): tab-scoped,
  defaults-aware saves; integration tests prove another tab's option
  is byte-identical after a save, intentional repeater clears persist,
  and bilingual EN/BN values round-trip.
- **Setup wizard**: integration test runs it twice — page count
  identical (idempotent); page templates assigned are real theme
  template names.
- **Dynamic sections**: all 21 types registered with admin UI,
  sanitiser and renderer; home before/after placements render.
- **SEO**: theme emits no meta/canonical/JSON-LD at all when an SEO
  plugin is active (`pdo_modern_seo_plugin_active()`), and removes
  core's `rel_canonical` so exactly one canonical is printed.
- **Homepage reset** (1.0.3): backs up current homepage, restores demo
  values, clears `pdo_home_order` / `pdo_section_hide_*` theme mods;
  reversible from the restore panel (verified live in the 1.0.3 pass).

## Test evidence (this audit)

| Suite | Coverage | Result |
|-------|----------|--------|
| `tests/integration.php` (new, in repo) | 41 assertions: compat surface, settings isolation, repeater clear, bilingual, backup/restore, import schema guard, CSV safety, global section query + render, form config, rate-limit hash, submission round-trip, wizard idempotency | **41/41 PASS** |
| Live HTTP form probes | bad/good phone, bad/good date against the real form + submissions table | **4/4 PASS** |
| Live placement probes | 4 global + 2 home placements × 3 page types over HTTP | **PASS** (each marker exactly once, correct pages only) |
| Smoke suite (`smoke.sh`) | 23 routes incl. taxonomies, search, 404, BN homepage | **23/23 OK** |
| PHP parse scan | 132 PHP files (`TOKEN_PARSE`, PHP 8.2) | **0 errors** |

## Not executed / limitations

- **No real-browser or real-device testing.** Responsive behaviour
  (320–1920 px), reduced-motion and JS degradation were audited in
  code (media queries, `prefers-reduced-motion`, no-JS fallbacks) but
  not exercised in physical browsers or with screen readers. WCAG 2.2
  AA is therefore *code-audited*, not *certified*.
- **Uninstall was not run end-to-end** — it would destroy the test
  site's content. The change is a reviewed `DELETE` of a enumerated
  option prefix under the existing explicit opt-in.
- **Malicious settings-import files were not uploaded over HTTP**;
  the 2 MB cap and backup-before-save are covered by code path and
  the integration-level backup/schema tests, not by a live upload of
  a hostile file.
- **Mail delivery** depends on host SMTP; tests verify storage and
  the honest failure path, not inbox delivery.
- **PHP 7.4**: the production minimum is 7.4 and all new syntax is
  7.4-compatible, but this sandbox runs PHP 8.2 only — 7.4 was not
  re-executed in this pass.
- **Performance** figures from the php-wasm sandbox are not
  representative of production hosting and are not reported as such.

## Compatibility & migration notes

- No options, post types, taxonomies, meta keys, theme mods, menu
  locations, table names or template-API functions were renamed or
  removed in 1.0.4 / theme 1.0.2. The integration suite asserts the
  registered-object surface.
- No migration is required to upgrade from 1.0.3 → 1.0.4. Imports now
  create restorable backups automatically; existing
  `pdo_sm_backup_*` data is untouched.
- Install/upgrade: upload `pdo-site-manager.zip`, then
  `pdo-modern.zip`, reactivate neither is required — update in place.
  See `INSTALL-FIRST.txt` in `dist/pdo-website-complete.zip`.
