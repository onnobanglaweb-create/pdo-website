<?php
/**
 * Component – standard WordPress post card.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

$pdo_post = isset( $args['post'] ) ? $args['post'] : get_post();
if ( ! $pdo_post ) {
	return;
}

$pdo_id = (int) ( is_object( $pdo_post ) ? $pdo_post->ID : $pdo_post );
?>
<article id="post-<?php echo esc_attr( (string) $pdo_id ); ?>" <?php post_class( 'pdo-card' ); ?>>
	<?php if ( has_post_thumbnail( $pdo_id ) ) : ?>
		<div class="pdo-card__media">
			<a href="<?php echo esc_url( (string) get_permalink( $pdo_id ) ); ?>" tabindex="-1" aria-hidden="true">
				<?php echo get_the_post_thumbnail( $pdo_id, 'pdo-card' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core escapes. ?>
			</a>
		</div>
	<?php endif; ?>

	<div class="pdo-card__body">
		<h2 class="pdo-card__title">
			<a href="<?php echo esc_url( (string) get_permalink( $pdo_id ) ); ?>"><?php echo esc_html( get_the_title( $pdo_id ) ); ?></a>
		</h2>

		<p class="pdo-card__text"><?php echo esc_html( pdo_modern_trim_words( get_the_excerpt( $pdo_id ), 26 ) ); ?></p>

		<div class="pdo-card__footer">
			<?php pdo_modern_post_meta( $pdo_id ); ?>
			<a class="pdo-readmore" href="<?php echo esc_url( (string) get_permalink( $pdo_id ) ); ?>">
				<?php echo esc_html( pdo_t( 'read_more', __( 'Read more', 'pdo-modern' ) ) ); ?>
			</a>
		</div>
	</div>
</article>
