<?php
/**
 * Component – previous / next links.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

$pdo_prev = get_previous_post();
$pdo_next = get_next_post();

if ( ! $pdo_prev && ! $pdo_next ) {
	return;
}
?>
<nav class="pdo-postnav" aria-label="<?php esc_attr_e( 'More articles', 'pdo-modern' ); ?>">
	<?php if ( $pdo_prev ) : ?>
		<a class="pdo-postnav__link pdo-postnav__link--prev" href="<?php echo esc_url( (string) get_permalink( $pdo_prev ) ); ?>">
			<span class="pdo-postnav__label"><?php esc_html_e( 'Previous', 'pdo-modern' ); ?></span>
			<span class="pdo-postnav__title"><?php echo esc_html( pdo_post_title( $pdo_prev ) ); ?></span>
		</a>
	<?php endif; ?>

	<?php if ( $pdo_next ) : ?>
		<a class="pdo-postnav__link pdo-postnav__link--next" href="<?php echo esc_url( (string) get_permalink( $pdo_next ) ); ?>">
			<span class="pdo-postnav__label"><?php esc_html_e( 'Next', 'pdo-modern' ); ?></span>
			<span class="pdo-postnav__title"><?php echo esc_html( pdo_post_title( $pdo_next ) ); ?></span>
		</a>
	<?php endif; ?>
</nav>
