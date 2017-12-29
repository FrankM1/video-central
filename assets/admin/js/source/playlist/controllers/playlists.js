var Playlists,
	Backbone = require( 'backbone' ),
	l10n = require( 'video_central' ).l10n,
	wp = require( 'wp' );

Playlists = wp.media.controller.State.extend({
	defaults: {
		id: 'video-central-playlists',
		title: l10n.insertPlaylist || 'Insert Playlist',
		collection: null,
		content: 'video-central-playlist-browser',
		menu: 'default',
		menuItem: {
			text: l10n.insertFromCue || 'Insert from Video Central',
			priority: 130
		},
		selection: null,
		toolbar: 'video-central-insert-playlist'
	},

	initialize: function( options ) {
		var collection = options.collection || new Backbone.Collection(),
			selection = options.selection || new Backbone.Collection();

		this.set( 'attributes', new Backbone.Model({
			id: null,
			show_playlist: true
		}) );

		this.set( 'collection', collection );
		this.set( 'selection', selection );

		this.listenTo( selection, 'remove', this.updateSelection );
	}
});

module.exports = Playlists;
