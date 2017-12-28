var Videos,
	_ = require( 'underscore' ),
	Backbone = require( 'backbone' ),
	settings = require( 'video_central' ).settings(),
	Video = require( '../models/video' ),
	wp = require( 'wp' );

Videos = Backbone.Collection.extend({
	model: Video,

	comparator: function( video ) {
		return parseInt( video.get( 'order' ), 10 );
	},

	fetch: function() {
		var collection = this;

		return wp.ajax.post( 'video_central_get_playlist_videos', {
			post_id: settings.postId
		}).done(function( videos ) {
			collection.reset( videos );
		});
	},

	save: function( data ) {
		this.sort();

		data = _.extend({}, data, {
			post_id: settings.postId,
			videos: this.toJSON(),
			nonce: settings.saveNonce
		});

		return wp.ajax.post( 'video_central_save_playlist_videos', data );
	}
});

module.exports = Videos;
