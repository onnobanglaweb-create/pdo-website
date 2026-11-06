<?php
/**
 * Homepage – hero.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

$pdo_title     = pdo_text( 'home', 'hero_title', '' );
$pdo_highlight = pdo_text( 'home', 'hero_title_highlight', '' );
$pdo_desc      = pdo_text( 'home', 'hero_description', '' );
$pdo_badge     = pdo_text( 'home', 'hero_badge', '' );
$pdo_bg        = pdo_setting( 'home', 'hero_background', '' );
$pdo_bg_image  = (int) pdo_setting( 'home', 'hero_background_image', 0 );
$pdo_badges    = pdo_rows( 'home', 'hero_badges' );
$pdo_images    = pdo_rows( 'home', 'hero_images', 4 );

if ( ! $pdo_title && ! $pdo_desc && ! $pdo_images ) {
	return;
}

pdo_modern_section_open(
	array(
		'id'         => 'pdo-section-hero',
		'class'      => 'pdo-hero',
		'background' => $pdo_bg,
		'visibility' => pdo_modern_visibility_class( 'hero' ),
		'label'      => 'pdo-hero-title',
		'container'  => false,
	)
);

if ( $pdo_bg_image ) {
	echo '<div class="pdo-hero__bg" aria-hidden="true">';
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by the media helper.
	echo pdo_image( $pdo_bg_image, '', array( 'size' => 'full', 'loading' => 'eager' ) );
	echo '</div>';
}
?>
<div class="pdo-container">
	<div class="pdo-hero__grid">
		<div class="pdo-hero__content">
			<?php if ( $pdo_badge ) : ?>
				<p class="pdo-eyebrow">
					<?php pdo_icon( pdo_setting( 'home', 'hero_badge_icon', 'sparkles' ), array( 'size' => 15 ) ); ?>
					<?php echo esc_html( $pdo_badge ); ?>
				</p>
			<?php endif; ?>

			<?php if ( $pdo_title ) : ?>
				<h1 class="pdo-hero__title" id="pdo-hero-title">
					<?php echo esc_html( $pdo_title ); ?>
					<?php if ( $pdo_highlight ) : ?>
						<span class="pdo-hero__highlight"><?php echo esc_html( $pdo_highlight ); ?></span>
					<?php endif; ?>
				</h1>
			<?php endif; ?>

			<?php if ( $pdo_desc ) : ?>
				<p class="pdo-hero__text"><?php echo esc_html( $pdo_desc ); ?></p>
			<?php endif; ?>

			<div class="pdo-btn-group">
				<?php
				pdo_modern_button(
					pdo_text( 'home', 'hero_btn1_label', '' ),
					pdo_setting( 'home', 'hero_btn1_url', '' ),
					array(
						'style' => 'primary',
						'icon'  => pdo_setting( 'home', 'hero_btn1_icon', '' ),
					)
				);
				pdo_modern_button(
					pdo_text( 'home', 'hero_btn2_label', '' ),
					pdo_setting( 'home', 'hero_btn2_url', '' ),
					array(
						'style' => in_array( $pdo_bg, array( 'dark', 'navy' ), true ) ? 'onDark' : 'outline',
						'icon'  => pdo_setting( 'home', 'hero_btn2_icon', '' ),
					)
				);
				?>
			</div>

			<?php if ( $pdo_badges ) : ?>
				<ul class="pdo-hero__badges">
					<?php foreach ( $pdo_badges as $pdo_item ) : ?>
						<?php $pdo_text_value = pdo_row_text( $pdo_item, 'text', '' ); ?>
						<?php if ( ! $pdo_text_value ) { continue; } ?>
						<li class="pdo-hero__badge">
							<?php pdo_icon( isset( $pdo_item['icon'] ) ? $pdo_item['icon'] : 'check', array( 'size' => 14 ) ); ?>
							<span><?php echo esc_html( $pdo_text_value ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php
			$pdo_hub_name = pdo_text( 'home', 'hero_hub_name', '' );
			if ( $pdo_hub_name ) :
				?>
				<div class="pdo-hero__hub">
					<?php pdo_icon( 'building', array( 'size' => 22 ) ); ?>
					<span>
						<?php if ( pdo_text( 'home', 'hero_hub_label', '' ) ) : ?>
							<strong><?php echo esc_html( pdo_text( 'home', 'hero_hub_label', '' ) ); ?>:</strong>
						<?php endif; ?>
						<?php echo esc_html( $pdo_hub_name ); ?>
						<?php if ( pdo_text( 'home', 'hero_hub_place', '' ) ) : ?>
							<em><?php echo esc_html( pdo_text( 'home', 'hero_hub_place', '' ) ); ?></em>
						<?php endif; ?>
					</span>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $pdo_images ) : ?>
			<div class="pdo-hero__visual">
				<div class="pdo-mosaic">
					<?php foreach ( $pdo_images as $pdo_index => $pdo_image_row ) : ?>
						<?php
						$pdo_tag = pdo_row_image(
							$pdo_image_row,
							array(
								'size'    => 'pdo-card',
								'loading' => $pdo_index < 2 ? 'eager' : 'lazy',
								'alt'     => isset( $pdo_image_row['alt'] ) ? $pdo_image_row['alt'] : '',
							)
						);
						if ( ! $pdo_tag ) {
							continue;
						}
						$pdo_caption = pdo_row_text( $pdo_image_row, 'caption', '' );
						?>
						<figure class="pdo-mosaic__item <?php echo esc_attr( pdo_modern_accent_class( isset( $pdo_image_row['accent'] ) ? $pdo_image_row['accent'] : '' ) ); ?>">
							<?php
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by the media helper.
							echo $pdo_tag;
							?>
							<?php if ( $pdo_caption ) : ?>
								<figcaption class="pdo-mosaic__caption"><?php echo esc_html( $pdo_caption ); ?></figcaption>
							<?php endif; ?>
						</figure>
					<?php endforeach; ?>

					<?php if ( pdo_text( 'home', 'hero_panel_title', '' ) ) : ?>
						<div class="pdo-hero__panel">
							<span class="pdo-hero__panel-icon"><?php pdo_icon( 'award', array( 'size' => 20 ) ); ?></span>
							<span>
								<span class="pdo-hero__panel-title"><?php echo esc_html( pdo_text( 'home', 'hero_panel_title', '' ) ); ?></span>
								<?php if ( pdo_text( 'home', 'hero_panel_tag', '' ) ) : ?>
									<span class="pdo-hero__panel-sub"><?php echo esc_html( pdo_text( 'home', 'hero_panel_tag', '' ) ); ?></span>
								<?php endif; ?>
							</span>
						</div>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>
<?php
pdo_modern_section_close( array( 'container' => false ) );
