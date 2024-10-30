function jpForTwentyTwentyOneSmoothScroll( target, offset ) {
	const targetRect = target.getBoundingClientRect();
	const targetY = targetRect.top + window.pageYOffset - offset;
	window.scrollTo( { left: 0, top: targetY, behavior: jp_for_twentytwentyone_scrolltop['scrolltop_scroll'] } );
}

function jpForTwentyTwentyOneScrollTop() {
	const wpadminbar = document.getElementById('wpadminbar');
	const smoothOffset = ( wpadminbar ? wpadminbar.clientHeight : 0 ) + 2;
	const target = document.body;
	jpForTwentyTwentyOneSmoothScroll( target, smoothOffset );
}

function scrollTopRepositionTogglerOnScroll() {
	var toggler = document.getElementById( 'scroll-top-toggler' ),
		prevScroll = window.scrollY || document.documentElement.scrollTop,
		currentScroll,

		checkScroll = function() {
			currentScroll = window.scrollY || document.documentElement.scrollTop;
			if (
				currentScroll + ( window.innerHeight * 1.5 ) > document.body.clientHeight ||
				currentScroll < prevScroll
			) {
				toggler.classList.remove( 'hide' );
			} else if ( currentScroll > prevScroll && 250 < currentScroll ) {
				toggler.classList.add( 'hide' );
			}
			prevScroll = currentScroll;
		};
	if ( toggler ) {
		window.addEventListener( 'scroll', checkScroll );
	}
}

scrollTopRepositionTogglerOnScroll();

var scrollTopToggler = document.getElementById( 'scroll-top-toggler' )
var darkModeToggler = document.getElementById( 'dark-mode-toggler' );
if ( darkModeToggler ) {
	darkModeToggler.style.right = ( scrollTopToggler.getBoundingClientRect().width + 16 + 8 ) + 'px';
}
