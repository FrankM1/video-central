window.rwmb = window.rwmb || {};

jQuery( function ( $ ) {
	'use strict';

	var views = rwmb.views = rwmb.views || {},
		MediaField = views.MediaField,
		MediaItem = views.MediaItem,
		MediaList = views.MediaList,
		ImageField;

	ImageField = views.ImageField = MediaField.extend( {
		createList: function () {
			this.list = new MediaList( {
				controller: this.controller,
				itemView: MediaItem.extend( {
					className: 'video-central-metaboxes-image-item',
					template: wp.template( 'video-central-metaboxes-image-item' )
				} )
			} );
		}
	} );

	/**
	 * Initialize image fields
	 */
	function initImageField() {
		new ImageField( {input: this, el: $( this ).siblings( 'div.video-central-metaboxes-media-view' )} );
	}

	$( 'input.video-central-metaboxes-image_advanced' ).each( initImageField );
	$( '#wpbody' ).on( 'clone', 'input.video-central-metaboxes-image_advanced', initImageField )
} );
