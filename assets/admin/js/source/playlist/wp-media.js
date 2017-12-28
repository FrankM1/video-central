(function( wp ) {
	'use strict';

	var video_central = require( 'video_central' );

	video_central.settings( videoCentralPlaylistConfig );

	wp.media.view.MediaFrame.Post = require( './views/frame/insert-playlist' );

}( wp ));
