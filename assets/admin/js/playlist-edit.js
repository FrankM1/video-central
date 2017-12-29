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
    PostFrame = wp.media.view.MediaFrame;

InsertVideosFrame = PostFrame.extend({

    initialize: function() {
		_.extend( this.options, {
			uploader: false
		});

		PostFrame.prototype.initialize.apply( this, arguments );

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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJhc3NldHMvYWRtaW4vanMvc291cmNlL3BsYXlsaXN0L2NvbGxlY3Rpb25zL3ZpZGVvcy5qcyIsImFzc2V0cy9hZG1pbi9qcy9zb3VyY2UvcGxheWxpc3QvY29udHJvbGxlcnMvdmlkZW9zLmpzIiwiYXNzZXRzL2FkbWluL2pzL3NvdXJjZS9wbGF5bGlzdC9tb2RlbHMvdmlkZW8uanMiLCJhc3NldHMvYWRtaW4vanMvc291cmNlL3BsYXlsaXN0L3BsYXlsaXN0LWVkaXQuanMiLCJhc3NldHMvYWRtaW4vanMvc291cmNlL3BsYXlsaXN0L3ZpZXdzL2J1dHRvbi9hZGQtdmlkZW9zLmpzIiwiYXNzZXRzL2FkbWluL2pzL3NvdXJjZS9wbGF5bGlzdC92aWV3cy9jb250ZW50L3ZpZGVvcy1icm93c2VyLmpzIiwiYXNzZXRzL2FkbWluL2pzL3NvdXJjZS9wbGF5bGlzdC92aWV3cy9mcmFtZS9pbnNlcnQtdmlkZW9zLmpzIiwiYXNzZXRzL2FkbWluL2pzL3NvdXJjZS9wbGF5bGlzdC92aWV3cy9wb3N0LWZvcm0uanMiLCJhc3NldHMvYWRtaW4vanMvc291cmNlL3BsYXlsaXN0L3ZpZXdzL3Rvb2xiYXIvdmlkZW9zLmpzIiwiYXNzZXRzL2FkbWluL2pzL3NvdXJjZS9wbGF5bGlzdC92aWV3cy92aWRlby1saXN0LmpzIiwiYXNzZXRzL2FkbWluL2pzL3NvdXJjZS9wbGF5bGlzdC92aWV3cy92aWRlby5qcyIsImFzc2V0cy9hZG1pbi9qcy9zb3VyY2UvcGxheWxpc3Qvdmlld3MvdmlkZW8vYXJ0d29yay5qcyIsImFzc2V0cy9hZG1pbi9qcy9zb3VyY2UvcGxheWxpc3Qvdmlld3MvdmlkZW8vYXVkaW8uanMiLCJhc3NldHMvYWRtaW4vanMvc291cmNlL3BsYXlsaXN0L3ZpZXdzL3ZpZGVvcy9pdGVtLmpzIiwiYXNzZXRzL2FkbWluL2pzL3NvdXJjZS9wbGF5bGlzdC92aWV3cy92aWRlb3MvaXRlbXMuanMiLCJhc3NldHMvYWRtaW4vanMvc291cmNlL3BsYXlsaXN0L3ZpZXdzL3ZpZGVvcy9uby1pdGVtcy5qcyIsImFzc2V0cy9hZG1pbi9qcy9zb3VyY2UvcGxheWxpc3Qvdmlld3MvdmlkZW9zL3NpZGViYXIuanMiLCJhc3NldHMvYWRtaW4vanMvc291cmNlL3BsYXlsaXN0L3dvcmtmbG93cy5qcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTs7QUNBQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7O0FDdENBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7Ozs7QUN0Q0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7O0FDbEJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7O0FDbENBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7O0FDbENBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7O0FDdEVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOzs7OztBQzdDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7O0FDckRBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7O0FDM0NBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7Ozs7QUNyREE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7Ozs7QUNoSEE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7Ozs7QUMvQkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOzs7OztBQ3ZFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOzs7OztBQzNDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7O0FDaENBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7O0FDekJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7O0FDbENBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBIiwiZmlsZSI6ImdlbmVyYXRlZC5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzQ29udGVudCI6WyIoZnVuY3Rpb24gZSh0LG4scil7ZnVuY3Rpb24gcyhvLHUpe2lmKCFuW29dKXtpZighdFtvXSl7dmFyIGE9dHlwZW9mIHJlcXVpcmU9PVwiZnVuY3Rpb25cIiYmcmVxdWlyZTtpZighdSYmYSlyZXR1cm4gYShvLCEwKTtpZihpKXJldHVybiBpKG8sITApO3ZhciBmPW5ldyBFcnJvcihcIkNhbm5vdCBmaW5kIG1vZHVsZSAnXCIrbytcIidcIik7dGhyb3cgZi5jb2RlPVwiTU9EVUxFX05PVF9GT1VORFwiLGZ9dmFyIGw9bltvXT17ZXhwb3J0czp7fX07dFtvXVswXS5jYWxsKGwuZXhwb3J0cyxmdW5jdGlvbihlKXt2YXIgbj10W29dWzFdW2VdO3JldHVybiBzKG4/bjplKX0sbCxsLmV4cG9ydHMsZSx0LG4scil9cmV0dXJuIG5bb10uZXhwb3J0c312YXIgaT10eXBlb2YgcmVxdWlyZT09XCJmdW5jdGlvblwiJiZyZXF1aXJlO2Zvcih2YXIgbz0wO288ci5sZW5ndGg7bysrKXMocltvXSk7cmV0dXJuIHN9KSIsInZhciBWaWRlb3MsXG5cdF8gPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snXyddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnXyddIDogbnVsbCksXG5cdEJhY2tib25lID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ0JhY2tib25lJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWydCYWNrYm9uZSddIDogbnVsbCksXG5cdHNldHRpbmdzID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ3ZpZGVvX2NlbnRyYWwnXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ3ZpZGVvX2NlbnRyYWwnXSA6IG51bGwpLnNldHRpbmdzKCksXG5cdFZpZGVvID0gcmVxdWlyZSggJy4uL21vZGVscy92aWRlbycgKSxcblx0d3AgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snd3AnXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ3dwJ10gOiBudWxsKTtcblxuVmlkZW9zID0gQmFja2JvbmUuQ29sbGVjdGlvbi5leHRlbmQoe1xuXHRtb2RlbDogVmlkZW8sXG5cblx0Y29tcGFyYXRvcjogZnVuY3Rpb24oIHZpZGVvICkge1xuXHRcdHJldHVybiBwYXJzZUludCggdmlkZW8uZ2V0KCAnb3JkZXInICksIDEwICk7XG5cdH0sXG5cblx0ZmV0Y2g6IGZ1bmN0aW9uKCkge1xuXHRcdHZhciBjb2xsZWN0aW9uID0gdGhpcztcblxuXHRcdHJldHVybiB3cC5hamF4LnBvc3QoICd2aWRlb19jZW50cmFsX2dldF9wbGF5bGlzdF92aWRlb3MnLCB7XG5cdFx0XHRwb3N0X2lkOiBzZXR0aW5ncy5wb3N0SWRcblx0XHR9KS5kb25lKGZ1bmN0aW9uKCB2aWRlb3MgKSB7XG5cdFx0XHRjb2xsZWN0aW9uLnJlc2V0KCB2aWRlb3MgKTtcblx0XHR9KTtcblx0fSxcblxuXHRzYXZlOiBmdW5jdGlvbiggZGF0YSApIHtcblx0XHR0aGlzLnNvcnQoKTtcblxuXHRcdGRhdGEgPSBfLmV4dGVuZCh7fSwgZGF0YSwge1xuXHRcdFx0cG9zdF9pZDogc2V0dGluZ3MucG9zdElkLFxuXHRcdFx0dmlkZW9zOiB0aGlzLnRvSlNPTigpLFxuXHRcdFx0bm9uY2U6IHNldHRpbmdzLnNhdmVOb25jZVxuXHRcdH0pO1xuXG5cdFx0cmV0dXJuIHdwLmFqYXgucG9zdCggJ3ZpZGVvX2NlbnRyYWxfc2F2ZV9wbGF5bGlzdF92aWRlb3MnLCBkYXRhICk7XG5cdH1cbn0pO1xuXG5tb2R1bGUuZXhwb3J0cyA9IFZpZGVvcztcbiIsInZhciBWaWRlb3MsXG5cdEJhY2tib25lID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ0JhY2tib25lJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWydCYWNrYm9uZSddIDogbnVsbCksXG5cdGwxMG4gPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1sndmlkZW9fY2VudHJhbCddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsndmlkZW9fY2VudHJhbCddIDogbnVsbCkubDEwbixcblx0d3AgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snd3AnXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ3dwJ10gOiBudWxsKTtcbiAgICBcblZpZGVvcyA9IHdwLm1lZGlhLmNvbnRyb2xsZXIuU3RhdGUuZXh0ZW5kKHtcblx0ZGVmYXVsdHM6IHtcblx0XHRpZCAgICAgICAgOiAndmlkZW8tY2VudHJhbC1wbGF5bGlzdC12aWRlb3MnLFxuXHRcdHRpdGxlICAgICA6IGwxMG4uaW5zZXJ0VmlkZW9zIHx8ICdJbnNlcnQgVmlkZW9zJyxcbiAgICAgICAgY29sbGVjdGlvbjogbnVsbCxcbiAgICAgICAgc2VsZWN0aW9uOiBudWxsLFxuXHRcdGNvbnRlbnQgICA6ICd2aWRlby1jZW50cmFsLXZpZGVvcy1icm93c2VyJyxcblx0XHRtZW51ICAgICAgOiAnZGVmYXVsdCcsXG5cdFx0bWVudUl0ZW0gIDoge1xuXHRcdFx0dGV4dCAgICA6IGwxMG4uaW5zZXJ0RnJvbVZpZGVvQ2VudHJhbCB8fCAnSW5zZXJ0IGZyb20gVmlkZW8gQ2VudHJhbCcsXG5cdFx0XHRwcmlvcml0eTogMVxuICAgICAgICB9LFxuICAgICAgICBtdWx0aXBsZSA6IHRydWUsXG4gICAgICAgIHRvb2xiYXIgIDogJ3ZpZGVvLWNlbnRyYWwtcGxheWxpc3QtaW5zZXJ0LXZpZGVvcydcblx0fSxcblxuXHRpbml0aWFsaXplOiBmdW5jdGlvbiggb3B0aW9ucyApIHtcblx0XHR2YXIgY29sbGVjdGlvbiA9IG9wdGlvbnMuY29sbGVjdGlvbiB8fCBuZXcgQmFja2JvbmUuQ29sbGVjdGlvbigpLFxuXHRcdFx0c2VsZWN0aW9uID0gb3B0aW9ucy5zZWxlY3Rpb24gfHwgbmV3IEJhY2tib25lLkNvbGxlY3Rpb24oKTtcbiAgIFxuXHRcdHRoaXMuc2V0KCAnYXR0cmlidXRlcycsIG5ldyBCYWNrYm9uZS5Nb2RlbCh7XG5cdFx0XHRpZDogbnVsbCxcblx0XHRcdHNob3dfdmlkZW9zOiB0cnVlXG5cdFx0fSkgKTtcblxuXHRcdHRoaXMuc2V0KCAnY29sbGVjdGlvbicsIGNvbGxlY3Rpb24gKTtcblx0XHR0aGlzLnNldCggJ3NlbGVjdGlvbicsIHNlbGVjdGlvbiApO1xuXG5cdFx0dGhpcy5saXN0ZW5Ubyggc2VsZWN0aW9uLCAncmVtb3ZlJywgdGhpcy51cGRhdGVTZWxlY3Rpb24gKTtcblx0fVxufSk7XG5cbm1vZHVsZS5leHBvcnRzID0gVmlkZW9zO1xuIiwidmFyIFZpZGVvLFxuXHRCYWNrYm9uZSA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93WydCYWNrYm9uZSddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnQmFja2JvbmUnXSA6IG51bGwpO1xuXG5WaWRlbyA9IEJhY2tib25lLk1vZGVsLmV4dGVuZCh7XG5cdGRlZmF1bHRzOiB7XG5cdFx0YXJ0aXN0OiAnJyxcblx0XHRhcnR3b3JrSWQ6ICcnLFxuXHRcdGFydHdvcmtVcmw6ICcnLFxuXHRcdHZpZGVvSWQ6ICcnLFxuXHRcdGF1ZGlvVXJsOiAnJyxcblx0XHRmb3JtYXQ6ICcnLFxuXHRcdGxlbmd0aDogJycsXG5cdFx0dGl0bGU6ICcnLFxuXHRcdG9yZGVyOiAwXG5cdH1cbn0pO1xuXG5tb2R1bGUuZXhwb3J0cyA9IFZpZGVvO1xuIiwidmFyIHZpZGVvX2NlbnRyYWwgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1sndmlkZW9fY2VudHJhbCddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsndmlkZW9fY2VudHJhbCddIDogbnVsbCk7XG52YXIgd3AgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snd3AnXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ3dwJ10gOiBudWxsKTtcblxudmlkZW9fY2VudHJhbC5kYXRhID0gdmlkZW9DZW50cmFsUGxheWxpc3RDb25maWc7XG52aWRlb19jZW50cmFsLnNldHRpbmdzKCB2aWRlb0NlbnRyYWxQbGF5bGlzdENvbmZpZyApO1xuXG53cC5tZWRpYS52aWV3LnNldHRpbmdzLnBvc3QuaWQgPSB2aWRlb19jZW50cmFsLmRhdGEucG9zdElkO1xud3AubWVkaWEudmlldy5zZXR0aW5ncy5kZWZhdWx0UHJvcHMgPSB7fTtcblxudmlkZW9fY2VudHJhbC5tb2RlbC5WaWRlbyA9IHJlcXVpcmUoICcuL21vZGVscy92aWRlbycgKTtcbnZpZGVvX2NlbnRyYWwubW9kZWwuVmlkZW9zID0gcmVxdWlyZSggJy4vY29sbGVjdGlvbnMvdmlkZW9zJyApO1xuXG52aWRlb19jZW50cmFsLnZpZXcuUG9zdEZvcm0gPSByZXF1aXJlKCAnLi92aWV3cy9wb3N0LWZvcm0nICk7XG52aWRlb19jZW50cmFsLnZpZXcuQWRkVmlkZW9zQnV0dG9uID0gcmVxdWlyZSggJy4vdmlld3MvYnV0dG9uL2FkZC12aWRlb3MnICk7XG52aWRlb19jZW50cmFsLnZpZXcuVmlkZW9MaXN0ID0gcmVxdWlyZSggJy4vdmlld3MvdmlkZW8tbGlzdCcgKTtcbnZpZGVvX2NlbnRyYWwudmlldy5WaWRlbyA9IHJlcXVpcmUoICcuL3ZpZXdzL3ZpZGVvJyApO1xudmlkZW9fY2VudHJhbC52aWV3LlZpZGVvQXJ0d29yayA9IHJlcXVpcmUoICcuL3ZpZXdzL3ZpZGVvL2FydHdvcmsnICk7XG52aWRlb19jZW50cmFsLnZpZXcuVmlkZW9BdWRpbyA9IHJlcXVpcmUoICcuL3ZpZXdzL3ZpZGVvL2F1ZGlvJyApO1xuXG52aWRlb19jZW50cmFsLndvcmtmbG93cyA9IHJlcXVpcmUoICcuL3dvcmtmbG93cycgKTtcblxuKCBmdW5jdGlvbiggJCApIHtcbiAgICB2YXIgdmlkZW9zO1xuXG5cdHZpZGVvcyA9IHZpZGVvX2NlbnRyYWwudmlkZW9zID0gbmV3IHZpZGVvX2NlbnRyYWwubW9kZWwuVmlkZW9zKCB2aWRlb19jZW50cmFsLmRhdGEudmlkZW9zICk7XG5cdGRlbGV0ZSB2aWRlb19jZW50cmFsLmRhdGEudmlkZW9zO1xuXG5cdHZhciBwb3N0Rm9ybSA9IG5ldyB2aWRlb19jZW50cmFsLnZpZXcuUG9zdEZvcm0oe1xuXHRcdGNvbGxlY3Rpb246IHZpZGVvcyxcblx0XHRsMTBuOiB2aWRlb19jZW50cmFsLmwxMG5cbiAgICB9KTtcbiAgICBcbn0gKCBqUXVlcnkgKSk7XG5cbiIsInZhciBBZGRWaWRlb3NCdXR0b24sXG5cdCQgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snalF1ZXJ5J10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWydqUXVlcnknXSA6IG51bGwpLFxuXHR3b3JrZmxvd3MgPSByZXF1aXJlKCAnLi4vLi4vd29ya2Zsb3dzJyApLFxuXHR3cCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93Wyd3cCddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnd3AnXSA6IG51bGwpO1xuXG5BZGRWaWRlb3NCdXR0b24gPSB3cC5CYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG5cdGlkOiAnYWRkLXZpZGVvcycsXG5cdHRhZ05hbWU6ICdwJyxcblxuXHRldmVudHM6IHtcblx0XHQnY2xpY2sgLmJ1dHRvbic6ICdjbGljaydcblx0fSxcblxuXHRpbml0aWFsaXplOiBmdW5jdGlvbiggb3B0aW9ucyApIHtcblx0XHR0aGlzLmwxMG4gPSBvcHRpb25zLmwxMG47XG5cdH0sXG5cblx0cmVuZGVyOiBmdW5jdGlvbigpIHtcblx0XHR2YXIgJGJ1dHRvbiA9ICQoICc8YSAvPicsIHtcblx0XHRcdHRleHQ6IHRoaXMubDEwbi5hZGRWaWRlb3Ncblx0XHR9KS5hZGRDbGFzcyggJ2J1dHRvbiBidXR0b24tc2Vjb25kYXJ5JyApO1xuXG5cdFx0dGhpcy4kZWwuaHRtbCggJGJ1dHRvbiApO1xuXG5cdFx0cmV0dXJuIHRoaXM7XG5cdH0sXG5cblx0Y2xpY2s6IGZ1bmN0aW9uKCBlICkge1xuXHRcdGUucHJldmVudERlZmF1bHQoKTtcblx0XHR3b3JrZmxvd3MuZ2V0KCAnYWRkVmlkZW9zJyApLm9wZW4oKTtcblx0fVxufSk7XG5cbm1vZHVsZS5leHBvcnRzID0gQWRkVmlkZW9zQnV0dG9uO1xuIiwidmFyIFZpZGVvc0Jyb3dzZXIsXG5cdF8gPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snXyddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnXyddIDogbnVsbCksXG5cdFZpZGVvc0l0ZW1zID0gcmVxdWlyZSggJy4uL3ZpZGVvcy9pdGVtcycgKSxcblx0VmlkZW9zTm9JdGVtcyA9IHJlcXVpcmUoICcuLi92aWRlb3Mvbm8taXRlbXMnICksXG5cdFZpZGVvc1NpZGViYXIgPSByZXF1aXJlKCAnLi4vdmlkZW9zL3NpZGViYXInICksXG5cdHdwID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ3dwJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWyd3cCddIDogbnVsbCk7XG5cblZpZGVvc0Jyb3dzZXIgPSB3cC5CYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG5cdGNsYXNzTmFtZTogJ3ZpZGVvLWNlbnRyYWwtdmlkZW9zLWJyb3dzZXInLFxuXG5cdGluaXRpYWxpemU6IGZ1bmN0aW9uKCBvcHRpb25zICkge1xuXHRcdHRoaXMuY29sbGVjdGlvbiA9IG9wdGlvbnMuY29udHJvbGxlci5zdGF0ZSgpLmdldCggJ2NvbGxlY3Rpb24nICk7XG5cdFx0dGhpcy5jb250cm9sbGVyID0gb3B0aW9ucy5jb250cm9sbGVyO1xuXG5cdFx0dGhpcy5fcGFnZWQgPSAxO1xuXHRcdHRoaXMuX3BlbmRpbmcgPSBmYWxzZTtcblxuXHRcdF8uYmluZEFsbCggdGhpcywgJ3Njcm9sbCcgKTtcbiAgICAgICAgdGhpcy5saXN0ZW5UbyggdGhpcy5jb2xsZWN0aW9uLCAncmVzZXQnLCB0aGlzLnJlbmRlciApO1xuICAgICAgICBcbiAgICAgICAgaWYgKCAhIHRoaXMuY29sbGVjdGlvbi5sZW5ndGggKSB7XG5cdFx0XHR0aGlzLmdldFZpZGVvcygpO1xuXHRcdH1cblx0fSxcblxuXHRyZW5kZXI6IGZ1bmN0aW9uKCkge1xuXHRcdHRoaXMuJGVsLm9mZiggJ3Njcm9sbCcgKS5vbiggJ3Njcm9sbCcsIHRoaXMuc2Nyb2xsICk7XG5cblx0XHR0aGlzLnZpZXdzLmFkZChbXG5cdFx0XHRuZXcgVmlkZW9zSXRlbXMoe1xuXHRcdFx0XHRjb2xsZWN0aW9uOiB0aGlzLmNvbGxlY3Rpb24sXG5cdFx0XHRcdGNvbnRyb2xsZXI6IHRoaXMuY29udHJvbGxlclxuXHRcdFx0fSksXG5cdFx0XHRuZXcgVmlkZW9zU2lkZWJhcih7XG5cdFx0XHRcdGNvbnRyb2xsZXI6IHRoaXMuY29udHJvbGxlclxuXHRcdFx0fSksXG5cdFx0XHRuZXcgVmlkZW9zTm9JdGVtcyh7XG5cdFx0XHRcdGNvbGxlY3Rpb246IHRoaXMuY29sbGVjdGlvblxuXHRcdFx0fSlcblx0XHRdKTtcblxuXHRcdHJldHVybiB0aGlzO1xuXHR9LFxuXG5cdHNjcm9sbDogZnVuY3Rpb24oKSB7XG5cdFx0aWYgKCAhIHRoaXMuX3BlbmRpbmcgJiYgdGhpcy5lbC5zY3JvbGxIZWlnaHQgPCB0aGlzLmVsLnNjcm9sbFRvcCArIHRoaXMuZWwuY2xpZW50SGVpZ2h0ICogMyApIHtcblx0XHRcdHRoaXMuX3BlbmRpbmcgPSB0cnVlO1xuXHRcdFx0dGhpcy5nZXRWaWRlb3MoKTtcblx0XHR9XG5cdH0sXG5cblx0Z2V0VmlkZW9zOiBmdW5jdGlvbigpIHtcblx0XHR2YXIgdmlldyA9IHRoaXM7XG5cblx0XHR3cC5hamF4LnBvc3QoICd2aWRlb19jZW50cmFsX2dldF92aWRlb3NfZm9yX2ZyYW1lJywge1xuXHRcdFx0cGFnZWQ6IHZpZXcuX3BhZ2VkXG5cdFx0fSkuZG9uZShmdW5jdGlvbiggcmVzcG9uc2UgKSB7XG5cdFx0XHR2aWV3LmNvbGxlY3Rpb24uYWRkKCByZXNwb25zZS52aWRlb3MgKTtcblxuXHRcdFx0dmlldy5fcGFnZWQrKztcblxuXHRcdFx0aWYgKCB2aWV3Ll9wYWdlZCA8PSByZXNwb25zZS5tYXhOdW1QYWdlcyApIHtcblx0XHRcdFx0dmlldy5fcGVuZGluZyA9IGZhbHNlO1xuXHRcdFx0XHR2aWV3LnNjcm9sbCgpO1xuXHRcdFx0fVxuXHRcdH0pO1xuXHR9XG59KTtcblxubW9kdWxlLmV4cG9ydHMgPSBWaWRlb3NCcm93c2VyO1xuIiwidmFyIEluc2VydFZpZGVvc0ZyYW1lLFxuXHRWaWRlb3NCcm93c2VyID0gcmVxdWlyZSggJy4uL2NvbnRlbnQvdmlkZW9zLWJyb3dzZXInICksXG4gICAgVmlkZW9zQ29udHJvbGxlciA9IHJlcXVpcmUoICcuLi8uLi9jb250cm9sbGVycy92aWRlb3MnICksXG5cdFZpZGVvc1Rvb2xiYXIgPSByZXF1aXJlKCAnLi4vdG9vbGJhci92aWRlb3MnICksXG4gICAgd3AgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snd3AnXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ3dwJ10gOiBudWxsKSxcbiAgICBQb3N0RnJhbWUgPSB3cC5tZWRpYS52aWV3Lk1lZGlhRnJhbWU7XG5cbkluc2VydFZpZGVvc0ZyYW1lID0gUG9zdEZyYW1lLmV4dGVuZCh7XG5cbiAgICBpbml0aWFsaXplOiBmdW5jdGlvbigpIHtcblx0XHRfLmV4dGVuZCggdGhpcy5vcHRpb25zLCB7XG5cdFx0XHR1cGxvYWRlcjogZmFsc2Vcblx0XHR9KTtcblxuXHRcdFBvc3RGcmFtZS5wcm90b3R5cGUuaW5pdGlhbGl6ZS5hcHBseSggdGhpcywgYXJndW1lbnRzICk7XG5cblx0XHR0aGlzLmNyZWF0ZVN0YXRlcygpO1xuXHRcdHRoaXMuYmluZEhhbmRsZXJzKCk7XG5cblx0XHR0aGlzLnNldFN0YXRlKCAndmlkZW8tY2VudHJhbC1wbGF5bGlzdC12aWRlb3MnICk7XG4gICAgfSxcbiAgICBcblx0Y3JlYXRlU3RhdGVzOiBmdW5jdGlvbigpIHtcblx0XHR0aGlzLnN0YXRlcy5hZGQoIG5ldyBWaWRlb3NDb250cm9sbGVyKHt9KSApO1xuXHR9LFxuXG5cdGJpbmRIYW5kbGVyczogZnVuY3Rpb24oKSB7XG5cdFx0dGhpcy5vbiggJ2NvbnRlbnQ6Y3JlYXRlOnZpZGVvLWNlbnRyYWwtdmlkZW9zLWJyb3dzZXInLCB0aGlzLmNyZWF0ZUN1ZUNvbnRlbnQsIHRoaXMgKTtcblx0XHR0aGlzLm9uKCAndG9vbGJhcjpjcmVhdGU6dmlkZW8tY2VudHJhbC1wbGF5bGlzdC1pbnNlcnQtdmlkZW9zJywgdGhpcy5jcmVhdGVDdWVUb29sYmFyLCB0aGlzICk7XG5cdH0sXG5cblx0Y3JlYXRlQ3VlQ29udGVudDogZnVuY3Rpb24oIGNvbnRlbnQgKSB7XG5cdFx0Y29udGVudC52aWV3ID0gbmV3IFZpZGVvc0Jyb3dzZXIoe1xuXHRcdFx0Y29udHJvbGxlcjogdGhpc1xuXHRcdH0pO1xuXHR9LFxuXG5cdGNyZWF0ZUN1ZVRvb2xiYXI6IGZ1bmN0aW9uKCB0b29sYmFyICkge1xuXHRcdHRvb2xiYXIudmlldyA9IG5ldyBWaWRlb3NUb29sYmFyKHtcblx0XHRcdGNvbnRyb2xsZXI6IHRoaXNcblx0XHR9KTtcblx0fSxcbn0pO1xuXG5tb2R1bGUuZXhwb3J0cyA9IEluc2VydFZpZGVvc0ZyYW1lO1xuIiwidmFyIFBvc3RGb3JtLFxuXHQkID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ2pRdWVyeSddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnalF1ZXJ5J10gOiBudWxsKSxcblx0QWRkVmlkZW9zQnV0dG9uID0gcmVxdWlyZSggJy4vYnV0dG9uL2FkZC12aWRlb3MnICksXG5cdFZpZGVvTGlzdCA9IHJlcXVpcmUoICcuL3ZpZGVvLWxpc3QnICksXG5cdHdwID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ3dwJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWyd3cCddIDogbnVsbCk7XG5cblBvc3RGb3JtID0gd3AuQmFja2JvbmUuVmlldy5leHRlbmQoe1xuXHRlbDogJyNwb3N0Jyxcblx0c2F2ZWQ6IGZhbHNlLFxuXG5cdGV2ZW50czoge1xuXHRcdCdjbGljayAjcHVibGlzaCc6ICdidXR0b25DbGljaycsXG5cdFx0J2NsaWNrICNzYXZlLXBvc3QnOiAnYnV0dG9uQ2xpY2snXG5cdFx0Ly8nc3VibWl0JzogJ3N1Ym1pdCdcblx0fSxcblxuXHRpbml0aWFsaXplOiBmdW5jdGlvbiggb3B0aW9ucyApIHtcblx0XHR0aGlzLmwxMG4gPSBvcHRpb25zLmwxMG47XG5cblx0XHR0aGlzLnJlbmRlcigpO1xuXHR9LFxuXG5cdHJlbmRlcjogZnVuY3Rpb24oKSB7XG5cdFx0dGhpcy52aWV3cy5hZGQoICcjdmlkZW8tY2VudHJhbC1wbGF5bGlzdC1wbGF5bGlzdC1lZGl0b3IgLnZpZGVvLWNlbnRyYWwtcGxheWxpc3QtcGFuZWwtYm9keScsIFtcblx0XHRcdG5ldyBBZGRWaWRlb3NCdXR0b24oe1xuXHRcdFx0XHRjb2xsZWN0aW9uOiB0aGlzLmNvbGxlY3Rpb24sXG5cdFx0XHRcdGwxMG46IHRoaXMubDEwblxuXHRcdFx0fSksXG5cblx0XHRcdG5ldyBWaWRlb0xpc3Qoe1xuXHRcdFx0XHRjb2xsZWN0aW9uOiB0aGlzLmNvbGxlY3Rpb25cblx0XHRcdH0pXG5cdFx0XSk7XG5cblx0XHRyZXR1cm4gdGhpcztcblx0fSxcblxuXHRidXR0b25DbGljazogZnVuY3Rpb24oIGUgKSB7XG5cdFx0dmFyIHNlbGYgPSB0aGlzLFxuXHRcdFx0JGJ1dHRvbiA9ICQoIGUudGFyZ2V0ICk7XG5cblx0XHRpZiAoICEgc2VsZi5zYXZlZCApIHtcblx0XHRcdHRoaXMuY29sbGVjdGlvbi5zYXZlKCkuZG9uZShmdW5jdGlvbiggZGF0YSApIHtcblx0XHRcdFx0c2VsZi5zYXZlZCA9IHRydWU7XG5cdFx0XHRcdCRidXR0b24uY2xpY2soKTtcblx0XHRcdH0pO1xuXHRcdH1cblxuXHRcdHJldHVybiBzZWxmLnNhdmVkO1xuXHR9XG59KTtcblxubW9kdWxlLmV4cG9ydHMgPSBQb3N0Rm9ybTtcbiIsInZhciBWaWRlb3NUb29sYmFyLFxuXHRfID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ18nXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ18nXSA6IG51bGwpLFxuXHR3cCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93Wyd3cCddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnd3AnXSA6IG51bGwpLFxuXHR2aWRlb19jZW50cmFsID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ3ZpZGVvX2NlbnRyYWwnXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ3ZpZGVvX2NlbnRyYWwnXSA6IG51bGwpLFxuXG5WaWRlb3NUb29sYmFyID0gd3AubWVkaWEudmlldy5Ub29sYmFyLmV4dGVuZCh7XG5cdGluaXRpYWxpemU6IGZ1bmN0aW9uKCBvcHRpb25zICkge1xuXHRcdHRoaXMuY29udHJvbGxlciA9IG9wdGlvbnMuY29udHJvbGxlcjtcblxuXHRcdF8uYmluZEFsbCggdGhpcywgJ2luc2VydFZpZGVvcycgKTtcblxuXHRcdC8vIFRoaXMgaXMgYSBidXR0b24uXG5cdFx0dGhpcy5vcHRpb25zLml0ZW1zID0gXy5kZWZhdWx0cyggdGhpcy5vcHRpb25zLml0ZW1zIHx8IHt9LCB7XG5cdFx0XHRpbnNlcnQ6IHtcblx0XHRcdFx0dGV4dDogd3AubWVkaWEudmlldy5sMTBuLmluc2VydEludG9QbGF5bGlzdCB8fCAnSW5zZXJ0IGludG8gcGxheWxpc3QnLFxuXHRcdFx0XHRzdHlsZTogJ3ByaW1hcnknLFxuXHRcdFx0XHRwcmlvcml0eTogODAsXG5cdFx0XHRcdHJlcXVpcmVzOiB7XG5cdFx0XHRcdFx0c2VsZWN0aW9uOiB0cnVlXG5cdFx0XHRcdH0sXG5cdFx0XHRcdGNsaWNrOiB0aGlzLmluc2VydFZpZGVvc1xuXHRcdFx0fVxuXHRcdH0pO1xuXG5cdFx0d3AubWVkaWEudmlldy5Ub29sYmFyLnByb3RvdHlwZS5pbml0aWFsaXplLmFwcGx5KCB0aGlzLCBhcmd1bWVudHMgKTtcblx0fSxcblxuXHRpbnNlcnRWaWRlb3M6IGZ1bmN0aW9uKCkge1xuICAgICAgICB2YXIgc3RhdGUgPSB0aGlzLmNvbnRyb2xsZXIuc3RhdGUoKSwgXG4gICAgICAgICAgICBzZWxlY3Rpb24gPSBzdGF0ZS5nZXQoICdzZWxlY3Rpb24nICk7XG4gICAgICAgICAgICBcbiAgICAgICAgXy5lYWNoKCBzZWxlY3Rpb24ubW9kZWxzLCBmdW5jdGlvbiggYXR0YWNobWVudCApIHtcbiAgICAgICAgICAgIGF0dGFjaG1lbnQuc2V0KCAndmlkZW9JZCcsIGF0dGFjaG1lbnQuZ2V0KCdpZCcpICk7XG4gICAgICAgICAgICB2aWRlb19jZW50cmFsLnZpZGVvcy5wdXNoKCBhdHRhY2htZW50LnRvSlNPTigpICk7XG4gICAgICAgIH0pO1xuXG4gICAgICAgIHRoaXMuY29udHJvbGxlci5jbG9zZSgpO1xuICAgICAgICBcbiAgICAgICAgc3RhdGUudHJpZ2dlciggJ2luc2VydCcsIHNlbGVjdGlvbiApLnJlc2V0KCk7XG5cdH1cbn0pO1xuXG5tb2R1bGUuZXhwb3J0cyA9IFZpZGVvc1Rvb2xiYXI7XG4iLCJ2YXIgVmlkZW9MaXN0LFxuXHQkID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ2pRdWVyeSddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnalF1ZXJ5J10gOiBudWxsKSxcblx0XyA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93WydfJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWydfJ10gOiBudWxsKSxcblx0VmlkZW8gPSByZXF1aXJlKCAnLi92aWRlbycgKSxcblx0d3AgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snd3AnXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ3dwJ10gOiBudWxsKTtcblxuVmlkZW9MaXN0ID0gd3AuQmFja2JvbmUuVmlldy5leHRlbmQoe1xuXHRjbGFzc05hbWU6ICd2aWRlby1jZW50cmFsLXBsYXlsaXN0LXZpZGVvbGlzdCcsXG5cdHRhZ05hbWU6ICdvbCcsXG5cblx0aW5pdGlhbGl6ZTogZnVuY3Rpb24oKSB7XG5cdFx0dGhpcy5saXN0ZW5UbyggdGhpcy5jb2xsZWN0aW9uLCAnYWRkJywgdGhpcy5hZGRWaWRlbyApO1xuXHRcdHRoaXMubGlzdGVuVG8oIHRoaXMuY29sbGVjdGlvbiwgJ2FkZCByZW1vdmUnLCB0aGlzLnVwZGF0ZU9yZGVyICk7XG5cdFx0dGhpcy5saXN0ZW5UbyggdGhpcy5jb2xsZWN0aW9uLCAncmVzZXQnLCB0aGlzLnJlbmRlciApO1xuXHR9LFxuXG5cdHJlbmRlcjogZnVuY3Rpb24oKSB7XG5cdFx0dGhpcy4kZWwuZW1wdHkoKTtcblxuXHRcdHRoaXMuY29sbGVjdGlvbi5lYWNoKCB0aGlzLmFkZFZpZGVvLCB0aGlzICk7XG5cdFx0dGhpcy51cGRhdGVPcmRlcigpO1xuXG5cdFx0dGhpcy4kZWwuc29ydGFibGUoIHtcblx0XHRcdGF4aXM6ICd5Jyxcblx0XHRcdGRlbGF5OiAxNTAsXG5cdFx0XHRmb3JjZUhlbHBlclNpemU6IHRydWUsXG5cdFx0XHRmb3JjZVBsYWNlaG9sZGVyU2l6ZTogdHJ1ZSxcblx0XHRcdG9wYWNpdHk6IDAuNixcblx0XHRcdHN0YXJ0OiBmdW5jdGlvbiggZSwgdWkgKSB7XG5cdFx0XHRcdHVpLnBsYWNlaG9sZGVyLmNzcyggJ3Zpc2liaWxpdHknLCAndmlzaWJsZScgKTtcblx0XHRcdH0sXG5cdFx0XHR1cGRhdGU6IF8uYmluZChmdW5jdGlvbiggZSwgdWkgKSB7XG5cdFx0XHRcdHRoaXMudXBkYXRlT3JkZXIoKTtcblx0XHRcdH0sIHRoaXMgKVxuXHRcdH0gKTtcblxuXHRcdHJldHVybiB0aGlzO1xuXHR9LFxuXG5cdGFkZFZpZGVvOiBmdW5jdGlvbiggdmlkZW8gKSB7XG5cdFx0dmFyIHZpZGVvVmlldyA9IG5ldyBWaWRlbyh7IG1vZGVsOiB2aWRlbyB9KTtcblx0XHR0aGlzLiRlbC5hcHBlbmQoIHZpZGVvVmlldy5yZW5kZXIoKS5lbCApO1xuXHR9LFxuXG5cdHVwZGF0ZU9yZGVyOiBmdW5jdGlvbigpIHtcblx0XHRfLmVhY2goIHRoaXMuJGVsLmZpbmQoICcudmlkZW8tY2VudHJhbC1wbGF5bGlzdC12aWRlbycgKSwgZnVuY3Rpb24oIGl0ZW0sIGkgKSB7XG5cdFx0XHR2YXIgY2lkID0gJCggaXRlbSApLmRhdGEoICdjaWQnICk7XG5cdFx0XHR0aGlzLmNvbGxlY3Rpb24uZ2V0KCBjaWQgKS5zZXQoICdvcmRlcicsIGkgKTtcblx0XHR9LCB0aGlzICk7XG5cdH1cbn0pO1xuXG5tb2R1bGUuZXhwb3J0cyA9IFZpZGVvTGlzdDtcbiIsInZhciBWaWRlbyxcblx0JCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93WydqUXVlcnknXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ2pRdWVyeSddIDogbnVsbCksXG5cdFZpZGVvQXJ0d29yayA9IHJlcXVpcmUoICcuL3ZpZGVvL2FydHdvcmsnICksXG5cdFZpZGVvQXVkaW8gPSByZXF1aXJlKCAnLi92aWRlby9hdWRpbycgKSxcblx0d3AgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snd3AnXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ3dwJ10gOiBudWxsKTtcblxuVmlkZW8gPSB3cC5CYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG5cdHRhZ05hbWU6ICdsaScsXG5cdGNsYXNzTmFtZTogJ3ZpZGVvLWNlbnRyYWwtcGxheWxpc3QtdmlkZW8nLFxuXHR0ZW1wbGF0ZTogd3AudGVtcGxhdGUoICd2aWRlby1jZW50cmFsLXBsYXlsaXN0LXBsYXlsaXN0LXZpZGVvJyApLFxuXG5cdGV2ZW50czoge1xuXHRcdCdjaGFuZ2UgW2RhdGEtc2V0dGluZ10nOiAndXBkYXRlQXR0cmlidXRlJyxcblx0XHQnY2xpY2sgLmpzLXRvZ2dsZSc6ICd0b2dnbGVPcGVuU3RhdHVzJyxcblx0XHQnZGJsY2xpY2sgLnZpZGVvLWNlbnRyYWwtcGxheWxpc3QtdmlkZW8tdGl0bGUnOiAndG9nZ2xlT3BlblN0YXR1cycsXG5cdFx0J2NsaWNrIC5qcy1jbG9zZSc6ICdtaW5pbWl6ZScsXG5cdFx0J2NsaWNrIC5qcy1yZW1vdmUnOiAnZGVzdHJveSdcblx0fSxcblxuXHRpbml0aWFsaXplOiBmdW5jdGlvbigpIHtcblx0XHR0aGlzLmxpc3RlblRvKCB0aGlzLm1vZGVsLCAnY2hhbmdlOnRpdGxlJywgdGhpcy51cGRhdGVUaXRsZSApO1xuXHRcdHRoaXMubGlzdGVuVG8oIHRoaXMubW9kZWwsICdjaGFuZ2UnLCB0aGlzLnVwZGF0ZUZpZWxkcyApO1xuXHRcdHRoaXMubGlzdGVuVG8oIHRoaXMubW9kZWwsICdkZXN0cm95JywgdGhpcy5yZW1vdmUgKTtcblx0fSxcblxuXHRyZW5kZXI6IGZ1bmN0aW9uKCkge1xuXHRcdHRoaXMuJGVsLmh0bWwoIHRoaXMudGVtcGxhdGUoIHRoaXMubW9kZWwudG9KU09OKCkgKSApLmRhdGEoICdjaWQnLCB0aGlzLm1vZGVsLmNpZCApO1xuXG5cdFx0dGhpcy52aWV3cy5hZGQoICcudmlkZW8tY2VudHJhbC1wbGF5bGlzdC12aWRlby1jb2x1bW4tYXJ0d29yaycsIG5ldyBWaWRlb0FydHdvcmsoe1xuXHRcdFx0bW9kZWw6IHRoaXMubW9kZWwsXG5cdFx0XHRwYXJlbnQ6IHRoaXNcblx0XHR9KSk7XG5cblx0XHR0aGlzLnZpZXdzLmFkZCggJy52aWRlby1jZW50cmFsLXBsYXlsaXN0LXZpZGVvLWF1ZGlvLWdyb3VwJywgbmV3IFZpZGVvQXVkaW8oe1xuXHRcdFx0bW9kZWw6IHRoaXMubW9kZWwsXG5cdFx0XHRwYXJlbnQ6IHRoaXNcblx0XHR9KSk7XG5cblx0XHRyZXR1cm4gdGhpcztcblx0fSxcblxuXHRtaW5pbWl6ZTogZnVuY3Rpb24oIGUgKSB7XG5cdFx0ZS5wcmV2ZW50RGVmYXVsdCgpO1xuXHRcdHRoaXMuJGVsLnJlbW92ZUNsYXNzKCAnaXMtb3BlbicgKS5maW5kKCAnaW5wdXQ6Zm9jdXMnICkuYmx1cigpO1xuXHR9LFxuXG5cdHRvZ2dsZU9wZW5TdGF0dXM6IGZ1bmN0aW9uKCBlICkge1xuXHRcdGUucHJldmVudERlZmF1bHQoKTtcblx0XHR0aGlzLiRlbC50b2dnbGVDbGFzcyggJ2lzLW9wZW4nICkuZmluZCggJ2lucHV0OmZvY3VzJyApLmJsdXIoKTtcblxuXHRcdC8vIFRyaWdnZXIgYSByZXNpemUgc28gdGhlIG1lZGlhIGVsZW1lbnQgd2lsbCBmaWxsIHRoZSBjb250YWluZXIuXG5cdFx0aWYgKCB0aGlzLiRlbC5oYXNDbGFzcyggJ2lzLW9wZW4nICkgKSB7XG5cdFx0XHQkKCB3aW5kb3cgKS50cmlnZ2VyKCAncmVzaXplJyApO1xuXHRcdH1cblx0fSxcblxuXHQvKipcblx0ICogVXBkYXRlIGEgbW9kZWwgYXR0cmlidXRlIHdoZW4gYSBmaWVsZCBpcyBjaGFuZ2VkLlxuXHQgKlxuXHQgKiBGaWVsZHMgd2l0aCBhICdkYXRhLXNldHRpbmc9XCJ7e2tleX19XCInIGF0dHJpYnV0ZSB3aG9zZSB2YWx1ZVxuXHQgKiBjb3JyZXNwb25kcyB0byBhIG1vZGVsIGF0dHJpYnV0ZSB3aWxsIGJlIGF1dG9tYXRpY2FsbHkgc3luY2VkLlxuXHQgKlxuXHQgKiBAcGFyYW0ge09iamVjdH0gZSBFdmVudCBvYmplY3QuXG5cdCAqL1xuXHR1cGRhdGVBdHRyaWJ1dGU6IGZ1bmN0aW9uKCBlICkge1xuXHRcdHZhciBhdHRyaWJ1dGUgPSAkKCBlLnRhcmdldCApLmRhdGEoICdzZXR0aW5nJyApLFxuXHRcdFx0dmFsdWUgPSBlLnRhcmdldC52YWx1ZTtcblxuXHRcdGlmICggdGhpcy5tb2RlbC5nZXQoIGF0dHJpYnV0ZSApICE9PSB2YWx1ZSApIHtcblx0XHRcdHRoaXMubW9kZWwuc2V0KCBhdHRyaWJ1dGUsIHZhbHVlICk7XG5cdFx0fVxuXHR9LFxuXG5cdC8qKlxuXHQgKiBVcGRhdGUgYSBzZXR0aW5nIGZpZWxkIHdoZW4gYSBtb2RlbCdzIGF0dHJpYnV0ZSBpcyBjaGFuZ2VkLlxuXHQgKi9cblx0dXBkYXRlRmllbGRzOiBmdW5jdGlvbigpIHtcblx0XHR2YXIgdmlkZW8gPSB0aGlzLm1vZGVsLnRvSlNPTigpLFxuXHRcdFx0JHNldHRpbmdzID0gdGhpcy4kZWwuZmluZCggJ1tkYXRhLXNldHRpbmddJyApLFxuXHRcdFx0YXR0cmlidXRlLCB2YWx1ZTtcblxuXHRcdC8vIEEgY2hhbmdlIGV2ZW50IHNob3VsZG4ndCBiZSB0cmlnZ2VyZWQgaGVyZSwgc28gaXQgd29uJ3QgY2F1c2Vcblx0XHQvLyB0aGUgbW9kZWwgYXR0cmlidXRlIHRvIGJlIHVwZGF0ZWQgYW5kIGdldCBzdHVjayBpbiBhblxuXHRcdC8vIGluZmluaXRlIGxvb3AuXG5cdFx0Zm9yICggYXR0cmlidXRlIGluIHZpZGVvICkge1xuXHRcdFx0Ly8gRGVjb2RlIEhUTUwgZW50aXRpZXMuXG5cdFx0XHR2YWx1ZSA9ICQoICc8ZGl2Lz4nICkuaHRtbCggdmlkZW9bIGF0dHJpYnV0ZSBdICkudGV4dCgpO1xuXHRcdFx0JHNldHRpbmdzLmZpbHRlciggJ1tkYXRhLXNldHRpbmc9XCInICsgYXR0cmlidXRlICsgJ1wiXScgKS52YWwoIHZhbHVlICk7XG5cdFx0fVxuXHR9LFxuXG5cdHVwZGF0ZVRpdGxlOiBmdW5jdGlvbigpIHtcblx0XHR2YXIgdGl0bGUgPSB0aGlzLm1vZGVsLmdldCggJ3RpdGxlJyApO1xuXHRcdHRoaXMuJGVsLmZpbmQoICcudmlkZW8tY2VudHJhbC1wbGF5bGlzdC12aWRlby10aXRsZSAudGV4dCcgKS50ZXh0KCB0aXRsZSA/IHRpdGxlIDogJ1RpdGxlJyApO1xuXHR9LFxuXG5cdC8qKlxuXHQgKiBEZXN0cm95IHRoZSB2aWV3J3MgbW9kZWwuXG5cdCAqXG5cdCAqIEF2b2lkIHN5bmNpbmcgdG8gdGhlIHNlcnZlciBieSB0cmlnZ2VyaW5nIGFuIGV2ZW50IGluc3RlYWQgb2Zcblx0ICogY2FsbGluZyBkZXN0cm95KCkgZGlyZWN0bHkgb24gdGhlIG1vZGVsLlxuXHQgKi9cblx0ZGVzdHJveTogZnVuY3Rpb24oKSB7XG5cdFx0dGhpcy5tb2RlbC50cmlnZ2VyKCAnZGVzdHJveScsIHRoaXMubW9kZWwgKTtcblx0fSxcblxuXHRyZW1vdmU6IGZ1bmN0aW9uKCkge1xuXHRcdHRoaXMuJGVsLnJlbW92ZSgpO1xuXHR9XG59KTtcblxubW9kdWxlLmV4cG9ydHMgPSBWaWRlbztcbiIsInZhciBWaWRlb0FydHdvcmssXG5cdF8gPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snXyddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnXyddIDogbnVsbCksXG5cdHdvcmtmbG93cyA9IHJlcXVpcmUoICcuLi8uLi93b3JrZmxvd3MnICksXG5cdHdwID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ3dwJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWyd3cCddIDogbnVsbCk7XG5cblZpZGVvQXJ0d29yayA9IHdwLkJhY2tib25lLlZpZXcuZXh0ZW5kKHtcblx0dGFnTmFtZTogJ3NwYW4nLFxuXHRjbGFzc05hbWU6ICd2aWRlby1jZW50cmFsLXBsYXlsaXN0LXZpZGVvLWFydHdvcmsnLFxuXHR0ZW1wbGF0ZTogd3AudGVtcGxhdGUoICd2aWRlby1jZW50cmFsLXBsYXlsaXN0LXBsYXlsaXN0LXZpZGVvLWFydHdvcmsnICksXG5cblx0ZXZlbnRzOiB7XG5cdFx0J2NsaWNrJzogJ3NlbGVjdCdcblx0fSxcblxuXHRpbml0aWFsaXplOiBmdW5jdGlvbiggb3B0aW9ucyApIHtcblx0XHR0aGlzLnBhcmVudCA9IG9wdGlvbnMucGFyZW50O1xuXHRcdHRoaXMubGlzdGVuVG8oIHRoaXMubW9kZWwsICdjaGFuZ2U6YXJ0d29ya1VybCcsIHRoaXMucmVuZGVyICk7XG5cdH0sXG5cblx0cmVuZGVyOiBmdW5jdGlvbigpIHtcblx0XHR0aGlzLiRlbC5odG1sKCB0aGlzLnRlbXBsYXRlKCB0aGlzLm1vZGVsLnRvSlNPTigpICkgKTtcblx0XHR0aGlzLnBhcmVudC4kZWwudG9nZ2xlQ2xhc3MoICdoYXMtYXJ0d29yaycsICEgXy5pc0VtcHR5KCB0aGlzLm1vZGVsLmdldCggJ2FydHdvcmtVcmwnICkgKSApO1xuXHRcdHJldHVybiB0aGlzO1xuXHR9LFxuXG5cdHNlbGVjdDogZnVuY3Rpb24oKSB7XG5cdFx0d29ya2Zsb3dzLnNldE1vZGVsKCB0aGlzLm1vZGVsICkuZ2V0KCAnc2VsZWN0QXJ0d29yaycgKS5vcGVuKCk7XG5cdH1cbn0pO1xuXG5tb2R1bGUuZXhwb3J0cyA9IFZpZGVvQXJ0d29yaztcbiIsInZhciBWaWRlb0F1ZGlvLFxuXHQkID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ2pRdWVyeSddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnalF1ZXJ5J10gOiBudWxsKSxcblx0XyA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93WydfJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWydfJ10gOiBudWxsKSxcblx0c2V0dGluZ3MgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1sndmlkZW9fY2VudHJhbCddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsndmlkZW9fY2VudHJhbCddIDogbnVsbCkuc2V0dGluZ3MoKSxcblx0d29ya2Zsb3dzID0gcmVxdWlyZSggJy4uLy4uL3dvcmtmbG93cycgKSxcblx0d3AgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snd3AnXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ3dwJ10gOiBudWxsKTtcblxuVmlkZW9BdWRpbyA9IHdwLkJhY2tib25lLlZpZXcuZXh0ZW5kKHtcblx0dGFnTmFtZTogJ3NwYW4nLFxuXHRjbGFzc05hbWU6ICd2aWRlby1jZW50cmFsLXBsYXlsaXN0LXZpZGVvLWF1ZGlvJyxcblx0dGVtcGxhdGU6IHdwLnRlbXBsYXRlKCAndmlkZW8tY2VudHJhbC1wbGF5bGlzdC1wbGF5bGlzdC12aWRlby1hdWRpbycgKSxcblxuXHRldmVudHM6IHtcblx0XHQnY2xpY2sgLnZpZGVvLWNlbnRyYWwtcGxheWxpc3QtdmlkZW8tYXVkaW8tc2VsZWN0b3InOiAnc2VsZWN0J1xuXHR9LFxuXG5cdGluaXRpYWxpemU6IGZ1bmN0aW9uKCBvcHRpb25zICkge1xuXHRcdHRoaXMucGFyZW50ID0gb3B0aW9ucy5wYXJlbnQ7XG5cblx0XHR0aGlzLmxpc3RlblRvKCB0aGlzLm1vZGVsLCAnY2hhbmdlOmF1ZGlvVXJsJywgdGhpcy5yZWZyZXNoICk7XG5cdFx0dGhpcy5saXN0ZW5UbyggdGhpcy5tb2RlbCwgJ2Rlc3Ryb3knLCB0aGlzLmNsZWFudXAgKTtcblx0fSxcblxuXHRyZW5kZXI6IGZ1bmN0aW9uKCkge1xuXHRcdHZhciAkbWVkaWFFbCwgcGxheWVyU2V0dGluZ3MsXG5cdFx0XHR2aWRlbyA9IHRoaXMubW9kZWwudG9KU09OKCksXG5cdFx0XHRwbGF5ZXJJZCA9IHRoaXMuJGVsLmZpbmQoICcubWVqcy1hdWRpbycgKS5hdHRyKCAnaWQnICk7XG5cblx0XHQvLyBSZW1vdmUgdGhlIE1lZGlhRWxlbWVudCBwbGF5ZXIgb2JqZWN0IGlmIHRoZVxuXHRcdC8vIGF1ZGlvIGZpbGUgVVJMIGlzIGVtcHR5LlxuXHRcdGlmICggJycgPT09IHZpZGVvLmF1ZGlvVXJsICYmIHBsYXllcklkICkge1xuXHRcdFx0bWVqcy5wbGF5ZXJzWyBwbGF5ZXJJZCBdLnJlbW92ZSgpO1xuXHRcdH1cblxuXHRcdC8vIFJlbmRlciB0aGUgbWVkaWEgZWxlbWVudC5cblx0XHR0aGlzLiRlbC5odG1sKCB0aGlzLnRlbXBsYXRlKCB0aGlzLm1vZGVsLnRvSlNPTigpICkgKTtcblxuXHRcdC8vIFNldCB1cCBNZWRpYUVsZW1lbnQuanMuXG5cdFx0JG1lZGlhRWwgPSB0aGlzLiRlbC5maW5kKCAnLnZpZGVvLWNlbnRyYWwtcGxheWxpc3QtYXVkaW8nICk7XG5cblx0XHRyZXR1cm4gdGhpcztcblx0fSxcblxuXHRyZWZyZXNoOiBmdW5jdGlvbiggZSApIHtcblx0XHR2YXIgdmlkZW8gPSB0aGlzLm1vZGVsLnRvSlNPTigpLFxuXHRcdFx0cGxheWVySWQgPSB0aGlzLiRlbC5maW5kKCAnLm1lanMtYXVkaW8nICkuYXR0ciggJ2lkJyApLFxuXHRcdFx0cGxheWVyID0gcGxheWVySWQgPyBtZWpzLnBsYXllcnNbIHBsYXllcklkIF0gOiBudWxsO1xuXG5cdFx0aWYgKCBwbGF5ZXIgJiYgJycgIT09IHZpZGVvLmF1ZGlvVXJsICkge1xuXHRcdFx0cGxheWVyLnBhdXNlKCk7XG5cdFx0XHRwbGF5ZXIuc2V0U3JjKCB2aWRlby5hdWRpb1VybCApO1xuXHRcdH0gZWxzZSB7XG5cdFx0XHR0aGlzLnJlbmRlcigpO1xuXHRcdH1cblx0fSxcblxuXHRjbGVhbnVwOiBmdW5jdGlvbigpIHtcblx0XHR2YXIgcGxheWVySWQgPSB0aGlzLiRlbC5maW5kKCAnLm1lanMtYXVkaW8nICkuYXR0ciggJ2lkJyApLFxuXHRcdFx0cGxheWVyID0gcGxheWVySWQgPyBtZWpzLnBsYXllcnNbIHBsYXllcklkIF0gOiBudWxsO1xuXG5cdFx0aWYgKCBwbGF5ZXIgKSB7XG5cdFx0XHRwbGF5ZXIucmVtb3ZlKCk7XG5cdFx0fVxuXHR9LFxuXG5cdHNlbGVjdDogZnVuY3Rpb24oKSB7XG5cdFx0d29ya2Zsb3dzLnNldE1vZGVsKCB0aGlzLm1vZGVsICkuZ2V0KCAnc2VsZWN0QXVkaW8nICkub3BlbigpO1xuXHR9XG59KTtcblxubW9kdWxlLmV4cG9ydHMgPSBWaWRlb0F1ZGlvO1xuIiwidmFyIFZpZGVvcyxcblx0d3AgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snd3AnXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ3dwJ10gOiBudWxsKTtcblxuVmlkZW9zID0gd3AuQmFja2JvbmUuVmlldy5leHRlbmQoe1xuXHR0YWdOYW1lOiAnbGknLFxuXHRjbGFzc05hbWU6ICd2aWRlby1jZW50cmFsLXZpZGVvcy1icm93c2VyLWxpc3QtaXRlbScsXG5cdHRlbXBsYXRlOiB3cC50ZW1wbGF0ZSggJ3ZpZGVvLWNlbnRyYWwtdmlkZW9zLWJyb3dzZXItbGlzdC1pdGVtJyApLFxuXG5cdGV2ZW50czoge1xuXHRcdCdjbGljayc6ICdyZXNldFNlbGVjdGlvbidcblx0fSxcblxuXHRpbml0aWFsaXplOiBmdW5jdGlvbiggb3B0aW9ucyApIHtcblx0XHR0aGlzLmNvbnRyb2xsZXIgPSBvcHRpb25zLmNvbnRyb2xsZXI7XG5cdFx0dGhpcy5tb2RlbCA9IG9wdGlvbnMubW9kZWw7XG5cdFx0dGhpcy5zZWxlY3Rpb24gPSB0aGlzLmNvbnRyb2xsZXIuc3RhdGUoKS5nZXQoICdzZWxlY3Rpb24nICk7XG5cblx0XHR0aGlzLmxpc3RlblRvKCB0aGlzLnNlbGVjdGlvbiwgJ2FkZCByZW1vdmUgcmVzZXQnLCB0aGlzLnVwZGF0ZVNlbGVjdGVkQ2xhc3MgKTtcblx0fSxcblxuXHRyZW5kZXI6IGZ1bmN0aW9uKCkge1xuXHRcdHRoaXMuJGVsLmh0bWwoIHRoaXMudGVtcGxhdGUoIHRoaXMubW9kZWwudG9KU09OKCkgKSApO1xuXHRcdHJldHVybiB0aGlzO1xuXHR9LFxuXG5cdHJlc2V0U2VsZWN0aW9uOiBmdW5jdGlvbiggZSApIHtcblx0XHRpZiAoIHRoaXMuc2VsZWN0aW9uLmNvbnRhaW5zKCB0aGlzLm1vZGVsICkgKSB7XG5cdFx0XHR0aGlzLnNlbGVjdGlvbi5yZW1vdmUoIHRoaXMubW9kZWwgKTtcblx0XHR9IGVsc2Uge1xuXHRcdFx0dGhpcy5zZWxlY3Rpb24ucmVzZXQoIHRoaXMubW9kZWwgKTtcblx0XHR9XG5cdH0sXG5cblx0dXBkYXRlU2VsZWN0ZWRDbGFzczogZnVuY3Rpb24oKSB7XG5cdFx0aWYgKCB0aGlzLnNlbGVjdGlvbi5jb250YWlucyggdGhpcy5tb2RlbCApICkge1xuXHRcdFx0dGhpcy4kZWwuYWRkQ2xhc3MoICdpcy1zZWxlY3RlZCcgKTtcblx0XHR9IGVsc2Uge1xuXHRcdFx0dGhpcy4kZWwucmVtb3ZlQ2xhc3MoICdpcy1zZWxlY3RlZCcgKTtcblx0XHR9XG5cdH1cbn0pO1xuXG5tb2R1bGUuZXhwb3J0cyA9IFZpZGVvcztcbiIsInZhciBWaWRlb3NJdGVtcyxcblx0VmlkZW9zSXRlbSA9IHJlcXVpcmUoICcuLi92aWRlb3MvaXRlbScgKSxcblx0d3AgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snd3AnXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ3dwJ10gOiBudWxsKTtcblxuVmlkZW9zSXRlbXMgPSB3cC5CYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG5cdGNsYXNzTmFtZTogJ3ZpZGVvLWNlbnRyYWwtdmlkZW9zLWJyb3dzZXItbGlzdCcsXG5cdHRhZ05hbWU6ICd1bCcsXG5cblx0aW5pdGlhbGl6ZTogZnVuY3Rpb24oIG9wdGlvbnMgKSB7XG5cdFx0dGhpcy5jb2xsZWN0aW9uID0gb3B0aW9ucy5jb250cm9sbGVyLnN0YXRlKCkuZ2V0KCAnY29sbGVjdGlvbicgKTtcblx0XHR0aGlzLmNvbnRyb2xsZXIgPSBvcHRpb25zLmNvbnRyb2xsZXI7XG5cblx0XHR0aGlzLmxpc3RlblRvKCB0aGlzLmNvbGxlY3Rpb24sICdhZGQnLCB0aGlzLmFkZEl0ZW0gKTtcblx0XHR0aGlzLmxpc3RlblRvKCB0aGlzLmNvbGxlY3Rpb24sICdyZXNldCcsIHRoaXMucmVuZGVyICk7XG5cdH0sXG5cblx0cmVuZGVyOiBmdW5jdGlvbigpIHtcblx0XHR0aGlzLmNvbGxlY3Rpb24uZWFjaCggdGhpcy5hZGRJdGVtLCB0aGlzICk7XG5cdFx0cmV0dXJuIHRoaXM7XG5cdH0sXG5cblx0YWRkSXRlbTogZnVuY3Rpb24oIG1vZGVsICkge1xuXHRcdHZhciB2aWV3ID0gbmV3IFZpZGVvc0l0ZW0oe1xuXHRcdFx0Y29udHJvbGxlcjogdGhpcy5jb250cm9sbGVyLFxuXHRcdFx0bW9kZWw6IG1vZGVsXG5cdFx0fSkucmVuZGVyKCk7XG5cblx0XHR0aGlzLiRlbC5hcHBlbmQoIHZpZXcuZWwgKTtcblx0fVxufSk7XG5cbm1vZHVsZS5leHBvcnRzID0gVmlkZW9zSXRlbXM7XG4iLCJ2YXIgVmlkZW9zTm9JdGVtcyxcblx0d3AgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snd3AnXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ3dwJ10gOiBudWxsKTtcblxuVmlkZW9zTm9JdGVtcyA9IHdwLkJhY2tib25lLlZpZXcuZXh0ZW5kKHtcblx0Y2xhc3NOYW1lOiAndmlkZW8tY2VudHJhbC12aWRlb3MtYnJvd3Nlci1lbXB0eScsXG5cdHRhZ05hbWU6ICdkaXYnLFxuXHR0ZW1wbGF0ZTogd3AudGVtcGxhdGUoICd2aWRlby1jZW50cmFsLXZpZGVvcy1icm93c2VyLWVtcHR5JyApLFxuXG5cdGluaXRpYWxpemU6IGZ1bmN0aW9uKCBvcHRpb25zICkge1xuXHRcdHRoaXMuY29sbGVjdGlvbiA9IHRoaXMuY29sbGVjdGlvbjtcblxuXHRcdHRoaXMubGlzdGVuVG8oIHRoaXMuY29sbGVjdGlvbiwgJ2FkZCByZW1vdmUgcmVzZXQnLCB0aGlzLnRvZ2dsZVZpc2liaWxpdHkgKTtcblx0fSxcblxuXHRyZW5kZXI6IGZ1bmN0aW9uKCkge1xuXHRcdHRoaXMuJGVsLmh0bWwoIHRoaXMudGVtcGxhdGUoKSApO1xuXHRcdHJldHVybiB0aGlzO1xuXHR9LFxuXG5cdHRvZ2dsZVZpc2liaWxpdHk6IGZ1bmN0aW9uKCkge1xuXHRcdHRoaXMuJGVsLnRvZ2dsZUNsYXNzKCAnaXMtdmlzaWJsZScsIHRoaXMuY29sbGVjdGlvbi5sZW5ndGggPCAxICk7XG5cdH1cbn0pO1xuXG5tb2R1bGUuZXhwb3J0cyA9IFZpZGVvc05vSXRlbXM7XG4iLCJ2YXIgVmlkZW9zU2lkZWJhcixcblx0JCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93WydqUXVlcnknXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ2pRdWVyeSddIDogbnVsbCksXG5cdHdwID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ3dwJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWyd3cCddIDogbnVsbCk7XG5cblZpZGVvc1NpZGViYXIgPSB3cC5CYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG5cdGNsYXNzTmFtZTogJ3ZpZGVvLWNlbnRyYWwtdmlkZW9zLWJyb3dzZXItc2lkZWJhciBtZWRpYS1zaWRlYmFyJyxcblx0dGVtcGxhdGU6IHdwLnRlbXBsYXRlKCAndmlkZW8tY2VudHJhbC12aWRlb3MtYnJvd3Nlci1zaWRlYmFyJyApLFxuXG5cdGV2ZW50czoge1xuXHRcdCdjaGFuZ2UgW2RhdGEtc2V0dGluZ10nOiAndXBkYXRlQXR0cmlidXRlJ1xuXHR9LFxuXG5cdGluaXRpYWxpemU6IGZ1bmN0aW9uKCBvcHRpb25zICkge1xuXHRcdHRoaXMuYXR0cmlidXRlcyA9IG9wdGlvbnMuY29udHJvbGxlci5zdGF0ZSgpLmdldCggJ2F0dHJpYnV0ZXMnICk7XG5cdH0sXG5cblx0cmVuZGVyOiBmdW5jdGlvbigpIHtcblx0XHR0aGlzLiRlbC5odG1sKCB0aGlzLnRlbXBsYXRlKCkgKTtcblx0fSxcblxuXHR1cGRhdGVBdHRyaWJ1dGU6IGZ1bmN0aW9uKCBlICkge1xuXHRcdHZhciAkdGFyZ2V0ID0gJCggZS50YXJnZXQgKSxcblx0XHRcdGF0dHJpYnV0ZSA9ICR0YXJnZXQuZGF0YSggJ3NldHRpbmcnICksXG5cdFx0XHR2YWx1ZSA9IGUudGFyZ2V0LnZhbHVlO1xuXG5cdFx0aWYgKCAnY2hlY2tib3gnID09PSBlLnRhcmdldC50eXBlICkge1xuXHRcdFx0dmFsdWUgPSAhISAkdGFyZ2V0LnByb3AoICdjaGVja2VkJyApO1xuXHRcdH1cblxuXHRcdHRoaXMuYXR0cmlidXRlcy5zZXQoIGF0dHJpYnV0ZSwgdmFsdWUgKTtcblx0fVxufSk7XG5cbm1vZHVsZS5leHBvcnRzID0gVmlkZW9zU2lkZWJhcjtcbiIsInZhciBXb3JrZmxvd3MsXG5cdF8gPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snXyddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnXyddIDogbnVsbCksXG5cdHZpZGVvX2NlbnRyYWwgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1sndmlkZW9fY2VudHJhbCddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsndmlkZW9fY2VudHJhbCddIDogbnVsbCksXG5cdGwxMG4gPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1sndmlkZW9fY2VudHJhbCddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsndmlkZW9fY2VudHJhbCddIDogbnVsbCkubDEwbixcbiAgICBBZGRWaWRlb3NGcmFtZSA9IHJlcXVpcmUoICcuL3ZpZXdzL2ZyYW1lL2luc2VydC12aWRlb3MnICksXG5cdHdwID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ3dwJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWyd3cCddIDogbnVsbCksXG5cdEF0dGFjaG1lbnQgPSB3cC5tZWRpYS5tb2RlbC5BdHRhY2htZW50O1xuXG5Xb3JrZmxvd3MgPSB7XG5cdGZyYW1lczogW10sXG5cdG1vZGVsOiB7fSxcblxuXHQvKipcblx0ICogU2V0IGEgbW9kZWwgZm9yIHRoZSBjdXJyZW50IHdvcmtmbG93LlxuXHQgKlxuXHQgKiBAcGFyYW0ge09iamVjdH0gZnJhbWVcblx0ICovXG5cdHNldE1vZGVsOiBmdW5jdGlvbiggbW9kZWwgKSB7XG5cdFx0dGhpcy5tb2RlbCA9IG1vZGVsO1xuXHRcdHJldHVybiB0aGlzO1xuXHR9LFxuXG5cdC8qKlxuXHQgKiBSZXRyaWV2ZSBvciBjcmVhdGUgYSBmcmFtZSBpbnN0YW5jZSBmb3IgYSBwYXJ0aWN1bGFyIHdvcmtmbG93LlxuXHQgKlxuXHQgKiBAcGFyYW0ge3N0cmluZ30gaWQgRnJhbWUgaWRlbnRpZmVyLlxuXHQgKi9cblx0Z2V0OiBmdW5jdGlvbiggaWQgKSAge1xuXHRcdHZhciBtZXRob2QgPSAnXycgKyBpZCxcblx0XHRcdGZyYW1lID0gdGhpcy5mcmFtZXNbIG1ldGhvZCBdIHx8IG51bGw7XG5cblx0XHQvLyBBbHdheXMgY2FsbCB0aGUgZnJhbWUgbWV0aG9kIHRvIHBlcmZvcm0gYW55IHJvdXRpbmUgc2V0IHVwLiBUaGVcblx0XHQvLyBmcmFtZSBtZXRob2Qgc2hvdWxkIHNob3J0LWNpcmN1aXQgYmVmb3JlIGJlaW5nIGluaXRpYWxpemVkIGFnYWluLlxuXHRcdGZyYW1lID0gdGhpc1sgbWV0aG9kIF0uY2FsbCggdGhpcywgZnJhbWUgKTtcblxuXHRcdC8vIFN0b3JlIHRoZSBmcmFtZSBmb3IgZnV0dXJlIHVzZS5cblx0XHR0aGlzLmZyYW1lc1sgbWV0aG9kIF0gPSBmcmFtZTtcblxuXHRcdHJldHVybiBmcmFtZTtcblx0fSxcblxuXHQvKipcblx0ICogV29ya2Zsb3cgZm9yIGFkZGluZyB2aWRlb3MgdG8gdGhlIHBsYXlsaXN0LlxuXHQgKlxuXHQgKiBAcGFyYW0ge09iamVjdH0gZnJhbWVcblx0ICovXG5cdF9hZGRWaWRlb3M6IGZ1bmN0aW9uKCBmcmFtZSApIHtcblx0XHQvLyBSZXR1cm4gdGhlIGV4aXN0aW5nIGZyYW1lIGZvciB0aGlzIHdvcmtmbG93LlxuXHRcdGlmICggZnJhbWUgKSB7XG5cdFx0XHRyZXR1cm4gZnJhbWU7XG5cdFx0fVxuXG5cdFx0Ly8gSW5pdGlhbGl6ZSB0aGUgYXVkaW8gZnJhbWUuXG4gICAgICAgIGZyYW1lID0gbmV3IEFkZFZpZGVvc0ZyYW1lKCk7XG4gICAgICAgIFxuXHRcdHJldHVybiBmcmFtZTtcblx0fSxcblxuXHQvKipcblx0ICogV29ya2Zsb3cgZm9yIHNlbGVjdGluZyB2aWRlbyBhcnR3b3JrIGltYWdlLlxuXHQgKlxuXHQgKiBAcGFyYW0ge09iamVjdH0gZnJhbWVcblx0ICovXG5cdF9zZWxlY3RBcnR3b3JrOiBmdW5jdGlvbiggZnJhbWUgKSB7XG5cdFx0dmFyIHdvcmtmbG93ID0gdGhpcztcblxuXHRcdC8vIFJldHVybiBleGlzdGluZyBmcmFtZSBmb3IgdGhpcyB3b3JrZmxvdy5cblx0XHRpZiAoIGZyYW1lICkge1xuXHRcdFx0cmV0dXJuIGZyYW1lO1xuXHRcdH1cblxuXHRcdC8vIEluaXRpYWxpemUgdGhlIGFydHdvcmsgZnJhbWUuXG5cdFx0ZnJhbWUgPSB3cC5tZWRpYSh7XG5cdFx0XHR0aXRsZTogbDEwbi53b3JrZmxvd3Muc2VsZWN0QXJ0d29yay5mcmFtZVRpdGxlLFxuXHRcdFx0bGlicmFyeToge1xuXHRcdFx0XHR0eXBlOiAnaW1hZ2UnXG5cdFx0XHR9LFxuXHRcdFx0YnV0dG9uOiB7XG5cdFx0XHRcdHRleHQ6IGwxMG4ud29ya2Zsb3dzLnNlbGVjdEFydHdvcmsuZnJhbWVCdXR0b25UZXh0XG5cdFx0XHR9LFxuXHRcdFx0bXVsdGlwbGU6IGZhbHNlXG5cdFx0fSk7XG5cblx0XHQvLyBTZXQgdGhlIGV4dGVuc2lvbnMgdGhhdCBjYW4gYmUgdXBsb2FkZWQuXG5cdFx0ZnJhbWUudXBsb2FkZXIub3B0aW9ucy51cGxvYWRlci5wbHVwbG9hZCA9IHtcblx0XHRcdGZpbHRlcnM6IHtcblx0XHRcdFx0bWltZV90eXBlczogW3tcblx0XHRcdFx0XHRmaWxlczogbDEwbi53b3JrZmxvd3Muc2VsZWN0QXJ0d29yay5maWxlVHlwZXMsXG5cdFx0XHRcdFx0ZXh0ZW5zaW9uczogJ2pwZyxqcGVnLGdpZixwbmcnXG5cdFx0XHRcdH1dXG5cdFx0XHR9XG5cdFx0fTtcblxuXHRcdC8vIEF1dG9tYXRpY2FsbHkgc2VsZWN0IHRoZSBleGlzdGluZyBhcnR3b3JrIGlmIHBvc3NpYmxlLlxuXHRcdGZyYW1lLm9uKCAnb3BlbicsIGZ1bmN0aW9uKCkge1xuXHRcdFx0dmFyIHNlbGVjdGlvbiA9IHRoaXMuZ2V0KCAnbGlicmFyeScgKS5nZXQoICdzZWxlY3Rpb24nICksXG5cdFx0XHRcdGFydHdvcmtJZCA9IHdvcmtmbG93Lm1vZGVsLmdldCggJ2FydHdvcmtJZCcgKSxcblx0XHRcdFx0YXR0YWNobWVudHMgPSBbXTtcblxuXHRcdFx0aWYgKCBhcnR3b3JrSWQgKSB7XG5cdFx0XHRcdGF0dGFjaG1lbnRzLnB1c2goIEF0dGFjaG1lbnQuZ2V0KCBhcnR3b3JrSWQgKSApO1xuXHRcdFx0XHRhdHRhY2htZW50c1swXS5mZXRjaCgpO1xuXHRcdFx0fVxuXG5cdFx0XHRzZWxlY3Rpb24ucmVzZXQoIGF0dGFjaG1lbnRzICk7XG5cdFx0fSk7XG5cblx0XHQvLyBTZXQgdGhlIG1vZGVsJ3MgYXJ0d29yayBJRCBhbmQgdXJsIHByb3BlcnRpZXMuXG5cdFx0ZnJhbWUuc3RhdGUoICdsaWJyYXJ5JyApLm9uKCAnc2VsZWN0JywgZnVuY3Rpb24oKSB7XG4gICAgICAgICAgICB2YXIgYXR0YWNobWVudCA9IHRoaXMuZ2V0KCAnc2VsZWN0aW9uJyApLmZpcnN0KCkudG9KU09OKCk7XG5cblx0XHRcdHdvcmtmbG93Lm1vZGVsLnNldCh7XG5cdFx0XHRcdGFydHdvcmtJZDogYXR0YWNobWVudC5pZCxcblx0XHRcdFx0YXJ0d29ya1VybDogYXR0YWNobWVudC51cmxcblx0XHRcdH0pO1xuXHRcdH0pO1xuXG5cdFx0cmV0dXJuIGZyYW1lO1xuICAgIH1cblxufTtcblxubW9kdWxlLmV4cG9ydHMgPSBXb3JrZmxvd3M7XG4iXX0=
