<?php
/**
 * Page body – Our Impact.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

pdo_modern_dynamic_sections( 'impact', 'before' );

/* ---------------------------------------------------------------- Intro -- */
if ( pdo_section_enabled( 'impact', 'intro' ) ) {
	get_template_part(
		'template-parts/components/page-hero',
		null,
		array(
			'eyebrow'     => pdo_text( 'impact', 'intro_eyebrow', '' ),
			'title'       => pdo_text( 'impact', 'intro_title', get_the_title() ),
			'description' => pdo_text( 'impact', 'intro_description', '' ),
			'image'       => (int) pdo_setting( 'impact', 'intro_banner', 0 ),
		)
	);
}

/* ---------------------------------------------------- Tiered scholarship -- */
if ( pdo_section_enabled( 'impact', 'scholarship' ) ) :
	$pdo_chart = pdo_scholarship_chart();

	pdo_modern_section_open( array( 'id' => 'pdo-impact-scholarship' ) );

	pdo_modern_section_header(
		array(
			'eyebrow' => pdo_text( 'impact', 'scholarship_eyebrow', '' ),
			'title'   => pdo_text( 'impact', 'scholarship_title', '' ),
		)
	);
	?>
	<?php if ( pdo_text( 'impact', 'scholarship_content', '' ) ) : ?>
		<div class="pdo-prose pdo-container--narrow">
			<?php echo wp_kses_post( wpautop( pdo_text( 'impact', 'scholarship_content', '' ) ) ); ?>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $pdo_chart['rows'] ) ) : ?>
		<div class="pdo-split pdo-chart">
			<div class="pdo-chart__visual">
				<?php
				pdo_modern_donut_chart(
					$pdo_chart,
					array(
						'center_value' => number_format_i18n( $pdo_chart['total'] ),
						'center_label' => pdo_text( 'impact', 'chart_subtitle', __( 'Students supported', 'pdo-modern' ) ),
						'title'        => pdo_text( 'impact', 'chart_title', __( 'Scholarship distribution', 'pdo-modern' ) ),
					)
				);
				?>
			</div>

			<div>
				<?php if ( pdo_text( 'impact', 'chart_title', '' ) ) : ?>
					<h3 class="pdo-subheading"><?php echo esc_html( pdo_text( 'impact', 'chart_title', '' ) ); ?></h3>
				<?php endif; ?>

				<ul class="pdo-legend">
					<?php foreach ( $pdo_chart['rows'] as $pdo_row ) : ?>
						<li class="pdo-legend__item">
							<span class="pdo-legend__swatch" style="background:<?php echo esc_attr( $pdo_row['color'] ); ?>"></span>
							<span>
								<span class="pdo-legend__title">
									<?php echo esc_html( $pdo_row['label'] ); ?>
									<?php if ( $pdo_row['percentage'] ) : ?>
										<span class="pdo-badge pdo-badge--outline"><?php echo esc_html( $pdo_row['percentage'] ); ?></span>
									<?php endif; ?>
								</span>
								<span class="pdo-legend__meta">
									<?php echo esc_html( number_format_i18n( $pdo_row['value'] ) ); ?>
									<?php echo $pdo_row['count_label'] ? esc_html( ' ' . $pdo_row['count_label'] ) : ''; ?>
									· <?php echo esc_html( $pdo_row['share'] ); ?>%
								</span>
								<?php if ( $pdo_row['description'] ) : ?>
									<span class="pdo-legend__meta"><?php echo esc_html( $pdo_row['description'] ); ?></span>
								<?php endif; ?>
							</span>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	<?php endif; ?>
	<?php
	pdo_modern_section_close();
endif;

