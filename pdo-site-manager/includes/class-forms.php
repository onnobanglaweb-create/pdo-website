<?php
/**
 * Front-end form processing.
 *
 * Handles the contact, volunteer and partnership forms with server side
 * validation, sanitising, nonce + honeypot + time-trap spam protection,
 * rate limiting, database storage and honest email reporting.
 *
 * @package PDO_Site_Manager
 */

defined( 'ABSPATH' ) || exit;

/**
 * Forms controller.
 */
class PDO_SM_Forms {

	/**
	 * Transient prefix used for the Post/Redirect/Get result payload.
	 */
	const RESULT_PREFIX = 'pdo_sm_result_';

	/**
	 * Hooks.
	 */
	public function __construct() {
		add_action( 'admin_post_nopriv_pdo_sm_submit', array( $this, 'handle' ) );
		add_action( 'admin_post_pdo_sm_submit', array( $this, 'handle' ) );
	}

	/**
	 * Configuration for a form type.
	 *
	 * @param string $type Form type.
	 * @return array|null
	 */
	public static function config( $type ) {
		switch ( $type ) {
			case 'contact':
				$group = PDO_SM_Settings::get_group( 'contact' );
				return array(
					'fields'  => isset( $group['form_fields'] ) ? PDO_SM_Helpers::visible_rows( $group['form_fields'] ) : array(),
					'success' => PDO_SM_I18n::field( $group, 'form_success', __( 'Thank you — your message has been received.', 'pdo-site-manager' ) ),
					'submit'  => PDO_SM_I18n::field( $group, 'form_submit', __( 'Send Message', 'pdo-site-manager' ) ),
					'title'   => PDO_SM_I18n::field( $group, 'form_title', '' ),
					'label'   => __( 'Contact message', 'pdo-site-manager' ),
				);

			case 'volunteer':
				$group = PDO_SM_Settings::get_group( 'involved' );
				return array(
					'fields'  => isset( $group['volunteer_fields'] ) ? PDO_SM_Helpers::visible_rows( $group['volunteer_fields'] ) : array(),
					'success' => PDO_SM_I18n::field( $group, 'volunteer_success', __( 'Thank you — your application has been received.', 'pdo-site-manager' ) ),
					'submit'  => PDO_SM_I18n::field( $group, 'volunteer_submit', __( 'Submit', 'pdo-site-manager' ) ),
					'title'   => PDO_SM_I18n::field( $group, 'volunteer_title', '' ),
					'label'   => __( 'Volunteer application', 'pdo-site-manager' ),
				);

			case 'partner':
				$group = PDO_SM_Settings::get_group( 'involved' );
				return array(
					'fields'  => isset( $group['partner_fields'] ) ? PDO_SM_Helpers::visible_rows( $group['partner_fields'] ) : array(),
					'success' => PDO_SM_I18n::field( $group, 'partner_success', __( 'Thank you — your inquiry has been received.', 'pdo-site-manager' ) ),
					'submit'  => PDO_SM_I18n::field( $group, 'partner_submit', __( 'Submit', 'pdo-site-manager' ) ),
					'title'   => PDO_SM_I18n::field( $group, 'partner_title', '' ),
					'label'   => __( 'Partnership inquiry', 'pdo-site-manager' ),
				);
		}

		return null;
	}

	/**
	 * Configuration for a form type, after filtering.
	 *
	 * @param string $type Form type.
	 * @return array|null
	 */
	public static function filtered_config( $type ) {
		$config = self::config( $type );
		if ( ! $config ) {
			return null;
		}

		/**
		 * Filter the field rows of a form type.
		 *
		 * @param array  $fields Field rows as configured in the dashboard.
		 * @param string $type   Form type: contact, volunteer or partner.
		 */
		$config['fields'] = (array) apply_filters( 'pdo_sm_form_fields', $config['fields'], $type );

		return $config;
	}

	/**
	 * Resolve the input name for a configured field row.
	 *
	 * @param array $field Field row.
	 * @param int   $index Row index.
	 * @return string
	 */
	public static function field_name( $field, $index = 0 ) {
		if ( ! empty( $field['name'] ) ) {
			return sanitize_key( $field['name'] );
		}
		if ( ! empty( $field['label'] ) ) {
			return PDO_SM_Helpers::key_from_label( $field['label'] );
		}
		return 'field_' . (int) $index;
	}

