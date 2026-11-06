<?php
/**
 * Dynamic section – call to action.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

$pdo_section = pdo_modern_section_args( isset( $args ) ? $args : array() );
if ( ! $pdo_section ) {
	return;
}

pdo_modern_dynamic_open( $pdo_section, 'pdo-cta' );
?>
<div class="pdo-cta__inner" data-pdo-reveal>
	<?php if ( pdo_modern_section_text( $pdo_section, 'eyebrow', '' ) ) : ?>
		<p class="pdo-eyebrow"><?php echo esc_html( pdo_modern_section_text( $pdo_section, 'eyebrow', '' ) ); ?></p>
	<?php endif; ?>
	<?php if ( pdo_modern_section_text( $pdo_section, 'heading', '' ) ) : ?>
		<h2><?php echo esc_html( pdo_modern_section_text( $pdo_section, 'heading', '' ) ); ?></h2>
	<?php endif; ?>
	<?php if ( pdo_modern_section_text( $pdo_section, 'content', '' ) ) : ?>
		<p><?php echo esc_html( pdo_modern_section_text( $pdo_section, 'content', '' ) ); ?></p>
	<?php endif; ?>
	<?php pdo_modern_dynamic_button( $pdo_section, 'light' ); ?>
</div>
<?php
pdo_modern_section_close();
