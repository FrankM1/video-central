window.rwmb = window.rwmb || {};

jQuery( function ( $ ) {
	'use strict';

	var views = rwmb.views = rwmb.views || {},
		ImageField = views.ImageField,
		ImageUploadField,
		UploadButton = views.UploadButton;

	ImageUploadField = views.ImageUploadField = ImageField.extend( {
		createAddButton: function () {
			this.addButton = new UploadButton( {controller: this.controller} );
		}
	} );

	/**
	 * Initialize fields
	 * @return void
	 */
	function init() {
		new ImageUploadField( {input: this, el: $( this ).siblings( 'div.video-central-metaboxes-media-view' )} );
	}

	$( ':input.video-central-metaboxes-image_upload, :input.video-central-metaboxes-plupload_image' ).each( init );
	$( '.video-central-metaboxes-input' )
		.on( 'clone', ':input.video-central-metaboxes-image_upload, :input.video-central-metaboxes-plupload_image', init )
} );
