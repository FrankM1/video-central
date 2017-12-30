var VideosSidebar,
	$ = require( 'jquery' ),
	wp = require( 'wp' );

VideosSidebar = wp.Backbone.View.extend({
	className: 'video-central-videos-browser-sidebar media-sidebar',
	template: wp.template( 'video-central-videos-browser-sidebar' ),

	events: {
		'change [data-setting]': 'updateAttribute'
	},

	initialize: function( options ) {
		this.attributes = options.controller.state().get( 'attributes' );
	},

	render: function() {
		this.$el.html( this.template() );
	},

	updateAttribute: function( e ) {
		var $target = $( e.target ),
			attribute = $target.data( 'setting' ),
			value = e.target.value;

		if ( 'checkbox' === e.target.type ) {
			value = !! $target.prop( 'checked' );
		}

		this.attributes.set( attribute, value );
	}
});

module.exports = VideosSidebar;
