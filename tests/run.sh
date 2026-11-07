#!/usr/bin/env bash
# Run the PDO Website integration tests against a throwaway
# WordPress in WPTEST_DIR (default /tmp/wptest) that has the
# plugin + theme synced and the demo content seeded.
#
#   WP_HOST=8080-xxxx.e2b.app bash tests/run.sh
#
# WP_HOST is the public host the test WordPress believes it lives
# on (the php-wasm harness needs it to build absolute URLs).
set -euo pipefail

WPTEST_DIR="${WPTEST_DIR:-/tmp/wptest}"
: "${WP_HOST:?Set WP_HOST to the test site host, e.g. WP_HOST=8080-xxxx.e2b.app}"

HERE="$(cd "$(dirname "$0")" && pwd)"

"$WPTEST_DIR/sync.sh" >/dev/null
cd "$WPTEST_DIR"

out="$(WP_HOST="$WP_HOST" node wpexec.mjs "$HERE/integration.php")"
echo "$out"

if grep -q '^FAIL' <<<"$out"; then
	echo "Integration tests FAILED." >&2
	exit 1
fi
grep -q 'RESULT: ALL PASS' <<<"$out"
echo "Integration tests passed."
