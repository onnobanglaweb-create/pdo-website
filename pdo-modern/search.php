<?php
/**
 * Search results.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

get_header();

pdo_modern_section_open( array( 'class' => 'pdo-search-results' ) );

pdo_modern_section_header(
	array(
		'eyebrow' => __( 'Search', 'pdo-modern' ),
		/* translators: %s: search term. */
		'title'   => sprintf( esc_html__( 'Results for “%s”', 'pdo-modern' ), esc_html( get_search_query() ) ),
		'level'   => 'h1',
		'align'   => 'left',
	)
);

get_search_form();

if ( have_posts() ) :
	?>
	<ol class="pdo-results">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<li class="pdo-result">
				<p class="pdo-result__type"><?php echo esc_html( get_post_type_object( get_post_type() )->labels->singular_name ); ?></p>
				<h2 class="pdo-result__title">
					<a href="<?php the_permalink(); ?>"><?php echo esc_html( pdo_post_title( get_the_ID() ) ); ?></a>
				</h2>
				<p class="pdo-result__text"><?php echo esc_html( pdo_modern_trim_words( get_the_excerpt(), 32 ) ); ?></p>
				<p class="pdo-result__url"><?php echo esc_html( (string) get_permalink() ); ?></p>
			</li>
			<?php
		endwhile;
		?>
	</ol>
	<?php
	pdo_modern_pagination();
else :
	get_template_part( 'template-parts/components/no-results' );
endif;

pdo_modern_section_close();

get_footer();
