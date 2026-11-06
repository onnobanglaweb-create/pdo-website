<?php
/**
 * Single news article.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$pdo_id    = (int) get_the_ID();
	$pdo_terms = get_the_terms( $pdo_id, 'pdo_news_cat' );
	$pdo_tags  = get_the_terms( $pdo_id, 'pdo_news_tag' );

	pdo_modern_section_open( array( 'class' => 'pdo-single pdo-single--news' ) );
	?>
	<article id="post-<?php echo esc_attr( (string) $pdo_id ); ?>" <?php post_class( 'pdo-entry pdo-container--narrow' ); ?>>
		<header class="pdo-entry__header">
			<?php if ( $pdo_terms && ! is_wp_error( $pdo_terms ) ) : ?>
				<p class="pdo-eyebrow">
					<?php pdo_icon( 'newspaper', array( 'size' => 14 ) ); ?>
					<?php echo esc_html( $pdo_terms[0]->name ); ?>
				</p>
			<?php endif; ?>

			<?php if ( pdo_meta( $pdo_id, 'kicker', '' ) ) : ?>
				<p class="pdo-card__kicker"><?php echo esc_html( pdo_meta( $pdo_id, 'kicker', '' ) ); ?></p>
			<?php endif; ?>

			<h1 class="pdo-section__title"><?php echo esc_html( pdo_post_title( $pdo_id ) ); ?></h1>

			<?php pdo_modern_post_meta( $pdo_id ); ?>
		</header>

		<?php
		$pdo_media = pdo_post_image( $pdo_id, array( 'size' => 'full', 'loading' => 'eager' ) );
		if ( $pdo_media ) :
			?>
			<figure class="pdo-entry__media">
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by the media helper.
				echo $pdo_media;
				?>
			</figure>
		<?php endif; ?>

		<div class="pdo-prose">
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered through the_content filters.
			echo pdo_post_content( $pdo_id );
			?>
		</div>

		<footer class="pdo-entry__footer">
			<?php if ( $pdo_tags && ! is_wp_error( $pdo_tags ) ) : ?>
				<ul class="pdo-taglist">
					<?php foreach ( $pdo_tags as $pdo_tag ) : ?>
						<li><a href="<?php echo esc_url( (string) get_term_link( $pdo_tag ) ); ?>">#<?php echo esc_html( $pdo_tag->name ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if ( pdo_setting( 'media', 'show_share', true ) ) : ?>
				<?php get_template_part( 'template-parts/components/share', null, array( 'post_id' => $pdo_id ) ); ?>
			<?php endif; ?>
		</footer>
	</article>
	<?php
	pdo_modern_section_close();

	/* Related articles. */
	if ( pdo_setting( 'media', 'show_related', true ) ) :
		$pdo_related = pdo_related_news( $pdo_id, 3 );

		if ( $pdo_related ) :
			pdo_modern_section_open(
				array(
					'class'      => 'pdo-section--tight',
					'background' => 'light',
				)
			);

			pdo_modern_section_header( array( 'title' => __( 'Related updates', 'pdo-modern' ) ) );
			?>
			<div class="pdo-grid pdo-grid--3">
				<?php foreach ( $pdo_related as $pdo_item ) : ?>
					<?php get_template_part( 'template-parts/components/card', 'news', array( 'post' => $pdo_item ) ); ?>
				<?php endforeach; ?>
			</div>
			<?php
			pdo_modern_section_close();
		endif;
	endif;

endwhile;

get_footer();
