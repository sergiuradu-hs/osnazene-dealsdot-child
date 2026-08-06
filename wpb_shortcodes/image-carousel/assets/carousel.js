( function () {
	'use strict';

	var GAP_PX = 24; // must match CSS gap on .osn-image-carousel__track
	var mobileQuery = window.matchMedia( '(max-width: 767px)' );

	function initCarousel( carousel ) {
		var track  = carousel.querySelector( '.osn-image-carousel__track' );
		var items  = Array.from( track.children );
		var speed  = parseFloat( carousel.dataset.speed ) || 80; // px per second

		if ( ! items.length ) {
			return;
		}

		// Clone every item and append so the strip is seamless.
		items.forEach( function ( item ) {
			track.appendChild( item.cloneNode( true ) );
		} );

		function measure() {
			// Total width of the ORIGINAL set (items + their trailing gap).
			// Flexbox gap sits *between* items, so for N items there are N-1 internal
			// gaps plus one gap before the first clone — treat it as N gaps total so
			// the visual rhythm is uniform.
			var totalShift = items.reduce( function ( sum, item ) {
				return sum + item.offsetWidth;
			}, 0 ) + items.length * GAP_PX;

			// Animation duration so scroll speed stays constant regardless of image count.
			var duration = totalShift / speed; // seconds

			track.classList.remove( 'is-animating' );
			track.style.setProperty( '--osn-carousel-shift', totalShift + 'px' );
			track.style.setProperty( '--osn-carousel-duration', duration + 's' );
			// Force a reflow so the animation restarts cleanly with new values.
			void track.offsetWidth;
			track.classList.add( 'is-animating' );
		}

		measure();

		// Image sizes change across the mobile breakpoint (mobile height
		// option), so re-measure and restart when it is crossed.
		var onBreakpointChange = function () {
			measure();
		};
		if ( typeof mobileQuery.addEventListener === 'function' ) {
			mobileQuery.addEventListener( 'change', onBreakpointChange );
		} else if ( typeof mobileQuery.addListener === 'function' ) {
			mobileQuery.addListener( onBreakpointChange );
		}
	}

	function init() {
		document.querySelectorAll( '.osn-image-carousel' ).forEach( initCarousel );
	}

	// Wait for images to be decoded so offsetWidth is correct.
	if ( document.readyState === 'complete' ) {
		init();
	} else {
		window.addEventListener( 'load', init );
	}
} )();
