# PDO Website — integration tests

`integration.php` contains 41 assertions that run **inside a booted
WordPress** (plugin + theme active, demo content seeded). They cover:

- Backward compatibility: all 7 custom post types still registered,
  `pdo_sm_*` option layout unchanged, 21 dynamic section types.
- Settings: saving one tab never touches another tab's option;
  intentionally emptying a repeater persists (is not refilled);
  bilingual (EN/BN) values round-trip.
- Backups: `backup_now()` snapshots a group; restore returns the
  previous values.
- Import safety: unknown settings groups are rejected by the schema.
- CSV export: spreadsheet formula-injection cells (`=`, `+`, `-`, `@`,
  leading tab) are neutralised; plain text is untouched.
- Dynamic sections: Global Top / Global Bottom posts are returned by
  the query API and rendered by the theme.
- Forms: volunteer form exposes `tel`/`email` fields; the rate-limit
  visitor hash is stable.
- Submissions: insert + payload round-trip through the custom table.
- Setup wizard: running it twice creates no new pages (idempotent).

## Running

The tests need a throwaway WordPress with the php-wasm harness
(`sync.sh`, `wpexec.mjs`, `server.mjs`) in `WPTEST_DIR`
(default `/tmp/wptest`):

```bash
WP_HOST=8080-xxxx.e2b.app bash tests/run.sh
```

The runner syncs the current plugin + theme sources, executes the
suite, prints one `PASS`/`FAIL` line per assertion and exits non-zero
if anything fails. Tests restore every option they touch and delete
every post they create, so the test site is unchanged afterwards.

Browser-level form validation (bad/good telephone and date values
submitted over HTTP and checked against the submissions table) was
verified live during the 1.0.4 audit; see `docs/AUDIT-REPORT.md`.
