<?php
/**
 * Small, reusable helpers.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

/**
 * Available font stacks.
 *
 * @param bool|null $bengali true = Bengali stacks, false = Latin stacks, null = all.
 * @return array<string,array{label:string,stack:string}>
 */
function pdo_modern_font_stacks( $bengali = false ) {
	$latin = array(
		'inter'       => array(
			'label' => __( 'Inter (bundled)', 'pdo-modern' ),
			'stack' => '"Inter", "Helvetica Neue", Arial, sans-serif',
		),
		'system'      => array(
			'label' => __( 'System UI', 'pdo-modern' ),
			'stack' => '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif',
		),
		'georgia'     => array(
			'label' => __( 'Georgia (serif)', 'pdo-modern' ),
			'stack' => 'Georgia, "Times New Roman", serif',
		),
		'serif-stack' => array(
			'label' => __( 'Classic serif', 'pdo-modern' ),
			'stack' => '"Iowan Old Style", "Palatino Linotype", Palatino, Georgia, serif',
		),
		'mono'        => array(
			'label' => __( 'Monospace', 'pdo-modern' ),
			'stack' => 'ui-monospace, SFMono-Regular, "SF Mono", Menlo, Consolas, monospace',
		),
	);

	$bangla = array(
		'hind-siliguri' => array(
			'label' => __( 'Hind Siliguri (bundled)', 'pdo-modern' ),
			'stack' => '"Hind Siliguri", "Noto Sans Bengali", "SolaimanLipi", "Kalpurush", sans-serif',
		),
		'noto-bengali'  => array(
			'label' => __( 'Noto Sans Bengali', 'pdo-modern' ),
			'stack' => '"Noto Sans Bengali", "Hind Siliguri", "SolaimanLipi", sans-serif',
		),
		'solaimanlipi'  => array(
			'label' => __( 'SolaimanLipi', 'pdo-modern' ),
			'stack' => '"SolaimanLipi", "Hind Siliguri", "Noto Sans Bengali", sans-serif',
		),
		'kalpurush'     => array(
			'label' => __( 'Kalpurush', 'pdo-modern' ),
			'stack' => '"Kalpurush", "Hind Siliguri", "Noto Sans Bengali", sans-serif',
		),
	);

	if ( true === $bengali ) {
		return $bangla;
	}
	if ( false === $bengali ) {
		return $latin;
	}

	return array_merge( $latin, $bangla );
}

/**
 * Resolve a font stack by its key.
 *
 * @param string $key      Stack key.
 * @param string $fallback Fallback key.
 * @return string
 */
function pdo_modern_font_stack( $key, $fallback = 'inter' ) {
	$stacks = pdo_modern_font_stacks( null );
	if ( isset( $stacks[ $key ] ) ) {
		return $stacks[ $key ]['stack'];
	}
	return isset( $stacks[ $fallback ] ) ? $stacks[ $fallback ]['stack'] : 'sans-serif';
}

/**
 * Container max width in px for the configured layout.
 *
 * @param string|null $preset Optional explicit preset; defaults to the saved value.
 * @return string
 */
function pdo_modern_container_width( $preset = null ) {
	switch ( null === $preset ? get_theme_mod( 'pdo_layout_width', 'wide' ) : $preset ) {
		case 'narrow':
			return '1100px';
		case 'xwide':
			return '1440px';
		case 'full':
			return '100%';
		case 'wide':
		default:
			return '1280px';
	}
}

/**
 * Convert a hex colour to "r, g, b" so it can feed rgba() in CSS.
 *
 * @param string $hex Hex colour.
 * @return string
 */
