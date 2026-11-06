<?php
/**
 * Dynamic section – hero banner.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

$pdo_section = pdo_modern_section_args( isset( $args ) ? $args : array() );
if ( ! $pdo_section ) {
	return;
}

$pdo_bg_id = (int) ( isset( $pdo_section['image'] ) ? $pdo_section['image'] : 0 );

pdo_modern_dynamic_open( $pdo_section, 'pdo-page-hero' );
?>
<div class="pdo-hero__content pdo-align-<?php echo esc_attr( ! empty( $pdo_section['align'] ) ? $pdo_section['align'] : 'center' ); ?>">
	<?php if ( pdo_modern_section_text( $pdo_section, 'eyebrow', '' ) ) : ?>
		<p class="pdo-eyebrow">
			<?php if ( ! empty( $pdo_section['icon'] ) ) { pdo_icon( $pdo_section['icon'], array( 'size' => 15 ) ); } ?>
			<?php echo esc_html( pdo_modern_section_text( $pdo_section, 'eyebrow', '' ) ); ?>
		</p>
	<?php endif; ?>

	<?php if ( pdo_modern_section_text( $pdo_section, 'heading', '' ) ) : ?>
		<h2 class="pdo-hero__title"><?php echo esc_html( pdo_modern_section_text( $pdo_section, 'heading', '' ) ); ?></h2>
	<?php endif; ?>

	<?php if ( pdo_modern_section_text( $pdo_section, 'content', '' ) ) : ?>
		<p class="pdo-hero__text"><?php echo esc_html( pdo_modern_section_text( $pdo_section, 'content', '' ) ); ?></p>
	<?php endif; ?>

	<?php pdo_modern_dynamic_button( $pdo_section ); ?>
</div>

<?php if ( $pdo_bg_id ) : ?>
	<figure class="pdo-dynamic__media">
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by the media helper.
		echo pdo_image( $pdo_bg_id, '', array( 'size' => 'full' ) );
		?>
	</figure>
<?php endif; ?>
<?php
pdo_modern_section_close();
