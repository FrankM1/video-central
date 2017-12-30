var VideosNoItems,
	wp = require( 'wp' );

VideosNoItems = wp.Backbone.View.extend({
	className: 'video-central-videos-browser-empty',
	tagName: 'div',
	template: wp.template( 'video-central-videos-browser-empty' ),

	initialize: function( options ) {
		this.collection = this.collection;

		this.listenTo( this.collection, 'add remove reset', this.toggleVisibility );
	},

	render: function() {
		this.$el.html( this.template() );
		return this;
	},

	toggleVisibility: function() {
		this.$el.toggleClass( 'is-visible', this.collection.length < 1 );
	}
});

module.exports = VideosNoItems;
