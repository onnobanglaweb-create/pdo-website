<?php
/**
 * Turn the Customizer settings into CSS custom properties.
 *
 * Templates and the stylesheet only ever reference `var(--pdo-*)`, so no
 * colour, font or spacing value is hardcoded in the markup.
 *
 * @package PDO_Modern
 */

defined( 'ABSPATH' ) || exit;

/**
 * Build the `:root` custom property block.
 *
 * @return string
 */
function pdo_modern_css_variables() {
	$vars = array();

	/* Colours ------------------------------------------------------------ */
	foreach ( pdo_modern_color_tokens() as $token => $meta ) {
		$value                                           = pdo_modern_color( $token );
		$vars[ '--pdo-' . str_replace( '_', '-', $token ) ] = $value;
		$vars[ '--pdo-' . str_replace( '_', '-', $token ) . '-rgb' ] = pdo_modern_hex_to_rgb( $value );
	}

	$emerald = pdo_modern_color( 'emerald' );
	$dark    = pdo_modern_color( 'emerald_dark' );

	$vars['--pdo-emerald-soft']   = pdo_modern_shade( $emerald, 82 );
	$vars['--pdo-emerald-hover']  = pdo_modern_shade( $emerald, -12 );
	$vars['--pdo-accent-hover']   = pdo_modern_shade( pdo_modern_color( 'accent' ), -12 );
	$vars['--pdo-overlay']        = 'rgba(' . pdo_modern_hex_to_rgb( pdo_modern_color( 'navy' ) ) . ', 0.62)';

	if ( get_theme_mod( 'pdo_gradient_enabled', true ) ) {
		$vars['--pdo-gradient'] = 'linear-gradient(135deg, ' . $dark . ' 0%, ' . $emerald . ' 58%, ' . pdo_modern_shade( $emerald, 14 ) . ' 100%)';
	} else {
		$vars['--pdo-gradient'] = $dark;
	}

	/* Typography --------------------------------------------------------- */
	$base  = (float) get_theme_mod( 'pdo_font_size_base', 16 );
	$scale = (float) get_theme_mod( 'pdo_font_scale', 1.25 );
	$scale = $scale > 1 ? $scale : 1.25;

	$vars['--pdo-font-body']      = pdo_modern_font_stack( (string) get_theme_mod( 'pdo_font_body', 'inter' ) );
	$vars['--pdo-font-heading']   = pdo_modern_font_stack( (string) get_theme_mod( 'pdo_font_heading', 'inter' ) );
	$vars['--pdo-font-bengali']   = pdo_modern_font_stack( (string) get_theme_mod( 'pdo_font_bengali', 'hind-siliguri' ), 'hind-siliguri' );
	$vars['--pdo-font-size']      = $base . 'px';
	$vars['--pdo-line-height']    = (string) get_theme_mod( 'pdo_line_height', 1.7 );
	$vars['--pdo-heading-weight'] = (string) (int) get_theme_mod( 'pdo_heading_weight', 800 );
	$vars['--pdo-heading-track']  = ( (float) get_theme_mod( 'pdo_heading_tracking', -2 ) / 100 ) . 'em';
	$vars['--pdo-bengali-scale']  = ( (float) get_theme_mod( 'pdo_bengali_scale', 104 ) / 100 ) . '';
	$vars['--pdo-eyebrow-case']   = get_theme_mod( 'pdo_uppercase_eyebrows', true ) ? 'uppercase' : 'none';

	// Modular type scale, clamped for fluid resizing.
	$steps = array(
		'xs' => -2,
		'sm' => -1,
		'md' => 0,
		'lg' => 1,
		'xl' => 2,
		'2xl' => 3,
		'3xl' => 4,
		'4xl' => 5,
	);
	foreach ( $steps as $name => $power ) {
		$size                                = $base * pow( $scale, $power );
		$min                                 = max( 12, $size * ( $power > 1 ? 0.72 : 0.94 ) );
		$vars[ '--pdo-text-' . $name ]       = sprintf(
			'clamp(%srem, %srem + %svw, %srem)',
			round( $min / 16, 3 ),
			round( $min / 16, 3 ),
			round( ( ( $size - $min ) / 16 ) * 0.9, 3 ),
			round( $size / 16, 3 )
		);
	}

	/* Layout ------------------------------------------------------------- */
	$radius = (float) get_theme_mod( 'pdo_radius', 16 );
	$shadow = (float) get_theme_mod( 'pdo_shadow_strength', 100 ) / 100;

	$vars['--pdo-container']      = pdo_modern_container_width();
	$vars['--pdo-gutter']         = (float) get_theme_mod( 'pdo_container_padding', 24 ) . 'px';
	$vars['--pdo-section-space']  = (float) get_theme_mod( 'pdo_section_spacing', 88 ) . 'px';
	$vars['--pdo-gap']            = (float) get_theme_mod( 'pdo_card_gap', 28 ) . 'px';
	$vars['--pdo-radius']         = $radius . 'px';
	$vars['--pdo-radius-sm']      = max( 0, $radius * 0.5 ) . 'px';
	$vars['--pdo-radius-lg']      = ( $radius * 1.5 ) . 'px';
	$vars['--pdo-radius-pill']    = $radius > 0 ? '999px' : '0';
	$vars['--pdo-shadow-sm']      = sprintf( '0 1px 2px rgba(15, 23, 42, %s)', round( 0.06 * $shadow, 4 ) );
	$vars['--pdo-shadow']         = sprintf( '0 10px 30px rgba(15, 23, 42, %s)', round( 0.08 * $shadow, 4 ) );
	$vars['--pdo-shadow-lg']      = sprintf( '0 24px 60px rgba(15, 23, 42, %s)', round( 0.14 * $shadow, 4 ) );
	$vars['--pdo-shadow-brand']   = sprintf( '0 14px 34px rgba(%s, %s)', pdo_modern_hex_to_rgb( $emerald ), round( 0.25 * $shadow, 4 ) );

	/* Header / navigation ------------------------------------------------ */
	$vars['--pdo-nav-size']       = (float) get_theme_mod( 'pdo_nav_font_size', 15 ) . 'px';
	$vars['--pdo-nav-gap']        = (float) get_theme_mod( 'pdo_nav_gap', 6 ) . 'px';
	$vars['--pdo-nav-case']       = get_theme_mod( 'pdo_nav_uppercase', false ) ? 'uppercase' : 'none';
	$vars['--pdo-logo-width']     = (int) get_theme_mod( 'pdo_logo_width', 190 ) . 'px';
	$vars['--pdo-logo-height']    = (int) get_theme_mod( 'pdo_logo_height', 52 ) . 'px';
	$vars['--pdo-logo-mobile']    = (int) get_theme_mod( 'pdo_logo_width_mobile', 150 ) . 'px';
	$vars['--pdo-logo-footer']    = (int) get_theme_mod( 'pdo_logo_width_footer', 200 ) . 'px';
	$vars['--pdo-breakpoint-nav'] = (int) get_theme_mod( 'pdo_mobile_breakpoint', 1024 ) . 'px';

	/* Assemble ----------------------------------------------------------- */
	$css = ":root{\n";
	foreach ( $vars as $name => $value ) {
		$css .= "\t" . $name . ': ' . $value . ";\n";
	}
	$css .= "}\n";

	// Mobile navigation breakpoint cannot live in a custom property, so the
	// media query is generated here.
	$breakpoint = (int) get_theme_mod( 'pdo_mobile_breakpoint', 1024 );
	$css       .= sprintf(
		"@media (max-width: %dpx){.pdo-nav--desktop{display:none;}.pdo-header__burger{display:inline-flex;}}\n",
		$breakpoint
	);
	$css .= sprintf(
		"@media (min-width: %dpx){.pdo-nav--desktop{display:block;}.pdo-header__burger{display:none;}.pdo-drawer{display:none !important;}}\n",
		$breakpoint + 1
	);

	/**
	 * Filter the generated custom property block.
	 *
	 * @param string $css  CSS.
	 * @param array  $vars Variables.
	 */
	return apply_filters( 'pdo_modern_css_variables', $css, $vars );
}

