<?php
/**
 * Settings export / import.
 *
 * @package PDO_Site_Manager
 */

defined( 'ABSPATH' ) || exit;

/**
 * Exporter.
 */
class PDO_SM_Exporter {

	/**
	 * Hooks.
	 */
	public function __construct() {
		add_action( 'admin_post_pdo_sm_export_settings', array( $this, 'export' ) );
		add_action( 'admin_post_pdo_sm_import_settings', array( $this, 'import' ) );
		add_action( 'admin_post_pdo_sm_save_tools', array( $this, 'save_tools' ) );
	}

	/**
	 * Export all PDO settings as JSON.
	 *
	 * @return void
	 */
	public function export() {
		PDO_SM_Capabilities::assert_manage();
		check_admin_referer( 'pdo_sm_export_settings' );

		$payload = array(
			'generator' => 'pdo-site-manager',
			'version'   => PDO_SM_VERSION,
			'exported'  => current_time( 'mysql' ),
			'settings'  => array(),
		);

		foreach ( array_keys( PDO_SM_Schema::all() ) as $group ) {
			$payload['settings'][ $group ] = PDO_SM_Settings::get_group( $group );
		}

		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=pdo-settings-' . gmdate( 'Y-m-d-His' ) . '.json' );
		echo wp_json_encode( $payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
		exit;
	}

	/**
	 * Import settings from an uploaded JSON file.
	 *
	 * @return void
	 */
	public function import() {
		PDO_SM_Capabilities::assert_manage();
		check_admin_referer( 'pdo_sm_import_settings' );

		$redirect = admin_url( 'admin.php?page=pdo-tools' );

		if ( empty( $_FILES['pdo_settings_file']['tmp_name'] ) ) {
			wp_safe_redirect( add_query_arg( 'pdo_error', 'nofile', $redirect ) );
			exit;
		}

		$tmp = isset( $_FILES['pdo_settings_file']['tmp_name'] ) ? sanitize_text_field( $_FILES['pdo_settings_file']['tmp_name'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		if ( ! is_uploaded_file( $tmp ) ) {
			wp_safe_redirect( add_query_arg( 'pdo_error', 'upload', $redirect ) );
			exit;
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$raw  = file_get_contents( $tmp );
		$data = json_decode( (string) $raw, true );

		if ( ! is_array( $data ) || empty( $data['settings'] ) || ! is_array( $data['settings'] ) ) {
			wp_safe_redirect( add_query_arg( 'pdo_error', 'invalid', $redirect ) );
			exit;
		}

		$imported = 0;
		foreach ( $data['settings'] as $group => $values ) {
			$group = sanitize_key( $group );
			if ( ! PDO_SM_Schema::group( $group ) || ! is_array( $values ) ) {
				continue;
			}
			// Snapshot first so an import can be rolled back from Tools.
			PDO_SM_Settings::backup_now( $group );
			if ( PDO_SM_Settings::save_group( $group, $values ) ) {
				++$imported;
			}
		}

		wp_safe_redirect( add_query_arg( 'pdo_imported_settings', $imported, $redirect ) );
		exit;
	}

	/**
	 * Save the tools preferences.
	 *
	 * @return void
	 */
	public function save_tools() {
		PDO_SM_Capabilities::assert_manage();
		check_admin_referer( 'pdo_sm_save_tools' );

		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- verified above.
		$delete = isset( $_POST['pdo_delete_data'] ) ? 1 : 0;
		update_option( 'pdo_sm_delete_data_on_uninstall', $delete, false );

		wp_safe_redirect( add_query_arg( 'pdo_saved', 1, admin_url( 'admin.php?page=pdo-tools' ) ) );
		exit;
	}
}
