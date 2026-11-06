<?php
/**
 * Component – human story card.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

$pdo_post = isset( $args['post'] ) ? $args['post'] : get_post();
if ( ! $pdo_post ) {
	return;
}

$pdo_id     = (int) ( is_object( $pdo_post ) ? $pdo_post->ID : $pdo_post );
$pdo_title  = pdo_post_title( $pdo_id );
$pdo_media  = pdo_post_image( $pdo_id, array( 'size' => 'pdo-card', 'alt' => $pdo_title ), 'photo' );
$pdo_quote  = pdo_meta( $pdo_id, 'quote', '' );
$pdo_who    = pdo_meta( $pdo_id, 'beneficiary', '' );
$pdo_place  = pdo_meta( $pdo_id, 'location', '' );
$pdo_tag    = pdo_meta( $pdo_id, 'tag', '' );
?>
<article class="pdo-story" data-pdo-reveal>
	<?php if ( $pdo_media ) : ?>
		<div class="pdo-story__media">
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by the media helper.
			echo $pdo_media;
			?>
		</div>
	<?php endif; ?>

	<div class="pdo-story__body">
		<?php if ( $pdo_tag ) : ?>
			<span class="pdo-badge pdo-badge--accent"><?php echo esc_html( $pdo_tag ); ?></span>
		<?php endif; ?>

		<h3 class="pdo-card__title">
			<a href="<?php echo esc_url( (string) get_permalink( $pdo_id ) ); ?>"><?php echo esc_html( $pdo_title ); ?></a>
		</h3>

		<?php if ( $pdo_quote ) : ?>
			<blockquote class="pdo-story__quote"><?php echo esc_html( $pdo_quote ); ?></blockquote>
		<?php else : ?>
			<p class="pdo-card__text"><?php echo esc_html( pdo_modern_trim_words( pdo_post_excerpt( $pdo_id ), 26 ) ); ?></p>
		<?php endif; ?>

		<p class="pdo-story__who">
			<?php echo esc_html( trim( $pdo_who . ( $pdo_who && $pdo_place ? ' · ' : '' ) . $pdo_place ) ); ?>
		</p>
	</div>
</article>
