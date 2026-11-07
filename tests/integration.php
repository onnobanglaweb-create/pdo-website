// PDO Website — integration tests (WordPress context).
//
// This file intentionally has NO <?php opening tag: the harness
// (tests/run.sh) executes it inside a booted WordPress via the
// php-wasm wpexec runner, which wraps the file in its own PHP tag.
// Every assertion prints PASS/FAIL; the runner fails on any FAIL.
//
// Requires: plugin + theme synced into a throwaway WordPress
// (WPTEST_DIR) with the demo content seeded. Tests restore every
// option they touch and delete every post they create.

$GLOBALS['pdo_test_fail'] = 0;
function t( $name, $cond ) {
	echo ( $cond ? 'PASS' : 'FAIL' ) . ' ' . $name . "\n";
	if ( ! $cond ) {
		++$GLOBALS['pdo_test_fail'];
	}
}

// --- Compatibility surface -------------------------------------------------
foreach ( array( 'pdo_news', 'pdo_program', 'pdo_team', 'pdo_partner', 'pdo_story', 'pdo_document', 'pdo_section' ) as $cpt ) {
	t( "compat: CPT $cpt registered", post_type_exists( $cpt ) );
}
t( 'compat: pdo_sm_home option is an array', is_array( get_option( 'pdo_sm_home' ) ) );
t( 'sections: 21 dynamic types registered', 21 === count( PDO_SM_Sections::types() ) );

// --- Settings: tab isolation ------------------------------------------------
$about_before = get_option( 'pdo_sm_about' );
$home_raw     = get_option( 'pdo_sm_home' );
t( 'settings: home option readable', is_array( $home_raw ) );
$probe_field = null;
foreach ( PDO_SM_Schema::fields( 'home' ) as $f ) {
	if ( ! empty( $f['key'] ) && 'text' === ( $f['type'] ?? '' ) ) {
		$probe_field = $f['key'];
		break;
	}
}
t( 'settings: home has a text field to probe', null !== $probe_field );
$home_mod                    = $home_raw;
$home_mod[ $probe_field ]    = 'INTEGRATION-PROBE';
PDO_SM_Settings::save_group( 'home', $home_mod );
t( 'settings: saved value persists', 'INTEGRATION-PROBE' === PDO_SM_Settings::get( 'home', $probe_field ) );
t( 'settings: other tab (about) untouched by home save', get_option( 'pdo_sm_about' ) === $about_before );

// --- Settings: intentional repeater clear -----------------------------------
$repeater_key = null;
foreach ( PDO_SM_Schema::fields( 'home' ) as $f ) {
	if ( ! empty( $f['key'] ) && 'repeater' === ( $f['type'] ?? '' ) ) {
		$repeater_key = $f['key'];
		break;
	}
}
t( 'settings: home has a repeater field to probe', null !== $repeater_key );
$home_mod                 = get_option( 'pdo_sm_home' );
$home_mod[ $repeater_key ] = array();
PDO_SM_Settings::save_group( 'home', $home_mod );
$after_clear = PDO_SM_Settings::get_group( 'home' );
t( 'settings: intentional repeater clear persists (not refilled)', empty( $after_clear[ $repeater_key ] ) );
update_option( 'pdo_sm_home', $home_raw ); // restore.

// --- Settings: bilingual persistence ----------------------------------------
$org_raw              = get_option( 'pdo_sm_org' );
$org_mod              = $org_raw;
$org_mod['name']      = 'Probe Org EN';
$org_mod['name_bn']   = 'প্রোব সংস্থা';
PDO_SM_Settings::save_group( 'org', $org_mod );
$org_after = PDO_SM_Settings::get_group( 'org' );
t( 'settings: bilingual EN persists', 'Probe Org EN' === ( $org_after['name'] ?? null ) );
t( 'settings: bilingual BN persists', 'প্রোব সংস্থা' === ( $org_after['name_bn'] ?? null ) );

// --- Settings: backup + restore ----------------------------------------------
PDO_SM_Settings::backup_now( 'org' );
$backup = PDO_SM_Settings::backup_group( 'org' );
t( 'backup: backup_now creates a snapshot with values', ! empty( $backup['values'] ) && ! empty( $backup['saved_at'] ) );
$org_mod          = get_option( 'pdo_sm_org' );
$org_mod['name']  = 'Changed After Backup';
PDO_SM_Settings::save_group( 'org', $org_mod );
t( 'backup: value changed after backup', 'Changed After Backup' === PDO_SM_Settings::get( 'org', 'name' ) );
PDO_SM_Settings::save_group( 'org', $backup['values'] );
t( 'backup: restore returns the backed-up value', 'Probe Org EN' === PDO_SM_Settings::get( 'org', 'name' ) );
update_option( 'pdo_sm_org', $org_raw ); // restore.
delete_option( 'pdo_sm_backup_org' );

