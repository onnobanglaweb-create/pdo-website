<?php
/**
 * Page body – Get Involved.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

pdo_modern_dynamic_sections( 'involved', 'before' );

/* ---------------------------------------------------------------- Intro -- */
if ( pdo_section_enabled( 'involved', 'intro' ) ) {
	get_template_part(
		'template-parts/components/page-hero',
		null,
		array(
			'eyebrow'     => pdo_text( 'involved', 'intro_eyebrow', '' ),
			'title'       => pdo_text( 'involved', 'intro_title', get_the_title() ),
			'description' => pdo_text( 'involved', 'intro_description', '' ),
			'image'       => (int) pdo_setting( 'involved', 'intro_image', 0 ),
		)
	);
}

/* ------------------------------------------------- Volunteer / partner --- */
$pdo_tabs = array();

if ( pdo_section_enabled( 'involved', 'volunteer' ) ) {
	$pdo_tabs['volunteer'] = array(
		'label' => pdo_text( 'involved', 'volunteer_tab_label', __( 'Volunteer', 'pdo-modern' ) ),
		'title' => pdo_text( 'involved', 'volunteer_title', '' ),
		'desc'  => pdo_text( 'involved', 'volunteer_description', '' ),
		'icon'  => 'hand-heart',
	);
}

if ( pdo_section_enabled( 'involved', 'partner' ) ) {
	$pdo_tabs['partner'] = array(
		'label' => pdo_text( 'involved', 'partner_tab_label', __( 'Partner with us', 'pdo-modern' ) ),
		'title' => pdo_text( 'involved', 'partner_title', '' ),
		'desc'  => pdo_text( 'involved', 'partner_description', '' ),
		'icon'  => 'handshake',
	);
}

if ( $pdo_tabs ) :
	// If a submission just came back, open the tab it belongs to.
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display only.
	$pdo_active = isset( $_GET['pdo_form'] ) ? sanitize_key( wp_unslash( $_GET['pdo_form'] ) ) : '';
	if ( ! isset( $pdo_tabs[ $pdo_active ] ) ) {
		$pdo_active = (string) array_key_first( $pdo_tabs );
	}

	pdo_modern_section_open( array( 'id' => 'pdo-involved-forms' ) );
	?>
	<div data-pdo-tabs>
		<ul class="pdo-tabs" role="tablist">
			<?php foreach ( $pdo_tabs as $pdo_key => $pdo_tab ) : ?>
				<li role="presentation">
					<button
						type="button"
						class="pdo-tabs__btn"
						role="tab"
						id="pdo-tab-<?php echo esc_attr( $pdo_key ); ?>"
						aria-controls="pdo-tabpanel-<?php echo esc_attr( $pdo_key ); ?>"
						aria-selected="<?php echo $pdo_key === $pdo_active ? 'true' : 'false'; ?>"
						tabindex="<?php echo $pdo_key === $pdo_active ? '0' : '-1'; ?>"
					>
						<?php pdo_icon( $pdo_tab['icon'], array( 'size' => 16 ) ); ?>
						<?php echo esc_html( $pdo_tab['label'] ); ?>
					</button>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php foreach ( $pdo_tabs as $pdo_key => $pdo_tab ) : ?>
			<div
				class="pdo-tabpanel"
				role="tabpanel"
				id="pdo-tabpanel-<?php echo esc_attr( $pdo_key ); ?>"
				aria-labelledby="pdo-tab-<?php echo esc_attr( $pdo_key ); ?>"
				<?php echo $pdo_key === $pdo_active ? '' : 'hidden'; ?>
			>
				<?php
				pdo_modern_section_header(
					array(
						'title'       => $pdo_tab['title'],
						'description' => $pdo_tab['desc'],
						'align'       => 'left',
						'level'       => 'h2',
					)
				);

				get_template_part( 'template-parts/components/form', null, array( 'type' => $pdo_key, 'heading' => 3 ) );
				?>
			</div>
		<?php endforeach; ?>
	</div>
	<?php
	pdo_modern_section_close();
endif;

/* -------------------------------------------------------- Adopt a project */
if ( pdo_section_enabled( 'involved', 'adopt' ) ) :
	$pdo_projects = pdo_rows( 'involved', 'adopt_projects' );

	if ( $pdo_projects ) :
		pdo_modern_section_open(
			array(
				'id'         => 'pdo-involved-adopt',
				'background' => 'light',
			)
		);

		pdo_modern_section_header(
			array(
				'eyebrow'     => pdo_text( 'involved', 'adopt_eyebrow', '' ),
				'title'       => pdo_text( 'involved', 'adopt_title', '' ),
				'description' => pdo_text( 'involved', 'adopt_description', '' ),
			)
		);
		?>
		<div class="pdo-grid pdo-grid--auto">
			<?php foreach ( $pdo_projects as $pdo_row ) : ?>
				<?php
				$pdo_title = pdo_row_text( $pdo_row, 'title', '' );
				$pdo_art   = pdo_row_image( $pdo_row, array( 'size' => 'pdo-card', 'alt' => isset( $pdo_row['alt'] ) ? $pdo_row['alt'] : $pdo_title ) );
				$pdo_form  = ! empty( $pdo_row['form'] ) && in_array( $pdo_row['form'], array( 'volunteer', 'partner', 'contact' ), true ) ? $pdo_row['form'] : 'partner';
				$pdo_url   = ! empty( $pdo_row['btn_url'] ) ? (string) $pdo_row['btn_url'] : '#pdo-form-' . $pdo_form;
				?>
				<article class="pdo-card" data-pdo-reveal>
					<?php if ( $pdo_art ) : ?>
						<div class="pdo-card__media">
							<?php
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by the media helper.
							echo $pdo_art;
							?>
							<?php if ( pdo_row_text( $pdo_row, 'category', '' ) ) : ?>
								<span class="pdo-card__tag"><?php echo esc_html( pdo_row_text( $pdo_row, 'category', '' ) ); ?></span>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<div class="pdo-card__body">
						<h3 class="pdo-card__title"><?php echo esc_html( $pdo_title ); ?></h3>

						<?php if ( pdo_row_text( $pdo_row, 'description', '' ) ) : ?>
							<p class="pdo-card__text"><?php echo esc_html( pdo_row_text( $pdo_row, 'description', '' ) ); ?></p>
						<?php endif; ?>

						<div class="pdo-card__footer">
							<?php if ( pdo_row_text( $pdo_row, 'stat', '' ) ) : ?>
								<span class="pdo-badge"><?php echo esc_html( pdo_row_text( $pdo_row, 'stat', '' ) ); ?></span>
							<?php endif; ?>
							<a
								class="pdo-btn pdo-btn--primary pdo-btn--sm"
								href="<?php echo esc_url( $pdo_url ); ?>"
								data-pdo-prefill="<?php echo esc_attr( $pdo_title ); ?>"
								data-pdo-form="<?php echo esc_attr( $pdo_form ); ?>"
							>
								<span><?php echo esc_html( pdo_row_text( $pdo_row, 'btn_label', __( 'Adopt this project', 'pdo-modern' ) ) ); ?></span>
							</a>
						</div>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
		<?php
		pdo_modern_section_close();
	endif;
endif;

pdo_modern_page_content();

pdo_modern_dynamic_sections( 'involved', 'after' );
