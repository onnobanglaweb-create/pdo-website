<?php
/**
 * Form submission storage.
 *
 * Submissions live in a dedicated, indexed table so that searching,
 * filtering, status changes and CSV export stay fast even with a large
 * number of records. Every query uses $wpdb->prepare().
 *
 * @package PDO_Site_Manager
 */

defined( 'ABSPATH' ) || exit;

/**
 * Submissions repository.
 */
class PDO_SM_Submissions {

	/**
	 * Allowed statuses.
	 *
	 * @return array<string,string>
	 */
	public static function statuses() {
		return array(
			'new'       => __( 'New', 'pdo-site-manager' ),
			'read'      => __( 'Read', 'pdo-site-manager' ),
			'pending'   => __( 'Pending', 'pdo-site-manager' ),
			'processed' => __( 'Processed', 'pdo-site-manager' ),
		);
	}

	/**
	 * Known form types.
	 *
	 * @return array<string,string>
	 */
	public static function form_types() {
		return array(
			'contact'   => __( 'Contact', 'pdo-site-manager' ),
			'volunteer' => __( 'Volunteer', 'pdo-site-manager' ),
			'partner'   => __( 'Partnership', 'pdo-site-manager' ),
		);
	}

	/**
	 * Hooks.
	 */
	public function __construct() {
		add_action( 'admin_post_pdo_sm_submission_action', array( $this, 'handle_action' ) );
		add_action( 'admin_post_pdo_sm_export_submissions', array( $this, 'handle_export' ) );
	}

	/**
	 * Table name.
	 *
	 * @return string
	 */
	public static function table() {
		global $wpdb;
		return $wpdb->prefix . 'pdo_submissions';
	}

	/**
	 * Insert a submission.
	 *
	 * @param array $data Submission data.
	 * @return int|WP_Error Inserted ID or error.
	 */
	public static function insert( $data ) {
		global $wpdb;

		$row = array(
			'form_type'  => isset( $data['form_type'] ) ? sanitize_key( $data['form_type'] ) : 'contact',
			'subject'    => isset( $data['subject'] ) ? sanitize_text_field( $data['subject'] ) : '',
			'name'       => isset( $data['name'] ) ? sanitize_text_field( $data['name'] ) : '',
			'email'      => isset( $data['email'] ) ? sanitize_email( $data['email'] ) : '',
			'phone'      => isset( $data['phone'] ) ? sanitize_text_field( $data['phone'] ) : '',
			'payload'    => wp_json_encode( isset( $data['payload'] ) ? $data['payload'] : array() ),
			'status'     => 'new',
			'ip_hash'    => isset( $data['ip_hash'] ) ? substr( (string) $data['ip_hash'], 0, 64 ) : '',
			'source_url' => isset( $data['source_url'] ) ? esc_url_raw( $data['source_url'] ) : '',
			'lang'       => isset( $data['lang'] ) ? sanitize_key( $data['lang'] ) : 'en',
			'created_at' => current_time( 'mysql' ),
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- custom table insert.
		$ok = $wpdb->insert( self::table(), $row, array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ) );

		if ( false === $ok ) {
			return new WP_Error( 'pdo_sm_db_error', __( 'The submission could not be stored.', 'pdo-site-manager' ) );
		}

		return (int) $wpdb->insert_id;
	}

	/**
	 * Count submissions, optionally filtered.
	 *
	 * @param array $args Filters.
	 * @return int
	 */
	public static function count( $args = array() ) {
		global $wpdb;
		list( $where, $params ) = self::build_where( $args );
		$table                  = self::table();
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$sql = "SELECT COUNT(*) FROM `{$table}` {$where}";
		if ( $params ) {
			return (int) $wpdb->get_var( $wpdb->prepare( $sql, $params ) );
		}
		return (int) $wpdb->get_var( $sql );
		// phpcs:enable
	}

