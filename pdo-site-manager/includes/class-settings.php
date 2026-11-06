<?php
/**
 * Option storage, defaults and schema driven sanitisation.
 *
 * @package PDO_Site_Manager
 */

defined( 'ABSPATH' ) || exit;

/**
 * Settings controller.
 */
class PDO_SM_Settings {

	/**
	 * Runtime cache of merged option groups.
	 *
	 * @var array<string,array>
	 */
	private static $cache = array();

	/**
	 * Hooks.
	 */
	public function __construct() {
		add_action( 'admin_post_pdo_sm_save_settings', array( $this, 'handle_save' ) );
		add_action( 'admin_init', array( $this, 'register_options' ) );
	}

	/**
	 * Register options so they are autoloaded and visible to WordPress tooling.
	 *
	 * @return void
	 */
	public function register_options() {
		foreach ( PDO_SM_Schema::all() as $key => $group ) {
			register_setting(
				'pdo_sm_' . $key,
				$group['option'],
				array(
					'type'              => 'array',
					'sanitize_callback' => null,
					'show_in_rest'      => false,
				)
			);
		}
	}

	/**
	 * Option name for a group key.
	 *
	 * @param string $key Group key.
	 * @return string
	 */
	public static function option_name( $key ) {
		$group = PDO_SM_Schema::group( $key );
		return isset( $group['option'] ) ? $group['option'] : 'pdo_sm_' . sanitize_key( $key );
	}

	/**
	 * Build the default value set for a group.
	 *
	 * @param string $key Group key.
	 * @return array
	 */
	public static function defaults( $key ) {
		$defaults = array();
		foreach ( PDO_SM_Schema::fields( $key ) as $field ) {
			self::collect_field_defaults( $field, $defaults );
		}
		return $defaults;
	}

	/**
	 * Recursively collect defaults.
	 *
	 * @param array $field    Field definition.
	 * @param array $defaults Reference to the defaults array.
	 * @return void
	 */
	private static function collect_field_defaults( $field, &$defaults ) {
		if ( empty( $field['key'] ) || 'heading' === $field['type'] ) {
			return;
		}
		$defaults[ $field['key'] ] = isset( $field['default'] ) ? $field['default'] : self::empty_value( $field['type'] );

		if ( ! empty( $field['bilingual'] ) ) {
			$defaults[ $field['key'] . '_bn' ] = isset( $field['default_bn'] ) ? $field['default_bn'] : '';
		}
	}

	/**
	 * Empty value per type.
	 *
	 * @param string $type Field type.
	 * @return mixed
	 */
	private static function empty_value( $type ) {
		switch ( $type ) {
			case 'repeater':
				return array();
			case 'checkbox':
			case 'number':
			case 'image':
			case 'file':
			case 'order':
				return 0;
			default:
				return '';
		}
	}

	/**
	 * Retrieve a settings group merged with its defaults.
	 *
	 * @param string $key Group key.
	 * @return array
	 */
	public static function get_group( $key ) {
		if ( isset( self::$cache[ $key ] ) ) {
			return self::$cache[ $key ];
		}

		$defaults = self::defaults( $key );
		$stored   = get_option( self::option_name( $key ), null );

		if ( ! is_array( $stored ) ) {
			$merged = $defaults;
		} else {
			$merged = $stored;
			foreach ( $defaults as $field_key => $value ) {
				if ( ! array_key_exists( $field_key, $merged ) ) {
					$merged[ $field_key ] = $value;
				}
			}
		}

		/**
		 * Filter a resolved settings group.
		 *
		 * @param array  $merged Values.
		 * @param string $key    Group key.
		 */
		$merged = apply_filters( 'pdo_sm_get_group', $merged, $key );

		self::$cache[ $key ] = $merged;

		return $merged;
	}

	/**
	 * Read a single value.
	 *
	 * @param string $group   Group key.
	 * @param string $field   Field key.
	 * @param mixed  $default Fallback.
	 * @return mixed
	 */
	public static function get( $group, $field, $default = '' ) {
		$data = self::get_group( $group );
		return array_key_exists( $field, $data ) && '' !== $data[ $field ] ? $data[ $field ] : $default;
	}

