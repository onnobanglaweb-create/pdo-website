/**
 * PDO Modern – front-end behaviour.
 *
 * Vanilla JavaScript only: no jQuery, no framework, no CDN.
 */
( function () {
	'use strict';

	var settings = window.pdoModern || {};
	var reduceMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	function $( selector, scope ) {
		return ( scope || document ).querySelector( selector );
	}

	function $$( selector, scope ) {
		return Array.prototype.slice.call( ( scope || document ).querySelectorAll( selector ) );
	}

	/* ------------------------------------------------------------------ */
	/* Sticky header                                                      */
	/* ------------------------------------------------------------------ */

	function initStickyHeader() {
		var header = $( '[data-pdo-sticky]' );
		if ( ! header ) {
			return;
		}

		var spacer = document.createElement( 'div' );
		spacer.className = 'pdo-header__spacer';
		header.parentNode.insertBefore( spacer, header.nextSibling );

		var threshold = header.offsetTop + 40;
		var stuck = false;

		function update() {
			var shouldStick = window.scrollY > threshold;
			if ( shouldStick === stuck ) {
				return;
			}
			stuck = shouldStick;
			header.classList.toggle( 'is-stuck', stuck );
			spacer.style.height = stuck ? header.offsetHeight + 'px' : '0px';
		}

		window.addEventListener( 'scroll', update, { passive: true } );
		window.addEventListener( 'resize', function () {
			threshold = stuck ? threshold : header.offsetTop + 40;
			if ( stuck ) {
				spacer.style.height = header.offsetHeight + 'px';
			}
		} );
		update();
	}

	/* ------------------------------------------------------------------ */
	/* Mobile drawer                                                      */
	/* ------------------------------------------------------------------ */

	function initDrawer() {
		var drawer = $( '[data-pdo-drawer]' );
		var toggles = $$( '[data-pdo-drawer-toggle]' );

		if ( ! drawer || ! toggles.length ) {
			return;
		}

		var lastFocused = null;

		function focusable() {
			return $$( 'a[href], button:not([disabled]), input, select, textarea', drawer ).filter( function ( el ) {
				return el.offsetParent !== null;
			} );
		}

		function open() {
			lastFocused = document.activeElement;
			drawer.hidden = false;
			// Force a reflow so the CSS transition runs.
			void drawer.offsetWidth;
			drawer.classList.add( 'is-open' );
			document.body.style.overflow = 'hidden';
			toggles.forEach( function ( btn ) {
				btn.setAttribute( 'aria-expanded', 'true' );
			} );
			var first = focusable()[ 0 ];
			if ( first ) {
				first.focus();
			}
		}

		function close() {
			drawer.classList.remove( 'is-open' );
			document.body.style.overflow = '';
			toggles.forEach( function ( btn ) {
				btn.setAttribute( 'aria-expanded', 'false' );
			} );
			window.setTimeout( function () {
				drawer.hidden = true;
			}, 280 );
			if ( lastFocused ) {
				lastFocused.focus();
			}
		}

		toggles.forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				if ( drawer.classList.contains( 'is-open' ) ) {
					close();
				} else {
					open();
				}
			} );
		} );

		$$( '[data-pdo-drawer-close]', drawer ).forEach( function ( btn ) {
			btn.addEventListener( 'click', close );
		} );

		document.addEventListener( 'keydown', function ( event ) {
			if ( ! drawer.classList.contains( 'is-open' ) ) {
				return;
			}
			if ( event.key === 'Escape' ) {
				close();
				return;
			}
			if ( event.key !== 'Tab' ) {
				return;
			}
			var items = focusable();
			if ( ! items.length ) {
				return;
			}
			var first = items[ 0 ];
			var last = items[ items.length - 1 ];
			if ( event.shiftKey && document.activeElement === first ) {
				event.preventDefault();
				last.focus();
			} else if ( ! event.shiftKey && document.activeElement === last ) {
				event.preventDefault();
				first.focus();
			}
		} );

		// Close the drawer when the viewport grows past the breakpoint.
		window.addEventListener( 'resize', function () {
			if ( window.innerWidth > 1400 && drawer.classList.contains( 'is-open' ) ) {
				close();
			}
		} );
	}

	/* ------------------------------------------------------------------ */
	/* Submenu disclosure (mobile + keyboard on desktop)                  */
	/* ------------------------------------------------------------------ */

	function initSubmenus() {
		$$( '.pdo-submenu-toggle' ).forEach( function ( toggle ) {
			toggle.addEventListener( 'click', function ( event ) {
				event.preventDefault();
				var item = toggle.closest( '.pdo-menu-item' );
				if ( ! item ) {
					return;
				}
				var open = item.classList.toggle( 'is-open' );
				toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			} );
		} );

		// Desktop: close any open dropdown with Escape.
		document.addEventListener( 'keydown', function ( event ) {
			if ( event.key !== 'Escape' ) {
				return;
			}
			$$( '.pdo-nav--desktop .pdo-menu-item.is-open' ).forEach( function ( item ) {
				item.classList.remove( 'is-open' );
			} );
		} );

		// Desktop: keep the dropdown open while focus is inside it.
		$$( '.pdo-nav--desktop .pdo-has-children' ).forEach( function ( item ) {
			item.addEventListener( 'focusin', function () {
				item.classList.add( 'is-open' );
			} );
			item.addEventListener( 'focusout', function ( event ) {
				if ( ! item.contains( event.relatedTarget ) ) {
					item.classList.remove( 'is-open' );
				}
			} );
		} );
	}

	/* ------------------------------------------------------------------ */
	/* Search panel                                                       */
	/* ------------------------------------------------------------------ */

	function initSearch() {
		var toggle = $( '[data-pdo-search-toggle]' );
		var panel = $( '#pdo-search-panel' );
		if ( ! toggle || ! panel ) {
			return;
		}

		toggle.addEventListener( 'click', function () {
			var open = panel.hidden;
			panel.hidden = ! open;
			toggle.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
			if ( open ) {
				var field = $( 'input[type="search"]', panel );
				if ( field ) {
					field.focus();
				}
			}
		} );

		document.addEventListener( 'keydown', function ( event ) {
			if ( event.key === 'Escape' && ! panel.hidden ) {
				panel.hidden = true;
				toggle.setAttribute( 'aria-expanded', 'false' );
				toggle.focus();
			}
		} );
	}

	/* ------------------------------------------------------------------ */
	/* Reveal on scroll + animated counters + bars                        */
	/* ------------------------------------------------------------------ */

	function animateCounter( el ) {
		var target = parseFloat( el.getAttribute( 'data-pdo-count' ) );
		if ( isNaN( target ) ) {
			return;
		}
		var raw = el.textContent;
		var decimals = ( String( target ).split( '.' )[ 1 ] || '' ).length;
		var duration = 1400;
		var started = null;

		function step( timestamp ) {
			if ( ! started ) {
				started = timestamp;
			}
			var progress = Math.min( ( timestamp - started ) / duration, 1 );
			var eased = 1 - Math.pow( 1 - progress, 3 );
			var value = target * eased;
			el.textContent = decimals
				? value.toFixed( decimals )
				: Math.round( value ).toLocaleString( settings.lang === 'bn' ? 'bn-BD' : undefined );
			if ( progress < 1 ) {
				window.requestAnimationFrame( step );
			} else {
				el.textContent = raw;
			}
		}

		window.requestAnimationFrame( step );
	}

	function initObservers() {
		var reveals = $$( '[data-pdo-reveal]' );
		var counters = $$( '[data-pdo-count]' );
		var bars = $$( '.pdo-bar' );

		if ( reduceMotion || settings.animations === false || ! ( 'IntersectionObserver' in window ) ) {
			reveals.forEach( function ( el ) {
				el.classList.add( 'is-visible' );
			} );
			bars.forEach( function ( el ) {
				el.classList.add( 'is-visible' );
			} );
			return;
		}

		var observer = new IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( ! entry.isIntersecting ) {
						return;
					}
					var el = entry.target;
					el.classList.add( 'is-visible' );

					if ( el.hasAttribute( 'data-pdo-count' ) ) {
						animateCounter( el );
					}

					$$( '[data-pdo-count]', el ).forEach( animateCounter );

					observer.unobserve( el );
				} );
			},
			{ rootMargin: '0px 0px -8% 0px', threshold: 0.12 }
		);

		reveals.concat( counters ).concat( bars ).forEach( function ( el ) {
			observer.observe( el );
		} );
	}

	/* ------------------------------------------------------------------ */
	/* Tabs                                                               */
	/* ------------------------------------------------------------------ */

	function initTabs() {
		$$( '[data-pdo-tabs]' ).forEach( function ( group ) {
			var buttons = $$( '[role="tab"]', group );
			if ( ! buttons.length ) {
				return;
			}

			function activate( button, setFocus ) {
				buttons.forEach( function ( other ) {
					var selected = other === button;
					other.setAttribute( 'aria-selected', selected ? 'true' : 'false' );
					other.setAttribute( 'tabindex', selected ? '0' : '-1' );
					var panel = document.getElementById( other.getAttribute( 'aria-controls' ) );
					if ( panel ) {
						panel.hidden = ! selected;
					}
				} );
				if ( setFocus ) {
					button.focus();
				}
			}

			buttons.forEach( function ( button, index ) {
				button.addEventListener( 'click', function () {
					activate( button, false );
					if ( history.replaceState ) {
						history.replaceState( null, '', '#' + button.getAttribute( 'aria-controls' ) );
					}
				} );

				button.addEventListener( 'keydown', function ( event ) {
					var next = null;
					if ( event.key === 'ArrowRight' ) {
						next = buttons[ ( index + 1 ) % buttons.length ];
					} else if ( event.key === 'ArrowLeft' ) {
						next = buttons[ ( index - 1 + buttons.length ) % buttons.length ];
					} else if ( event.key === 'Home' ) {
						next = buttons[ 0 ];
					} else if ( event.key === 'End' ) {
						next = buttons[ buttons.length - 1 ];
					}
					if ( next ) {
						event.preventDefault();
						activate( next, true );
					}
				} );
			} );

			// Honour a deep link such as #pdo-tab-partner.
			if ( window.location.hash ) {
				var target = buttons.filter( function ( button ) {
					return '#' + button.getAttribute( 'aria-controls' ) === window.location.hash;
				} )[ 0 ];
				if ( target ) {
					activate( target, false );
				}
			}
		} );
	}

	/* ------------------------------------------------------------------ */
	/* Adopt-a-project → prefill the inquiry form                         */
	/* ------------------------------------------------------------------ */

	function initPrefill() {
		$$( '[data-pdo-prefill]' ).forEach( function ( trigger ) {
			trigger.addEventListener( 'click', function ( event ) {
				var project = trigger.getAttribute( 'data-pdo-prefill' );
				var formType = trigger.getAttribute( 'data-pdo-form' ) || 'partner';
				var form = $( '#pdo-form-' + formType );

				if ( ! form ) {
					return; // Let the link navigate normally.
				}

				event.preventDefault();

				var tabButton = $( '[aria-controls="pdo-tabpanel-' + formType + '"]' );
				if ( tabButton ) {
					tabButton.click();
				}

				var hidden = $( 'input[name="pdo_project"]', form );
				if ( hidden ) {
					hidden.value = project;
				}

				var notice = $( '[data-pdo-prefill-notice]', form );
				if ( notice ) {
					notice.textContent = notice.getAttribute( 'data-template' ).replace( '%s', project );
					notice.hidden = false;
				}

				var message = form.querySelector( 'textarea' );
				if ( message && ! message.value ) {
					var template = message.getAttribute( 'data-pdo-prefill-template' );
					if ( template ) {
						message.value = template.replace( '%s', project );
					}
				}

				form.scrollIntoView( { behavior: reduceMotion ? 'auto' : 'smooth', block: 'start' } );
				var firstField = form.querySelector( 'input:not([type="hidden"]), textarea, select' );
				if ( firstField ) {
					window.setTimeout( function () {
						firstField.focus();
					}, 400 );
				}
			} );
		} );
	}

	/* ------------------------------------------------------------------ */
	/* Client-side news filtering (progressive enhancement)               */
	/* ------------------------------------------------------------------ */

	function initFilters() {
		$$( '[data-pdo-filter-group]' ).forEach( function ( group ) {
			var buttons = $$( '[data-pdo-filter]', group );
			var targetSelector = group.getAttribute( 'data-pdo-filter-target' );
			var items = targetSelector ? $$( targetSelector ) : [];
			var empty = $( '[data-pdo-filter-empty]' );

			if ( ! buttons.length || ! items.length ) {
				return;
			}

			buttons.forEach( function ( button ) {
				button.addEventListener( 'click', function ( event ) {
					event.preventDefault();
					var filter = button.getAttribute( 'data-pdo-filter' );
					var shown = 0;

					buttons.forEach( function ( other ) {
						other.classList.toggle( 'is-active', other === button );
						other.setAttribute( 'aria-pressed', other === button ? 'true' : 'false' );
					} );

					items.forEach( function ( item ) {
						var terms = ( item.getAttribute( 'data-pdo-terms' ) || '' ).split( ' ' );
						var match = filter === 'all' || terms.indexOf( filter ) !== -1;
						item.hidden = ! match;
						if ( match ) {
							shown++;
						}
					} );

					if ( empty ) {
						empty.hidden = shown > 0;
					}
				} );
			} );
		} );
	}

	/* ------------------------------------------------------------------ */
	/* Back to top                                                        */
	/* ------------------------------------------------------------------ */

	function initBackToTop() {
		var button = $( '[data-pdo-totop]' );
		if ( ! button ) {
			return;
		}

		button.hidden = false;

		function update() {
			button.classList.toggle( 'is-visible', window.scrollY > 500 );
		}

		window.addEventListener( 'scroll', update, { passive: true } );
		button.addEventListener( 'click', function () {
			window.scrollTo( { top: 0, behavior: reduceMotion ? 'auto' : 'smooth' } );
		} );
		update();
	}

	/* ------------------------------------------------------------------ */
	/* Bar widths                                                         */
	/* ------------------------------------------------------------------ */

	function initBars() {
		$$( '.pdo-bar' ).forEach( function ( bar ) {
			var value = bar.getAttribute( 'data-pdo-bar' );
			if ( value ) {
				bar.style.setProperty( '--pdo-bar-width', value + '%' );
			}
		} );
	}

	/* ------------------------------------------------------------------ */
	/* Lazy map: only load the iframe once it scrolls into view           */
	/* ------------------------------------------------------------------ */

	function initLazyMap() {
		var holders = $$( '[data-pdo-map-src]' );
		if ( ! holders.length ) {
			return;
		}

		function load( holder ) {
			var src = holder.getAttribute( 'data-pdo-map-src' );
			if ( ! src || holder.querySelector( 'iframe' ) ) {
				return;
			}
			var frame = document.createElement( 'iframe' );
			frame.src = src;
			frame.loading = 'lazy';
			frame.title = holder.getAttribute( 'data-pdo-map-title' ) || 'Map';
			frame.setAttribute( 'referrerpolicy', 'no-referrer-when-downgrade' );
			holder.insertBefore( frame, holder.firstChild );
		}

		if ( ! ( 'IntersectionObserver' in window ) ) {
			holders.forEach( load );
			return;
		}

		var observer = new IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						load( entry.target );
						observer.unobserve( entry.target );
					}
				} );
			},
			{ rootMargin: '250px' }
		);

		holders.forEach( function ( holder ) {
			observer.observe( holder );
		} );
	}

	/* ------------------------------------------------------------------ */
	/* Honest form UX: disable the button while the browser posts         */
	/* ------------------------------------------------------------------ */

	function initForms() {
		$$( 'form[data-pdo-form]' ).forEach( function ( form ) {
			// Stamp the render time so the server side time-trap can work even
			// when the page is served from a cache.
			var stamp = form.querySelector( 'input[name="pdo_ts"]' );
			if ( stamp && ! stamp.value ) {
				stamp.value = Math.floor( Date.now() / 1000 );
			}

			form.addEventListener( 'submit', function () {
				var button = form.querySelector( '[type="submit"]' );
				if ( button ) {
					button.classList.add( 'is-disabled' );
					button.setAttribute( 'aria-busy', 'true' );
				}
			} );
		} );

		// Move focus to the response so screen readers announce it.
		var alert = $( '[data-pdo-form-result]' );
		if ( alert ) {
			alert.setAttribute( 'tabindex', '-1' );
			alert.focus( { preventScroll: true } );
		}
	}

	/* ------------------------------------------------------------------ */
	/* Boot                                                               */
	/* ------------------------------------------------------------------ */

	function boot() {
		initStickyHeader();
		initDrawer();
		initSubmenus();
		initSearch();
		initObservers();
		initTabs();
		initPrefill();
		initFilters();
		initBackToTop();
		initBars();
		initLazyMap();
		initForms();
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
} )();
