<?php
/**
 * Desktop navigation.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

if ( ! has_nav_menu( 'primary' ) ) {
	if ( current_user_can( 'edit_theme_options' ) ) {
		printf(
			'<nav class="pdo-nav pdo-nav--desktop"><a class="pdo-nav__setup" href="%s">%s</a></nav>',
			esc_url( admin_url( 'nav-menus.php' ) ),
			esc_html__( 'Set up the primary menu', 'pdo-modern' )
		);
	}
	return;
}
?>
<nav
	class="pdo-nav pdo-nav--desktop pdo-nav--<?php echo esc_attr( sanitize_html_class( (string) get_theme_mod( 'pdo_nav_style', 'underline' ) ) ); ?>"
	aria-label="<?php esc_attr_e( 'Primary navigation', 'pdo-modern' ); ?>"
>
	<?php
	wp_nav_menu(
		array(
			'theme_location' => 'primary',
			'container'      => false,
			'menu_class'     => 'pdo-menu',
			'depth'          => 3,
			'walker'         => new PDO_Nav_Walker(),
			'fallback_cb'    => false,
		)
	);
	?>
</nav>
