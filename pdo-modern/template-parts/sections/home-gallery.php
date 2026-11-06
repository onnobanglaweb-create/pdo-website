<?php
/**
 * Homepage – photo gallery.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

$pdo_items = pdo_rows( 'home', 'gallery' );

if ( ! $pdo_items ) {
	return;
}

pdo_modern_section_open(
	array(
		'id'         => 'pdo-section-gallery',
		'class'      => 'pdo-section-gallery',
		'visibility' => pdo_modern_visibility_class( 'gallery' ),
		'label'      => 'pdo-gallery-title',
	)
);

pdo_modern_section_header(
	array(
		'eyebrow' => pdo_text( 'home', 'gallery_eyebrow', '' ),
		'title'   => pdo_text( 'home', 'gallery_title', '' ),
		'id'      => 'pdo-gallery-title',
	)
);
?>
<ul class="pdo-gallery">
	<?php foreach ( $pdo_items as $pdo_row ) : ?>
		<?php
		$pdo_tag = pdo_row_image( $pdo_row, array( 'size' => 'pdo-card', 'alt' => isset( $pdo_row['alt'] ) ? $pdo_row['alt'] : '' ) );
		if ( ! $pdo_tag ) {
			continue;
		}
		$pdo_caption = pdo_row_text( $pdo_row, 'caption', '' );
		$pdo_cat     = pdo_row_text( $pdo_row, 'category', '' );
		$pdo_link    = isset( $pdo_row['url'] ) ? (string) $pdo_row['url'] : '';
		?>
		<li class="pdo-gallery__item">
			<?php if ( $pdo_link ) : ?>
				<a href="<?php echo esc_url( $pdo_link ); ?>">
			<?php endif; ?>

			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by the media helper.
			echo $pdo_tag;
			?>

			<?php if ( $pdo_caption || $pdo_cat ) : ?>
				<span class="pdo-gallery__caption">
					<?php if ( $pdo_cat ) : ?>
						<strong><?php echo esc_html( $pdo_cat ); ?></strong>
					<?php endif; ?>
					<?php echo esc_html( $pdo_caption ); ?>
				</span>
			<?php endif; ?>

			<?php if ( $pdo_link ) : ?>
				</a>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
</ul>

<?php if ( pdo_setting( 'home', 'gallery_link_url', '' ) ) : ?>
	<p class="pdo-section__cta">
		<?php
		pdo_modern_button(
			pdo_text( 'home', 'gallery_link_label', __( 'View the full gallery', 'pdo-modern' ) ),
			pdo_setting( 'home', 'gallery_link_url', '' ),
			array(
				'style' => 'outline',
				'icon'  => 'images',
			)
		);
		?>
	</p>
<?php endif; ?>
<?php
pdo_modern_section_close();
