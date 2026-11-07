<?php
/**
 * Site footer.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

$pdo_columns = max( 1, min( 5, (int) get_theme_mod( 'pdo_footer_columns', 4 ) ) );
$pdo_socials = pdo_rows( 'org', 'socials' );
$pdo_phone   = pdo_setting( 'org', 'phone', '' );
$pdo_email   = pdo_setting( 'org', 'email', '' );
$pdo_address = pdo_text( 'org', 'address', '' );
?>
	</main><!-- .pdo-main -->

	<?php pdo_modern_dynamic_sections( 'global_bottom', 'before' ); ?>

	<footer id="pdo-colophon" class="pdo-footer">
		<div class="pdo-container">
			<div class="pdo-footer__grid pdo-footer__grid--<?php echo esc_attr( (string) $pdo_columns ); ?>">

				<?php if ( get_theme_mod( 'pdo_footer_about', true ) ) : ?>
					<div class="pdo-footer__col pdo-footer__col--brand">
						<?php pdo_modern_logo( 'footer' ); ?>

						<?php if ( pdo_org( 'description', '' ) ) : ?>
							<p class="pdo-footer__text"><?php echo esc_html( pdo_org( 'description', '' ) ); ?></p>
						<?php endif; ?>

						<?php if ( get_theme_mod( 'pdo_footer_badge', true ) ) : ?>
							<?php
							$pdo_reg       = pdo_setting( 'org', 'reg_number', '' );
							$pdo_authority = pdo_text( 'org', 'reg_authority', '' );
							if ( $pdo_reg ) :
								?>
								<p class="pdo-footer__badge">
									<?php pdo_icon( 'shield-check', array( 'size' => 16 ) ); ?>
									<span>
										<?php echo esc_html( $pdo_authority ); ?>
										<strong><?php echo esc_html( $pdo_reg ); ?></strong>
									</span>
								</p>
							<?php endif; ?>
						<?php endif; ?>

						<?php if ( pdo_text( 'org', 'compliance_note', '' ) ) : ?>
							<p class="pdo-footer__small"><?php echo esc_html( pdo_text( 'org', 'compliance_note', '' ) ); ?></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( get_theme_mod( 'pdo_footer_nav', true ) && has_nav_menu( 'footer' ) ) : ?>
					<div class="pdo-footer__col">
						<h2 class="pdo-footer__heading"><?php echo esc_html( pdo_t( 'quick_links', __( 'Quick Navigation', 'pdo-modern' ) ) ); ?></h2>
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'footer',
								'container'      => 'nav',
								'container_class' => 'pdo-footer__nav',
								'container_aria_label' => __( 'Footer navigation', 'pdo-modern' ),
								'menu_class'     => 'pdo-footer__menu',
								'depth'          => 1,
								'fallback_cb'    => false,
							)
						);
						?>
					</div>
				<?php endif; ?>

				<?php if ( get_theme_mod( 'pdo_footer_programs', true ) ) : ?>
					<div class="pdo-footer__col">
						<h2 class="pdo-footer__heading"><?php echo esc_html( pdo_t( 'flagship_programs', __( 'Flagship Programs', 'pdo-modern' ) ) ); ?></h2>
						<?php if ( has_nav_menu( 'footer_programs' ) ) : ?>
							<?php
							wp_nav_menu(
								array(
									'theme_location' => 'footer_programs',
									'container'      => 'nav',
									'container_class' => 'pdo-footer__nav',
									'container_aria_label' => __( 'Flagship programs', 'pdo-modern' ),
									'menu_class'     => 'pdo-footer__menu',
									'depth'          => 1,
									'fallback_cb'    => false,
								)
							);
							?>
						<?php else : ?>
							<?php $pdo_programs = pdo_get_posts( 'pdo_program', array( 'posts_per_page' => 6 ) ); ?>
							<?php if ( $pdo_programs ) : ?>
								<ul class="pdo-footer__menu">
									<?php foreach ( $pdo_programs as $pdo_program ) : ?>
										<li><a href="<?php echo esc_url( (string) get_permalink( $pdo_program ) ); ?>"><?php echo esc_html( get_the_title( $pdo_program ) ); ?></a></li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( get_theme_mod( 'pdo_footer_contact', true ) ) : ?>
					<div class="pdo-footer__col">
						<h2 class="pdo-footer__heading"><?php echo esc_html( pdo_t( 'connect', __( 'Connect With Us', 'pdo-modern' ) ) ); ?></h2>

						<ul class="pdo-footer__contact">
							<?php if ( $pdo_address ) : ?>
								<li>
									<?php pdo_icon( 'map-pin', array( 'size' => 16 ) ); ?>
									<span><?php echo nl2br( esc_html( $pdo_address ) ); ?></span>
								</li>
							<?php endif; ?>
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

						<?php if ( $pdo_socials && get_theme_mod( 'pdo_footer_socials', true ) ) : ?>
							<ul class="pdo-socials pdo-socials--footer">
								<?php foreach ( $pdo_socials as $pdo_social ) : ?>
									<?php
									$pdo_url = isset( $pdo_social['url'] ) ? (string) $pdo_social['url'] : '';
									if ( ! $pdo_url ) {
										continue;
									}
									?>
									<li>
										<a href="<?php echo esc_url( $pdo_url ); ?>" rel="noopener" target="_blank">
											<?php pdo_icon( isset( $pdo_social['icon'] ) ? $pdo_social['icon'] : 'link', array( 'size' => 18 ) ); ?>
											<span class="screen-reader-text"><?php echo esc_html( pdo_row_text( $pdo_social, 'label', '' ) ); ?></span>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>

						<?php if ( get_theme_mod( 'pdo_footer_newsletter', true ) && pdo_text( 'org', 'response_time', '' ) ) : ?>
							<p class="pdo-footer__small"><?php echo esc_html( pdo_text( 'org', 'response_time', '' ) ); ?></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( is_active_sidebar( 'footer-extra' ) ) : ?>
					<div class="pdo-footer__col pdo-footer__col--widgets">
						<?php dynamic_sidebar( 'footer-extra' ); ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="pdo-footer__bottom">
				<p class="pdo-footer__copyright">
					<?php
					$pdo_copyright = pdo_text( 'org', 'copyright', '' );
					if ( $pdo_copyright ) {
						echo esc_html( $pdo_copyright );
					} else {
						printf(
							/* translators: 1: year, 2: site name. */
							esc_html__( '© %1$s %2$s. All rights reserved.', 'pdo-modern' ),
							esc_html( gmdate( 'Y' ) ),
							esc_html( pdo_org( 'name', get_bloginfo( 'name' ) ) )
						);
					}
					?>
				</p>

				<?php if ( pdo_text( 'org', 'legal_notice', '' ) ) : ?>
					<p class="pdo-footer__legal"><?php echo esc_html( pdo_text( 'org', 'legal_notice', '' ) ); ?></p>
				<?php endif; ?>

				<?php if ( has_nav_menu( 'legal' ) ) : ?>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'legal',
							'container'      => 'nav',
							'container_class' => 'pdo-footer__legal-nav',
							'container_aria_label' => __( 'Legal links', 'pdo-modern' ),
							'menu_class'     => 'pdo-footer__legal-menu',
							'depth'          => 1,
							'fallback_cb'    => false,
						)
					);
					?>
				<?php endif; ?>

				<?php if ( get_theme_mod( 'pdo_footer_credit', '' ) ) : ?>
					<p class="pdo-footer__credit"><?php echo wp_kses_post( (string) get_theme_mod( 'pdo_footer_credit', '' ) ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</footer>

	<?php pdo_modern_dynamic_sections( 'global_bottom', 'after' ); ?>

	<?php if ( get_theme_mod( 'pdo_back_to_top', true ) ) : ?>
		<button type="button" class="pdo-totop" data-pdo-totop hidden>
			<?php pdo_icon( 'arrow-up', array( 'size' => 18 ) ); ?>
			<span class="screen-reader-text"><?php esc_html_e( 'Back to top', 'pdo-modern' ); ?></span>
		</button>
	<?php endif; ?>
</div><!-- #pdo-page -->

<?php wp_footer(); ?>
</body>
</html>
