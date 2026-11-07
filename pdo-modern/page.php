<?php
/**
 * Generic page.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();

	if ( has_post_thumbnail() ) :
		?>
		<section class="pdo-page-hero pdo-page-hero--image">
			<div class="pdo-page-hero__bg" aria-hidden="true">
				<?php the_post_thumbnail( 'full', array( 'loading' => 'eager' ) ); ?>
			</div>
			<div class="pdo-container">
				<h1 class="pdo-section__title"><?php echo esc_html( pdo_post_title( get_the_ID() ) ); ?></h1>
			</div>
		</section>
		<?php
	endif;

	pdo_modern_section_open( array( 'class' => 'pdo-page' ) );

	if ( ! has_post_thumbnail() ) {
		pdo_modern_section_header(
			array(
				'title' => pdo_post_title( get_the_ID() ),
				'level' => 'h1',
				'align' => 'left',
			)
		);
	}
	?>
	<div class="pdo-prose pdo-container--narrow">
		<?php
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rendered through the_content filters.
		echo pdo_post_content( get_the_ID() );

		wp_link_pages(
			array(
				'before' => '<nav class="pdo-pagination"><ul class="pdo-pagination__list"><li>',
				'after'  => '</li></ul></nav>',
			)
		);
		?>
	</div>
	<?php
	pdo_modern_section_close();

	if ( comments_open() || get_comments_number() ) {
		pdo_modern_section_open( array( 'class' => 'pdo-section--tight' ) );
		comments_template();
		pdo_modern_section_close();
	}

endwhile;

get_footer();
