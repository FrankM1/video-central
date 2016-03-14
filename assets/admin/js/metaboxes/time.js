jQuery( function ( $ )
{
	'use strict';

	/**
	 * Update datetime picker element
	 * Used for static & dynamic added elements (when clone)
	 */
	function update()
	{
		var $this = $( this ),
			options = $this.data( 'options' );

		$this.siblings( '.ui-datepicker-append' ).remove();  // Remove appended text
		$this.removeClass( 'hasDatepicker' ).timepicker( options );
	}

	// Set language if available
	if ( $.timepicker.regional.hasOwnProperty( Video_Central_Metaboxes_Timepicker.locale ) )
	{
		$.timepicker.setDefaults( $.timepicker.regional[Video_Central_Metaboxes_Timepicker.locale] );
	}
	else if ( $.timepicker.regional.hasOwnProperty( Video_Central_Metaboxes_Timepicker.localeShort ) )
	{
		$.timepicker.setDefaults( $.timepicker.regional[Video_Central_Metaboxes_Timepicker.localeShort] );
	}

	$( '.video-central-metaboxes-time' ).each( update );
	$( '.video-central-metaboxes-input' ).on( 'clone', '.video-central-metaboxes-time', update );
} );
