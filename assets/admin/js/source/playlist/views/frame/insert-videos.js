var InsertVideosFrame,
	VideosBrowser = require( '../content/videos-browser' ),
    VideosController = require( '../../controllers/videos' ),
    VideoQuery = require( '../../models/videos' ),
	VideosToolbar = require( '../toolbar/videos' ),
	wp = require( 'wp' ),
	PostFrame = wp.media.view.MediaFrame.Post;
    
InsertVideosFrame = PostFrame.extend({
	createStates: function() {
        PostFrame.prototype.createStates.apply( this, arguments );

		// Add the default states.
		this.states.add(
            // Add our HTML slide controller state.
            new VideosController()
        );
	},

	bindHandlers: function() {
		PostFrame.prototype.bindHandlers.apply( this, arguments );

		// this.on( 'menu:create:default', this.createCueMenu, this );
		this.on( 'content:create:video-central-videos-browser', this.createCueContent, this );
		this.on( 'toolbar:create:video-central-playlist-insert-videos', this.createCueToolbar, this );
	},

	createCueMenu: function( menu ) {
		menu.view.set({
			'video-central-playlist-videos-separator': new wp.media.View({
				className: 'separator',
				priority: 200
			})
		});
	},

	createCueContent: function( content ) {
		content.view = new VideosBrowser({
			controller: this
		});
	},

	createCueToolbar: function( toolbar ) {
		toolbar.view = new VideosToolbar({
			controller: this
		});
	},
});

module.exports = InsertVideosFrame;
