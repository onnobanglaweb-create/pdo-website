<?php
/**
 * Setup wizard.
 *
 * @package PDO_Site_Manager
 */

defined( 'ABSPATH' ) || exit;

// Defence in depth: add_submenu_page() already gates the screen, but a
// direct include must never render privileged data.
if ( ! current_user_can( PDO_SM_Capabilities::MANAGE ) ) {
	wp_die( esc_html__( 'You do not have permission to view this screen.', 'pdo-site-manager' ), 403 );
}

$pdo_report   = get_transient( 'pdo_sm_import_report_' . get_current_user_id() );
$pdo_complete = (bool) get_option( 'pdo_sm_setup_complete' );
$pdo_pages    = get_option( 'pdo_sm_pages', array() );

$pdo_steps = array(
	'settings'  => array(
		__( 'Page content & settings', 'pdo-site-manager' ),
		__( 'Writes the migrated texts, statistics, pillars, milestones and form definitions into the option groups. Groups you have already edited are never touched.', 'pdo-site-manager' ),
	),
	'terms'     => array(
		__( 'Categories & groups', 'pdo-site-manager' ),
		__( 'Creates the news categories, program categories, team groups, partner categories and document categories.', 'pdo-site-manager' ),
	),
	'pages'     => array(
		__( 'Website pages', 'pdo-site-manager' ),
		__( 'Creates Home, About, Impact, Transparency, Media, Get Involved and Contact and assigns the matching page templates. Existing pages with the same slug are reused.', 'pdo-site-manager' ),
	),
	'programs'  => array(
		__( 'Programs', 'pdo-site-manager' ),
		__( 'Creates the five flagship program records with their statistics and achievements.', 'pdo-site-manager' ),
	),
	'team'      => array(
		__( 'Leadership', 'pdo-site-manager' ),
		__( 'Creates the Executive Committee and management records.', 'pdo-site-manager' ),
	),
	'partners'  => array(
		__( 'Partners', 'pdo-site-manager' ),
		__( 'Creates the strategic partner records.', 'pdo-site-manager' ),
	),
	'stories'   => array(
		__( 'Impact stories', 'pdo-site-manager' ),
		__( 'Creates the three human impact stories.', 'pdo-site-manager' ),
	),
	'documents' => array(
		__( 'Policy & report library', 'pdo-site-manager' ),
		__( 'Creates the four policy records. No file is attached — upload the real PDF on each record to switch the download button on.', 'pdo-site-manager' ),
	),
	'news'      => array(
		__( 'News articles', 'pdo-site-manager' ),
		__( 'Creates the six field-action and policy articles.', 'pdo-site-manager' ),
	),
	'menus'     => array(
		__( 'Navigation menus', 'pdo-site-manager' ),
		__( 'Builds the primary menu with its dropdowns plus the two footer menus, including the Bengali labels.', 'pdo-site-manager' ),
	),
);
?>
<div class="wrap pdo-wrap">
	<h1 class="pdo-page-title"><?php esc_html_e( 'PDO Setup Wizard', 'pdo-site-manager' ); ?></h1>
	<p class="pdo-lede"><?php esc_html_e( 'Migrates the original PDO website into editable WordPress records. Every step is idempotent: running it again never duplicates content and never overwrites something you have edited.', 'pdo-site-manager' ); ?></p>

	<?php if ( is_array( $pdo_report ) && $pdo_report ) : ?>
		<div class="notice notice-success">
			<p><strong><?php esc_html_e( 'Import finished.', 'pdo-site-manager' ); ?></strong></p>
			<ul class="pdo-report">
				<?php foreach ( $pdo_report as $pdo_step => $pdo_result ) : ?>
					<li>
						<strong><?php echo esc_html( isset( $pdo_steps[ $pdo_step ] ) ? $pdo_steps[ $pdo_step ][0] : $pdo_step ); ?>:</strong>
						<?php
						if ( isset( $pdo_result['created'] ) && is_array( $pdo_result['created'] ) ) {
							echo esc_html(
								$pdo_result['created']
									? implode( ', ', $pdo_result['created'] )
									: __( 'already present — nothing to do', 'pdo-site-manager' )
							);
						} elseif ( isset( $pdo_result['created'] ) ) {
							printf(
								/* translators: %s: number of records. */
								esc_html__( '%s new record(s)', 'pdo-site-manager' ),
								esc_html( number_format_i18n( (int) $pdo_result['created'] ) )
							);
						}
						?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php delete_transient( 'pdo_sm_import_report_' . get_current_user_id() ); ?>
	<?php endif; ?>

	<?php if ( $pdo_complete ) : ?>
		<div class="notice notice-info"><p><?php esc_html_e( 'The wizard has run before. You can safely run it again to add anything that is missing.', 'pdo-site-manager' ); ?></p></div>
	<?php endif; ?>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="pdo_sm_run_import" />
		<?php wp_nonce_field( 'pdo_sm_run_import' ); ?>

		<div class="pdo-panel">
			<div class="pdo-panel__head">
				<h2><?php esc_html_e( 'Choose what to import', 'pdo-site-manager' ); ?></h2>
			</div>
			<div class="pdo-panel__body">
				<?php foreach ( $pdo_steps as $pdo_key => $pdo_step ) : ?>
					<label class="pdo-step">
						<input type="checkbox" name="steps[]" value="<?php echo esc_attr( $pdo_key ); ?>" checked="checked" />
						<span>
							<strong><?php echo esc_html( $pdo_step[0] ); ?></strong>
							<span class="description"><?php echo esc_html( $pdo_step[1] ); ?></span>
						</span>
					</label>
				<?php endforeach; ?>
			</div>
		</div>

		<?php submit_button( __( 'Run the import', 'pdo-site-manager' ) ); ?>
	</form>

	<?php if ( is_array( $pdo_pages ) && $pdo_pages ) : ?>
		<div class="pdo-panel">
			<div class="pdo-panel__head"><h2><?php esc_html_e( 'Next steps', 'pdo-site-manager' ); ?></h2></div>
			<div class="pdo-panel__body">
				<ol class="pdo-ol">
					<li><?php esc_html_e( 'Activate the PDO Modern theme under Appearance → Themes.', 'pdo-site-manager' ); ?></li>
					<li><?php esc_html_e( 'Open Appearance → Customize to set the logos, colors, typography and section order.', 'pdo-site-manager' ); ?></li>
					<li><?php esc_html_e( 'Replace the demo photographs with your own images in each record.', 'pdo-site-manager' ); ?></li>
					<li><?php esc_html_e( 'Upload the real policy and audit PDFs on each document record.', 'pdo-site-manager' ); ?></li>
					<li><?php esc_html_e( 'Fill in the Bengali fields — nothing is machine translated.', 'pdo-site-manager' ); ?></li>
				</ol>
			</div>
		</div>
	<?php endif; ?>
</div>
