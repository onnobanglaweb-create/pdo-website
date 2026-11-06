<?php
/**
 * Single team member.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$pdo_id    = (int) get_the_ID();
	$pdo_photo = pdo_post_image( $pdo_id, array( 'size' => 'pdo-portrait' ), 'photo' );
	$pdo_mail  = (string) pdo_raw_meta( $pdo_id, 'email', '' );
	$pdo_phone = (string) pdo_raw_meta( $pdo_id, 'phone', '' );

	pdo_modern_section_open( array( 'class' => 'pdo-single pdo-single--team' ) );
	?>
	<article id="post-<?php echo esc_attr( (string) $pdo_id ); ?>" <?php post_class( 'pdo-entry' ); ?>>
		<div class="pdo-split">
			<?php if ( $pdo_photo ) : ?>
				<figure class="pdo-entry__media">
					<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by the media helper.
					echo $pdo_photo;
					?>
				</figure>
			<?php endif; ?>

			<div>
				<h1 class="pdo-section__title"><?php echo esc_html( pdo_post_title( $pdo_id ) ); ?></h1>

				<?php if ( pdo_meta( $pdo_id, 'designation', '' ) ) : ?>
					<p class="pdo-person__role"><?php echo esc_html( pdo_meta( $pdo_id, 'designation', '' ) ); ?></p>
				<?php endif; ?>

				<div class="pdo-prose">
					<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered through the_content filters.
					echo pdo_post_content( $pdo_id );
					?>
				</div>

				<?php if ( $pdo_mail || $pdo_phone ) : ?>
					<ul class="pdo-drawer__contact">
						<?php if ( $pdo_mail ) : ?>
							<li><?php pdo_icon( 'mail', array( 'size' => 16 ) ); ?><a href="<?php echo esc_url( 'mailto:' . $pdo_mail ); ?>"><?php echo esc_html( $pdo_mail ); ?></a></li>
						<?php endif; ?>
						<?php if ( $pdo_phone ) : ?>
							<li><?php pdo_icon( 'phone', array( 'size' => 16 ) ); ?><a href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', $pdo_phone ) ); ?>"><?php echo esc_html( $pdo_phone ); ?></a></li>
						<?php endif; ?>
					</ul>
				<?php endif; ?>
			</div>
		</div>
	</article>
	<?php
	pdo_modern_section_close();

endwhile;

get_footer();