	/**
	 * Process a submission.
	 *
	 * @return void
	 */
	public function handle() {
		$type   = isset( $_POST['pdo_form'] ) ? sanitize_key( wp_unslash( $_POST['pdo_form'] ) ) : '';
		$config = self::filtered_config( $type );

		$redirect = isset( $_POST['_pdo_redirect'] ) ? esc_url_raw( wp_unslash( $_POST['_pdo_redirect'] ) ) : home_url( '/' );
		$redirect = wp_validate_redirect( $redirect, home_url( '/' ) );

		if ( ! $config ) {
			$this->finish( $redirect, $type, false, __( 'Unknown form.', 'pdo-site-manager' ), array(), array() );
		}

		// 1. Nonce.
		$nonce = isset( $_POST['_pdo_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['_pdo_nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'pdo_sm_form_' . $type ) ) {
			$this->finish( $redirect, $type, false, pdo_t( 'spam_error', __( 'Security check failed. Please reload the page and try again.', 'pdo-site-manager' ) ), array(), array() );
		}

		// 2. Honeypot.
		$honeypot = isset( $_POST['pdo_website'] ) ? trim( (string) wp_unslash( $_POST['pdo_website'] ) ) : '';
		if ( '' !== $honeypot ) {
			$this->finish( $redirect, $type, false, pdo_t( 'spam_error', __( 'Your submission was blocked by our spam filter.', 'pdo-site-manager' ) ), array(), array() );
		}

		// 3. Time trap – a human needs at least a few seconds.
		$started = isset( $_POST['pdo_ts'] ) ? absint( wp_unslash( $_POST['pdo_ts'] ) ) : 0;
		if ( $started && ( time() - $started ) < 3 ) {
			$this->finish( $redirect, $type, false, pdo_t( 'spam_error', __( 'Your submission was blocked by our spam filter. Please try again.', 'pdo-site-manager' ) ), array(), array() );
		}

		// 4. Rate limiting – max 5 submissions per 10 minutes per visitor.
		$bucket_key = 'pdo_rl_' . PDO_SM_Helpers::visitor_hash();
		$bucket     = (int) get_transient( $bucket_key );
		if ( $bucket >= 5 ) {
			$this->finish( $redirect, $type, false, pdo_t( 'rate_error', __( 'Too many submissions from this device. Please try again later.', 'pdo-site-manager' ) ), array(), array() );
		}

		// 5. Validate + sanitise every configured field.
		$values = array();
		$errors = array();
		$pretty = array();

		foreach ( $config['fields'] as $index => $field ) {
			$name  = self::field_name( $field, $index );
			$label = PDO_SM_I18n::field( $field, 'label', $name );
			$type_ = isset( $field['type'] ) ? $field['type'] : 'text';
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above.
			$raw = isset( $_POST['pdo_field'][ $name ] ) ? wp_unslash( $_POST['pdo_field'][ $name ] ) : '';

			switch ( $type_ ) {
				case 'email':
					$value = sanitize_email( (string) $raw );
					if ( '' !== $value && ! is_email( $value ) ) {
						$errors[ $name ] = pdo_t( 'invalid_email', __( 'Please enter a valid email address.', 'pdo-site-manager' ) );
						$value           = '';
					}
					break;
				case 'url':
					$value = esc_url_raw( (string) $raw );
					break;
				case 'number':
					$value = is_numeric( $raw ) ? $raw + 0 : '';
					break;
				case 'tel':
				case 'phone':
					$value = sanitize_text_field( (string) $raw );
					if ( '' !== $value && ! preg_match( '/^[+0-9][0-9\s\-()]{4,24}$/', $value ) ) {
						$errors[ $name ] = pdo_t( 'invalid_phone', __( 'Please enter a valid phone number.', 'pdo-site-manager' ) );
						$value           = '';
					}
					break;
				case 'date':
					$value = sanitize_text_field( (string) $raw );
					if ( '' !== $value ) {
						$parsed = DateTimeImmutable::createFromFormat( 'Y-m-d', $value );
						if ( ! $parsed || $parsed->format( 'Y-m-d' ) !== $value ) {
							$errors[ $name ] = pdo_t( 'invalid_date', __( 'Please enter a valid date.', 'pdo-site-manager' ) );
							$value           = '';
						}
					}
					break;
				case 'textarea':
					$value = sanitize_textarea_field( (string) $raw );
					break;
				case 'checkbox':
					$value = PDO_SM_Helpers::to_bool( $raw ) ? '1' : '';
					break;
				case 'select':
				case 'radio':
					$value   = sanitize_text_field( (string) $raw );
					$choices = self::choices( $field );
					if ( '' !== $value && $choices && ! in_array( $value, $choices, true ) ) {
						$errors[ $name ] = __( 'Please choose one of the available options.', 'pdo-site-manager' );
						$value           = '';
					}
					break;
				default:
					$value = sanitize_text_field( (string) $raw );
					break;
			}

			if ( ! empty( $field['required'] ) && ( '' === $value || null === $value ) && ! isset( $errors[ $name ] ) ) {
				$errors[ $name ] = pdo_t( 'required_field', __( 'This field is required.', 'pdo-site-manager' ) );
			}

			$values[ $name ]  = $value;
			$pretty[ $label ] = $value;
		}

		// Optional project context from the Adopt-a-Project cards.
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce verified above.
		$project = isset( $_POST['pdo_project'] ) ? sanitize_text_field( wp_unslash( $_POST['pdo_project'] ) ) : '';
		if ( $project ) {
			$values['project']                                             = $project;
			$pretty[ __( 'Selected project', 'pdo-site-manager' ) ]        = $project;
		}

		if ( $errors ) {
			$this->finish( $redirect, $type, false, pdo_t( 'form_error', __( 'Please review the highlighted fields.', 'pdo-site-manager' ) ), $errors, $values );
		}

		// 6. Store.
		$subject = '';
		foreach ( array( 'subject', 'focus_area', 'area_of_action' ) as $subject_key ) {
			if ( ! empty( $values[ $subject_key ] ) ) {
				$subject = $values[ $subject_key ];
				break;
			}
		}
		if ( ! $subject ) {
			$subject = $config['label'];
		}
		if ( $project ) {
			$subject = $project . ' — ' . $subject;
		}

		$name = '';
		foreach ( array( 'full_name', 'name', 'contact_person', 'organization' ) as $name_key ) {
			if ( ! empty( $values[ $name_key ] ) ) {
				$name = $values[ $name_key ];
				break;
			}
		}

		$row = array(
			'form_type'  => $type,
			'subject'    => $subject,
			'name'       => $name,
			'email'      => isset( $values['email'] ) ? $values['email'] : '',
			'phone'      => isset( $values['phone'] ) ? $values['phone'] : '',
			'payload'    => $pretty,
			'ip_hash'    => PDO_SM_Helpers::visitor_hash(),
			'source_url' => $redirect,
			'lang'       => PDO_SM_I18n::current(),
		);

		/**
		 * Filter the submission row just before it is written to the database.
		 *
		 * @param array  $row    Column => value map.
		 * @param string $type   Form type.
		 * @param array  $values Sanitised field name => value map.
		 */
		$row = (array) apply_filters( 'pdo_sm_submission_data', $row, $type, $values );

		$insert = PDO_SM_Submissions::insert( $row );

		if ( is_wp_error( $insert ) ) {
			$this->finish( $redirect, $type, false, __( 'We could not save your message because of a server error. Please try again or email us directly.', 'pdo-site-manager' ), array(), $values );
		}

		set_transient( $bucket_key, $bucket + 1, 10 * MINUTE_IN_SECONDS );

		/**
		 * Fires after a submission has been stored.
		 *
		 * @param int    $insert Submission ID.
		 * @param string $type   Form type.
		 * @param array  $pretty Label => value map.
		 */
		do_action( 'pdo_sm_submission_stored', $insert, $type, $pretty );

		// 7. Notify – and report the real result.
		$mail_sent = $this->notify( $type, $config, $pretty, isset( $values['email'] ) ? $values['email'] : '' );

		$message = $config['success'];
		if ( ! $mail_sent ) {
			$message .= ' ' . __( '(Your message is stored safely in the PDO dashboard, but the email notification could not be delivered.)', 'pdo-site-manager' );
		}

		$this->finish( $redirect, $type, true, $message, array(), array() );
	}

