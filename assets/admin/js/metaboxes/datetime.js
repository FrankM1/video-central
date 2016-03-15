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

		$this.siblings( '.ui-datepicker-append' ).remove();         // Remove appended text
		$this.removeClass( 'hasDatepicker' ).datetimepicker( options );

	}

	// Set language if available
	if ( $.timepicker.regional.hasOwnProperty( Video_Central_Metaboxes_Datetimepicker.locale ) )
	{
		$.timepicker.setDefaults( $.timepicker.regional[Video_Central_Metaboxes_Datetimepicker.locale] );
	}
	else if ( $.timepicker.regional.hasOwnProperty( Video_Central_Metaboxes_Datetimepicker.localeShort ) )
	{
		$.timepicker.setDefaults( $.timepicker.regional[Video_Central_Metaboxes_Datetimepicker.localeShort] );
	}

	$( ':input.video-central-metaboxes-datetime' ).each( update );
	$( '.video-central-metaboxes-input' ).on( 'clone', ':input.video-central-metaboxes-datetime', update );
} );