/**
 * Expose the tokens to the block editor as a colour palette.
 *
 * @return void
 */
function pdo_modern_editor_palette() {
	$palette = array();
	$wanted  = array( 'emerald', 'emerald_dark', 'emerald_light', 'accent', 'gold', 'teal', 'navy', 'slate', 'surface_alt', 'border' );

	foreach ( $wanted as $token ) {
		$tokens = pdo_modern_color_tokens();
		if ( ! isset( $tokens[ $token ] ) ) {
			continue;
		}
		$palette[] = array(
			'name'  => $tokens[ $token ][0],
			'slug'  => 'pdo-' . str_replace( '_', '-', $token ),
			'color' => pdo_modern_color( $token ),
		);
	}

	add_theme_support( 'editor-color-palette', $palette );

	add_theme_support(
		'editor-font-sizes',
		array(
			array(
				'name' => __( 'Small', 'pdo-modern' ),
				'slug' => 'small',
				'size' => 14,
			),
			array(
				'name' => __( 'Normal', 'pdo-modern' ),
				'slug' => 'normal',
				'size' => (int) get_theme_mod( 'pdo_font_size_base', 16 ),
			),
			array(
				'name' => __( 'Large', 'pdo-modern' ),
				'slug' => 'large',
				'size' => 22,
			),
			array(
				'name' => __( 'Extra large', 'pdo-modern' ),
				'slug' => 'huge',
				'size' => 32,
			),
		)
	);
}
add_action( 'after_setup_theme', 'pdo_modern_editor_palette', 20 );
