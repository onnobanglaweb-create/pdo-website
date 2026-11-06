<?php
/**
 * Component – partner card.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

$pdo_post = isset( $args['post'] ) ? $args['post'] : get_post();
if ( ! $pdo_post ) {
	return;
}

$pdo_h = pdo_modern_heading_tag( $args, 3 );

$pdo_id      = (int) ( is_object( $pdo_post ) ? $pdo_post->ID : $pdo_post );
$pdo_name    = pdo_post_title( $pdo_id );
$pdo_site    = (string) pdo_raw_meta( $pdo_id, 'website', '' );
$pdo_logo    = pdo_post_image( $pdo_id, array( 'size' => 'pdo-thumb', 'alt' => $pdo_name ), 'logo' );
$pdo_accent  = (string) pdo_raw_meta( $pdo_id, 'accent', '' );
$pdo_summary = pdo_meta( $pdo_id, 'description', '' );
$pdo_tag     = $pdo_site ? 'a' : 'div';
?>
<<?php echo tag_escape( $pdo_tag ); ?>
	class="pdo-partner <?php echo esc_attr( pdo_modern_accent_class( $pdo_accent ) ); ?>"
	<?php if ( $pdo_site ) : ?>
		href="<?php echo esc_url( $pdo_site ); ?>" rel="noopener" target="_blank"
	<?php endif; ?>
>
	<?php if ( $pdo_logo ) : ?>
		<span class="pdo-partner__logo">
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by the media helper.
			echo $pdo_logo;
			?>
		</span>
	<?php else : ?>
		<span class="pdo-partner__mark">
			<?php pdo_icon( pdo_raw_meta( $pdo_id, 'icon', 'handshake' ), array( 'size' => 26 ) ); ?>
		</span>
	<?php endif; ?>

	<<?php echo esc_attr( $pdo_h ); ?> class="pdo-partner__name"><?php echo esc_html( $pdo_name ); ?></<?php echo esc_attr( $pdo_h ); ?>>

	<?php if ( $pdo_summary ) : ?>
		<p class="pdo-partner__text"><?php echo esc_html( $pdo_summary ); ?></p>
	<?php endif; ?>
</<?php echo tag_escape( $pdo_tag ); ?>>
