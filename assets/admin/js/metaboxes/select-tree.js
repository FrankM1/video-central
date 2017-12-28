jQuery( function ( $ ) {
	'use strict';

	function update() {
		var $this = $( this ),
			val = $this.val(),
			$selected = $this.siblings( "[data-parent-id='" + val + "']" ),
			$notSelected = $this.parent().find( '.video-central-metaboxes-select-tree' ).not( $selected );

		$selected.removeClass( 'hidden' );
		$notSelected
			.addClass( 'hidden' )
			.find( 'select' )
			.prop( 'selectedIndex', 0 );
	}

	$( '.video-central-metaboxes-input' )
		.on( 'change', '.video-central-metaboxes-select-tree select', update )
		.on( 'clone', '.video-central-metaboxes-select-tree select', update );
} );
