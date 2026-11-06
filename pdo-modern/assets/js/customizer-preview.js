/**
 * PDO Modern – Customizer live preview.
 *
 * Every setting registered with `postMessage` transport is wired here so the
 * preview updates without reloading. Values are written straight onto the
 * documentElement as CSS custom properties, which is exactly where
 * inc/customizer/customizer-css.php puts them on the server side.
 */
( function ( $, api ) {
	'use strict';

	if ( ! api ) {
		return;
	}

	var data = window.pdoCustomizePreview || {};
	var root = document.documentElement;

	function setVar( name, value ) {
		root.style.setProperty( name, value );
	}

	function hexToRgb( hex ) {
		var clean = String( hex || '' ).replace( '#', '' ).trim();
		if ( clean.length === 3 ) {
			clean = clean[ 0 ] + clean[ 0 ] + clean[ 1 ] + clean[ 1 ] + clean[ 2 ] + clean[ 2 ];
		}
		if ( ! /^[0-9a-f]{6}$/i.test( clean ) ) {
			return null;
		}
		return [
			parseInt( clean.substring( 0, 2 ), 16 ),
			parseInt( clean.substring( 2, 4 ), 16 ),
			parseInt( clean.substring( 4, 6 ), 16 )
		];
	}

	function shade( hex, percent ) {
		var rgb = hexToRgb( hex );
		if ( ! rgb ) {
			return hex;
		}
		return (
			'#' +
			rgb
				.map( function ( channel ) {
					var next = Math.round( channel + ( percent / 100 ) * ( percent > 0 ? 255 - channel : channel ) );
					next = Math.max( 0, Math.min( 255, next ) );
					return ( '0' + next.toString( 16 ) ).slice( -2 );
				} )
				.join( '' )
		);
	}

	function rgbString( hex, fallback ) {
		var rgb = hexToRgb( hex );
		return rgb ? rgb.join( ', ' ) : fallback;
	}

	function currentColor( token, fallback ) {
		var setting = api( 'pdo_color_' + token );
		return setting ? setting.get() : fallback;
	}

	function refreshDerivedColours() {
		var emerald = currentColor( 'emerald', '#0d7a53' );
		var accent = currentColor( 'accent', '#d97706' );
		var dark = currentColor( 'emerald_dark', '#084c34' );
		var navy = currentColor( 'navy', '#0f172a' );

		setVar( '--pdo-emerald-hover', shade( emerald, -14 ) );
		setVar( '--pdo-emerald-soft', shade( emerald, 86 ) );
		setVar( '--pdo-accent-hover', shade( accent, -12 ) );
		setVar( '--pdo-shadow-brand', '0 10px 30px -12px rgba(' + rgbString( emerald, '13, 122, 83' ) + ', 0.55)' );
		setVar( '--pdo-overlay', 'linear-gradient(120deg, rgba(' + rgbString( navy, '15, 23, 42' ) + ', 0.78), rgba(' + rgbString( dark, '8, 76, 52' ) + ', 0.6))' );

		var gradient = api( 'pdo_gradient_enabled' );
		if ( gradient && ! gradient.get() ) {
			setVar( '--pdo-gradient', dark );
		} else {
			setVar( '--pdo-gradient', 'linear-gradient(135deg, ' + dark + ' 0%, ' + emerald + ' 100%)' );
		}
	}

	/* --------------------------------------------------------------- */
	/* Site identity                                                    */
	/* --------------------------------------------------------------- */

	api( 'blogname', function ( value ) {
		value.bind( function ( to ) {
			$( '.pdo-brand__name, .site-title a' ).each( function () {
				var badge = $( this ).find( '.pdo-brand__badge' ).detach();
				$( this ).text( to ).append( badge );
			} );
		} );
	} );

	api( 'blogdescription', function ( value ) {
		value.bind( function ( to ) {
			$( '.pdo-brand__tagline, .site-description' ).text( to );
		} );
	} );

	/* --------------------------------------------------------------- */
	/* Colours                                                          */
	/* --------------------------------------------------------------- */

	api.each( function ( setting, id ) {
		if ( id.indexOf( 'pdo_color_' ) !== 0 ) {
			return;
		}
		var token = id.replace( 'pdo_color_', '' ).replace( /_/g, '-' );
		setting.bind( function ( to ) {
			setVar( '--pdo-' + token, to );
			var rgb = rgbString( to, null );
			if ( rgb ) {
				setVar( '--pdo-' + token + '-rgb', rgb );
			}
			refreshDerivedColours();
		} );
	} );

	api( 'pdo_gradient_enabled', function ( value ) {
		value.bind( refreshDerivedColours );
	} );

	/* --------------------------------------------------------------- */
	/* Typography                                                       */
	/* --------------------------------------------------------------- */

	function bindFont( settingId, cssVar ) {
		api( settingId, function ( value ) {
			value.bind( function ( to ) {
				var stacks = data.fonts || {};
				if ( stacks[ to ] ) {
					setVar( cssVar, stacks[ to ] );
				}
			} );
		} );
	}

	bindFont( 'pdo_font_body', '--pdo-font-body' );
	bindFont( 'pdo_font_heading', '--pdo-font-heading' );
	bindFont( 'pdo_font_bengali', '--pdo-font-bengali' );

	function typeScale( base, ratio ) {
		var steps = {
			'--pdo-text-xs': -2,
			'--pdo-text-sm': -1,
			'--pdo-text-md': 0,
			'--pdo-text-lg': 1,
			'--pdo-text-xl': 2,
			'--pdo-text-2xl': 3,
			'--pdo-text-3xl': 4,
			'--pdo-text-4xl': 5
		};

		Object.keys( steps ).forEach( function ( name ) {
			var size = base * Math.pow( ratio, steps[ name ] );
			var min = Math.max( 12, size * 0.82 );
			setVar(
				name,
				'clamp(' + min.toFixed( 2 ) + 'px, ' + ( size / 16 * 0.9 ).toFixed( 3 ) + 'rem + 0.35vw, ' + size.toFixed( 2 ) + 'px)'
			);
		} );
	}

	function refreshTypeScale() {
		var base = parseFloat( api( 'pdo_font_size_base' ) ? api( 'pdo_font_size_base' ).get() : 16 ) || 16;
		var ratio = parseFloat( api( 'pdo_font_scale' ) ? api( 'pdo_font_scale' ).get() : 1.25 ) || 1.25;
		setVar( '--pdo-font-size', base + 'px' );
		typeScale( base, ratio );
	}

	api( 'pdo_font_size_base', function ( value ) {
		value.bind( refreshTypeScale );
	} );

	api( 'pdo_font_scale', function ( value ) {
		value.bind( refreshTypeScale );
	} );

	api( 'pdo_line_height', function ( value ) {
		value.bind( function ( to ) {
			setVar( '--pdo-line-height', String( to ) );
		} );
	} );

	api( 'pdo_heading_weight', function ( value ) {
		value.bind( function ( to ) {
			setVar( '--pdo-heading-weight', String( parseInt( to, 10 ) || 800 ) );
		} );
	} );

	api( 'pdo_heading_tracking', function ( value ) {
		value.bind( function ( to ) {
			setVar( '--pdo-heading-track', ( parseFloat( to ) / 100 ).toFixed( 3 ) + 'em' );
		} );
	} );

	api( 'pdo_bengali_scale', function ( value ) {
		value.bind( function ( to ) {
			setVar( '--pdo-bengali-scale', ( parseFloat( to ) / 100 ).toFixed( 3 ) );
		} );
	} );

	api( 'pdo_uppercase_eyebrows', function ( value ) {
		value.bind( function ( to ) {
			setVar( '--pdo-eyebrow-case', to ? 'uppercase' : 'none' );
		} );
	} );

	/* --------------------------------------------------------------- */
	/* Layout                                                           */
	/* --------------------------------------------------------------- */

	var pxVars = {
		pdo_container_padding: '--pdo-gutter',
		pdo_section_spacing: '--pdo-section-space',
		pdo_card_gap: '--pdo-gap',
		pdo_nav_font_size: '--pdo-nav-size',
		pdo_nav_gap: '--pdo-nav-gap',
		pdo_logo_width: '--pdo-logo-width',
		pdo_logo_height: '--pdo-logo-height',
		pdo_logo_width_mobile: '--pdo-logo-mobile',
		pdo_logo_width_footer: '--pdo-logo-footer'
	};

	Object.keys( pxVars ).forEach( function ( settingId ) {
		api( settingId, function ( value ) {
			value.bind( function ( to ) {
				setVar( pxVars[ settingId ], parseFloat( to ) + 'px' );
			} );
		} );
	} );

	api( 'pdo_layout_width', function ( value ) {
		value.bind( function ( to ) {
			var widths = data.widths || {};
			setVar( '--pdo-container', widths[ to ] || '1280px' );
		} );
	} );

	api( 'pdo_radius', function ( value ) {
		value.bind( function ( to ) {
			var radius = parseFloat( to ) || 0;
			setVar( '--pdo-radius', radius + 'px' );
			setVar( '--pdo-radius-sm', Math.max( 0, radius * 0.6 ).toFixed( 1 ) + 'px' );
			setVar( '--pdo-radius-lg', ( radius * 1.6 ).toFixed( 1 ) + 'px' );
		} );
	} );

	api( 'pdo_shadow_strength', function ( value ) {
		value.bind( function ( to ) {
			var factor = ( parseFloat( to ) || 100 ) / 100;
			setVar( '--pdo-shadow-sm', '0 1px 2px rgba(15, 23, 42, ' + ( 0.05 * factor ).toFixed( 3 ) + '), 0 2px 8px rgba(15, 23, 42, ' + ( 0.05 * factor ).toFixed( 3 ) + ')' );
			setVar( '--pdo-shadow', '0 8px 24px rgba(15, 23, 42, ' + ( 0.09 * factor ).toFixed( 3 ) + ')' );
			setVar( '--pdo-shadow-lg', '0 24px 60px rgba(15, 23, 42, ' + ( 0.16 * factor ).toFixed( 3 ) + ')' );
		} );
	} );

	api( 'pdo_rounded_corners', function ( value ) {
		value.bind( function ( to ) {
			document.body.classList.toggle( 'pdo-square', ! to );
		} );
	} );

	api( 'pdo_animations', function ( value ) {
		value.bind( function ( to ) {
			document.body.classList.toggle( 'pdo-no-animation', ! to );
		} );
	} );

	/* --------------------------------------------------------------- */
	/* Header CTA                                                       */
	/* --------------------------------------------------------------- */

	api( 'pdo_header_cta_label', function ( value ) {
		value.bind( function ( to ) {
			$( '.pdo-header__cta' ).find( 'span' ).first().text( to );
		} );
	} );

	api( 'pdo_header_cta_url', function ( value ) {
		value.bind( function ( to ) {
			$( '.pdo-header__cta' ).attr( 'href', to );
		} );
	} );

	/* --------------------------------------------------------------- */
	/* Navigation                                                       */
	/* --------------------------------------------------------------- */

	api( 'pdo_nav_uppercase', function ( value ) {
		value.bind( function ( to ) {
			setVar( '--pdo-nav-case', to ? 'uppercase' : 'none' );
		} );
	} );

	api( 'pdo_nav_style', function ( value ) {
		value.bind( function ( to ) {
			var nav = document.querySelector( '.pdo-nav--desktop' );
			if ( ! nav ) {
				return;
			}
			[ 'underline', 'pill', 'color' ].forEach( function ( style ) {
				nav.classList.toggle( 'pdo-nav--' + style, style === to );
			} );
		} );
	} );

	/* --------------------------------------------------------------- */
	/* Footer                                                           */
	/* --------------------------------------------------------------- */

	api( 'pdo_footer_credit', function ( value ) {
		value.bind( function ( to ) {
			var node = document.querySelector( '.pdo-footer__credit' );
			if ( node ) {
				node.innerHTML = to;
			}
		} );
	} );
} )( window.jQuery, window.wp && window.wp.customize );
