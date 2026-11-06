<?php
/**
 * Component – news card.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

$pdo_post = isset( $args['post'] ) ? $args['post'] : get_post();
if ( ! $pdo_post ) {
	return;
}

$pdo_h = pdo_modern_heading_tag( $args, 3 );

$pdo_id     = (int) ( is_object( $pdo_post ) ? $pdo_post->ID : $pdo_post );
$pdo_title  = pdo_post_title( $pdo_id );
$pdo_media  = pdo_post_image( $pdo_id, array( 'size' => 'pdo-card', 'alt' => $pdo_title ) );
$pdo_kicker = pdo_meta( $pdo_id, 'kicker', '' );
$pdo_terms  = get_the_terms( $pdo_id, 'pdo_news_cat' );
$pdo_slugs  = array();

if ( $pdo_terms && ! is_wp_error( $pdo_terms ) ) {
	foreach ( $pdo_terms as $pdo_term ) {
		$pdo_slugs[] = $pdo_term->slug;
	}
}
?>
<article class="pdo-card pdo-card--news" data-pdo-reveal data-pdo-terms="<?php echo esc_attr( implode( ' ', $pdo_slugs ) ); ?>">
	<?php if ( $pdo_media ) : ?>
		<div class="pdo-card__media">
			<a href="<?php echo esc_url( (string) get_permalink( $pdo_id ) ); ?>" tabindex="-1" aria-hidden="true">
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by the media helper.
				echo $pdo_media;
				?>
			</a>
			<?php if ( $pdo_terms && ! is_wp_error( $pdo_terms ) ) : ?>
				<span class="pdo-card__tag"><?php echo esc_html( $pdo_terms[0]->name ); ?></span>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<div class="pdo-card__body">
		<?php if ( $pdo_kicker ) : ?>
			<p class="pdo-card__kicker"><?php echo esc_html( $pdo_kicker ); ?></p>
		<?php endif; ?>

		<<?php echo esc_attr( $pdo_h ); ?> class="pdo-card__title">
			<a href="<?php echo esc_url( (string) get_permalink( $pdo_id ) ); ?>"><?php echo esc_html( $pdo_title ); ?></a>
		</<?php echo esc_attr( $pdo_h ); ?>>

		<?php $pdo_excerpt = pdo_post_excerpt( $pdo_id ); ?>
		<?php if ( $pdo_excerpt ) : ?>
			<p class="pdo-card__text"><?php echo esc_html( pdo_modern_trim_words( $pdo_excerpt, 22 ) ); ?></p>
		<?php endif; ?>

		<div class="pdo-card__footer">
			<?php pdo_modern_post_meta( $pdo_id, false ); ?>
			<a class="pdo-readmore" href="<?php echo esc_url( (string) get_permalink( $pdo_id ) ); ?>">
				<?php echo esc_html( pdo_t( 'read_more', __( 'Read more', 'pdo-modern' ) ) ); ?>
			</a>
		</div>
	</div>
</article>
