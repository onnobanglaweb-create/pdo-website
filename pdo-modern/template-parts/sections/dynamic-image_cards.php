<?php
/**
 * Dynamic section – image cards.
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
<div class="pdo-grid <?php echo esc_attr( pdo_modern_columns_class( isset( $pdo_section['columns'] ) ? $pdo_section['columns'] : 3 ) ); ?>">
	<?php foreach ( $pdo_items as $pdo_item ) : ?>
		<?php $pdo_tag = pdo_row_image( $pdo_item, array( 'size' => 'pdo-card', 'alt' => isset( $pdo_item['alt'] ) ? $pdo_item['alt'] : '' ) ); ?>
		<article class="pdo-card" data-pdo-reveal>
			<?php if ( $pdo_tag ) : ?>
				<div class="pdo-card__media">
					<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by the media helper.
					echo $pdo_tag;
					?>
					<?php if ( pdo_row_text( $pdo_item, 'subtitle', '' ) ) : ?>
						<span class="pdo-card__tag"><?php echo esc_html( pdo_row_text( $pdo_item, 'subtitle', '' ) ); ?></span>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<div class="pdo-card__body">
				<?php if ( pdo_row_text( $pdo_item, 'title', '' ) ) : ?>
					<h3 class="pdo-card__title"><?php echo esc_html( pdo_row_text( $pdo_item, 'title', '' ) ); ?></h3>
				<?php endif; ?>
				<?php if ( pdo_row_text( $pdo_item, 'description', '' ) ) : ?>
					<p class="pdo-card__text"><?php echo esc_html( pdo_row_text( $pdo_item, 'description', '' ) ); ?></p>
				<?php endif; ?>
				<?php if ( ! empty( $pdo_item['link_url'] ) ) : ?>
					<div class="pdo-card__footer">
						<a class="pdo-readmore" href="<?php echo esc_url( $pdo_item['link_url'] ); ?>">
							<?php echo esc_html( pdo_row_text( $pdo_item, 'link_label', __( 'Learn more', 'pdo-modern' ) ) ); ?>
						</a>
					</div>
				<?php endif; ?>
			</div>
		</article>
	<?php endforeach; ?>
</div>
<?php
pdo_modern_dynamic_button( $pdo_section );
pdo_modern_section_close();
