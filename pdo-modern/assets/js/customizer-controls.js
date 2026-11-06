/**
 * PDO Modern – Customizer control behaviour.
 *
 * Powers the range slider readout/reset and the drag-to-reorder homepage
 * section list. jQuery and jquery-ui-sortable ship with WordPress admin, so
 * using them here costs nothing extra (the front end stays dependency free).
 */
( function ( $, api ) {
	'use strict';

	/* ------------------------------------------------------------------ */
	/* Range control                                                      */
	/* ------------------------------------------------------------------ */

	function initRanges( scope ) {
		$( '.pdo-range', scope ).each( function () {
			var wrap = $( this );

			if ( wrap.data( 'pdoReady' ) ) {
				return;
			}
			wrap.data( 'pdoReady', true );

			var slider = wrap.find( '.pdo-range__slider' );
			var output = wrap.find( '.pdo-range__output' );
			var reset = wrap.find( '.pdo-range__reset' );
			var unit = slider.data( 'unit' ) || '';

			function paint( value ) {
				output.text( value + unit );

				var min = parseFloat( slider.attr( 'min' ) ) || 0;
				var max = parseFloat( slider.attr( 'max' ) ) || 100;
				var percent = max === min ? 0 : ( ( parseFloat( value ) - min ) / ( max - min ) ) * 100;
				slider[ 0 ].style.setProperty( '--pdo-range-progress', percent.toFixed( 2 ) + '%' );
			}

			slider.on( 'input change', function () {
				paint( this.value );
			} );

			reset.on( 'click', function () {
				var fallback = $( this ).data( 'default' );
				slider.val( fallback ).trigger( 'input' ).trigger( 'change' );
				paint( fallback );
			} );

			paint( slider.val() );
		} );
	}

	/* ------------------------------------------------------------------ */
	/* Sortable section order                                             */
	/* ------------------------------------------------------------------ */

	function initSortables( scope ) {
		$( '[data-pdo-sortable]', scope ).each( function () {
			var list = $( this );

			if ( list.data( 'pdoReady' ) ) {
				return;
			}
			list.data( 'pdoReady', true );

			var field = list.closest( '.pdo-sortable' ).find( '.pdo-sortable__value' );
			if ( ! field.length ) {
				field = list.siblings( '.pdo-sortable__value' );
			}

			function sync() {
				var order = [];
				list.children( '.pdo-sortable__item' ).each( function () {
					var item = $( this );
					var slug = item.data( 'slug' );
					var enabled = item.find( '.pdo-sortable__toggle' ).is( ':checked' );
					item.toggleClass( 'is-disabled', ! enabled );
					order.push( ( enabled ? '' : '_' ) + slug );
				} );
				field.val( order.join( ',' ) ).trigger( 'change' );
			}

			list.sortable( {
				handle: '.pdo-sortable__handle',
				axis: 'y',
				cursor: 'move',
				placeholder: 'pdo-sortable__placeholder',
				forcePlaceholderSize: true,
				update: sync
			} );

			list.on( 'change', '.pdo-sortable__toggle', sync );

			// Keyboard reordering for accessibility.
			list.on( 'keydown', '.pdo-sortable__handle', function ( event ) {
				var item = $( this ).closest( '.pdo-sortable__item' );
				if ( event.key === 'ArrowUp' ) {
					event.preventDefault();
					item.insertBefore( item.prev( '.pdo-sortable__item' ) );
					this.focus();
					sync();
				} else if ( event.key === 'ArrowDown' ) {
					event.preventDefault();
					item.insertAfter( item.next( '.pdo-sortable__item' ) );
					this.focus();
					sync();
				}
			} );
		} );
	}

	/* ------------------------------------------------------------------ */
	/* Boot                                                               */
	/* ------------------------------------------------------------------ */

	$( function () {
		initRanges( document );
		initSortables( document );
	} );

	if ( api && api.bind ) {
		api.bind( 'ready', function () {
			initRanges( document );
			initSortables( document );

			// Re-initialise controls that were rendered lazily when a section
			// is expanded for the first time.
			api.section.each( function ( section ) {
				section.expanded.bind( function ( expanded ) {
					if ( expanded ) {
						window.setTimeout( function () {
							initRanges( section.container );
							initSortables( section.container );
						}, 0 );
					}
				} );
			} );
		} );
	}
} )( window.jQuery, window.wp && window.wp.customize );
