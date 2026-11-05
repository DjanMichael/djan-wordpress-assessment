/**
 * Estate Showcase front-end behaviour.
 *
 * 1. Reviews slider: Bootstrap's carousel moves one .carousel-item at a time, so
 *    the reviews are regrouped per breakpoint — 3 per slide on desktop, 2 on
 *    tablets, 1 on phones — and the dots are rebuilt to match.
 * 2. Favourite buttons on the property cards toggle their pressed state.
 */
( function () {
	'use strict';

	var desktop = window.matchMedia( '(min-width: 992px)' );
	var tablet = window.matchMedia( '(min-width: 768px)' );
	var reducedMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' );

	function reviewsPerSlide() {
		if ( desktop.matches ) {
			return 3;
		}
		return tablet.matches ? 2 : 1;
	}

	function initReviews( carousel ) {
		var inner = carousel.querySelector( '.carousel-inner' );
		var dots = carousel.querySelector( '.carousel-indicators' );
		var reviews = Array.prototype.slice.call( carousel.querySelectorAll( '[data-es-review]' ) );
		var current = 0;

		if ( ! inner || ! dots || ! reviews.length || ! window.bootstrap ) {
			return;
		}

		function build() {
			var perSlide = reviewsPerSlide();
			var slides = Math.ceil( reviews.length / perSlide );

			if ( perSlide === current ) {
				return;
			}
			current = perSlide;

			var existing = window.bootstrap.Carousel.getInstance( carousel );
			if ( existing ) {
				existing.dispose();
			}

			inner.textContent = '';
			dots.textContent = '';

			for ( var i = 0; i < slides; i++ ) {
				var group = reviews.slice( i * perSlide, ( i + 1 ) * perSlide );
				var item = document.createElement( 'div' );
				var row = document.createElement( 'div' );
				var dot = document.createElement( 'button' );

				item.className = 'carousel-item' + ( 0 === i ? ' active' : '' );
				item.setAttribute( 'role', 'group' );
				item.setAttribute( 'aria-roledescription', 'slide' );
				item.setAttribute( 'aria-label', ( i + 1 ) + ' / ' + slides );
				row.className = 'row g-4';
				group.forEach( function ( review ) {
					row.appendChild( review );
				} );
				item.appendChild( row );
				inner.appendChild( item );

				dot.type = 'button';
				dot.setAttribute( 'data-bs-target', '#' + carousel.id );
				dot.setAttribute( 'data-bs-slide-to', String( i ) );
				dot.setAttribute(
					'aria-label',
					'Show reviews ' + ( i * perSlide + 1 ) + ' to ' + ( i * perSlide + group.length ) + ' of ' + reviews.length
				);
				if ( 0 === i ) {
					dot.className = 'active';
					dot.setAttribute( 'aria-current', 'true' );
				}
				dots.appendChild( dot );
			}

			new window.bootstrap.Carousel( carousel, {
				interval: reducedMotion.matches ? false : 6000,
				ride: reducedMotion.matches ? false : 'carousel',
				pause: 'hover',
				touch: true,
				keyboard: true,
			} );
		}

		build();
		[ desktop, tablet ].forEach( function ( query ) {
			if ( query.addEventListener ) {
				query.addEventListener( 'change', build );
			} else {
				query.addListener( build );
			}
		} );
	}

	function initFavourites() {
		document.addEventListener( 'click', function ( event ) {
			var button = event.target.closest( '[data-es-favourite]' );
			if ( ! button ) {
				return;
			}

			var pressed = 'true' !== button.getAttribute( 'aria-pressed' );
			var icon = button.querySelector( 'i' );

			button.setAttribute( 'aria-pressed', String( pressed ) );
			if ( icon ) {
				icon.classList.toggle( 'fa-regular', ! pressed );
				icon.classList.toggle( 'fa-solid', pressed );
			}
		} );
	}

	function init() {
		document.querySelectorAll( '[data-es-reviews]' ).forEach( initReviews );
		initFavourites();
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
