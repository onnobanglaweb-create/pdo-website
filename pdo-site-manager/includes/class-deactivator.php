<?php
/**
 * Deactivation routine.
 *
 * @package PDO_Site_Manager
 */

defined( 'ABSPATH' ) || exit;

/**
 * Deactivator.
 */
class PDO_SM_Deactivator {

	/**
	 * Run on deactivation. Content is deliberately preserved.
	 *
	 * @return void
	 */
	public static function deactivate() {
		flush_rewrite_rules();
	}
}
