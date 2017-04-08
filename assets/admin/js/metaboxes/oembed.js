jQuery( function ( $ ) {
	'use strict';

	/**
	 * Show preview of oembeded media.
	 */
	function showPreview( e ) {
		e.preventDefault();

		var $this = $( this ),
			$spinner = $this.siblings( '.spinner' ),
			data = {
				action: 'video_central_metaboxes_get_embed',
				url: $this.siblings( 'input' ).val()
			};

		$spinner.css( 'visibility', 'visible' );
		$.post( ajaxurl, data, function ( r ) {
			$spinner.css( 'visibility', 'hidden' );
			$this.siblings( '.video-central-metaboxes-embed-media' ).html( r.data );
		}, 'json' );
	}

	/**
	 * Remove oembed preview when cloning.
	 */
	function removePreview() {
		$( this ).siblings( '.video-central-metaboxes-embed-media' ).html( '' );
	}

	// Show oembeded media when clicking "Preview" button
	$( 'body' ).on( 'click', '.video-central-metaboxes-embed-show', showPreview );

	// Remove oembed preview when cloning
	$( '.video-central-metaboxes-input' ).on( 'clone', '.video-central-metaboxes-oembed', removePreview );
} );
