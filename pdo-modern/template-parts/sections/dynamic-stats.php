<?php
/**
 * Dynamic section – statistics.
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
<div class="pdo-stats">
	<?php foreach ( $pdo_items as $pdo_item ) : ?>
		<?php
		$pdo_style = ! empty( $pdo_item['color'] ) ? ' style="--pdo-stat-color:' . esc_attr( $pdo_item['color'] ) . '"' : '';
		$pdo_value = isset( $pdo_item['value'] ) && '' !== $pdo_item['value'] ? $pdo_item['value'] : pdo_row_text( $pdo_item, 'title', '' );
		?>
		<div class="pdo-stat"<?php echo $pdo_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?>>
			<?php if ( ! empty( $pdo_item['icon'] ) ) : ?>
				<span class="pdo-stat__icon"><?php pdo_icon( $pdo_item['icon'], array( 'size' => 24 ) ); ?></span>
			<?php endif; ?>
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper.
			echo pdo_modern_stat_value( array( 'value' => $pdo_value ) );
			?>
			<?php if ( pdo_row_text( $pdo_item, 'subtitle', '' ) ) : ?>
				<span class="pdo-stat__label"><?php echo esc_html( pdo_row_text( $pdo_item, 'subtitle', '' ) ); ?></span>
			<?php endif; ?>
			<?php if ( pdo_row_text( $pdo_item, 'description', '' ) ) : ?>
				<span class="pdo-stat__desc"><?php echo esc_html( pdo_row_text( $pdo_item, 'description', '' ) ); ?></span>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>
<?php
pdo_modern_dynamic_button( $pdo_section );
pdo_modern_section_close();
