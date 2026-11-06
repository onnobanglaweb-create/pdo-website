<?php
/**
 * Component – program card.
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
$pdo_accent = (string) pdo_raw_meta( $pdo_id, 'accent', '' );
$pdo_short  = pdo_meta( $pdo_id, 'short_name', '' );
$pdo_sdgs   = pdo_meta( $pdo_id, 'sdgs', '' );
$pdo_cta    = (string) pdo_raw_meta( $pdo_id, 'cta_url', '' );
$pdo_excerpt = pdo_post_excerpt( $pdo_id );
?>
<article class="pdo-card <?php echo esc_attr( pdo_modern_accent_class( $pdo_accent ) ); ?>" data-pdo-reveal>
	<?php if ( $pdo_media ) : ?>
		<div class="pdo-card__media">
			<a href="<?php echo esc_url( (string) get_permalink( $pdo_id ) ); ?>" tabindex="-1" aria-hidden="true">
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by the media helper.
				echo $pdo_media;
				?>
			</a>
			<?php if ( $pdo_short ) : ?>
				<span class="pdo-card__tag"><?php echo esc_html( $pdo_short ); ?></span>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<div class="pdo-card__body">
		<?php if ( ! $pdo_media && ! empty( pdo_raw_meta( $pdo_id, 'icon', '' ) ) ) : ?>
			<span class="pdo-card__icon"><?php pdo_icon( pdo_raw_meta( $pdo_id, 'icon', 'sprout' ), array( 'size' => 24 ) ); ?></span>
		<?php endif; ?>

		<<?php echo esc_attr( $pdo_h ); ?> class="pdo-card__title">
			<a href="<?php echo esc_url( (string) get_permalink( $pdo_id ) ); ?>"><?php echo esc_html( $pdo_title ); ?></a>
		</<?php echo esc_attr( $pdo_h ); ?>>

		<?php if ( $pdo_excerpt ) : ?>
			<p class="pdo-card__text"><?php echo esc_html( pdo_modern_trim_words( $pdo_excerpt, 26 ) ); ?></p>
		<?php endif; ?>

		<div class="pdo-card__footer">
			<?php if ( $pdo_sdgs ) : ?>
				<span class="pdo-badge pdo-badge--outline"><?php echo esc_html( $pdo_sdgs ); ?></span>
			<?php endif; ?>
			<a class="pdo-readmore" href="<?php echo esc_url( $pdo_cta ? $pdo_cta : (string) get_permalink( $pdo_id ) ); ?>">
				<?php echo esc_html( pdo_meta( $pdo_id, 'cta_label', pdo_t( 'read_more', __( 'Read more', 'pdo-modern' ) ) ) ); ?>
			</a>
		</div>
	</div>
</article>
