<?php
/**
 * Page body – Transparency & Governance.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

pdo_modern_dynamic_sections( 'transparency', 'before' );

/* ---------------------------------------------------------------- Intro -- */
if ( pdo_section_enabled( 'transparency', 'intro' ) ) {
	get_template_part(
		'template-parts/components/page-hero',
		null,
		array(
			'eyebrow'     => pdo_text( 'transparency', 'intro_eyebrow', '' ),
			'title'       => pdo_text( 'transparency', 'intro_title', get_the_title() ),
			'description' => pdo_text( 'transparency', 'intro_description', '' ),
			'icon'        => 'shield-check',
		)
	);
}

/* ----------------------------------------------------------- Governance -- */
if ( pdo_section_enabled( 'transparency', 'governance' ) ) :
	$pdo_items = pdo_rows( 'transparency', 'governance' );

	if ( $pdo_items ) :
		pdo_modern_section_open( array( 'id' => 'pdo-transparency-governance' ) );

		pdo_modern_section_header( array( 'title' => pdo_text( 'transparency', 'governance_title', '' ) ) );
		?>
		<div class="pdo-grid pdo-grid--auto">
			<?php foreach ( $pdo_items as $pdo_row ) : ?>
				<article class="pdo-card pdo-card--icon <?php echo esc_attr( pdo_modern_accent_class( isset( $pdo_row['accent'] ) ? $pdo_row['accent'] : '' ) ); ?>" data-pdo-reveal>
					<div class="pdo-card__body">
						<?php if ( ! empty( $pdo_row['icon'] ) ) : ?>
							<span class="pdo-card__icon"><?php pdo_icon( $pdo_row['icon'], array( 'size' => 22 ) ); ?></span>
						<?php endif; ?>
						<h3 class="pdo-card__title"><?php echo esc_html( pdo_row_text( $pdo_row, 'title', '' ) ); ?></h3>
						<p class="pdo-card__text"><?php echo esc_html( pdo_row_text( $pdo_row, 'description', '' ) ); ?></p>
						<?php if ( ! empty( $pdo_row['link_url'] ) ) : ?>
							<div class="pdo-card__footer">
								<a class="pdo-readmore" href="<?php echo esc_url( $pdo_row['link_url'] ); ?>">
									<?php echo esc_html( pdo_row_text( $pdo_row, 'link_label', __( 'Read the policy', 'pdo-modern' ) ) ); ?>
								</a>
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

/* ------------------------------------------------------- MEAL framework -- */
if ( pdo_section_enabled( 'transparency', 'meal' ) ) :
	$pdo_items = pdo_rows( 'transparency', 'meal_items' );

	if ( $pdo_items ) :
		pdo_modern_section_open(
			array(
				'id'         => 'pdo-transparency-meal',
				'background' => 'tint',
			)
		);

		pdo_modern_section_header(
			array(
				'eyebrow'     => pdo_text( 'transparency', 'meal_eyebrow', '' ),
				'title'       => pdo_text( 'transparency', 'meal_title', '' ),
				'description' => pdo_text( 'transparency', 'meal_description', '' ),
			)
		);
		?>
		<ol class="pdo-stages">
			<?php foreach ( $pdo_items as $pdo_index => $pdo_row ) : ?>
				<li class="pdo-stage" data-pdo-reveal>
					<span class="pdo-stage__number"><?php echo esc_html( (string) ( $pdo_index + 1 ) ); ?></span>
					<h3 class="pdo-stage__title"><?php echo esc_html( pdo_row_text( $pdo_row, 'title', '' ) ); ?></h3>
					<p class="pdo-stage__text"><?php echo esc_html( pdo_row_text( $pdo_row, 'description', '' ) ); ?></p>
					<?php if ( ! empty( $pdo_row['icon'] ) ) : ?>
						<span class="pdo-stage__icon"><?php pdo_icon( $pdo_row['icon'], array( 'size' => 22 ) ); ?></span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ol>
		<?php
		pdo_modern_section_close();
	endif;
endif;

