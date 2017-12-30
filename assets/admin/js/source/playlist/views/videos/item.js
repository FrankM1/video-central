var Videos,
	wp = require( 'wp' );

Videos = wp.Backbone.View.extend({
	tagName: 'li',
	className: 'video-central-videos-browser-list-item',
	template: wp.template( 'video-central-videos-browser-list-item' ),

    attributes: function() {
		return {
			'tabIndex':     0,
			'role':         'checkbox',
			'aria-label':   this.model.get( 'title' ),
			'aria-checked': false,
			'data-id':      this.model.get( 'id' )
		};
    },
    
	events: {
		'click': 'addSelection'
	},

	initialize: function( options ) {
		this.controller = options.controller;
		this.model = options.model;
        this.selection = this.controller.state().get( 'selection' );

        this.listenTo( this.selection, 'add remove reset', this.updateSelectedClass );

        /* Update the selection.  */
		if ( this.selection ) {
			this.selection.on( 'reset', this.updateSelect, this );
        }
	},

    /**
	 * @returns {wp.media.view.Attachment} Returns itself to allow chaining
	 */
	render: function() {
		this.views.detach();

		// Check if the model is selected.
		this.updateSelect();

		this.views.render();

		this.$el.html( this.template( this.model.toJSON() ) );
		return this;
	},

	addSelection: function( e ) {
		if ( this.selection.contains( this.model ) ) {
			this.selection.remove( this.model );
		} else {
			this.selection.add( this.model );
		}
	},

	updateSelectedClass: function() {
		if ( this.selection.contains( this.model ) ) {
            this.select();
		} else {
            this.deselect();
		}
    },

    updateSelect: function() {
		this[ this.selected() ? 'select' : 'deselect' ]();
    },
    
    /**
	 * @returns {unresolved|Boolean}
	 */
	selected: function() {
		var selection = this.selection;
		if ( selection ) {
			return !! selection.get( this.model.cid );
		}
	},

    /**
	 * @param {Backbone.Model} model
	 * @param {Backbone.Collection} collection
	 */
	select: function( model, collection ) {
		var selection = this.selection,
			controller = this.controller;

		// Check if a selection exists and if it's the collection provided.
		// If they're not the same collection, bail; we're in another
		// selection's event loop.
		if ( ! selection || ( collection && collection !== selection ) ) {
			return;
		}

		// Bail if the model is already selected.
		if ( this.$el.hasClass( 'selected' ) ) {
			return;
		}

		// Add 'selected' class to model, set aria-checked to true.
		this.$el.addClass( 'selected' ).attr( 'aria-checked', true );
		//  Make the checkbox tabable, except in media grid (bulk select mode).
		if ( ! ( controller.isModeActive( 'grid' ) && controller.isModeActive( 'select' ) ) ) {
			this.$( '.check' ).attr( 'tabindex', '0' );
		}
    },

	/**
	 * @param {Backbone.Model} model
	 * @param {Backbone.Collection} collection
	 */
	deselect: function( model, collection ) {
		var selection = this.selection;

		// Check if a selection exists and if it's the collection provided.
		// If they're not the same collection, bail; we're in another
		// selection's event loop.
		if ( ! selection || ( collection && collection !== selection ) ) {
			return;
		}
		this.$el.removeClass( 'selected' ).attr( 'aria-checked', false )
			.find( '.check' ).attr( 'tabindex', '-1' );
	}
});

module.exports = Videos;
