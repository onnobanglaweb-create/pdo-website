<?php
/**
 * Component – team member card.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

$pdo_post = isset( $args['post'] ) ? $args['post'] : get_post();
if ( ! $pdo_post ) {
	return;
}

$pdo_h = pdo_modern_heading_tag( $args, 3 );

$pdo_id    = (int) ( is_object( $pdo_post ) ? $pdo_post->ID : $pdo_post );
$pdo_name  = pdo_post_title( $pdo_id );
$pdo_photo = pdo_post_image( $pdo_id, array( 'size' => 'pdo-thumb', 'alt' => $pdo_name ), 'photo' );
$pdo_role  = pdo_meta( $pdo_id, 'designation', '' );
$pdo_bio   = pdo_post_excerpt( $pdo_id );
$pdo_mail  = (string) pdo_raw_meta( $pdo_id, 'email', '' );
$pdo_socials = (array) pdo_raw_meta( $pdo_id, 'socials', array() );
$pdo_compact = ! empty( $args['compact'] );
?>
<article class="pdo-person<?php echo $pdo_compact ? ' pdo-person--compact' : ''; ?>" data-pdo-reveal>
	<div class="pdo-person__photo">
		<?php if ( $pdo_photo ) : ?>
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by the media helper.
			echo $pdo_photo;
			?>
		<?php else : ?>
			<span class="pdo-person__initials" aria-hidden="true"><?php echo esc_html( mb_substr( $pdo_name, 0, 1 ) ); ?></span>
		<?php endif; ?>
	</div>

	<div class="pdo-person__body">
		<<?php echo esc_attr( $pdo_h ); ?> class="pdo-person__name"><?php echo esc_html( $pdo_name ); ?></<?php echo esc_attr( $pdo_h ); ?>>

		<?php if ( $pdo_role ) : ?>
			<span class="pdo-person__role"><?php echo esc_html( $pdo_role ); ?></span>
		<?php endif; ?>

		<?php if ( $pdo_bio && ! $pdo_compact ) : ?>
			<p class="pdo-person__bio"><?php echo esc_html( pdo_modern_trim_words( $pdo_bio, 24 ) ); ?></p>
		<?php endif; ?>

		<?php if ( $pdo_socials || $pdo_mail ) : ?>
			<ul class="pdo-socials pdo-socials--person">
				<?php if ( $pdo_mail ) : ?>
					<li>
						<a href="<?php echo esc_url( 'mailto:' . $pdo_mail ); ?>">
							<?php pdo_icon( 'mail', array( 'size' => 15 ) ); ?>
							<span class="screen-reader-text"><?php echo esc_html( $pdo_name . ' — ' . __( 'email', 'pdo-modern' ) ); ?></span>
						</a>
					</li>
				<?php endif; ?>
				<?php foreach ( pdo_visible_rows( $pdo_socials ) as $pdo_social ) : ?>
					<?php if ( empty( $pdo_social['url'] ) ) { continue; } ?>
					<li>
						<a href="<?php echo esc_url( $pdo_social['url'] ); ?>" rel="noopener" target="_blank">
							<?php pdo_icon( isset( $pdo_social['icon'] ) ? $pdo_social['icon'] : 'link', array( 'size' => 15 ) ); ?>
							<span class="screen-reader-text"><?php echo esc_html( isset( $pdo_social['label'] ) ? $pdo_social['label'] : '' ); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</article>
