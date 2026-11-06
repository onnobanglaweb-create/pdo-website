<?php
/**
 * Dynamic section – testimonials.
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
		<?php $pdo_tag = pdo_row_image( $pdo_item, array( 'size' => 'pdo-thumb', 'alt' => pdo_row_text( $pdo_item, 'title', '' ) ) ); ?>
		<figure class="pdo-story" data-pdo-reveal>
			<div class="pdo-story__body">
				<blockquote class="pdo-story__quote"><?php echo esc_html( pdo_row_text( $pdo_item, 'description', '' ) ); ?></blockquote>
				<figcaption class="pdo-person pdo-person--compact">
					<?php if ( $pdo_tag ) : ?>
						<span class="pdo-person__photo">
							<?php
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by the media helper.
							echo $pdo_tag;
							?>
						</span>
					<?php endif; ?>
					<span class="pdo-person__body">
						<strong class="pdo-person__name"><?php echo esc_html( pdo_row_text( $pdo_item, 'title', '' ) ); ?></strong>
						<?php if ( pdo_row_text( $pdo_item, 'subtitle', '' ) ) : ?>
							<span class="pdo-person__role"><?php echo esc_html( pdo_row_text( $pdo_item, 'subtitle', '' ) ); ?></span>
						<?php endif; ?>
					</span>
				</figcaption>
			</div>
		</figure>
	<?php endforeach; ?>
</div>
<?php
pdo_modern_dynamic_button( $pdo_section );
pdo_modern_section_close();
