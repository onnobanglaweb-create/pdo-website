<?php
/**
 * Schema-driven settings screen.
 *
 * @package PDO_Site_Manager
 *
 * @var string $page        Current page slug.
 * @var string $group_key   Settings group key.
 * @var array  $group       Group schema.
 * @var array  $values      Current values.
 * @var array  $tabs        Tabs.
 * @var string $current_tab Active tab key.
 * @var string $page_title  Page title.
 */

defined( 'ABSPATH' ) || exit;

$pdo_tab    = isset( $tabs[ $current_tab ] ) ? $tabs[ $current_tab ] : array();
$pdo_panels = isset( $pdo_tab['panels'] ) ? $pdo_tab['panels'] : array();

$pdo_tab_fields = array();
foreach ( $pdo_panels as $pdo_panel ) {
	if ( empty( $pdo_panel['fields'] ) ) {
		continue;
	}
	foreach ( $pdo_panel['fields'] as $pdo_field ) {
		$pdo_tab_fields[] = $pdo_field;
	}
}
?>
<div class="wrap pdo-wrap">
	<h1 class="pdo-page-title"><?php echo esc_html( $page_title ); ?></h1>

	<?php PDO_SM_Dashboard::settings_notice(); ?>

	<?php if ( count( $tabs ) > 1 ) : ?>
		<nav class="nav-tab-wrapper pdo-tabs" aria-label="<?php esc_attr_e( 'Settings sections', 'pdo-site-manager' ); ?>">
			<?php foreach ( $tabs as $pdo_tab_key => $pdo_tab_def ) : ?>
				<a
					class="nav-tab <?php echo $pdo_tab_key === $current_tab ? 'nav-tab-active' : ''; ?>"
					href="<?php echo esc_url( admin_url( 'admin.php?page=' . $page . '&tab=' . $pdo_tab_key ) ); ?>"
				><?php echo esc_html( $pdo_tab_def['label'] ); ?></a>
			<?php endforeach; ?>
		</nav>
	<?php endif; ?>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="pdo-settings-form">
		<input type="hidden" name="action" value="pdo_sm_save_settings" />
		<input type="hidden" name="pdo_group" value="<?php echo esc_attr( $group_key ); ?>" />
		<input type="hidden" name="pdo_page" value="<?php echo esc_attr( $page ); ?>" />
		<input type="hidden" name="pdo_tab" value="<?php echo esc_attr( $current_tab ); ?>" />
		<?php wp_nonce_field( 'pdo_sm_save_' . $group_key ); ?>
		<?php PDO_SM_Fields::render_field_registry( $pdo_tab_fields ); ?>

		<?php if ( ! empty( $pdo_tab['desc'] ) ) : ?>
			<p class="pdo-tab-desc"><?php echo esc_html( $pdo_tab['desc'] ); ?></p>
		<?php endif; ?>

		<?php foreach ( $pdo_panels as $pdo_panel ) : ?>
			<div class="pdo-panel">
				<?php if ( ! empty( $pdo_panel['title'] ) ) : ?>
					<div class="pdo-panel__head">
						<h2><?php echo esc_html( $pdo_panel['title'] ); ?></h2>
						<?php if ( ! empty( $pdo_panel['desc'] ) ) : ?>
							<p class="description"><?php echo esc_html( $pdo_panel['desc'] ); ?></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<div class="pdo-panel__body">
					<?php
					if ( ! empty( $pdo_panel['fields'] ) ) {
						PDO_SM_Fields::render_fields( $pdo_panel['fields'], $values );
					}
					?>
				</div>
			</div>
		<?php endforeach; ?>

		<div class="pdo-sticky-actions">
			<?php submit_button( __( 'Save Changes', 'pdo-site-manager' ), 'primary', 'submit', false ); ?>
			<span class="pdo-sticky-actions__hint"><?php esc_html_e( 'Saving writes to the database and reports the verified result.', 'pdo-site-manager' ); ?></span>
		</div>
	</form>
</div>