// --- Import guards ------------------------------------------------------------
t( 'import: unknown settings group is rejected by schema', ! PDO_SM_Schema::group( 'definitely_bogus_group' ) );
t( 'import: known settings group resolves', (bool) PDO_SM_Schema::group( 'home' ) );

// --- CSV export: formula-injection safety --------------------------------------
$m = new ReflectionMethod( 'PDO_SM_Submissions', 'csv_safe' );
$m->setAccessible( true );
foreach ( array( '=1+1', '+CMD()', '-2+3', '@SUM(A1:A2)', "\t=HYPERLINK()" ) as $evil ) {
	$out = $m->invoke( null, $evil );
	t( 'csv: dangerous cell neutralised ' . json_encode( $evil ), is_string( $out ) && "'" === substr( $out, 0, 1 ) );
}
t( 'csv: normal text untouched', 'Annual report 2026' === $m->invoke( null, 'Annual report 2026' ) );

// --- Dynamic sections: global placements --------------------------------------
$mk = function ( $loc, $place, $marker ) {
	$id = wp_insert_post(
		array(
			'post_type'   => 'pdo_section',
			'post_status' => 'publish',
			'post_title'  => $marker,
		)
	);
	update_post_meta( $id, '_pdo_type', 'heading' );
	update_post_meta( $id, '_pdo_location', $loc );
	update_post_meta( $id, '_pdo_placement', $place );
	update_post_meta( $id, '_pdo_heading', $marker );
	update_post_meta( $id, '_pdo_hidden', 0 );
	return $id;
};
$ids   = array();
$ids[] = $mk( 'global_top', 'after', 'ITEST-GTOP' );
$ids[] = $mk( 'global_bottom', 'before', 'ITEST-GBOT' );
t( 'sections: global_top/after query returns the post', ! empty( PDO_SM_Sections::get( 'global_top', 'after' ) ) );
if ( function_exists( 'pdo_modern_dynamic_sections' ) ) {
	ob_start();
	pdo_modern_dynamic_sections( 'global_top', 'after' );
	$html = ob_get_clean();
	t( 'sections: theme renders global_top marker', false !== strpos( $html, 'ITEST-GTOP' ) );
	ob_start();
	pdo_modern_dynamic_sections( 'global_bottom', 'before' );
	$html = ob_get_clean();
	t( 'sections: theme renders global_bottom marker', false !== strpos( $html, 'ITEST-GBOT' ) );
} else {
	t( 'sections: theme renderer available', false );
}
foreach ( $ids as $id ) {
	wp_delete_post( $id, true );
}

// --- Forms: configuration + primitives -----------------------------------------
$vol = PDO_SM_Forms::config( 'volunteer' );
$types = array();
foreach ( (array) $vol['fields'] as $f ) {
	$types[ $f['name'] ?? '' ] = $f['type'] ?? 'text';
}
t( 'forms: volunteer has a tel field', 'tel' === ( $types['phone'] ?? null ) );
t( 'forms: volunteer has an email field', 'email' === ( $types['email'] ?? null ) );
$hash = PDO_SM_Helpers::visitor_hash();
t( 'forms: rate-limit visitor hash is stable', is_string( $hash ) && '' !== $hash && $hash === PDO_SM_Helpers::visitor_hash() );
t( 'submissions: statuses include new', array_key_exists( 'new', PDO_SM_Submissions::statuses() ) );

// --- Submissions: insert + read back --------------------------------------------
$sid = PDO_SM_Submissions::insert(
	array(
		'form_type' => 'volunteer',
		'name'      => 'Integration Probe',
		'email'     => 'itest@example.org',
		'payload'   => array( 'Phone' => '+880 1000-000000' ),
	)
);
t( 'submissions: insert returns an id', is_numeric( $sid ) && (int) $sid > 0 );
global $wpdb;
$tbl = $wpdb->prefix . 'pdo_submissions';
// phpcs:ignore WordPress.DB.DirectDatabaseQuery,WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $tbl WHERE id = %d", (int) $sid ), ARRAY_A );
t( 'submissions: payload round-trips', $row && false !== strpos( (string) $row['payload'], '+880 1000-000000' ) );
$wpdb->delete( $tbl, array( 'id' => (int) $sid ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

// --- Setup wizard: idempotency ----------------------------------------------------
$pages_before = count( get_pages( array( 'post_status' => 'publish,draft,private' ) ) );
$importer     = new PDO_SM_Importer();
$importer->run();
$pages_mid = count( get_pages( array( 'post_status' => 'publish,draft,private' ) ) );
$importer->run();
$pages_after = count( get_pages( array( 'post_status' => 'publish,draft,private' ) ) );
t( 'wizard: second run creates no new pages', $pages_mid === $pages_after );
t( 'wizard: pages exist after runs', $pages_after >= $pages_before );

echo 'RESULT: ' . ( 0 === $GLOBALS['pdo_test_fail'] ? 'ALL PASS' : $GLOBALS['pdo_test_fail'] . ' FAILURES' ) . "\n";
