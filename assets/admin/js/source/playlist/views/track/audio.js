var TrackAudio,
	$ = require( 'jquery' ),
	_ = require( 'underscore' ),
	settings = require( 'video_central' ).settings(),
	workflows = require( '../../workflows' ),
	wp = require( 'wp' );

TrackAudio = wp.Backbone.View.extend({
	tagName: 'span',
	className: 'video-central-playlist-track-audio',
	template: wp.template( 'video-central-playlist-playlist-track-audio' ),

	events: {
		'click .video-central-playlist-track-audio-selector': 'select'
	},

	initialize: function( options ) {
		this.parent = options.parent;

		this.listenTo( this.model, 'change:audioUrl', this.refresh );
		this.listenTo( this.model, 'destroy', this.cleanup );
	},

	render: function() {
		var $mediaEl, playerSettings,
			track = this.model.toJSON(),
			playerId = this.$el.find( '.mejs-audio' ).attr( 'id' );

		// Remove the MediaElement player object if the
		// audio file URL is empty.
		if ( '' === track.audioUrl && playerId ) {
			mejs.players[ playerId ].remove();
		}

		// Render the media element.
		this.$el.html( this.template( this.model.toJSON() ) );

		// Set up MediaElement.js.
		$mediaEl = this.$el.find( '.video-central-playlist-audio' );

		return this;
	},

	refresh: function( e ) {
		var track = this.model.toJSON(),
			playerId = this.$el.find( '.mejs-audio' ).attr( 'id' ),
			player = playerId ? mejs.players[ playerId ] : null;

		if ( player && '' !== track.audioUrl ) {
			player.pause();
			player.setSrc( track.audioUrl );
		} else {
			this.render();
		}
	},

	cleanup: function() {
		var playerId = this.$el.find( '.mejs-audio' ).attr( 'id' ),
			player = playerId ? mejs.players[ playerId ] : null;

		if ( player ) {
			player.remove();
		}
	},

	select: function() {
		workflows.setModel( this.model ).get( 'selectAudio' ).open();
	}
});

module.exports = TrackAudio;
