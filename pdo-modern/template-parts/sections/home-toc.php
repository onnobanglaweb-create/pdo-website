<?php
/**
 * Homepage – theory of change.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

$pdo_stages = pdo_rows( 'home', 'toc_stages' );

if ( ! $pdo_stages ) {
	return;
}

pdo_modern_section_open(
	array(
		'id'         => 'pdo-section-toc',
		'class'      => 'pdo-section-toc',
		'background' => 'tint',
		'visibility' => pdo_modern_visibility_class( 'toc' ),
		'label'      => 'pdo-toc-title',
	)
);

pdo_modern_section_header(
	array(
		'eyebrow'     => pdo_text( 'home', 'toc_eyebrow', '' ),
		'title'       => pdo_text( 'home', 'toc_title', '' ),
		'description' => pdo_text( 'home', 'toc_description', '' ),
		'id'          => 'pdo-toc-title',
	)
);
?>
<ol class="pdo-stages">
	<?php foreach ( $pdo_stages as $pdo_index => $pdo_row ) : ?>
		<li class="pdo-stage <?php echo esc_attr( pdo_modern_accent_class( isset( $pdo_row['accent'] ) ? $pdo_row['accent'] : '' ) ); ?>" data-pdo-reveal>
			<span class="pdo-stage__number">
				<?php echo esc_html( ! empty( $pdo_row['number'] ) ? $pdo_row['number'] : (string) ( $pdo_index + 1 ) ); ?>
			</span>

			<?php if ( pdo_row_text( $pdo_row, 'stage_label', '' ) ) : ?>
				<span class="pdo-stage__label"><?php echo esc_html( pdo_row_text( $pdo_row, 'stage_label', '' ) ); ?></span>
			<?php endif; ?>

			<?php if ( pdo_row_text( $pdo_row, 'title', '' ) ) : ?>
				<h3 class="pdo-stage__title"><?php echo esc_html( pdo_row_text( $pdo_row, 'title', '' ) ); ?></h3>
			<?php endif; ?>

			<?php if ( pdo_row_text( $pdo_row, 'description', '' ) ) : ?>
				<p class="pdo-stage__text"><?php echo esc_html( pdo_row_text( $pdo_row, 'description', '' ) ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $pdo_row['icon'] ) ) : ?>
				<span class="pdo-stage__icon"><?php pdo_icon( $pdo_row['icon'], array( 'size' => 22 ) ); ?></span>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
</ol>
<?php
pdo_modern_section_close();
