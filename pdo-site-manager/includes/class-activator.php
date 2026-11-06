<?php
/**
 * Activation routine.
 *
 * @package PDO_Site_Manager
 */

defined( 'ABSPATH' ) || exit;

/**
 * Activator.
 */
class PDO_SM_Activator {

	/**
	 * Run on activation.
	 *
	 * @return void
	 */
	public static function activate() {
		self::create_tables();
		PDO_SM_Capabilities::add_caps();

		// Register post types so the rewrite rules are flushed with them present.
		$plugin = pdo_sm();
		$types  = $plugin->get( 'content_types' );
		if ( $types ) {
			$types->register();
		}
		$tax = $plugin->get( 'taxonomies' );
		if ( $tax ) {
			$tax->register();
		}

		flush_rewrite_rules();

		if ( ! get_option( 'pdo_sm_installed_at' ) ) {
			update_option( 'pdo_sm_installed_at', current_time( 'mysql' ), false );
		}
		update_option( 'pdo_sm_db_version', PDO_SM_DB_VERSION, false );
	}

	/**
	 * Create (or upgrade) the submissions table.
	 *
	 * @return void
	 */
	public static function create_tables() {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table   = $wpdb->prefix . 'pdo_submissions';
		$charset = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			form_type varchar(32) NOT NULL DEFAULT 'contact',
			subject varchar(255) NOT NULL DEFAULT '',
			name varchar(191) NOT NULL DEFAULT '',
			email varchar(191) NOT NULL DEFAULT '',
			phone varchar(64) NOT NULL DEFAULT '',
			payload longtext NULL,
			status varchar(20) NOT NULL DEFAULT 'new',
			ip_hash varchar(64) NOT NULL DEFAULT '',
			source_url varchar(255) NOT NULL DEFAULT '',
			lang varchar(8) NOT NULL DEFAULT 'en',
			created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			PRIMARY KEY  (id),
			KEY form_type (form_type),
			KEY status (status),
			KEY created_at (created_at),
			KEY email (email)
		) {$charset};";

		dbDelta( $sql );
	}
}
