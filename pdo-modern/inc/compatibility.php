<?php
/**
 * Graceful degradation when the PDO Website Manager plugin is not active.
 *
 * Every plugin-provided template function is mirrored here with a safe
 * fallback so the theme never fatals on a plain WordPress install.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

/**
 * Is the companion plugin active?
 *
 * @return bool
 */
function pdo_modern_has_plugin() {
	return function_exists( 'pdo_settings' ) && class_exists( 'PDO_SM_Settings' );
}

/* -------------------------------------------------------------------------
 * Content API fallbacks.
 * ---------------------------------------------------------------------- */

if ( ! function_exists( 'pdo_settings' ) ) {
	/**
	 * Fallback: no settings available.
	 *
	 * @param string $group Group key.
	 * @return array
	 */
	function pdo_settings( $group ) {
		unset( $group );
		return array();
	}
}

if ( ! function_exists( 'pdo_setting' ) ) {
	/**
	 * Fallback: return the default.
	 *
	 * @param string $group   Group key.
	 * @param string $field   Field key.
	 * @param mixed  $default Default.
	 * @return mixed
	 */
	function pdo_setting( $group, $field, $default = '' ) {
		unset( $group, $field );
		return $default;
	}
}

if ( ! function_exists( 'pdo_text' ) ) {
	/**
	 * Fallback: return the default.
	 *
	 * @param string $group   Group key.
	 * @param string $field   Field key.
	 * @param mixed  $default Default.
	 * @return mixed
	 */
	function pdo_text( $group, $field, $default = '' ) {
		unset( $group, $field );
		return $default;
	}
}

if ( ! function_exists( 'pdo_rows' ) ) {
	/**
	 * Fallback: no repeater rows.
	 *
	 * @param string $group Group key.
	 * @param string $field Field key.
	 * @param int    $limit Limit.
	 * @return array
	 */
	function pdo_rows( $group, $field, $limit = 0 ) {
		unset( $group, $field, $limit );
		return array();
	}
}

if ( ! function_exists( 'pdo_row_text' ) ) {
	/**
	 * Fallback: plain array access.
	 *
	 * @param array  $row     Row.
	 * @param string $field   Key.
	 * @param string $default Default.
	 * @return string
	 */
	function pdo_row_text( $row, $field, $default = '' ) {
		return isset( $row[ $field ] ) && '' !== $row[ $field ] ? (string) $row[ $field ] : $default;
	}
}

if ( ! function_exists( 'pdo_section_enabled' ) ) {
	/**
	 * Fallback: everything enabled.
	 *
	 * @param string $group  Group.
	 * @param string $prefix Prefix.
	 * @return bool
	 */
	function pdo_section_enabled( $group, $prefix ) {
		unset( $group, $prefix );
		return true;
	}
}

if ( ! function_exists( 'pdo_org' ) ) {
	/**
	 * Fallback: use the WordPress site identity.
	 *
	 * @param string $field   Field key.
	 * @param mixed  $default Default.
	 * @return mixed
	 */
	function pdo_org( $field = '', $default = '' ) {
		switch ( $field ) {
			case '':
				return array();
			case 'name':
				return get_bloginfo( 'name' );
			case 'tagline':
				return get_bloginfo( 'description' );
			case 'email':
				return get_option( 'admin_email' );
		}
		return $default;
	}
}

if ( ! function_exists( 'pdo_meta' ) ) {
	/**
	 * Fallback: raw post meta.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $key     Key.
	 * @param mixed  $default Default.
	 * @return mixed
	 */
	function pdo_meta( $post_id, $key, $default = '' ) {
		$value = get_post_meta( $post_id, '_pdo_' . $key, true );
		return ( '' === $value || array() === $value ) ? $default : $value;
	}
}

if ( ! function_exists( 'pdo_raw_meta' ) ) {
	/**
	 * Fallback: raw post meta.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $key     Key.
	 * @param mixed  $default Default.
	 * @return mixed
	 */
	function pdo_raw_meta( $post_id, $key, $default = '' ) {
		return pdo_meta( $post_id, $key, $default );
	}
}

