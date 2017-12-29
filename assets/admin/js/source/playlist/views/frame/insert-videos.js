var InsertVideosFrame,
	VideosBrowser = require( '../content/videos-browser' ),
    VideosController = require( '../../controllers/videos' ),
	VideosToolbar = require( '../toolbar/videos' ),
    wp = require( 'wp' ),
    MediaFrame = wp.media.view.MediaFrame;

InsertVideosFrame = MediaFrame.extend({

    initialize: function() {
		_.extend( this.options, {
            uploader: false,
            multiple: true
        });

		MediaFrame.prototype.initialize.apply( this, arguments );

		this.createStates();
		this.bindHandlers();

		this.setState( 'video-central-playlist-videos' );
    },
    
	createStates: function() {
		this.states.add( new VideosController({}) );
	},

	bindHandlers: function() {
		this.on( 'content:create:video-central-videos-browser', this.createCueContent, this );
		this.on( 'toolbar:create:video-central-playlist-insert-videos', this.createCueToolbar, this );
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
