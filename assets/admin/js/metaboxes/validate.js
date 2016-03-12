jQuery( document ).ready( function( $ )
{
	var $form = $( '#post' );

	// Required field styling
	$.each( VideoCentralMetaboxes.validationOptions.rules, function( k, v )
	{
		if ( v['required'] )
			$( '#' + k ).parent().siblings( '.video-central-metaboxes-label' ).addClass( 'required' ).append( '<span>*</span>' );
	} );

	VideoCentralMetaboxes.validationOptions.invalidHandler = function( form, validator )
	{
		// Re-enable the submit ( publish/update ) button and hide the ajax indicator
		$( '#publish' ).removeClass( 'button-primary-disabled' );
		$( '#ajax-loading' ).attr( 'style', '' );
		$form.siblings( '#message' ).remove();
		$form.before( '<div id="message" class="error"><p>' + VideoCentralMetaboxes.summaryMessage + '</p></div>' );
	};

	$form.validate( VideoCentralMetaboxes.validationOptions );
} );
