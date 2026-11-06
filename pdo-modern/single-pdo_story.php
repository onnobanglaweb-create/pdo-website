<?php
/**
 * Single impact story.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$pdo_id    = (int) get_the_ID();
	$pdo_photo = pdo_post_image( $pdo_id, array( 'size' => 'full', 'loading' => 'eager' ), 'photo' );
	$pdo_quote = pdo_meta( $pdo_id, 'quote', '' );

	pdo_modern_section_open( array( 'class' => 'pdo-single pdo-single--story' ) );
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
				<?php if ( pdo_meta( $pdo_id, 'tag', '' ) ) : ?>
					<p class="pdo-eyebrow"><?php echo esc_html( pdo_meta( $pdo_id, 'tag', '' ) ); ?></p>
				<?php endif; ?>

				<h1 class="pdo-section__title"><?php echo esc_html( pdo_post_title( $pdo_id ) ); ?></h1>

				<p class="pdo-meta">
					<?php if ( pdo_meta( $pdo_id, 'beneficiary', '' ) ) : ?>
						<span><?php pdo_icon( 'user', array( 'size' => 14 ) ); ?><?php echo esc_html( pdo_meta( $pdo_id, 'beneficiary', '' ) ); ?></span>
					<?php endif; ?>
					<?php if ( pdo_meta( $pdo_id, 'location', '' ) ) : ?>
						<span><?php pdo_icon( 'map-pin', array( 'size' => 14 ) ); ?><?php echo esc_html( pdo_meta( $pdo_id, 'location', '' ) ); ?></span>
					<?php endif; ?>
					<?php if ( pdo_raw_meta( $pdo_id, 'story_date', '' ) ) : ?>
						<span><?php pdo_icon( 'calendar', array( 'size' => 14 ) ); ?><?php echo esc_html( pdo_raw_meta( $pdo_id, 'story_date', '' ) ); ?></span>
					<?php endif; ?>
				</p>

				<?php if ( $pdo_quote ) : ?>
					<blockquote class="pdo-story__quote"><?php echo esc_html( $pdo_quote ); ?></blockquote>
				<?php endif; ?>
			</div>
		</div>

		<div class="pdo-prose pdo-container--narrow">
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered through the_content filters.
			echo pdo_post_content( $pdo_id );
			?>
		</div>

		<?php
		$pdo_program = (int) pdo_raw_meta( $pdo_id, 'program', 0 );
		if ( $pdo_program && 'publish' === get_post_status( $pdo_program ) ) :
			?>
			<div class="pdo-container--narrow">
				<p class="pdo-section__cta">
					<?php
					pdo_modern_button(
						/* translators: %s: program title. */
						sprintf( __( 'About the %s program', 'pdo-modern' ), pdo_post_title( $pdo_program ) ),
						(string) get_permalink( $pdo_program ),
						array( 'style' => 'outline', 'icon' => 'arrow-right' )
					);
					?>
				</p>
			</div>
		<?php endif; ?>

		<footer class="pdo-entry__footer pdo-container--narrow">
			<?php get_template_part( 'template-parts/components/share', null, array( 'post_id' => $pdo_id ) ); ?>
		</footer>
	</article>
	<?php
	pdo_modern_section_close();

endwhile;

get_footer();
