<?php
/**
 * Homepage – impact statistics strip.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

$pdo_stats = pdo_rows( 'home', 'stats' );

if ( ! $pdo_stats ) {
	return;
}

pdo_modern_section_open(
	array(
		'id'         => 'pdo-section-stats',
		'class'      => 'pdo-section--tight pdo-section-stats',
		'background' => 'light',
		'visibility' => pdo_modern_visibility_class( 'stats' ),
	)
);
?>
<div class="pdo-stats" data-pdo-reveal>
	<?php foreach ( $pdo_stats as $pdo_row ) : ?>
		<?php
		$pdo_label = pdo_row_text( $pdo_row, 'label', '' );
		$pdo_color = isset( $pdo_row['color'] ) ? (string) $pdo_row['color'] : '';
		$pdo_bg    = isset( $pdo_row['bg'] ) ? (string) $pdo_row['bg'] : '';
		$pdo_style = array();
		if ( $pdo_color ) {
			$pdo_style[] = '--pdo-stat-color:' . $pdo_color;
		}
		if ( $pdo_bg ) {
			$pdo_style[] = '--pdo-stat-bg:' . $pdo_bg;
		}
		?>
		<div class="pdo-stat"<?php echo $pdo_style ? ' style="' . esc_attr( implode( ';', $pdo_style ) ) . '"' : ''; ?>>
			<?php if ( ! empty( $pdo_row['icon'] ) ) : ?>
				<span class="pdo-stat__icon"><?php pdo_icon( $pdo_row['icon'], array( 'size' => 24 ) ); ?></span>
			<?php endif; ?>

			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper.
			echo pdo_modern_stat_value( $pdo_row );
			?>

			<?php if ( $pdo_label ) : ?>
				<span class="pdo-stat__label"><?php echo esc_html( $pdo_label ); ?></span>
			<?php endif; ?>

			<?php if ( pdo_row_text( $pdo_row, 'description', '' ) ) : ?>
				<span class="pdo-stat__desc"><?php echo esc_html( pdo_row_text( $pdo_row, 'description', '' ) ); ?></span>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>
<?php
pdo_modern_section_close();