/* ------------------------------------------------------ Value for money -- */
if ( pdo_section_enabled( 'transparency', 'vfm' ) ) :
	$pdo_bars  = pdo_rows( 'transparency', 'vfm_bars' );
	$pdo_image = pdo_image( (int) pdo_setting( 'transparency', 'vfm_image', 0 ), '', array( 'size' => 'pdo-card' ) );

	pdo_modern_section_open( array( 'id' => 'pdo-transparency-vfm' ) );

	pdo_modern_section_header(
		array(
			'eyebrow'     => pdo_text( 'transparency', 'vfm_eyebrow', '' ),
			'title'       => pdo_text( 'transparency', 'vfm_title', '' ),
			'description' => pdo_text( 'transparency', 'vfm_intro', '' ),
		)
	);
	?>
	<div class="pdo-split">
		<div>
			<?php if ( $pdo_bars ) : ?>
				<ul class="pdo-bars">
					<?php foreach ( $pdo_bars as $pdo_row ) : ?>
						<?php
						$pdo_value   = isset( $pdo_row['value'] ) ? max( 0, min( 100, (float) $pdo_row['value'] ) ) : 0;
						$pdo_display = ! empty( $pdo_row['display'] ) ? (string) $pdo_row['display'] : $pdo_value . '%';
						$pdo_color   = ! empty( $pdo_row['color'] ) ? (string) $pdo_row['color'] : '';
						?>
						<li class="pdo-bar" data-pdo-bar="<?php echo esc_attr( (string) $pdo_value ); ?>"<?php echo $pdo_color ? ' style="--pdo-bar-color:' . esc_attr( $pdo_color ) . '"' : ''; ?>>
							<p class="pdo-bar__head">
								<span><?php echo esc_html( pdo_row_text( $pdo_row, 'label', '' ) ); ?></span>
								<span><?php echo esc_html( $pdo_display ); ?></span>
							</p>
							<div class="pdo-bar__track">
								<div class="pdo-bar__fill"></div>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if ( pdo_text( 'transparency', 'vfm_detail', '' ) ) : ?>
				<div class="pdo-prose">
					<?php echo wp_kses_post( wpautop( pdo_text( 'transparency', 'vfm_detail', '' ) ) ); ?>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $pdo_image ) : ?>
			<figure class="pdo-figure">
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by the media helper.
				echo $pdo_image;
				?>
			</figure>
		<?php endif; ?>
	</div>
	<?php
	pdo_modern_section_close();
endif;

/* -------------------------------------------- Audit reports & policies --- */
if ( pdo_section_enabled( 'transparency', 'reports' ) ) :
	$pdo_documents = pdo_get_posts( 'pdo_document', array( 'posts_per_page' => 50 ) );

	if ( $pdo_documents ) :
		$pdo_show_filter = (bool) pdo_setting( 'transparency', 'reports_filter', true );
		$pdo_cats        = $pdo_show_filter ? get_terms(
			array(
				'taxonomy'   => 'pdo_doc_cat',
				'hide_empty' => true,
			)
		) : array();

		pdo_modern_section_open(
			array(
				'id'         => 'pdo-transparency-reports',
				'background' => 'light',
			)
		);

		pdo_modern_section_header(
			array(
				'title'       => pdo_text( 'transparency', 'reports_title', '' ),
				'description' => pdo_text( 'transparency', 'reports_description', '' ),
			)
		);

		if ( pdo_text( 'transparency', 'reports_badge', '' ) ) {
			printf(
				'<p class="pdo-align-center"><span class="pdo-badge">%s</span></p>',
				esc_html( pdo_text( 'transparency', 'reports_badge', '' ) )
			);
		}

		if ( $pdo_cats && ! is_wp_error( $pdo_cats ) ) :
			?>
			<ul class="pdo-filters" data-pdo-filter-group data-pdo-filter-target="#pdo-doc-list .pdo-doc-item">
				<li>
					<button type="button" class="pdo-filters__btn is-active" data-pdo-filter="all" aria-pressed="true">
						<?php esc_html_e( 'All documents', 'pdo-modern' ); ?>
					</button>
				</li>
				<?php foreach ( $pdo_cats as $pdo_cat ) : ?>
					<li>
						<button type="button" class="pdo-filters__btn" data-pdo-filter="<?php echo esc_attr( $pdo_cat->slug ); ?>" aria-pressed="false">
							<?php echo esc_html( $pdo_cat->name ); ?>
						</button>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<div class="pdo-grid pdo-grid--2" id="pdo-doc-list">
			<?php foreach ( $pdo_documents as $pdo_document ) : ?>
				<?php
				$pdo_terms = get_the_terms( $pdo_document, 'pdo_doc_cat' );
				$pdo_slugs = array();
				if ( $pdo_terms && ! is_wp_error( $pdo_terms ) ) {
					foreach ( $pdo_terms as $pdo_term ) {
						$pdo_slugs[] = $pdo_term->slug;
					}
				}
				?>
				<div class="pdo-doc-item" data-pdo-terms="<?php echo esc_attr( implode( ' ', $pdo_slugs ) ); ?>">
					<?php get_template_part( 'template-parts/components/card', 'document', array( 'post' => $pdo_document ) ); ?>
				</div>
			<?php endforeach; ?>
		</div>

		<p class="pdo-align-center" data-pdo-filter-empty hidden>
			<?php esc_html_e( 'No documents match that filter yet.', 'pdo-modern' ); ?>
		</p>
		<?php
		pdo_modern_section_close();
	endif;
endif;

pdo_modern_page_content();

pdo_modern_dynamic_sections( 'transparency', 'after' );
