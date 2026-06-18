
/**
 * Front-end view script for the Flexible Contact Information Block.
 * Adds copy-to-clipboard functionality when clicking on contact values.
 */
( function () {
	'use strict';

	document.querySelectorAll(
		'.wp-block-telex-block-ingeni-contact-info-block .ingeni-contact-info-block__item'
	).forEach( function ( item ) {
		var valueEl = item.querySelector( '.ingeni-contact-info-block__value' );
		if ( ! valueEl ) {
			return;
		}

		var textContent = valueEl.textContent.trim();
		if ( ! textContent ) {
			return;
		}

		item.style.cursor = 'pointer';
		item.setAttribute( 'title', 'Click to copy' );

		item.addEventListener( 'click', function ( e ) {
			// Don't interfere with link clicks.
			if ( e.target.tagName === 'A' ) {
				return;
			}

			if ( navigator.clipboard && navigator.clipboard.writeText ) {
				navigator.clipboard.writeText( textContent ).then( function () {
					showCopiedFeedback( item );
				} );
			}
		} );
	} );

	function showCopiedFeedback( el ) {
		if ( el.querySelector( '.ingeni-contact-info-block__copied' ) ) {
			return;
		}

		var badge = document.createElement( 'span' );
		badge.className = 'ingeni-contact-info-block__copied';
		badge.textContent = 'Copied!';
		badge.style.cssText =
			'margin-left:0.5em;font-size:0.75em;background:#007cba;color:#fff;padding:0.15em 0.5em;border-radius:3px;opacity:1;transition:opacity 0.4s ease;';
		el.appendChild( badge );

		setTimeout( function () {
			badge.style.opacity = '0';
			setTimeout( function () {
				badge.remove();
			}, 400 );
		}, 1200 );
	}
} )();
