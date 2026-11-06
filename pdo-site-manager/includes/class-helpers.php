<?php
/**
 * Shared helper utilities.
 *
 * @package PDO_Site_Manager
 */

defined( 'ABSPATH' ) || exit;

/**
 * Static helper collection.
 */
class PDO_SM_Helpers {

	/**
	 * Safely read a nested array value.
	 *
	 * @param array  $array   Source array.
	 * @param string $key     Key or dot path.
	 * @param mixed  $default Fallback.
	 * @return mixed
	 */
	public static function get( $array, $key, $default = '' ) {
		if ( ! is_array( $array ) ) {
			return $default;
		}
		if ( false === strpos( $key, '.' ) ) {
			return array_key_exists( $key, $array ) ? $array[ $key ] : $default;
		}
		$parts = explode( '.', $key );
		$value = $array;
		foreach ( $parts as $part ) {
			if ( ! is_array( $value ) || ! array_key_exists( $part, $value ) ) {
				return $default;
			}
			$value = $value[ $part ];
		}
		return $value;
	}

	/**
	 * Boolean cast that understands checkbox style values.
	 *
	 * @param mixed $value Raw value.
	 * @return bool
	 */
	public static function to_bool( $value ) {
		if ( is_bool( $value ) ) {
			return $value;
		}
		if ( is_numeric( $value ) ) {
			return (bool) (int) $value;
		}
		if ( is_string( $value ) ) {
			return in_array( strtolower( trim( $value ) ), array( '1', 'yes', 'on', 'true' ), true );
		}
		return (bool) $value;
	}

	/**
	 * Sanitize a hex colour (also allows empty and rgba()).
	 *
	 * @param string $value Raw colour.
	 * @return string
	 */
	public static function sanitize_color( $value ) {
		$value = trim( (string) $value );
		if ( '' === $value ) {
			return '';
		}
		if ( preg_match( '/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{4}|[A-Fa-f0-9]{6}|[A-Fa-f0-9]{8})$/', $value ) ) {
			return $value;
		}
		if ( preg_match( '/^rgba?\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*(,\s*(0|1|0?\.\d+)\s*)?\)$/', $value ) ) {
			return $value;
		}
		return '';
	}

	/**
	 * Sanitize multi-paragraph plain text while keeping line breaks.
	 *
	 * @param string $value Raw text.
	 * @return string
	 */
	public static function sanitize_multiline( $value ) {
		return implode( "\n", array_map( 'sanitize_text_field', explode( "\n", (string) $value ) ) );
	}

	/**
	 * Sanitize rich text coming from the editor.
	 *
	 * @param string $value Raw HTML.
	 * @return string
	 */
	public static function sanitize_rich_text( $value ) {
		return wp_kses_post( (string) $value );
	}

	/**
	 * Normalise an ordered list of records by the `order` key.
	 *
	 * @param array $rows Rows.
	 * @return array
	 */
	public static function sort_rows( $rows ) {
		if ( ! is_array( $rows ) ) {
			return array();
		}
		$rows = array_values( $rows );
		usort(
			$rows,
			static function ( $a, $b ) {
				$oa = isset( $a['order'] ) ? (int) $a['order'] : 0;
				$ob = isset( $b['order'] ) ? (int) $b['order'] : 0;
				if ( $oa === $ob ) {
					return 0;
				}
				return ( $oa < $ob ) ? -1 : 1;
			}
		);
		return $rows;
	}

	/**
	 * Filter out rows flagged as hidden.
	 *
	 * @param array $rows Rows.
	 * @return array
	 */
	public static function visible_rows( $rows ) {
		$out = array();
		foreach ( (array) $rows as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			if ( array_key_exists( 'visible', $row ) && ! self::to_bool( $row['visible'] ) ) {
				continue;
			}
			$out[] = $row;
		}
		return self::sort_rows( $out );
	}

	/**
	 * Convert a stored number string ("1,200") to an integer.
	 *
	 * @param mixed $value Raw value.
	 * @return int
	 */
	public static function to_int( $value ) {
		return (int) preg_replace( '/[^0-9\-]/', '', (string) $value );
	}

	/**
	 * Build an attachment image array.
	 *
	 * @param int    $attachment_id Attachment ID.
	 * @param string $size          Image size.
	 * @return array{url:string,width:int,height:int,alt:string}
	 */
	public static function image( $attachment_id, $size = 'large' ) {
		$attachment_id = (int) $attachment_id;
		$out           = array(
			'url'    => '',
			'width'  => 0,
			'height' => 0,
			'alt'    => '',
		);
		if ( $attachment_id <= 0 ) {
			return $out;
		}
		$src = wp_get_attachment_image_src( $attachment_id, $size );
		if ( ! $src ) {
			return $out;
		}
		$out['url']    = $src[0];
		$out['width']  = (int) $src[1];
		$out['height'] = (int) $src[2];
		$out['alt']    = (string) get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
		return $out;
	}

	/**
	 * Resolve an image URL from either an attachment ID or a raw URL field.
	 *
	 * @param array  $row     Row data.
	 * @param string $id_key  Attachment ID key.
	 * @param string $url_key Fallback URL key.
	 * @param string $size    Image size.
	 * @return string
	 */
	public static function row_image_url( $row, $id_key = 'image', $url_key = 'image_url', $size = 'large' ) {
		$id = isset( $row[ $id_key ] ) ? (int) $row[ $id_key ] : 0;
		if ( $id > 0 ) {
			$img = self::image( $id, $size );
			if ( $img['url'] ) {
				return $img['url'];
			}
		}
		if ( ! empty( $row[ $url_key ] ) ) {
			return esc_url_raw( $row[ $url_key ] );
		}
		return '';
	}

	/**
	 * Current visitor IP, hashed for privacy-safe rate limiting.
	 *
	 * @return string
	 */
	public static function visitor_hash() {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
		$ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
		return md5( $ip . '|' . $ua . '|' . wp_salt() );
	}

	/**
	 * Unslashed, sanitized text from a request array.
	 *
	 * @param array  $source Source superglobal (already verified).
	 * @param string $key    Key.
	 * @return string
	 */
	public static function request_text( $source, $key ) {
		return isset( $source[ $key ] ) ? sanitize_text_field( wp_unslash( $source[ $key ] ) ) : '';
	}

	/**
	 * Convert a label into a stable slug/key.
	 *
	 * @param string $label Label.
	 * @return string
	 */
	public static function key_from_label( $label ) {
		$key = sanitize_title( $label );
		$key = str_replace( '-', '_', $key );
		return $key ? $key : 'field';
	}
}
