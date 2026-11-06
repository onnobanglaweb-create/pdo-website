<?php
/**
 * Dynamic section – gallery.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

$pdo_section = pdo_modern_section_args( isset( $args ) ? $args : array() );
if ( ! $pdo_section ) {
	return;
}

$pdo_items = pdo_modern_dynamic_items( $pdo_section );
if ( ! $pdo_items ) {
	return;
}

pdo_modern_dynamic_open( $pdo_section );
pdo_modern_dynamic_header( $pdo_section );
?>
<ul class="pdo-gallery">
	<?php foreach ( $pdo_items as $pdo_item ) : ?>
		<?php
		$pdo_tag = pdo_row_image( $pdo_item, array( 'size' => 'pdo-card', 'alt' => isset( $pdo_item['alt'] ) ? $pdo_item['alt'] : '' ) );
		if ( ! $pdo_tag ) {
			continue;
		}
		?>
		<li class="pdo-gallery__item">
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by the media helper.
			echo $pdo_tag;
			?>
			<?php if ( pdo_row_text( $pdo_item, 'title', '' ) ) : ?>
				<span class="pdo-gallery__caption"><?php echo esc_html( pdo_row_text( $pdo_item, 'title', '' ) ); ?></span>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
</ul>
<?php
pdo_modern_dynamic_button( $pdo_section );
pdo_modern_section_close();
