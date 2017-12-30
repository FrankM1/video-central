var video_central = require( 'video_central' );
var wp = require( 'wp' );

video_central.data = videoCentralPlaylistConfig;
video_central.settings( videoCentralPlaylistConfig );

wp.media.view.settings.post.id = video_central.data.postId;
wp.media.view.settings.defaultProps = {};

video_central.model.Video = require( './models/video' );
video_central.model.Videos = require( './collections/videos' );

video_central.view.PostForm = require( './views/post-form' );
video_central.view.AddVideosButton = require( './views/button/add-videos' );
video_central.view.VideoList = require( './views/video-list' );
video_central.view.Video = require( './views/video' );
video_central.view.VideoArtwork = require( './views/video/artwork' );
video_central.view.VideoAudio = require( './views/video/audio' );

video_central.workflows = require( './workflows' );

( function( $ ) {
    var videos;

	videos = video_central.videos = new video_central.model.Videos( video_central.data.videos );
	delete video_central.data.videos;

	var postForm = new video_central.view.PostForm({
		collection: videos,
		l10n: video_central.l10n
    });
    
} ( jQuery ));

