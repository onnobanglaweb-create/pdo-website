<?php
/**
 * Search form.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

$pdo_uid = 'pdo-search-' . wp_unique_id();
?>
<form role="search" method="get" class="pdo-searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $pdo_uid ); ?>">
		<?php echo esc_html( pdo_t( 'search', __( 'Search this site', 'pdo-modern' ) ) ); ?>
	</label>
	<input
		type="search"
		id="<?php echo esc_attr( $pdo_uid ); ?>"
		class="pdo-searchform__input"
		name="s"
		value="<?php echo esc_attr( get_search_query() ); ?>"
		placeholder="<?php echo esc_attr( pdo_t( 'search_placeholder', __( 'Search programs, reports, news…', 'pdo-modern' ) ) ); ?>"
	/>
	<button type="submit" class="pdo-btn pdo-btn--primary">
		<?php pdo_icon( 'search', array( 'size' => 16 ) ); ?>
		<span class="screen-reader-text"><?php esc_html_e( 'Search', 'pdo-modern' ); ?></span>
	</button>
</form>