if ( ! function_exists( 'pdo_meta_rows' ) ) {
	/**
	 * Fallback: repeater meta.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $key     Key.
	 * @return array
	 */
	function pdo_meta_rows( $post_id, $key ) {
		$rows = get_post_meta( $post_id, '_pdo_' . $key, true );
		return is_array( $rows ) ? $rows : array();
	}
}

if ( ! function_exists( 'pdo_get_posts' ) ) {
	/**
	 * Fallback: simple query.
	 *
	 * @param string $post_type Post type.
	 * @param array  $args      Args.
	 * @return WP_Post[]
	 */
	function pdo_get_posts( $post_type, $args = array() ) {
		if ( ! post_type_exists( $post_type ) ) {
			return array();
		}
		$query = new WP_Query(
			wp_parse_args(
				$args,
				array(
					'post_type'      => $post_type,
					'post_status'    => 'publish',
					'posts_per_page' => 12,
					'no_found_rows'  => true,
				)
			)
		);
		return $query->posts;
	}
}

if ( ! function_exists( 'pdo_sections' ) ) {
	/**
	 * Fallback: no dynamic sections.
	 *
	 * @param string $location  Location.
	 * @param string $placement Placement.
	 * @return array
	 */
	function pdo_sections( $location, $placement = 'after' ) {
		unset( $location, $placement );
		return array();
	}
}

if ( ! function_exists( 'pdo_document_file' ) ) {
	/**
	 * Fallback: no attached file.
	 *
	 * @param int $post_id Post ID.
	 * @return array
	 */
	function pdo_document_file( $post_id ) {
		unset( $post_id );
		return array();
	}
}

if ( ! function_exists( 'pdo_form_config' ) ) {
	/**
	 * Fallback: no form configuration.
	 *
	 * @param string $type Type.
	 * @return null
	 */
	function pdo_form_config( $type ) {
		unset( $type );
		return null;
	}
}

if ( ! function_exists( 'pdo_form_result' ) ) {
	/**
	 * Fallback: nothing submitted.
	 *
	 * @param string $type Type.
	 * @return null
	 */
	function pdo_form_result( $type ) {
		unset( $type );
		return null;
	}
}

if ( ! function_exists( 'pdo_form_field_name' ) ) {
	/**
	 * Fallback: derive a field name.
	 *
	 * @param array $field Field.
	 * @param int   $index Index.
	 * @return string
	 */
	function pdo_form_field_name( $field, $index = 0 ) {
		if ( ! empty( $field['name'] ) ) {
			return sanitize_key( $field['name'] );
		}
		return 'field_' . (int) $index;
	}
}

if ( ! function_exists( 'pdo_form_field_choices' ) ) {
	/**
	 * Fallback: split the options textarea.
	 *
	 * @param array $field Field.
	 * @return array
	 */
	function pdo_form_field_choices( $field ) {
		if ( empty( $field['options'] ) ) {
			return array();
		}
		return array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $field['options'] ) ), 'strlen' ) );
	}
}

if ( ! function_exists( 'pdo_page_url' ) ) {
	/**
	 * Fallback: resolve by slug.
	 *
	 * @param string $key Page key.
	 * @return string
	 */
	function pdo_page_url( $key ) {
		$slugs = array(
			'home'         => 'home',
			'about'        => 'about',
			'impact'       => 'impact',
			'transparency' => 'transparency',
			'media'        => 'media-insights',
			'involved'     => 'get-involved',
			'contact'      => 'contact',
		);
		if ( isset( $slugs[ $key ] ) ) {
			$page = get_page_by_path( $slugs[ $key ] );
			if ( $page ) {
				return (string) get_permalink( $page );
			}
		}
		return home_url( '/' );
	}
}

