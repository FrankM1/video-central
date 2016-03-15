jQuery( function( $ )
{
	'use strict';

	$( '.video-central-metaboxes-oembed-wrapper .spinner' ).hide();

	$( 'body' ).on( 'click', '.video-central-metaboxes-oembed-wrapper .show-embed', function() {
		var $this = $( this ),
			$spinner = $this.siblings( '.spinner' ),
			data = {
				action: 'video_central_metaboxes_get_embed',
				url: $this.siblings( 'input' ).val()
			};

		$spinner.show();
		$.post( ajaxurl, data, function( r )
		{
			$spinner.hide();
			$this.siblings( '.embed-code' ).html( r.data );
		}, 'json' );

		return false;
	} );
} );
