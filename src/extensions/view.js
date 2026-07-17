import './style.scss';

const SELECTOR = '[data-noortemplates-animation]';

function reveal( el ) {
	const duration = el.getAttribute( 'data-noortemplates-duration' );
	const delay = el.getAttribute( 'data-noortemplates-delay' );

	if ( duration ) {
		el.style.setProperty(
			'--noortemplates-animation-duration',
			duration + 'ms'
		);
	}

	if ( delay ) {
		el.style.setProperty(
			'--noortemplates-animation-delay',
			delay + 'ms'
		);
	}

	el.classList.add( 'is-in-view' );
}

function init() {
	const elements = document.querySelectorAll( SELECTOR );

	if ( ! elements.length ) {
		return;
	}

	if ( ! ( 'IntersectionObserver' in window ) ) {
		elements.forEach( reveal );
		return;
	}

	const observer = new IntersectionObserver(
		( entries, obs ) => {
			entries.forEach( ( entry ) => {
				if ( entry.isIntersecting ) {
					reveal( entry.target );
					obs.unobserve( entry.target );
				}
			} );
		},
		{ threshold: 0.2 }
	);

	elements.forEach( ( el ) => observer.observe( el ) );
}

if ( document.readyState === 'loading' ) {
	document.addEventListener( 'DOMContentLoaded', init );
} else {
	init();
}
