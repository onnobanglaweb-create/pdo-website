<?php
/**
 * Dynamic section – unknown type (generic renderer).
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

$pdo_section = pdo_modern_section_args( isset( $args ) ? $args : array() );
if ( ! $pdo_section ) {
	return;
}

pdo_modern_dynamic_open( $pdo_section );
pdo_modern_dynamic_header( $pdo_section );

$pdo_items = pdo_modern_dynamic_items( $pdo_section );
$pdo_media = pdo_image( (int) ( isset( $pdo_section['image'] ) ? $pdo_section['image'] : 0 ), '', array( 'size' => 'pdo-card' ) );

if ( $pdo_media ) {
	echo '<figure class="pdo-dynamic__media">';
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by the media helper.
	echo $pdo_media;
	echo '</figure>';
}

if ( $pdo_items ) :
	?>
	<div class="pdo-grid <?php echo esc_attr( pdo_modern_columns_class( isset( $pdo_section['columns'] ) ? $pdo_section['columns'] : 3 ) ); ?>">
		<?php foreach ( $pdo_items as $pdo_item ) : ?>
			<article class="pdo-card pdo-card--icon">
				<div class="pdo-card__body">
					<?php if ( ! empty( $pdo_item['icon'] ) ) : ?>
						<span class="pdo-card__icon"><?php pdo_icon( $pdo_item['icon'], array( 'size' => 22 ) ); ?></span>
					<?php endif; ?>
					<?php if ( pdo_row_text( $pdo_item, 'title', '' ) ) : ?>
						<h3 class="pdo-card__title"><?php echo esc_html( pdo_row_text( $pdo_item, 'title', '' ) ); ?></h3>
					<?php endif; ?>
					<?php if ( pdo_row_text( $pdo_item, 'subtitle', '' ) ) : ?>
						<p class="pdo-card__kicker"><?php echo esc_html( pdo_row_text( $pdo_item, 'subtitle', '' ) ); ?></p>
					<?php endif; ?>
					<?php if ( pdo_row_text( $pdo_item, 'description', '' ) ) : ?>
						<p class="pdo-card__text"><?php echo esc_html( pdo_row_text( $pdo_item, 'description', '' ) ); ?></p>
					<?php endif; ?>
					<?php if ( ! empty( $pdo_item['link_url'] ) ) : ?>
						<a class="pdo-readmore" href="<?php echo esc_url( $pdo_item['link_url'] ); ?>">
							<?php echo esc_html( pdo_row_text( $pdo_item, 'link_label', __( 'Learn more', 'pdo-modern' ) ) ); ?>
						</a>
					<?php endif; ?>
				</div>
			</article>
		<?php endforeach; ?>
	</div>
	<?php
endif;

pdo_modern_dynamic_button( $pdo_section );
pdo_modern_section_close();