	/**
	 * Read a bilingual value for the active language.
	 *
	 * @param string $group   Group key.
	 * @param string $field   Field key.
	 * @param mixed  $default Fallback.
	 * @return string
	 */
	public static function text( $group, $field, $default = '' ) {
		return PDO_SM_I18n::field( self::get_group( $group ), $field, $default );
	}

	/**
	 * Persist a group (used by the importer and the wizard).
	 *
	 * @param string $key    Group key.
	 * @param array  $values Raw values.
	 * @return bool True when the option now holds the requested values.
	 */
	public static function save_group( $key, $values ) {
		$clean  = self::sanitize_group( $key, $values );
		$option = self::option_name( $key );
		$old    = get_option( $option, null );

		if ( is_array( $old ) && $old === $clean ) {
			unset( self::$cache[ $key ] );
			return true;
		}

		$saved = update_option( $option, $clean );
		unset( self::$cache[ $key ] );

		if ( ! $saved ) {
			// update_option() returns false when nothing changed; verify for real.
			return get_option( $option ) === $clean;
		}

		return true;
	}

	/**
	 * Flush the runtime cache.
	 *
	 * @return void
	 */
	public static function flush_cache() {
		self::$cache = array();
	}

	/**
	 * Sanitise a whole group against its schema.
	 *
	 * @param string $key    Group key.
	 * @param array  $values Raw values.
	 * @return array
	 */
	public static function sanitize_group( $key, $values ) {
		$values  = is_array( $values ) ? $values : array();
		$current = get_option( self::option_name( $key ), array() );
		$current = is_array( $current ) ? $current : array();
		$clean   = array();

		foreach ( PDO_SM_Schema::fields( $key ) as $field ) {
			if ( empty( $field['key'] ) || 'heading' === $field['type'] ) {
				continue;
			}
			$field_key           = $field['key'];
			$clean[ $field_key ] = self::sanitize_field( $field, isset( $values[ $field_key ] ) ? $values[ $field_key ] : null );

			if ( ! empty( $field['bilingual'] ) ) {
				$bn_key           = $field_key . '_bn';
				$bn_field         = $field;
				$bn_field['key']  = $bn_key;
				$clean[ $bn_key ] = self::sanitize_field( $bn_field, isset( $values[ $bn_key ] ) ? $values[ $bn_key ] : null );
			}
		}

		// Preserve anything stored by third party extensions.
		foreach ( $current as $existing_key => $existing_value ) {
			if ( ! array_key_exists( $existing_key, $clean ) ) {
				$clean[ $existing_key ] = $existing_value;
			}
		}

		return $clean;
	}

	/**
	 * Sanitise one field value.
	 *
	 * @param array $field Field definition.
	 * @param mixed $value Raw value.
	 * @return mixed
	 */
	public static function sanitize_field( $field, $value ) {
		$type = isset( $field['type'] ) ? $field['type'] : 'text';

		switch ( $type ) {
			case 'repeater':
				return self::sanitize_repeater( $field, $value );

			case 'checkbox':
				return PDO_SM_Helpers::to_bool( $value ) ? 1 : 0;

			case 'number':
			case 'order':
				$number = is_numeric( $value ) ? $value + 0 : 0;
				if ( isset( $field['min'] ) && $number < $field['min'] ) {
					$number = $field['min'];
				}
				if ( isset( $field['max'] ) && $number > $field['max'] ) {
					$number = $field['max'];
				}
				return is_float( $number ) ? (float) $number : (int) $number;

			case 'image':
			case 'file':
				return absint( $value );

			case 'color':
				return PDO_SM_Helpers::sanitize_color( $value );

			case 'icon':
				$icon = sanitize_key( (string) $value );
				return PDO_SM_Icons::exists( $icon ) ? $icon : '';

			case 'url':
				$url = trim( (string) $value );
				if ( '' === $url ) {
					return '';
				}
				// Allow site-relative URLs and anchors.
				if ( preg_match( '#^(/|\#)#', $url ) ) {
					return esc_url_raw( $url, array( 'http', 'https', 'mailto', 'tel' ) );
				}
				return esc_url_raw( $url, array( 'http', 'https', 'mailto', 'tel' ) );

			case 'email':
				$email = sanitize_email( (string) $value );
				return is_email( $email ) ? $email : '';

			case 'select':
				$val     = sanitize_text_field( (string) $value );
				$options = isset( $field['options'] ) ? $field['options'] : array();
				if ( $options && ! array_key_exists( $val, $options ) ) {
					return isset( $field['default'] ) ? $field['default'] : '';
				}
				return $val;

			case 'richtext':
				return PDO_SM_Helpers::sanitize_rich_text( $value );

			case 'textarea':
				return PDO_SM_Helpers::sanitize_multiline( $value );

			case 'text':
			default:
				return sanitize_text_field( (string) $value );
		}
	}

