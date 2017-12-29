(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
(function (global){
var Videos,
	_ = (typeof window !== "undefined" ? window['_'] : typeof global !== "undefined" ? global['_'] : null),
	Backbone = (typeof window !== "undefined" ? window['Backbone'] : typeof global !== "undefined" ? global['Backbone'] : null),
	settings = (typeof window !== "undefined" ? window['video_central'] : typeof global !== "undefined" ? global['video_central'] : null).settings(),
	Video = require( '../models/video' ),
	wp = (typeof window !== "undefined" ? window['wp'] : typeof global !== "undefined" ? global['wp'] : null);

Videos = Backbone.Collection.extend({
	model: Video,

	comparator: function( video ) {
		return parseInt( video.get( 'order' ), 10 );
	},

	fetch: function() {
		var collection = this;

		return wp.ajax.post( 'video_central_get_playlist_videos', {
			post_id: settings.postId
		}).done(function( videos ) {
			collection.reset( videos );
		});
	},

	save: function( data ) {
		this.sort();

		data = _.extend({}, data, {
			post_id: settings.postId,
			videos: this.toJSON(),
			nonce: settings.saveNonce
		});

		return wp.ajax.post( 'video_central_save_playlist_videos', data );
	}
});

module.exports = Videos;

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{"../models/video":3}],2:[function(require,module,exports){
(function (global){
var Videos,
	Backbone = (typeof window !== "undefined" ? window['Backbone'] : typeof global !== "undefined" ? global['Backbone'] : null),
	l10n = (typeof window !== "undefined" ? window['video_central'] : typeof global !== "undefined" ? global['video_central'] : null).l10n,
	wp = (typeof window !== "undefined" ? window['wp'] : typeof global !== "undefined" ? global['wp'] : null);
    
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

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{}],3:[function(require,module,exports){
(function (global){
var Video,
	Backbone = (typeof window !== "undefined" ? window['Backbone'] : typeof global !== "undefined" ? global['Backbone'] : null);

Video = Backbone.Model.extend({
	defaults: {
		artist: '',
		artworkId: '',
		artworkUrl: '',
		videoId: '',
		audioUrl: '',
		format: '',
		length: '',
		title: '',
		order: 0
	}
});

module.exports = Video;

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{}],4:[function(require,module,exports){
(function (global){
var video_central = (typeof window !== "undefined" ? window['video_central'] : typeof global !== "undefined" ? global['video_central'] : null);
var wp = (typeof window !== "undefined" ? window['wp'] : typeof global !== "undefined" ? global['wp'] : null);

video_central.data = videoCentralPlaylistConfig;
video_central.settings( videoCentralPlaylistConfig );

wp.media.view.settings.post.id = video_central.data.postId;
wp.media.view.settings.defaultProps = {};

video_central.model.Video = require( './models/video' );
video_central.model.Videos = require( './collections/videos' );

video_central.view.PostForm = require( './views/post-form' );
video_central.view.AddVideosButton = require( './views/button/add-videos' );
video_central.view.VideoList = require( './views/video-list' );
video_central.view.Video = require( './views/video' );
video_central.view.VideoArtwork = require( './views/video/artwork' );
video_central.view.VideoAudio = require( './views/video/audio' );

video_central.workflows = require( './workflows' );

( function( $ ) {
    var videos;

	videos = video_central.videos = new video_central.model.Videos( video_central.data.videos );
	delete video_central.data.videos;

	var postForm = new video_central.view.PostForm({
		collection: videos,
		l10n: video_central.l10n
    });
    
} ( jQuery ));


}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{"./collections/videos":1,"./models/video":3,"./views/button/add-videos":5,"./views/post-form":8,"./views/video":11,"./views/video-list":10,"./views/video/artwork":12,"./views/video/audio":13,"./workflows":18}],5:[function(require,module,exports){
(function (global){
var AddVideosButton,
	$ = (typeof window !== "undefined" ? window['jQuery'] : typeof global !== "undefined" ? global['jQuery'] : null),
	workflows = require( '../../workflows' ),
	wp = (typeof window !== "undefined" ? window['wp'] : typeof global !== "undefined" ? global['wp'] : null);

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

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{"../../workflows":18}],6:[function(require,module,exports){
(function (global){
var VideosBrowser,
	_ = (typeof window !== "undefined" ? window['_'] : typeof global !== "undefined" ? global['_'] : null),
	VideosItems = require( '../videos/items' ),
	VideosNoItems = require( '../videos/no-items' ),
	VideosSidebar = require( '../videos/sidebar' ),
	wp = (typeof window !== "undefined" ? window['wp'] : typeof global !== "undefined" ? global['wp'] : null);

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

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{"../videos/items":15,"../videos/no-items":16,"../videos/sidebar":17}],7:[function(require,module,exports){
(function (global){
var InsertVideosFrame,
	VideosBrowser = require( '../content/videos-browser' ),
    VideosController = require( '../../controllers/videos' ),
	VideosToolbar = require( '../toolbar/videos' ),
    wp = (typeof window !== "undefined" ? window['wp'] : typeof global !== "undefined" ? global['wp'] : null),
    MediaFrame = wp.media.view.MediaFrame;

InsertVideosFrame = MediaFrame.extend({

    initialize: function() {
		_.extend( this.options, {
            uploader: false,
            multiple: true
        });

		MediaFrame.prototype.initialize.apply( this, arguments );

		this.createStates();
		this.bindHandlers();

		this.setState( 'video-central-playlist-videos' );
    },
    
	createStates: function() {
		this.states.add( new VideosController({}) );
	},

	bindHandlers: function() {
		this.on( 'content:create:video-central-videos-browser', this.createCueContent, this );
		this.on( 'toolbar:create:video-central-playlist-insert-videos', this.createCueToolbar, this );
	},

	createCueContent: function( content ) {
		content.view = new VideosBrowser({
			controller: this
		});
	},

	createCueToolbar: function( toolbar ) {
		toolbar.view = new VideosToolbar({
			controller: this
		});
	},
});

module.exports = InsertVideosFrame;

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{"../../controllers/videos":2,"../content/videos-browser":6,"../toolbar/videos":9}],8:[function(require,module,exports){
(function (global){
var PostForm,
	$ = (typeof window !== "undefined" ? window['jQuery'] : typeof global !== "undefined" ? global['jQuery'] : null),
	AddVideosButton = require( './button/add-videos' ),
	VideoList = require( './video-list' ),
	wp = (typeof window !== "undefined" ? window['wp'] : typeof global !== "undefined" ? global['wp'] : null);

PostForm = wp.Backbone.View.extend({
	el: '#post',
	saved: false,

	events: {
		'click #publish': 'buttonClick',
		'click #save-post': 'buttonClick'
		//'submit': 'submit'
	},

	initialize: function( options ) {
		this.l10n = options.l10n;

		this.render();
	},

	render: function() {
		this.views.add( '#video-central-playlist-playlist-editor .video-central-playlist-panel-body', [
			new AddVideosButton({
				collection: this.collection,
				l10n: this.l10n
			}),

			new VideoList({
				collection: this.collection
			})
		]);

		return this;
	},

	buttonClick: function( e ) {
		var self = this,
			$button = $( e.target );

		if ( ! self.saved ) {
			this.collection.save().done(function( data ) {
				self.saved = true;
				$button.click();
			});
		}

		return self.saved;
	}
});

module.exports = PostForm;

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{"./button/add-videos":5,"./video-list":10}],9:[function(require,module,exports){
(function (global){
var VideosToolbar,
	_ = (typeof window !== "undefined" ? window['_'] : typeof global !== "undefined" ? global['_'] : null),
	wp = (typeof window !== "undefined" ? window['wp'] : typeof global !== "undefined" ? global['wp'] : null),
	video_central = (typeof window !== "undefined" ? window['video_central'] : typeof global !== "undefined" ? global['video_central'] : null),

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

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{}],10:[function(require,module,exports){
(function (global){
var VideoList,
	$ = (typeof window !== "undefined" ? window['jQuery'] : typeof global !== "undefined" ? global['jQuery'] : null),
	_ = (typeof window !== "undefined" ? window['_'] : typeof global !== "undefined" ? global['_'] : null),
	Video = require( './video' ),
	wp = (typeof window !== "undefined" ? window['wp'] : typeof global !== "undefined" ? global['wp'] : null);

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

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{"./video":11}],11:[function(require,module,exports){
(function (global){
var Video,
	$ = (typeof window !== "undefined" ? window['jQuery'] : typeof global !== "undefined" ? global['jQuery'] : null),
	VideoArtwork = require( './video/artwork' ),
	VideoAudio = require( './video/audio' ),
	wp = (typeof window !== "undefined" ? window['wp'] : typeof global !== "undefined" ? global['wp'] : null);

Video = wp.Backbone.View.extend({
	tagName: 'li',
	className: 'video-central-playlist-video',
	template: wp.template( 'video-central-playlist-playlist-video' ),

	events: {
		'change [data-setting]': 'updateAttribute',
		'click .js-toggle': 'toggleOpenStatus',
		'dblclick .video-central-playlist-video-title': 'toggleOpenStatus',
		'click .js-close': 'minimize',
		'click .js-remove': 'destroy'
	},

	initialize: function() {
		this.listenTo( this.model, 'change:title', this.updateTitle );
		this.listenTo( this.model, 'change', this.updateFields );
		this.listenTo( this.model, 'destroy', this.remove );
	},

	render: function() {
		this.$el.html( this.template( this.model.toJSON() ) ).data( 'cid', this.model.cid );

		this.views.add( '.video-central-playlist-video-column-artwork', new VideoArtwork({
			model: this.model,
			parent: this
		}));

		this.views.add( '.video-central-playlist-video-audio-group', new VideoAudio({
			model: this.model,
			parent: this
		}));

		return this;
	},

	minimize: function( e ) {
		e.preventDefault();
		this.$el.removeClass( 'is-open' ).find( 'input:focus' ).blur();
	},

	toggleOpenStatus: function( e ) {
		e.preventDefault();
		this.$el.toggleClass( 'is-open' ).find( 'input:focus' ).blur();

		// Trigger a resize so the media element will fill the container.
		if ( this.$el.hasClass( 'is-open' ) ) {
			$( window ).trigger( 'resize' );
		}
	},

	/**
	 * Update a model attribute when a field is changed.
	 *
	 * Fields with a 'data-setting="{{key}}"' attribute whose value
	 * corresponds to a model attribute will be automatically synced.
	 *
	 * @param {Object} e Event object.
	 */
	updateAttribute: function( e ) {
		var attribute = $( e.target ).data( 'setting' ),
			value = e.target.value;

		if ( this.model.get( attribute ) !== value ) {
			this.model.set( attribute, value );
		}
	},

	/**
	 * Update a setting field when a model's attribute is changed.
	 */
	updateFields: function() {
		var video = this.model.toJSON(),
			$settings = this.$el.find( '[data-setting]' ),
			attribute, value;

		// A change event shouldn't be triggered here, so it won't cause
		// the model attribute to be updated and get stuck in an
		// infinite loop.
		for ( attribute in video ) {
			// Decode HTML entities.
			value = $( '<div/>' ).html( video[ attribute ] ).text();
			$settings.filter( '[data-setting="' + attribute + '"]' ).val( value );
		}
	},

	updateTitle: function() {
		var title = this.model.get( 'title' );
		this.$el.find( '.video-central-playlist-video-title .text' ).text( title ? title : 'Title' );
	},

	/**
	 * Destroy the view's model.
	 *
	 * Avoid syncing to the server by triggering an event instead of
	 * calling destroy() directly on the model.
	 */
	destroy: function() {
		this.model.trigger( 'destroy', this.model );
	},

	remove: function() {
		this.$el.remove();
	}
});

module.exports = Video;

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{"./video/artwork":12,"./video/audio":13}],12:[function(require,module,exports){
(function (global){
var VideoArtwork,
	_ = (typeof window !== "undefined" ? window['_'] : typeof global !== "undefined" ? global['_'] : null),
	workflows = require( '../../workflows' ),
	wp = (typeof window !== "undefined" ? window['wp'] : typeof global !== "undefined" ? global['wp'] : null);

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

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{"../../workflows":18}],13:[function(require,module,exports){
(function (global){
var VideoAudio,
	$ = (typeof window !== "undefined" ? window['jQuery'] : typeof global !== "undefined" ? global['jQuery'] : null),
	_ = (typeof window !== "undefined" ? window['_'] : typeof global !== "undefined" ? global['_'] : null),
	settings = (typeof window !== "undefined" ? window['video_central'] : typeof global !== "undefined" ? global['video_central'] : null).settings(),
	workflows = require( '../../workflows' ),
	wp = (typeof window !== "undefined" ? window['wp'] : typeof global !== "undefined" ? global['wp'] : null);

VideoAudio = wp.Backbone.View.extend({
	tagName: 'span',
	className: 'video-central-playlist-video-audio',
	template: wp.template( 'video-central-playlist-playlist-video-audio' ),

	events: {
		'click .video-central-playlist-video-audio-selector': 'select'
	},

	initialize: function( options ) {
		this.parent = options.parent;

		this.listenTo( this.model, 'change:audioUrl', this.refresh );
		this.listenTo( this.model, 'destroy', this.cleanup );
	},

	render: function() {
		var $mediaEl, playerSettings,
			video = this.model.toJSON(),
			playerId = this.$el.find( '.mejs-audio' ).attr( 'id' );

		// Remove the MediaElement player object if the
		// audio file URL is empty.
		if ( '' === video.audioUrl && playerId ) {
			mejs.players[ playerId ].remove();
		}

		// Render the media element.
		this.$el.html( this.template( this.model.toJSON() ) );

		// Set up MediaElement.js.
		$mediaEl = this.$el.find( '.video-central-playlist-audio' );

		return this;
	},

	refresh: function( e ) {
		var video = this.model.toJSON(),
			playerId = this.$el.find( '.mejs-audio' ).attr( 'id' ),
			player = playerId ? mejs.players[ playerId ] : null;

		if ( player && '' !== video.audioUrl ) {
			player.pause();
			player.setSrc( video.audioUrl );
		} else {
			this.render();
		}
	},

	cleanup: function() {
		var playerId = this.$el.find( '.mejs-audio' ).attr( 'id' ),
			player = playerId ? mejs.players[ playerId ] : null;

		if ( player ) {
			player.remove();
		}
	},

	select: function() {
		workflows.setModel( this.model ).get( 'selectAudio' ).open();
	}
});

module.exports = VideoAudio;

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{"../../workflows":18}],14:[function(require,module,exports){
(function (global){
var Videos,
	wp = (typeof window !== "undefined" ? window['wp'] : typeof global !== "undefined" ? global['wp'] : null);

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

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{}],15:[function(require,module,exports){
(function (global){
var VideosItems,
	VideosItem = require( '../videos/item' ),
	wp = (typeof window !== "undefined" ? window['wp'] : typeof global !== "undefined" ? global['wp'] : null);

VideosItems = wp.Backbone.View.extend({
	className: 'video-central-videos-browser-list',
	tagName: 'ul',

	initialize: function( options ) {
		this.collection = options.controller.state().get( 'collection' );
		this.controller = options.controller;

		this.listenTo( this.collection, 'add', this.addItem );
		this.listenTo( this.collection, 'reset', this.render );
	},

	render: function() {
		this.collection.each( this.addItem, this );
		return this;
	},

	addItem: function( model ) {
		var view = new VideosItem({
			controller: this.controller,
			model: model
		}).render();

		this.$el.append( view.el );
	}
});

module.exports = VideosItems;

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{"../videos/item":14}],16:[function(require,module,exports){
(function (global){
var VideosNoItems,
	wp = (typeof window !== "undefined" ? window['wp'] : typeof global !== "undefined" ? global['wp'] : null);

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

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{}],17:[function(require,module,exports){
(function (global){
var VideosSidebar,
	$ = (typeof window !== "undefined" ? window['jQuery'] : typeof global !== "undefined" ? global['jQuery'] : null),
	wp = (typeof window !== "undefined" ? window['wp'] : typeof global !== "undefined" ? global['wp'] : null);

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

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{}],18:[function(require,module,exports){
(function (global){
var Workflows,
	_ = (typeof window !== "undefined" ? window['_'] : typeof global !== "undefined" ? global['_'] : null),
	video_central = (typeof window !== "undefined" ? window['video_central'] : typeof global !== "undefined" ? global['video_central'] : null),
	l10n = (typeof window !== "undefined" ? window['video_central'] : typeof global !== "undefined" ? global['video_central'] : null).l10n,
    AddVideosFrame = require( './views/frame/insert-videos' ),
	wp = (typeof window !== "undefined" ? window['wp'] : typeof global !== "undefined" ? global['wp'] : null),
	Attachment = wp.media.model.Attachment;

Workflows = {
	frames: [],
	model: {},

	/**
	 * Set a model for the current workflow.
	 *
	 * @param {Object} frame
	 */
	setModel: function( model ) {
		this.model = model;
		return this;
	},

	/**
	 * Retrieve or create a frame instance for a particular workflow.
	 *
	 * @param {string} id Frame identifer.
	 */
	get: function( id )  {
		var method = '_' + id,
			frame = this.frames[ method ] || null;

		// Always call the frame method to perform any routine set up. The
		// frame method should short-circuit before being initialized again.
		frame = this[ method ].call( this, frame );

		// Store the frame for future use.
		this.frames[ method ] = frame;

		return frame;
	},

	/**
	 * Workflow for adding videos to the playlist.
	 *
	 * @param {Object} frame
	 */
	_addVideos: function( frame ) {
		// Return the existing frame for this workflow.
		if ( frame ) {
			return frame;
		}

		// Initialize the audio frame.
        frame = new AddVideosFrame();
        
		return frame;
	},

	/**
	 * Workflow for selecting video artwork image.
	 *
	 * @param {Object} frame
	 */
	_selectArtwork: function( frame ) {
		var workflow = this;

		// Return existing frame for this workflow.
		if ( frame ) {
			return frame;
		}

		// Initialize the artwork frame.
		frame = wp.media({
			title: l10n.workflows.selectArtwork.frameTitle,
			library: {
				type: 'image'
			},
			button: {
				text: l10n.workflows.selectArtwork.frameButtonText
			},
			multiple: false
		});

		// Set the extensions that can be uploaded.
		frame.uploader.options.uploader.plupload = {
			filters: {
				mime_types: [{
					files: l10n.workflows.selectArtwork.fileTypes,
					extensions: 'jpg,jpeg,gif,png'
				}]
			}
		};

		// Automatically select the existing artwork if possible.
		frame.on( 'open', function() {
			var selection = this.get( 'library' ).get( 'selection' ),
				artworkId = workflow.model.get( 'artworkId' ),
				attachments = [];

			if ( artworkId ) {
				attachments.push( Attachment.get( artworkId ) );
				attachments[0].fetch();
			}

			selection.reset( attachments );
		});

		// Set the model's artwork ID and url properties.
		frame.state( 'library' ).on( 'select', function() {
            var attachment = this.get( 'selection' ).first().toJSON();

			workflow.model.set({
				artworkId: attachment.id,
				artworkUrl: attachment.url
			});
		});

		return frame;
    }

};

module.exports = Workflows;

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{"./views/frame/insert-videos":7}]},{},[4])
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJhc3NldHMvYWRtaW4vanMvc291cmNlL3BsYXlsaXN0L2NvbGxlY3Rpb25zL3ZpZGVvcy5qcyIsImFzc2V0cy9hZG1pbi9qcy9zb3VyY2UvcGxheWxpc3QvY29udHJvbGxlcnMvdmlkZW9zLmpzIiwiYXNzZXRzL2FkbWluL2pzL3NvdXJjZS9wbGF5bGlzdC9tb2RlbHMvdmlkZW8uanMiLCJhc3NldHMvYWRtaW4vanMvc291cmNlL3BsYXlsaXN0L3BsYXlsaXN0LWVkaXQuanMiLCJhc3NldHMvYWRtaW4vanMvc291cmNlL3BsYXlsaXN0L3ZpZXdzL2J1dHRvbi9hZGQtdmlkZW9zLmpzIiwiYXNzZXRzL2FkbWluL2pzL3NvdXJjZS9wbGF5bGlzdC92aWV3cy9jb250ZW50L3ZpZGVvcy1icm93c2VyLmpzIiwiYXNzZXRzL2FkbWluL2pzL3NvdXJjZS9wbGF5bGlzdC92aWV3cy9mcmFtZS9pbnNlcnQtdmlkZW9zLmpzIiwiYXNzZXRzL2FkbWluL2pzL3NvdXJjZS9wbGF5bGlzdC92aWV3cy9wb3N0LWZvcm0uanMiLCJhc3NldHMvYWRtaW4vanMvc291cmNlL3BsYXlsaXN0L3ZpZXdzL3Rvb2xiYXIvdmlkZW9zLmpzIiwiYXNzZXRzL2FkbWluL2pzL3NvdXJjZS9wbGF5bGlzdC92aWV3cy92aWRlby1saXN0LmpzIiwiYXNzZXRzL2FkbWluL2pzL3NvdXJjZS9wbGF5bGlzdC92aWV3cy92aWRlby5qcyIsImFzc2V0cy9hZG1pbi9qcy9zb3VyY2UvcGxheWxpc3Qvdmlld3MvdmlkZW8vYXJ0d29yay5qcyIsImFzc2V0cy9hZG1pbi9qcy9zb3VyY2UvcGxheWxpc3Qvdmlld3MvdmlkZW8vYXVkaW8uanMiLCJhc3NldHMvYWRtaW4vanMvc291cmNlL3BsYXlsaXN0L3ZpZXdzL3ZpZGVvcy9pdGVtLmpzIiwiYXNzZXRzL2FkbWluL2pzL3NvdXJjZS9wbGF5bGlzdC92aWV3cy92aWRlb3MvaXRlbXMuanMiLCJhc3NldHMvYWRtaW4vanMvc291cmNlL3BsYXlsaXN0L3ZpZXdzL3ZpZGVvcy9uby1pdGVtcy5qcyIsImFzc2V0cy9hZG1pbi9qcy9zb3VyY2UvcGxheWxpc3Qvdmlld3MvdmlkZW9zL3NpZGViYXIuanMiLCJhc3NldHMvYWRtaW4vanMvc291cmNlL3BsYXlsaXN0L3dvcmtmbG93cy5qcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTs7QUNBQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7O0FDdENBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7Ozs7QUN0Q0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7O0FDbEJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7O0FDbENBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7O0FDbENBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7O0FDdEVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7O0FDOUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7Ozs7QUNyREE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7Ozs7QUMzQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOzs7OztBQ3JEQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOzs7OztBQ2hIQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOzs7OztBQy9CQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7O0FDdkVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7O0FDL0hBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7Ozs7QUNoQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7Ozs7QUN6QkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7Ozs7QUNsQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EiLCJmaWxlIjoiZ2VuZXJhdGVkLmpzIiwic291cmNlUm9vdCI6IiIsInNvdXJjZXNDb250ZW50IjpbIihmdW5jdGlvbiBlKHQsbixyKXtmdW5jdGlvbiBzKG8sdSl7aWYoIW5bb10pe2lmKCF0W29dKXt2YXIgYT10eXBlb2YgcmVxdWlyZT09XCJmdW5jdGlvblwiJiZyZXF1aXJlO2lmKCF1JiZhKXJldHVybiBhKG8sITApO2lmKGkpcmV0dXJuIGkobywhMCk7dmFyIGY9bmV3IEVycm9yKFwiQ2Fubm90IGZpbmQgbW9kdWxlICdcIitvK1wiJ1wiKTt0aHJvdyBmLmNvZGU9XCJNT0RVTEVfTk9UX0ZPVU5EXCIsZn12YXIgbD1uW29dPXtleHBvcnRzOnt9fTt0W29dWzBdLmNhbGwobC5leHBvcnRzLGZ1bmN0aW9uKGUpe3ZhciBuPXRbb11bMV1bZV07cmV0dXJuIHMobj9uOmUpfSxsLGwuZXhwb3J0cyxlLHQsbixyKX1yZXR1cm4gbltvXS5leHBvcnRzfXZhciBpPXR5cGVvZiByZXF1aXJlPT1cImZ1bmN0aW9uXCImJnJlcXVpcmU7Zm9yKHZhciBvPTA7bzxyLmxlbmd0aDtvKyspcyhyW29dKTtyZXR1cm4gc30pIiwidmFyIFZpZGVvcyxcblx0XyA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93WydfJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWydfJ10gOiBudWxsKSxcblx0QmFja2JvbmUgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snQmFja2JvbmUnXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ0JhY2tib25lJ10gOiBudWxsKSxcblx0c2V0dGluZ3MgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1sndmlkZW9fY2VudHJhbCddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsndmlkZW9fY2VudHJhbCddIDogbnVsbCkuc2V0dGluZ3MoKSxcblx0VmlkZW8gPSByZXF1aXJlKCAnLi4vbW9kZWxzL3ZpZGVvJyApLFxuXHR3cCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93Wyd3cCddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnd3AnXSA6IG51bGwpO1xuXG5WaWRlb3MgPSBCYWNrYm9uZS5Db2xsZWN0aW9uLmV4dGVuZCh7XG5cdG1vZGVsOiBWaWRlbyxcblxuXHRjb21wYXJhdG9yOiBmdW5jdGlvbiggdmlkZW8gKSB7XG5cdFx0cmV0dXJuIHBhcnNlSW50KCB2aWRlby5nZXQoICdvcmRlcicgKSwgMTAgKTtcblx0fSxcblxuXHRmZXRjaDogZnVuY3Rpb24oKSB7XG5cdFx0dmFyIGNvbGxlY3Rpb24gPSB0aGlzO1xuXG5cdFx0cmV0dXJuIHdwLmFqYXgucG9zdCggJ3ZpZGVvX2NlbnRyYWxfZ2V0X3BsYXlsaXN0X3ZpZGVvcycsIHtcblx0XHRcdHBvc3RfaWQ6IHNldHRpbmdzLnBvc3RJZFxuXHRcdH0pLmRvbmUoZnVuY3Rpb24oIHZpZGVvcyApIHtcblx0XHRcdGNvbGxlY3Rpb24ucmVzZXQoIHZpZGVvcyApO1xuXHRcdH0pO1xuXHR9LFxuXG5cdHNhdmU6IGZ1bmN0aW9uKCBkYXRhICkge1xuXHRcdHRoaXMuc29ydCgpO1xuXG5cdFx0ZGF0YSA9IF8uZXh0ZW5kKHt9LCBkYXRhLCB7XG5cdFx0XHRwb3N0X2lkOiBzZXR0aW5ncy5wb3N0SWQsXG5cdFx0XHR2aWRlb3M6IHRoaXMudG9KU09OKCksXG5cdFx0XHRub25jZTogc2V0dGluZ3Muc2F2ZU5vbmNlXG5cdFx0fSk7XG5cblx0XHRyZXR1cm4gd3AuYWpheC5wb3N0KCAndmlkZW9fY2VudHJhbF9zYXZlX3BsYXlsaXN0X3ZpZGVvcycsIGRhdGEgKTtcblx0fVxufSk7XG5cbm1vZHVsZS5leHBvcnRzID0gVmlkZW9zO1xuIiwidmFyIFZpZGVvcyxcblx0QmFja2JvbmUgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snQmFja2JvbmUnXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ0JhY2tib25lJ10gOiBudWxsKSxcblx0bDEwbiA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93Wyd2aWRlb19jZW50cmFsJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWyd2aWRlb19jZW50cmFsJ10gOiBudWxsKS5sMTBuLFxuXHR3cCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93Wyd3cCddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnd3AnXSA6IG51bGwpO1xuICAgIFxuVmlkZW9zID0gd3AubWVkaWEuY29udHJvbGxlci5TdGF0ZS5leHRlbmQoe1xuXHRkZWZhdWx0czoge1xuXHRcdGlkICAgICAgICA6ICd2aWRlby1jZW50cmFsLXBsYXlsaXN0LXZpZGVvcycsXG5cdFx0dGl0bGUgICAgIDogbDEwbi5pbnNlcnRWaWRlb3MgfHwgJ0luc2VydCBWaWRlb3MnLFxuICAgICAgICBjb2xsZWN0aW9uOiBudWxsLFxuICAgICAgICBzZWxlY3Rpb246IG51bGwsXG5cdFx0Y29udGVudCAgIDogJ3ZpZGVvLWNlbnRyYWwtdmlkZW9zLWJyb3dzZXInLFxuXHRcdG1lbnUgICAgICA6ICdkZWZhdWx0Jyxcblx0XHRtZW51SXRlbSAgOiB7XG5cdFx0XHR0ZXh0ICAgIDogbDEwbi5pbnNlcnRGcm9tVmlkZW9DZW50cmFsIHx8ICdJbnNlcnQgZnJvbSBWaWRlbyBDZW50cmFsJyxcblx0XHRcdHByaW9yaXR5OiAxXG4gICAgICAgIH0sXG4gICAgICAgIG11bHRpcGxlIDogdHJ1ZSxcbiAgICAgICAgdG9vbGJhciAgOiAndmlkZW8tY2VudHJhbC1wbGF5bGlzdC1pbnNlcnQtdmlkZW9zJ1xuXHR9LFxuXG5cdGluaXRpYWxpemU6IGZ1bmN0aW9uKCBvcHRpb25zICkge1xuXHRcdHZhciBjb2xsZWN0aW9uID0gb3B0aW9ucy5jb2xsZWN0aW9uIHx8IG5ldyBCYWNrYm9uZS5Db2xsZWN0aW9uKCksXG5cdFx0XHRzZWxlY3Rpb24gPSBvcHRpb25zLnNlbGVjdGlvbiB8fCBuZXcgQmFja2JvbmUuQ29sbGVjdGlvbigpO1xuICAgXG5cdFx0dGhpcy5zZXQoICdhdHRyaWJ1dGVzJywgbmV3IEJhY2tib25lLk1vZGVsKHtcblx0XHRcdGlkOiBudWxsLFxuXHRcdFx0c2hvd192aWRlb3M6IHRydWVcblx0XHR9KSApO1xuXG5cdFx0dGhpcy5zZXQoICdjb2xsZWN0aW9uJywgY29sbGVjdGlvbiApO1xuICAgICAgICB0aGlzLnNldCggJ3NlbGVjdGlvbicsIHNlbGVjdGlvbiApO1xuICAgICAgICBcblx0XHR0aGlzLmxpc3RlblRvKCBzZWxlY3Rpb24sICdyZW1vdmUnLCB0aGlzLnVwZGF0ZVNlbGVjdGlvbiApO1xuXHR9XG59KTtcblxubW9kdWxlLmV4cG9ydHMgPSBWaWRlb3M7XG4iLCJ2YXIgVmlkZW8sXG5cdEJhY2tib25lID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ0JhY2tib25lJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWydCYWNrYm9uZSddIDogbnVsbCk7XG5cblZpZGVvID0gQmFja2JvbmUuTW9kZWwuZXh0ZW5kKHtcblx0ZGVmYXVsdHM6IHtcblx0XHRhcnRpc3Q6ICcnLFxuXHRcdGFydHdvcmtJZDogJycsXG5cdFx0YXJ0d29ya1VybDogJycsXG5cdFx0dmlkZW9JZDogJycsXG5cdFx0YXVkaW9Vcmw6ICcnLFxuXHRcdGZvcm1hdDogJycsXG5cdFx0bGVuZ3RoOiAnJyxcblx0XHR0aXRsZTogJycsXG5cdFx0b3JkZXI6IDBcblx0fVxufSk7XG5cbm1vZHVsZS5leHBvcnRzID0gVmlkZW87XG4iLCJ2YXIgdmlkZW9fY2VudHJhbCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93Wyd2aWRlb19jZW50cmFsJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWyd2aWRlb19jZW50cmFsJ10gOiBudWxsKTtcbnZhciB3cCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93Wyd3cCddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnd3AnXSA6IG51bGwpO1xuXG52aWRlb19jZW50cmFsLmRhdGEgPSB2aWRlb0NlbnRyYWxQbGF5bGlzdENvbmZpZztcbnZpZGVvX2NlbnRyYWwuc2V0dGluZ3MoIHZpZGVvQ2VudHJhbFBsYXlsaXN0Q29uZmlnICk7XG5cbndwLm1lZGlhLnZpZXcuc2V0dGluZ3MucG9zdC5pZCA9IHZpZGVvX2NlbnRyYWwuZGF0YS5wb3N0SWQ7XG53cC5tZWRpYS52aWV3LnNldHRpbmdzLmRlZmF1bHRQcm9wcyA9IHt9O1xuXG52aWRlb19jZW50cmFsLm1vZGVsLlZpZGVvID0gcmVxdWlyZSggJy4vbW9kZWxzL3ZpZGVvJyApO1xudmlkZW9fY2VudHJhbC5tb2RlbC5WaWRlb3MgPSByZXF1aXJlKCAnLi9jb2xsZWN0aW9ucy92aWRlb3MnICk7XG5cbnZpZGVvX2NlbnRyYWwudmlldy5Qb3N0Rm9ybSA9IHJlcXVpcmUoICcuL3ZpZXdzL3Bvc3QtZm9ybScgKTtcbnZpZGVvX2NlbnRyYWwudmlldy5BZGRWaWRlb3NCdXR0b24gPSByZXF1aXJlKCAnLi92aWV3cy9idXR0b24vYWRkLXZpZGVvcycgKTtcbnZpZGVvX2NlbnRyYWwudmlldy5WaWRlb0xpc3QgPSByZXF1aXJlKCAnLi92aWV3cy92aWRlby1saXN0JyApO1xudmlkZW9fY2VudHJhbC52aWV3LlZpZGVvID0gcmVxdWlyZSggJy4vdmlld3MvdmlkZW8nICk7XG52aWRlb19jZW50cmFsLnZpZXcuVmlkZW9BcnR3b3JrID0gcmVxdWlyZSggJy4vdmlld3MvdmlkZW8vYXJ0d29yaycgKTtcbnZpZGVvX2NlbnRyYWwudmlldy5WaWRlb0F1ZGlvID0gcmVxdWlyZSggJy4vdmlld3MvdmlkZW8vYXVkaW8nICk7XG5cbnZpZGVvX2NlbnRyYWwud29ya2Zsb3dzID0gcmVxdWlyZSggJy4vd29ya2Zsb3dzJyApO1xuXG4oIGZ1bmN0aW9uKCAkICkge1xuICAgIHZhciB2aWRlb3M7XG5cblx0dmlkZW9zID0gdmlkZW9fY2VudHJhbC52aWRlb3MgPSBuZXcgdmlkZW9fY2VudHJhbC5tb2RlbC5WaWRlb3MoIHZpZGVvX2NlbnRyYWwuZGF0YS52aWRlb3MgKTtcblx0ZGVsZXRlIHZpZGVvX2NlbnRyYWwuZGF0YS52aWRlb3M7XG5cblx0dmFyIHBvc3RGb3JtID0gbmV3IHZpZGVvX2NlbnRyYWwudmlldy5Qb3N0Rm9ybSh7XG5cdFx0Y29sbGVjdGlvbjogdmlkZW9zLFxuXHRcdGwxMG46IHZpZGVvX2NlbnRyYWwubDEwblxuICAgIH0pO1xuICAgIFxufSAoIGpRdWVyeSApKTtcblxuIiwidmFyIEFkZFZpZGVvc0J1dHRvbixcblx0JCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93WydqUXVlcnknXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ2pRdWVyeSddIDogbnVsbCksXG5cdHdvcmtmbG93cyA9IHJlcXVpcmUoICcuLi8uLi93b3JrZmxvd3MnICksXG5cdHdwID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ3dwJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWyd3cCddIDogbnVsbCk7XG5cbkFkZFZpZGVvc0J1dHRvbiA9IHdwLkJhY2tib25lLlZpZXcuZXh0ZW5kKHtcblx0aWQ6ICdhZGQtdmlkZW9zJyxcblx0dGFnTmFtZTogJ3AnLFxuXG5cdGV2ZW50czoge1xuXHRcdCdjbGljayAuYnV0dG9uJzogJ2NsaWNrJ1xuXHR9LFxuXG5cdGluaXRpYWxpemU6IGZ1bmN0aW9uKCBvcHRpb25zICkge1xuXHRcdHRoaXMubDEwbiA9IG9wdGlvbnMubDEwbjtcblx0fSxcblxuXHRyZW5kZXI6IGZ1bmN0aW9uKCkge1xuXHRcdHZhciAkYnV0dG9uID0gJCggJzxhIC8+Jywge1xuXHRcdFx0dGV4dDogdGhpcy5sMTBuLmFkZFZpZGVvc1xuXHRcdH0pLmFkZENsYXNzKCAnYnV0dG9uIGJ1dHRvbi1zZWNvbmRhcnknICk7XG5cblx0XHR0aGlzLiRlbC5odG1sKCAkYnV0dG9uICk7XG5cblx0XHRyZXR1cm4gdGhpcztcblx0fSxcblxuXHRjbGljazogZnVuY3Rpb24oIGUgKSB7XG5cdFx0ZS5wcmV2ZW50RGVmYXVsdCgpO1xuXHRcdHdvcmtmbG93cy5nZXQoICdhZGRWaWRlb3MnICkub3BlbigpO1xuXHR9XG59KTtcblxubW9kdWxlLmV4cG9ydHMgPSBBZGRWaWRlb3NCdXR0b247XG4iLCJ2YXIgVmlkZW9zQnJvd3Nlcixcblx0XyA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93WydfJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWydfJ10gOiBudWxsKSxcblx0VmlkZW9zSXRlbXMgPSByZXF1aXJlKCAnLi4vdmlkZW9zL2l0ZW1zJyApLFxuXHRWaWRlb3NOb0l0ZW1zID0gcmVxdWlyZSggJy4uL3ZpZGVvcy9uby1pdGVtcycgKSxcblx0VmlkZW9zU2lkZWJhciA9IHJlcXVpcmUoICcuLi92aWRlb3Mvc2lkZWJhcicgKSxcblx0d3AgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snd3AnXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ3dwJ10gOiBudWxsKTtcblxuVmlkZW9zQnJvd3NlciA9IHdwLkJhY2tib25lLlZpZXcuZXh0ZW5kKHtcblx0Y2xhc3NOYW1lOiAndmlkZW8tY2VudHJhbC12aWRlb3MtYnJvd3NlcicsXG5cblx0aW5pdGlhbGl6ZTogZnVuY3Rpb24oIG9wdGlvbnMgKSB7XG5cdFx0dGhpcy5jb2xsZWN0aW9uID0gb3B0aW9ucy5jb250cm9sbGVyLnN0YXRlKCkuZ2V0KCAnY29sbGVjdGlvbicgKTtcblx0XHR0aGlzLmNvbnRyb2xsZXIgPSBvcHRpb25zLmNvbnRyb2xsZXI7XG5cblx0XHR0aGlzLl9wYWdlZCA9IDE7XG5cdFx0dGhpcy5fcGVuZGluZyA9IGZhbHNlO1xuXG5cdFx0Xy5iaW5kQWxsKCB0aGlzLCAnc2Nyb2xsJyApO1xuICAgICAgICB0aGlzLmxpc3RlblRvKCB0aGlzLmNvbGxlY3Rpb24sICdyZXNldCcsIHRoaXMucmVuZGVyICk7XG4gICAgICAgIFxuICAgICAgICBpZiAoICEgdGhpcy5jb2xsZWN0aW9uLmxlbmd0aCApIHtcblx0XHRcdHRoaXMuZ2V0VmlkZW9zKCk7XG5cdFx0fVxuXHR9LFxuXG5cdHJlbmRlcjogZnVuY3Rpb24oKSB7XG5cdFx0dGhpcy4kZWwub2ZmKCAnc2Nyb2xsJyApLm9uKCAnc2Nyb2xsJywgdGhpcy5zY3JvbGwgKTtcblxuXHRcdHRoaXMudmlld3MuYWRkKFtcblx0XHRcdG5ldyBWaWRlb3NJdGVtcyh7XG5cdFx0XHRcdGNvbGxlY3Rpb246IHRoaXMuY29sbGVjdGlvbixcblx0XHRcdFx0Y29udHJvbGxlcjogdGhpcy5jb250cm9sbGVyXG5cdFx0XHR9KSxcblx0XHRcdG5ldyBWaWRlb3NTaWRlYmFyKHtcblx0XHRcdFx0Y29udHJvbGxlcjogdGhpcy5jb250cm9sbGVyXG5cdFx0XHR9KSxcblx0XHRcdG5ldyBWaWRlb3NOb0l0ZW1zKHtcblx0XHRcdFx0Y29sbGVjdGlvbjogdGhpcy5jb2xsZWN0aW9uXG5cdFx0XHR9KVxuXHRcdF0pO1xuXG5cdFx0cmV0dXJuIHRoaXM7XG5cdH0sXG5cblx0c2Nyb2xsOiBmdW5jdGlvbigpIHtcblx0XHRpZiAoICEgdGhpcy5fcGVuZGluZyAmJiB0aGlzLmVsLnNjcm9sbEhlaWdodCA8IHRoaXMuZWwuc2Nyb2xsVG9wICsgdGhpcy5lbC5jbGllbnRIZWlnaHQgKiAzICkge1xuXHRcdFx0dGhpcy5fcGVuZGluZyA9IHRydWU7XG5cdFx0XHR0aGlzLmdldFZpZGVvcygpO1xuXHRcdH1cblx0fSxcblxuXHRnZXRWaWRlb3M6IGZ1bmN0aW9uKCkge1xuXHRcdHZhciB2aWV3ID0gdGhpcztcblxuXHRcdHdwLmFqYXgucG9zdCggJ3ZpZGVvX2NlbnRyYWxfZ2V0X3ZpZGVvc19mb3JfZnJhbWUnLCB7XG5cdFx0XHRwYWdlZDogdmlldy5fcGFnZWRcblx0XHR9KS5kb25lKGZ1bmN0aW9uKCByZXNwb25zZSApIHtcblx0XHRcdHZpZXcuY29sbGVjdGlvbi5hZGQoIHJlc3BvbnNlLnZpZGVvcyApO1xuXG5cdFx0XHR2aWV3Ll9wYWdlZCsrO1xuXG5cdFx0XHRpZiAoIHZpZXcuX3BhZ2VkIDw9IHJlc3BvbnNlLm1heE51bVBhZ2VzICkge1xuXHRcdFx0XHR2aWV3Ll9wZW5kaW5nID0gZmFsc2U7XG5cdFx0XHRcdHZpZXcuc2Nyb2xsKCk7XG5cdFx0XHR9XG5cdFx0fSk7XG5cdH1cbn0pO1xuXG5tb2R1bGUuZXhwb3J0cyA9IFZpZGVvc0Jyb3dzZXI7XG4iLCJ2YXIgSW5zZXJ0VmlkZW9zRnJhbWUsXG5cdFZpZGVvc0Jyb3dzZXIgPSByZXF1aXJlKCAnLi4vY29udGVudC92aWRlb3MtYnJvd3NlcicgKSxcbiAgICBWaWRlb3NDb250cm9sbGVyID0gcmVxdWlyZSggJy4uLy4uL2NvbnRyb2xsZXJzL3ZpZGVvcycgKSxcblx0VmlkZW9zVG9vbGJhciA9IHJlcXVpcmUoICcuLi90b29sYmFyL3ZpZGVvcycgKSxcbiAgICB3cCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93Wyd3cCddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnd3AnXSA6IG51bGwpLFxuICAgIE1lZGlhRnJhbWUgPSB3cC5tZWRpYS52aWV3Lk1lZGlhRnJhbWU7XG5cbkluc2VydFZpZGVvc0ZyYW1lID0gTWVkaWFGcmFtZS5leHRlbmQoe1xuXG4gICAgaW5pdGlhbGl6ZTogZnVuY3Rpb24oKSB7XG5cdFx0Xy5leHRlbmQoIHRoaXMub3B0aW9ucywge1xuICAgICAgICAgICAgdXBsb2FkZXI6IGZhbHNlLFxuICAgICAgICAgICAgbXVsdGlwbGU6IHRydWVcbiAgICAgICAgfSk7XG5cblx0XHRNZWRpYUZyYW1lLnByb3RvdHlwZS5pbml0aWFsaXplLmFwcGx5KCB0aGlzLCBhcmd1bWVudHMgKTtcblxuXHRcdHRoaXMuY3JlYXRlU3RhdGVzKCk7XG5cdFx0dGhpcy5iaW5kSGFuZGxlcnMoKTtcblxuXHRcdHRoaXMuc2V0U3RhdGUoICd2aWRlby1jZW50cmFsLXBsYXlsaXN0LXZpZGVvcycgKTtcbiAgICB9LFxuICAgIFxuXHRjcmVhdGVTdGF0ZXM6IGZ1bmN0aW9uKCkge1xuXHRcdHRoaXMuc3RhdGVzLmFkZCggbmV3IFZpZGVvc0NvbnRyb2xsZXIoe30pICk7XG5cdH0sXG5cblx0YmluZEhhbmRsZXJzOiBmdW5jdGlvbigpIHtcblx0XHR0aGlzLm9uKCAnY29udGVudDpjcmVhdGU6dmlkZW8tY2VudHJhbC12aWRlb3MtYnJvd3NlcicsIHRoaXMuY3JlYXRlQ3VlQ29udGVudCwgdGhpcyApO1xuXHRcdHRoaXMub24oICd0b29sYmFyOmNyZWF0ZTp2aWRlby1jZW50cmFsLXBsYXlsaXN0LWluc2VydC12aWRlb3MnLCB0aGlzLmNyZWF0ZUN1ZVRvb2xiYXIsIHRoaXMgKTtcblx0fSxcblxuXHRjcmVhdGVDdWVDb250ZW50OiBmdW5jdGlvbiggY29udGVudCApIHtcblx0XHRjb250ZW50LnZpZXcgPSBuZXcgVmlkZW9zQnJvd3Nlcih7XG5cdFx0XHRjb250cm9sbGVyOiB0aGlzXG5cdFx0fSk7XG5cdH0sXG5cblx0Y3JlYXRlQ3VlVG9vbGJhcjogZnVuY3Rpb24oIHRvb2xiYXIgKSB7XG5cdFx0dG9vbGJhci52aWV3ID0gbmV3IFZpZGVvc1Rvb2xiYXIoe1xuXHRcdFx0Y29udHJvbGxlcjogdGhpc1xuXHRcdH0pO1xuXHR9LFxufSk7XG5cbm1vZHVsZS5leHBvcnRzID0gSW5zZXJ0VmlkZW9zRnJhbWU7XG4iLCJ2YXIgUG9zdEZvcm0sXG5cdCQgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snalF1ZXJ5J10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWydqUXVlcnknXSA6IG51bGwpLFxuXHRBZGRWaWRlb3NCdXR0b24gPSByZXF1aXJlKCAnLi9idXR0b24vYWRkLXZpZGVvcycgKSxcblx0VmlkZW9MaXN0ID0gcmVxdWlyZSggJy4vdmlkZW8tbGlzdCcgKSxcblx0d3AgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snd3AnXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ3dwJ10gOiBudWxsKTtcblxuUG9zdEZvcm0gPSB3cC5CYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG5cdGVsOiAnI3Bvc3QnLFxuXHRzYXZlZDogZmFsc2UsXG5cblx0ZXZlbnRzOiB7XG5cdFx0J2NsaWNrICNwdWJsaXNoJzogJ2J1dHRvbkNsaWNrJyxcblx0XHQnY2xpY2sgI3NhdmUtcG9zdCc6ICdidXR0b25DbGljaydcblx0XHQvLydzdWJtaXQnOiAnc3VibWl0J1xuXHR9LFxuXG5cdGluaXRpYWxpemU6IGZ1bmN0aW9uKCBvcHRpb25zICkge1xuXHRcdHRoaXMubDEwbiA9IG9wdGlvbnMubDEwbjtcblxuXHRcdHRoaXMucmVuZGVyKCk7XG5cdH0sXG5cblx0cmVuZGVyOiBmdW5jdGlvbigpIHtcblx0XHR0aGlzLnZpZXdzLmFkZCggJyN2aWRlby1jZW50cmFsLXBsYXlsaXN0LXBsYXlsaXN0LWVkaXRvciAudmlkZW8tY2VudHJhbC1wbGF5bGlzdC1wYW5lbC1ib2R5JywgW1xuXHRcdFx0bmV3IEFkZFZpZGVvc0J1dHRvbih7XG5cdFx0XHRcdGNvbGxlY3Rpb246IHRoaXMuY29sbGVjdGlvbixcblx0XHRcdFx0bDEwbjogdGhpcy5sMTBuXG5cdFx0XHR9KSxcblxuXHRcdFx0bmV3IFZpZGVvTGlzdCh7XG5cdFx0XHRcdGNvbGxlY3Rpb246IHRoaXMuY29sbGVjdGlvblxuXHRcdFx0fSlcblx0XHRdKTtcblxuXHRcdHJldHVybiB0aGlzO1xuXHR9LFxuXG5cdGJ1dHRvbkNsaWNrOiBmdW5jdGlvbiggZSApIHtcblx0XHR2YXIgc2VsZiA9IHRoaXMsXG5cdFx0XHQkYnV0dG9uID0gJCggZS50YXJnZXQgKTtcblxuXHRcdGlmICggISBzZWxmLnNhdmVkICkge1xuXHRcdFx0dGhpcy5jb2xsZWN0aW9uLnNhdmUoKS5kb25lKGZ1bmN0aW9uKCBkYXRhICkge1xuXHRcdFx0XHRzZWxmLnNhdmVkID0gdHJ1ZTtcblx0XHRcdFx0JGJ1dHRvbi5jbGljaygpO1xuXHRcdFx0fSk7XG5cdFx0fVxuXG5cdFx0cmV0dXJuIHNlbGYuc2F2ZWQ7XG5cdH1cbn0pO1xuXG5tb2R1bGUuZXhwb3J0cyA9IFBvc3RGb3JtO1xuIiwidmFyIFZpZGVvc1Rvb2xiYXIsXG5cdF8gPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snXyddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnXyddIDogbnVsbCksXG5cdHdwID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ3dwJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWyd3cCddIDogbnVsbCksXG5cdHZpZGVvX2NlbnRyYWwgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1sndmlkZW9fY2VudHJhbCddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsndmlkZW9fY2VudHJhbCddIDogbnVsbCksXG5cblZpZGVvc1Rvb2xiYXIgPSB3cC5tZWRpYS52aWV3LlRvb2xiYXIuZXh0ZW5kKHtcblx0aW5pdGlhbGl6ZTogZnVuY3Rpb24oIG9wdGlvbnMgKSB7XG5cdFx0dGhpcy5jb250cm9sbGVyID0gb3B0aW9ucy5jb250cm9sbGVyO1xuXG5cdFx0Xy5iaW5kQWxsKCB0aGlzLCAnaW5zZXJ0VmlkZW9zJyApO1xuXG5cdFx0Ly8gVGhpcyBpcyBhIGJ1dHRvbi5cblx0XHR0aGlzLm9wdGlvbnMuaXRlbXMgPSBfLmRlZmF1bHRzKCB0aGlzLm9wdGlvbnMuaXRlbXMgfHwge30sIHtcblx0XHRcdGluc2VydDoge1xuXHRcdFx0XHR0ZXh0OiB3cC5tZWRpYS52aWV3LmwxMG4uaW5zZXJ0SW50b1BsYXlsaXN0IHx8ICdJbnNlcnQgaW50byBwbGF5bGlzdCcsXG5cdFx0XHRcdHN0eWxlOiAncHJpbWFyeScsXG5cdFx0XHRcdHByaW9yaXR5OiA4MCxcblx0XHRcdFx0cmVxdWlyZXM6IHtcblx0XHRcdFx0XHRzZWxlY3Rpb246IHRydWVcblx0XHRcdFx0fSxcblx0XHRcdFx0Y2xpY2s6IHRoaXMuaW5zZXJ0VmlkZW9zXG5cdFx0XHR9XG5cdFx0fSk7XG5cblx0XHR3cC5tZWRpYS52aWV3LlRvb2xiYXIucHJvdG90eXBlLmluaXRpYWxpemUuYXBwbHkoIHRoaXMsIGFyZ3VtZW50cyApO1xuXHR9LFxuXG5cdGluc2VydFZpZGVvczogZnVuY3Rpb24oKSB7XG4gICAgICAgIHZhciBzdGF0ZSA9IHRoaXMuY29udHJvbGxlci5zdGF0ZSgpLCBcbiAgICAgICAgICAgIHNlbGVjdGlvbiA9IHN0YXRlLmdldCggJ3NlbGVjdGlvbicgKTtcbiAgICAgICAgICAgIFxuICAgICAgICBfLmVhY2goIHNlbGVjdGlvbi5tb2RlbHMsIGZ1bmN0aW9uKCBhdHRhY2htZW50ICkge1xuICAgICAgICAgICAgYXR0YWNobWVudC5zZXQoICd2aWRlb0lkJywgYXR0YWNobWVudC5nZXQoJ2lkJykgKTtcbiAgICAgICAgICAgIHZpZGVvX2NlbnRyYWwudmlkZW9zLnB1c2goIGF0dGFjaG1lbnQudG9KU09OKCkgKTtcbiAgICAgICAgfSk7XG5cbiAgICAgICAgdGhpcy5jb250cm9sbGVyLmNsb3NlKCk7XG4gICAgICAgIFxuICAgICAgICBzdGF0ZS50cmlnZ2VyKCAnaW5zZXJ0Jywgc2VsZWN0aW9uICkucmVzZXQoKTtcblx0fVxufSk7XG5cbm1vZHVsZS5leHBvcnRzID0gVmlkZW9zVG9vbGJhcjtcbiIsInZhciBWaWRlb0xpc3QsXG5cdCQgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snalF1ZXJ5J10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWydqUXVlcnknXSA6IG51bGwpLFxuXHRfID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ18nXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ18nXSA6IG51bGwpLFxuXHRWaWRlbyA9IHJlcXVpcmUoICcuL3ZpZGVvJyApLFxuXHR3cCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93Wyd3cCddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnd3AnXSA6IG51bGwpO1xuXG5WaWRlb0xpc3QgPSB3cC5CYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG5cdGNsYXNzTmFtZTogJ3ZpZGVvLWNlbnRyYWwtcGxheWxpc3QtdmlkZW9saXN0Jyxcblx0dGFnTmFtZTogJ29sJyxcblxuXHRpbml0aWFsaXplOiBmdW5jdGlvbigpIHtcblx0XHR0aGlzLmxpc3RlblRvKCB0aGlzLmNvbGxlY3Rpb24sICdhZGQnLCB0aGlzLmFkZFZpZGVvICk7XG5cdFx0dGhpcy5saXN0ZW5UbyggdGhpcy5jb2xsZWN0aW9uLCAnYWRkIHJlbW92ZScsIHRoaXMudXBkYXRlT3JkZXIgKTtcblx0XHR0aGlzLmxpc3RlblRvKCB0aGlzLmNvbGxlY3Rpb24sICdyZXNldCcsIHRoaXMucmVuZGVyICk7XG5cdH0sXG5cblx0cmVuZGVyOiBmdW5jdGlvbigpIHtcblx0XHR0aGlzLiRlbC5lbXB0eSgpO1xuXG5cdFx0dGhpcy5jb2xsZWN0aW9uLmVhY2goIHRoaXMuYWRkVmlkZW8sIHRoaXMgKTtcblx0XHR0aGlzLnVwZGF0ZU9yZGVyKCk7XG5cblx0XHR0aGlzLiRlbC5zb3J0YWJsZSgge1xuXHRcdFx0YXhpczogJ3knLFxuXHRcdFx0ZGVsYXk6IDE1MCxcblx0XHRcdGZvcmNlSGVscGVyU2l6ZTogdHJ1ZSxcblx0XHRcdGZvcmNlUGxhY2Vob2xkZXJTaXplOiB0cnVlLFxuXHRcdFx0b3BhY2l0eTogMC42LFxuXHRcdFx0c3RhcnQ6IGZ1bmN0aW9uKCBlLCB1aSApIHtcblx0XHRcdFx0dWkucGxhY2Vob2xkZXIuY3NzKCAndmlzaWJpbGl0eScsICd2aXNpYmxlJyApO1xuXHRcdFx0fSxcblx0XHRcdHVwZGF0ZTogXy5iaW5kKGZ1bmN0aW9uKCBlLCB1aSApIHtcblx0XHRcdFx0dGhpcy51cGRhdGVPcmRlcigpO1xuXHRcdFx0fSwgdGhpcyApXG5cdFx0fSApO1xuXG5cdFx0cmV0dXJuIHRoaXM7XG5cdH0sXG5cblx0YWRkVmlkZW86IGZ1bmN0aW9uKCB2aWRlbyApIHtcblx0XHR2YXIgdmlkZW9WaWV3ID0gbmV3IFZpZGVvKHsgbW9kZWw6IHZpZGVvIH0pO1xuXHRcdHRoaXMuJGVsLmFwcGVuZCggdmlkZW9WaWV3LnJlbmRlcigpLmVsICk7XG5cdH0sXG5cblx0dXBkYXRlT3JkZXI6IGZ1bmN0aW9uKCkge1xuXHRcdF8uZWFjaCggdGhpcy4kZWwuZmluZCggJy52aWRlby1jZW50cmFsLXBsYXlsaXN0LXZpZGVvJyApLCBmdW5jdGlvbiggaXRlbSwgaSApIHtcblx0XHRcdHZhciBjaWQgPSAkKCBpdGVtICkuZGF0YSggJ2NpZCcgKTtcblx0XHRcdHRoaXMuY29sbGVjdGlvbi5nZXQoIGNpZCApLnNldCggJ29yZGVyJywgaSApO1xuXHRcdH0sIHRoaXMgKTtcblx0fVxufSk7XG5cbm1vZHVsZS5leHBvcnRzID0gVmlkZW9MaXN0O1xuIiwidmFyIFZpZGVvLFxuXHQkID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ2pRdWVyeSddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnalF1ZXJ5J10gOiBudWxsKSxcblx0VmlkZW9BcnR3b3JrID0gcmVxdWlyZSggJy4vdmlkZW8vYXJ0d29yaycgKSxcblx0VmlkZW9BdWRpbyA9IHJlcXVpcmUoICcuL3ZpZGVvL2F1ZGlvJyApLFxuXHR3cCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93Wyd3cCddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnd3AnXSA6IG51bGwpO1xuXG5WaWRlbyA9IHdwLkJhY2tib25lLlZpZXcuZXh0ZW5kKHtcblx0dGFnTmFtZTogJ2xpJyxcblx0Y2xhc3NOYW1lOiAndmlkZW8tY2VudHJhbC1wbGF5bGlzdC12aWRlbycsXG5cdHRlbXBsYXRlOiB3cC50ZW1wbGF0ZSggJ3ZpZGVvLWNlbnRyYWwtcGxheWxpc3QtcGxheWxpc3QtdmlkZW8nICksXG5cblx0ZXZlbnRzOiB7XG5cdFx0J2NoYW5nZSBbZGF0YS1zZXR0aW5nXSc6ICd1cGRhdGVBdHRyaWJ1dGUnLFxuXHRcdCdjbGljayAuanMtdG9nZ2xlJzogJ3RvZ2dsZU9wZW5TdGF0dXMnLFxuXHRcdCdkYmxjbGljayAudmlkZW8tY2VudHJhbC1wbGF5bGlzdC12aWRlby10aXRsZSc6ICd0b2dnbGVPcGVuU3RhdHVzJyxcblx0XHQnY2xpY2sgLmpzLWNsb3NlJzogJ21pbmltaXplJyxcblx0XHQnY2xpY2sgLmpzLXJlbW92ZSc6ICdkZXN0cm95J1xuXHR9LFxuXG5cdGluaXRpYWxpemU6IGZ1bmN0aW9uKCkge1xuXHRcdHRoaXMubGlzdGVuVG8oIHRoaXMubW9kZWwsICdjaGFuZ2U6dGl0bGUnLCB0aGlzLnVwZGF0ZVRpdGxlICk7XG5cdFx0dGhpcy5saXN0ZW5UbyggdGhpcy5tb2RlbCwgJ2NoYW5nZScsIHRoaXMudXBkYXRlRmllbGRzICk7XG5cdFx0dGhpcy5saXN0ZW5UbyggdGhpcy5tb2RlbCwgJ2Rlc3Ryb3knLCB0aGlzLnJlbW92ZSApO1xuXHR9LFxuXG5cdHJlbmRlcjogZnVuY3Rpb24oKSB7XG5cdFx0dGhpcy4kZWwuaHRtbCggdGhpcy50ZW1wbGF0ZSggdGhpcy5tb2RlbC50b0pTT04oKSApICkuZGF0YSggJ2NpZCcsIHRoaXMubW9kZWwuY2lkICk7XG5cblx0XHR0aGlzLnZpZXdzLmFkZCggJy52aWRlby1jZW50cmFsLXBsYXlsaXN0LXZpZGVvLWNvbHVtbi1hcnR3b3JrJywgbmV3IFZpZGVvQXJ0d29yayh7XG5cdFx0XHRtb2RlbDogdGhpcy5tb2RlbCxcblx0XHRcdHBhcmVudDogdGhpc1xuXHRcdH0pKTtcblxuXHRcdHRoaXMudmlld3MuYWRkKCAnLnZpZGVvLWNlbnRyYWwtcGxheWxpc3QtdmlkZW8tYXVkaW8tZ3JvdXAnLCBuZXcgVmlkZW9BdWRpbyh7XG5cdFx0XHRtb2RlbDogdGhpcy5tb2RlbCxcblx0XHRcdHBhcmVudDogdGhpc1xuXHRcdH0pKTtcblxuXHRcdHJldHVybiB0aGlzO1xuXHR9LFxuXG5cdG1pbmltaXplOiBmdW5jdGlvbiggZSApIHtcblx0XHRlLnByZXZlbnREZWZhdWx0KCk7XG5cdFx0dGhpcy4kZWwucmVtb3ZlQ2xhc3MoICdpcy1vcGVuJyApLmZpbmQoICdpbnB1dDpmb2N1cycgKS5ibHVyKCk7XG5cdH0sXG5cblx0dG9nZ2xlT3BlblN0YXR1czogZnVuY3Rpb24oIGUgKSB7XG5cdFx0ZS5wcmV2ZW50RGVmYXVsdCgpO1xuXHRcdHRoaXMuJGVsLnRvZ2dsZUNsYXNzKCAnaXMtb3BlbicgKS5maW5kKCAnaW5wdXQ6Zm9jdXMnICkuYmx1cigpO1xuXG5cdFx0Ly8gVHJpZ2dlciBhIHJlc2l6ZSBzbyB0aGUgbWVkaWEgZWxlbWVudCB3aWxsIGZpbGwgdGhlIGNvbnRhaW5lci5cblx0XHRpZiAoIHRoaXMuJGVsLmhhc0NsYXNzKCAnaXMtb3BlbicgKSApIHtcblx0XHRcdCQoIHdpbmRvdyApLnRyaWdnZXIoICdyZXNpemUnICk7XG5cdFx0fVxuXHR9LFxuXG5cdC8qKlxuXHQgKiBVcGRhdGUgYSBtb2RlbCBhdHRyaWJ1dGUgd2hlbiBhIGZpZWxkIGlzIGNoYW5nZWQuXG5cdCAqXG5cdCAqIEZpZWxkcyB3aXRoIGEgJ2RhdGEtc2V0dGluZz1cInt7a2V5fX1cIicgYXR0cmlidXRlIHdob3NlIHZhbHVlXG5cdCAqIGNvcnJlc3BvbmRzIHRvIGEgbW9kZWwgYXR0cmlidXRlIHdpbGwgYmUgYXV0b21hdGljYWxseSBzeW5jZWQuXG5cdCAqXG5cdCAqIEBwYXJhbSB7T2JqZWN0fSBlIEV2ZW50IG9iamVjdC5cblx0ICovXG5cdHVwZGF0ZUF0dHJpYnV0ZTogZnVuY3Rpb24oIGUgKSB7XG5cdFx0dmFyIGF0dHJpYnV0ZSA9ICQoIGUudGFyZ2V0ICkuZGF0YSggJ3NldHRpbmcnICksXG5cdFx0XHR2YWx1ZSA9IGUudGFyZ2V0LnZhbHVlO1xuXG5cdFx0aWYgKCB0aGlzLm1vZGVsLmdldCggYXR0cmlidXRlICkgIT09IHZhbHVlICkge1xuXHRcdFx0dGhpcy5tb2RlbC5zZXQoIGF0dHJpYnV0ZSwgdmFsdWUgKTtcblx0XHR9XG5cdH0sXG5cblx0LyoqXG5cdCAqIFVwZGF0ZSBhIHNldHRpbmcgZmllbGQgd2hlbiBhIG1vZGVsJ3MgYXR0cmlidXRlIGlzIGNoYW5nZWQuXG5cdCAqL1xuXHR1cGRhdGVGaWVsZHM6IGZ1bmN0aW9uKCkge1xuXHRcdHZhciB2aWRlbyA9IHRoaXMubW9kZWwudG9KU09OKCksXG5cdFx0XHQkc2V0dGluZ3MgPSB0aGlzLiRlbC5maW5kKCAnW2RhdGEtc2V0dGluZ10nICksXG5cdFx0XHRhdHRyaWJ1dGUsIHZhbHVlO1xuXG5cdFx0Ly8gQSBjaGFuZ2UgZXZlbnQgc2hvdWxkbid0IGJlIHRyaWdnZXJlZCBoZXJlLCBzbyBpdCB3b24ndCBjYXVzZVxuXHRcdC8vIHRoZSBtb2RlbCBhdHRyaWJ1dGUgdG8gYmUgdXBkYXRlZCBhbmQgZ2V0IHN0dWNrIGluIGFuXG5cdFx0Ly8gaW5maW5pdGUgbG9vcC5cblx0XHRmb3IgKCBhdHRyaWJ1dGUgaW4gdmlkZW8gKSB7XG5cdFx0XHQvLyBEZWNvZGUgSFRNTCBlbnRpdGllcy5cblx0XHRcdHZhbHVlID0gJCggJzxkaXYvPicgKS5odG1sKCB2aWRlb1sgYXR0cmlidXRlIF0gKS50ZXh0KCk7XG5cdFx0XHQkc2V0dGluZ3MuZmlsdGVyKCAnW2RhdGEtc2V0dGluZz1cIicgKyBhdHRyaWJ1dGUgKyAnXCJdJyApLnZhbCggdmFsdWUgKTtcblx0XHR9XG5cdH0sXG5cblx0dXBkYXRlVGl0bGU6IGZ1bmN0aW9uKCkge1xuXHRcdHZhciB0aXRsZSA9IHRoaXMubW9kZWwuZ2V0KCAndGl0bGUnICk7XG5cdFx0dGhpcy4kZWwuZmluZCggJy52aWRlby1jZW50cmFsLXBsYXlsaXN0LXZpZGVvLXRpdGxlIC50ZXh0JyApLnRleHQoIHRpdGxlID8gdGl0bGUgOiAnVGl0bGUnICk7XG5cdH0sXG5cblx0LyoqXG5cdCAqIERlc3Ryb3kgdGhlIHZpZXcncyBtb2RlbC5cblx0ICpcblx0ICogQXZvaWQgc3luY2luZyB0byB0aGUgc2VydmVyIGJ5IHRyaWdnZXJpbmcgYW4gZXZlbnQgaW5zdGVhZCBvZlxuXHQgKiBjYWxsaW5nIGRlc3Ryb3koKSBkaXJlY3RseSBvbiB0aGUgbW9kZWwuXG5cdCAqL1xuXHRkZXN0cm95OiBmdW5jdGlvbigpIHtcblx0XHR0aGlzLm1vZGVsLnRyaWdnZXIoICdkZXN0cm95JywgdGhpcy5tb2RlbCApO1xuXHR9LFxuXG5cdHJlbW92ZTogZnVuY3Rpb24oKSB7XG5cdFx0dGhpcy4kZWwucmVtb3ZlKCk7XG5cdH1cbn0pO1xuXG5tb2R1bGUuZXhwb3J0cyA9IFZpZGVvO1xuIiwidmFyIFZpZGVvQXJ0d29yayxcblx0XyA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93WydfJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWydfJ10gOiBudWxsKSxcblx0d29ya2Zsb3dzID0gcmVxdWlyZSggJy4uLy4uL3dvcmtmbG93cycgKSxcblx0d3AgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snd3AnXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ3dwJ10gOiBudWxsKTtcblxuVmlkZW9BcnR3b3JrID0gd3AuQmFja2JvbmUuVmlldy5leHRlbmQoe1xuXHR0YWdOYW1lOiAnc3BhbicsXG5cdGNsYXNzTmFtZTogJ3ZpZGVvLWNlbnRyYWwtcGxheWxpc3QtdmlkZW8tYXJ0d29yaycsXG5cdHRlbXBsYXRlOiB3cC50ZW1wbGF0ZSggJ3ZpZGVvLWNlbnRyYWwtcGxheWxpc3QtcGxheWxpc3QtdmlkZW8tYXJ0d29yaycgKSxcblxuXHRldmVudHM6IHtcblx0XHQnY2xpY2snOiAnc2VsZWN0J1xuXHR9LFxuXG5cdGluaXRpYWxpemU6IGZ1bmN0aW9uKCBvcHRpb25zICkge1xuXHRcdHRoaXMucGFyZW50ID0gb3B0aW9ucy5wYXJlbnQ7XG5cdFx0dGhpcy5saXN0ZW5UbyggdGhpcy5tb2RlbCwgJ2NoYW5nZTphcnR3b3JrVXJsJywgdGhpcy5yZW5kZXIgKTtcblx0fSxcblxuXHRyZW5kZXI6IGZ1bmN0aW9uKCkge1xuXHRcdHRoaXMuJGVsLmh0bWwoIHRoaXMudGVtcGxhdGUoIHRoaXMubW9kZWwudG9KU09OKCkgKSApO1xuXHRcdHRoaXMucGFyZW50LiRlbC50b2dnbGVDbGFzcyggJ2hhcy1hcnR3b3JrJywgISBfLmlzRW1wdHkoIHRoaXMubW9kZWwuZ2V0KCAnYXJ0d29ya1VybCcgKSApICk7XG5cdFx0cmV0dXJuIHRoaXM7XG5cdH0sXG5cblx0c2VsZWN0OiBmdW5jdGlvbigpIHtcblx0XHR3b3JrZmxvd3Muc2V0TW9kZWwoIHRoaXMubW9kZWwgKS5nZXQoICdzZWxlY3RBcnR3b3JrJyApLm9wZW4oKTtcblx0fVxufSk7XG5cbm1vZHVsZS5leHBvcnRzID0gVmlkZW9BcnR3b3JrO1xuIiwidmFyIFZpZGVvQXVkaW8sXG5cdCQgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snalF1ZXJ5J10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWydqUXVlcnknXSA6IG51bGwpLFxuXHRfID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ18nXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ18nXSA6IG51bGwpLFxuXHRzZXR0aW5ncyA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93Wyd2aWRlb19jZW50cmFsJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWyd2aWRlb19jZW50cmFsJ10gOiBudWxsKS5zZXR0aW5ncygpLFxuXHR3b3JrZmxvd3MgPSByZXF1aXJlKCAnLi4vLi4vd29ya2Zsb3dzJyApLFxuXHR3cCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93Wyd3cCddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnd3AnXSA6IG51bGwpO1xuXG5WaWRlb0F1ZGlvID0gd3AuQmFja2JvbmUuVmlldy5leHRlbmQoe1xuXHR0YWdOYW1lOiAnc3BhbicsXG5cdGNsYXNzTmFtZTogJ3ZpZGVvLWNlbnRyYWwtcGxheWxpc3QtdmlkZW8tYXVkaW8nLFxuXHR0ZW1wbGF0ZTogd3AudGVtcGxhdGUoICd2aWRlby1jZW50cmFsLXBsYXlsaXN0LXBsYXlsaXN0LXZpZGVvLWF1ZGlvJyApLFxuXG5cdGV2ZW50czoge1xuXHRcdCdjbGljayAudmlkZW8tY2VudHJhbC1wbGF5bGlzdC12aWRlby1hdWRpby1zZWxlY3Rvcic6ICdzZWxlY3QnXG5cdH0sXG5cblx0aW5pdGlhbGl6ZTogZnVuY3Rpb24oIG9wdGlvbnMgKSB7XG5cdFx0dGhpcy5wYXJlbnQgPSBvcHRpb25zLnBhcmVudDtcblxuXHRcdHRoaXMubGlzdGVuVG8oIHRoaXMubW9kZWwsICdjaGFuZ2U6YXVkaW9VcmwnLCB0aGlzLnJlZnJlc2ggKTtcblx0XHR0aGlzLmxpc3RlblRvKCB0aGlzLm1vZGVsLCAnZGVzdHJveScsIHRoaXMuY2xlYW51cCApO1xuXHR9LFxuXG5cdHJlbmRlcjogZnVuY3Rpb24oKSB7XG5cdFx0dmFyICRtZWRpYUVsLCBwbGF5ZXJTZXR0aW5ncyxcblx0XHRcdHZpZGVvID0gdGhpcy5tb2RlbC50b0pTT04oKSxcblx0XHRcdHBsYXllcklkID0gdGhpcy4kZWwuZmluZCggJy5tZWpzLWF1ZGlvJyApLmF0dHIoICdpZCcgKTtcblxuXHRcdC8vIFJlbW92ZSB0aGUgTWVkaWFFbGVtZW50IHBsYXllciBvYmplY3QgaWYgdGhlXG5cdFx0Ly8gYXVkaW8gZmlsZSBVUkwgaXMgZW1wdHkuXG5cdFx0aWYgKCAnJyA9PT0gdmlkZW8uYXVkaW9VcmwgJiYgcGxheWVySWQgKSB7XG5cdFx0XHRtZWpzLnBsYXllcnNbIHBsYXllcklkIF0ucmVtb3ZlKCk7XG5cdFx0fVxuXG5cdFx0Ly8gUmVuZGVyIHRoZSBtZWRpYSBlbGVtZW50LlxuXHRcdHRoaXMuJGVsLmh0bWwoIHRoaXMudGVtcGxhdGUoIHRoaXMubW9kZWwudG9KU09OKCkgKSApO1xuXG5cdFx0Ly8gU2V0IHVwIE1lZGlhRWxlbWVudC5qcy5cblx0XHQkbWVkaWFFbCA9IHRoaXMuJGVsLmZpbmQoICcudmlkZW8tY2VudHJhbC1wbGF5bGlzdC1hdWRpbycgKTtcblxuXHRcdHJldHVybiB0aGlzO1xuXHR9LFxuXG5cdHJlZnJlc2g6IGZ1bmN0aW9uKCBlICkge1xuXHRcdHZhciB2aWRlbyA9IHRoaXMubW9kZWwudG9KU09OKCksXG5cdFx0XHRwbGF5ZXJJZCA9IHRoaXMuJGVsLmZpbmQoICcubWVqcy1hdWRpbycgKS5hdHRyKCAnaWQnICksXG5cdFx0XHRwbGF5ZXIgPSBwbGF5ZXJJZCA/IG1lanMucGxheWVyc1sgcGxheWVySWQgXSA6IG51bGw7XG5cblx0XHRpZiAoIHBsYXllciAmJiAnJyAhPT0gdmlkZW8uYXVkaW9VcmwgKSB7XG5cdFx0XHRwbGF5ZXIucGF1c2UoKTtcblx0XHRcdHBsYXllci5zZXRTcmMoIHZpZGVvLmF1ZGlvVXJsICk7XG5cdFx0fSBlbHNlIHtcblx0XHRcdHRoaXMucmVuZGVyKCk7XG5cdFx0fVxuXHR9LFxuXG5cdGNsZWFudXA6IGZ1bmN0aW9uKCkge1xuXHRcdHZhciBwbGF5ZXJJZCA9IHRoaXMuJGVsLmZpbmQoICcubWVqcy1hdWRpbycgKS5hdHRyKCAnaWQnICksXG5cdFx0XHRwbGF5ZXIgPSBwbGF5ZXJJZCA/IG1lanMucGxheWVyc1sgcGxheWVySWQgXSA6IG51bGw7XG5cblx0XHRpZiAoIHBsYXllciApIHtcblx0XHRcdHBsYXllci5yZW1vdmUoKTtcblx0XHR9XG5cdH0sXG5cblx0c2VsZWN0OiBmdW5jdGlvbigpIHtcblx0XHR3b3JrZmxvd3Muc2V0TW9kZWwoIHRoaXMubW9kZWwgKS5nZXQoICdzZWxlY3RBdWRpbycgKS5vcGVuKCk7XG5cdH1cbn0pO1xuXG5tb2R1bGUuZXhwb3J0cyA9IFZpZGVvQXVkaW87XG4iLCJ2YXIgVmlkZW9zLFxuXHR3cCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93Wyd3cCddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnd3AnXSA6IG51bGwpO1xuXG5WaWRlb3MgPSB3cC5CYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG5cdHRhZ05hbWU6ICdsaScsXG5cdGNsYXNzTmFtZTogJ3ZpZGVvLWNlbnRyYWwtdmlkZW9zLWJyb3dzZXItbGlzdC1pdGVtJyxcblx0dGVtcGxhdGU6IHdwLnRlbXBsYXRlKCAndmlkZW8tY2VudHJhbC12aWRlb3MtYnJvd3Nlci1saXN0LWl0ZW0nICksXG5cbiAgICBhdHRyaWJ1dGVzOiBmdW5jdGlvbigpIHtcblx0XHRyZXR1cm4ge1xuXHRcdFx0J3RhYkluZGV4JzogICAgIDAsXG5cdFx0XHQncm9sZSc6ICAgICAgICAgJ2NoZWNrYm94Jyxcblx0XHRcdCdhcmlhLWxhYmVsJzogICB0aGlzLm1vZGVsLmdldCggJ3RpdGxlJyApLFxuXHRcdFx0J2FyaWEtY2hlY2tlZCc6IGZhbHNlLFxuXHRcdFx0J2RhdGEtaWQnOiAgICAgIHRoaXMubW9kZWwuZ2V0KCAnaWQnIClcblx0XHR9O1xuICAgIH0sXG4gICAgXG5cdGV2ZW50czoge1xuXHRcdCdjbGljayc6ICdhZGRTZWxlY3Rpb24nXG5cdH0sXG5cblx0aW5pdGlhbGl6ZTogZnVuY3Rpb24oIG9wdGlvbnMgKSB7XG5cdFx0dGhpcy5jb250cm9sbGVyID0gb3B0aW9ucy5jb250cm9sbGVyO1xuXHRcdHRoaXMubW9kZWwgPSBvcHRpb25zLm1vZGVsO1xuICAgICAgICB0aGlzLnNlbGVjdGlvbiA9IHRoaXMuY29udHJvbGxlci5zdGF0ZSgpLmdldCggJ3NlbGVjdGlvbicgKTtcblxuICAgICAgICB0aGlzLmxpc3RlblRvKCB0aGlzLnNlbGVjdGlvbiwgJ2FkZCByZW1vdmUgcmVzZXQnLCB0aGlzLnVwZGF0ZVNlbGVjdGVkQ2xhc3MgKTtcblxuICAgICAgICAvKiBVcGRhdGUgdGhlIHNlbGVjdGlvbi4gICovXG5cdFx0aWYgKCB0aGlzLnNlbGVjdGlvbiApIHtcblx0XHRcdHRoaXMuc2VsZWN0aW9uLm9uKCAncmVzZXQnLCB0aGlzLnVwZGF0ZVNlbGVjdCwgdGhpcyApO1xuICAgICAgICB9XG5cdH0sXG5cbiAgICAvKipcblx0ICogQHJldHVybnMge3dwLm1lZGlhLnZpZXcuQXR0YWNobWVudH0gUmV0dXJucyBpdHNlbGYgdG8gYWxsb3cgY2hhaW5pbmdcblx0ICovXG5cdHJlbmRlcjogZnVuY3Rpb24oKSB7XG5cdFx0dGhpcy52aWV3cy5kZXRhY2goKTtcblxuXHRcdC8vIENoZWNrIGlmIHRoZSBtb2RlbCBpcyBzZWxlY3RlZC5cblx0XHR0aGlzLnVwZGF0ZVNlbGVjdCgpO1xuXG5cdFx0dGhpcy52aWV3cy5yZW5kZXIoKTtcblxuXHRcdHRoaXMuJGVsLmh0bWwoIHRoaXMudGVtcGxhdGUoIHRoaXMubW9kZWwudG9KU09OKCkgKSApO1xuXHRcdHJldHVybiB0aGlzO1xuXHR9LFxuXG5cdGFkZFNlbGVjdGlvbjogZnVuY3Rpb24oIGUgKSB7XG5cdFx0aWYgKCB0aGlzLnNlbGVjdGlvbi5jb250YWlucyggdGhpcy5tb2RlbCApICkge1xuXHRcdFx0dGhpcy5zZWxlY3Rpb24ucmVtb3ZlKCB0aGlzLm1vZGVsICk7XG5cdFx0fSBlbHNlIHtcblx0XHRcdHRoaXMuc2VsZWN0aW9uLmFkZCggdGhpcy5tb2RlbCApO1xuXHRcdH1cblx0fSxcblxuXHR1cGRhdGVTZWxlY3RlZENsYXNzOiBmdW5jdGlvbigpIHtcblx0XHRpZiAoIHRoaXMuc2VsZWN0aW9uLmNvbnRhaW5zKCB0aGlzLm1vZGVsICkgKSB7XG4gICAgICAgICAgICB0aGlzLnNlbGVjdCgpO1xuXHRcdH0gZWxzZSB7XG4gICAgICAgICAgICB0aGlzLmRlc2VsZWN0KCk7XG5cdFx0fVxuICAgIH0sXG5cbiAgICB1cGRhdGVTZWxlY3Q6IGZ1bmN0aW9uKCkge1xuXHRcdHRoaXNbIHRoaXMuc2VsZWN0ZWQoKSA/ICdzZWxlY3QnIDogJ2Rlc2VsZWN0JyBdKCk7XG4gICAgfSxcbiAgICBcbiAgICAvKipcblx0ICogQHJldHVybnMge3VucmVzb2x2ZWR8Qm9vbGVhbn1cblx0ICovXG5cdHNlbGVjdGVkOiBmdW5jdGlvbigpIHtcblx0XHR2YXIgc2VsZWN0aW9uID0gdGhpcy5zZWxlY3Rpb247XG5cdFx0aWYgKCBzZWxlY3Rpb24gKSB7XG5cdFx0XHRyZXR1cm4gISEgc2VsZWN0aW9uLmdldCggdGhpcy5tb2RlbC5jaWQgKTtcblx0XHR9XG5cdH0sXG5cbiAgICAvKipcblx0ICogQHBhcmFtIHtCYWNrYm9uZS5Nb2RlbH0gbW9kZWxcblx0ICogQHBhcmFtIHtCYWNrYm9uZS5Db2xsZWN0aW9ufSBjb2xsZWN0aW9uXG5cdCAqL1xuXHRzZWxlY3Q6IGZ1bmN0aW9uKCBtb2RlbCwgY29sbGVjdGlvbiApIHtcblx0XHR2YXIgc2VsZWN0aW9uID0gdGhpcy5zZWxlY3Rpb24sXG5cdFx0XHRjb250cm9sbGVyID0gdGhpcy5jb250cm9sbGVyO1xuXG5cdFx0Ly8gQ2hlY2sgaWYgYSBzZWxlY3Rpb24gZXhpc3RzIGFuZCBpZiBpdCdzIHRoZSBjb2xsZWN0aW9uIHByb3ZpZGVkLlxuXHRcdC8vIElmIHRoZXkncmUgbm90IHRoZSBzYW1lIGNvbGxlY3Rpb24sIGJhaWw7IHdlJ3JlIGluIGFub3RoZXJcblx0XHQvLyBzZWxlY3Rpb24ncyBldmVudCBsb29wLlxuXHRcdGlmICggISBzZWxlY3Rpb24gfHwgKCBjb2xsZWN0aW9uICYmIGNvbGxlY3Rpb24gIT09IHNlbGVjdGlvbiApICkge1xuXHRcdFx0cmV0dXJuO1xuXHRcdH1cblxuXHRcdC8vIEJhaWwgaWYgdGhlIG1vZGVsIGlzIGFscmVhZHkgc2VsZWN0ZWQuXG5cdFx0aWYgKCB0aGlzLiRlbC5oYXNDbGFzcyggJ3NlbGVjdGVkJyApICkge1xuXHRcdFx0cmV0dXJuO1xuXHRcdH1cblxuXHRcdC8vIEFkZCAnc2VsZWN0ZWQnIGNsYXNzIHRvIG1vZGVsLCBzZXQgYXJpYS1jaGVja2VkIHRvIHRydWUuXG5cdFx0dGhpcy4kZWwuYWRkQ2xhc3MoICdzZWxlY3RlZCcgKS5hdHRyKCAnYXJpYS1jaGVja2VkJywgdHJ1ZSApO1xuXHRcdC8vICBNYWtlIHRoZSBjaGVja2JveCB0YWJhYmxlLCBleGNlcHQgaW4gbWVkaWEgZ3JpZCAoYnVsayBzZWxlY3QgbW9kZSkuXG5cdFx0aWYgKCAhICggY29udHJvbGxlci5pc01vZGVBY3RpdmUoICdncmlkJyApICYmIGNvbnRyb2xsZXIuaXNNb2RlQWN0aXZlKCAnc2VsZWN0JyApICkgKSB7XG5cdFx0XHR0aGlzLiQoICcuY2hlY2snICkuYXR0ciggJ3RhYmluZGV4JywgJzAnICk7XG5cdFx0fVxuICAgIH0sXG5cblx0LyoqXG5cdCAqIEBwYXJhbSB7QmFja2JvbmUuTW9kZWx9IG1vZGVsXG5cdCAqIEBwYXJhbSB7QmFja2JvbmUuQ29sbGVjdGlvbn0gY29sbGVjdGlvblxuXHQgKi9cblx0ZGVzZWxlY3Q6IGZ1bmN0aW9uKCBtb2RlbCwgY29sbGVjdGlvbiApIHtcblx0XHR2YXIgc2VsZWN0aW9uID0gdGhpcy5zZWxlY3Rpb247XG5cblx0XHQvLyBDaGVjayBpZiBhIHNlbGVjdGlvbiBleGlzdHMgYW5kIGlmIGl0J3MgdGhlIGNvbGxlY3Rpb24gcHJvdmlkZWQuXG5cdFx0Ly8gSWYgdGhleSdyZSBub3QgdGhlIHNhbWUgY29sbGVjdGlvbiwgYmFpbDsgd2UncmUgaW4gYW5vdGhlclxuXHRcdC8vIHNlbGVjdGlvbidzIGV2ZW50IGxvb3AuXG5cdFx0aWYgKCAhIHNlbGVjdGlvbiB8fCAoIGNvbGxlY3Rpb24gJiYgY29sbGVjdGlvbiAhPT0gc2VsZWN0aW9uICkgKSB7XG5cdFx0XHRyZXR1cm47XG5cdFx0fVxuXHRcdHRoaXMuJGVsLnJlbW92ZUNsYXNzKCAnc2VsZWN0ZWQnICkuYXR0ciggJ2FyaWEtY2hlY2tlZCcsIGZhbHNlIClcblx0XHRcdC5maW5kKCAnLmNoZWNrJyApLmF0dHIoICd0YWJpbmRleCcsICctMScgKTtcblx0fVxufSk7XG5cbm1vZHVsZS5leHBvcnRzID0gVmlkZW9zO1xuIiwidmFyIFZpZGVvc0l0ZW1zLFxuXHRWaWRlb3NJdGVtID0gcmVxdWlyZSggJy4uL3ZpZGVvcy9pdGVtJyApLFxuXHR3cCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93Wyd3cCddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnd3AnXSA6IG51bGwpO1xuXG5WaWRlb3NJdGVtcyA9IHdwLkJhY2tib25lLlZpZXcuZXh0ZW5kKHtcblx0Y2xhc3NOYW1lOiAndmlkZW8tY2VudHJhbC12aWRlb3MtYnJvd3Nlci1saXN0Jyxcblx0dGFnTmFtZTogJ3VsJyxcblxuXHRpbml0aWFsaXplOiBmdW5jdGlvbiggb3B0aW9ucyApIHtcblx0XHR0aGlzLmNvbGxlY3Rpb24gPSBvcHRpb25zLmNvbnRyb2xsZXIuc3RhdGUoKS5nZXQoICdjb2xsZWN0aW9uJyApO1xuXHRcdHRoaXMuY29udHJvbGxlciA9IG9wdGlvbnMuY29udHJvbGxlcjtcblxuXHRcdHRoaXMubGlzdGVuVG8oIHRoaXMuY29sbGVjdGlvbiwgJ2FkZCcsIHRoaXMuYWRkSXRlbSApO1xuXHRcdHRoaXMubGlzdGVuVG8oIHRoaXMuY29sbGVjdGlvbiwgJ3Jlc2V0JywgdGhpcy5yZW5kZXIgKTtcblx0fSxcblxuXHRyZW5kZXI6IGZ1bmN0aW9uKCkge1xuXHRcdHRoaXMuY29sbGVjdGlvbi5lYWNoKCB0aGlzLmFkZEl0ZW0sIHRoaXMgKTtcblx0XHRyZXR1cm4gdGhpcztcblx0fSxcblxuXHRhZGRJdGVtOiBmdW5jdGlvbiggbW9kZWwgKSB7XG5cdFx0dmFyIHZpZXcgPSBuZXcgVmlkZW9zSXRlbSh7XG5cdFx0XHRjb250cm9sbGVyOiB0aGlzLmNvbnRyb2xsZXIsXG5cdFx0XHRtb2RlbDogbW9kZWxcblx0XHR9KS5yZW5kZXIoKTtcblxuXHRcdHRoaXMuJGVsLmFwcGVuZCggdmlldy5lbCApO1xuXHR9XG59KTtcblxubW9kdWxlLmV4cG9ydHMgPSBWaWRlb3NJdGVtcztcbiIsInZhciBWaWRlb3NOb0l0ZW1zLFxuXHR3cCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93Wyd3cCddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnd3AnXSA6IG51bGwpO1xuXG5WaWRlb3NOb0l0ZW1zID0gd3AuQmFja2JvbmUuVmlldy5leHRlbmQoe1xuXHRjbGFzc05hbWU6ICd2aWRlby1jZW50cmFsLXZpZGVvcy1icm93c2VyLWVtcHR5Jyxcblx0dGFnTmFtZTogJ2RpdicsXG5cdHRlbXBsYXRlOiB3cC50ZW1wbGF0ZSggJ3ZpZGVvLWNlbnRyYWwtdmlkZW9zLWJyb3dzZXItZW1wdHknICksXG5cblx0aW5pdGlhbGl6ZTogZnVuY3Rpb24oIG9wdGlvbnMgKSB7XG5cdFx0dGhpcy5jb2xsZWN0aW9uID0gdGhpcy5jb2xsZWN0aW9uO1xuXG5cdFx0dGhpcy5saXN0ZW5UbyggdGhpcy5jb2xsZWN0aW9uLCAnYWRkIHJlbW92ZSByZXNldCcsIHRoaXMudG9nZ2xlVmlzaWJpbGl0eSApO1xuXHR9LFxuXG5cdHJlbmRlcjogZnVuY3Rpb24oKSB7XG5cdFx0dGhpcy4kZWwuaHRtbCggdGhpcy50ZW1wbGF0ZSgpICk7XG5cdFx0cmV0dXJuIHRoaXM7XG5cdH0sXG5cblx0dG9nZ2xlVmlzaWJpbGl0eTogZnVuY3Rpb24oKSB7XG5cdFx0dGhpcy4kZWwudG9nZ2xlQ2xhc3MoICdpcy12aXNpYmxlJywgdGhpcy5jb2xsZWN0aW9uLmxlbmd0aCA8IDEgKTtcblx0fVxufSk7XG5cbm1vZHVsZS5leHBvcnRzID0gVmlkZW9zTm9JdGVtcztcbiIsInZhciBWaWRlb3NTaWRlYmFyLFxuXHQkID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ2pRdWVyeSddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnalF1ZXJ5J10gOiBudWxsKSxcblx0d3AgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snd3AnXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ3dwJ10gOiBudWxsKTtcblxuVmlkZW9zU2lkZWJhciA9IHdwLkJhY2tib25lLlZpZXcuZXh0ZW5kKHtcblx0Y2xhc3NOYW1lOiAndmlkZW8tY2VudHJhbC12aWRlb3MtYnJvd3Nlci1zaWRlYmFyIG1lZGlhLXNpZGViYXInLFxuXHR0ZW1wbGF0ZTogd3AudGVtcGxhdGUoICd2aWRlby1jZW50cmFsLXZpZGVvcy1icm93c2VyLXNpZGViYXInICksXG5cblx0ZXZlbnRzOiB7XG5cdFx0J2NoYW5nZSBbZGF0YS1zZXR0aW5nXSc6ICd1cGRhdGVBdHRyaWJ1dGUnXG5cdH0sXG5cblx0aW5pdGlhbGl6ZTogZnVuY3Rpb24oIG9wdGlvbnMgKSB7XG5cdFx0dGhpcy5hdHRyaWJ1dGVzID0gb3B0aW9ucy5jb250cm9sbGVyLnN0YXRlKCkuZ2V0KCAnYXR0cmlidXRlcycgKTtcblx0fSxcblxuXHRyZW5kZXI6IGZ1bmN0aW9uKCkge1xuXHRcdHRoaXMuJGVsLmh0bWwoIHRoaXMudGVtcGxhdGUoKSApO1xuXHR9LFxuXG5cdHVwZGF0ZUF0dHJpYnV0ZTogZnVuY3Rpb24oIGUgKSB7XG5cdFx0dmFyICR0YXJnZXQgPSAkKCBlLnRhcmdldCApLFxuXHRcdFx0YXR0cmlidXRlID0gJHRhcmdldC5kYXRhKCAnc2V0dGluZycgKSxcblx0XHRcdHZhbHVlID0gZS50YXJnZXQudmFsdWU7XG5cblx0XHRpZiAoICdjaGVja2JveCcgPT09IGUudGFyZ2V0LnR5cGUgKSB7XG5cdFx0XHR2YWx1ZSA9ICEhICR0YXJnZXQucHJvcCggJ2NoZWNrZWQnICk7XG5cdFx0fVxuXG5cdFx0dGhpcy5hdHRyaWJ1dGVzLnNldCggYXR0cmlidXRlLCB2YWx1ZSApO1xuXHR9XG59KTtcblxubW9kdWxlLmV4cG9ydHMgPSBWaWRlb3NTaWRlYmFyO1xuIiwidmFyIFdvcmtmbG93cyxcblx0XyA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93WydfJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWydfJ10gOiBudWxsKSxcblx0dmlkZW9fY2VudHJhbCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93Wyd2aWRlb19jZW50cmFsJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWyd2aWRlb19jZW50cmFsJ10gOiBudWxsKSxcblx0bDEwbiA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93Wyd2aWRlb19jZW50cmFsJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWyd2aWRlb19jZW50cmFsJ10gOiBudWxsKS5sMTBuLFxuICAgIEFkZFZpZGVvc0ZyYW1lID0gcmVxdWlyZSggJy4vdmlld3MvZnJhbWUvaW5zZXJ0LXZpZGVvcycgKSxcblx0d3AgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snd3AnXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ3dwJ10gOiBudWxsKSxcblx0QXR0YWNobWVudCA9IHdwLm1lZGlhLm1vZGVsLkF0dGFjaG1lbnQ7XG5cbldvcmtmbG93cyA9IHtcblx0ZnJhbWVzOiBbXSxcblx0bW9kZWw6IHt9LFxuXG5cdC8qKlxuXHQgKiBTZXQgYSBtb2RlbCBmb3IgdGhlIGN1cnJlbnQgd29ya2Zsb3cuXG5cdCAqXG5cdCAqIEBwYXJhbSB7T2JqZWN0fSBmcmFtZVxuXHQgKi9cblx0c2V0TW9kZWw6IGZ1bmN0aW9uKCBtb2RlbCApIHtcblx0XHR0aGlzLm1vZGVsID0gbW9kZWw7XG5cdFx0cmV0dXJuIHRoaXM7XG5cdH0sXG5cblx0LyoqXG5cdCAqIFJldHJpZXZlIG9yIGNyZWF0ZSBhIGZyYW1lIGluc3RhbmNlIGZvciBhIHBhcnRpY3VsYXIgd29ya2Zsb3cuXG5cdCAqXG5cdCAqIEBwYXJhbSB7c3RyaW5nfSBpZCBGcmFtZSBpZGVudGlmZXIuXG5cdCAqL1xuXHRnZXQ6IGZ1bmN0aW9uKCBpZCApICB7XG5cdFx0dmFyIG1ldGhvZCA9ICdfJyArIGlkLFxuXHRcdFx0ZnJhbWUgPSB0aGlzLmZyYW1lc1sgbWV0aG9kIF0gfHwgbnVsbDtcblxuXHRcdC8vIEFsd2F5cyBjYWxsIHRoZSBmcmFtZSBtZXRob2QgdG8gcGVyZm9ybSBhbnkgcm91dGluZSBzZXQgdXAuIFRoZVxuXHRcdC8vIGZyYW1lIG1ldGhvZCBzaG91bGQgc2hvcnQtY2lyY3VpdCBiZWZvcmUgYmVpbmcgaW5pdGlhbGl6ZWQgYWdhaW4uXG5cdFx0ZnJhbWUgPSB0aGlzWyBtZXRob2QgXS5jYWxsKCB0aGlzLCBmcmFtZSApO1xuXG5cdFx0Ly8gU3RvcmUgdGhlIGZyYW1lIGZvciBmdXR1cmUgdXNlLlxuXHRcdHRoaXMuZnJhbWVzWyBtZXRob2QgXSA9IGZyYW1lO1xuXG5cdFx0cmV0dXJuIGZyYW1lO1xuXHR9LFxuXG5cdC8qKlxuXHQgKiBXb3JrZmxvdyBmb3IgYWRkaW5nIHZpZGVvcyB0byB0aGUgcGxheWxpc3QuXG5cdCAqXG5cdCAqIEBwYXJhbSB7T2JqZWN0fSBmcmFtZVxuXHQgKi9cblx0X2FkZFZpZGVvczogZnVuY3Rpb24oIGZyYW1lICkge1xuXHRcdC8vIFJldHVybiB0aGUgZXhpc3RpbmcgZnJhbWUgZm9yIHRoaXMgd29ya2Zsb3cuXG5cdFx0aWYgKCBmcmFtZSApIHtcblx0XHRcdHJldHVybiBmcmFtZTtcblx0XHR9XG5cblx0XHQvLyBJbml0aWFsaXplIHRoZSBhdWRpbyBmcmFtZS5cbiAgICAgICAgZnJhbWUgPSBuZXcgQWRkVmlkZW9zRnJhbWUoKTtcbiAgICAgICAgXG5cdFx0cmV0dXJuIGZyYW1lO1xuXHR9LFxuXG5cdC8qKlxuXHQgKiBXb3JrZmxvdyBmb3Igc2VsZWN0aW5nIHZpZGVvIGFydHdvcmsgaW1hZ2UuXG5cdCAqXG5cdCAqIEBwYXJhbSB7T2JqZWN0fSBmcmFtZVxuXHQgKi9cblx0X3NlbGVjdEFydHdvcms6IGZ1bmN0aW9uKCBmcmFtZSApIHtcblx0XHR2YXIgd29ya2Zsb3cgPSB0aGlzO1xuXG5cdFx0Ly8gUmV0dXJuIGV4aXN0aW5nIGZyYW1lIGZvciB0aGlzIHdvcmtmbG93LlxuXHRcdGlmICggZnJhbWUgKSB7XG5cdFx0XHRyZXR1cm4gZnJhbWU7XG5cdFx0fVxuXG5cdFx0Ly8gSW5pdGlhbGl6ZSB0aGUgYXJ0d29yayBmcmFtZS5cblx0XHRmcmFtZSA9IHdwLm1lZGlhKHtcblx0XHRcdHRpdGxlOiBsMTBuLndvcmtmbG93cy5zZWxlY3RBcnR3b3JrLmZyYW1lVGl0bGUsXG5cdFx0XHRsaWJyYXJ5OiB7XG5cdFx0XHRcdHR5cGU6ICdpbWFnZSdcblx0XHRcdH0sXG5cdFx0XHRidXR0b246IHtcblx0XHRcdFx0dGV4dDogbDEwbi53b3JrZmxvd3Muc2VsZWN0QXJ0d29yay5mcmFtZUJ1dHRvblRleHRcblx0XHRcdH0sXG5cdFx0XHRtdWx0aXBsZTogZmFsc2Vcblx0XHR9KTtcblxuXHRcdC8vIFNldCB0aGUgZXh0ZW5zaW9ucyB0aGF0IGNhbiBiZSB1cGxvYWRlZC5cblx0XHRmcmFtZS51cGxvYWRlci5vcHRpb25zLnVwbG9hZGVyLnBsdXBsb2FkID0ge1xuXHRcdFx0ZmlsdGVyczoge1xuXHRcdFx0XHRtaW1lX3R5cGVzOiBbe1xuXHRcdFx0XHRcdGZpbGVzOiBsMTBuLndvcmtmbG93cy5zZWxlY3RBcnR3b3JrLmZpbGVUeXBlcyxcblx0XHRcdFx0XHRleHRlbnNpb25zOiAnanBnLGpwZWcsZ2lmLHBuZydcblx0XHRcdFx0fV1cblx0XHRcdH1cblx0XHR9O1xuXG5cdFx0Ly8gQXV0b21hdGljYWxseSBzZWxlY3QgdGhlIGV4aXN0aW5nIGFydHdvcmsgaWYgcG9zc2libGUuXG5cdFx0ZnJhbWUub24oICdvcGVuJywgZnVuY3Rpb24oKSB7XG5cdFx0XHR2YXIgc2VsZWN0aW9uID0gdGhpcy5nZXQoICdsaWJyYXJ5JyApLmdldCggJ3NlbGVjdGlvbicgKSxcblx0XHRcdFx0YXJ0d29ya0lkID0gd29ya2Zsb3cubW9kZWwuZ2V0KCAnYXJ0d29ya0lkJyApLFxuXHRcdFx0XHRhdHRhY2htZW50cyA9IFtdO1xuXG5cdFx0XHRpZiAoIGFydHdvcmtJZCApIHtcblx0XHRcdFx0YXR0YWNobWVudHMucHVzaCggQXR0YWNobWVudC5nZXQoIGFydHdvcmtJZCApICk7XG5cdFx0XHRcdGF0dGFjaG1lbnRzWzBdLmZldGNoKCk7XG5cdFx0XHR9XG5cblx0XHRcdHNlbGVjdGlvbi5yZXNldCggYXR0YWNobWVudHMgKTtcblx0XHR9KTtcblxuXHRcdC8vIFNldCB0aGUgbW9kZWwncyBhcnR3b3JrIElEIGFuZCB1cmwgcHJvcGVydGllcy5cblx0XHRmcmFtZS5zdGF0ZSggJ2xpYnJhcnknICkub24oICdzZWxlY3QnLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgIHZhciBhdHRhY2htZW50ID0gdGhpcy5nZXQoICdzZWxlY3Rpb24nICkuZmlyc3QoKS50b0pTT04oKTtcblxuXHRcdFx0d29ya2Zsb3cubW9kZWwuc2V0KHtcblx0XHRcdFx0YXJ0d29ya0lkOiBhdHRhY2htZW50LmlkLFxuXHRcdFx0XHRhcnR3b3JrVXJsOiBhdHRhY2htZW50LnVybFxuXHRcdFx0fSk7XG5cdFx0fSk7XG5cblx0XHRyZXR1cm4gZnJhbWU7XG4gICAgfVxuXG59O1xuXG5tb2R1bGUuZXhwb3J0cyA9IFdvcmtmbG93cztcbiJdfQ==