	/**
	 * Query submissions.
	 *
	 * @param array $args Filters: form_type, status, search, date_from, date_to, per_page, page, orderby, order.
	 * @return array<object>
	 */
	public static function query( $args = array() ) {
		global $wpdb;

		$args = wp_parse_args(
			$args,
			array(
				'per_page' => 20,
				'page'     => 1,
				'orderby'  => 'created_at',
				'order'    => 'DESC',
			)
		);

		list( $where, $params ) = self::build_where( $args );

		$allowed_orderby = array( 'id', 'created_at', 'form_type', 'status', 'name', 'email' );
		$orderby         = in_array( $args['orderby'], $allowed_orderby, true ) ? $args['orderby'] : 'created_at';
		$order           = 'ASC' === strtoupper( $args['order'] ) ? 'ASC' : 'DESC';
		$per_page        = max( 1, min( 500, (int) $args['per_page'] ) );
		$offset          = max( 0, ( (int) $args['page'] - 1 ) * $per_page );

		$table = self::table();
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$sql      = "SELECT * FROM `{$table}` {$where} ORDER BY `{$orderby}` {$order} LIMIT %d OFFSET %d";
		$params[] = $per_page;
		$params[] = $offset;
		return (array) $wpdb->get_results( $wpdb->prepare( $sql, $params ) );
		// phpcs:enable
	}

	/**
	 * Fetch a single submission.
	 *
	 * @param int $id Submission ID.
	 * @return object|null
	 */
	public static function get( $id ) {
		global $wpdb;
		$table = self::table();
		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `{$table}` WHERE id = %d", (int) $id ) );
		// phpcs:enable
	}

