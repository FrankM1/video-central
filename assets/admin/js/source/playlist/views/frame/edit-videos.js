var EditVideosFrame,
	_ = require( 'underscore' ),
	VideosBrowser = require( '../content/videos-browser' ),
	VideosController = require( '../../controllers/videos' ),
	VideosToolbar = require( '../toolbar/videos' ),
	wp = require( 'wp' ),
	MediaFrame = wp.media.view.MediaFrame;

EditVideosFrame = MediaFrame.extend({
	initialize: function() {
		_.extend( this.options, {
			uploader: false
		});

		MediaFrame.prototype.initialize.apply( this, arguments );

		this.createStates();
		this.bindHandlers();

		this.setState( 'video-central-playlist-edit-videos' );
	},

	createStates: function() {
		this.states.add( new VideosController({
			id: 'video-central-playlist-edit-videos',
			content: 'video-central-playlist-edit-videos',
			menuItem: false,
			title: 'Edit Videos',
			toolbar: 'video-central-playlist-edit-videos'
		}) );
	},

	bindHandlers: function() {
		this.on( 'content:create:video-central-playlist-edit-videos', this.createCueContent, this );
		this.on( 'toolbar:create:video-central-playlist-edit-videos', this.createCueToolbar, this );
	},

	createCueContent: function( region ) {
		region.view = new VideosBrowser({
			controller: this
		});
	},

	createCueToolbar: function( region, options ) {
		region.view = new VideosToolbar({
			controller: this
		});
	}
});

module.exports = EditVideosFrame;
