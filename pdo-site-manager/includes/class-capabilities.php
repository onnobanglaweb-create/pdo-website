<?php
/**
 * Capability management.
 *
 * @package PDO_Site_Manager
 */

defined( 'ABSPATH' ) || exit;

/**
 * Registers the PDO specific capabilities.
 */
class PDO_SM_Capabilities {

	/**
	 * Capability required to manage PDO content.
	 */
	const MANAGE = 'pdo_manage_content';

	/**
	 * Capability required to read form submissions.
	 */
	const SUBMISSIONS = 'pdo_manage_submissions';

	/**
	 * Roles that receive the capabilities by default.
	 *
	 * @return array<string,array<string>>
	 */
	public static function role_map() {
		return array(
			'administrator' => array( self::MANAGE, self::SUBMISSIONS ),
			'editor'        => array( self::MANAGE ),
		);
	}

	/**
	 * Grant the capabilities.
	 *
	 * @return void
	 */
	public static function add_caps() {
		foreach ( self::role_map() as $role_name => $caps ) {
			$role = get_role( $role_name );
			if ( ! $role ) {
				continue;
			}
			foreach ( $caps as $cap ) {
				if ( ! $role->has_cap( $cap ) ) {
					$role->add_cap( $cap );
				}
			}
		}
	}

	/**
	 * Remove the capabilities (used on uninstall only).
	 *
	 * @return void
	 */
	public static function remove_caps() {
		foreach ( self::role_map() as $role_name => $caps ) {
			$role = get_role( $role_name );
			if ( ! $role ) {
				continue;
			}
			foreach ( $caps as $cap ) {
				$role->remove_cap( $cap );
			}
		}
	}

	/**
	 * Capability used for the content dashboards.
	 *
	 * @return string
	 */
	public static function manage_cap() {
		$cap = current_user_can( self::MANAGE ) ? self::MANAGE : 'manage_options';
		/**
		 * Filter the capability needed to manage PDO content.
		 *
		 * @param string $cap Capability name.
		 */
		return apply_filters( 'pdo_sm_manage_capability', $cap );
	}

	/**
	 * Capability used for submissions.
	 *
	 * @return string
	 */
	public static function submissions_cap() {
		$cap = current_user_can( self::SUBMISSIONS ) ? self::SUBMISSIONS : 'manage_options';
		/**
		 * Filter the capability needed to read submissions.
		 *
		 * @param string $cap Capability name.
		 */
		return apply_filters( 'pdo_sm_submissions_capability', $cap );
	}

	/**
	 * Guard helper – dies when the current user may not manage content.
	 *
	 * @return void
	 */
	public static function assert_manage() {
		if ( ! current_user_can( self::MANAGE ) && ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to manage PDO website content.', 'pdo-site-manager' ), 403 );
		}
	}

	/**
	 * Guard helper – dies when the current user may not read submissions.
	 *
	 * @return void
	 */
	public static function assert_submissions() {
		if ( ! current_user_can( self::SUBMISSIONS ) && ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to view PDO form submissions.', 'pdo-site-manager' ), 403 );
		}
	}
}