	/**
	 * Extract the choices of a select/radio field.
	 *
	 * @param array $field Field row.
	 * @return array<string>
	 */
	public static function choices( $field ) {
		$raw = PDO_SM_I18n::field( $field, 'options', '' );
		if ( '' === trim( (string) $raw ) ) {
			return array();
		}
		$lines = preg_split( '/\r\n|\r|\n/', (string) $raw );
		return array_values( array_filter( array_map( 'trim', $lines ), 'strlen' ) );
	}

	/**
	 * Send the notification email.
	 *
	 * @param string $type     Form type.
	 * @param array  $config   Form config.
	 * @param array  $pretty   Label => value pairs.
	 * @param string $reply_to Reply-to address.
	 * @return bool
	 */
	private function notify( $type, $config, $pretty, $reply_to ) {
		$org       = PDO_SM_Settings::get_group( 'org' );
		$recipient = ! empty( $org['notification_email'] ) ? $org['notification_email'] : ( ! empty( $org['email'] ) ? $org['email'] : get_option( 'admin_email' ) );

		if ( ! is_email( $recipient ) ) {
			return false;
		}

		$lines = array();
		foreach ( $pretty as $label => $value ) {
			$lines[] = $label . ': ' . ( is_array( $value ) ? implode( ', ', $value ) : $value );
		}

		$body = implode( "\n", $lines ) . "\n\n" . sprintf(
			/* translators: %s: admin URL. */
			__( 'Manage this submission: %s', 'pdo-site-manager' ),
			admin_url( 'admin.php?page=pdo-submissions' )
		);

		$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
		if ( $reply_to && is_email( $reply_to ) ) {
			$headers[] = 'Reply-To: ' . $reply_to;
		}

		$subject = sprintf(
			/* translators: 1: site name, 2: form label. */
			__( '[%1$s] New %2$s', 'pdo-site-manager' ),
			wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
			$config['label']
		);

		unset( $type );

		return (bool) wp_mail( $recipient, $subject, $body, $headers );
	}

