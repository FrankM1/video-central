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
		id: 'video-central-playlist-videos',
		title: l10n.insertVideos || 'Insert Videos',
		collection: null,
		content: 'video-central-videos-browser',
		menu: 'default',
		menuItem: {
			text: l10n.insertFromVideoCentral || 'Insert from Video Central',
			priority: 130
        },
        multiple: 'add',
        editable:   false,
		selection: null,
        toolbar: 'video-central-playlist-insert-videos'
	},

	initialize: function() {
		var collection = this.get('collection') || new Backbone.Collection(),
            selection = this.get('selection') || new Backbone.Collection();
            
		this.set( 'attributes', new Backbone.Model({
			id: null,
			show_videos: true
		}) );

		this.set( 'collection', collection );
        this.set( 'selection', selection );
        
        this.listenTo( selection, 'remove', this.updateSelection );
        this.listenTo( selection, 'insert', this.insertSelection );

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
var Backbone = (typeof window !== "undefined" ? window['Backbone'] : typeof global !== "undefined" ? global['Backbone'] : null),
    _ = (typeof window !== "undefined" ? window['_'] : typeof global !== "undefined" ? global['_'] : null),
	wp = (typeof window !== "undefined" ? window['wp'] : typeof global !== "undefined" ? global['wp'] : null);

/**
 * wp.media.model.Query
 *
 * A collection of attachments that match the supplied query arguments.
 *
 * Note: Do NOT change this.args after the query has been initialized.
 *       Things will break.
 *
 * @memberOf wp.media.model
 *
 * @class
 * @augments wp.media.model.Query
 * @augments Backbone.Collection
 *
 * @param {array}  [models]                      Models to initialize with the collection.
 * @param {object} [options]                     Options hash.
 * @param {object} [options.args]                Videos query arguments.
 * @param {object} [options.args.posts_per_page]
 */
module.exports = wp.media.model.Query.extend(/** @lends wp.media.model.Query.prototype */{
	
	/**
	 * Overrides Backbone.Collection.sync
	 * Overrides wp.media.model.Query.sync
	 *
	 * @param {String} method
	 * @param {Backbone.Model} model
	 * @param {Object} [options={}]
	 * @returns {Promise}
	 */
	sync: function( method, model, options ) {
		var args, fallback;

		// Overload the read method so Attachment.fetch() functions correctly.
		if ( 'read' === method ) {
			options = options || {};
			options.context = this;
			options.data = _.extend( options.data || {}, {
				action:  'video_central_get_playlist_videos_for_frame'
			});

			// Clone the args so manipulation is non-destructive.
			args = _.clone( this.args );

			// Determine which page to query.
			if ( -1 !== args.posts_per_page ) {
				args.paged = Math.round( this.length / args.posts_per_page ) + 1;
			}

			options.data.query = args;
			return wp.media.ajax( options );

		// Otherwise, fall back to Backbone.sync()
		}
	}
}, /** @lends wp.media.model.Query */{
	/**
	 * @readonly
	 */
	defaultProps: {
		orderby: 'date',
		order:   'DESC'
	},
	/**
	 * @readonly
	 */
	defaultArgs: {
		posts_per_page: 40
	},
	/**
	 * @readonly
	 */
	orderby: {
		allowed:  [ 'name', 'author', 'date', 'title', 'modified', 'id', 'post__in', 'menuOrder' ],
		/**
		 * A map of JavaScript orderby values to their WP_Query equivalents.
		 * @type {Object}
		 */
		valuemap: {
			'id':         'ID',
			'menuOrder':  'menu_order ID'
		}
	},
	/**
	 * A map of JavaScript query properties to their WP_Query equivalents.
	 *
	 * @readonly
	 */
	propmap: {
		'exclude'  : 'post__not_in',
		'include'  : 'post__in',
		'menuOrder': 'menu_order',
		'perPage'  : 'posts_per_page',
		'search'   : 's',
		'status'   : 'post_status',
		'type'     : 'post_type'
	},
	/**
	 * Creates and returns an Attachments Query collection given the properties.
	 *
	 * Caches query objects and reuses where possible.
	 *
	 * @static
	 * @method
	 *
	 * @param {object} [props]
	 * @param {Object} [props.cache=true]   Whether to use the query cache or not.
	 * @param {Object} [props.order]
	 * @param {Object} [props.orderby]
	 * @param {Object} [props.include]
	 * @param {Object} [props.exclude]
	 * @param {Object} [props.s]
	 * @param {Object} [props.post_mime_type]
	 * @param {Object} [props.posts_per_page]
	 * @param {Object} [props.menu_order]
	 * @param {Object} [props.post_parent]
	 * @param {Object} [props.post_status]
	 * @param {Object} [options]
	 *
	 * @returns {wp.media.model.Query} A new Attachments Query collection.
	 */
	get: (function(){
		/**
		 * @static
		 * @type Array
		 */
		var queries = [];

		/**
		 * @returns {Query}
		 */
		return function( props, options ) {
			var args     = {},
				orderby  = Query.orderby,
				defaults = Query.defaultProps,
				query,
				cache    = !! props.cache || _.isUndefined( props.cache );

			// Remove the `query` property. This isn't linked to a query,
			// this *is* the query.
			delete props.query;
			delete props.cache;

			// Fill default args.
			_.defaults( props, defaults );

			// Normalize the order.
			props.order = props.order.toUpperCase();
			if ( 'DESC' !== props.order && 'ASC' !== props.order ) {
				props.order = defaults.order.toUpperCase();
			}

			// Ensure we have a valid orderby value.
			if ( ! _.contains( orderby.allowed, props.orderby ) ) {
				props.orderby = defaults.orderby;
			}

			_.each( [ 'include', 'exclude' ], function( prop ) {
				if ( props[ prop ] && ! _.isArray( props[ prop ] ) ) {
					props[ prop ] = [ props[ prop ] ];
				}
			} );

			// Generate the query `args` object.
			// Correct any differing property names.
			_.each( props, function( value, prop ) {
				if ( _.isNull( value ) ) {
					return;
				}

				args[ Query.propmap[ prop ] || prop ] = value;
			});

			// Fill any other default query args.
			_.defaults( args, Query.defaultArgs );

			// `props.orderby` does not always map directly to `args.orderby`.
			// Substitute exceptions specified in orderby.keymap.
			args.orderby = orderby.valuemap[ props.orderby ] || props.orderby;

			// Search the query cache for a matching query.
			if ( cache ) {
				query = _.find( queries, function( query ) {
					return _.isEqual( query.args, args );
				});
			} else {
				queries = [];
			}

			// Otherwise, create a new query and add it to the cache.
			if ( ! query ) {
				query = new wp.media.model.Query( [], _.extend( options || {}, {
					props: props,
					args:  args
				} ) );
				queries.push( query );
			}

			return query;
		};
	}())
});
}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{}],5:[function(require,module,exports){
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

},{"./collections/videos":1,"./models/video":3,"./views/button/add-videos":6,"./views/post-form":9,"./views/video":12,"./views/video-list":11,"./views/video/artwork":13,"./views/video/audio":14,"./workflows":19}],6:[function(require,module,exports){
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

},{"../../workflows":19}],7:[function(require,module,exports){
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

},{"../videos/items":16,"../videos/no-items":17,"../videos/sidebar":18}],8:[function(require,module,exports){
(function (global){
var InsertVideosFrame,
	VideosBrowser = require( '../content/videos-browser' ),
    VideosController = require( '../../controllers/videos' ),
    VideoQuery = require( '../../models/videos' ),
	VideosToolbar = require( '../toolbar/videos' ),
	wp = (typeof window !== "undefined" ? window['wp'] : typeof global !== "undefined" ? global['wp'] : null),
	PostFrame = wp.media.view.MediaFrame.Post;
    
InsertVideosFrame = PostFrame.extend({
	createStates: function() {
        PostFrame.prototype.createStates.apply( this, arguments );

		// Add the default states.
		this.states.add(
            // Add our HTML slide controller state.
            new VideosController()
        );
	},

	bindHandlers: function() {
		PostFrame.prototype.bindHandlers.apply( this, arguments );

		// this.on( 'menu:create:default', this.createCueMenu, this );
		this.on( 'content:create:video-central-videos-browser', this.createCueContent, this );
		this.on( 'toolbar:create:video-central-playlist-insert-videos', this.createCueToolbar, this );
	},

	createCueMenu: function( menu ) {
		menu.view.set({
			'video-central-playlist-videos-separator': new wp.media.View({
				className: 'separator',
				priority: 200
			})
		});
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

},{"../../controllers/videos":2,"../../models/videos":4,"../content/videos-browser":7,"../toolbar/videos":10}],9:[function(require,module,exports){
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

},{"./button/add-videos":6,"./video-list":11}],10:[function(require,module,exports){
(function (global){
var VideosToolbar,
	_ = (typeof window !== "undefined" ? window['_'] : typeof global !== "undefined" ? global['_'] : null),
	wp = (typeof window !== "undefined" ? window['wp'] : typeof global !== "undefined" ? global['wp'] : null);

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

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{}],11:[function(require,module,exports){
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

},{"./video":12}],12:[function(require,module,exports){
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

},{"./video/artwork":13,"./video/audio":14}],13:[function(require,module,exports){
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

},{"../../workflows":19}],14:[function(require,module,exports){
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

},{"../../workflows":19}],15:[function(require,module,exports){
(function (global){
var Videos,
	wp = (typeof window !== "undefined" ? window['wp'] : typeof global !== "undefined" ? global['wp'] : null);

Videos = wp.Backbone.View.extend({
	tagName: 'li',
	className: 'video-central-videos-browser-list-item',
	template: wp.template( 'video-central-videos-browser-list-item' ),

	events: {
		'click': 'resetSelection'
	},

	initialize: function( options ) {
		this.controller = options.controller;
		this.model = options.model;
		this.selection = this.controller.state().get( 'selection' );

		this.listenTo( this.selection, 'add remove reset', this.updateSelectedClass );
	},

	render: function() {
		this.$el.html( this.template( this.model.toJSON() ) );
		return this;
	},

	resetSelection: function( e ) {
		if ( this.selection.contains( this.model ) ) {
			this.selection.remove( this.model );
		} else {
			this.selection.reset( this.model );
		}
	},

	updateSelectedClass: function() {
		if ( this.selection.contains( this.model ) ) {
			this.$el.addClass( 'is-selected' );
		} else {
			this.$el.removeClass( 'is-selected' );
		}
	}
});

module.exports = Videos;

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{}],16:[function(require,module,exports){
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

},{"../videos/item":15}],17:[function(require,module,exports){
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

},{}],18:[function(require,module,exports){
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

},{}],19:[function(require,module,exports){
(function (global){
var Workflows,
	_ = (typeof window !== "undefined" ? window['_'] : typeof global !== "undefined" ? global['_'] : null),
	video_central = (typeof window !== "undefined" ? window['video_central'] : typeof global !== "undefined" ? global['video_central'] : null),
	l10n = (typeof window !== "undefined" ? window['video_central'] : typeof global !== "undefined" ? global['video_central'] : null).l10n,
    MediaFrame = require( './views/frame/insert-videos' ),
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
        frame = new MediaFrame();
        
        console.log( frame.state( 'video-central-playlist-videos' ) );

		// Insert each selected attachment as a new video model.
		frame.state( 'video-central-playlist-videos' ).on( 'insert', function( selection ) {
            console.log(selection);
			_.each( selection.models, function( attachment ) {
                video_central.videos.push( attachment.toJSON() );
            });
        });

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

},{"./views/frame/insert-videos":8}]},{},[5])
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJhc3NldHMvYWRtaW4vanMvc291cmNlL3BsYXlsaXN0L2NvbGxlY3Rpb25zL3ZpZGVvcy5qcyIsImFzc2V0cy9hZG1pbi9qcy9zb3VyY2UvcGxheWxpc3QvY29udHJvbGxlcnMvdmlkZW9zLmpzIiwiYXNzZXRzL2FkbWluL2pzL3NvdXJjZS9wbGF5bGlzdC9tb2RlbHMvdmlkZW8uanMiLCJhc3NldHMvYWRtaW4vanMvc291cmNlL3BsYXlsaXN0L21vZGVscy92aWRlb3MuanMiLCJhc3NldHMvYWRtaW4vanMvc291cmNlL3BsYXlsaXN0L3BsYXlsaXN0LWVkaXQuanMiLCJhc3NldHMvYWRtaW4vanMvc291cmNlL3BsYXlsaXN0L3ZpZXdzL2J1dHRvbi9hZGQtdmlkZW9zLmpzIiwiYXNzZXRzL2FkbWluL2pzL3NvdXJjZS9wbGF5bGlzdC92aWV3cy9jb250ZW50L3ZpZGVvcy1icm93c2VyLmpzIiwiYXNzZXRzL2FkbWluL2pzL3NvdXJjZS9wbGF5bGlzdC92aWV3cy9mcmFtZS9pbnNlcnQtdmlkZW9zLmpzIiwiYXNzZXRzL2FkbWluL2pzL3NvdXJjZS9wbGF5bGlzdC92aWV3cy9wb3N0LWZvcm0uanMiLCJhc3NldHMvYWRtaW4vanMvc291cmNlL3BsYXlsaXN0L3ZpZXdzL3Rvb2xiYXIvdmlkZW9zLmpzIiwiYXNzZXRzL2FkbWluL2pzL3NvdXJjZS9wbGF5bGlzdC92aWV3cy92aWRlby1saXN0LmpzIiwiYXNzZXRzL2FkbWluL2pzL3NvdXJjZS9wbGF5bGlzdC92aWV3cy92aWRlby5qcyIsImFzc2V0cy9hZG1pbi9qcy9zb3VyY2UvcGxheWxpc3Qvdmlld3MvdmlkZW8vYXJ0d29yay5qcyIsImFzc2V0cy9hZG1pbi9qcy9zb3VyY2UvcGxheWxpc3Qvdmlld3MvdmlkZW8vYXVkaW8uanMiLCJhc3NldHMvYWRtaW4vanMvc291cmNlL3BsYXlsaXN0L3ZpZXdzL3ZpZGVvcy9pdGVtLmpzIiwiYXNzZXRzL2FkbWluL2pzL3NvdXJjZS9wbGF5bGlzdC92aWV3cy92aWRlb3MvaXRlbXMuanMiLCJhc3NldHMvYWRtaW4vanMvc291cmNlL3BsYXlsaXN0L3ZpZXdzL3ZpZGVvcy9uby1pdGVtcy5qcyIsImFzc2V0cy9hZG1pbi9qcy9zb3VyY2UvcGxheWxpc3Qvdmlld3MvdmlkZW9zL3NpZGViYXIuanMiLCJhc3NldHMvYWRtaW4vanMvc291cmNlL3BsYXlsaXN0L3dvcmtmbG93cy5qcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTs7QUNBQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7O0FDdENBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7Ozs7QUN6Q0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7O0FDbEJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7O0FDN01BO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7O0FDbENBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7O0FDbENBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7O0FDdEVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7Ozs7QUNsREE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOzs7OztBQ3JEQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7O0FDckRBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7Ozs7QUNyREE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7Ozs7QUNoSEE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7Ozs7QUMvQkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOzs7OztBQ3ZFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOzs7OztBQzNDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7O0FDaENBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7O0FDekJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7O0FDbENBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EiLCJmaWxlIjoiZ2VuZXJhdGVkLmpzIiwic291cmNlUm9vdCI6IiIsInNvdXJjZXNDb250ZW50IjpbIihmdW5jdGlvbiBlKHQsbixyKXtmdW5jdGlvbiBzKG8sdSl7aWYoIW5bb10pe2lmKCF0W29dKXt2YXIgYT10eXBlb2YgcmVxdWlyZT09XCJmdW5jdGlvblwiJiZyZXF1aXJlO2lmKCF1JiZhKXJldHVybiBhKG8sITApO2lmKGkpcmV0dXJuIGkobywhMCk7dmFyIGY9bmV3IEVycm9yKFwiQ2Fubm90IGZpbmQgbW9kdWxlICdcIitvK1wiJ1wiKTt0aHJvdyBmLmNvZGU9XCJNT0RVTEVfTk9UX0ZPVU5EXCIsZn12YXIgbD1uW29dPXtleHBvcnRzOnt9fTt0W29dWzBdLmNhbGwobC5leHBvcnRzLGZ1bmN0aW9uKGUpe3ZhciBuPXRbb11bMV1bZV07cmV0dXJuIHMobj9uOmUpfSxsLGwuZXhwb3J0cyxlLHQsbixyKX1yZXR1cm4gbltvXS5leHBvcnRzfXZhciBpPXR5cGVvZiByZXF1aXJlPT1cImZ1bmN0aW9uXCImJnJlcXVpcmU7Zm9yKHZhciBvPTA7bzxyLmxlbmd0aDtvKyspcyhyW29dKTtyZXR1cm4gc30pIiwidmFyIFZpZGVvcyxcblx0XyA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93WydfJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWydfJ10gOiBudWxsKSxcblx0QmFja2JvbmUgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snQmFja2JvbmUnXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ0JhY2tib25lJ10gOiBudWxsKSxcblx0c2V0dGluZ3MgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1sndmlkZW9fY2VudHJhbCddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsndmlkZW9fY2VudHJhbCddIDogbnVsbCkuc2V0dGluZ3MoKSxcblx0VmlkZW8gPSByZXF1aXJlKCAnLi4vbW9kZWxzL3ZpZGVvJyApLFxuXHR3cCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93Wyd3cCddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnd3AnXSA6IG51bGwpO1xuXG5WaWRlb3MgPSBCYWNrYm9uZS5Db2xsZWN0aW9uLmV4dGVuZCh7XG5cdG1vZGVsOiBWaWRlbyxcblxuXHRjb21wYXJhdG9yOiBmdW5jdGlvbiggdmlkZW8gKSB7XG5cdFx0cmV0dXJuIHBhcnNlSW50KCB2aWRlby5nZXQoICdvcmRlcicgKSwgMTAgKTtcblx0fSxcblxuXHRmZXRjaDogZnVuY3Rpb24oKSB7XG5cdFx0dmFyIGNvbGxlY3Rpb24gPSB0aGlzO1xuXG5cdFx0cmV0dXJuIHdwLmFqYXgucG9zdCggJ3ZpZGVvX2NlbnRyYWxfZ2V0X3BsYXlsaXN0X3ZpZGVvcycsIHtcblx0XHRcdHBvc3RfaWQ6IHNldHRpbmdzLnBvc3RJZFxuXHRcdH0pLmRvbmUoZnVuY3Rpb24oIHZpZGVvcyApIHtcblx0XHRcdGNvbGxlY3Rpb24ucmVzZXQoIHZpZGVvcyApO1xuXHRcdH0pO1xuXHR9LFxuXG5cdHNhdmU6IGZ1bmN0aW9uKCBkYXRhICkge1xuXHRcdHRoaXMuc29ydCgpO1xuXG5cdFx0ZGF0YSA9IF8uZXh0ZW5kKHt9LCBkYXRhLCB7XG5cdFx0XHRwb3N0X2lkOiBzZXR0aW5ncy5wb3N0SWQsXG5cdFx0XHR2aWRlb3M6IHRoaXMudG9KU09OKCksXG5cdFx0XHRub25jZTogc2V0dGluZ3Muc2F2ZU5vbmNlXG5cdFx0fSk7XG5cblx0XHRyZXR1cm4gd3AuYWpheC5wb3N0KCAndmlkZW9fY2VudHJhbF9zYXZlX3BsYXlsaXN0X3ZpZGVvcycsIGRhdGEgKTtcblx0fVxufSk7XG5cbm1vZHVsZS5leHBvcnRzID0gVmlkZW9zO1xuIiwidmFyIFZpZGVvcyxcblx0QmFja2JvbmUgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snQmFja2JvbmUnXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ0JhY2tib25lJ10gOiBudWxsKSxcblx0bDEwbiA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93Wyd2aWRlb19jZW50cmFsJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWyd2aWRlb19jZW50cmFsJ10gOiBudWxsKS5sMTBuLFxuXHR3cCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93Wyd3cCddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnd3AnXSA6IG51bGwpO1xuICAgIFxuVmlkZW9zID0gd3AubWVkaWEuY29udHJvbGxlci5TdGF0ZS5leHRlbmQoe1xuXHRkZWZhdWx0czoge1xuXHRcdGlkOiAndmlkZW8tY2VudHJhbC1wbGF5bGlzdC12aWRlb3MnLFxuXHRcdHRpdGxlOiBsMTBuLmluc2VydFZpZGVvcyB8fCAnSW5zZXJ0IFZpZGVvcycsXG5cdFx0Y29sbGVjdGlvbjogbnVsbCxcblx0XHRjb250ZW50OiAndmlkZW8tY2VudHJhbC12aWRlb3MtYnJvd3NlcicsXG5cdFx0bWVudTogJ2RlZmF1bHQnLFxuXHRcdG1lbnVJdGVtOiB7XG5cdFx0XHR0ZXh0OiBsMTBuLmluc2VydEZyb21WaWRlb0NlbnRyYWwgfHwgJ0luc2VydCBmcm9tIFZpZGVvIENlbnRyYWwnLFxuXHRcdFx0cHJpb3JpdHk6IDEzMFxuICAgICAgICB9LFxuICAgICAgICBtdWx0aXBsZTogJ2FkZCcsXG4gICAgICAgIGVkaXRhYmxlOiAgIGZhbHNlLFxuXHRcdHNlbGVjdGlvbjogbnVsbCxcbiAgICAgICAgdG9vbGJhcjogJ3ZpZGVvLWNlbnRyYWwtcGxheWxpc3QtaW5zZXJ0LXZpZGVvcydcblx0fSxcblxuXHRpbml0aWFsaXplOiBmdW5jdGlvbigpIHtcblx0XHR2YXIgY29sbGVjdGlvbiA9IHRoaXMuZ2V0KCdjb2xsZWN0aW9uJykgfHwgbmV3IEJhY2tib25lLkNvbGxlY3Rpb24oKSxcbiAgICAgICAgICAgIHNlbGVjdGlvbiA9IHRoaXMuZ2V0KCdzZWxlY3Rpb24nKSB8fCBuZXcgQmFja2JvbmUuQ29sbGVjdGlvbigpO1xuICAgICAgICAgICAgXG5cdFx0dGhpcy5zZXQoICdhdHRyaWJ1dGVzJywgbmV3IEJhY2tib25lLk1vZGVsKHtcblx0XHRcdGlkOiBudWxsLFxuXHRcdFx0c2hvd192aWRlb3M6IHRydWVcblx0XHR9KSApO1xuXG5cdFx0dGhpcy5zZXQoICdjb2xsZWN0aW9uJywgY29sbGVjdGlvbiApO1xuICAgICAgICB0aGlzLnNldCggJ3NlbGVjdGlvbicsIHNlbGVjdGlvbiApO1xuICAgICAgICBcbiAgICAgICAgdGhpcy5saXN0ZW5Ubyggc2VsZWN0aW9uLCAncmVtb3ZlJywgdGhpcy51cGRhdGVTZWxlY3Rpb24gKTtcbiAgICAgICAgdGhpcy5saXN0ZW5Ubyggc2VsZWN0aW9uLCAnaW5zZXJ0JywgdGhpcy5pbnNlcnRTZWxlY3Rpb24gKTtcblxuXHR9XG59KTtcblxubW9kdWxlLmV4cG9ydHMgPSBWaWRlb3M7XG4iLCJ2YXIgVmlkZW8sXG5cdEJhY2tib25lID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ0JhY2tib25lJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWydCYWNrYm9uZSddIDogbnVsbCk7XG5cblZpZGVvID0gQmFja2JvbmUuTW9kZWwuZXh0ZW5kKHtcblx0ZGVmYXVsdHM6IHtcblx0XHRhcnRpc3Q6ICcnLFxuXHRcdGFydHdvcmtJZDogJycsXG5cdFx0YXJ0d29ya1VybDogJycsXG5cdFx0dmlkZW9JZDogJycsXG5cdFx0YXVkaW9Vcmw6ICcnLFxuXHRcdGZvcm1hdDogJycsXG5cdFx0bGVuZ3RoOiAnJyxcblx0XHR0aXRsZTogJycsXG5cdFx0b3JkZXI6IDBcblx0fVxufSk7XG5cbm1vZHVsZS5leHBvcnRzID0gVmlkZW87XG4iLCJ2YXIgQmFja2JvbmUgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snQmFja2JvbmUnXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ0JhY2tib25lJ10gOiBudWxsKSxcbiAgICBfID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ18nXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ18nXSA6IG51bGwpLFxuXHR3cCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93Wyd3cCddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnd3AnXSA6IG51bGwpO1xuXG4vKipcbiAqIHdwLm1lZGlhLm1vZGVsLlF1ZXJ5XG4gKlxuICogQSBjb2xsZWN0aW9uIG9mIGF0dGFjaG1lbnRzIHRoYXQgbWF0Y2ggdGhlIHN1cHBsaWVkIHF1ZXJ5IGFyZ3VtZW50cy5cbiAqXG4gKiBOb3RlOiBEbyBOT1QgY2hhbmdlIHRoaXMuYXJncyBhZnRlciB0aGUgcXVlcnkgaGFzIGJlZW4gaW5pdGlhbGl6ZWQuXG4gKiAgICAgICBUaGluZ3Mgd2lsbCBicmVhay5cbiAqXG4gKiBAbWVtYmVyT2Ygd3AubWVkaWEubW9kZWxcbiAqXG4gKiBAY2xhc3NcbiAqIEBhdWdtZW50cyB3cC5tZWRpYS5tb2RlbC5RdWVyeVxuICogQGF1Z21lbnRzIEJhY2tib25lLkNvbGxlY3Rpb25cbiAqXG4gKiBAcGFyYW0ge2FycmF5fSAgW21vZGVsc10gICAgICAgICAgICAgICAgICAgICAgTW9kZWxzIHRvIGluaXRpYWxpemUgd2l0aCB0aGUgY29sbGVjdGlvbi5cbiAqIEBwYXJhbSB7b2JqZWN0fSBbb3B0aW9uc10gICAgICAgICAgICAgICAgICAgICBPcHRpb25zIGhhc2guXG4gKiBAcGFyYW0ge29iamVjdH0gW29wdGlvbnMuYXJnc10gICAgICAgICAgICAgICAgVmlkZW9zIHF1ZXJ5IGFyZ3VtZW50cy5cbiAqIEBwYXJhbSB7b2JqZWN0fSBbb3B0aW9ucy5hcmdzLnBvc3RzX3Blcl9wYWdlXVxuICovXG5tb2R1bGUuZXhwb3J0cyA9IHdwLm1lZGlhLm1vZGVsLlF1ZXJ5LmV4dGVuZCgvKiogQGxlbmRzIHdwLm1lZGlhLm1vZGVsLlF1ZXJ5LnByb3RvdHlwZSAqL3tcblx0XG5cdC8qKlxuXHQgKiBPdmVycmlkZXMgQmFja2JvbmUuQ29sbGVjdGlvbi5zeW5jXG5cdCAqIE92ZXJyaWRlcyB3cC5tZWRpYS5tb2RlbC5RdWVyeS5zeW5jXG5cdCAqXG5cdCAqIEBwYXJhbSB7U3RyaW5nfSBtZXRob2Rcblx0ICogQHBhcmFtIHtCYWNrYm9uZS5Nb2RlbH0gbW9kZWxcblx0ICogQHBhcmFtIHtPYmplY3R9IFtvcHRpb25zPXt9XVxuXHQgKiBAcmV0dXJucyB7UHJvbWlzZX1cblx0ICovXG5cdHN5bmM6IGZ1bmN0aW9uKCBtZXRob2QsIG1vZGVsLCBvcHRpb25zICkge1xuXHRcdHZhciBhcmdzLCBmYWxsYmFjaztcblxuXHRcdC8vIE92ZXJsb2FkIHRoZSByZWFkIG1ldGhvZCBzbyBBdHRhY2htZW50LmZldGNoKCkgZnVuY3Rpb25zIGNvcnJlY3RseS5cblx0XHRpZiAoICdyZWFkJyA9PT0gbWV0aG9kICkge1xuXHRcdFx0b3B0aW9ucyA9IG9wdGlvbnMgfHwge307XG5cdFx0XHRvcHRpb25zLmNvbnRleHQgPSB0aGlzO1xuXHRcdFx0b3B0aW9ucy5kYXRhID0gXy5leHRlbmQoIG9wdGlvbnMuZGF0YSB8fCB7fSwge1xuXHRcdFx0XHRhY3Rpb246ICAndmlkZW9fY2VudHJhbF9nZXRfcGxheWxpc3RfdmlkZW9zX2Zvcl9mcmFtZSdcblx0XHRcdH0pO1xuXG5cdFx0XHQvLyBDbG9uZSB0aGUgYXJncyBzbyBtYW5pcHVsYXRpb24gaXMgbm9uLWRlc3RydWN0aXZlLlxuXHRcdFx0YXJncyA9IF8uY2xvbmUoIHRoaXMuYXJncyApO1xuXG5cdFx0XHQvLyBEZXRlcm1pbmUgd2hpY2ggcGFnZSB0byBxdWVyeS5cblx0XHRcdGlmICggLTEgIT09IGFyZ3MucG9zdHNfcGVyX3BhZ2UgKSB7XG5cdFx0XHRcdGFyZ3MucGFnZWQgPSBNYXRoLnJvdW5kKCB0aGlzLmxlbmd0aCAvIGFyZ3MucG9zdHNfcGVyX3BhZ2UgKSArIDE7XG5cdFx0XHR9XG5cblx0XHRcdG9wdGlvbnMuZGF0YS5xdWVyeSA9IGFyZ3M7XG5cdFx0XHRyZXR1cm4gd3AubWVkaWEuYWpheCggb3B0aW9ucyApO1xuXG5cdFx0Ly8gT3RoZXJ3aXNlLCBmYWxsIGJhY2sgdG8gQmFja2JvbmUuc3luYygpXG5cdFx0fVxuXHR9XG59LCAvKiogQGxlbmRzIHdwLm1lZGlhLm1vZGVsLlF1ZXJ5ICove1xuXHQvKipcblx0ICogQHJlYWRvbmx5XG5cdCAqL1xuXHRkZWZhdWx0UHJvcHM6IHtcblx0XHRvcmRlcmJ5OiAnZGF0ZScsXG5cdFx0b3JkZXI6ICAgJ0RFU0MnXG5cdH0sXG5cdC8qKlxuXHQgKiBAcmVhZG9ubHlcblx0ICovXG5cdGRlZmF1bHRBcmdzOiB7XG5cdFx0cG9zdHNfcGVyX3BhZ2U6IDQwXG5cdH0sXG5cdC8qKlxuXHQgKiBAcmVhZG9ubHlcblx0ICovXG5cdG9yZGVyYnk6IHtcblx0XHRhbGxvd2VkOiAgWyAnbmFtZScsICdhdXRob3InLCAnZGF0ZScsICd0aXRsZScsICdtb2RpZmllZCcsICdpZCcsICdwb3N0X19pbicsICdtZW51T3JkZXInIF0sXG5cdFx0LyoqXG5cdFx0ICogQSBtYXAgb2YgSmF2YVNjcmlwdCBvcmRlcmJ5IHZhbHVlcyB0byB0aGVpciBXUF9RdWVyeSBlcXVpdmFsZW50cy5cblx0XHQgKiBAdHlwZSB7T2JqZWN0fVxuXHRcdCAqL1xuXHRcdHZhbHVlbWFwOiB7XG5cdFx0XHQnaWQnOiAgICAgICAgICdJRCcsXG5cdFx0XHQnbWVudU9yZGVyJzogICdtZW51X29yZGVyIElEJ1xuXHRcdH1cblx0fSxcblx0LyoqXG5cdCAqIEEgbWFwIG9mIEphdmFTY3JpcHQgcXVlcnkgcHJvcGVydGllcyB0byB0aGVpciBXUF9RdWVyeSBlcXVpdmFsZW50cy5cblx0ICpcblx0ICogQHJlYWRvbmx5XG5cdCAqL1xuXHRwcm9wbWFwOiB7XG5cdFx0J2V4Y2x1ZGUnICA6ICdwb3N0X19ub3RfaW4nLFxuXHRcdCdpbmNsdWRlJyAgOiAncG9zdF9faW4nLFxuXHRcdCdtZW51T3JkZXInOiAnbWVudV9vcmRlcicsXG5cdFx0J3BlclBhZ2UnICA6ICdwb3N0c19wZXJfcGFnZScsXG5cdFx0J3NlYXJjaCcgICA6ICdzJyxcblx0XHQnc3RhdHVzJyAgIDogJ3Bvc3Rfc3RhdHVzJyxcblx0XHQndHlwZScgICAgIDogJ3Bvc3RfdHlwZSdcblx0fSxcblx0LyoqXG5cdCAqIENyZWF0ZXMgYW5kIHJldHVybnMgYW4gQXR0YWNobWVudHMgUXVlcnkgY29sbGVjdGlvbiBnaXZlbiB0aGUgcHJvcGVydGllcy5cblx0ICpcblx0ICogQ2FjaGVzIHF1ZXJ5IG9iamVjdHMgYW5kIHJldXNlcyB3aGVyZSBwb3NzaWJsZS5cblx0ICpcblx0ICogQHN0YXRpY1xuXHQgKiBAbWV0aG9kXG5cdCAqXG5cdCAqIEBwYXJhbSB7b2JqZWN0fSBbcHJvcHNdXG5cdCAqIEBwYXJhbSB7T2JqZWN0fSBbcHJvcHMuY2FjaGU9dHJ1ZV0gICBXaGV0aGVyIHRvIHVzZSB0aGUgcXVlcnkgY2FjaGUgb3Igbm90LlxuXHQgKiBAcGFyYW0ge09iamVjdH0gW3Byb3BzLm9yZGVyXVxuXHQgKiBAcGFyYW0ge09iamVjdH0gW3Byb3BzLm9yZGVyYnldXG5cdCAqIEBwYXJhbSB7T2JqZWN0fSBbcHJvcHMuaW5jbHVkZV1cblx0ICogQHBhcmFtIHtPYmplY3R9IFtwcm9wcy5leGNsdWRlXVxuXHQgKiBAcGFyYW0ge09iamVjdH0gW3Byb3BzLnNdXG5cdCAqIEBwYXJhbSB7T2JqZWN0fSBbcHJvcHMucG9zdF9taW1lX3R5cGVdXG5cdCAqIEBwYXJhbSB7T2JqZWN0fSBbcHJvcHMucG9zdHNfcGVyX3BhZ2VdXG5cdCAqIEBwYXJhbSB7T2JqZWN0fSBbcHJvcHMubWVudV9vcmRlcl1cblx0ICogQHBhcmFtIHtPYmplY3R9IFtwcm9wcy5wb3N0X3BhcmVudF1cblx0ICogQHBhcmFtIHtPYmplY3R9IFtwcm9wcy5wb3N0X3N0YXR1c11cblx0ICogQHBhcmFtIHtPYmplY3R9IFtvcHRpb25zXVxuXHQgKlxuXHQgKiBAcmV0dXJucyB7d3AubWVkaWEubW9kZWwuUXVlcnl9IEEgbmV3IEF0dGFjaG1lbnRzIFF1ZXJ5IGNvbGxlY3Rpb24uXG5cdCAqL1xuXHRnZXQ6IChmdW5jdGlvbigpe1xuXHRcdC8qKlxuXHRcdCAqIEBzdGF0aWNcblx0XHQgKiBAdHlwZSBBcnJheVxuXHRcdCAqL1xuXHRcdHZhciBxdWVyaWVzID0gW107XG5cblx0XHQvKipcblx0XHQgKiBAcmV0dXJucyB7UXVlcnl9XG5cdFx0ICovXG5cdFx0cmV0dXJuIGZ1bmN0aW9uKCBwcm9wcywgb3B0aW9ucyApIHtcblx0XHRcdHZhciBhcmdzICAgICA9IHt9LFxuXHRcdFx0XHRvcmRlcmJ5ICA9IFF1ZXJ5Lm9yZGVyYnksXG5cdFx0XHRcdGRlZmF1bHRzID0gUXVlcnkuZGVmYXVsdFByb3BzLFxuXHRcdFx0XHRxdWVyeSxcblx0XHRcdFx0Y2FjaGUgICAgPSAhISBwcm9wcy5jYWNoZSB8fCBfLmlzVW5kZWZpbmVkKCBwcm9wcy5jYWNoZSApO1xuXG5cdFx0XHQvLyBSZW1vdmUgdGhlIGBxdWVyeWAgcHJvcGVydHkuIFRoaXMgaXNuJ3QgbGlua2VkIHRvIGEgcXVlcnksXG5cdFx0XHQvLyB0aGlzICppcyogdGhlIHF1ZXJ5LlxuXHRcdFx0ZGVsZXRlIHByb3BzLnF1ZXJ5O1xuXHRcdFx0ZGVsZXRlIHByb3BzLmNhY2hlO1xuXG5cdFx0XHQvLyBGaWxsIGRlZmF1bHQgYXJncy5cblx0XHRcdF8uZGVmYXVsdHMoIHByb3BzLCBkZWZhdWx0cyApO1xuXG5cdFx0XHQvLyBOb3JtYWxpemUgdGhlIG9yZGVyLlxuXHRcdFx0cHJvcHMub3JkZXIgPSBwcm9wcy5vcmRlci50b1VwcGVyQ2FzZSgpO1xuXHRcdFx0aWYgKCAnREVTQycgIT09IHByb3BzLm9yZGVyICYmICdBU0MnICE9PSBwcm9wcy5vcmRlciApIHtcblx0XHRcdFx0cHJvcHMub3JkZXIgPSBkZWZhdWx0cy5vcmRlci50b1VwcGVyQ2FzZSgpO1xuXHRcdFx0fVxuXG5cdFx0XHQvLyBFbnN1cmUgd2UgaGF2ZSBhIHZhbGlkIG9yZGVyYnkgdmFsdWUuXG5cdFx0XHRpZiAoICEgXy5jb250YWlucyggb3JkZXJieS5hbGxvd2VkLCBwcm9wcy5vcmRlcmJ5ICkgKSB7XG5cdFx0XHRcdHByb3BzLm9yZGVyYnkgPSBkZWZhdWx0cy5vcmRlcmJ5O1xuXHRcdFx0fVxuXG5cdFx0XHRfLmVhY2goIFsgJ2luY2x1ZGUnLCAnZXhjbHVkZScgXSwgZnVuY3Rpb24oIHByb3AgKSB7XG5cdFx0XHRcdGlmICggcHJvcHNbIHByb3AgXSAmJiAhIF8uaXNBcnJheSggcHJvcHNbIHByb3AgXSApICkge1xuXHRcdFx0XHRcdHByb3BzWyBwcm9wIF0gPSBbIHByb3BzWyBwcm9wIF0gXTtcblx0XHRcdFx0fVxuXHRcdFx0fSApO1xuXG5cdFx0XHQvLyBHZW5lcmF0ZSB0aGUgcXVlcnkgYGFyZ3NgIG9iamVjdC5cblx0XHRcdC8vIENvcnJlY3QgYW55IGRpZmZlcmluZyBwcm9wZXJ0eSBuYW1lcy5cblx0XHRcdF8uZWFjaCggcHJvcHMsIGZ1bmN0aW9uKCB2YWx1ZSwgcHJvcCApIHtcblx0XHRcdFx0aWYgKCBfLmlzTnVsbCggdmFsdWUgKSApIHtcblx0XHRcdFx0XHRyZXR1cm47XG5cdFx0XHRcdH1cblxuXHRcdFx0XHRhcmdzWyBRdWVyeS5wcm9wbWFwWyBwcm9wIF0gfHwgcHJvcCBdID0gdmFsdWU7XG5cdFx0XHR9KTtcblxuXHRcdFx0Ly8gRmlsbCBhbnkgb3RoZXIgZGVmYXVsdCBxdWVyeSBhcmdzLlxuXHRcdFx0Xy5kZWZhdWx0cyggYXJncywgUXVlcnkuZGVmYXVsdEFyZ3MgKTtcblxuXHRcdFx0Ly8gYHByb3BzLm9yZGVyYnlgIGRvZXMgbm90IGFsd2F5cyBtYXAgZGlyZWN0bHkgdG8gYGFyZ3Mub3JkZXJieWAuXG5cdFx0XHQvLyBTdWJzdGl0dXRlIGV4Y2VwdGlvbnMgc3BlY2lmaWVkIGluIG9yZGVyYnkua2V5bWFwLlxuXHRcdFx0YXJncy5vcmRlcmJ5ID0gb3JkZXJieS52YWx1ZW1hcFsgcHJvcHMub3JkZXJieSBdIHx8IHByb3BzLm9yZGVyYnk7XG5cblx0XHRcdC8vIFNlYXJjaCB0aGUgcXVlcnkgY2FjaGUgZm9yIGEgbWF0Y2hpbmcgcXVlcnkuXG5cdFx0XHRpZiAoIGNhY2hlICkge1xuXHRcdFx0XHRxdWVyeSA9IF8uZmluZCggcXVlcmllcywgZnVuY3Rpb24oIHF1ZXJ5ICkge1xuXHRcdFx0XHRcdHJldHVybiBfLmlzRXF1YWwoIHF1ZXJ5LmFyZ3MsIGFyZ3MgKTtcblx0XHRcdFx0fSk7XG5cdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRxdWVyaWVzID0gW107XG5cdFx0XHR9XG5cblx0XHRcdC8vIE90aGVyd2lzZSwgY3JlYXRlIGEgbmV3IHF1ZXJ5IGFuZCBhZGQgaXQgdG8gdGhlIGNhY2hlLlxuXHRcdFx0aWYgKCAhIHF1ZXJ5ICkge1xuXHRcdFx0XHRxdWVyeSA9IG5ldyB3cC5tZWRpYS5tb2RlbC5RdWVyeSggW10sIF8uZXh0ZW5kKCBvcHRpb25zIHx8IHt9LCB7XG5cdFx0XHRcdFx0cHJvcHM6IHByb3BzLFxuXHRcdFx0XHRcdGFyZ3M6ICBhcmdzXG5cdFx0XHRcdH0gKSApO1xuXHRcdFx0XHRxdWVyaWVzLnB1c2goIHF1ZXJ5ICk7XG5cdFx0XHR9XG5cblx0XHRcdHJldHVybiBxdWVyeTtcblx0XHR9O1xuXHR9KCkpXG59KTsiLCJ2YXIgdmlkZW9fY2VudHJhbCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93Wyd2aWRlb19jZW50cmFsJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWyd2aWRlb19jZW50cmFsJ10gOiBudWxsKTtcbnZhciB3cCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93Wyd3cCddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnd3AnXSA6IG51bGwpO1xuXG52aWRlb19jZW50cmFsLmRhdGEgPSB2aWRlb0NlbnRyYWxQbGF5bGlzdENvbmZpZztcbnZpZGVvX2NlbnRyYWwuc2V0dGluZ3MoIHZpZGVvQ2VudHJhbFBsYXlsaXN0Q29uZmlnICk7XG5cbndwLm1lZGlhLnZpZXcuc2V0dGluZ3MucG9zdC5pZCA9IHZpZGVvX2NlbnRyYWwuZGF0YS5wb3N0SWQ7XG53cC5tZWRpYS52aWV3LnNldHRpbmdzLmRlZmF1bHRQcm9wcyA9IHt9O1xuXG52aWRlb19jZW50cmFsLm1vZGVsLlZpZGVvID0gcmVxdWlyZSggJy4vbW9kZWxzL3ZpZGVvJyApO1xudmlkZW9fY2VudHJhbC5tb2RlbC5WaWRlb3MgPSByZXF1aXJlKCAnLi9jb2xsZWN0aW9ucy92aWRlb3MnICk7XG5cbnZpZGVvX2NlbnRyYWwudmlldy5Qb3N0Rm9ybSA9IHJlcXVpcmUoICcuL3ZpZXdzL3Bvc3QtZm9ybScgKTtcbnZpZGVvX2NlbnRyYWwudmlldy5BZGRWaWRlb3NCdXR0b24gPSByZXF1aXJlKCAnLi92aWV3cy9idXR0b24vYWRkLXZpZGVvcycgKTtcbnZpZGVvX2NlbnRyYWwudmlldy5WaWRlb0xpc3QgPSByZXF1aXJlKCAnLi92aWV3cy92aWRlby1saXN0JyApO1xudmlkZW9fY2VudHJhbC52aWV3LlZpZGVvID0gcmVxdWlyZSggJy4vdmlld3MvdmlkZW8nICk7XG52aWRlb19jZW50cmFsLnZpZXcuVmlkZW9BcnR3b3JrID0gcmVxdWlyZSggJy4vdmlld3MvdmlkZW8vYXJ0d29yaycgKTtcbnZpZGVvX2NlbnRyYWwudmlldy5WaWRlb0F1ZGlvID0gcmVxdWlyZSggJy4vdmlld3MvdmlkZW8vYXVkaW8nICk7XG5cbnZpZGVvX2NlbnRyYWwud29ya2Zsb3dzID0gcmVxdWlyZSggJy4vd29ya2Zsb3dzJyApO1xuXG4oIGZ1bmN0aW9uKCAkICkge1xuICAgIHZhciB2aWRlb3M7XG5cblx0dmlkZW9zID0gdmlkZW9fY2VudHJhbC52aWRlb3MgPSBuZXcgdmlkZW9fY2VudHJhbC5tb2RlbC5WaWRlb3MoIHZpZGVvX2NlbnRyYWwuZGF0YS52aWRlb3MgKTtcblx0ZGVsZXRlIHZpZGVvX2NlbnRyYWwuZGF0YS52aWRlb3M7XG5cblx0dmFyIHBvc3RGb3JtID0gbmV3IHZpZGVvX2NlbnRyYWwudmlldy5Qb3N0Rm9ybSh7XG5cdFx0Y29sbGVjdGlvbjogdmlkZW9zLFxuXHRcdGwxMG46IHZpZGVvX2NlbnRyYWwubDEwblxuICAgIH0pO1xuICAgIFxufSAoIGpRdWVyeSApKTtcblxuIiwidmFyIEFkZFZpZGVvc0J1dHRvbixcblx0JCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93WydqUXVlcnknXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ2pRdWVyeSddIDogbnVsbCksXG5cdHdvcmtmbG93cyA9IHJlcXVpcmUoICcuLi8uLi93b3JrZmxvd3MnICksXG5cdHdwID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ3dwJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWyd3cCddIDogbnVsbCk7XG5cbkFkZFZpZGVvc0J1dHRvbiA9IHdwLkJhY2tib25lLlZpZXcuZXh0ZW5kKHtcblx0aWQ6ICdhZGQtdmlkZW9zJyxcblx0dGFnTmFtZTogJ3AnLFxuXG5cdGV2ZW50czoge1xuXHRcdCdjbGljayAuYnV0dG9uJzogJ2NsaWNrJ1xuXHR9LFxuXG5cdGluaXRpYWxpemU6IGZ1bmN0aW9uKCBvcHRpb25zICkge1xuXHRcdHRoaXMubDEwbiA9IG9wdGlvbnMubDEwbjtcblx0fSxcblxuXHRyZW5kZXI6IGZ1bmN0aW9uKCkge1xuXHRcdHZhciAkYnV0dG9uID0gJCggJzxhIC8+Jywge1xuXHRcdFx0dGV4dDogdGhpcy5sMTBuLmFkZFZpZGVvc1xuXHRcdH0pLmFkZENsYXNzKCAnYnV0dG9uIGJ1dHRvbi1zZWNvbmRhcnknICk7XG5cblx0XHR0aGlzLiRlbC5odG1sKCAkYnV0dG9uICk7XG5cblx0XHRyZXR1cm4gdGhpcztcblx0fSxcblxuXHRjbGljazogZnVuY3Rpb24oIGUgKSB7XG5cdFx0ZS5wcmV2ZW50RGVmYXVsdCgpO1xuXHRcdHdvcmtmbG93cy5nZXQoICdhZGRWaWRlb3MnICkub3BlbigpO1xuXHR9XG59KTtcblxubW9kdWxlLmV4cG9ydHMgPSBBZGRWaWRlb3NCdXR0b247XG4iLCJ2YXIgVmlkZW9zQnJvd3Nlcixcblx0XyA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93WydfJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWydfJ10gOiBudWxsKSxcblx0VmlkZW9zSXRlbXMgPSByZXF1aXJlKCAnLi4vdmlkZW9zL2l0ZW1zJyApLFxuXHRWaWRlb3NOb0l0ZW1zID0gcmVxdWlyZSggJy4uL3ZpZGVvcy9uby1pdGVtcycgKSxcblx0VmlkZW9zU2lkZWJhciA9IHJlcXVpcmUoICcuLi92aWRlb3Mvc2lkZWJhcicgKSxcblx0d3AgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snd3AnXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ3dwJ10gOiBudWxsKTtcblxuVmlkZW9zQnJvd3NlciA9IHdwLkJhY2tib25lLlZpZXcuZXh0ZW5kKHtcblx0Y2xhc3NOYW1lOiAndmlkZW8tY2VudHJhbC12aWRlb3MtYnJvd3NlcicsXG5cblx0aW5pdGlhbGl6ZTogZnVuY3Rpb24oIG9wdGlvbnMgKSB7XG5cdFx0dGhpcy5jb2xsZWN0aW9uID0gb3B0aW9ucy5jb250cm9sbGVyLnN0YXRlKCkuZ2V0KCAnY29sbGVjdGlvbicgKTtcblx0XHR0aGlzLmNvbnRyb2xsZXIgPSBvcHRpb25zLmNvbnRyb2xsZXI7XG5cblx0XHR0aGlzLl9wYWdlZCA9IDE7XG5cdFx0dGhpcy5fcGVuZGluZyA9IGZhbHNlO1xuXG5cdFx0Xy5iaW5kQWxsKCB0aGlzLCAnc2Nyb2xsJyApO1xuICAgICAgICB0aGlzLmxpc3RlblRvKCB0aGlzLmNvbGxlY3Rpb24sICdyZXNldCcsIHRoaXMucmVuZGVyICk7XG4gICAgICAgIFxuICAgICAgICBpZiAoICEgdGhpcy5jb2xsZWN0aW9uLmxlbmd0aCApIHtcblx0XHRcdHRoaXMuZ2V0VmlkZW9zKCk7XG5cdFx0fVxuXHR9LFxuXG5cdHJlbmRlcjogZnVuY3Rpb24oKSB7XG5cdFx0dGhpcy4kZWwub2ZmKCAnc2Nyb2xsJyApLm9uKCAnc2Nyb2xsJywgdGhpcy5zY3JvbGwgKTtcblxuXHRcdHRoaXMudmlld3MuYWRkKFtcblx0XHRcdG5ldyBWaWRlb3NJdGVtcyh7XG5cdFx0XHRcdGNvbGxlY3Rpb246IHRoaXMuY29sbGVjdGlvbixcblx0XHRcdFx0Y29udHJvbGxlcjogdGhpcy5jb250cm9sbGVyXG5cdFx0XHR9KSxcblx0XHRcdG5ldyBWaWRlb3NTaWRlYmFyKHtcblx0XHRcdFx0Y29udHJvbGxlcjogdGhpcy5jb250cm9sbGVyXG5cdFx0XHR9KSxcblx0XHRcdG5ldyBWaWRlb3NOb0l0ZW1zKHtcblx0XHRcdFx0Y29sbGVjdGlvbjogdGhpcy5jb2xsZWN0aW9uXG5cdFx0XHR9KVxuXHRcdF0pO1xuXG5cdFx0cmV0dXJuIHRoaXM7XG5cdH0sXG5cblx0c2Nyb2xsOiBmdW5jdGlvbigpIHtcblx0XHRpZiAoICEgdGhpcy5fcGVuZGluZyAmJiB0aGlzLmVsLnNjcm9sbEhlaWdodCA8IHRoaXMuZWwuc2Nyb2xsVG9wICsgdGhpcy5lbC5jbGllbnRIZWlnaHQgKiAzICkge1xuXHRcdFx0dGhpcy5fcGVuZGluZyA9IHRydWU7XG5cdFx0XHR0aGlzLmdldFZpZGVvcygpO1xuXHRcdH1cblx0fSxcblxuXHRnZXRWaWRlb3M6IGZ1bmN0aW9uKCkge1xuXHRcdHZhciB2aWV3ID0gdGhpcztcblxuXHRcdHdwLmFqYXgucG9zdCggJ3ZpZGVvX2NlbnRyYWxfZ2V0X3ZpZGVvc19mb3JfZnJhbWUnLCB7XG5cdFx0XHRwYWdlZDogdmlldy5fcGFnZWRcblx0XHR9KS5kb25lKGZ1bmN0aW9uKCByZXNwb25zZSApIHtcblx0XHRcdHZpZXcuY29sbGVjdGlvbi5hZGQoIHJlc3BvbnNlLnZpZGVvcyApO1xuXG5cdFx0XHR2aWV3Ll9wYWdlZCsrO1xuXG5cdFx0XHRpZiAoIHZpZXcuX3BhZ2VkIDw9IHJlc3BvbnNlLm1heE51bVBhZ2VzICkge1xuXHRcdFx0XHR2aWV3Ll9wZW5kaW5nID0gZmFsc2U7XG5cdFx0XHRcdHZpZXcuc2Nyb2xsKCk7XG5cdFx0XHR9XG5cdFx0fSk7XG5cdH1cbn0pO1xuXG5tb2R1bGUuZXhwb3J0cyA9IFZpZGVvc0Jyb3dzZXI7XG4iLCJ2YXIgSW5zZXJ0VmlkZW9zRnJhbWUsXG5cdFZpZGVvc0Jyb3dzZXIgPSByZXF1aXJlKCAnLi4vY29udGVudC92aWRlb3MtYnJvd3NlcicgKSxcbiAgICBWaWRlb3NDb250cm9sbGVyID0gcmVxdWlyZSggJy4uLy4uL2NvbnRyb2xsZXJzL3ZpZGVvcycgKSxcbiAgICBWaWRlb1F1ZXJ5ID0gcmVxdWlyZSggJy4uLy4uL21vZGVscy92aWRlb3MnICksXG5cdFZpZGVvc1Rvb2xiYXIgPSByZXF1aXJlKCAnLi4vdG9vbGJhci92aWRlb3MnICksXG5cdHdwID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ3dwJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWyd3cCddIDogbnVsbCksXG5cdFBvc3RGcmFtZSA9IHdwLm1lZGlhLnZpZXcuTWVkaWFGcmFtZS5Qb3N0O1xuICAgIFxuSW5zZXJ0VmlkZW9zRnJhbWUgPSBQb3N0RnJhbWUuZXh0ZW5kKHtcblx0Y3JlYXRlU3RhdGVzOiBmdW5jdGlvbigpIHtcbiAgICAgICAgUG9zdEZyYW1lLnByb3RvdHlwZS5jcmVhdGVTdGF0ZXMuYXBwbHkoIHRoaXMsIGFyZ3VtZW50cyApO1xuXG5cdFx0Ly8gQWRkIHRoZSBkZWZhdWx0IHN0YXRlcy5cblx0XHR0aGlzLnN0YXRlcy5hZGQoXG4gICAgICAgICAgICAvLyBBZGQgb3VyIEhUTUwgc2xpZGUgY29udHJvbGxlciBzdGF0ZS5cbiAgICAgICAgICAgIG5ldyBWaWRlb3NDb250cm9sbGVyKClcbiAgICAgICAgKTtcblx0fSxcblxuXHRiaW5kSGFuZGxlcnM6IGZ1bmN0aW9uKCkge1xuXHRcdFBvc3RGcmFtZS5wcm90b3R5cGUuYmluZEhhbmRsZXJzLmFwcGx5KCB0aGlzLCBhcmd1bWVudHMgKTtcblxuXHRcdC8vIHRoaXMub24oICdtZW51OmNyZWF0ZTpkZWZhdWx0JywgdGhpcy5jcmVhdGVDdWVNZW51LCB0aGlzICk7XG5cdFx0dGhpcy5vbiggJ2NvbnRlbnQ6Y3JlYXRlOnZpZGVvLWNlbnRyYWwtdmlkZW9zLWJyb3dzZXInLCB0aGlzLmNyZWF0ZUN1ZUNvbnRlbnQsIHRoaXMgKTtcblx0XHR0aGlzLm9uKCAndG9vbGJhcjpjcmVhdGU6dmlkZW8tY2VudHJhbC1wbGF5bGlzdC1pbnNlcnQtdmlkZW9zJywgdGhpcy5jcmVhdGVDdWVUb29sYmFyLCB0aGlzICk7XG5cdH0sXG5cblx0Y3JlYXRlQ3VlTWVudTogZnVuY3Rpb24oIG1lbnUgKSB7XG5cdFx0bWVudS52aWV3LnNldCh7XG5cdFx0XHQndmlkZW8tY2VudHJhbC1wbGF5bGlzdC12aWRlb3Mtc2VwYXJhdG9yJzogbmV3IHdwLm1lZGlhLlZpZXcoe1xuXHRcdFx0XHRjbGFzc05hbWU6ICdzZXBhcmF0b3InLFxuXHRcdFx0XHRwcmlvcml0eTogMjAwXG5cdFx0XHR9KVxuXHRcdH0pO1xuXHR9LFxuXG5cdGNyZWF0ZUN1ZUNvbnRlbnQ6IGZ1bmN0aW9uKCBjb250ZW50ICkge1xuXHRcdGNvbnRlbnQudmlldyA9IG5ldyBWaWRlb3NCcm93c2VyKHtcblx0XHRcdGNvbnRyb2xsZXI6IHRoaXNcblx0XHR9KTtcblx0fSxcblxuXHRjcmVhdGVDdWVUb29sYmFyOiBmdW5jdGlvbiggdG9vbGJhciApIHtcblx0XHR0b29sYmFyLnZpZXcgPSBuZXcgVmlkZW9zVG9vbGJhcih7XG5cdFx0XHRjb250cm9sbGVyOiB0aGlzXG5cdFx0fSk7XG5cdH0sXG59KTtcblxubW9kdWxlLmV4cG9ydHMgPSBJbnNlcnRWaWRlb3NGcmFtZTtcbiIsInZhciBQb3N0Rm9ybSxcblx0JCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93WydqUXVlcnknXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ2pRdWVyeSddIDogbnVsbCksXG5cdEFkZFZpZGVvc0J1dHRvbiA9IHJlcXVpcmUoICcuL2J1dHRvbi9hZGQtdmlkZW9zJyApLFxuXHRWaWRlb0xpc3QgPSByZXF1aXJlKCAnLi92aWRlby1saXN0JyApLFxuXHR3cCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93Wyd3cCddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnd3AnXSA6IG51bGwpO1xuXG5Qb3N0Rm9ybSA9IHdwLkJhY2tib25lLlZpZXcuZXh0ZW5kKHtcblx0ZWw6ICcjcG9zdCcsXG5cdHNhdmVkOiBmYWxzZSxcblxuXHRldmVudHM6IHtcblx0XHQnY2xpY2sgI3B1Ymxpc2gnOiAnYnV0dG9uQ2xpY2snLFxuXHRcdCdjbGljayAjc2F2ZS1wb3N0JzogJ2J1dHRvbkNsaWNrJ1xuXHRcdC8vJ3N1Ym1pdCc6ICdzdWJtaXQnXG5cdH0sXG5cblx0aW5pdGlhbGl6ZTogZnVuY3Rpb24oIG9wdGlvbnMgKSB7XG5cdFx0dGhpcy5sMTBuID0gb3B0aW9ucy5sMTBuO1xuXG5cdFx0dGhpcy5yZW5kZXIoKTtcblx0fSxcblxuXHRyZW5kZXI6IGZ1bmN0aW9uKCkge1xuXHRcdHRoaXMudmlld3MuYWRkKCAnI3ZpZGVvLWNlbnRyYWwtcGxheWxpc3QtcGxheWxpc3QtZWRpdG9yIC52aWRlby1jZW50cmFsLXBsYXlsaXN0LXBhbmVsLWJvZHknLCBbXG5cdFx0XHRuZXcgQWRkVmlkZW9zQnV0dG9uKHtcblx0XHRcdFx0Y29sbGVjdGlvbjogdGhpcy5jb2xsZWN0aW9uLFxuXHRcdFx0XHRsMTBuOiB0aGlzLmwxMG5cblx0XHRcdH0pLFxuXG5cdFx0XHRuZXcgVmlkZW9MaXN0KHtcblx0XHRcdFx0Y29sbGVjdGlvbjogdGhpcy5jb2xsZWN0aW9uXG5cdFx0XHR9KVxuXHRcdF0pO1xuXG5cdFx0cmV0dXJuIHRoaXM7XG5cdH0sXG5cblx0YnV0dG9uQ2xpY2s6IGZ1bmN0aW9uKCBlICkge1xuXHRcdHZhciBzZWxmID0gdGhpcyxcblx0XHRcdCRidXR0b24gPSAkKCBlLnRhcmdldCApO1xuXG5cdFx0aWYgKCAhIHNlbGYuc2F2ZWQgKSB7XG5cdFx0XHR0aGlzLmNvbGxlY3Rpb24uc2F2ZSgpLmRvbmUoZnVuY3Rpb24oIGRhdGEgKSB7XG5cdFx0XHRcdHNlbGYuc2F2ZWQgPSB0cnVlO1xuXHRcdFx0XHQkYnV0dG9uLmNsaWNrKCk7XG5cdFx0XHR9KTtcblx0XHR9XG5cblx0XHRyZXR1cm4gc2VsZi5zYXZlZDtcblx0fVxufSk7XG5cbm1vZHVsZS5leHBvcnRzID0gUG9zdEZvcm07XG4iLCJ2YXIgVmlkZW9zVG9vbGJhcixcblx0XyA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93WydfJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWydfJ10gOiBudWxsKSxcblx0d3AgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snd3AnXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ3dwJ10gOiBudWxsKTtcblxuVmlkZW9zVG9vbGJhciA9IHdwLm1lZGlhLnZpZXcuVG9vbGJhci5leHRlbmQoe1xuXHRpbml0aWFsaXplOiBmdW5jdGlvbiggb3B0aW9ucyApIHtcblx0XHR0aGlzLmNvbnRyb2xsZXIgPSBvcHRpb25zLmNvbnRyb2xsZXI7XG5cblx0XHRfLmJpbmRBbGwoIHRoaXMsICdpbnNlcnRDdWVTaG9ydGNvZGUnICk7XG5cblx0XHQvLyBUaGlzIGlzIGEgYnV0dG9uLlxuXHRcdHRoaXMub3B0aW9ucy5pdGVtcyA9IF8uZGVmYXVsdHMoIHRoaXMub3B0aW9ucy5pdGVtcyB8fCB7fSwge1xuXHRcdFx0aW5zZXJ0OiB7XG5cdFx0XHRcdHRleHQ6IHdwLm1lZGlhLnZpZXcubDEwbi5pbnNlcnRJbnRvUG9zdCB8fCAnSW5zZXJ0IGludG8gcG9zdCcsXG5cdFx0XHRcdHN0eWxlOiAncHJpbWFyeScsXG5cdFx0XHRcdHByaW9yaXR5OiA4MCxcblx0XHRcdFx0cmVxdWlyZXM6IHtcblx0XHRcdFx0XHRzZWxlY3Rpb246IHRydWVcblx0XHRcdFx0fSxcblx0XHRcdFx0Y2xpY2s6IHRoaXMuaW5zZXJ0Q3VlU2hvcnRjb2RlXG5cdFx0XHR9XG5cdFx0fSk7XG5cblx0XHR3cC5tZWRpYS52aWV3LlRvb2xiYXIucHJvdG90eXBlLmluaXRpYWxpemUuYXBwbHkoIHRoaXMsIGFyZ3VtZW50cyApO1xuXHR9LFxuXG5cdGluc2VydEN1ZVNob3J0Y29kZTogZnVuY3Rpb24oKSB7XG5cdFx0dmFyIGh0bWwsXG5cdFx0XHRzdGF0ZSA9IHRoaXMuY29udHJvbGxlci5zdGF0ZSgpLFxuXHRcdFx0YXR0cmlidXRlcyA9IHN0YXRlLmdldCggJ2F0dHJpYnV0ZXMnICkudG9KU09OKCksXG5cdFx0XHRzZWxlY3Rpb24gPSBzdGF0ZS5nZXQoICdzZWxlY3Rpb24nICkuZmlyc3QoKTtcblxuXHRcdGF0dHJpYnV0ZXMuaWQgPSBzZWxlY3Rpb24uZ2V0KCAnaWQnICk7XG5cdFx0Xy5waWNrKCBhdHRyaWJ1dGVzLCAnaWQnLCAndGhlbWUnLCAnd2lkdGgnLCAnc2hvd192aWRlb3MnICk7XG5cblx0XHRpZiAoICEgYXR0cmlidXRlcy5zaG93X3ZpZGVvcyApIHtcblx0XHRcdGF0dHJpYnV0ZXMuc2hvd192aWRlb3MgPSAnMCc7XG5cdFx0fSBlbHNlIHtcblx0XHRcdGRlbGV0ZSBhdHRyaWJ1dGVzLnNob3dfdmlkZW9zO1xuXHRcdH1cblxuXHRcdGh0bWwgPSB3cC5zaG9ydGNvZGUuc3RyaW5nKHtcblx0XHRcdHRhZzogJ3ZpZGVvX2NlbnRyYWwnLFxuXHRcdFx0dHlwZTogJ3NpbmdsZScsXG5cdFx0XHRhdHRyczogYXR0cmlidXRlc1xuXHRcdH0pO1xuXG5cdFx0d3AubWVkaWEuZWRpdG9yLmluc2VydCggaHRtbCApO1xuXHRcdHRoaXMuY29udHJvbGxlci5jbG9zZSgpO1xuXHR9XG59KTtcblxubW9kdWxlLmV4cG9ydHMgPSBWaWRlb3NUb29sYmFyO1xuIiwidmFyIFZpZGVvTGlzdCxcblx0JCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93WydqUXVlcnknXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ2pRdWVyeSddIDogbnVsbCksXG5cdF8gPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snXyddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnXyddIDogbnVsbCksXG5cdFZpZGVvID0gcmVxdWlyZSggJy4vdmlkZW8nICksXG5cdHdwID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ3dwJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWyd3cCddIDogbnVsbCk7XG5cblZpZGVvTGlzdCA9IHdwLkJhY2tib25lLlZpZXcuZXh0ZW5kKHtcblx0Y2xhc3NOYW1lOiAndmlkZW8tY2VudHJhbC1wbGF5bGlzdC12aWRlb2xpc3QnLFxuXHR0YWdOYW1lOiAnb2wnLFxuXG5cdGluaXRpYWxpemU6IGZ1bmN0aW9uKCkge1xuXHRcdHRoaXMubGlzdGVuVG8oIHRoaXMuY29sbGVjdGlvbiwgJ2FkZCcsIHRoaXMuYWRkVmlkZW8gKTtcblx0XHR0aGlzLmxpc3RlblRvKCB0aGlzLmNvbGxlY3Rpb24sICdhZGQgcmVtb3ZlJywgdGhpcy51cGRhdGVPcmRlciApO1xuXHRcdHRoaXMubGlzdGVuVG8oIHRoaXMuY29sbGVjdGlvbiwgJ3Jlc2V0JywgdGhpcy5yZW5kZXIgKTtcblx0fSxcblxuXHRyZW5kZXI6IGZ1bmN0aW9uKCkge1xuXHRcdHRoaXMuJGVsLmVtcHR5KCk7XG5cblx0XHR0aGlzLmNvbGxlY3Rpb24uZWFjaCggdGhpcy5hZGRWaWRlbywgdGhpcyApO1xuXHRcdHRoaXMudXBkYXRlT3JkZXIoKTtcblxuXHRcdHRoaXMuJGVsLnNvcnRhYmxlKCB7XG5cdFx0XHRheGlzOiAneScsXG5cdFx0XHRkZWxheTogMTUwLFxuXHRcdFx0Zm9yY2VIZWxwZXJTaXplOiB0cnVlLFxuXHRcdFx0Zm9yY2VQbGFjZWhvbGRlclNpemU6IHRydWUsXG5cdFx0XHRvcGFjaXR5OiAwLjYsXG5cdFx0XHRzdGFydDogZnVuY3Rpb24oIGUsIHVpICkge1xuXHRcdFx0XHR1aS5wbGFjZWhvbGRlci5jc3MoICd2aXNpYmlsaXR5JywgJ3Zpc2libGUnICk7XG5cdFx0XHR9LFxuXHRcdFx0dXBkYXRlOiBfLmJpbmQoZnVuY3Rpb24oIGUsIHVpICkge1xuXHRcdFx0XHR0aGlzLnVwZGF0ZU9yZGVyKCk7XG5cdFx0XHR9LCB0aGlzIClcblx0XHR9ICk7XG5cblx0XHRyZXR1cm4gdGhpcztcblx0fSxcblxuXHRhZGRWaWRlbzogZnVuY3Rpb24oIHZpZGVvICkge1xuXHRcdHZhciB2aWRlb1ZpZXcgPSBuZXcgVmlkZW8oeyBtb2RlbDogdmlkZW8gfSk7XG5cdFx0dGhpcy4kZWwuYXBwZW5kKCB2aWRlb1ZpZXcucmVuZGVyKCkuZWwgKTtcblx0fSxcblxuXHR1cGRhdGVPcmRlcjogZnVuY3Rpb24oKSB7XG5cdFx0Xy5lYWNoKCB0aGlzLiRlbC5maW5kKCAnLnZpZGVvLWNlbnRyYWwtcGxheWxpc3QtdmlkZW8nICksIGZ1bmN0aW9uKCBpdGVtLCBpICkge1xuXHRcdFx0dmFyIGNpZCA9ICQoIGl0ZW0gKS5kYXRhKCAnY2lkJyApO1xuXHRcdFx0dGhpcy5jb2xsZWN0aW9uLmdldCggY2lkICkuc2V0KCAnb3JkZXInLCBpICk7XG5cdFx0fSwgdGhpcyApO1xuXHR9XG59KTtcblxubW9kdWxlLmV4cG9ydHMgPSBWaWRlb0xpc3Q7XG4iLCJ2YXIgVmlkZW8sXG5cdCQgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snalF1ZXJ5J10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWydqUXVlcnknXSA6IG51bGwpLFxuXHRWaWRlb0FydHdvcmsgPSByZXF1aXJlKCAnLi92aWRlby9hcnR3b3JrJyApLFxuXHRWaWRlb0F1ZGlvID0gcmVxdWlyZSggJy4vdmlkZW8vYXVkaW8nICksXG5cdHdwID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ3dwJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWyd3cCddIDogbnVsbCk7XG5cblZpZGVvID0gd3AuQmFja2JvbmUuVmlldy5leHRlbmQoe1xuXHR0YWdOYW1lOiAnbGknLFxuXHRjbGFzc05hbWU6ICd2aWRlby1jZW50cmFsLXBsYXlsaXN0LXZpZGVvJyxcblx0dGVtcGxhdGU6IHdwLnRlbXBsYXRlKCAndmlkZW8tY2VudHJhbC1wbGF5bGlzdC1wbGF5bGlzdC12aWRlbycgKSxcblxuXHRldmVudHM6IHtcblx0XHQnY2hhbmdlIFtkYXRhLXNldHRpbmddJzogJ3VwZGF0ZUF0dHJpYnV0ZScsXG5cdFx0J2NsaWNrIC5qcy10b2dnbGUnOiAndG9nZ2xlT3BlblN0YXR1cycsXG5cdFx0J2RibGNsaWNrIC52aWRlby1jZW50cmFsLXBsYXlsaXN0LXZpZGVvLXRpdGxlJzogJ3RvZ2dsZU9wZW5TdGF0dXMnLFxuXHRcdCdjbGljayAuanMtY2xvc2UnOiAnbWluaW1pemUnLFxuXHRcdCdjbGljayAuanMtcmVtb3ZlJzogJ2Rlc3Ryb3knXG5cdH0sXG5cblx0aW5pdGlhbGl6ZTogZnVuY3Rpb24oKSB7XG5cdFx0dGhpcy5saXN0ZW5UbyggdGhpcy5tb2RlbCwgJ2NoYW5nZTp0aXRsZScsIHRoaXMudXBkYXRlVGl0bGUgKTtcblx0XHR0aGlzLmxpc3RlblRvKCB0aGlzLm1vZGVsLCAnY2hhbmdlJywgdGhpcy51cGRhdGVGaWVsZHMgKTtcblx0XHR0aGlzLmxpc3RlblRvKCB0aGlzLm1vZGVsLCAnZGVzdHJveScsIHRoaXMucmVtb3ZlICk7XG5cdH0sXG5cblx0cmVuZGVyOiBmdW5jdGlvbigpIHtcblx0XHR0aGlzLiRlbC5odG1sKCB0aGlzLnRlbXBsYXRlKCB0aGlzLm1vZGVsLnRvSlNPTigpICkgKS5kYXRhKCAnY2lkJywgdGhpcy5tb2RlbC5jaWQgKTtcblxuXHRcdHRoaXMudmlld3MuYWRkKCAnLnZpZGVvLWNlbnRyYWwtcGxheWxpc3QtdmlkZW8tY29sdW1uLWFydHdvcmsnLCBuZXcgVmlkZW9BcnR3b3JrKHtcblx0XHRcdG1vZGVsOiB0aGlzLm1vZGVsLFxuXHRcdFx0cGFyZW50OiB0aGlzXG5cdFx0fSkpO1xuXG5cdFx0dGhpcy52aWV3cy5hZGQoICcudmlkZW8tY2VudHJhbC1wbGF5bGlzdC12aWRlby1hdWRpby1ncm91cCcsIG5ldyBWaWRlb0F1ZGlvKHtcblx0XHRcdG1vZGVsOiB0aGlzLm1vZGVsLFxuXHRcdFx0cGFyZW50OiB0aGlzXG5cdFx0fSkpO1xuXG5cdFx0cmV0dXJuIHRoaXM7XG5cdH0sXG5cblx0bWluaW1pemU6IGZ1bmN0aW9uKCBlICkge1xuXHRcdGUucHJldmVudERlZmF1bHQoKTtcblx0XHR0aGlzLiRlbC5yZW1vdmVDbGFzcyggJ2lzLW9wZW4nICkuZmluZCggJ2lucHV0OmZvY3VzJyApLmJsdXIoKTtcblx0fSxcblxuXHR0b2dnbGVPcGVuU3RhdHVzOiBmdW5jdGlvbiggZSApIHtcblx0XHRlLnByZXZlbnREZWZhdWx0KCk7XG5cdFx0dGhpcy4kZWwudG9nZ2xlQ2xhc3MoICdpcy1vcGVuJyApLmZpbmQoICdpbnB1dDpmb2N1cycgKS5ibHVyKCk7XG5cblx0XHQvLyBUcmlnZ2VyIGEgcmVzaXplIHNvIHRoZSBtZWRpYSBlbGVtZW50IHdpbGwgZmlsbCB0aGUgY29udGFpbmVyLlxuXHRcdGlmICggdGhpcy4kZWwuaGFzQ2xhc3MoICdpcy1vcGVuJyApICkge1xuXHRcdFx0JCggd2luZG93ICkudHJpZ2dlciggJ3Jlc2l6ZScgKTtcblx0XHR9XG5cdH0sXG5cblx0LyoqXG5cdCAqIFVwZGF0ZSBhIG1vZGVsIGF0dHJpYnV0ZSB3aGVuIGEgZmllbGQgaXMgY2hhbmdlZC5cblx0ICpcblx0ICogRmllbGRzIHdpdGggYSAnZGF0YS1zZXR0aW5nPVwie3trZXl9fVwiJyBhdHRyaWJ1dGUgd2hvc2UgdmFsdWVcblx0ICogY29ycmVzcG9uZHMgdG8gYSBtb2RlbCBhdHRyaWJ1dGUgd2lsbCBiZSBhdXRvbWF0aWNhbGx5IHN5bmNlZC5cblx0ICpcblx0ICogQHBhcmFtIHtPYmplY3R9IGUgRXZlbnQgb2JqZWN0LlxuXHQgKi9cblx0dXBkYXRlQXR0cmlidXRlOiBmdW5jdGlvbiggZSApIHtcblx0XHR2YXIgYXR0cmlidXRlID0gJCggZS50YXJnZXQgKS5kYXRhKCAnc2V0dGluZycgKSxcblx0XHRcdHZhbHVlID0gZS50YXJnZXQudmFsdWU7XG5cblx0XHRpZiAoIHRoaXMubW9kZWwuZ2V0KCBhdHRyaWJ1dGUgKSAhPT0gdmFsdWUgKSB7XG5cdFx0XHR0aGlzLm1vZGVsLnNldCggYXR0cmlidXRlLCB2YWx1ZSApO1xuXHRcdH1cblx0fSxcblxuXHQvKipcblx0ICogVXBkYXRlIGEgc2V0dGluZyBmaWVsZCB3aGVuIGEgbW9kZWwncyBhdHRyaWJ1dGUgaXMgY2hhbmdlZC5cblx0ICovXG5cdHVwZGF0ZUZpZWxkczogZnVuY3Rpb24oKSB7XG5cdFx0dmFyIHZpZGVvID0gdGhpcy5tb2RlbC50b0pTT04oKSxcblx0XHRcdCRzZXR0aW5ncyA9IHRoaXMuJGVsLmZpbmQoICdbZGF0YS1zZXR0aW5nXScgKSxcblx0XHRcdGF0dHJpYnV0ZSwgdmFsdWU7XG5cblx0XHQvLyBBIGNoYW5nZSBldmVudCBzaG91bGRuJ3QgYmUgdHJpZ2dlcmVkIGhlcmUsIHNvIGl0IHdvbid0IGNhdXNlXG5cdFx0Ly8gdGhlIG1vZGVsIGF0dHJpYnV0ZSB0byBiZSB1cGRhdGVkIGFuZCBnZXQgc3R1Y2sgaW4gYW5cblx0XHQvLyBpbmZpbml0ZSBsb29wLlxuXHRcdGZvciAoIGF0dHJpYnV0ZSBpbiB2aWRlbyApIHtcblx0XHRcdC8vIERlY29kZSBIVE1MIGVudGl0aWVzLlxuXHRcdFx0dmFsdWUgPSAkKCAnPGRpdi8+JyApLmh0bWwoIHZpZGVvWyBhdHRyaWJ1dGUgXSApLnRleHQoKTtcblx0XHRcdCRzZXR0aW5ncy5maWx0ZXIoICdbZGF0YS1zZXR0aW5nPVwiJyArIGF0dHJpYnV0ZSArICdcIl0nICkudmFsKCB2YWx1ZSApO1xuXHRcdH1cblx0fSxcblxuXHR1cGRhdGVUaXRsZTogZnVuY3Rpb24oKSB7XG5cdFx0dmFyIHRpdGxlID0gdGhpcy5tb2RlbC5nZXQoICd0aXRsZScgKTtcblx0XHR0aGlzLiRlbC5maW5kKCAnLnZpZGVvLWNlbnRyYWwtcGxheWxpc3QtdmlkZW8tdGl0bGUgLnRleHQnICkudGV4dCggdGl0bGUgPyB0aXRsZSA6ICdUaXRsZScgKTtcblx0fSxcblxuXHQvKipcblx0ICogRGVzdHJveSB0aGUgdmlldydzIG1vZGVsLlxuXHQgKlxuXHQgKiBBdm9pZCBzeW5jaW5nIHRvIHRoZSBzZXJ2ZXIgYnkgdHJpZ2dlcmluZyBhbiBldmVudCBpbnN0ZWFkIG9mXG5cdCAqIGNhbGxpbmcgZGVzdHJveSgpIGRpcmVjdGx5IG9uIHRoZSBtb2RlbC5cblx0ICovXG5cdGRlc3Ryb3k6IGZ1bmN0aW9uKCkge1xuXHRcdHRoaXMubW9kZWwudHJpZ2dlciggJ2Rlc3Ryb3knLCB0aGlzLm1vZGVsICk7XG5cdH0sXG5cblx0cmVtb3ZlOiBmdW5jdGlvbigpIHtcblx0XHR0aGlzLiRlbC5yZW1vdmUoKTtcblx0fVxufSk7XG5cbm1vZHVsZS5leHBvcnRzID0gVmlkZW87XG4iLCJ2YXIgVmlkZW9BcnR3b3JrLFxuXHRfID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ18nXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ18nXSA6IG51bGwpLFxuXHR3b3JrZmxvd3MgPSByZXF1aXJlKCAnLi4vLi4vd29ya2Zsb3dzJyApLFxuXHR3cCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93Wyd3cCddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnd3AnXSA6IG51bGwpO1xuXG5WaWRlb0FydHdvcmsgPSB3cC5CYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG5cdHRhZ05hbWU6ICdzcGFuJyxcblx0Y2xhc3NOYW1lOiAndmlkZW8tY2VudHJhbC1wbGF5bGlzdC12aWRlby1hcnR3b3JrJyxcblx0dGVtcGxhdGU6IHdwLnRlbXBsYXRlKCAndmlkZW8tY2VudHJhbC1wbGF5bGlzdC1wbGF5bGlzdC12aWRlby1hcnR3b3JrJyApLFxuXG5cdGV2ZW50czoge1xuXHRcdCdjbGljayc6ICdzZWxlY3QnXG5cdH0sXG5cblx0aW5pdGlhbGl6ZTogZnVuY3Rpb24oIG9wdGlvbnMgKSB7XG5cdFx0dGhpcy5wYXJlbnQgPSBvcHRpb25zLnBhcmVudDtcblx0XHR0aGlzLmxpc3RlblRvKCB0aGlzLm1vZGVsLCAnY2hhbmdlOmFydHdvcmtVcmwnLCB0aGlzLnJlbmRlciApO1xuXHR9LFxuXG5cdHJlbmRlcjogZnVuY3Rpb24oKSB7XG5cdFx0dGhpcy4kZWwuaHRtbCggdGhpcy50ZW1wbGF0ZSggdGhpcy5tb2RlbC50b0pTT04oKSApICk7XG5cdFx0dGhpcy5wYXJlbnQuJGVsLnRvZ2dsZUNsYXNzKCAnaGFzLWFydHdvcmsnLCAhIF8uaXNFbXB0eSggdGhpcy5tb2RlbC5nZXQoICdhcnR3b3JrVXJsJyApICkgKTtcblx0XHRyZXR1cm4gdGhpcztcblx0fSxcblxuXHRzZWxlY3Q6IGZ1bmN0aW9uKCkge1xuXHRcdHdvcmtmbG93cy5zZXRNb2RlbCggdGhpcy5tb2RlbCApLmdldCggJ3NlbGVjdEFydHdvcmsnICkub3BlbigpO1xuXHR9XG59KTtcblxubW9kdWxlLmV4cG9ydHMgPSBWaWRlb0FydHdvcms7XG4iLCJ2YXIgVmlkZW9BdWRpbyxcblx0JCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93WydqUXVlcnknXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ2pRdWVyeSddIDogbnVsbCksXG5cdF8gPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snXyddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnXyddIDogbnVsbCksXG5cdHNldHRpbmdzID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ3ZpZGVvX2NlbnRyYWwnXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ3ZpZGVvX2NlbnRyYWwnXSA6IG51bGwpLnNldHRpbmdzKCksXG5cdHdvcmtmbG93cyA9IHJlcXVpcmUoICcuLi8uLi93b3JrZmxvd3MnICksXG5cdHdwID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ3dwJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWyd3cCddIDogbnVsbCk7XG5cblZpZGVvQXVkaW8gPSB3cC5CYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG5cdHRhZ05hbWU6ICdzcGFuJyxcblx0Y2xhc3NOYW1lOiAndmlkZW8tY2VudHJhbC1wbGF5bGlzdC12aWRlby1hdWRpbycsXG5cdHRlbXBsYXRlOiB3cC50ZW1wbGF0ZSggJ3ZpZGVvLWNlbnRyYWwtcGxheWxpc3QtcGxheWxpc3QtdmlkZW8tYXVkaW8nICksXG5cblx0ZXZlbnRzOiB7XG5cdFx0J2NsaWNrIC52aWRlby1jZW50cmFsLXBsYXlsaXN0LXZpZGVvLWF1ZGlvLXNlbGVjdG9yJzogJ3NlbGVjdCdcblx0fSxcblxuXHRpbml0aWFsaXplOiBmdW5jdGlvbiggb3B0aW9ucyApIHtcblx0XHR0aGlzLnBhcmVudCA9IG9wdGlvbnMucGFyZW50O1xuXG5cdFx0dGhpcy5saXN0ZW5UbyggdGhpcy5tb2RlbCwgJ2NoYW5nZTphdWRpb1VybCcsIHRoaXMucmVmcmVzaCApO1xuXHRcdHRoaXMubGlzdGVuVG8oIHRoaXMubW9kZWwsICdkZXN0cm95JywgdGhpcy5jbGVhbnVwICk7XG5cdH0sXG5cblx0cmVuZGVyOiBmdW5jdGlvbigpIHtcblx0XHR2YXIgJG1lZGlhRWwsIHBsYXllclNldHRpbmdzLFxuXHRcdFx0dmlkZW8gPSB0aGlzLm1vZGVsLnRvSlNPTigpLFxuXHRcdFx0cGxheWVySWQgPSB0aGlzLiRlbC5maW5kKCAnLm1lanMtYXVkaW8nICkuYXR0ciggJ2lkJyApO1xuXG5cdFx0Ly8gUmVtb3ZlIHRoZSBNZWRpYUVsZW1lbnQgcGxheWVyIG9iamVjdCBpZiB0aGVcblx0XHQvLyBhdWRpbyBmaWxlIFVSTCBpcyBlbXB0eS5cblx0XHRpZiAoICcnID09PSB2aWRlby5hdWRpb1VybCAmJiBwbGF5ZXJJZCApIHtcblx0XHRcdG1lanMucGxheWVyc1sgcGxheWVySWQgXS5yZW1vdmUoKTtcblx0XHR9XG5cblx0XHQvLyBSZW5kZXIgdGhlIG1lZGlhIGVsZW1lbnQuXG5cdFx0dGhpcy4kZWwuaHRtbCggdGhpcy50ZW1wbGF0ZSggdGhpcy5tb2RlbC50b0pTT04oKSApICk7XG5cblx0XHQvLyBTZXQgdXAgTWVkaWFFbGVtZW50LmpzLlxuXHRcdCRtZWRpYUVsID0gdGhpcy4kZWwuZmluZCggJy52aWRlby1jZW50cmFsLXBsYXlsaXN0LWF1ZGlvJyApO1xuXG5cdFx0cmV0dXJuIHRoaXM7XG5cdH0sXG5cblx0cmVmcmVzaDogZnVuY3Rpb24oIGUgKSB7XG5cdFx0dmFyIHZpZGVvID0gdGhpcy5tb2RlbC50b0pTT04oKSxcblx0XHRcdHBsYXllcklkID0gdGhpcy4kZWwuZmluZCggJy5tZWpzLWF1ZGlvJyApLmF0dHIoICdpZCcgKSxcblx0XHRcdHBsYXllciA9IHBsYXllcklkID8gbWVqcy5wbGF5ZXJzWyBwbGF5ZXJJZCBdIDogbnVsbDtcblxuXHRcdGlmICggcGxheWVyICYmICcnICE9PSB2aWRlby5hdWRpb1VybCApIHtcblx0XHRcdHBsYXllci5wYXVzZSgpO1xuXHRcdFx0cGxheWVyLnNldFNyYyggdmlkZW8uYXVkaW9VcmwgKTtcblx0XHR9IGVsc2Uge1xuXHRcdFx0dGhpcy5yZW5kZXIoKTtcblx0XHR9XG5cdH0sXG5cblx0Y2xlYW51cDogZnVuY3Rpb24oKSB7XG5cdFx0dmFyIHBsYXllcklkID0gdGhpcy4kZWwuZmluZCggJy5tZWpzLWF1ZGlvJyApLmF0dHIoICdpZCcgKSxcblx0XHRcdHBsYXllciA9IHBsYXllcklkID8gbWVqcy5wbGF5ZXJzWyBwbGF5ZXJJZCBdIDogbnVsbDtcblxuXHRcdGlmICggcGxheWVyICkge1xuXHRcdFx0cGxheWVyLnJlbW92ZSgpO1xuXHRcdH1cblx0fSxcblxuXHRzZWxlY3Q6IGZ1bmN0aW9uKCkge1xuXHRcdHdvcmtmbG93cy5zZXRNb2RlbCggdGhpcy5tb2RlbCApLmdldCggJ3NlbGVjdEF1ZGlvJyApLm9wZW4oKTtcblx0fVxufSk7XG5cbm1vZHVsZS5leHBvcnRzID0gVmlkZW9BdWRpbztcbiIsInZhciBWaWRlb3MsXG5cdHdwID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ3dwJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWyd3cCddIDogbnVsbCk7XG5cblZpZGVvcyA9IHdwLkJhY2tib25lLlZpZXcuZXh0ZW5kKHtcblx0dGFnTmFtZTogJ2xpJyxcblx0Y2xhc3NOYW1lOiAndmlkZW8tY2VudHJhbC12aWRlb3MtYnJvd3Nlci1saXN0LWl0ZW0nLFxuXHR0ZW1wbGF0ZTogd3AudGVtcGxhdGUoICd2aWRlby1jZW50cmFsLXZpZGVvcy1icm93c2VyLWxpc3QtaXRlbScgKSxcblxuXHRldmVudHM6IHtcblx0XHQnY2xpY2snOiAncmVzZXRTZWxlY3Rpb24nXG5cdH0sXG5cblx0aW5pdGlhbGl6ZTogZnVuY3Rpb24oIG9wdGlvbnMgKSB7XG5cdFx0dGhpcy5jb250cm9sbGVyID0gb3B0aW9ucy5jb250cm9sbGVyO1xuXHRcdHRoaXMubW9kZWwgPSBvcHRpb25zLm1vZGVsO1xuXHRcdHRoaXMuc2VsZWN0aW9uID0gdGhpcy5jb250cm9sbGVyLnN0YXRlKCkuZ2V0KCAnc2VsZWN0aW9uJyApO1xuXG5cdFx0dGhpcy5saXN0ZW5UbyggdGhpcy5zZWxlY3Rpb24sICdhZGQgcmVtb3ZlIHJlc2V0JywgdGhpcy51cGRhdGVTZWxlY3RlZENsYXNzICk7XG5cdH0sXG5cblx0cmVuZGVyOiBmdW5jdGlvbigpIHtcblx0XHR0aGlzLiRlbC5odG1sKCB0aGlzLnRlbXBsYXRlKCB0aGlzLm1vZGVsLnRvSlNPTigpICkgKTtcblx0XHRyZXR1cm4gdGhpcztcblx0fSxcblxuXHRyZXNldFNlbGVjdGlvbjogZnVuY3Rpb24oIGUgKSB7XG5cdFx0aWYgKCB0aGlzLnNlbGVjdGlvbi5jb250YWlucyggdGhpcy5tb2RlbCApICkge1xuXHRcdFx0dGhpcy5zZWxlY3Rpb24ucmVtb3ZlKCB0aGlzLm1vZGVsICk7XG5cdFx0fSBlbHNlIHtcblx0XHRcdHRoaXMuc2VsZWN0aW9uLnJlc2V0KCB0aGlzLm1vZGVsICk7XG5cdFx0fVxuXHR9LFxuXG5cdHVwZGF0ZVNlbGVjdGVkQ2xhc3M6IGZ1bmN0aW9uKCkge1xuXHRcdGlmICggdGhpcy5zZWxlY3Rpb24uY29udGFpbnMoIHRoaXMubW9kZWwgKSApIHtcblx0XHRcdHRoaXMuJGVsLmFkZENsYXNzKCAnaXMtc2VsZWN0ZWQnICk7XG5cdFx0fSBlbHNlIHtcblx0XHRcdHRoaXMuJGVsLnJlbW92ZUNsYXNzKCAnaXMtc2VsZWN0ZWQnICk7XG5cdFx0fVxuXHR9XG59KTtcblxubW9kdWxlLmV4cG9ydHMgPSBWaWRlb3M7XG4iLCJ2YXIgVmlkZW9zSXRlbXMsXG5cdFZpZGVvc0l0ZW0gPSByZXF1aXJlKCAnLi4vdmlkZW9zL2l0ZW0nICksXG5cdHdwID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ3dwJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWyd3cCddIDogbnVsbCk7XG5cblZpZGVvc0l0ZW1zID0gd3AuQmFja2JvbmUuVmlldy5leHRlbmQoe1xuXHRjbGFzc05hbWU6ICd2aWRlby1jZW50cmFsLXZpZGVvcy1icm93c2VyLWxpc3QnLFxuXHR0YWdOYW1lOiAndWwnLFxuXG5cdGluaXRpYWxpemU6IGZ1bmN0aW9uKCBvcHRpb25zICkge1xuXHRcdHRoaXMuY29sbGVjdGlvbiA9IG9wdGlvbnMuY29udHJvbGxlci5zdGF0ZSgpLmdldCggJ2NvbGxlY3Rpb24nICk7XG5cdFx0dGhpcy5jb250cm9sbGVyID0gb3B0aW9ucy5jb250cm9sbGVyO1xuXG5cdFx0dGhpcy5saXN0ZW5UbyggdGhpcy5jb2xsZWN0aW9uLCAnYWRkJywgdGhpcy5hZGRJdGVtICk7XG5cdFx0dGhpcy5saXN0ZW5UbyggdGhpcy5jb2xsZWN0aW9uLCAncmVzZXQnLCB0aGlzLnJlbmRlciApO1xuXHR9LFxuXG5cdHJlbmRlcjogZnVuY3Rpb24oKSB7XG5cdFx0dGhpcy5jb2xsZWN0aW9uLmVhY2goIHRoaXMuYWRkSXRlbSwgdGhpcyApO1xuXHRcdHJldHVybiB0aGlzO1xuXHR9LFxuXG5cdGFkZEl0ZW06IGZ1bmN0aW9uKCBtb2RlbCApIHtcblx0XHR2YXIgdmlldyA9IG5ldyBWaWRlb3NJdGVtKHtcblx0XHRcdGNvbnRyb2xsZXI6IHRoaXMuY29udHJvbGxlcixcblx0XHRcdG1vZGVsOiBtb2RlbFxuXHRcdH0pLnJlbmRlcigpO1xuXG5cdFx0dGhpcy4kZWwuYXBwZW5kKCB2aWV3LmVsICk7XG5cdH1cbn0pO1xuXG5tb2R1bGUuZXhwb3J0cyA9IFZpZGVvc0l0ZW1zO1xuIiwidmFyIFZpZGVvc05vSXRlbXMsXG5cdHdwID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ3dwJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWyd3cCddIDogbnVsbCk7XG5cblZpZGVvc05vSXRlbXMgPSB3cC5CYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG5cdGNsYXNzTmFtZTogJ3ZpZGVvLWNlbnRyYWwtdmlkZW9zLWJyb3dzZXItZW1wdHknLFxuXHR0YWdOYW1lOiAnZGl2Jyxcblx0dGVtcGxhdGU6IHdwLnRlbXBsYXRlKCAndmlkZW8tY2VudHJhbC12aWRlb3MtYnJvd3Nlci1lbXB0eScgKSxcblxuXHRpbml0aWFsaXplOiBmdW5jdGlvbiggb3B0aW9ucyApIHtcblx0XHR0aGlzLmNvbGxlY3Rpb24gPSB0aGlzLmNvbGxlY3Rpb247XG5cblx0XHR0aGlzLmxpc3RlblRvKCB0aGlzLmNvbGxlY3Rpb24sICdhZGQgcmVtb3ZlIHJlc2V0JywgdGhpcy50b2dnbGVWaXNpYmlsaXR5ICk7XG5cdH0sXG5cblx0cmVuZGVyOiBmdW5jdGlvbigpIHtcblx0XHR0aGlzLiRlbC5odG1sKCB0aGlzLnRlbXBsYXRlKCkgKTtcblx0XHRyZXR1cm4gdGhpcztcblx0fSxcblxuXHR0b2dnbGVWaXNpYmlsaXR5OiBmdW5jdGlvbigpIHtcblx0XHR0aGlzLiRlbC50b2dnbGVDbGFzcyggJ2lzLXZpc2libGUnLCB0aGlzLmNvbGxlY3Rpb24ubGVuZ3RoIDwgMSApO1xuXHR9XG59KTtcblxubW9kdWxlLmV4cG9ydHMgPSBWaWRlb3NOb0l0ZW1zO1xuIiwidmFyIFZpZGVvc1NpZGViYXIsXG5cdCQgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snalF1ZXJ5J10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWydqUXVlcnknXSA6IG51bGwpLFxuXHR3cCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93Wyd3cCddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnd3AnXSA6IG51bGwpO1xuXG5WaWRlb3NTaWRlYmFyID0gd3AuQmFja2JvbmUuVmlldy5leHRlbmQoe1xuXHRjbGFzc05hbWU6ICd2aWRlby1jZW50cmFsLXZpZGVvcy1icm93c2VyLXNpZGViYXIgbWVkaWEtc2lkZWJhcicsXG5cdHRlbXBsYXRlOiB3cC50ZW1wbGF0ZSggJ3ZpZGVvLWNlbnRyYWwtdmlkZW9zLWJyb3dzZXItc2lkZWJhcicgKSxcblxuXHRldmVudHM6IHtcblx0XHQnY2hhbmdlIFtkYXRhLXNldHRpbmddJzogJ3VwZGF0ZUF0dHJpYnV0ZSdcblx0fSxcblxuXHRpbml0aWFsaXplOiBmdW5jdGlvbiggb3B0aW9ucyApIHtcblx0XHR0aGlzLmF0dHJpYnV0ZXMgPSBvcHRpb25zLmNvbnRyb2xsZXIuc3RhdGUoKS5nZXQoICdhdHRyaWJ1dGVzJyApO1xuXHR9LFxuXG5cdHJlbmRlcjogZnVuY3Rpb24oKSB7XG5cdFx0dGhpcy4kZWwuaHRtbCggdGhpcy50ZW1wbGF0ZSgpICk7XG5cdH0sXG5cblx0dXBkYXRlQXR0cmlidXRlOiBmdW5jdGlvbiggZSApIHtcblx0XHR2YXIgJHRhcmdldCA9ICQoIGUudGFyZ2V0ICksXG5cdFx0XHRhdHRyaWJ1dGUgPSAkdGFyZ2V0LmRhdGEoICdzZXR0aW5nJyApLFxuXHRcdFx0dmFsdWUgPSBlLnRhcmdldC52YWx1ZTtcblxuXHRcdGlmICggJ2NoZWNrYm94JyA9PT0gZS50YXJnZXQudHlwZSApIHtcblx0XHRcdHZhbHVlID0gISEgJHRhcmdldC5wcm9wKCAnY2hlY2tlZCcgKTtcblx0XHR9XG5cblx0XHR0aGlzLmF0dHJpYnV0ZXMuc2V0KCBhdHRyaWJ1dGUsIHZhbHVlICk7XG5cdH1cbn0pO1xuXG5tb2R1bGUuZXhwb3J0cyA9IFZpZGVvc1NpZGViYXI7XG4iLCJ2YXIgV29ya2Zsb3dzLFxuXHRfID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ18nXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ18nXSA6IG51bGwpLFxuXHR2aWRlb19jZW50cmFsID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ3ZpZGVvX2NlbnRyYWwnXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ3ZpZGVvX2NlbnRyYWwnXSA6IG51bGwpLFxuXHRsMTBuID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ3ZpZGVvX2NlbnRyYWwnXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ3ZpZGVvX2NlbnRyYWwnXSA6IG51bGwpLmwxMG4sXG4gICAgTWVkaWFGcmFtZSA9IHJlcXVpcmUoICcuL3ZpZXdzL2ZyYW1lL2luc2VydC12aWRlb3MnICksXG5cdHdwID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ3dwJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWyd3cCddIDogbnVsbCksXG5cdEF0dGFjaG1lbnQgPSB3cC5tZWRpYS5tb2RlbC5BdHRhY2htZW50O1xuXG5Xb3JrZmxvd3MgPSB7XG5cdGZyYW1lczogW10sXG5cdG1vZGVsOiB7fSxcblxuXHQvKipcblx0ICogU2V0IGEgbW9kZWwgZm9yIHRoZSBjdXJyZW50IHdvcmtmbG93LlxuXHQgKlxuXHQgKiBAcGFyYW0ge09iamVjdH0gZnJhbWVcblx0ICovXG5cdHNldE1vZGVsOiBmdW5jdGlvbiggbW9kZWwgKSB7XG5cdFx0dGhpcy5tb2RlbCA9IG1vZGVsO1xuXHRcdHJldHVybiB0aGlzO1xuXHR9LFxuXG5cdC8qKlxuXHQgKiBSZXRyaWV2ZSBvciBjcmVhdGUgYSBmcmFtZSBpbnN0YW5jZSBmb3IgYSBwYXJ0aWN1bGFyIHdvcmtmbG93LlxuXHQgKlxuXHQgKiBAcGFyYW0ge3N0cmluZ30gaWQgRnJhbWUgaWRlbnRpZmVyLlxuXHQgKi9cblx0Z2V0OiBmdW5jdGlvbiggaWQgKSAge1xuXHRcdHZhciBtZXRob2QgPSAnXycgKyBpZCxcblx0XHRcdGZyYW1lID0gdGhpcy5mcmFtZXNbIG1ldGhvZCBdIHx8IG51bGw7XG5cblx0XHQvLyBBbHdheXMgY2FsbCB0aGUgZnJhbWUgbWV0aG9kIHRvIHBlcmZvcm0gYW55IHJvdXRpbmUgc2V0IHVwLiBUaGVcblx0XHQvLyBmcmFtZSBtZXRob2Qgc2hvdWxkIHNob3J0LWNpcmN1aXQgYmVmb3JlIGJlaW5nIGluaXRpYWxpemVkIGFnYWluLlxuXHRcdGZyYW1lID0gdGhpc1sgbWV0aG9kIF0uY2FsbCggdGhpcywgZnJhbWUgKTtcblxuXHRcdC8vIFN0b3JlIHRoZSBmcmFtZSBmb3IgZnV0dXJlIHVzZS5cblx0XHR0aGlzLmZyYW1lc1sgbWV0aG9kIF0gPSBmcmFtZTtcblxuXHRcdHJldHVybiBmcmFtZTtcblx0fSxcblxuXHQvKipcblx0ICogV29ya2Zsb3cgZm9yIGFkZGluZyB2aWRlb3MgdG8gdGhlIHBsYXlsaXN0LlxuXHQgKlxuXHQgKiBAcGFyYW0ge09iamVjdH0gZnJhbWVcblx0ICovXG5cdF9hZGRWaWRlb3M6IGZ1bmN0aW9uKCBmcmFtZSApIHtcblx0XHQvLyBSZXR1cm4gdGhlIGV4aXN0aW5nIGZyYW1lIGZvciB0aGlzIHdvcmtmbG93LlxuXHRcdGlmICggZnJhbWUgKSB7XG5cdFx0XHRyZXR1cm4gZnJhbWU7XG5cdFx0fVxuXG5cdFx0Ly8gSW5pdGlhbGl6ZSB0aGUgYXVkaW8gZnJhbWUuXG4gICAgICAgIGZyYW1lID0gbmV3IE1lZGlhRnJhbWUoKTtcbiAgICAgICAgXG4gICAgICAgIGNvbnNvbGUubG9nKCBmcmFtZS5zdGF0ZSggJ3ZpZGVvLWNlbnRyYWwtcGxheWxpc3QtdmlkZW9zJyApICk7XG5cblx0XHQvLyBJbnNlcnQgZWFjaCBzZWxlY3RlZCBhdHRhY2htZW50IGFzIGEgbmV3IHZpZGVvIG1vZGVsLlxuXHRcdGZyYW1lLnN0YXRlKCAndmlkZW8tY2VudHJhbC1wbGF5bGlzdC12aWRlb3MnICkub24oICdpbnNlcnQnLCBmdW5jdGlvbiggc2VsZWN0aW9uICkge1xuICAgICAgICAgICAgY29uc29sZS5sb2coc2VsZWN0aW9uKTtcblx0XHRcdF8uZWFjaCggc2VsZWN0aW9uLm1vZGVscywgZnVuY3Rpb24oIGF0dGFjaG1lbnQgKSB7XG4gICAgICAgICAgICAgICAgdmlkZW9fY2VudHJhbC52aWRlb3MucHVzaCggYXR0YWNobWVudC50b0pTT04oKSApO1xuICAgICAgICAgICAgfSk7XG4gICAgICAgIH0pO1xuXG5cdFx0cmV0dXJuIGZyYW1lO1xuXHR9LFxuXG5cdC8qKlxuXHQgKiBXb3JrZmxvdyBmb3Igc2VsZWN0aW5nIHZpZGVvIGFydHdvcmsgaW1hZ2UuXG5cdCAqXG5cdCAqIEBwYXJhbSB7T2JqZWN0fSBmcmFtZVxuXHQgKi9cblx0X3NlbGVjdEFydHdvcms6IGZ1bmN0aW9uKCBmcmFtZSApIHtcblx0XHR2YXIgd29ya2Zsb3cgPSB0aGlzO1xuXG5cdFx0Ly8gUmV0dXJuIGV4aXN0aW5nIGZyYW1lIGZvciB0aGlzIHdvcmtmbG93LlxuXHRcdGlmICggZnJhbWUgKSB7XG5cdFx0XHRyZXR1cm4gZnJhbWU7XG5cdFx0fVxuXG5cdFx0Ly8gSW5pdGlhbGl6ZSB0aGUgYXJ0d29yayBmcmFtZS5cblx0XHRmcmFtZSA9IHdwLm1lZGlhKHtcblx0XHRcdHRpdGxlOiBsMTBuLndvcmtmbG93cy5zZWxlY3RBcnR3b3JrLmZyYW1lVGl0bGUsXG5cdFx0XHRsaWJyYXJ5OiB7XG5cdFx0XHRcdHR5cGU6ICdpbWFnZSdcblx0XHRcdH0sXG5cdFx0XHRidXR0b246IHtcblx0XHRcdFx0dGV4dDogbDEwbi53b3JrZmxvd3Muc2VsZWN0QXJ0d29yay5mcmFtZUJ1dHRvblRleHRcblx0XHRcdH0sXG5cdFx0XHRtdWx0aXBsZTogZmFsc2Vcblx0XHR9KTtcblxuXHRcdC8vIFNldCB0aGUgZXh0ZW5zaW9ucyB0aGF0IGNhbiBiZSB1cGxvYWRlZC5cblx0XHRmcmFtZS51cGxvYWRlci5vcHRpb25zLnVwbG9hZGVyLnBsdXBsb2FkID0ge1xuXHRcdFx0ZmlsdGVyczoge1xuXHRcdFx0XHRtaW1lX3R5cGVzOiBbe1xuXHRcdFx0XHRcdGZpbGVzOiBsMTBuLndvcmtmbG93cy5zZWxlY3RBcnR3b3JrLmZpbGVUeXBlcyxcblx0XHRcdFx0XHRleHRlbnNpb25zOiAnanBnLGpwZWcsZ2lmLHBuZydcblx0XHRcdFx0fV1cblx0XHRcdH1cblx0XHR9O1xuXG5cdFx0Ly8gQXV0b21hdGljYWxseSBzZWxlY3QgdGhlIGV4aXN0aW5nIGFydHdvcmsgaWYgcG9zc2libGUuXG5cdFx0ZnJhbWUub24oICdvcGVuJywgZnVuY3Rpb24oKSB7XG5cdFx0XHR2YXIgc2VsZWN0aW9uID0gdGhpcy5nZXQoICdsaWJyYXJ5JyApLmdldCggJ3NlbGVjdGlvbicgKSxcblx0XHRcdFx0YXJ0d29ya0lkID0gd29ya2Zsb3cubW9kZWwuZ2V0KCAnYXJ0d29ya0lkJyApLFxuXHRcdFx0XHRhdHRhY2htZW50cyA9IFtdO1xuXG5cdFx0XHRpZiAoIGFydHdvcmtJZCApIHtcblx0XHRcdFx0YXR0YWNobWVudHMucHVzaCggQXR0YWNobWVudC5nZXQoIGFydHdvcmtJZCApICk7XG5cdFx0XHRcdGF0dGFjaG1lbnRzWzBdLmZldGNoKCk7XG5cdFx0XHR9XG5cblx0XHRcdHNlbGVjdGlvbi5yZXNldCggYXR0YWNobWVudHMgKTtcblx0XHR9KTtcblxuXHRcdC8vIFNldCB0aGUgbW9kZWwncyBhcnR3b3JrIElEIGFuZCB1cmwgcHJvcGVydGllcy5cblx0XHRmcmFtZS5zdGF0ZSggJ2xpYnJhcnknICkub24oICdzZWxlY3QnLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgIHZhciBhdHRhY2htZW50ID0gdGhpcy5nZXQoICdzZWxlY3Rpb24nICkuZmlyc3QoKS50b0pTT04oKTtcblxuXHRcdFx0d29ya2Zsb3cubW9kZWwuc2V0KHtcblx0XHRcdFx0YXJ0d29ya0lkOiBhdHRhY2htZW50LmlkLFxuXHRcdFx0XHRhcnR3b3JrVXJsOiBhdHRhY2htZW50LnVybFxuXHRcdFx0fSk7XG5cdFx0fSk7XG5cblx0XHRyZXR1cm4gZnJhbWU7XG4gICAgfVxuXG59O1xuXG5tb2R1bGUuZXhwb3J0cyA9IFdvcmtmbG93cztcbiJdfQ==
