<?php
/**
 * 404 – page not found.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

get_header();

pdo_modern_section_open( array( 'class' => 'pdo-404' ) );
?>
<div class="pdo-align-center">
	<p class="pdo-404__code">404</p>
	<h1 class="pdo-section__title"><?php esc_html_e( 'We could not find that page', 'pdo-modern' ); ?></h1>
	<p class="pdo-section__lede">
		<?php esc_html_e( 'The link may be outdated, or the page may have been moved. Use the search box or one of the shortcuts below.', 'pdo-modern' ); ?>
	</p>

	<div class="pdo-404__search"><?php get_search_form(); ?></div>

	<div class="pdo-btn-group pdo-404__links">
		<?php
		pdo_modern_button( pdo_t( 'nav_home', __( 'Homepage', 'pdo-modern' ) ), home_url( '/' ), array( 'style' => 'primary', 'icon' => 'home' ) );
		pdo_modern_button( __( 'Our Impact', 'pdo-modern' ), pdo_page_url( 'impact' ), array( 'style' => 'outline' ) );
		pdo_modern_button( __( 'Transparency', 'pdo-modern' ), pdo_page_url( 'transparency' ), array( 'style' => 'outline' ) );
		pdo_modern_button( __( 'Contact us', 'pdo-modern' ), pdo_page_url( 'contact' ), array( 'style' => 'outline' ) );
		?>
	</div>
</div>
<?php
pdo_modern_section_close();

get_footer();