if ( ! function_exists( 'pdo_image' ) ) {
	/**
	 * Fallback image renderer.
	 *
	 * @param int    $attachment_id Attachment ID.
	 * @param string $fallback_url  URL.
	 * @param array  $args          Args.
	 * @return string
	 */
	function pdo_image( $attachment_id, $fallback_url = '', $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'size'    => 'large',
				'class'   => '',
				'alt'     => '',
				'loading' => 'lazy',
			)
		);
		if ( $attachment_id && wp_attachment_is_image( $attachment_id ) ) {
			return wp_get_attachment_image(
				$attachment_id,
				$args['size'],
				false,
				array(
					'class'   => $args['class'],
					'alt'     => $args['alt'],
					'loading' => $args['loading'],
				)
			);
		}
		if ( ! $fallback_url ) {
			return '';
		}
		return sprintf(
			'<img src="%1$s" alt="%2$s" class="%3$s" loading="%4$s" decoding="async" />',
			esc_url( $fallback_url ),
			esc_attr( $args['alt'] ),
			esc_attr( $args['class'] ),
			esc_attr( $args['loading'] )
		);
	}
}

if ( ! function_exists( 'pdo_row_image' ) ) {
	/**
	 * Fallback row image renderer.
	 *
	 * @param array  $row    Row.
	 * @param array  $args   Args.
	 * @param string $prefix Prefix.
	 * @return string
	 */
	function pdo_row_image( $row, $args = array(), $prefix = 'image' ) {
		$id  = isset( $row[ $prefix ] ) ? (int) $row[ $prefix ] : 0;
		$url = isset( $row[ $prefix . '_url' ] ) ? (string) $row[ $prefix . '_url' ] : '';
		if ( empty( $args['alt'] ) && ! empty( $row['alt'] ) ) {
			$args['alt'] = $row['alt'];
		}
		return pdo_image( $id, $url, $args );
	}
}

if ( ! function_exists( 'pdo_post_image' ) ) {
	/**
	 * Fallback record image renderer.
	 *
	 * @param int    $post_id Post ID.
	 * @param array  $args    Args.
	 * @param string $prefix  Prefix.
	 * @return string
	 */
	function pdo_post_image( $post_id, $args = array(), $prefix = 'image' ) {
		$id = (int) get_post_thumbnail_id( $post_id );
		if ( ! $id ) {
			$id = (int) get_post_meta( $post_id, '_pdo_' . $prefix, true );
		}
		$url = (string) get_post_meta( $post_id, '_pdo_' . $prefix . '_url', true );
		if ( empty( $args['alt'] ) ) {
			$args['alt'] = get_the_title( $post_id );
		}
		return pdo_image( $id, $url, $args );
	}
}

if ( ! function_exists( 'pdo_scholarship_chart' ) ) {
	/**
	 * Fallback: no chart data.
	 *
	 * @return array
	 */
	function pdo_scholarship_chart() {
		return array(
			'rows'  => array(),
			'total' => 0,
		);
	}
}

if ( ! function_exists( 'pdo_news_categories' ) ) {
	/**
	 * Fallback: no categories.
	 *
	 * @return array
	 */
	function pdo_news_categories() {
		return array();
	}
}

if ( ! function_exists( 'pdo_related_news' ) ) {
	/**
	 * Fallback: no related posts.
	 *
	 * @param int $post_id Post ID.
	 * @param int $limit   Limit.
	 * @return array
	 */
	function pdo_related_news( $post_id, $limit = 3 ) {
		unset( $post_id, $limit );
		return array();
	}
}

if ( ! function_exists( 'pdo_share_links' ) ) {
	/**
	 * Fallback share links.
	 *
	 * @param int $post_id Post ID.
	 * @return array
	 */
	function pdo_share_links( $post_id ) {
		$url   = rawurlencode( (string) get_permalink( $post_id ) );
		$title = rawurlencode( wp_strip_all_tags( get_the_title( $post_id ) ) );
		return array(
			'facebook' => array(
				'label' => __( 'Share on Facebook', 'pdo-modern' ),
				'url'   => 'https://www.facebook.com/sharer/sharer.php?u=' . $url,
				'icon'  => 'facebook',
			),
			'whatsapp' => array(
				'label' => __( 'Share on WhatsApp', 'pdo-modern' ),
				'url'   => 'https://wa.me/?text=' . $title . '%20' . $url,
				'icon'  => 'whatsapp',
			),
			'email'    => array(
				'label' => __( 'Share by email', 'pdo-modern' ),
				'url'   => 'mailto:?subject=' . $title . '&body=' . $url,
				'icon'  => 'mail',
			),
		);
	}
}

