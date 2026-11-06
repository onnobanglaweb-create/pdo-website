<?php
/**
 * Tools screen: export / import / maintenance.
 *
 * @package PDO_Site_Manager
 */

defined( 'ABSPATH' ) || exit;

// Defence in depth: add_submenu_page() already gates the screen, but a
// direct include must never render privileged data.
if ( ! current_user_can( PDO_SM_Capabilities::MANAGE ) ) {
	wp_die( esc_html__( 'You do not have permission to view this screen.', 'pdo-site-manager' ), 403 );
}

// phpcs:disable WordPress.Security.NonceVerification.Recommended
$pdo_imported = isset( $_GET['pdo_imported_settings'] ) ? absint( wp_unslash( $_GET['pdo_imported_settings'] ) ) : -1;
// phpcs:enable
?>
<div class="wrap pdo-wrap">
	<h1 class="pdo-page-title"><?php esc_html_e( 'PDO Tools', 'pdo-site-manager' ); ?></h1>

	<?php PDO_SM_Dashboard::settings_notice(); ?>

	<?php if ( $pdo_imported >= 0 ) : ?>
		<div class="notice notice-success is-dismissible">
			<p>
				<?php
				printf(
					/* translators: %s: number of groups. */
					esc_html__( '%s settings group(s) imported.', 'pdo-site-manager' ),
					esc_html( number_format_i18n( $pdo_imported ) )
				);
				?>
			</p>
		</div>
	<?php endif; ?>

	<div class="pdo-panel">
		<div class="pdo-panel__head">
			<h2><?php esc_html_e( 'Export settings', 'pdo-site-manager' ); ?></h2>
			<p class="description"><?php esc_html_e( 'Downloads every PDO settings group as JSON. Posts, images and submissions are not included — use the normal WordPress exporter for those.', 'pdo-site-manager' ); ?></p>
		</div>
		<div class="pdo-panel__body">
			<a class="button button-primary" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=pdo_sm_export_settings' ), 'pdo_sm_export_settings' ) ); ?>">
				<?php esc_html_e( 'Download JSON', 'pdo-site-manager' ); ?>
			</a>
		</div>
	</div>

	<div class="pdo-panel">
		<div class="pdo-panel__head">
			<h2><?php esc_html_e( 'Import settings', 'pdo-site-manager' ); ?></h2>
			<p class="description"><?php esc_html_e( 'Restores a previously exported JSON file. Every value is re-sanitised against the schema before it is stored.', 'pdo-site-manager' ); ?></p>
		</div>
		<div class="pdo-panel__body">
			<form method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="pdo_sm_import_settings" />
				<?php wp_nonce_field( 'pdo_sm_import_settings' ); ?>
				<input type="file" name="pdo_settings_file" accept="application/json,.json" required="required" />
				<?php submit_button( __( 'Import', 'pdo-site-manager' ), 'secondary', 'submit', false ); ?>
			</form>
		</div>
	</div>

	<div class="pdo-panel">
		<div class="pdo-panel__head">
			<h2><?php esc_html_e( 'Data retention', 'pdo-site-manager' ); ?></h2>
		</div>
		<div class="pdo-panel__body">
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="pdo_sm_save_tools" />
				<?php wp_nonce_field( 'pdo_sm_save_tools' ); ?>
				<label>
					<input type="checkbox" name="pdo_delete_data" value="1" <?php checked( (bool) get_option( 'pdo_sm_delete_data_on_uninstall' ) ); ?> />
					<?php esc_html_e( 'Delete all PDO content and settings when the plugin is deleted', 'pdo-site-manager' ); ?>
				</label>
				<p class="description"><?php esc_html_e( 'Off by default: deactivating or deleting the plugin keeps your content safe.', 'pdo-site-manager' ); ?></p>
				<?php submit_button( __( 'Save preference', 'pdo-site-manager' ), 'secondary', 'submit', false ); ?>
			</form>
		</div>
	</div>

	<div class="pdo-panel">
		<div class="pdo-panel__head">
			<h2><?php esc_html_e( 'System status', 'pdo-site-manager' ); ?></h2>
		</div>
		<div class="pdo-panel__body">
			<?php
			global $wpdb;
			$pdo_table  = PDO_SM_Submissions::table();
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			$pdo_exists = (bool) $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $pdo_table ) );
			$pdo_theme  = wp_get_theme();
			?>
			<table class="pdo-kv">
				<tbody>
					<tr><th><?php esc_html_e( 'Plugin version', 'pdo-site-manager' ); ?></th><td><?php echo esc_html( PDO_SM_VERSION ); ?></td></tr>
					<tr><th><?php esc_html_e( 'Database schema', 'pdo-site-manager' ); ?></th><td><?php echo esc_html( (string) get_option( 'pdo_sm_db_version', '—' ) ); ?></td></tr>
					<tr><th><?php esc_html_e( 'Submissions table', 'pdo-site-manager' ); ?></th><td><?php echo $pdo_exists ? esc_html( $pdo_table ) : esc_html__( 'Missing — deactivate and reactivate the plugin.', 'pdo-site-manager' ); ?></td></tr>
					<tr><th><?php esc_html_e( 'Active theme', 'pdo-site-manager' ); ?></th><td><?php echo esc_html( $pdo_theme->get( 'Name' ) . ' ' . $pdo_theme->get( 'Version' ) ); ?></td></tr>
					<tr><th><?php esc_html_e( 'WordPress', 'pdo-site-manager' ); ?></th><td><?php echo esc_html( get_bloginfo( 'version' ) ); ?></td></tr>
					<tr><th><?php esc_html_e( 'PHP', 'pdo-site-manager' ); ?></th><td><?php echo esc_html( PHP_VERSION ); ?></td></tr>
					<tr><th><?php esc_html_e( 'Permalinks', 'pdo-site-manager' ); ?></th><td><?php echo esc_html( get_option( 'permalink_structure' ) ? get_option( 'permalink_structure' ) : __( 'Plain — switch to a pretty structure for clean URLs.', 'pdo-site-manager' ) ); ?></td></tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
