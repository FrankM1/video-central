jQuery( function ( $ ) {
	'use strict';

	$( 'body' ).on( 'change', '.video-central-metaboxes-image-select input', function () {
		var $this = $( this ),
			type = $this.attr( 'type' ),
			selected = $this.is( ':checked' ),
			$parent = $this.parent(),
			$others = $parent.siblings();
		if ( selected ) {
			$parent.addClass( 'video-central-metaboxes-active' );
			if ( type === 'radio' ) {
				$others.removeClass( 'video-central-metaboxes-active' );
			}
		} else {
			$parent.removeClass( 'video-central-metaboxes-active' );
		}
	} );
	$( '.video-central-metaboxes-image-select input' ).trigger( 'change' );
} );
