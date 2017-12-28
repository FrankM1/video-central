jQuery( function ( $ ) {
	function update() {
		var $this = $( this ),
			$children = $this.closest( 'li' ).children( 'ul' );

		if ( $this.is( ':checked' ) ) {
			$children.removeClass( 'hidden' );
		} else {
			$children
				.addClass( 'hidden' )
				.find( 'input' )
				.removeAttr( 'checked' );
		}
	}

	$( '.video-central-metaboxes-input' )
		.on( 'change', '.video-central-metaboxes-input-list.collapse :checkbox', update )
		.on( 'clone', '.video-central-metaboxes-input-list.collapse :checkbox', update );
	$( '.video-central-metaboxes-input-list.collapse :checkbox' ).each( update );
} );
