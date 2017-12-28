jQuery( function ( $ ) {
	'use strict';

	/**
	 * Update color picker element
	 * Used for static & dynamic added elements (when clone)
	 */
	function update() {
		var $this = $( this ),
			$output = $this.siblings( '.video-central-metaboxes-output' );

		$this.on( 'input propertychange change', function ( e ) {
			$output.html( $this.val() );
		} );

	}

	$( ':input.video-central-metaboxes-range' ).each( update );
	$( '.video-central-metaboxes-input' ).on( 'clone', 'input.video-central-metaboxes-range', update );
} );
