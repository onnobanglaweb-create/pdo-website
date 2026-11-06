<?php
/**
 * Bilingual (English / Bengali) content engine.
 *
 * The plugin stores a Bengali variant for every editable field
 * (`field` + `field_bn`). The active language is resolved from the
 * `lang` query argument, falling back to a cookie and finally to the
 * configured default language.
 *
 * @package PDO_Site_Manager
 */

defined( 'ABSPATH' ) || exit;

/**
 * Language runtime.
 */
class PDO_SM_I18n {

	/**
	 * Cookie used to remember the visitor's choice.
	 */
	const COOKIE = 'pdo_lang';

	/**
	 * Resolved language for this request.
	 *
	 * @var string|null
	 */
	private static $current = null;

	/**
	 * Hooks.
	 */
	public function __construct() {
		add_action( 'wp_head', array( $this, 'print_hreflang' ), 2 );
		add_filter( 'language_attributes', array( $this, 'language_attributes' ) );
		add_filter( 'body_class', array( $this, 'body_class' ) );

		// Bengali labels for navigation menu items.
		add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'menu_item_fields' ), 10, 2 );
		add_action( 'wp_update_nav_menu_item', array( $this, 'save_menu_item_fields' ), 10, 2 );
		add_filter( 'wp_setup_nav_menu_item', array( $this, 'apply_menu_item_translation' ) );
	}

	/*
	 * NOTE: `lang` is deliberately NOT registered as a public query var.
	 * Registering it would make `/?lang=bn` a non-empty query, which stops
	 * WordPress from swapping in the static front page (see WP_Query::get_posts()).
	 * The language is read straight from $_GET / the cookie in current().
	 */

	/**
	 * Supported languages.
	 *
	 * @return array<string,array{label:string,native:string,short:string,locale:string,dir:string}>
	 */
	public static function languages() {
		return array(
			'en' => array(
				'label'  => __( 'English', 'pdo-site-manager' ),
				'native' => 'English',
				'short'  => 'EN',
				'locale' => 'en_US',
				'dir'    => 'ltr',
			),
			'bn' => array(
				'label'  => __( 'Bengali', 'pdo-site-manager' ),
				'native' => 'বাংলা',
				'short'  => 'বাং',
				'locale' => 'bn_BD',
				'dir'    => 'ltr',
			),
		);
	}

	/**
	 * Default site language from the organisation settings.
	 *
	 * @return string
	 */
	public static function default_language() {
		$org  = PDO_SM_Settings::get_group( 'org' );
		$lang = isset( $org['default_language'] ) ? $org['default_language'] : 'en';
		return in_array( $lang, array( 'en', 'bn' ), true ) ? $lang : 'en';
	}

	/**
	 * Whether the bilingual switcher is enabled.
	 *
	 * @return bool
	 */
	public static function enabled() {
		$org = PDO_SM_Settings::get_group( 'org' );
		return ! isset( $org['bilingual_enabled'] ) || PDO_SM_Helpers::to_bool( $org['bilingual_enabled'] );
	}

	/**
	 * Resolve the current language.
	 *
	 * @return string
	 */
	public static function current() {
		if ( null !== self::$current ) {
			return self::$current;
		}

		$lang = '';

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only language preference.
		if ( isset( $_GET['lang'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$lang = sanitize_key( wp_unslash( $_GET['lang'] ) );
		} elseif ( isset( $_COOKIE[ self::COOKIE ] ) ) {
			$lang = sanitize_key( wp_unslash( $_COOKIE[ self::COOKIE ] ) );
		}

		if ( ! array_key_exists( $lang, self::languages() ) || ! self::enabled() ) {
			$lang = self::default_language();
		}

		self::$current = $lang;

		return self::$current;
	}

	/**
	 * Force a language (used by unit tests / previews).
	 *
	 * @param string $lang Language code.
	 * @return void
	 */
	public static function set_current( $lang ) {
		self::$current = array_key_exists( $lang, self::languages() ) ? $lang : 'en';
	}

	/**
	 * Is Bengali currently active?
	 *
	 * @return bool
	 */
	public static function is_bn() {
		return 'bn' === self::current();
	}

	/**
	 * Pick the right value out of an array for the current language.
	 *
	 * Falls back to the English value when the Bengali translation is missing.
	 *
	 * @param array  $data Source data.
	 * @param string $key  Base key (the Bengali key is `{key}_bn`).
	 * @param string $default Fallback.
	 * @return string
	 */
	public static function field( $data, $key, $default = '' ) {
		if ( ! is_array( $data ) ) {
			return $default;
		}
		if ( self::is_bn() ) {
			$bn = isset( $data[ $key . '_bn' ] ) ? $data[ $key . '_bn' ] : '';
			if ( is_string( $bn ) ) {
				$bn = trim( $bn );
			}
			if ( '' !== $bn && null !== $bn ) {
				return $bn;
			}
		}
		return isset( $data[ $key ] ) && '' !== $data[ $key ] ? $data[ $key ] : $default;
	}

	/**
	 * Pick a translated post meta value.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $key     Meta key (without `_pdo_` prefix).
	 * @param string $default Fallback.
	 * @return string
	 */
	public static function meta( $post_id, $key, $default = '' ) {
		if ( self::is_bn() ) {
			$bn = get_post_meta( $post_id, '_pdo_' . $key . '_bn', true );
			if ( '' !== $bn && null !== $bn ) {
				return $bn;
			}
		}
		$value = get_post_meta( $post_id, '_pdo_' . $key, true );
		return ( '' !== $value && null !== $value ) ? $value : $default;
	}

	/**
	 * Translated post title.
	 *
	 * @param WP_Post|int $post Post.
	 * @return string
	 */
	public static function post_title( $post ) {
		$post = get_post( $post );
		if ( ! $post ) {
			return '';
		}
		if ( self::is_bn() ) {
			$bn = get_post_meta( $post->ID, '_pdo_title_bn', true );
			if ( '' !== $bn ) {
				return $bn;
			}
		}
		return get_the_title( $post );
	}

	/**
	 * Translated post content.
	 *
	 * @param WP_Post|int $post Post.
	 * @return string
	 */
	public static function post_content( $post ) {
		$post = get_post( $post );
		if ( ! $post ) {
			return '';
		}
		if ( self::is_bn() ) {
			$bn = get_post_meta( $post->ID, '_pdo_content_bn', true );
			if ( '' !== trim( (string) $bn ) ) {
				return apply_filters( 'the_content', $bn );
			}
		}
		return apply_filters( 'the_content', $post->post_content );
	}

	/**
	 * Translated post excerpt.
	 *
	 * @param WP_Post|int $post Post.
	 * @return string
	 */
	public static function post_excerpt( $post ) {
		$post = get_post( $post );
		if ( ! $post ) {
			return '';
		}
		if ( self::is_bn() ) {
			$bn = get_post_meta( $post->ID, '_pdo_excerpt_bn', true );
			if ( '' !== trim( (string) $bn ) ) {
				return $bn;
			}
		}
		return has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( wp_strip_all_tags( $post->post_content ), 28 );
	}

	/**
	 * Translate a theme/plugin UI string using the editable string table.
	 *
	 * @param string $key      String key.
	 * @param string $fallback English fallback.
	 * @return string
	 */
	public static function string( $key, $fallback = '' ) {
		$strings = PDO_SM_Settings::get_group( 'i18n' );
		$table   = isset( $strings['strings'] ) && is_array( $strings['strings'] ) ? $strings['strings'] : array();

		foreach ( $table as $row ) {
			if ( ! isset( $row['key'] ) || $row['key'] !== $key ) {
				continue;
			}
			if ( self::is_bn() && ! empty( $row['bn'] ) ) {
				return $row['bn'];
			}
			if ( ! empty( $row['en'] ) ) {
				return $row['en'];
			}
		}

		return $fallback;
	}

	/**
	 * Build the URL of the current page in another language.
	 *
	 * @param string $lang Target language.
	 * @return string
	 */
	public static function switch_url( $lang ) {
		$lang = array_key_exists( $lang, self::languages() ) ? $lang : 'en';

		$scheme  = is_ssl() ? 'https://' : 'http://';
		$host    = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : wp_parse_url( home_url(), PHP_URL_HOST );
		$request = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/';
		$current = $scheme . $host . $request;

		return add_query_arg( 'lang', $lang, remove_query_arg( 'lang', $current ) );
	}

	/**
	 * Output hreflang + canonical language metadata.
	 *
	 * @return void
	 */
	public function print_hreflang() {
		if ( ! self::enabled() || is_admin() ) {
			return;
		}
		foreach ( array_keys( self::languages() ) as $code ) {
			printf(
				'<link rel="alternate" hreflang="%1$s" href="%2$s" />' . "\n",
				esc_attr( $code ),
				esc_url( self::switch_url( $code ) )
			);
		}
		printf(
			'<link rel="alternate" hreflang="x-default" href="%s" />' . "\n",
			esc_url( self::switch_url( self::default_language() ) )
		);
	}

	/**
	 * Adjust <html lang="">.
	 *
	 * @param string $output Attributes.
	 * @return string
	 */
	public function language_attributes( $output ) {
		if ( is_admin() || ! self::enabled() ) {
			return $output;
		}
		$code   = self::current();
		$output = preg_replace( '/lang="[^"]*"/', 'lang="' . esc_attr( 'bn' === $code ? 'bn-BD' : 'en' ) . '"', $output );
		return $output;
	}

	/**
	 * Add a language body class.
	 *
	 * @param array $classes Classes.
	 * @return array
	 */
	public function body_class( $classes ) {
		$classes[] = 'pdo-lang-' . self::current();
		return $classes;
	}

	/**
	 * Bengali label field inside Appearance → Menus.
	 *
	 * @param int    $item_id Menu item ID.
	 * @param object $item    Menu item.
	 * @return void
	 */
	public function menu_item_fields( $item_id, $item ) {
		$value = get_post_meta( $item_id, '_pdo_label_bn', true );
		$icon  = get_post_meta( $item_id, '_pdo_menu_icon', true );
		?>
		<p class="field-pdo-label-bn description description-wide">
			<label for="pdo-label-bn-<?php echo esc_attr( $item_id ); ?>">
				<?php esc_html_e( 'Bengali label (বাংলা)', 'pdo-site-manager' ); ?><br />
				<input type="text" id="pdo-label-bn-<?php echo esc_attr( $item_id ); ?>" class="widefat" name="pdo_label_bn[<?php echo esc_attr( $item_id ); ?>]" value="<?php echo esc_attr( $value ); ?>" />
			</label>
		</p>
		<p class="field-pdo-menu-icon description description-wide">
			<label for="pdo-menu-icon-<?php echo esc_attr( $item_id ); ?>">
				<?php esc_html_e( 'Menu icon (optional)', 'pdo-site-manager' ); ?><br />
				<select id="pdo-menu-icon-<?php echo esc_attr( $item_id ); ?>" class="widefat" name="pdo_menu_icon[<?php echo esc_attr( $item_id ); ?>]">
					<option value=""><?php esc_html_e( '— none —', 'pdo-site-manager' ); ?></option>
					<?php foreach ( PDO_SM_Icons::all() as $name => $icon_data ) : ?>
						<option value="<?php echo esc_attr( $name ); ?>" <?php selected( $icon, $name ); ?>><?php echo esc_html( $icon_data['label'] ); ?></option>
					<?php endforeach; ?>
				</select>
			</label>
		</p>
		<?php
	}

	/**
	 * Persist the menu item fields.
	 *
	 * @param int $menu_id      Menu ID.
	 * @param int $menu_item_id Item ID.
	 * @return void
	 */
	public function save_menu_item_fields( $menu_id, $menu_item_id ) {
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- core verifies the nav-menu nonce before firing this hook.
		if ( isset( $_POST['pdo_label_bn'][ $menu_item_id ] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			$label = sanitize_text_field( wp_unslash( $_POST['pdo_label_bn'][ $menu_item_id ] ) );
			if ( '' === $label ) {
				delete_post_meta( $menu_item_id, '_pdo_label_bn' );
			} else {
				update_post_meta( $menu_item_id, '_pdo_label_bn', $label );
			}
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['pdo_menu_icon'][ $menu_item_id ] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			$icon = sanitize_key( wp_unslash( $_POST['pdo_menu_icon'][ $menu_item_id ] ) );
			if ( '' === $icon || ! PDO_SM_Icons::exists( $icon ) ) {
				delete_post_meta( $menu_item_id, '_pdo_menu_icon' );
			} else {
				update_post_meta( $menu_item_id, '_pdo_menu_icon', $icon );
			}
		}
	}

	/**
	 * Swap the menu item title when Bengali is active and expose the icon.
	 *
	 * @param object $item Menu item.
	 * @return object
	 */
	public function apply_menu_item_translation( $item ) {
		if ( ! isset( $item->ID ) ) {
			return $item;
		}
		$item->pdo_icon = get_post_meta( $item->ID, '_pdo_menu_icon', true );
		if ( ! is_admin() && self::is_bn() ) {
			$bn = get_post_meta( $item->ID, '_pdo_label_bn', true );
			if ( '' !== $bn ) {
				$item->title = $bn;
			}
		}
		return $item;
	}
}

if ( ! function_exists( 'pdo_lang' ) ) {
	/**
	 * Shortcut for the current language.
	 *
	 * @return string
	 */
	function pdo_lang() {
		return PDO_SM_I18n::current();
	}
}

if ( ! function_exists( 'pdo_t' ) ) {
	/**
	 * Shortcut for a translated UI string.
	 *
	 * @param string $key      Key.
	 * @param string $fallback Fallback.
	 * @return string
	 */
	function pdo_t( $key, $fallback = '' ) {
		return PDO_SM_I18n::string( $key, $fallback );
	}
}