	/**
	 * Store the result and redirect back to the form.
	 *
	 * @param string $redirect Redirect URL.
	 * @param string $type     Form type.
	 * @param bool   $success  Success flag.
	 * @param string $message  Message.
	 * @param array  $errors   Field errors.
	 * @param array  $values   Submitted values (for repopulating).
	 * @return void
	 */
	private function finish( $redirect, $type, $success, $message, $errors = array(), $values = array() ) {
		$token = wp_generate_password( 16, false, false );
		set_transient(
			self::RESULT_PREFIX . $token,
			array(
				'form'    => $type,
				'success' => (bool) $success,
				'message' => $message,
				'errors'  => $errors,
				'values'  => $values,
			),
			5 * MINUTE_IN_SECONDS
		);

		$redirect = remove_query_arg( array( 'pdo_result', 'pdo_form' ), $redirect );
		$redirect = add_query_arg(
			array(
				'pdo_result' => $token,
				'pdo_form'   => $type,
			),
			$redirect
		);
		$redirect .= '#pdo-form-' . $type;

		wp_safe_redirect( $redirect );
		exit;
	}

	/**
	 * Read the result of the previous submission (if any).
	 *
	 * @param string $type Form type.
	 * @return array|null
	 */
	public static function result( $type ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only, token based.
		if ( empty( $_GET['pdo_result'] ) ) {
			return null;
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$token  = sanitize_text_field( wp_unslash( $_GET['pdo_result'] ) );
		$result = get_transient( self::RESULT_PREFIX . $token );
		if ( ! is_array( $result ) || ( isset( $result['form'] ) && $result['form'] !== $type ) ) {
			return null;
		}
		return $result;
	}
}
