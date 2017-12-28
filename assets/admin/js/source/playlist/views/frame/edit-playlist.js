var EditPlaylistFrame,
	_ = require( 'underscore' ),
	PlaylistBrowser = require( '../content/playlist-browser' ),
	PlaylistsController = require( '../../controllers/playlists' ),
	PlaylistToolbar = require( '../toolbar/playlist' ),
	wp = require( 'wp' ),
	MediaFrame = wp.media.view.MediaFrame;

EditPlaylistFrame = MediaFrame.extend({
	initialize: function() {
		_.extend( this.options, {
			uploader: false
		});

		MediaFrame.prototype.initialize.apply( this, arguments );

		this.createStates();
		this.bindHandlers();

		this.setState( 'video-central-playlist-edit-playlist' );
	},

	createStates: function() {
		this.states.add( new PlaylistsController({
			id: 'video-central-playlist-edit-playlist',
			content: 'video-central-playlist-edit-playlist',
			menuItem: false,
			title: 'Edit Playlist',
			toolbar: 'video-central-playlist-edit-playlist'
		}) );
	},

	bindHandlers: function() {
		this.on( 'content:create:video-central-playlist-edit-playlist', this.createCueContent, this );
		this.on( 'toolbar:create:video-central-playlist-edit-playlist', this.createCueToolbar, this );
	},

	createCueContent: function( region ) {
		region.view = new PlaylistBrowser({
			controller: this
		});
	},

	createCueToolbar: function( region, options ) {
		region.view = new PlaylistToolbar({
			controller: this
		});
	}
});

module.exports = EditPlaylistFrame;
