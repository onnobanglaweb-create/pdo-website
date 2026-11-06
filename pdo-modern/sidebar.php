<?php
/**
 * Sidebar.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>
<aside class="pdo-sidebar" role="complementary" aria-label="<?php esc_attr_e( 'Sidebar', 'pdo-modern' ); ?>">
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside>
