<?php
/**
 * Component – empty state.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="pdo-empty pdo-align-center">
	<span class="pdo-empty__icon"><?php pdo_icon( 'search', array( 'size' => 34 ) ); ?></span>
	<h2 class="pdo-empty__title"><?php esc_html_e( 'Nothing found here yet', 'pdo-modern' ); ?></h2>

	<?php if ( is_search() ) : ?>
		<p><?php esc_html_e( 'Try a different wording, or browse the sections below.', 'pdo-modern' ); ?></p>
		<?php get_search_form(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'This section has no published content at the moment. Please check back soon.', 'pdo-modern' ); ?></p>
	<?php endif; ?>

	<div class="pdo-btn-group pdo-empty__links">
		<?php
		pdo_modern_button( pdo_t( 'nav_home', __( 'Back to the homepage', 'pdo-modern' ) ), home_url( '/' ), array( 'style' => 'primary', 'icon' => 'home' ) );
		pdo_modern_button( __( 'Our Impact', 'pdo-modern' ), pdo_page_url( 'impact' ), array( 'style' => 'outline' ) );
		pdo_modern_button( __( 'Contact us', 'pdo-modern' ), pdo_page_url( 'contact' ), array( 'style' => 'outline' ) );
		?>
	</div>
</div>
