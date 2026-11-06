<?php
/**
 * Dynamic section – timeline.
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
<ol class="pdo-timeline">
	<?php foreach ( $pdo_items as $pdo_index => $pdo_item ) : ?>
		<li class="pdo-timeline__item" data-pdo-reveal>
			<span class="pdo-timeline__marker">
				<?php if ( ! empty( $pdo_item['icon'] ) ) : ?>
					<?php pdo_icon( $pdo_item['icon'], array( 'size' => 20 ) ); ?>
				<?php else : ?>
					<?php echo esc_html( ! empty( $pdo_item['value'] ) ? $pdo_item['value'] : (string) ( $pdo_index + 1 ) ); ?>
				<?php endif; ?>
			</span>
			<?php if ( pdo_row_text( $pdo_item, 'subtitle', '' ) ) : ?>
				<span class="pdo-timeline__year"><?php echo esc_html( pdo_row_text( $pdo_item, 'subtitle', '' ) ); ?></span>
			<?php endif; ?>
			<?php if ( pdo_row_text( $pdo_item, 'title', '' ) ) : ?>
				<h3 class="pdo-timeline__title"><?php echo esc_html( pdo_row_text( $pdo_item, 'title', '' ) ); ?></h3>
			<?php endif; ?>
			<?php if ( pdo_row_text( $pdo_item, 'description', '' ) ) : ?>
				<p class="pdo-timeline__text"><?php echo esc_html( pdo_row_text( $pdo_item, 'description', '' ) ); ?></p>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
</ol>
<?php
pdo_modern_dynamic_button( $pdo_section );
pdo_modern_section_close();