	/**
	 * Sanitise a repeater value.
	 *
	 * @param array $field Field definition.
	 * @param mixed $value Raw rows.
	 * @return array
	 */
	private static function sanitize_repeater( $field, $value ) {
		if ( ! is_array( $value ) ) {
			return array();
		}
		$sub_fields = isset( $field['fields'] ) ? $field['fields'] : array();
		$sub_fields = array_merge( $sub_fields, PDO_SM_Schema::row_meta_fields() );

		$rows  = array();
		$index = 0;

		foreach ( $value as $raw_row ) {
			if ( ! is_array( $raw_row ) ) {
				continue;
			}
			if ( ! empty( $raw_row['__template'] ) ) {
				continue;
			}
			$row = array();
			foreach ( $sub_fields as $sub ) {
				if ( empty( $sub['key'] ) ) {
					continue;
				}
				$sub_key         = $sub['key'];
				$row[ $sub_key ] = self::sanitize_field( $sub, isset( $raw_row[ $sub_key ] ) ? $raw_row[ $sub_key ] : null );
				if ( ! empty( $sub['bilingual'] ) ) {
					$bn              = $sub_key . '_bn';
					$row[ $bn ]      = self::sanitize_field( $sub, isset( $raw_row[ $bn ] ) ? $raw_row[ $bn ] : null );
				}
			}
			$row['order'] = $index;
			$rows[]       = $row;
			++$index;
		}

		return $rows;
	}

	/**
	 * Handle the admin-post save request for any settings group.
	 *
	 * @return void
	 */
	public function handle_save() {
		PDO_SM_Capabilities::assert_manage();

		$group = isset( $_POST['pdo_group'] ) ? sanitize_key( wp_unslash( $_POST['pdo_group'] ) ) : '';
		$tab   = isset( $_POST['pdo_tab'] ) ? sanitize_key( wp_unslash( $_POST['pdo_tab'] ) ) : '';

		if ( ! $group || ! PDO_SM_Schema::group( $group ) ) {
			wp_die( esc_html__( 'Unknown settings group.', 'pdo-site-manager' ), 400 );
		}

		check_admin_referer( 'pdo_sm_save_' . $group );

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitised field by field against the schema below.
		$raw = isset( $_POST['pdo'] ) ? wp_unslash( $_POST['pdo'] ) : array();

		// Start from the current values so a tab only overwrites its own fields.
		$current = get_option( self::option_name( $group ), array() );
		$current = is_array( $current ) ? $current : array();
		$merged  = array_merge( $current, is_array( $raw ) ? $raw : array() );

		// Unchecked checkboxes and emptied repeaters are absent from $_POST – rebuild them per submitted tab.
		$submitted_keys = isset( $_POST['pdo_fields'] ) ? array_map( 'sanitize_key', (array) wp_unslash( $_POST['pdo_fields'] ) ) : array();
		foreach ( $submitted_keys as $submitted_key ) {
			if ( ! isset( $raw[ $submitted_key ] ) ) {
				$merged[ $submitted_key ] = null;
			}
		}

		$ok = self::save_group( $group, $merged );

		$redirect = wp_get_referer();
		if ( ! $redirect ) {
			$redirect = admin_url( 'admin.php?page=pdo-' . $group );
		}
		$redirect = remove_query_arg( array( 'pdo_saved', 'pdo_error' ), $redirect );
		$redirect = add_query_arg( $ok ? array( 'pdo_saved' => 1 ) : array( 'pdo_error' => 1 ), $redirect );
		if ( $tab ) {
			$redirect = add_query_arg( 'tab', $tab, $redirect );
		}

		wp_safe_redirect( $redirect );
		exit;
	}
}
