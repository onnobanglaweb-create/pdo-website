<?php
/**
 * Mobile navigation drawer.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

$pdo_style = sanitize_html_class( (string) get_theme_mod( 'pdo_mobile_style', 'drawer' ) );
?>
<div
	id="pdo-drawer"
	class="pdo-drawer pdo-drawer--<?php echo esc_attr( $pdo_style ); ?>"
	data-pdo-drawer
	hidden
>
	<div class="pdo-drawer__backdrop" data-pdo-drawer-close></div>

	<div class="pdo-drawer__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Site menu', 'pdo-modern' ); ?>">
		<div class="pdo-drawer__head">
			<?php pdo_modern_logo( 'mobile' ); ?>
			<button type="button" class="pdo-iconbtn pdo-drawer__close" data-pdo-drawer-close>
				<?php pdo_icon( 'x', array( 'size' => 20 ) ); ?>
				<span class="screen-reader-text"><?php esc_html_e( 'Close the menu', 'pdo-modern' ); ?></span>
			</button>
		</div>

		<div class="pdo-drawer__body">
			<?php get_search_form(); ?>

			<?php
			if ( has_nav_menu( 'primary' ) ) {
				$pdo_walker         = new PDO_Nav_Walker();
				$pdo_walker->mobile = true;
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => 'nav',
						'container_class' => 'pdo-nav pdo-nav--mobile',
						'container_aria_label' => __( 'Mobile navigation', 'pdo-modern' ),
						'menu_class'     => 'pdo-menu pdo-menu--mobile',
						'depth'          => 3,
						'walker'         => $pdo_walker,
						'fallback_cb'    => false,
					)
				);
			}
			?>

			<?php if ( get_theme_mod( 'pdo_mobile_cta', true ) ) : ?>
				<div class="pdo-drawer__cta">
					<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper.
					echo pdo_modern_render_header_cta();
					?>
				</div>
			<?php endif; ?>

			<?php pdo_modern_language_switcher( 'drawer' ); ?>

			<?php
			$pdo_phone = pdo_setting( 'org', 'phone', '' );
			$pdo_email = pdo_setting( 'org', 'email', '' );
			if ( $pdo_phone || $pdo_email ) :
				?>
				<ul class="pdo-drawer__contact">
					<?php if ( $pdo_phone ) : ?>
						<li>
							<?php pdo_icon( 'phone', array( 'size' => 16 ) ); ?>
							<a href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', $pdo_phone ) ); ?>"><?php echo esc_html( $pdo_phone ); ?></a>
						</li>
					<?php endif; ?>
					<?php if ( $pdo_email ) : ?>
						<li>
							<?php pdo_icon( 'mail', array( 'size' => 16 ) ); ?>
							<a href="<?php echo esc_url( 'mailto:' . $pdo_email ); ?>"><?php echo esc_html( $pdo_email ); ?></a>
						</li>
					<?php endif; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>
</div>
