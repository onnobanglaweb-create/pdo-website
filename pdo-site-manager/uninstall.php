<?php
/**
 * Uninstall routine.
 *
 * Only runs when the administrator explicitly deletes the plugin and has
 * opted in to data removal via the Tools screen.
 *
 * @package PDO_Site_Manager
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

if ( ! get_option( 'pdo_sm_delete_data_on_uninstall' ) ) {
	return;
}

global $wpdb;

$options = array(
	'pdo_sm_org',
	'pdo_sm_home',
	'pdo_sm_about',
	'pdo_sm_impact',
	'pdo_sm_transparency',
	'pdo_sm_media',
	'pdo_sm_involved',
	'pdo_sm_contact',
	'pdo_sm_i18n',
	'pdo_sm_db_version',
	'pdo_sm_installed_at',
	'pdo_sm_setup_complete',
	'pdo_sm_pages',
	'pdo_sm_delete_data_on_uninstall',
);

foreach ( $options as $option ) {
	delete_option( $option );
}

$post_types = array( 'pdo_news', 'pdo_program', 'pdo_team', 'pdo_partner', 'pdo_story', 'pdo_document', 'pdo_section' );
foreach ( $post_types as $post_type ) {
	$posts = get_posts(
		array(
			'post_type'      => $post_type,
			'posts_per_page' => -1,
			'post_status'    => 'any',
			'fields'         => 'ids',
		)
	);
	foreach ( $posts as $post_id ) {
		wp_delete_post( $post_id, true );
	}
}

// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}pdo_submissions" );
