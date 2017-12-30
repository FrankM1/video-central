var AddVideosButton,
	$ = require( 'jquery' ),
	workflows = require( '../../workflows' ),
	wp = require( 'wp' );

AddVideosButton = wp.Backbone.View.extend({
	id: 'add-videos',
	tagName: 'p',

	events: {
		'click .button': 'click'
	},

	initialize: function( options ) {
		this.l10n = options.l10n;
	},

	render: function() {
		var $button = $( '<a />', {
			text: this.l10n.addVideos
		}).addClass( 'button button-secondary' );

		this.$el.html( $button );

		return this;
	},

	click: function( e ) {
		e.preventDefault();
		workflows.get( 'addVideos' ).open();
	}
});

module.exports = AddVideosButton;
