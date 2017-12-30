var VideoList,
	$ = require( 'jquery' ),
	_ = require( 'underscore' ),
	Video = require( './video' ),
	wp = require( 'wp' );

VideoList = wp.Backbone.View.extend({
	className: 'video-central-playlist-videolist',
	tagName: 'ol',

	initialize: function() {
		this.listenTo( this.collection, 'add', this.addVideo );
		this.listenTo( this.collection, 'add remove', this.updateOrder );
		this.listenTo( this.collection, 'reset', this.render );
	},

	render: function() {
		this.$el.empty();

		this.collection.each( this.addVideo, this );
		this.updateOrder();

		this.$el.sortable( {
			axis: 'y',
			delay: 150,
			forceHelperSize: true,
			forcePlaceholderSize: true,
			opacity: 0.6,
			start: function( e, ui ) {
				ui.placeholder.css( 'visibility', 'visible' );
			},
			update: _.bind(function( e, ui ) {
				this.updateOrder();
			}, this )
		} );

		return this;
	},

	addVideo: function( video ) {
		var videoView = new Video({ model: video });
		this.$el.append( videoView.render().el );
	},

	updateOrder: function() {
		_.each( this.$el.find( '.video-central-playlist-video' ), function( item, i ) {
			var cid = $( item ).data( 'cid' );
			this.collection.get( cid ).set( 'order', i );
		}, this );
	}
});

module.exports = VideoList;
