var VideosToolbar,
	_ = require( 'underscore' ),
	wp = require( 'wp' ),
	video_central = require( 'video_central' ),

VideosToolbar = wp.media.view.Toolbar.extend({
	initialize: function( options ) {
		this.controller = options.controller;

		_.bindAll( this, 'insertVideos' );

		// This is a button.
		this.options.items = _.defaults( this.options.items || {}, {
			insert: {
				text: wp.media.view.l10n.insertIntoPlaylist || 'Insert into playlist',
				style: 'primary',
				priority: 80,
				requires: {
					selection: true
				},
				click: this.insertVideos
			}
		});

		wp.media.view.Toolbar.prototype.initialize.apply( this, arguments );
	},

	insertVideos: function() {
        var state = this.controller.state(), 
            selection = state.get( 'selection' );
            
        _.each( selection.models, function( attachment ) {
            attachment.set( 'videoId', attachment.get('id') );
            video_central.videos.push( attachment.toJSON() );
        });

        this.controller.close();
        
        state.trigger( 'insert', selection ).reset();
	}
});

module.exports = VideosToolbar;
