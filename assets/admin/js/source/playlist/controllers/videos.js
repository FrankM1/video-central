var Videos,
	Backbone = require( 'backbone' ),
	l10n = require( 'video_central' ).l10n,
	wp = require( 'wp' );
    
Videos = wp.media.controller.State.extend({
	defaults: {
		id        : 'video-central-playlist-videos',
		title     : l10n.insertVideos || 'Insert Videos',
        collection: null,
        selection: null,
		content   : 'video-central-videos-browser',
		menu      : 'default',
		menuItem  : {
			text    : l10n.insertFromVideoCentral || 'Insert from Video Central',
			priority: 1
        },
        multiple : true,
        toolbar  : 'video-central-playlist-insert-videos'
	},

	initialize: function( options ) {
		var collection = options.collection || new Backbone.Collection(),
			selection = options.selection || new Backbone.Collection();
   
		this.set( 'attributes', new Backbone.Model({
			id: null,
			show_videos: true
		}) );

		this.set( 'collection', collection );
        this.set( 'selection', selection );
        
		this.listenTo( selection, 'remove', this.updateSelection );
	}
});

module.exports = Videos;
