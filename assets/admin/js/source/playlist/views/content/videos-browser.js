var VideosBrowser,
	_ = require( 'underscore' ),
	VideosItems = require( '../videos/items' ),
	VideosNoItems = require( '../videos/no-items' ),
	VideosSidebar = require( '../videos/sidebar' ),
	wp = require( 'wp' );

VideosBrowser = wp.Backbone.View.extend({
	className: 'video-central-videos-browser',

	initialize: function( options ) {
		this.collection = options.controller.state().get( 'collection' );
		this.controller = options.controller;

		this._paged = 1;
		this._pending = false;

		_.bindAll( this, 'scroll' );
        this.listenTo( this.collection, 'reset', this.render );
        
        if ( ! this.collection.length ) {
			this.getVideos();
		}
	},

	render: function() {
		this.$el.off( 'scroll' ).on( 'scroll', this.scroll );

		this.views.add([
			new VideosItems({
				collection: this.collection,
				controller: this.controller
			}),
			new VideosSidebar({
				controller: this.controller
			}),
			new VideosNoItems({
				collection: this.collection
			})
		]);

		return this;
	},

	scroll: function() {
		if ( ! this._pending && this.el.scrollHeight < this.el.scrollTop + this.el.clientHeight * 3 ) {
			this._pending = true;
			this.getVideos();
		}
	},

	getVideos: function() {
		var view = this;

		wp.ajax.post( 'video_central_get_videos_for_frame', {
			paged: view._paged
		}).done(function( response ) {
			view.collection.add( response.videos );

			view._paged++;

			if ( view._paged <= response.maxNumPages ) {
				view._pending = false;
				view.scroll();
			}
		});
	}
});

module.exports = VideosBrowser;
