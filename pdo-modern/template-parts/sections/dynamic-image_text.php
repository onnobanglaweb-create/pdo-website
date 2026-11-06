<?php
/**
 * Dynamic section – image beside text.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

$pdo_section = pdo_modern_section_args( isset( $args ) ? $args : array() );
if ( ! $pdo_section ) {
	return;
}

$pdo_media = pdo_image( (int) ( isset( $pdo_section['image'] ) ? $pdo_section['image'] : 0 ), '', array( 'size' => 'pdo-card' ) );

pdo_modern_dynamic_open( $pdo_section );
?>
<div class="pdo-split">
	<?php if ( $pdo_media ) : ?>
		<figure class="pdo-dynamic__media">
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by the media helper.
			echo $pdo_media;
			?>
		</figure>
	<?php endif; ?>

	<div class="pdo-dynamic__text">
		<?php
		pdo_modern_section_header(
			array(
				'eyebrow'     => pdo_modern_section_text( $pdo_section, 'eyebrow', '' ),
				'title'       => pdo_modern_section_text( $pdo_section, 'heading', '' ),
				'align'       => 'left',
				'icon'        => isset( $pdo_section['icon'] ) ? $pdo_section['icon'] : '',
			)
		);
		?>
		<div class="pdo-prose">
			<?php echo wp_kses_post( wpautop( pdo_modern_section_text( $pdo_section, 'content', '' ) ) ); ?>
		</div>
		<?php pdo_modern_dynamic_button( $pdo_section ); ?>
	</div>
</div>
<?php
pdo_modern_section_close();