	/**
	 * Update a submission status.
	 *
	 * @param int    $id     Submission ID.
	 * @param string $status New status.
	 * @return bool
	 */
	public static function set_status( $id, $status ) {
		global $wpdb;
		if ( ! array_key_exists( $status, self::statuses() ) ) {
			return false;
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		return false !== $wpdb->update( self::table(), array( 'status' => $status ), array( 'id' => (int) $id ), array( '%s' ), array( '%d' ) );
	}

	/**
	 * Delete a submission.
	 *
	 * @param int $id Submission ID.
	 * @return bool
	 */
	public static function delete( $id ) {
		global $wpdb;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		return false !== $wpdb->delete( self::table(), array( 'id' => (int) $id ), array( '%d' ) );
	}

	/**
	 * Build the WHERE clause.
	 *
	 * @param array $args Filters.
	 * @return array{0:string,1:array}
	 */
	private static function build_where( $args ) {
		global $wpdb;
		$clauses = array();
		$params  = array();

		if ( ! empty( $args['form_type'] ) && array_key_exists( $args['form_type'], self::form_types() ) ) {
			$clauses[] = 'form_type = %s';
			$params[]  = $args['form_type'];
		}
		if ( ! empty( $args['status'] ) && array_key_exists( $args['status'], self::statuses() ) ) {
			$clauses[] = 'status = %s';
			$params[]  = $args['status'];
		}
		if ( ! empty( $args['date_from'] ) ) {
			$clauses[] = 'created_at >= %s';
			$params[]  = $args['date_from'] . ' 00:00:00';
		}
		if ( ! empty( $args['date_to'] ) ) {
			$clauses[] = 'created_at <= %s';
			$params[]  = $args['date_to'] . ' 23:59:59';
		}
		if ( ! empty( $args['search'] ) ) {
			$like      = '%' . $wpdb->esc_like( $args['search'] ) . '%';
			$clauses[] = '(name LIKE %s OR email LIKE %s OR subject LIKE %s OR payload LIKE %s)';
			$params[]  = $like;
			$params[]  = $like;
			$params[]  = $like;
			$params[]  = $like;
		}

		$where = $clauses ? 'WHERE ' . implode( ' AND ', $clauses ) : '';

		return array( $where, $params );
	}

	/**
	 * Handle single / bulk actions from the submissions screen.
	 *
	 * @return void
	 */
	public function handle_action() {
		PDO_SM_Capabilities::assert_submissions();
		check_admin_referer( 'pdo_sm_submissions' );

		$action = isset( $_POST['pdo_action'] ) ? sanitize_key( wp_unslash( $_POST['pdo_action'] ) ) : '';
		if ( ! $action && isset( $_GET['pdo_action'] ) ) {
			$action = sanitize_key( wp_unslash( $_GET['pdo_action'] ) );
		}

		$ids = array();
		if ( isset( $_REQUEST['ids'] ) ) {
			$ids = array_map( 'absint', (array) wp_unslash( $_REQUEST['ids'] ) );
		}
		if ( isset( $_REQUEST['id'] ) ) {
			$ids[] = absint( wp_unslash( $_REQUEST['id'] ) );
		}
		$ids = array_filter( array_unique( $ids ) );

		$done = 0;
		foreach ( $ids as $id ) {
			if ( 'delete' === $action ) {
				$done += self::delete( $id ) ? 1 : 0;
			} elseif ( 0 === strpos( $action, 'status_' ) ) {
				$status = substr( $action, 7 );
				$done  += self::set_status( $id, $status ) ? 1 : 0;
			}
		}

		$redirect = wp_get_referer();
		if ( ! $redirect ) {
			$redirect = admin_url( 'admin.php?page=pdo-submissions' );
		}
		$redirect = remove_query_arg( array( 'pdo_done', 'pdo_action', 'id', '_wpnonce' ), $redirect );
		$redirect = add_query_arg( 'pdo_done', $done, $redirect );

		wp_safe_redirect( $redirect );
		exit;
	}

	/**
	 * Stream a CSV export.
	 *
	 * @return void
	 */
	public function handle_export() {
		PDO_SM_Capabilities::assert_submissions();
		check_admin_referer( 'pdo_sm_export_submissions' );

		$args = array(
			'form_type' => isset( $_GET['form_type'] ) ? sanitize_key( wp_unslash( $_GET['form_type'] ) ) : '',
			'status'    => isset( $_GET['status'] ) ? sanitize_key( wp_unslash( $_GET['status'] ) ) : '',
			'search'    => isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '',
			'date_from' => isset( $_GET['date_from'] ) ? sanitize_text_field( wp_unslash( $_GET['date_from'] ) ) : '',
			'date_to'   => isset( $_GET['date_to'] ) ? sanitize_text_field( wp_unslash( $_GET['date_to'] ) ) : '',
			'per_page'  => 500,
			'page'      => 1,
		);

		$selected = isset( $_GET['ids'] ) ? array_filter( array_map( 'absint', explode( ',', sanitize_text_field( wp_unslash( $_GET['ids'] ) ) ) ) ) : array();

		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=pdo-submissions-' . gmdate( 'Y-m-d-His' ) . '.csv' );

		$out = fopen( 'php://output', 'w' );
		fputcsv( $out, array( 'ID', 'Date', 'Form', 'Status', 'Name', 'Email', 'Phone', 'Subject', 'Language', 'Source URL', 'Details' ) );

		$page = 1;
		do {
			$args['page'] = $page;
			$rows         = self::query( $args );
			foreach ( $rows as $row ) {
				if ( $selected && ! in_array( (int) $row->id, $selected, true ) ) {
					continue;
				}
				$payload = json_decode( $row->payload, true );
				$details = array();
				if ( is_array( $payload ) ) {
					foreach ( $payload as $k => $v ) {
						$details[] = $k . ': ' . ( is_array( $v ) ? implode( ', ', $v ) : $v );
					}
				}
				fputcsv(
					$out,
					array(
						$row->id,
						$row->created_at,
						$row->form_type,
						$row->status,
						$row->name,
						$row->email,
						$row->phone,
						$row->subject,
						$row->lang,
						$row->source_url,
						implode( ' | ', $details ),
					)
				);
			}
			++$page;
		} while ( count( $rows ) === (int) $args['per_page'] );

		fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
		exit;
	}
}
