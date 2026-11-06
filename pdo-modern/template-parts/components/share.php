<?php
/**
 * Component – share links.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

$pdo_id = isset( $args['post_id'] ) ? (int) $args['post_id'] : (int) get_the_ID();

if ( ! $pdo_id ) {
	return;
}

$pdo_links = pdo_share_links( $pdo_id );

if ( ! $pdo_links ) {
	return;
}
?>
<div class="pdo-share">
	<span class="pdo-share__label"><?php echo esc_html( pdo_t( 'share', __( 'Share this page', 'pdo-modern' ) ) ); ?></span>
	<?php foreach ( $pdo_links as $pdo_key => $pdo_link ) : ?>
		<a
			href="<?php echo esc_url( $pdo_link['url'] ); ?>"
			<?php echo 'email' === $pdo_key ? '' : 'target="_blank" rel="noopener nofollow"'; ?>
		>
			<?php pdo_icon( $pdo_link['icon'], array( 'size' => 16 ) ); ?>
			<span class="screen-reader-text"><?php echo esc_html( $pdo_link['label'] ); ?></span>
		</a>
	<?php endforeach; ?>
</div>
