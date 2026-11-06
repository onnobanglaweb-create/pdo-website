<?php
/**
 * Page body – Contact Us.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

pdo_modern_dynamic_sections( 'contact', 'before' );

get_template_part(
	'template-parts/components/page-hero',
	null,
	array(
		'eyebrow'     => pdo_text( 'contact', 'intro_eyebrow', '' ),
		'title'       => pdo_text( 'contact', 'intro_title', get_the_title() ),
		'description' => pdo_text( 'contact', 'intro_description', '' ),
		'icon'        => 'mail',
	)
);

pdo_modern_section_open( array( 'id' => 'pdo-contact-details' ) );
get_template_part( 'template-parts/components/contact-cards' );
pdo_modern_section_close();

if ( pdo_section_enabled( 'contact', 'form' ) ) :
	pdo_modern_section_open(
		array(
			'id'         => 'pdo-contact-form',
			'background' => 'light',
		)
	);
	?>
	<div class="pdo-split">
		<div>
			<?php get_template_part( 'template-parts/components/form', null, array( 'type' => 'contact' ) ); ?>
		</div>

		<div class="pdo-contact__aside">
			<?php get_template_part( 'template-parts/components/map' ); ?>

			<?php if ( pdo_text( 'contact', 'legal_title', '' ) || pdo_text( 'contact', 'legal_description', '' ) ) : ?>
				<div class="pdo-card pdo-card--outline pdo-contact__legal">
					<div class="pdo-card__body">
						<span class="pdo-card__icon"><?php pdo_icon( 'shield-check', array( 'size' => 20 ) ); ?></span>
						<?php if ( pdo_text( 'contact', 'legal_title', '' ) ) : ?>
							<h2 class="pdo-card__title"><?php echo esc_html( pdo_text( 'contact', 'legal_title', '' ) ); ?></h2>
						<?php endif; ?>
						<?php if ( pdo_text( 'contact', 'legal_description', '' ) ) : ?>
							<p class="pdo-card__text"><?php echo esc_html( pdo_text( 'contact', 'legal_description', '' ) ); ?></p>
						<?php endif; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<?php
	pdo_modern_section_close();
endif;

pdo_modern_page_content();

pdo_modern_dynamic_sections( 'contact', 'after' );