if ( ! function_exists( 'pdo_team_groups' ) ) {
	/**
	 * Fallback: no team groups.
	 *
	 * @return array
	 */
	function pdo_team_groups() {
		return array();
	}
}

/* -------------------------------------------------------------------------
 * Icon + language fallbacks.
 * ---------------------------------------------------------------------- */

if ( ! function_exists( 'pdo_get_icon' ) ) {
	/**
	 * Fallback: no bundled icon library.
	 *
	 * @param string $name Icon name.
	 * @param array  $args Args.
	 * @return string
	 */
	function pdo_get_icon( $name, $args = array() ) {
		unset( $name, $args );
		return '';
	}
}

if ( ! function_exists( 'pdo_icon' ) ) {
	/**
	 * Fallback: print nothing.
	 *
	 * @param string $name Icon name.
	 * @param array  $args Args.
	 * @return void
	 */
	function pdo_icon( $name, $args = array() ) {
		echo pdo_get_icon( $name, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- whitelisted SVG.
	}
}

if ( ! function_exists( 'pdo_lang' ) ) {
	/**
	 * Fallback: English only.
	 *
	 * @return string
	 */
	function pdo_lang() {
		return 'en';
	}
}

if ( ! function_exists( 'pdo_t' ) ) {
	/**
	 * Fallback: return the supplied English text.
	 *
	 * @param string $key      String key.
	 * @param string $fallback Fallback.
	 * @return string
	 */
	function pdo_t( $key, $fallback = '' ) {
		unset( $key );
		return $fallback;
	}
}

if ( ! function_exists( 'pdo_post_title' ) ) {
	/**
	 * Fallback: the untranslated title.
	 *
	 * @param int|WP_Post $post Post.
	 * @return string
	 */
	function pdo_post_title( $post ) {
		return get_the_title( $post );
	}
}

if ( ! function_exists( 'pdo_post_content' ) ) {
	/**
	 * Fallback: the untranslated content.
	 *
	 * @param int|WP_Post $post Post.
	 * @return string
	 */
	function pdo_post_content( $post ) {
		$object = get_post( $post );
		if ( ! $object ) {
			return '';
		}
		return apply_filters( 'the_content', $object->post_content );
	}
}

if ( ! function_exists( 'pdo_post_excerpt' ) ) {
	/**
	 * Fallback: the untranslated excerpt.
	 *
	 * @param int|WP_Post $post Post.
	 * @return string
	 */
	function pdo_post_excerpt( $post ) {
		$object = get_post( $post );
		if ( ! $object ) {
			return '';
		}
		if ( $object->post_excerpt ) {
			return $object->post_excerpt;
		}
		return wp_trim_words( wp_strip_all_tags( $object->post_content ), 28, '…' );
	}
}

if ( ! function_exists( 'pdo_visible_rows' ) ) {
	/**
	 * Fallback: return the rows untouched (minus hidden ones).
	 *
	 * @param mixed $rows Rows.
	 * @return array
	 */
	function pdo_visible_rows( $rows ) {
		if ( ! is_array( $rows ) ) {
			return array();
		}
		return array_values(
			array_filter(
				$rows,
				static function ( $row ) {
					return ! is_array( $row ) || ! isset( $row['visible'] ) || $row['visible'];
				}
			)
		);
	}
}

/**
 * Admin notice telling the administrator to install the plugin.
 *
 * @return void
 */
function pdo_modern_plugin_notice() {
	if ( pdo_modern_has_plugin() || ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	?>
	<div class="notice notice-warning">
		<p>
			<strong><?php esc_html_e( 'PDO Modern needs the “PDO Website Manager” plugin.', 'pdo-modern' ); ?></strong>
			<?php esc_html_e( 'The theme renders fine without it, but the organisation content, programs, bilingual texts, forms and dynamic sections are supplied by that plugin.', 'pdo-modern' ); ?>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'pdo_modern_plugin_notice' );
