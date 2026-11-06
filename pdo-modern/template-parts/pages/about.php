<?php
/**
 * Page body – About Us.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

pdo_modern_dynamic_sections( 'about', 'before' );

/* ---------------------------------------------------------------- Intro -- */
if ( pdo_section_enabled( 'about', 'intro' ) ) {
	get_template_part(
		'template-parts/components/page-hero',
		null,
		array(
			'eyebrow'     => pdo_text( 'about', 'intro_eyebrow', '' ),
			'title'       => pdo_text( 'about', 'intro_title', get_the_title() ),
			'description' => pdo_text( 'about', 'intro_description', '' ),
			'image'       => (int) pdo_setting( 'about', 'intro_banner', 0 ),
		)
	);
}

/* -------------------------------------------------------------- History -- */
if ( pdo_section_enabled( 'about', 'history' ) ) :
	$pdo_history_image = pdo_image( (int) pdo_setting( 'about', 'history_image', 0 ), '', array( 'size' => 'pdo-card' ) );
	$pdo_left          = 'left' === pdo_setting( 'about', 'history_image_position', 'right' );
	$pdo_milestones    = pdo_rows( 'about', 'milestones' );

	pdo_modern_section_open(
		array(
			'id'         => 'pdo-about-history',
			'background' => pdo_setting( 'about', 'history_background', '' ),
		)
	);
	?>
	<div class="pdo-split<?php echo $pdo_left ? '' : ' pdo-split--reverse'; ?>">
		<?php if ( $pdo_history_image ) : ?>
			<figure class="pdo-figure">
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by the media helper.
				echo $pdo_history_image;
				?>
			</figure>
		<?php endif; ?>

		<div>
			<?php
			pdo_modern_section_header(
				array(
					'title' => pdo_text( 'about', 'history_title', '' ),
					'icon'  => pdo_setting( 'about', 'history_icon', '' ),
					'align' => 'left',
				)
			);
			?>
			<div class="pdo-prose">
				<?php echo wp_kses_post( wpautop( pdo_text( 'about', 'history_content', '' ) ) ); ?>
			</div>
		</div>
	</div>

	<?php if ( $pdo_milestones ) : ?>
		<div class="pdo-about__milestones">
			<?php if ( pdo_text( 'about', 'milestones_title', '' ) ) : ?>
				<h3 class="pdo-subheading"><?php echo esc_html( pdo_text( 'about', 'milestones_title', '' ) ); ?></h3>
			<?php endif; ?>

			<ol class="pdo-timeline">
				<?php foreach ( $pdo_milestones as $pdo_row ) : ?>
					<li class="pdo-timeline__item" data-pdo-reveal>
						<span class="pdo-timeline__marker">
							<?php if ( ! empty( $pdo_row['icon'] ) ) : ?>
								<?php pdo_icon( $pdo_row['icon'], array( 'size' => 20 ) ); ?>
							<?php else : ?>
								<?php pdo_icon( 'flag', array( 'size' => 20 ) ); ?>
							<?php endif; ?>
						</span>
						<?php if ( ! empty( $pdo_row['year'] ) ) : ?>
							<span class="pdo-timeline__year"><?php echo esc_html( $pdo_row['year'] ); ?></span>
						<?php endif; ?>
						<h4 class="pdo-timeline__title"><?php echo esc_html( pdo_row_text( $pdo_row, 'title', '' ) ); ?></h4>
						<?php if ( pdo_row_text( $pdo_row, 'description', '' ) ) : ?>
							<p class="pdo-timeline__text"><?php echo esc_html( pdo_row_text( $pdo_row, 'description', '' ) ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	<?php endif; ?>
	<?php
	pdo_modern_section_close();
endif;

/* ----------------------------------------------- Vision / mission / values */
if ( pdo_section_enabled( 'about', 'vision' ) ) :
	$pdo_values = pdo_rows( 'about', 'values' );

	pdo_modern_section_open(
		array(
			'id'         => 'pdo-about-vision',
			'background' => pdo_setting( 'about', 'vision_background', 'light' ),
		)
	);
	?>
	<div class="pdo-grid pdo-grid--2">
		<?php
		foreach ( array( 'vision', 'mission' ) as $pdo_key ) :
			$pdo_heading = pdo_text( 'about', $pdo_key . '_title', '' );
			$pdo_body    = pdo_text( 'about', $pdo_key . '_description', '' );
			if ( ! $pdo_heading && ! $pdo_body ) {
				continue;
			}
			$pdo_art = pdo_image( (int) pdo_setting( 'about', $pdo_key . '_image', 0 ), '', array( 'size' => 'pdo-card' ) );
			?>
			<article class="pdo-card pdo-card--icon" data-pdo-reveal>
				<?php if ( $pdo_art ) : ?>
					<div class="pdo-card__media">
						<?php
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by the media helper.
						echo $pdo_art;
						?>
					</div>
				<?php endif; ?>
				<div class="pdo-card__body">
					<span class="pdo-card__icon">
						<?php pdo_icon( pdo_setting( 'about', $pdo_key . '_icon', 'vision' === $pdo_key ? 'eye' : 'target' ), array( 'size' => 24 ) ); ?>
					</span>
					<h2 class="pdo-card__title"><?php echo esc_html( $pdo_heading ); ?></h2>
					<p class="pdo-card__text"><?php echo esc_html( $pdo_body ); ?></p>
				</div>
			</article>
		<?php endforeach; ?>
	</div>

	<?php if ( $pdo_values ) : ?>
		<div class="pdo-about__values">
			<?php
			pdo_modern_section_header(
				array(
					'title' => pdo_text( 'about', 'values_title', '' ),
					'icon'  => pdo_setting( 'about', 'values_icon', '' ),
					'level' => 'h2',
				)
			);
			?>
			<div class="pdo-grid pdo-grid--auto">
				<?php foreach ( $pdo_values as $pdo_row ) : ?>
					<article class="pdo-card pdo-card--icon pdo-card--outline" data-pdo-reveal>
						<div class="pdo-card__body">
							<?php if ( ! empty( $pdo_row['icon'] ) ) : ?>
								<span class="pdo-card__icon"><?php pdo_icon( $pdo_row['icon'], array( 'size' => 22 ) ); ?></span>
							<?php endif; ?>
							<h3 class="pdo-card__title"><?php echo esc_html( pdo_row_text( $pdo_row, 'title', '' ) ); ?></h3>
							<p class="pdo-card__text"><?php echo esc_html( pdo_row_text( $pdo_row, 'description', '' ) ); ?></p>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>
	<?php
	pdo_modern_section_close();
endif;

/* ------------------------------------------------------- Thematic areas -- */
if ( pdo_section_enabled( 'about', 'thematic' ) ) :
	$pdo_thematic = pdo_rows( 'about', 'thematic' );

	if ( $pdo_thematic ) :
		pdo_modern_section_open( array( 'id' => 'pdo-about-thematic' ) );

		pdo_modern_section_header(
			array(
				'title'       => pdo_text( 'about', 'thematic_title', '' ),
				'description' => pdo_text( 'about', 'thematic_description', '' ),
			)
		);
		?>
		<div class="pdo-grid pdo-grid--auto">
			<?php foreach ( $pdo_thematic as $pdo_row ) : ?>
				<?php $pdo_art = pdo_row_image( $pdo_row, array( 'size' => 'pdo-card', 'alt' => pdo_row_text( $pdo_row, 'title', '' ) ) ); ?>
				<article class="pdo-card <?php echo esc_attr( pdo_modern_accent_class( isset( $pdo_row['accent'] ) ? $pdo_row['accent'] : '' ) ); ?>" data-pdo-reveal>
					<?php if ( $pdo_art ) : ?>
						<div class="pdo-card__media">
							<?php
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by the media helper.
							echo $pdo_art;
							?>
							<?php if ( pdo_row_text( $pdo_row, 'number', '' ) ) : ?>
								<span class="pdo-card__tag"><?php echo esc_html( pdo_row_text( $pdo_row, 'number', '' ) ); ?></span>
							<?php endif; ?>
						</div>
					<?php endif; ?>
					<div class="pdo-card__body">
						<?php if ( ! empty( $pdo_row['icon'] ) ) : ?>
							<span class="pdo-card__icon"><?php pdo_icon( $pdo_row['icon'], array( 'size' => 22 ) ); ?></span>
						<?php endif; ?>
						<h3 class="pdo-card__title"><?php echo esc_html( pdo_row_text( $pdo_row, 'title', '' ) ); ?></h3>
						<p class="pdo-card__text"><?php echo esc_html( pdo_row_text( $pdo_row, 'description', '' ) ); ?></p>
						<?php if ( ! empty( $pdo_row['sdgs'] ) ) : ?>
							<div class="pdo-card__footer">
								<span class="pdo-badge pdo-badge--outline"><?php echo esc_html( $pdo_row['sdgs'] ); ?></span>
							</div>
						<?php endif; ?>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
		<?php
		pdo_modern_section_close();
	endif;
endif;

/* ------------------------------------------------------ President letter -- */
if ( pdo_section_enabled( 'about', 'president' ) ) :
	$pdo_message = pdo_text( 'about', 'president_message', '' );

	if ( $pdo_message ) :
		$pdo_portrait = pdo_image(
			(int) pdo_setting( 'about', 'president_photo', 0 ),
			pdo_setting( 'about', 'president_photo_url', '' ),
			array(
				'size' => 'pdo-portrait',
				'alt'  => pdo_text( 'about', 'president_name', '' ),
			)
		);
		$pdo_sign = pdo_image( (int) pdo_setting( 'about', 'president_signature_image', 0 ), '', array( 'size' => 'pdo-thumb' ) );

		pdo_modern_section_open(
			array(
				'id'         => 'pdo-about-president',
				'background' => 'tint',
			)
		);
		?>
		<div class="pdo-split">
			<div class="pdo-president__aside">
				<?php if ( $pdo_portrait ) : ?>
					<figure class="pdo-president__photo">
						<?php
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by the media helper.
						echo $pdo_portrait;
						?>
					</figure>
				<?php endif; ?>
				<p class="pdo-president__identity">
					<strong><?php echo esc_html( pdo_text( 'about', 'president_name', '' ) ); ?></strong>
					<span><?php echo esc_html( pdo_text( 'about', 'president_role', '' ) ); ?></span>
					<?php if ( pdo_text( 'about', 'president_tenure', '' ) ) : ?>
						<em><?php echo esc_html( pdo_text( 'about', 'president_tenure', '' ) ); ?></em>
					<?php endif; ?>
				</p>
			</div>

			<div>
				<?php
				pdo_modern_section_header(
					array(
						'eyebrow' => pdo_text( 'about', 'president_label', '' ),
						'title'   => pdo_text( 'about', 'president_heading', '' ),
						'align'   => 'left',
						'icon'    => 'quote',
					)
				);
				?>
				<div class="pdo-prose">
					<?php echo wp_kses_post( wpautop( $pdo_message ) ); ?>
				</div>

				<div class="pdo-president__signature">
					<?php if ( $pdo_sign ) : ?>
						<?php
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by the media helper.
						echo $pdo_sign;
						?>
					<?php elseif ( pdo_text( 'about', 'president_signature', '' ) ) : ?>
						<span class="pdo-president__signature-text"><?php echo esc_html( pdo_text( 'about', 'president_signature', '' ) ); ?></span>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
		pdo_modern_section_close();
	endif;
endif;

/* ----------------------------------------------------------- Leadership -- */
if ( pdo_section_enabled( 'about', 'leadership' ) ) :
	$pdo_groups = pdo_team_groups();

	if ( $pdo_groups ) :
		$pdo_columns = pdo_modern_columns_class( pdo_setting( 'about', 'leadership_columns', 4 ), 4 );
		$pdo_compact = 'compact' === pdo_setting( 'about', 'leadership_layout', 'grid' );

		pdo_modern_section_open( array( 'id' => 'pdo-about-leadership' ) );

		pdo_modern_section_header(
			array(
				'eyebrow'     => pdo_text( 'about', 'leadership_eyebrow', '' ),
				'title'       => pdo_text( 'about', 'leadership_title', '' ),
				'description' => pdo_text( 'about', 'leadership_description', '' ),
			)
		);

		foreach ( $pdo_groups as $pdo_group ) :
			?>
			<div class="pdo-team-group">
				<h3 class="pdo-subheading"><?php echo esc_html( $pdo_group['term']->name ); ?></h3>
				<?php if ( $pdo_group['term']->description ) : ?>
					<p class="pdo-section__lede"><?php echo esc_html( $pdo_group['term']->description ); ?></p>
				<?php endif; ?>
				<div class="pdo-grid <?php echo esc_attr( $pdo_columns ); ?>">
					<?php foreach ( $pdo_group['members'] as $pdo_member ) : ?>
						<?php
						get_template_part(
							'template-parts/components/card',
							'team',
							array(
								'post'    => $pdo_member,
								'compact' => $pdo_compact,
							)
						);
						?>
					<?php endforeach; ?>
				</div>
			</div>
			<?php
		endforeach;

		pdo_modern_section_close();
	endif;
endif;

/* ------------------------------------------------------------- Partners -- */
if ( pdo_section_enabled( 'about', 'partners' ) ) :
	$pdo_partners = pdo_get_posts( 'pdo_partner', array( 'posts_per_page' => 24 ) );

	if ( $pdo_partners ) :
		pdo_modern_section_open(
			array(
				'id'         => 'pdo-about-partners',
				'background' => 'light',
			)
		);

		pdo_modern_section_header(
			array(
				'title'       => pdo_text( 'about', 'partners_title', '' ),
				'description' => pdo_text( 'about', 'partners_description', '' ),
			)
		);
		?>
		<div class="pdo-grid pdo-grid--auto">
			<?php foreach ( $pdo_partners as $pdo_partner ) : ?>
				<?php get_template_part( 'template-parts/components/card', 'partner', array( 'post' => $pdo_partner ) ); ?>
			<?php endforeach; ?>
		</div>
		<?php
		pdo_modern_section_close();
	endif;
endif;

pdo_modern_page_content();

pdo_modern_dynamic_sections( 'about', 'after' );
