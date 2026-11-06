<?php
/**
 * Utility top bar.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

if ( ! get_theme_mod( 'pdo_topbar_enabled', true ) ) {
	return;
}

$pdo_reg       = pdo_setting( 'org', 'reg_number', '' );
$pdo_authority = pdo_text( 'org', 'reg_authority', '' );
$pdo_phone     = pdo_setting( 'org', 'phone', '' );
$pdo_email     = pdo_setting( 'org', 'email', '' );
$pdo_location  = pdo_setting( 'org', 'short_location', '' );
$pdo_socials   = pdo_rows( 'org', 'socials' );
?>
<div class="pdo-topbar">
	<div class="pdo-container pdo-topbar__inner">
		<ul class="pdo-topbar__list">
			<?php if ( $pdo_reg && get_theme_mod( 'pdo_topbar_show_reg', true ) ) : ?>
				<li class="pdo-topbar__item pdo-topbar__item--reg">
					<?php pdo_icon( 'shield-check', array( 'size' => 14 ) ); ?>
					<span>
						<?php echo esc_html( $pdo_authority ? $pdo_authority . ' · ' : '' ); ?>
						<strong><?php echo esc_html( $pdo_reg ); ?></strong>
					</span>
				</li>
			<?php endif; ?>

			<?php if ( $pdo_location && get_theme_mod( 'pdo_topbar_show_location', true ) ) : ?>
				<li class="pdo-topbar__item">
					<?php pdo_icon( 'map-pin', array( 'size' => 14 ) ); ?>
					<span><?php echo esc_html( $pdo_location ); ?></span>
				</li>
			<?php endif; ?>

			<?php if ( $pdo_phone && get_theme_mod( 'pdo_topbar_show_phone', true ) ) : ?>
				<li class="pdo-topbar__item">
					<?php pdo_icon( 'phone', array( 'size' => 14 ) ); ?>
					<a href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', $pdo_phone ) ); ?>"><?php echo esc_html( $pdo_phone ); ?></a>
				</li>
			<?php endif; ?>

			<?php if ( $pdo_email && get_theme_mod( 'pdo_topbar_show_email', true ) ) : ?>
				<li class="pdo-topbar__item pdo-topbar__item--email">
					<?php pdo_icon( 'mail', array( 'size' => 14 ) ); ?>
					<a href="<?php echo esc_url( 'mailto:' . $pdo_email ); ?>"><?php echo esc_html( $pdo_email ); ?></a>
				</li>
			<?php endif; ?>
		</ul>

		<div class="pdo-topbar__aside">
			<?php if ( $pdo_socials && get_theme_mod( 'pdo_topbar_show_socials', true ) ) : ?>
				<ul class="pdo-socials pdo-socials--topbar">
					<?php foreach ( $pdo_socials as $pdo_social ) : ?>
						<?php
						$pdo_url = isset( $pdo_social['url'] ) ? (string) $pdo_social['url'] : '';
						if ( ! $pdo_url ) {
							continue;
						}
						$pdo_label = pdo_row_text( $pdo_social, 'label', '' );
						?>
						<li>
							<a href="<?php echo esc_url( $pdo_url ); ?>" rel="noopener" target="_blank">
								<?php pdo_icon( isset( $pdo_social['icon'] ) ? $pdo_social['icon'] : 'link', array( 'size' => 14 ) ); ?>
								<span class="screen-reader-text"><?php echo esc_html( $pdo_label ); ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php
			if ( get_theme_mod( 'pdo_topbar_show_lang', true ) ) {
				pdo_modern_language_switcher( 'topbar' );
			}
			?>

			<?php
			if ( has_nav_menu( 'topbar' ) ) {
				wp_nav_menu(
					array(
						'theme_location' => 'topbar',
						'container'      => false,
						'menu_class'     => 'pdo-topbar__menu',
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
			}
			?>
		</div>
	</div>
</div>
