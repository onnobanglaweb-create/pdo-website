<?php
/**
 * Homepage – strategic pillars.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

$pdo_pillars = pdo_rows( 'home', 'pillars' );

if ( ! $pdo_pillars ) {
	return;
}

pdo_modern_section_open(
	array(
		'id'         => 'pdo-section-pillars',
		'class'      => 'pdo-section-pillars',
		'visibility' => pdo_modern_visibility_class( 'pillars' ),
		'label'      => 'pdo-pillars-title',
	)
);

pdo_modern_section_header(
	array(
		'eyebrow'     => pdo_text( 'home', 'pillars_eyebrow', '' ),
		'title'       => pdo_text( 'home', 'pillars_title', '' ),
		'description' => pdo_text( 'home', 'pillars_description', '' ),
		'id'          => 'pdo-pillars-title',
	)
);
?>
<div class="pdo-grid pdo-grid--auto">
	<?php foreach ( $pdo_pillars as $pdo_row ) : ?>
		<?php
		$pdo_title = pdo_row_text( $pdo_row, 'title', '' );
		$pdo_media = pdo_row_image( $pdo_row, array( 'size' => 'pdo-card', 'alt' => isset( $pdo_row['alt'] ) ? $pdo_row['alt'] : $pdo_title ) );
		$pdo_badge = pdo_row_text( $pdo_row, 'badge', '' );
		$pdo_link  = isset( $pdo_row['link_url'] ) ? (string) $pdo_row['link_url'] : '';
		?>
		<article class="pdo-card <?php echo esc_attr( pdo_modern_accent_class( isset( $pdo_row['accent'] ) ? $pdo_row['accent'] : '' ) ); ?>" data-pdo-reveal>
			<?php if ( $pdo_media ) : ?>
				<div class="pdo-card__media">
					<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by the media helper.
					echo $pdo_media;
					?>
					<?php if ( $pdo_badge ) : ?>
						<span class="pdo-card__tag"><?php echo esc_html( $pdo_badge ); ?></span>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="pdo-card__body">
				<?php if ( ! empty( $pdo_row['icon'] ) ) : ?>
					<span class="pdo-card__icon"><?php pdo_icon( $pdo_row['icon'], array( 'size' => 24 ) ); ?></span>
				<?php endif; ?>

				<?php if ( ! $pdo_media && $pdo_badge ) : ?>
					<p class="pdo-card__kicker"><?php echo esc_html( $pdo_badge ); ?></p>
				<?php endif; ?>

				<?php if ( $pdo_title ) : ?>
					<h3 class="pdo-card__title"><?php echo esc_html( $pdo_title ); ?></h3>
				<?php endif; ?>

				<?php if ( pdo_row_text( $pdo_row, 'description', '' ) ) : ?>
					<p class="pdo-card__text"><?php echo esc_html( pdo_row_text( $pdo_row, 'description', '' ) ); ?></p>
				<?php endif; ?>

				<?php if ( pdo_row_text( $pdo_row, 'footer', '' ) || $pdo_link ) : ?>
					<div class="pdo-card__footer">
						<?php if ( pdo_row_text( $pdo_row, 'footer', '' ) ) : ?>
							<span class="pdo-card__text"><?php echo esc_html( pdo_row_text( $pdo_row, 'footer', '' ) ); ?></span>
						<?php endif; ?>
						<?php if ( $pdo_link ) : ?>
							<a class="pdo-readmore" href="<?php echo esc_url( $pdo_link ); ?>">
								<?php echo esc_html( pdo_row_text( $pdo_row, 'link_label', __( 'Learn more', 'pdo-modern' ) ) ); ?>
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</article>
	<?php endforeach; ?>
</div>
<?php
pdo_modern_section_close();