function pdo_modern_hex_to_rgb( $hex ) {
	$hex = ltrim( (string) $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	if ( 6 !== strlen( $hex ) || ! ctype_xdigit( $hex ) ) {
		return '0, 0, 0';
	}
	return sprintf(
		'%d, %d, %d',
		hexdec( substr( $hex, 0, 2 ) ),
		hexdec( substr( $hex, 2, 2 ) ),
		hexdec( substr( $hex, 4, 2 ) )
	);
}

/**
 * Lighten or darken a hex colour.
 *
 * @param string $hex     Hex colour.
 * @param int    $percent -100..100.
 * @return string
 */
function pdo_modern_shade( $hex, $percent ) {
	$hex = ltrim( (string) $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	if ( 6 !== strlen( $hex ) || ! ctype_xdigit( $hex ) ) {
		return '#' . $hex;
	}

	$out = '#';
	for ( $i = 0; $i < 3; $i++ ) {
		$channel = hexdec( substr( $hex, $i * 2, 2 ) );
		if ( $percent >= 0 ) {
			$channel = (int) round( $channel + ( 255 - $channel ) * ( $percent / 100 ) );
		} else {
			$channel = (int) round( $channel * ( 1 + ( $percent / 100 ) ) );
		}
		$out .= str_pad( dechex( max( 0, min( 255, $channel ) ) ), 2, '0', STR_PAD_LEFT );
	}

	return $out;
}

/**
 * Read a theme mod colour with the registered default.
 *
 * @param string $token Token key.
 * @return string
 */
function pdo_modern_color( $token ) {
	$tokens  = pdo_modern_color_tokens();
	$default = isset( $tokens[ $token ] ) ? $tokens[ $token ][1] : '#000000';
	$value   = get_theme_mod( 'pdo_color_' . $token, $default );
	return $value ? $value : $default;
}

/**
 * Resolve the background class for a configured background choice.
 *
 * @param string $choice Choice key.
 * @return string
 */
function pdo_modern_background_class( $choice ) {
	$map = array(
		''            => '',
		'light'       => 'pdo-bg-light',
		'tint'        => 'pdo-bg-tint',
		'dark'        => 'pdo-bg-dark',
		'navy'        => 'pdo-bg-navy',
		'pattern'     => 'pdo-bg-pattern',
		'transparent' => 'pdo-bg-transparent',
	);
	$choice = (string) $choice;
	return isset( $map[ $choice ] ) ? $map[ $choice ] : '';
}

/**
 * Resolve the accent class for a configured accent choice.
 *
 * @param string $choice Choice key.
 * @return string
 */
function pdo_modern_accent_class( $choice ) {
	$choice = sanitize_html_class( (string) $choice );
	return $choice ? 'pdo-accent-' . $choice : 'pdo-accent-emerald';
}

/**
 * Responsive visibility classes for a section.
 *
 * @param string $key Section key.
 * @return string
 */
function pdo_modern_visibility_class( $key ) {
	$value = get_theme_mod( 'pdo_section_hide_' . $key, '' );
	switch ( $value ) {
		case 'mobile':
			return 'pdo-hide-mobile';
		case 'tablet':
			return 'pdo-hide-tablet';
		case 'desktop':
			return 'pdo-hide-desktop';
		case 'all':
			return 'pdo-hide-all';
	}
	return '';
}

/**
 * Build an HTML attribute string from an associative array.
 *
 * @param array $attrs Attributes.
 * @return string
 */
function pdo_modern_attrs( $attrs ) {
	$out = array();
	foreach ( $attrs as $key => $value ) {
		if ( false === $value || null === $value || '' === $value ) {
			continue;
		}
		if ( true === $value ) {
			$out[] = esc_attr( $key );
			continue;
		}
		$out[] = sprintf( '%s="%s"', esc_attr( $key ), esc_attr( $value ) );
	}
	return $out ? ' ' . implode( ' ', $out ) : '';
}

/**
 * Trim a string to a word count without breaking HTML entities.
 *
 * @param string $text  Text.
 * @param int    $words Word count.
 * @return string
 */
function pdo_modern_trim_words( $text, $words = 24 ) {
	return wp_trim_words( wp_strip_all_tags( (string) $text ), (int) $words, '…' );
}

/**
 * Does the current request show one of the PDO managed pages?
 *
 * @param string $key Page key.
 * @return bool
 */
function pdo_modern_is_page( $key ) {
	if ( ! is_page() ) {
		return false;
	}
	$pages = get_option( 'pdo_sm_pages', array() );
	if ( is_array( $pages ) && ! empty( $pages[ $key ] ) ) {
		return is_page( (int) $pages[ $key ] );
	}
	return false;
}

/**
 * The location key of the current request, for dynamic sections.
 *
 * @return string
 */
function pdo_modern_current_location() {
	foreach ( array( 'home', 'about', 'impact', 'transparency', 'media', 'involved', 'contact' ) as $key ) {
		if ( pdo_modern_is_page( $key ) ) {
			return $key;
		}
	}
	if ( is_front_page() ) {
		return 'home';
	}
	if ( is_post_type_archive( 'pdo_news' ) || is_singular( 'pdo_news' ) || is_tax( 'pdo_news_cat' ) ) {
		return 'media';
	}
	return '';
}

/**
 * Render a labelled statistic value, splitting prefix / value / suffix.
 *
 * @param array $row Repeater row.
 * @return string
 */
function pdo_modern_stat_value( $row ) {
	$prefix = isset( $row['prefix'] ) ? (string) $row['prefix'] : '';
	$value  = isset( $row['value'] ) ? (string) $row['value'] : '';
	$suffix = isset( $row['suffix'] ) ? (string) $row['suffix'] : '';

	$animate = ! isset( $row['animate'] ) || $row['animate'];
	$numeric = preg_replace( '/[^0-9.]/', '', $value );

	$attrs = '';
	if ( $animate && '' !== $numeric && is_numeric( $numeric ) ) {
		$attrs = ' data-pdo-count="' . esc_attr( $numeric ) . '"';
	}

	return sprintf(
		'<span class="pdo-stat__value"><span class="pdo-stat__prefix">%1$s</span><span class="pdo-stat__number"%4$s>%2$s</span><span class="pdo-stat__suffix">%3$s</span></span>',
		esc_html( $prefix ),
		esc_html( $value ),
		esc_html( $suffix ),
		$attrs
	);
}

/**
 * Escape a URL but allow in-page anchors and tel:/mailto:.
 *
 * @param string $url URL.
 * @return string
 */
function pdo_modern_url( $url ) {
	$url = trim( (string) $url );
	if ( '' === $url ) {
		return '';
	}
	if ( 0 === strpos( $url, '#' ) ) {
		return esc_attr( $url );
	}
	return esc_url( $url );
}

/**
 * Current front-end URL, safe to use as a form redirect target.
 *
 * @return string
 */
function pdo_modern_current_url() {
	global $wp;

	$path = isset( $wp->request ) ? (string) $wp->request : '';
	$url  = $path ? home_url( user_trailingslashit( $path ) ) : home_url( '/' );

	if ( is_front_page() ) {
		$url = home_url( '/' );
	}

	return remove_query_arg( array( 'pdo_result', 'pdo_form' ), $url );
}
