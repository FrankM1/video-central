jQuery( function ( $ ) {
	'use strict';

	function video_central_metaboxes_update_slider() {
		var $input = $( this ),
			$slider = $input.siblings( '.video-central-metaboxes-slider' ),
			$valueLabel = $slider.siblings( '.video-central-metaboxes-slider-value-label' ).find( 'span' ),
			value = $input.val(),
			options = $slider.data( 'options' );


		$slider.html( '' );

		if ( ! value ) {
			value = 0;
			$input.val( 0 );
			$valueLabel.text( '0' );
		}
		else {
			$valueLabel.text( value );
		}

		// Assign field value and callback function when slide
		options.value = value;
		options.slide = function ( event, ui ) {
			$input.val( ui.value );
			$valueLabel.text( ui.value );
		};

		$slider.slider( options );
	}

	$( ':input.video-central-metaboxes-slider-value' ).each( video_central_metaboxes_update_slider );
	$( '.video-central-metaboxes-input' ).on( 'clone', ':input.video-central-metaboxes-slider-value', video_central_metaboxes_update_slider );
} );
