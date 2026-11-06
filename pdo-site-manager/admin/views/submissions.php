<?php
/**
 * Form submissions inbox.
 *
 * @package PDO_Site_Manager
 */

defined( 'ABSPATH' ) || exit;

// Defence in depth: add_submenu_page() already gates the screen, but a
// direct include must never render privileged data.
if ( ! current_user_can( PDO_SM_Capabilities::SUBMISSIONS ) ) {
	wp_die( esc_html__( 'You do not have permission to view this screen.', 'pdo-site-manager' ), 403 );
}

// phpcs:disable WordPress.Security.NonceVerification.Recommended -- read-only filters.
$pdo_filters = array(
	'form_type' => isset( $_GET['form_type'] ) ? sanitize_key( wp_unslash( $_GET['form_type'] ) ) : '',
	'status'    => isset( $_GET['status'] ) ? sanitize_key( wp_unslash( $_GET['status'] ) ) : '',
	'search'    => isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '',
	'date_from' => isset( $_GET['date_from'] ) ? sanitize_text_field( wp_unslash( $_GET['date_from'] ) ) : '',
	'date_to'   => isset( $_GET['date_to'] ) ? sanitize_text_field( wp_unslash( $_GET['date_to'] ) ) : '',
);
$pdo_paged   = isset( $_GET['paged'] ) ? max( 1, absint( wp_unslash( $_GET['paged'] ) ) ) : 1;
$pdo_view    = isset( $_GET['view'] ) ? absint( wp_unslash( $_GET['view'] ) ) : 0;
$pdo_done    = isset( $_GET['pdo_done'] ) ? absint( wp_unslash( $_GET['pdo_done'] ) ) : -1;
// phpcs:enable

