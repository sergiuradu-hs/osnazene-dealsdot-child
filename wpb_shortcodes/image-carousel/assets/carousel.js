( function () {
	'use strict';

	var GAP_PX = 24; // must match CSS gap on .osn-image-carousel__track

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

		// Total width of the ORIGINAL set (items + their trailing gap).
		// Flexbox gap sits *between* items, so for N items there are N-1 internal
		// gaps plus one gap before the first clone — treat it as N gaps total so
		// the visual rhythm is uniform.
		var totalShift = items.reduce( function ( sum, item ) {
			return sum + item.offsetWidth;
		}, 0 ) + items.length * GAP_PX;

		// Animation duration so scroll speed stays constant regardless of image count.
		var duration = totalShift / speed; // seconds

		track.style.setProperty( '--osn-carousel-shift', totalShift + 'px' );
		track.style.setProperty( '--osn-carousel-duration', duration + 's' );
		track.classList.add( 'is-animating' );
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
