/**
 * PDO Website Manager – admin UI.
 *
 * Repeaters (add / duplicate / delete / drag-reorder / collapse),
 * media pickers, icon picker, language tabs, colour pickers and the
 * drag-and-drop content ordering screen.
 */
( function ( $ ) {
	'use strict';

	var settings = window.pdoSMAdmin || { i18n: {} };

	/* ------------------------------------------------------------------ */
	/* Helpers                                                            */
	/* ------------------------------------------------------------------ */

	function reindexRepeater( $repeater ) {
		var base = $repeater.data( 'name' );
		if ( ! base ) {
			return;
		}
		var pattern = new RegExp( '^' + base.replace( /[.*+?^${}()|[\]\\]/g, '\\$&' ) + '\\[(\\d+|__INDEX__)\\]' );

		$repeater.children( '.pdo-repeater__rows' ).children( '.pdo-repeater__row' ).each( function ( index ) {
			$( this )
				.find( 'input, textarea, select' )
				.each( function () {
					var name = this.getAttribute( 'name' );
					if ( ! name ) {
						return;
					}
					this.setAttribute( 'name', name.replace( pattern, base + '[' + index + ']' ) );
				} );

			$( this )
				.find( '[name$="[order]"]' )
				.filter( function () {
					return this.type === 'number' || this.type === 'hidden';
				} )
				.first()
				.val( index );
		} );

		updateCount( $repeater );
	}

	function updateCount( $repeater ) {
		var total = $repeater.children( '.pdo-repeater__rows' ).children( '.pdo-repeater__row' ).length;
		$repeater.find( '> .pdo-repeater__head .pdo-repeater__count' ).text( total ? total : '' );
	}

	function refreshRowTitle( $row ) {
		var $repeater = $row.closest( '.pdo-repeater' );
		var labelKey = $repeater.data( 'label-field' );
		if ( ! labelKey ) {
			return;
		}
		var $input = $row.find( '[name$="[' + labelKey + ']"]' ).first();
		if ( ! $input.length ) {
			return;
		}
		var value = $.trim( String( $input.val() || '' ) ).replace( /<[^>]*>/g, '' );
		$row.find( '> .pdo-repeater__row-head .pdo-repeater__title' ).text( value || '(untitled item)' );
	}

	function refreshHiddenFlag( $row ) {
		var $toggle = $row.find( '[name$="[visible]"]' ).filter( ':checkbox' ).first();
		if ( ! $toggle.length ) {
			return;
		}
		$row.toggleClass( 'is-hidden-row', ! $toggle.is( ':checked' ) );
	}

	function initColorPickers( $scope ) {
		if ( ! $.fn.wpColorPicker ) {
			return;
		}
		$scope.find( '.pdo-color-input' ).not( '.wp-color-picker' ).each( function () {
			$( this ).wpColorPicker( {
				change: function () {
					$( this ).trigger( 'pdo:changed' );
				}
			} );
		} );
	}

	function initSortable( $repeater ) {
		if ( ! $.fn.sortable ) {
			return;
		}
		var $rows = $repeater.children( '.pdo-repeater__rows' );
		if ( $rows.data( 'ui-sortable' ) ) {
			$rows.sortable( 'refresh' );
			return;
		}
		$rows.sortable( {
			handle: '.pdo-repeater__handle',
			items: '> .pdo-repeater__row',
			axis: 'y',
			placeholder: 'pdo-repeater__placeholder',
			forcePlaceholderSize: true,
			update: function () {
				reindexRepeater( $repeater );
			}
		} );
	}

	/* ------------------------------------------------------------------ */
	/* Repeaters                                                          */
	/* ------------------------------------------------------------------ */

	function initRepeaters( $scope ) {
		$scope.find( '[data-pdo-repeater]' ).each( function () {
			var $repeater = $( this );
			if ( $repeater.data( 'pdo-ready' ) ) {
				return;
			}
			$repeater.data( 'pdo-ready', true );
			initSortable( $repeater );
			updateCount( $repeater );
			$repeater.children( '.pdo-repeater__rows' ).children( '.pdo-repeater__row' ).each( function () {
				refreshHiddenFlag( $( this ) );
			} );
		} );
	}

	$( document ).on( 'click', '.pdo-repeater__add', function ( event ) {
		event.preventDefault();
		var $repeater = $( this ).closest( '.pdo-repeater' );
		var template = $repeater.children( 'script.pdo-repeater__template' ).html() || '';
		var index = $repeater.children( '.pdo-repeater__rows' ).children( '.pdo-repeater__row' ).length;
		var html = template.replace( /__INDEX__/g, String( index ) );
		var $row = $( $.parseHTML( html ) ).filter( '.pdo-repeater__row' );

		$repeater.children( '.pdo-repeater__rows' ).append( $row );
		$row.find( '> .pdo-repeater__row-body' ).prop( 'hidden', false );
		$row.find( '> .pdo-repeater__row-head .pdo-repeater__toggle' ).attr( 'aria-expanded', 'true' );
		$row.addClass( 'is-open' );

		initColorPickers( $row );
		reindexRepeater( $repeater );
		initSortable( $repeater );
		refreshHiddenFlag( $row );
		$row.find( 'input, textarea, select' ).filter( ':visible' ).first().trigger( 'focus' );
	} );

	$( document ).on( 'click', '.pdo-repeater__remove', function ( event ) {
		event.preventDefault();
		if ( ! window.confirm( settings.i18n.confirmRemove || 'Remove this item?' ) ) {
			return;
		}
		var $row = $( this ).closest( '.pdo-repeater__row' );
		var $repeater = $row.closest( '.pdo-repeater' );
		$row.remove();
		reindexRepeater( $repeater );
	} );

	$( document ).on( 'click', '.pdo-repeater__duplicate', function ( event ) {
		event.preventDefault();
		var $row = $( this ).closest( '.pdo-repeater__row' );
		var $repeater = $row.closest( '.pdo-repeater' );

		// Detach any colour picker wrappers before cloning.
		var $clone = $row.clone();
		$clone.find( '.wp-picker-container' ).each( function () {
			var $wrap = $( this );
			var $input = $wrap.find( '.pdo-color-input' ).first();
			$input.removeClass( 'wp-color-picker' ).removeAttr( 'style' ).show();
			$wrap.replaceWith( $input );
		} );

		// Carry over the live values of the originals.
		$row.find( 'input, textarea, select' ).each( function ( i ) {
			var $target = $clone.find( 'input, textarea, select' ).eq( i );
			if ( ! $target.length ) {
				return;
			}
			if ( this.type === 'checkbox' || this.type === 'radio' ) {
				$target.prop( 'checked', $( this ).prop( 'checked' ) );
			} else {
				$target.val( $( this ).val() );
			}
		} );

		$row.after( $clone );
		initColorPickers( $clone );
		reindexRepeater( $repeater );
		initSortable( $repeater );
		refreshRowTitle( $clone );
		refreshHiddenFlag( $clone );
	} );

	$( document ).on( 'click', '.pdo-repeater__toggle', function ( event ) {
		event.preventDefault();
		var $row = $( this ).closest( '.pdo-repeater__row' );
		var $body = $row.children( '.pdo-repeater__row-body' );
		var open = $body.prop( 'hidden' );
		$body.prop( 'hidden', ! open );
		$row.toggleClass( 'is-open', open );
		$( this ).attr( 'aria-expanded', open ? 'true' : 'false' );
	} );

	$( document ).on( 'input change', '.pdo-repeater__row input, .pdo-repeater__row textarea, .pdo-repeater__row select', function () {
		refreshRowTitle( $( this ).closest( '.pdo-repeater__row' ) );
	} );

	$( document ).on( 'change', '.pdo-repeater__row [name$="[visible]"]', function () {
		refreshHiddenFlag( $( this ).closest( '.pdo-repeater__row' ) );
	} );

	/* ------------------------------------------------------------------ */
	/* Media picker                                                       */
	/* ------------------------------------------------------------------ */

	$( document ).on( 'click', '.pdo-media__select', function ( event ) {
		event.preventDefault();

		if ( ! window.wp || ! window.wp.media ) {
			return;
		}

		var $wrap = $( this ).closest( '[data-pdo-media]' );
		var mode = $wrap.data( 'mode' ) === 'file' ? 'file' : 'image';
		var frame = wp.media( {
			title: mode === 'image' ? settings.i18n.selectImage : settings.i18n.selectFile,
			button: { text: mode === 'image' ? settings.i18n.useImage : settings.i18n.useFile },
			library: mode === 'image' ? { type: 'image' } : {},
			multiple: false
		} );

		frame.on( 'select', function () {
			var attachment = frame.state().get( 'selection' ).first().toJSON();
			$wrap.find( '.pdo-media__input' ).val( attachment.id ).trigger( 'change' );

			var preview = '';
			if ( attachment.type === 'image' ) {
				var src = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;
				preview = $( '<img>' ).attr( { src: src, alt: '' } );
			} else {
				preview = $( '<span class="pdo-media__file"></span>' ).text( attachment.filename || attachment.title );
			}

			$wrap.find( '.pdo-media__preview' ).empty().append( preview ).prop( 'hidden', false );
			$wrap.find( '.pdo-media__remove' ).prop( 'hidden', false );
		} );

		frame.open();
	} );

	$( document ).on( 'click', '.pdo-media__remove', function ( event ) {
		event.preventDefault();
		var $wrap = $( this ).closest( '[data-pdo-media]' );
		$wrap.find( '.pdo-media__input' ).val( '' ).trigger( 'change' );
		$wrap.find( '.pdo-media__preview' ).empty().prop( 'hidden', true );
		$( this ).prop( 'hidden', true );
	} );

	/* ------------------------------------------------------------------ */
	/* Icon picker (one shared popover, built lazily from the icon map)   */
	/* ------------------------------------------------------------------ */

	var iconPanel = null;
	var iconOwner = null;

	function iconSvg( path ) {
		return (
			'<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" ' +
			'stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' +
			path +
			'</svg>'
		);
	}

	function buildIconPanel() {
		if ( iconPanel ) {
			return iconPanel;
		}

		iconPanel = $(
			'<div class="pdo-iconpicker__panel" role="dialog" hidden>' +
				'<input type="search" class="pdo-iconpicker__search" />' +
				'<div class="pdo-iconpicker__grid"></div>' +
			'</div>'
		);
		iconPanel.find( '.pdo-iconpicker__search' ).attr( 'placeholder', settings.i18n.searchIcons || 'Search icons…' );

		var icons = settings.icons || {};
		var html = [];
		Object.keys( icons ).forEach( function ( name ) {
			var icon = icons[ name ];
			html.push(
				'<button type="button" class="pdo-iconpicker__item" data-icon="' + name + '" data-label="' +
					String( icon.label + ' ' + name + ' ' + ( icon.group || '' ) ).replace( /"/g, '' ) +
					'" title="' + String( icon.label ).replace( /"/g, '' ) + '">' +
					iconSvg( icon.path ) +
				'</button>'
			);
		} );
		iconPanel.find( '.pdo-iconpicker__grid' ).html( html.join( '' ) );
		$( 'body' ).append( iconPanel );

		return iconPanel;
	}

	function closeIconPanel() {
		if ( iconPanel ) {
			iconPanel.prop( 'hidden', true );
		}
		if ( iconOwner ) {
			iconOwner.find( '.pdo-iconpicker__toggle' ).attr( 'aria-expanded', 'false' );
		}
		iconOwner = null;
	}

	$( document ).on( 'click', '.pdo-iconpicker__toggle', function ( event ) {
		event.preventDefault();
		event.stopPropagation();

		var $wrap = $( this ).closest( '[data-pdo-iconpicker]' );
		if ( iconOwner && iconOwner[ 0 ] === $wrap[ 0 ] && iconPanel && ! iconPanel.prop( 'hidden' ) ) {
			closeIconPanel();
			return;
		}

		var $panel = buildIconPanel();
		iconOwner = $wrap;

		var current = String( $wrap.find( '.pdo-iconpicker__input' ).val() || '' );
		$panel.find( '.pdo-iconpicker__item' ).each( function () {
			this.hidden = false;
			$( this ).toggleClass( 'is-selected', this.getAttribute( 'data-icon' ) === current );
		} );
		$panel.find( '.pdo-iconpicker__search' ).val( '' );

		var offset = $( this ).offset();
		$panel
			.css( {
				top: offset.top + $( this ).outerHeight() + 6 + 'px',
				left: Math.min( offset.left, $( window ).width() - 350 ) + 'px'
			} )
			.prop( 'hidden', false );

		$( this ).attr( 'aria-expanded', 'true' );
		$panel.find( '.pdo-iconpicker__search' ).trigger( 'focus' );
	} );

	$( document ).on( 'input', '.pdo-iconpicker__search', function () {
		var term = String( $( this ).val() || '' ).toLowerCase();
		$( this )
			.siblings( '.pdo-iconpicker__grid' )
			.children( '.pdo-iconpicker__item' )
			.each( function () {
				var label = String( this.getAttribute( 'data-label' ) || '' ).toLowerCase();
				this.hidden = term !== '' && label.indexOf( term ) === -1;
			} );
	} );

	$( document ).on( 'click', '.pdo-iconpicker__item', function ( event ) {
		event.preventDefault();
		if ( ! iconOwner ) {
			return;
		}
		var $item = $( this );
		var name = String( $item.data( 'icon' ) );

		iconOwner.find( '.pdo-iconpicker__input' ).val( name ).trigger( 'change' );
		iconOwner.find( '.pdo-iconpicker__current' ).html( $item.html() );
		iconOwner.find( '.pdo-iconpicker__name' ).text( name );
		iconOwner.find( '.pdo-iconpicker__clear' ).prop( 'hidden', false );

		closeIconPanel();
	} );

	$( document ).on( 'click', '.pdo-iconpicker__clear', function ( event ) {
		event.preventDefault();
		var $wrap = $( this ).closest( '[data-pdo-iconpicker]' );
		$wrap.find( '.pdo-iconpicker__input' ).val( '' ).trigger( 'change' );
		$wrap.find( '.pdo-iconpicker__current' ).text( 'Choose icon' );
		$wrap.find( '.pdo-iconpicker__name' ).text( '' );
		$( this ).prop( 'hidden', true );
	} );

	$( document ).on( 'click', function ( event ) {
		if ( ! $( event.target ).closest( '.pdo-iconpicker__panel, [data-pdo-iconpicker]' ).length ) {
			closeIconPanel();
		}
	} );

	$( document ).on( 'keydown', function ( event ) {
		if ( event.key === 'Escape' ) {
			closeIconPanel();
		}
	} );

	/* ------------------------------------------------------------------ */
	/* Language tabs                                                      */
	/* ------------------------------------------------------------------ */

	$( document ).on( 'click', '.pdo-langtabs__tab', function ( event ) {
		event.preventDefault();
		var $tab = $( this );
		var $wrap = $tab.closest( '[data-pdo-langtabs]' );
		var lang = $tab.data( 'lang' );

		$wrap.find( '> .pdo-langtabs__nav .pdo-langtabs__tab' ).removeClass( 'is-active' ).attr( 'aria-selected', 'false' );
		$tab.addClass( 'is-active' ).attr( 'aria-selected', 'true' );
		$wrap.find( '> .pdo-langtabs__panel' ).each( function () {
			this.hidden = $( this ).data( 'lang' ) !== lang;
		} );
	} );

	/* ------------------------------------------------------------------ */
	/* Submissions screen                                                 */
	/* ------------------------------------------------------------------ */

	$( document ).on( 'change', '#pdo-check-all', function () {
		$( '.pdo-submissions-table tbody input[type="checkbox"]' ).prop( 'checked', $( this ).is( ':checked' ) );
	} );

	/* ------------------------------------------------------------------ */
	/* Content reordering                                                 */
	/* ------------------------------------------------------------------ */

	function initReorder() {
		var $list = $( '[data-pdo-reorder]' );
		if ( ! $list.length || ! $.fn.sortable ) {
			return;
		}
		var $status = $( '[data-pdo-reorder-status]' );

		$list.sortable( {
			handle: '.pdo-reorder__handle',
			items: '> .pdo-reorder__item',
			axis: 'y',
			placeholder: 'pdo-reorder__placeholder',
			forcePlaceholderSize: true,
			update: function () {
				var ids = $list
					.children( '.pdo-reorder__item' )
					.map( function () {
						return $( this ).data( 'id' );
					} )
					.get();

				$status.removeClass( 'is-error' ).text( '…' );

				$.post( settings.ajaxUrl, {
					action: 'pdo_sm_reorder',
					nonce: settings.reorderNonce,
					ids: ids
				} )
					.done( function ( response ) {
						if ( response && response.success ) {
							$status.text( settings.i18n.saved );
						} else {
							$status.addClass( 'is-error' ).text( settings.i18n.saveFailed );
						}
					} )
					.fail( function () {
						$status.addClass( 'is-error' ).text( settings.i18n.saveFailed );
					} );
			}
		} );
	}

	/* ------------------------------------------------------------------ */
	/* Boot                                                               */
	/* ------------------------------------------------------------------ */

	$( function () {
		var $document = $( document );
		initRepeaters( $document );
		initColorPickers( $document );
		initReorder();

		$( '.pdo-repeater__row' ).each( function () {
			refreshRowTitle( $( this ) );
		} );
	} );
} )( window.jQuery );
