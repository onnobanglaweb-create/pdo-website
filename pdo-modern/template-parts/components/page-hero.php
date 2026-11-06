<?php
/**
 * Component – page hero / intro banner.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

$pdo_args = wp_parse_args(
	isset( $args ) ? $args : array(),
	array(
		'eyebrow'     => '',
		'title'       => '',
		'description' => '',
		'image'       => 0,
		'icon'        => '',
		'level'       => 'h1',
	)
);

if ( ! $pdo_args['title'] && ! $pdo_args['description'] ) {
	return;
}

$pdo_banner = pdo_image( (int) $pdo_args['image'], '', array( 'size' => 'full', 'loading' => 'eager' ) );
?>
<section class="pdo-page-hero<?php echo $pdo_banner ? ' pdo-page-hero--image' : ''; ?>">
	<?php if ( $pdo_banner ) : ?>
		<div class="pdo-page-hero__bg" aria-hidden="true">
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by the media helper.
			echo $pdo_banner;
			?>
		</div>
	<?php endif; ?>

	<div class="pdo-container">
		<?php
		pdo_modern_section_header(
			array(
				'eyebrow'     => $pdo_args['eyebrow'],
				'title'       => $pdo_args['title'],
				'description' => $pdo_args['description'],
				'icon'        => $pdo_args['icon'],
				'level'       => $pdo_args['level'],
				'align'       => 'center',
			)
		);
		?>
	</div>
</section>
