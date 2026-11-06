<?php
/**
 * Page body – Media & Insights.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

pdo_modern_dynamic_sections( 'media', 'before' );

if ( pdo_section_enabled( 'media', 'intro' ) ) {
	get_template_part(
		'template-parts/components/page-hero',
		null,
		array(
			'eyebrow'     => pdo_text( 'media', 'intro_eyebrow', '' ),
			'title'       => pdo_text( 'media', 'intro_title', get_the_title() ),
			'description' => pdo_text( 'media', 'intro_description', '' ),
			'image'       => (int) pdo_setting( 'media', 'intro_banner', 0 ),
		)
	);
}

$pdo_paged    = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
$pdo_per_page = (int) pdo_setting( 'media', 'per_page', 9 );
$pdo_per_page = $pdo_per_page > 0 ? $pdo_per_page : 9;

$pdo_query = new WP_Query(
	array(
		'post_type'      => 'pdo_news',
		'post_status'    => 'publish',
		'posts_per_page' => $pdo_per_page,
		'paged'          => $pdo_paged,
	)
);

pdo_modern_section_open( array( 'id' => 'pdo-media-list' ) );

if ( pdo_setting( 'media', 'show_filter', true ) ) {
	get_template_part( 'template-parts/components/news-filter' );
}

if ( $pdo_query->have_posts() ) :
	?>
	<div class="pdo-grid pdo-grid--auto" id="pdo-news-list">
		<?php
		while ( $pdo_query->have_posts() ) :
			$pdo_query->the_post();
			get_template_part( 'template-parts/components/card', 'news', array( 'post' => get_post(), 'heading' => 2 ) );
		endwhile;
		?>
	</div>

	<p class="pdo-align-center" data-pdo-filter-empty hidden>
		<?php esc_html_e( 'No articles match that category yet.', 'pdo-modern' ); ?>
	</p>

	<?php
	pdo_modern_pagination( $pdo_query );
else :
	?>
	<p class="pdo-align-center"><?php esc_html_e( 'No articles have been published yet. Please check back soon.', 'pdo-modern' ); ?></p>
	<?php
endif;

wp_reset_postdata();

pdo_modern_section_close();

pdo_modern_page_content();

pdo_modern_dynamic_sections( 'media', 'after' );
