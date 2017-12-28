var VideoArtwork,
	_ = require( 'underscore' ),
	workflows = require( '../../workflows' ),
	wp = require( 'wp' );

VideoArtwork = wp.Backbone.View.extend({
	tagName: 'span',
	className: 'video-central-playlist-video-artwork',
	template: wp.template( 'video-central-playlist-playlist-video-artwork' ),

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

module.exports = VideoArtwork;
