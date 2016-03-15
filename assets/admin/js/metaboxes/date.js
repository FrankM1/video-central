jQuery( function ( $ )
{
	'use strict';

	/**
	 * Update date picker element
	 * Used for static & dynamic added elements (when clone)
	 */
	function update()
	{
		var $this = $( this ),
			options = $this.data( 'options' );

		$this.siblings( '.ui-datepicker-append' ).remove();         // Remove appended text
		$this.removeClass( 'hasDatepicker' ).datepicker( options );
	}

	$( ':input.video-central-metaboxes-date' ).each( update );
	$( '.video-central-metaboxes-input' ).on( 'clone', ':input.video-central-metaboxes-date', update );
} );
