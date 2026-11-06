<?php
/**
 * Setup wizard support: onboarding notice and first-run state.
 *
 * The wizard screen itself lives in admin/views/setup-wizard.php and the
 * actual migration work is performed by PDO_SM_Importer.
 *
 * @package PDO_Site_Manager
 */

defined( 'ABSPATH' ) || exit;

/**
 * Setup wizard.
 */
class PDO_SM_Setup_Wizard {

	/**
	 * User meta flag used to dismiss the notice.
	 */
	const DISMISS_META = 'pdo_sm_hide_setup_notice';

	/**
	 * Hooks.
	 */
	public function __construct() {
		add_action( 'admin_notices', array( $this, 'notice' ) );
		add_action( 'admin_post_pdo_sm_dismiss_setup', array( $this, 'dismiss' ) );
	}

	/**
	 * Has the wizard been completed?
	 *
	 * @return bool
	 */
	public static function is_complete() {
		return (bool) get_option( 'pdo_sm_setup_complete' );
	}

	/**
	 * Onboarding notice.
	 *
	 * @return void
	 */
	public function notice() {
		if ( self::is_complete() ) {
			return;
		}
		if ( ! current_user_can( PDO_SM_Capabilities::MANAGE ) && ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( get_user_meta( get_current_user_id(), self::DISMISS_META, true ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $screen && 'pdo-manager_page_pdo-setup' === $screen->id ) {
			return;
		}
		?>
		<div class="notice notice-info pdo-notice">
			<p>
				<strong><?php esc_html_e( 'PDO Website Manager is installed.', 'pdo-site-manager' ); ?></strong>
				<?php esc_html_e( 'Run the setup wizard once to create the pages, menus and starter content. Nothing that already exists will be overwritten.', 'pdo-site-manager' ); ?>
			</p>
			<p>
				<a class="button button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=pdo-setup' ) ); ?>"><?php esc_html_e( 'Open the Setup Wizard', 'pdo-site-manager' ); ?></a>
				<a class="button button-link" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=pdo_sm_dismiss_setup' ), 'pdo_sm_dismiss_setup' ) ); ?>"><?php esc_html_e( 'Dismiss', 'pdo-site-manager' ); ?></a>
			</p>
		</div>
		<?php
	}

	/**
	 * Dismiss the notice for the current user.
	 *
	 * @return void
	 */
	public function dismiss() {
		check_admin_referer( 'pdo_sm_dismiss_setup' );
		update_user_meta( get_current_user_id(), self::DISMISS_META, 1 );

		$redirect = wp_get_referer();
		wp_safe_redirect( $redirect ? $redirect : admin_url() );
		exit;
	}
}
