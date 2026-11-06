<?php
/**
 * Lightweight AJAX endpoints used by the admin UI.
 *
 * @package PDO_Site_Manager
 */

defined( 'ABSPATH' ) || exit;

/**
 * Admin AJAX controller.
 */
class PDO_SM_Rest {

	/**
	 * Hooks.
	 */
	public function __construct() {
		add_action( 'wp_ajax_pdo_sm_reorder', array( $this, 'reorder' ) );
	}

	/**
	 * Persist a new drag-and-drop ordering for a post type.
	 *
	 * @return void
	 */
	public function reorder() {
		check_ajax_referer( 'pdo_sm_reorder', 'nonce' );

		if ( ! current_user_can( PDO_SM_Capabilities::MANAGE ) && ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'pdo-site-manager' ) ), 403 );
		}

		$ids = isset( $_POST['ids'] ) ? array_map( 'absint', (array) wp_unslash( $_POST['ids'] ) ) : array();
		if ( ! $ids ) {
			wp_send_json_error( array( 'message' => __( 'Nothing to reorder.', 'pdo-site-manager' ) ), 400 );
		}

		$order = 0;
		foreach ( $ids as $id ) {
			if ( ! $id || ! current_user_can( 'edit_post', $id ) ) {
				continue;
			}
			wp_update_post(
				array(
					'ID'         => $id,
					'menu_order' => $order,
				)
			);
			++$order;
		}

		wp_send_json_success( array( 'updated' => $order ) );
	}
}
