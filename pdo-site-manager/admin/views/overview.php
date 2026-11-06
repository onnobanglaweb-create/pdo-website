<?php
/**
 * Dashboard overview.
 *
 * @package PDO_Site_Manager
 */

defined( 'ABSPATH' ) || exit;

// Defence in depth: add_submenu_page() already gates the screen, but a
// direct include must never render privileged data.
if ( ! current_user_can( PDO_SM_Capabilities::MANAGE ) ) {
	wp_die( esc_html__( 'You do not have permission to view this screen.', 'pdo-site-manager' ), 403 );
}

$pdo_counts   = PDO_SM_Dashboard::counts();
$pdo_org      = PDO_SM_Settings::get_group( 'org' );
$pdo_pages    = get_option( 'pdo_sm_pages', array() );
$pdo_complete = (bool) get_option( 'pdo_sm_setup_complete' );
$pdo_new      = PDO_SM_Submissions::count( array( 'status' => 'new' ) );
$pdo_total    = PDO_SM_Submissions::count();
$pdo_theme    = wp_get_theme();
$pdo_is_pdo   = ( 'pdo-modern' === $pdo_theme->get_stylesheet() || 'pdo-modern' === $pdo_theme->get_template() );
?>
<div class="wrap pdo-wrap">
	<h1 class="pdo-page-title"><?php esc_html_e( 'PDO Website Manager', 'pdo-site-manager' ); ?></h1>
	<p class="pdo-lede">
		<?php
		printf(
			/* translators: %s: organisation name. */
			esc_html__( 'All website content for %s lives here, so it survives theme changes and updates.', 'pdo-site-manager' ),
			esc_html( isset( $pdo_org['name'] ) ? $pdo_org['name'] : 'PDO' )
		);
		?>
	</p>

	<?php if ( ! $pdo_complete ) : ?>
		<div class="notice notice-warning pdo-notice">
			<p>
				<strong><?php esc_html_e( 'Finish the setup.', 'pdo-site-manager' ); ?></strong>
				<?php esc_html_e( 'Run the setup wizard to create the pages, menus and starter content.', 'pdo-site-manager' ); ?>
				<a class="button button-primary" href="<?php echo esc_url( admin_url( 'admin.php?page=pdo-setup' ) ); ?>"><?php esc_html_e( 'Open Setup Wizard', 'pdo-site-manager' ); ?></a>
			</p>
		</div>
	<?php endif; ?>

	<?php if ( ! $pdo_is_pdo ) : ?>
		<div class="notice notice-info pdo-notice">
			<p><?php esc_html_e( 'The PDO Modern theme is not active. The plugin keeps storing your content, but the front-end layouts ship with that theme.', 'pdo-site-manager' ); ?></p>
		</div>
	<?php endif; ?>

	<div class="pdo-cards">
		<?php foreach ( $pdo_counts as $pdo_slug => $pdo_card ) : ?>
			<div class="pdo-card">
				<div class="pdo-card__icon"><?php echo pdo_get_icon( $pdo_card['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				<div class="pdo-card__body">
					<span class="pdo-card__count"><?php echo esc_html( number_format_i18n( $pdo_card['published'] ) ); ?></span>
					<span class="pdo-card__label"><?php echo esc_html( $pdo_card['label'] ); ?></span>
					<?php if ( $pdo_card['draft'] ) : ?>
						<span class="pdo-card__meta">
							<?php
							printf(
								/* translators: %s: number of drafts. */
								esc_html__( '%s draft', 'pdo-site-manager' ),
								esc_html( number_format_i18n( $pdo_card['draft'] ) )
							);
							?>
						</span>
					<?php endif; ?>
				</div>
				<div class="pdo-card__actions">
					<a class="button button-small" href="<?php echo esc_url( $pdo_card['list_url'] ); ?>"><?php esc_html_e( 'Manage', 'pdo-site-manager' ); ?></a>
					<a class="button button-small button-primary" href="<?php echo esc_url( $pdo_card['add_url'] ); ?>"><?php esc_html_e( 'Add new', 'pdo-site-manager' ); ?></a>
				</div>
			</div>
		<?php endforeach; ?>

		<div class="pdo-card pdo-card--accent">
			<div class="pdo-card__icon"><?php echo pdo_get_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			<div class="pdo-card__body">
				<span class="pdo-card__count"><?php echo esc_html( number_format_i18n( $pdo_new ) ); ?></span>
				<span class="pdo-card__label"><?php esc_html_e( 'New submissions', 'pdo-site-manager' ); ?></span>
				<span class="pdo-card__meta">
					<?php
					printf(
						/* translators: %s: total submissions. */
						esc_html__( '%s total', 'pdo-site-manager' ),
						esc_html( number_format_i18n( $pdo_total ) )
					);
					?>
				</span>
			</div>
			<div class="pdo-card__actions">
				<a class="button button-small" href="<?php echo esc_url( admin_url( 'admin.php?page=pdo-submissions' ) ); ?>"><?php esc_html_e( 'Open inbox', 'pdo-site-manager' ); ?></a>
			</div>
		</div>
	</div>

	<div class="pdo-columns">
		<div class="pdo-column">
			<h2><?php esc_html_e( 'Edit page content', 'pdo-site-manager' ); ?></h2>
			<ul class="pdo-linklist">
				<?php foreach ( PDO_SM_Dashboard::settings_pages() as $pdo_slug_page => $pdo_page_def ) : ?>
					<li>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $pdo_slug_page ) ); ?>">
							<?php echo esc_html( $pdo_page_def['title'] ); ?>
						</a>
					</li>
				<?php endforeach; ?>
				<li><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=pdo_section' ) ); ?>"><?php esc_html_e( 'Dynamic Sections Builder', 'pdo-site-manager' ); ?></a></li>
				<li><a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>"><?php esc_html_e( 'Design &amp; colors (Customizer)', 'pdo-site-manager' ); ?></a></li>
				<li><a href="<?php echo esc_url( admin_url( 'nav-menus.php' ) ); ?>"><?php esc_html_e( 'Navigation menus', 'pdo-site-manager' ); ?></a></li>
			</ul>
		</div>

		<div class="pdo-column">
			<h2><?php esc_html_e( 'Website pages', 'pdo-site-manager' ); ?></h2>
			<?php if ( is_array( $pdo_pages ) && $pdo_pages ) : ?>
				<ul class="pdo-linklist">
					<?php
					foreach ( PDO_SM_Importer::pages() as $pdo_key => $pdo_page ) :
						$pdo_id = isset( $pdo_pages[ $pdo_key ] ) ? (int) $pdo_pages[ $pdo_key ] : 0;
						if ( ! $pdo_id || ! get_post( $pdo_id ) ) {
							continue;
						}
						?>
						<li>
							<a href="<?php echo esc_url( get_permalink( $pdo_id ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( get_the_title( $pdo_id ) ); ?></a>
							<a class="pdo-linklist__edit" href="<?php echo esc_url( get_edit_post_link( $pdo_id ) ); ?>"><?php esc_html_e( 'Edit', 'pdo-site-manager' ); ?></a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php else : ?>
				<p class="description"><?php esc_html_e( 'No pages have been created yet. Run the setup wizard.', 'pdo-site-manager' ); ?></p>
			<?php endif; ?>

			<h2><?php esc_html_e( 'Organisation snapshot', 'pdo-site-manager' ); ?></h2>
			<table class="pdo-kv">
				<tbody>
					<tr><th><?php esc_html_e( 'Established', 'pdo-site-manager' ); ?></th><td><?php echo esc_html( PDO_SM_Settings::get( 'org', 'established', '—' ) ); ?></td></tr>
					<tr><th><?php esc_html_e( 'Registration', 'pdo-site-manager' ); ?></th><td><?php echo esc_html( PDO_SM_Settings::get( 'org', 'registration_no', '—' ) ); ?></td></tr>
					<tr><th><?php esc_html_e( 'Email', 'pdo-site-manager' ); ?></th><td><?php echo esc_html( PDO_SM_Settings::get( 'org', 'email', '—' ) ); ?></td></tr>
					<tr><th><?php esc_html_e( 'Phone', 'pdo-site-manager' ); ?></th><td><?php echo esc_html( PDO_SM_Settings::get( 'org', 'phone', '—' ) ); ?></td></tr>
					<tr><th><?php esc_html_e( 'Bilingual mode', 'pdo-site-manager' ); ?></th><td><?php echo PDO_SM_Helpers::to_bool( PDO_SM_Settings::get( 'org', 'language_enabled', true ) ) ? esc_html__( 'Enabled', 'pdo-site-manager' ) : esc_html__( 'Disabled', 'pdo-site-manager' ); ?></td></tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