/* ------------------------------------------------------------- Programs -- */
if ( pdo_section_enabled( 'impact', 'programs' ) ) :
	$pdo_limit    = (int) pdo_setting( 'impact', 'programs_limit', 9 );
	$pdo_programs = pdo_get_posts( 'pdo_program', array( 'posts_per_page' => $pdo_limit > 0 ? $pdo_limit : 9 ) );

	if ( $pdo_programs ) :
		pdo_modern_section_open(
			array(
				'id'         => 'pdo-impact-programs',
				'background' => 'light',
			)
		);

		pdo_modern_section_header(
			array(
				'title'       => pdo_text( 'impact', 'programs_title', '' ),
				'description' => pdo_text( 'impact', 'programs_description', '' ),
			)
		);
		?>
		<div class="pdo-grid pdo-grid--auto">
			<?php foreach ( $pdo_programs as $pdo_program ) : ?>
				<?php get_template_part( 'template-parts/components/card', 'program', array( 'post' => $pdo_program ) ); ?>
			<?php endforeach; ?>
		</div>
		<?php
		pdo_modern_section_close();
	endif;
endif;

/* -------------------------------------------------------- Human stories -- */
if ( pdo_section_enabled( 'impact', 'stories' ) ) :
	$pdo_limit   = (int) pdo_setting( 'impact', 'stories_limit', 6 );
	$pdo_stories = pdo_get_posts( 'pdo_story', array( 'posts_per_page' => $pdo_limit > 0 ? $pdo_limit : 6 ) );

	if ( $pdo_stories ) :
		pdo_modern_section_open( array( 'id' => 'pdo-impact-stories' ) );

		pdo_modern_section_header(
			array(
				'eyebrow'     => pdo_text( 'impact', 'stories_eyebrow', '' ),
				'title'       => pdo_text( 'impact', 'stories_title', '' ),
				'description' => pdo_text( 'impact', 'stories_description', '' ),
			)
		);
		?>
		<div class="pdo-grid pdo-grid--auto">
			<?php foreach ( $pdo_stories as $pdo_story ) : ?>
				<?php get_template_part( 'template-parts/components/card', 'story', array( 'post' => $pdo_story ) ); ?>
			<?php endforeach; ?>
		</div>
		<?php
		pdo_modern_section_close();
	endif;
endif;

/* ----------------------------------------------------------- Vision 2030 -- */
if ( pdo_section_enabled( 'impact', 'vision2030' ) ) :
	$pdo_targets = pdo_rows( 'impact', 'vision2030_targets' );

	if ( $pdo_targets ) :
		pdo_modern_section_open(
			array(
				'id'         => 'pdo-impact-vision2030',
				'background' => 'dark',
			)
		);

		pdo_modern_section_header(
			array(
				'eyebrow'     => pdo_text( 'impact', 'vision2030_eyebrow', '' ),
				'title'       => pdo_text( 'impact', 'vision2030_title', '' ),
				'description' => pdo_text( 'impact', 'vision2030_description', '' ),
			)
		);
		?>
		<div class="pdo-stats">
			<?php foreach ( $pdo_targets as $pdo_row ) : ?>
				<div class="pdo-stat pdo-card--dark <?php echo esc_attr( pdo_modern_accent_class( isset( $pdo_row['accent'] ) ? $pdo_row['accent'] : '' ) ); ?>" data-pdo-reveal>
					<?php if ( ! empty( $pdo_row['icon'] ) ) : ?>
						<span class="pdo-stat__icon"><?php pdo_icon( $pdo_row['icon'], array( 'size' => 24 ) ); ?></span>
					<?php endif; ?>
					<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the helper.
					echo pdo_modern_stat_value(
						array(
							'value'  => isset( $pdo_row['number'] ) ? $pdo_row['number'] : '',
							'suffix' => isset( $pdo_row['suffix'] ) ? $pdo_row['suffix'] : '',
						)
					);
					?>
					<span class="pdo-stat__label"><?php echo esc_html( pdo_row_text( $pdo_row, 'title', '' ) ); ?></span>
					<?php if ( pdo_row_text( $pdo_row, 'description', '' ) ) : ?>
						<span class="pdo-stat__desc"><?php echo esc_html( pdo_row_text( $pdo_row, 'description', '' ) ); ?></span>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
		pdo_modern_section_close();
	endif;
endif;

pdo_modern_page_content();

pdo_modern_dynamic_sections( 'impact', 'after' );
