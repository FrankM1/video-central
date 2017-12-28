var TrackArtwork,
	_ = require( 'underscore' ),
	workflows = require( '../../workflows' ),
	wp = require( 'wp' );

TrackArtwork = wp.Backbone.View.extend({
	tagName: 'span',
	className: 'video-central-playlist-track-artwork',
	template: wp.template( 'video-central-playlist-playlist-track-artwork' ),

	events: {
		'click': 'select'
	},

	initialize: function( options ) {
		this.parent = options.parent;
		this.listenTo( this.model, 'change:artworkUrl', this.render );
	},

	render: function() {
		this.$el.html( this.template( this.model.toJSON() ) );
		this.parent.$el.toggleClass( 'has-artwork', ! _.isEmpty( this.model.get( 'artworkUrl' ) ) );
		return this;
	},

	select: function() {
		workflows.setModel( this.model ).get( 'selectArtwork' ).open();
	}
});

module.exports = TrackArtwork;
