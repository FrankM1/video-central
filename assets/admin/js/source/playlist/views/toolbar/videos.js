var VideosToolbar,
	_ = require( 'underscore' ),
	wp = require( 'wp' );

VideosToolbar = wp.media.view.Toolbar.extend({
	initialize: function( options ) {
		this.controller = options.controller;

		_.bindAll( this, 'insertCueShortcode' );

		// This is a button.
		this.options.items = _.defaults( this.options.items || {}, {
			insert: {
				text: wp.media.view.l10n.insertIntoPost || 'Insert into post',
				style: 'primary',
				priority: 80,
				requires: {
					selection: true
				},
				click: this.insertCueShortcode
			}
		});

		wp.media.view.Toolbar.prototype.initialize.apply( this, arguments );
	},

	insertCueShortcode: function() {
		var html,
			state = this.controller.state(),
			attributes = state.get( 'attributes' ).toJSON(),
			selection = state.get( 'selection' ).first();

		attributes.id = selection.get( 'id' );
		_.pick( attributes, 'id', 'theme', 'width', 'show_videos' );

		if ( ! attributes.show_videos ) {
			attributes.show_videos = '0';
		} else {
			delete attributes.show_videos;
		}

		html = wp.shortcode.string({
			tag: 'video_central',
			type: 'single',
			attrs: attributes
		});

		wp.media.editor.insert( html );
		this.controller.close();
	}
});

module.exports = VideosToolbar;