$pdo_per_page = 20;
$pdo_total    = PDO_SM_Submissions::count( $pdo_filters );
$pdo_rows     = PDO_SM_Submissions::query(
	array_merge(
		$pdo_filters,
		array(
			'per_page' => $pdo_per_page,
			'page'     => $pdo_paged,
		)
	)
);
$pdo_pages    = max( 1, (int) ceil( $pdo_total / $pdo_per_page ) );
$pdo_statuses = PDO_SM_Submissions::statuses();
$pdo_forms    = PDO_SM_Submissions::form_types();
?>
<div class="wrap pdo-wrap">
	<h1 class="pdo-page-title"><?php esc_html_e( 'Form Submissions', 'pdo-site-manager' ); ?></h1>

	<?php if ( $pdo_done >= 0 ) : ?>
		<div class="notice notice-success is-dismissible">
			<p>
				<?php
				printf(
					/* translators: %s: number of records. */
					esc_html( _n( '%s submission updated.', '%s submissions updated.', max( 1, $pdo_done ), 'pdo-site-manager' ) ),
					esc_html( number_format_i18n( $pdo_done ) )
				);
				?>
			</p>
		</div>
	<?php endif; ?>

	<?php
	if ( $pdo_view ) :
		$pdo_row = PDO_SM_Submissions::get( $pdo_view );
		if ( $pdo_row ) :
			$pdo_payload = json_decode( $pdo_row->payload, true );
			?>
			<div class="pdo-panel pdo-submission-detail">
				<div class="pdo-panel__head">
					<h2><?php echo esc_html( $pdo_row->subject ? $pdo_row->subject : __( 'Submission', 'pdo-site-manager' ) ); ?></h2>
					<p class="description">
						<?php echo esc_html( isset( $pdo_forms[ $pdo_row->form_type ] ) ? $pdo_forms[ $pdo_row->form_type ] : $pdo_row->form_type ); ?>
						&middot; <?php echo esc_html( mysql2date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $pdo_row->created_at ) ); ?>
						&middot; <?php echo esc_html( strtoupper( $pdo_row->lang ) ); ?>
					</p>
				</div>
				<div class="pdo-panel__body">
					<table class="pdo-kv">
						<tbody>
						<?php if ( is_array( $pdo_payload ) ) : ?>
							<?php foreach ( $pdo_payload as $pdo_label => $pdo_value ) : ?>
								<tr>
									<th><?php echo esc_html( $pdo_label ); ?></th>
									<td><?php echo nl2br( esc_html( is_array( $pdo_value ) ? implode( ', ', $pdo_value ) : (string) $pdo_value ) ); ?></td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
							<tr><th><?php esc_html_e( 'Submitted from', 'pdo-site-manager' ); ?></th><td><a href="<?php echo esc_url( $pdo_row->source_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $pdo_row->source_url ); ?></a></td></tr>
						</tbody>
					</table>

					<p class="pdo-submission-detail__actions">
						<?php if ( $pdo_row->email ) : ?>
							<a class="button button-primary" href="<?php echo esc_url( 'mailto:' . $pdo_row->email . '?subject=' . rawurlencode( 'Re: ' . $pdo_row->subject ) ); ?>"><?php esc_html_e( 'Reply by email', 'pdo-site-manager' ); ?></a>
						<?php endif; ?>
						<a class="button" href="<?php echo esc_url( remove_query_arg( 'view' ) ); ?>"><?php esc_html_e( 'Back to the list', 'pdo-site-manager' ); ?></a>
					</p>
				</div>
			</div>
			<?php
		endif;
	endif;
	?>

	<form method="get" class="pdo-submission-filters">
		<input type="hidden" name="page" value="pdo-submissions" />
		<label class="screen-reader-text" for="pdo-filter-form"><?php esc_html_e( 'Form type', 'pdo-site-manager' ); ?></label>
		<select name="form_type" id="pdo-filter-form">
			<option value=""><?php esc_html_e( 'All forms', 'pdo-site-manager' ); ?></option>
			<?php foreach ( $pdo_forms as $pdo_key => $pdo_label ) : ?>
				<option value="<?php echo esc_attr( $pdo_key ); ?>" <?php selected( $pdo_filters['form_type'], $pdo_key ); ?>><?php echo esc_html( $pdo_label ); ?></option>
			<?php endforeach; ?>
		</select>

		<label class="screen-reader-text" for="pdo-filter-status"><?php esc_html_e( 'Status', 'pdo-site-manager' ); ?></label>
		<select name="status" id="pdo-filter-status">
			<option value=""><?php esc_html_e( 'All statuses', 'pdo-site-manager' ); ?></option>
			<?php foreach ( $pdo_statuses as $pdo_key => $pdo_label ) : ?>
				<option value="<?php echo esc_attr( $pdo_key ); ?>" <?php selected( $pdo_filters['status'], $pdo_key ); ?>><?php echo esc_html( $pdo_label ); ?></option>
			<?php endforeach; ?>
		</select>

		<label for="pdo-date-from" class="screen-reader-text"><?php esc_html_e( 'From', 'pdo-site-manager' ); ?></label>
		<input type="date" id="pdo-date-from" name="date_from" value="<?php echo esc_attr( $pdo_filters['date_from'] ); ?>" />
		<label for="pdo-date-to" class="screen-reader-text"><?php esc_html_e( 'To', 'pdo-site-manager' ); ?></label>
		<input type="date" id="pdo-date-to" name="date_to" value="<?php echo esc_attr( $pdo_filters['date_to'] ); ?>" />

		<label for="pdo-search" class="screen-reader-text"><?php esc_html_e( 'Search submissions', 'pdo-site-manager' ); ?></label>
		<input type="search" id="pdo-search" name="s" value="<?php echo esc_attr( $pdo_filters['search'] ); ?>" placeholder="<?php esc_attr_e( 'Search name, email, content…', 'pdo-site-manager' ); ?>" />

		<?php submit_button( __( 'Filter', 'pdo-site-manager' ), 'secondary', '', false ); ?>
		<a class="button" href="<?php echo esc_url( wp_nonce_url( add_query_arg( array_merge( array( 'action' => 'pdo_sm_export_submissions' ), array_filter( $pdo_filters ) ), admin_url( 'admin-post.php' ) ), 'pdo_sm_export_submissions' ) ); ?>">
			<?php esc_html_e( 'Export CSV', 'pdo-site-manager' ); ?>
		</a>
	</form>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="pdo_sm_submission_action" />
		<?php wp_nonce_field( 'pdo_sm_submissions' ); ?>

		<div class="tablenav top">
			<div class="alignleft actions bulkactions">
				<label class="screen-reader-text" for="pdo-bulk"><?php esc_html_e( 'Bulk action', 'pdo-site-manager' ); ?></label>
				<select name="pdo_action" id="pdo-bulk">
					<option value=""><?php esc_html_e( 'Bulk actions', 'pdo-site-manager' ); ?></option>
					<?php foreach ( $pdo_statuses as $pdo_key => $pdo_label ) : ?>
						<option value="status_<?php echo esc_attr( $pdo_key ); ?>">
							<?php
							printf(
								/* translators: %s: status label. */
								esc_html__( 'Mark as %s', 'pdo-site-manager' ),
								esc_html( $pdo_label )
							);
							?>
						</option>
					<?php endforeach; ?>
					<option value="delete"><?php esc_html_e( 'Delete permanently', 'pdo-site-manager' ); ?></option>
				</select>
				<?php submit_button( __( 'Apply', 'pdo-site-manager' ), 'secondary', '', false ); ?>
			</div>
			<div class="tablenav-pages">
				<span class="displaying-num">
					<?php
					printf(
						/* translators: %s: number of items. */
						esc_html( _n( '%s item', '%s items', $pdo_total, 'pdo-site-manager' ) ),
						esc_html( number_format_i18n( $pdo_total ) )
					);
					?>
				</span>
				<?php
				// paginate_links() returns null when there is only one page.
				echo wp_kses_post(
					(string) paginate_links(
						array(
							'base'      => add_query_arg( 'paged', '%#%' ),
							'format'    => '',
							'prev_text' => '&laquo;',
							'next_text' => '&raquo;',
							'total'     => $pdo_pages,
							'current'   => $pdo_paged,
							'type'      => 'plain',
						)
					)
				);
				?>
			</div>
		</div>

		<table class="wp-list-table widefat fixed striped pdo-submissions-table">
			<thead>
				<tr>
					<td class="manage-column column-cb check-column"><input type="checkbox" id="pdo-check-all" /><label for="pdo-check-all" class="screen-reader-text"><?php esc_html_e( 'Select all', 'pdo-site-manager' ); ?></label></td>
					<th scope="col"><?php esc_html_e( 'Subject', 'pdo-site-manager' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Form', 'pdo-site-manager' ); ?></th>
					<th scope="col"><?php esc_html_e( 'From', 'pdo-site-manager' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Status', 'pdo-site-manager' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Received', 'pdo-site-manager' ); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php if ( ! $pdo_rows ) : ?>
				<tr><td colspan="6"><?php esc_html_e( 'No submissions found.', 'pdo-site-manager' ); ?></td></tr>
			<?php endif; ?>
			<?php foreach ( $pdo_rows as $pdo_row ) : ?>
				<tr class="pdo-status-<?php echo esc_attr( $pdo_row->status ); ?>">
					<th scope="row" class="check-column">
						<label class="screen-reader-text" for="pdo-cb-<?php echo esc_attr( $pdo_row->id ); ?>"><?php esc_html_e( 'Select submission', 'pdo-site-manager' ); ?></label>
						<input type="checkbox" id="pdo-cb-<?php echo esc_attr( $pdo_row->id ); ?>" name="ids[]" value="<?php echo esc_attr( $pdo_row->id ); ?>" />
					</th>
					<td>
						<strong><a href="<?php echo esc_url( add_query_arg( 'view', $pdo_row->id ) ); ?>"><?php echo esc_html( $pdo_row->subject ? $pdo_row->subject : __( '(no subject)', 'pdo-site-manager' ) ); ?></a></strong>
					</td>
					<td><?php echo esc_html( isset( $pdo_forms[ $pdo_row->form_type ] ) ? $pdo_forms[ $pdo_row->form_type ] : $pdo_row->form_type ); ?></td>
					<td>
						<?php echo esc_html( $pdo_row->name ); ?>
						<?php if ( $pdo_row->email ) : ?>
							<br /><a href="mailto:<?php echo esc_attr( $pdo_row->email ); ?>"><?php echo esc_html( $pdo_row->email ); ?></a>
						<?php endif; ?>
					</td>
					<td><span class="pdo-badge pdo-badge--<?php echo esc_attr( $pdo_row->status ); ?>"><?php echo esc_html( isset( $pdo_statuses[ $pdo_row->status ] ) ? $pdo_statuses[ $pdo_row->status ] : $pdo_row->status ); ?></span></td>
					<td><?php echo esc_html( mysql2date( get_option( 'date_format' ) . ' H:i', $pdo_row->created_at ) ); ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</form>
</div>
