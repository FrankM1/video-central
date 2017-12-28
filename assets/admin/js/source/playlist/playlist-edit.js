var video_central = require( 'video_central' );
var wp = require( 'wp' );

video_central.data = videoCentralPlaylistConfig;
video_central.settings( videoCentralPlaylistConfig );

wp.media.view.settings.post.id = video_central.data.postId;
wp.media.view.settings.defaultProps = {};

video_central.model.Track = require( './models/track' );
video_central.model.Tracks = require( './collections/tracks' );

video_central.view.MediaFrame = require( './views/media-frame' );
video_central.view.PostForm = require( './views/post-form' );
video_central.view.AddTracksButton = require( './views/button/add-tracks' );
video_central.view.TrackList = require( './views/track-list' );
video_central.view.Track = require( './views/track' );
video_central.view.TrackArtwork = require( './views/track/artwork' );
video_central.view.TrackAudio = require( './views/track/audio' );

video_central.workflows = require( './workflows' );

( function( $ ) {
    var tracks;

	tracks = video_central.tracks = new video_central.model.Tracks( video_central.data.tracks );
	delete video_central.data.tracks;

	var postForm = new video_central.view.PostForm({
		collection: tracks,
		l10n: video_central.l10n
    });
    
} ( jQuery ));

