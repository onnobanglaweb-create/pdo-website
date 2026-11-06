<?php
/**
 * Single partner.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$pdo_id   = (int) get_the_ID();
	$pdo_logo = pdo_post_image( $pdo_id, array( 'size' => 'pdo-card' ), 'logo' );
	$pdo_site = (string) pdo_raw_meta( $pdo_id, 'website', '' );

	pdo_modern_section_open( array( 'class' => 'pdo-single pdo-single--partner' ) );
	?>
	<article id="post-<?php echo esc_attr( (string) $pdo_id ); ?>" <?php post_class( 'pdo-entry pdo-container--narrow' ); ?>>
		<header class="pdo-entry__header pdo-align-center">
			<?php if ( $pdo_logo ) : ?>
				<figure class="pdo-partner__logo">
					<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by the media helper.
					echo $pdo_logo;
					?>
				</figure>
			<?php endif; ?>
			<h1 class="pdo-section__title"><?php echo esc_html( pdo_post_title( $pdo_id ) ); ?></h1>
			<?php if ( pdo_meta( $pdo_id, 'description', '' ) ) : ?>
				<p class="pdo-section__lede"><?php echo esc_html( pdo_meta( $pdo_id, 'description', '' ) ); ?></p>
			<?php endif; ?>
		</header>

		<div class="pdo-prose">
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered through the_content filters.
			echo pdo_post_content( $pdo_id );
			?>
		</div>

		<?php if ( $pdo_site ) : ?>
			<p class="pdo-section__cta pdo-align-center">
				<?php pdo_modern_button( __( 'Visit the website', 'pdo-modern' ), $pdo_site, array( 'style' => 'outline', 'icon' => 'external-link' ) ); ?>
			</p>
		<?php endif; ?>
	</article>
	<?php
	pdo_modern_section_close();

endwhile;

get_footer();
