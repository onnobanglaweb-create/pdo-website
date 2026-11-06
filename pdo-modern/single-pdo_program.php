<?php
/**
 * Single program.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	$pdo_id     = (int) get_the_ID();
	$pdo_accent = pdo_modern_accent_class( pdo_raw_meta( $pdo_id, 'accent', '' ) );
	$pdo_banner = pdo_post_image( $pdo_id, array( 'size' => 'full', 'loading' => 'eager' ) );
	?>
	<section class="pdo-page-hero<?php echo $pdo_banner ? ' pdo-page-hero--image' : ''; ?> <?php echo esc_attr( $pdo_accent ); ?>">
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
					'eyebrow'     => pdo_meta( $pdo_id, 'short_name', __( 'Program', 'pdo-modern' ) ),
					'title'       => pdo_post_title( $pdo_id ),
					'description' => pdo_post_excerpt( $pdo_id ),
					'icon'        => pdo_raw_meta( $pdo_id, 'icon', '' ),
					'level'       => 'h1',
				)
			);
			?>
		</div>
	</section>

	<?php
	$pdo_stats = pdo_meta_rows( $pdo_id, 'stats' );
	if ( $pdo_stats ) :
		pdo_modern_section_open(
			array(
				'class'      => 'pdo-section--tight',
				'background' => 'light',
			)
		);
		?>
		<div class="pdo-stats">
			<?php foreach ( $pdo_stats as $pdo_row ) : ?>
				<div class="pdo-stat">
					<?php if ( ! empty( $pdo_row['icon'] ) ) : ?>
						<span class="pdo-stat__icon"><?php pdo_icon( $pdo_row['icon'], array( 'size' => 22 ) ); ?></span>
					<?php endif; ?>
					<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper.
					echo pdo_modern_stat_value( $pdo_row );
					?>
					<span class="pdo-stat__label"><?php echo esc_html( pdo_row_text( $pdo_row, 'label', '' ) ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
		pdo_modern_section_close();
	endif;

	pdo_modern_section_open( array( 'class' => 'pdo-single pdo-single--program' ) );
	?>
	<div class="pdo-split">
		<div class="pdo-prose">
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered through the_content filters.
			echo pdo_post_content( $pdo_id );
			?>

			<?php if ( pdo_meta( $pdo_id, 'results', '' ) ) : ?>
				<h2><?php esc_html_e( 'Results so far', 'pdo-modern' ); ?></h2>
				<?php echo wp_kses_post( wpautop( pdo_meta( $pdo_id, 'results', '' ) ) ); ?>
			<?php endif; ?>
		</div>

		<aside class="pdo-single__aside">
			<?php $pdo_achievements = pdo_meta_rows( $pdo_id, 'achievements' ); ?>
			<?php if ( $pdo_achievements ) : ?>
				<div class="pdo-card pdo-card--outline">
					<div class="pdo-card__body">
						<h2 class="pdo-card__title"><?php esc_html_e( 'Key achievements', 'pdo-modern' ); ?></h2>
						<ul class="pdo-checklist">
							<?php foreach ( $pdo_achievements as $pdo_row ) : ?>
								<li>
									<?php pdo_icon( ! empty( $pdo_row['icon'] ) ? $pdo_row['icon'] : 'check', array( 'size' => 16 ) ); ?>
									<span><?php echo esc_html( pdo_row_text( $pdo_row, 'text', '' ) ); ?></span>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( pdo_meta( $pdo_id, 'sdgs', '' ) ) : ?>
				<div class="pdo-card pdo-card--outline">
					<div class="pdo-card__body">
						<h2 class="pdo-card__title"><?php esc_html_e( 'Linked SDGs', 'pdo-modern' ); ?></h2>
						<p class="pdo-card__text"><?php echo esc_html( pdo_meta( $pdo_id, 'sdgs', '' ) ); ?></p>
					</div>
				</div>
			<?php endif; ?>

			<?php $pdo_docs = pdo_meta_rows( $pdo_id, 'documents' ); ?>
			<?php if ( $pdo_docs ) : ?>
				<div class="pdo-card pdo-card--outline">
					<div class="pdo-card__body">
						<h2 class="pdo-card__title"><?php esc_html_e( 'Documents', 'pdo-modern' ); ?></h2>
						<ul class="pdo-checklist">
							<?php foreach ( $pdo_docs as $pdo_row ) : ?>
								<?php
								$pdo_url = '';
								if ( ! empty( $pdo_row['file'] ) ) {
									$pdo_url = (string) wp_get_attachment_url( (int) $pdo_row['file'] );
								}
								if ( ! $pdo_url && ! empty( $pdo_row['url'] ) ) {
									$pdo_url = (string) $pdo_row['url'];
								}
								if ( ! $pdo_url ) {
									continue;
								}
								?>
								<li>
									<?php pdo_icon( 'download', array( 'size' => 16 ) ); ?>
									<a href="<?php echo esc_url( $pdo_url ); ?>"><?php echo esc_html( pdo_row_text( $pdo_row, 'label', __( 'Download', 'pdo-modern' ) ) ); ?></a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			<?php endif; ?>

			<?php if ( pdo_raw_meta( $pdo_id, 'cta_url', '' ) ) : ?>
				<?php
				pdo_modern_button(
					pdo_meta( $pdo_id, 'cta_label', __( 'Support this program', 'pdo-modern' ) ),
					pdo_raw_meta( $pdo_id, 'cta_url', '' ),
					array(
						'style' => 'primary',
						'class' => 'pdo-btn--block',
						'icon'  => 'hand-heart',
					)
				);
				?>
			<?php endif; ?>
		</aside>
	</div>
	<?php
	pdo_modern_section_close();

	$pdo_gallery = pdo_meta_rows( $pdo_id, 'gallery' );
	if ( $pdo_gallery ) :
		pdo_modern_section_open(
			array(
				'class'      => 'pdo-section--tight',
				'background' => 'light',
			)
		);
		pdo_modern_section_header( array( 'title' => __( 'From the field', 'pdo-modern' ) ) );
		?>
		<ul class="pdo-gallery">
			<?php foreach ( $pdo_gallery as $pdo_row ) : ?>
				<?php
				$pdo_tag = pdo_row_image( $pdo_row, array( 'size' => 'pdo-card', 'alt' => isset( $pdo_row['alt'] ) ? $pdo_row['alt'] : '' ) );
				if ( ! $pdo_tag ) {
					continue;
				}
				?>
				<li class="pdo-gallery__item">
					<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by the media helper.
					echo $pdo_tag;
					?>
					<?php if ( pdo_row_text( $pdo_row, 'caption', '' ) ) : ?>
						<span class="pdo-gallery__caption"><?php echo esc_html( pdo_row_text( $pdo_row, 'caption', '' ) ); ?></span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
		pdo_modern_section_close();
	endif;

endwhile;

get_footer();
