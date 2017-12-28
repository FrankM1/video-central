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

},{"../models/video":2}],2:[function(require,module,exports){
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

},{}],3:[function(require,module,exports){
(function (global){
var video_central = (typeof window !== "undefined" ? window['video_central'] : typeof global !== "undefined" ? global['video_central'] : null);
var wp = (typeof window !== "undefined" ? window['wp'] : typeof global !== "undefined" ? global['wp'] : null);

video_central.data = videoCentralPlaylistConfig;
video_central.settings( videoCentralPlaylistConfig );

wp.media.view.settings.post.id = video_central.data.postId;
wp.media.view.settings.defaultProps = {};

video_central.model.Video = require( './models/video' );
video_central.model.Videos = require( './collections/videos' );

video_central.view.MediaFrame = require( './views/media-frame' );
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

},{"./collections/videos":1,"./models/video":2,"./views/button/add-videos":4,"./views/media-frame":5,"./views/post-form":6,"./views/video":8,"./views/video-list":7,"./views/video/artwork":9,"./views/video/audio":10,"./workflows":11}],4:[function(require,module,exports){
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

},{"../../workflows":11}],5:[function(require,module,exports){
(function (global){
var MediaFrame,
	_ = (typeof window !== "undefined" ? window['_'] : typeof global !== "undefined" ? global['_'] : null),
	l10n = (typeof window !== "undefined" ? window['video_central'] : typeof global !== "undefined" ? global['video_central'] : null).l10n,
	wp = (typeof window !== "undefined" ? window['wp'] : typeof global !== "undefined" ? global['wp'] : null);

MediaFrame = wp.media.view.MediaFrame.Post.extend({
	createStates: function() {
		var options = this.options;

		// Add the default states.
		this.states.add([
			// Main states.
			new wp.media.controller.Library({
				id: 'insert',
				title: this.options.title,
				priority: 20,
				toolbar: 'main-insert',
				filterable: 'uploaded',
				library: wp.media.query( options.library ),
				multiple: options.multiple ? 'reset' : false,
				editable: false,

				// If the user isn't allowed to edit fields,
				// can they still edit it locally?
				allowLocalEdits: true,

				// Show the attachment display settings.
				displaySettings: false,
				// Update user settings when users adjust the
				// attachment display settings.
				displayUserSettings: false
			}),

			// Embed states.
			new wp.media.controller.Embed({
				title: l10n.addFromUrl,
				menuItem: { text: l10n.addFromUrl, priority: 120 },
				type: 'link'
			})
		]);
	},

	bindHandlers: function() {
		wp.media.view.MediaFrame.Select.prototype.bindHandlers.apply( this, arguments );

		this.on( 'toolbar:create:main-insert', this.createToolbar, this );
		this.on( 'toolbar:create:main-embed', this.mainEmbedToolbar, this );

		var handlers = {
				menu: {
					'default': 'mainMenu'
				},

				content: {
					'embed': 'embedContent',
					'edit-selection': 'editSelectionContent'
				},

				toolbar: {
					'main-insert': 'mainInsertToolbar'
				}
			};

		_.each( handlers, function( regionHandlers, region ) {
			_.each( regionHandlers, function( callback, handler ) {
				this.on( region + ':render:' + handler, this[ callback ], this );
			}, this );
		}, this );
	},

	// Toolbars.
	mainInsertToolbar: function( view ) {
		var controller = this;

		this.selectionStatusToolbar( view );

		view.set( 'insert', {
			style: 'primary',
			priority: 80,
			text: controller.options.button.text,
			requires: {
				selection: true
			},
			click: function() {
				var state = controller.state(),
					selection = state.get( 'selection' );

				controller.close();
				state.trigger( 'insert', selection ).reset();
			}
		});
	},

	mainEmbedToolbar: function( toolbar ) {
		toolbar.view = new wp.media.view.Toolbar.Embed({
			controller: this,
			text: this.options.button.text
		});
	}
});

module.exports = MediaFrame;

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{}],6:[function(require,module,exports){
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

},{"./button/add-videos":4,"./video-list":7}],7:[function(require,module,exports){
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

},{"./video":8}],8:[function(require,module,exports){
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

},{"./video/artwork":9,"./video/audio":10}],9:[function(require,module,exports){
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

},{"../../workflows":11}],10:[function(require,module,exports){
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

},{"../../workflows":11}],11:[function(require,module,exports){
(function (global){
var Workflows,
	_ = (typeof window !== "undefined" ? window['_'] : typeof global !== "undefined" ? global['_'] : null),
	video_central = (typeof window !== "undefined" ? window['video_central'] : typeof global !== "undefined" ? global['video_central'] : null),
	l10n = (typeof window !== "undefined" ? window['video_central'] : typeof global !== "undefined" ? global['video_central'] : null).l10n,
	MediaFrame = require( './views/media-frame' ),
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
		frame = new MediaFrame({
			title: l10n.workflows.addVideos.frameTitle,
			library: {
				type: 'audio'
			},
			button: {
				text: l10n.workflows.addVideos.frameButtonText
			},
			multiple: 'add'
		});

		// Set the extensions that can be uploaded.
		frame.uploader.options.uploader.plupload = {
			filters: {
				mime_types: [{
					title: l10n.workflows.addVideos.fileTypes,
					extensions: 'm4a,mp3,ogg,wma'
				}]
			}
		};

		// Prevent the Embed controller scanner from changing the state.
		frame.state( 'embed' ).props.off( 'change:url', frame.state( 'embed' ).debouncedScan );

		// Insert each selected attachment as a new video model.
		frame.state( 'insert' ).on( 'insert', function( selection ) {
			_.each( selection.models, function( attachment ) {
                video_central.videos.push( attachment.toJSON() );
            });
        });
        
		// Insert the embed data as a new model.
		frame.state( 'embed' ).on( 'select', function() {

			var embed = this.props.toJSON(),
                video = {
					videoId: '',
					audioUrl: embed.url
				};

			if ( ( 'title' in embed ) && '' !== embed.title ) {
				video.title = embed.title;
			}

			video_central.videos.push( video );
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
	},

	/**
	 * Workflow for selecting video audio.
	 *
	 * @param {Object} frame
	 */
	_selectAudio: function( frame ) {
		var workflow = this;

		// Return the existing frame for this workflow.
		if ( frame ) {
			return frame;
		}

		// Initialize the audio frame.
		frame = new MediaFrame({
			title: l10n.workflows.selectAudio.frameTitle,
			library: {
				type: 'audio'
			},
			button: {
				text: l10n.workflows.selectAudio.frameButtonText
			},
			multiple: false
		});

		// Set the extensions that can be uploaded.
		frame.uploader.options.uploader.plupload = {
			filters: {
				mime_types: [{
					title: l10n.workflows.selectAudio.fileTypes,
					extensions: 'm4a,mp3,ogg,wma'
				}]
			}
		};

		// Prevent the Embed controller scanner from changing the state.
		frame.state( 'embed' ).props.off( 'change:url', frame.state( 'embed' ).debouncedScan );

		// Set the frame state when opening it.
		frame.on( 'open', function() {
			var selection = this.get( 'insert' ).get( 'selection' ),
				videoId = workflow.model.get( 'videoId' ),
				audioUrl = workflow.model.get( 'audioUrl' ),
				isEmbed = audioUrl && ! videoId,
				attachments = [];

			// Automatically select the existing audio file if possible.
			if ( videoId ) {
				attachments.push( Attachment.get( videoId ) );
				attachments[0].fetch();
			}

			selection.reset( attachments );

			// Set the embed state properties.
			if ( isEmbed ) {
				this.get( 'embed' ).props.set({
					url: audioUrl,
					title: workflow.model.get( 'title' )
				});
			} else {
				this.get( 'embed' ).props.set({
					url: '',
					title: ''
				});
			}

			// Set the state to 'embed' if the model has an audio URL but
			// not a corresponding attachment ID.
			frame.setState( isEmbed ? 'embed' : 'insert' );
		});

		// Copy data from the selected attachment to the current model.
		frame.state( 'insert' ).on( 'insert', function( selection ) {
			var attachment = selection.first().toJSON().video_central,
				data = {},
				keys = _.keys( workflow.model.attributes );

			// Attributes that shouldn't be updated when inserting an
			// audio attachment.
			_.without( keys, [ 'id', 'order' ] );

			// Update these attributes if they're empty.
			// They shouldn't overwrite any data entered by the user.
			_.each( keys, function( key ) {
				var value = workflow.model.get( key );

				if ( ! value && ( key in attachment ) && value !== attachment[ key ] ) {
					data[ key ] = attachment[ key ];
				}
			});

			// Attributes that should always be replaced.
			data.videoId  = attachment.videoId;
			data.audioUrl = attachment.audioUrl;

			workflow.model.set( data );
		});

		// Copy the embed data to the current model.
		frame.state( 'embed' ).on( 'select', function() {
			var embed = this.props.toJSON(),
				data = {};

			data.videoId  = '';
			data.audioUrl = embed.url;

			if ( ( 'title' in embed ) && '' !== embed.title ) {
				data.title = embed.title;
			}

			workflow.model.set( data );
		});

		// Remove an empty model if the frame is escaped.
		frame.on( 'escape', function() {
			var model = workflow.model.toJSON();

			if ( ! model.artworkUrl && ! model.audioUrl ) {
				workflow.model.destroy();
			}
		});

		return frame;
	}
};

module.exports = Workflows;

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{"./views/media-frame":5}]},{},[3])
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJhc3NldHMvYWRtaW4vanMvc291cmNlL3BsYXlsaXN0L2NvbGxlY3Rpb25zL3ZpZGVvcy5qcyIsImFzc2V0cy9hZG1pbi9qcy9zb3VyY2UvcGxheWxpc3QvbW9kZWxzL3ZpZGVvLmpzIiwiYXNzZXRzL2FkbWluL2pzL3NvdXJjZS9wbGF5bGlzdC9wbGF5bGlzdC1lZGl0LmpzIiwiYXNzZXRzL2FkbWluL2pzL3NvdXJjZS9wbGF5bGlzdC92aWV3cy9idXR0b24vYWRkLXZpZGVvcy5qcyIsImFzc2V0cy9hZG1pbi9qcy9zb3VyY2UvcGxheWxpc3Qvdmlld3MvbWVkaWEtZnJhbWUuanMiLCJhc3NldHMvYWRtaW4vanMvc291cmNlL3BsYXlsaXN0L3ZpZXdzL3Bvc3QtZm9ybS5qcyIsImFzc2V0cy9hZG1pbi9qcy9zb3VyY2UvcGxheWxpc3Qvdmlld3MvdmlkZW8tbGlzdC5qcyIsImFzc2V0cy9hZG1pbi9qcy9zb3VyY2UvcGxheWxpc3Qvdmlld3MvdmlkZW8uanMiLCJhc3NldHMvYWRtaW4vanMvc291cmNlL3BsYXlsaXN0L3ZpZXdzL3ZpZGVvL2FydHdvcmsuanMiLCJhc3NldHMvYWRtaW4vanMvc291cmNlL3BsYXlsaXN0L3ZpZXdzL3ZpZGVvL2F1ZGlvLmpzIiwiYXNzZXRzL2FkbWluL2pzL3NvdXJjZS9wbGF5bGlzdC93b3JrZmxvd3MuanMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7O0FDQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOzs7OztBQ3RDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7Ozs7QUNsQkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOzs7OztBQ25DQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOzs7OztBQ2xDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7Ozs7QUN0R0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOzs7OztBQ3JEQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7O0FDckRBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7O0FDaEhBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7O0FDL0JBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7Ozs7QUN2RUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBIiwiZmlsZSI6ImdlbmVyYXRlZC5qcyIsInNvdXJjZVJvb3QiOiIiLCJzb3VyY2VzQ29udGVudCI6WyIoZnVuY3Rpb24gZSh0LG4scil7ZnVuY3Rpb24gcyhvLHUpe2lmKCFuW29dKXtpZighdFtvXSl7dmFyIGE9dHlwZW9mIHJlcXVpcmU9PVwiZnVuY3Rpb25cIiYmcmVxdWlyZTtpZighdSYmYSlyZXR1cm4gYShvLCEwKTtpZihpKXJldHVybiBpKG8sITApO3ZhciBmPW5ldyBFcnJvcihcIkNhbm5vdCBmaW5kIG1vZHVsZSAnXCIrbytcIidcIik7dGhyb3cgZi5jb2RlPVwiTU9EVUxFX05PVF9GT1VORFwiLGZ9dmFyIGw9bltvXT17ZXhwb3J0czp7fX07dFtvXVswXS5jYWxsKGwuZXhwb3J0cyxmdW5jdGlvbihlKXt2YXIgbj10W29dWzFdW2VdO3JldHVybiBzKG4/bjplKX0sbCxsLmV4cG9ydHMsZSx0LG4scil9cmV0dXJuIG5bb10uZXhwb3J0c312YXIgaT10eXBlb2YgcmVxdWlyZT09XCJmdW5jdGlvblwiJiZyZXF1aXJlO2Zvcih2YXIgbz0wO288ci5sZW5ndGg7bysrKXMocltvXSk7cmV0dXJuIHN9KSIsInZhciBWaWRlb3MsXG5cdF8gPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snXyddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnXyddIDogbnVsbCksXG5cdEJhY2tib25lID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ0JhY2tib25lJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWydCYWNrYm9uZSddIDogbnVsbCksXG5cdHNldHRpbmdzID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ3ZpZGVvX2NlbnRyYWwnXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ3ZpZGVvX2NlbnRyYWwnXSA6IG51bGwpLnNldHRpbmdzKCksXG5cdFZpZGVvID0gcmVxdWlyZSggJy4uL21vZGVscy92aWRlbycgKSxcblx0d3AgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snd3AnXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ3dwJ10gOiBudWxsKTtcblxuVmlkZW9zID0gQmFja2JvbmUuQ29sbGVjdGlvbi5leHRlbmQoe1xuXHRtb2RlbDogVmlkZW8sXG5cblx0Y29tcGFyYXRvcjogZnVuY3Rpb24oIHZpZGVvICkge1xuXHRcdHJldHVybiBwYXJzZUludCggdmlkZW8uZ2V0KCAnb3JkZXInICksIDEwICk7XG5cdH0sXG5cblx0ZmV0Y2g6IGZ1bmN0aW9uKCkge1xuXHRcdHZhciBjb2xsZWN0aW9uID0gdGhpcztcblxuXHRcdHJldHVybiB3cC5hamF4LnBvc3QoICd2aWRlb19jZW50cmFsX2dldF9wbGF5bGlzdF92aWRlb3MnLCB7XG5cdFx0XHRwb3N0X2lkOiBzZXR0aW5ncy5wb3N0SWRcblx0XHR9KS5kb25lKGZ1bmN0aW9uKCB2aWRlb3MgKSB7XG5cdFx0XHRjb2xsZWN0aW9uLnJlc2V0KCB2aWRlb3MgKTtcblx0XHR9KTtcblx0fSxcblxuXHRzYXZlOiBmdW5jdGlvbiggZGF0YSApIHtcblx0XHR0aGlzLnNvcnQoKTtcblxuXHRcdGRhdGEgPSBfLmV4dGVuZCh7fSwgZGF0YSwge1xuXHRcdFx0cG9zdF9pZDogc2V0dGluZ3MucG9zdElkLFxuXHRcdFx0dmlkZW9zOiB0aGlzLnRvSlNPTigpLFxuXHRcdFx0bm9uY2U6IHNldHRpbmdzLnNhdmVOb25jZVxuXHRcdH0pO1xuXG5cdFx0cmV0dXJuIHdwLmFqYXgucG9zdCggJ3ZpZGVvX2NlbnRyYWxfc2F2ZV9wbGF5bGlzdF92aWRlb3MnLCBkYXRhICk7XG5cdH1cbn0pO1xuXG5tb2R1bGUuZXhwb3J0cyA9IFZpZGVvcztcbiIsInZhciBWaWRlbyxcblx0QmFja2JvbmUgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snQmFja2JvbmUnXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ0JhY2tib25lJ10gOiBudWxsKTtcblxuVmlkZW8gPSBCYWNrYm9uZS5Nb2RlbC5leHRlbmQoe1xuXHRkZWZhdWx0czoge1xuXHRcdGFydGlzdDogJycsXG5cdFx0YXJ0d29ya0lkOiAnJyxcblx0XHRhcnR3b3JrVXJsOiAnJyxcblx0XHR2aWRlb0lkOiAnJyxcblx0XHRhdWRpb1VybDogJycsXG5cdFx0Zm9ybWF0OiAnJyxcblx0XHRsZW5ndGg6ICcnLFxuXHRcdHRpdGxlOiAnJyxcblx0XHRvcmRlcjogMFxuXHR9XG59KTtcblxubW9kdWxlLmV4cG9ydHMgPSBWaWRlbztcbiIsInZhciB2aWRlb19jZW50cmFsID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ3ZpZGVvX2NlbnRyYWwnXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ3ZpZGVvX2NlbnRyYWwnXSA6IG51bGwpO1xudmFyIHdwID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ3dwJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWyd3cCddIDogbnVsbCk7XG5cbnZpZGVvX2NlbnRyYWwuZGF0YSA9IHZpZGVvQ2VudHJhbFBsYXlsaXN0Q29uZmlnO1xudmlkZW9fY2VudHJhbC5zZXR0aW5ncyggdmlkZW9DZW50cmFsUGxheWxpc3RDb25maWcgKTtcblxud3AubWVkaWEudmlldy5zZXR0aW5ncy5wb3N0LmlkID0gdmlkZW9fY2VudHJhbC5kYXRhLnBvc3RJZDtcbndwLm1lZGlhLnZpZXcuc2V0dGluZ3MuZGVmYXVsdFByb3BzID0ge307XG5cbnZpZGVvX2NlbnRyYWwubW9kZWwuVmlkZW8gPSByZXF1aXJlKCAnLi9tb2RlbHMvdmlkZW8nICk7XG52aWRlb19jZW50cmFsLm1vZGVsLlZpZGVvcyA9IHJlcXVpcmUoICcuL2NvbGxlY3Rpb25zL3ZpZGVvcycgKTtcblxudmlkZW9fY2VudHJhbC52aWV3Lk1lZGlhRnJhbWUgPSByZXF1aXJlKCAnLi92aWV3cy9tZWRpYS1mcmFtZScgKTtcbnZpZGVvX2NlbnRyYWwudmlldy5Qb3N0Rm9ybSA9IHJlcXVpcmUoICcuL3ZpZXdzL3Bvc3QtZm9ybScgKTtcbnZpZGVvX2NlbnRyYWwudmlldy5BZGRWaWRlb3NCdXR0b24gPSByZXF1aXJlKCAnLi92aWV3cy9idXR0b24vYWRkLXZpZGVvcycgKTtcbnZpZGVvX2NlbnRyYWwudmlldy5WaWRlb0xpc3QgPSByZXF1aXJlKCAnLi92aWV3cy92aWRlby1saXN0JyApO1xudmlkZW9fY2VudHJhbC52aWV3LlZpZGVvID0gcmVxdWlyZSggJy4vdmlld3MvdmlkZW8nICk7XG52aWRlb19jZW50cmFsLnZpZXcuVmlkZW9BcnR3b3JrID0gcmVxdWlyZSggJy4vdmlld3MvdmlkZW8vYXJ0d29yaycgKTtcbnZpZGVvX2NlbnRyYWwudmlldy5WaWRlb0F1ZGlvID0gcmVxdWlyZSggJy4vdmlld3MvdmlkZW8vYXVkaW8nICk7XG5cbnZpZGVvX2NlbnRyYWwud29ya2Zsb3dzID0gcmVxdWlyZSggJy4vd29ya2Zsb3dzJyApO1xuXG4oIGZ1bmN0aW9uKCAkICkge1xuICAgIHZhciB2aWRlb3M7XG5cblx0dmlkZW9zID0gdmlkZW9fY2VudHJhbC52aWRlb3MgPSBuZXcgdmlkZW9fY2VudHJhbC5tb2RlbC5WaWRlb3MoIHZpZGVvX2NlbnRyYWwuZGF0YS52aWRlb3MgKTtcblx0ZGVsZXRlIHZpZGVvX2NlbnRyYWwuZGF0YS52aWRlb3M7XG5cblx0dmFyIHBvc3RGb3JtID0gbmV3IHZpZGVvX2NlbnRyYWwudmlldy5Qb3N0Rm9ybSh7XG5cdFx0Y29sbGVjdGlvbjogdmlkZW9zLFxuXHRcdGwxMG46IHZpZGVvX2NlbnRyYWwubDEwblxuICAgIH0pO1xuICAgIFxufSAoIGpRdWVyeSApKTtcblxuIiwidmFyIEFkZFZpZGVvc0J1dHRvbixcblx0JCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93WydqUXVlcnknXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ2pRdWVyeSddIDogbnVsbCksXG5cdHdvcmtmbG93cyA9IHJlcXVpcmUoICcuLi8uLi93b3JrZmxvd3MnICksXG5cdHdwID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ3dwJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWyd3cCddIDogbnVsbCk7XG5cbkFkZFZpZGVvc0J1dHRvbiA9IHdwLkJhY2tib25lLlZpZXcuZXh0ZW5kKHtcblx0aWQ6ICdhZGQtdmlkZW9zJyxcblx0dGFnTmFtZTogJ3AnLFxuXG5cdGV2ZW50czoge1xuXHRcdCdjbGljayAuYnV0dG9uJzogJ2NsaWNrJ1xuXHR9LFxuXG5cdGluaXRpYWxpemU6IGZ1bmN0aW9uKCBvcHRpb25zICkge1xuXHRcdHRoaXMubDEwbiA9IG9wdGlvbnMubDEwbjtcblx0fSxcblxuXHRyZW5kZXI6IGZ1bmN0aW9uKCkge1xuXHRcdHZhciAkYnV0dG9uID0gJCggJzxhIC8+Jywge1xuXHRcdFx0dGV4dDogdGhpcy5sMTBuLmFkZFZpZGVvc1xuXHRcdH0pLmFkZENsYXNzKCAnYnV0dG9uIGJ1dHRvbi1zZWNvbmRhcnknICk7XG5cblx0XHR0aGlzLiRlbC5odG1sKCAkYnV0dG9uICk7XG5cblx0XHRyZXR1cm4gdGhpcztcblx0fSxcblxuXHRjbGljazogZnVuY3Rpb24oIGUgKSB7XG5cdFx0ZS5wcmV2ZW50RGVmYXVsdCgpO1xuXHRcdHdvcmtmbG93cy5nZXQoICdhZGRWaWRlb3MnICkub3BlbigpO1xuXHR9XG59KTtcblxubW9kdWxlLmV4cG9ydHMgPSBBZGRWaWRlb3NCdXR0b247XG4iLCJ2YXIgTWVkaWFGcmFtZSxcblx0XyA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93WydfJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWydfJ10gOiBudWxsKSxcblx0bDEwbiA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93Wyd2aWRlb19jZW50cmFsJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWyd2aWRlb19jZW50cmFsJ10gOiBudWxsKS5sMTBuLFxuXHR3cCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93Wyd3cCddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnd3AnXSA6IG51bGwpO1xuXG5NZWRpYUZyYW1lID0gd3AubWVkaWEudmlldy5NZWRpYUZyYW1lLlBvc3QuZXh0ZW5kKHtcblx0Y3JlYXRlU3RhdGVzOiBmdW5jdGlvbigpIHtcblx0XHR2YXIgb3B0aW9ucyA9IHRoaXMub3B0aW9ucztcblxuXHRcdC8vIEFkZCB0aGUgZGVmYXVsdCBzdGF0ZXMuXG5cdFx0dGhpcy5zdGF0ZXMuYWRkKFtcblx0XHRcdC8vIE1haW4gc3RhdGVzLlxuXHRcdFx0bmV3IHdwLm1lZGlhLmNvbnRyb2xsZXIuTGlicmFyeSh7XG5cdFx0XHRcdGlkOiAnaW5zZXJ0Jyxcblx0XHRcdFx0dGl0bGU6IHRoaXMub3B0aW9ucy50aXRsZSxcblx0XHRcdFx0cHJpb3JpdHk6IDIwLFxuXHRcdFx0XHR0b29sYmFyOiAnbWFpbi1pbnNlcnQnLFxuXHRcdFx0XHRmaWx0ZXJhYmxlOiAndXBsb2FkZWQnLFxuXHRcdFx0XHRsaWJyYXJ5OiB3cC5tZWRpYS5xdWVyeSggb3B0aW9ucy5saWJyYXJ5ICksXG5cdFx0XHRcdG11bHRpcGxlOiBvcHRpb25zLm11bHRpcGxlID8gJ3Jlc2V0JyA6IGZhbHNlLFxuXHRcdFx0XHRlZGl0YWJsZTogZmFsc2UsXG5cblx0XHRcdFx0Ly8gSWYgdGhlIHVzZXIgaXNuJ3QgYWxsb3dlZCB0byBlZGl0IGZpZWxkcyxcblx0XHRcdFx0Ly8gY2FuIHRoZXkgc3RpbGwgZWRpdCBpdCBsb2NhbGx5P1xuXHRcdFx0XHRhbGxvd0xvY2FsRWRpdHM6IHRydWUsXG5cblx0XHRcdFx0Ly8gU2hvdyB0aGUgYXR0YWNobWVudCBkaXNwbGF5IHNldHRpbmdzLlxuXHRcdFx0XHRkaXNwbGF5U2V0dGluZ3M6IGZhbHNlLFxuXHRcdFx0XHQvLyBVcGRhdGUgdXNlciBzZXR0aW5ncyB3aGVuIHVzZXJzIGFkanVzdCB0aGVcblx0XHRcdFx0Ly8gYXR0YWNobWVudCBkaXNwbGF5IHNldHRpbmdzLlxuXHRcdFx0XHRkaXNwbGF5VXNlclNldHRpbmdzOiBmYWxzZVxuXHRcdFx0fSksXG5cblx0XHRcdC8vIEVtYmVkIHN0YXRlcy5cblx0XHRcdG5ldyB3cC5tZWRpYS5jb250cm9sbGVyLkVtYmVkKHtcblx0XHRcdFx0dGl0bGU6IGwxMG4uYWRkRnJvbVVybCxcblx0XHRcdFx0bWVudUl0ZW06IHsgdGV4dDogbDEwbi5hZGRGcm9tVXJsLCBwcmlvcml0eTogMTIwIH0sXG5cdFx0XHRcdHR5cGU6ICdsaW5rJ1xuXHRcdFx0fSlcblx0XHRdKTtcblx0fSxcblxuXHRiaW5kSGFuZGxlcnM6IGZ1bmN0aW9uKCkge1xuXHRcdHdwLm1lZGlhLnZpZXcuTWVkaWFGcmFtZS5TZWxlY3QucHJvdG90eXBlLmJpbmRIYW5kbGVycy5hcHBseSggdGhpcywgYXJndW1lbnRzICk7XG5cblx0XHR0aGlzLm9uKCAndG9vbGJhcjpjcmVhdGU6bWFpbi1pbnNlcnQnLCB0aGlzLmNyZWF0ZVRvb2xiYXIsIHRoaXMgKTtcblx0XHR0aGlzLm9uKCAndG9vbGJhcjpjcmVhdGU6bWFpbi1lbWJlZCcsIHRoaXMubWFpbkVtYmVkVG9vbGJhciwgdGhpcyApO1xuXG5cdFx0dmFyIGhhbmRsZXJzID0ge1xuXHRcdFx0XHRtZW51OiB7XG5cdFx0XHRcdFx0J2RlZmF1bHQnOiAnbWFpbk1lbnUnXG5cdFx0XHRcdH0sXG5cblx0XHRcdFx0Y29udGVudDoge1xuXHRcdFx0XHRcdCdlbWJlZCc6ICdlbWJlZENvbnRlbnQnLFxuXHRcdFx0XHRcdCdlZGl0LXNlbGVjdGlvbic6ICdlZGl0U2VsZWN0aW9uQ29udGVudCdcblx0XHRcdFx0fSxcblxuXHRcdFx0XHR0b29sYmFyOiB7XG5cdFx0XHRcdFx0J21haW4taW5zZXJ0JzogJ21haW5JbnNlcnRUb29sYmFyJ1xuXHRcdFx0XHR9XG5cdFx0XHR9O1xuXG5cdFx0Xy5lYWNoKCBoYW5kbGVycywgZnVuY3Rpb24oIHJlZ2lvbkhhbmRsZXJzLCByZWdpb24gKSB7XG5cdFx0XHRfLmVhY2goIHJlZ2lvbkhhbmRsZXJzLCBmdW5jdGlvbiggY2FsbGJhY2ssIGhhbmRsZXIgKSB7XG5cdFx0XHRcdHRoaXMub24oIHJlZ2lvbiArICc6cmVuZGVyOicgKyBoYW5kbGVyLCB0aGlzWyBjYWxsYmFjayBdLCB0aGlzICk7XG5cdFx0XHR9LCB0aGlzICk7XG5cdFx0fSwgdGhpcyApO1xuXHR9LFxuXG5cdC8vIFRvb2xiYXJzLlxuXHRtYWluSW5zZXJ0VG9vbGJhcjogZnVuY3Rpb24oIHZpZXcgKSB7XG5cdFx0dmFyIGNvbnRyb2xsZXIgPSB0aGlzO1xuXG5cdFx0dGhpcy5zZWxlY3Rpb25TdGF0dXNUb29sYmFyKCB2aWV3ICk7XG5cblx0XHR2aWV3LnNldCggJ2luc2VydCcsIHtcblx0XHRcdHN0eWxlOiAncHJpbWFyeScsXG5cdFx0XHRwcmlvcml0eTogODAsXG5cdFx0XHR0ZXh0OiBjb250cm9sbGVyLm9wdGlvbnMuYnV0dG9uLnRleHQsXG5cdFx0XHRyZXF1aXJlczoge1xuXHRcdFx0XHRzZWxlY3Rpb246IHRydWVcblx0XHRcdH0sXG5cdFx0XHRjbGljazogZnVuY3Rpb24oKSB7XG5cdFx0XHRcdHZhciBzdGF0ZSA9IGNvbnRyb2xsZXIuc3RhdGUoKSxcblx0XHRcdFx0XHRzZWxlY3Rpb24gPSBzdGF0ZS5nZXQoICdzZWxlY3Rpb24nICk7XG5cblx0XHRcdFx0Y29udHJvbGxlci5jbG9zZSgpO1xuXHRcdFx0XHRzdGF0ZS50cmlnZ2VyKCAnaW5zZXJ0Jywgc2VsZWN0aW9uICkucmVzZXQoKTtcblx0XHRcdH1cblx0XHR9KTtcblx0fSxcblxuXHRtYWluRW1iZWRUb29sYmFyOiBmdW5jdGlvbiggdG9vbGJhciApIHtcblx0XHR0b29sYmFyLnZpZXcgPSBuZXcgd3AubWVkaWEudmlldy5Ub29sYmFyLkVtYmVkKHtcblx0XHRcdGNvbnRyb2xsZXI6IHRoaXMsXG5cdFx0XHR0ZXh0OiB0aGlzLm9wdGlvbnMuYnV0dG9uLnRleHRcblx0XHR9KTtcblx0fVxufSk7XG5cbm1vZHVsZS5leHBvcnRzID0gTWVkaWFGcmFtZTtcbiIsInZhciBQb3N0Rm9ybSxcblx0JCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93WydqUXVlcnknXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ2pRdWVyeSddIDogbnVsbCksXG5cdEFkZFZpZGVvc0J1dHRvbiA9IHJlcXVpcmUoICcuL2J1dHRvbi9hZGQtdmlkZW9zJyApLFxuXHRWaWRlb0xpc3QgPSByZXF1aXJlKCAnLi92aWRlby1saXN0JyApLFxuXHR3cCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93Wyd3cCddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnd3AnXSA6IG51bGwpO1xuXG5Qb3N0Rm9ybSA9IHdwLkJhY2tib25lLlZpZXcuZXh0ZW5kKHtcblx0ZWw6ICcjcG9zdCcsXG5cdHNhdmVkOiBmYWxzZSxcblxuXHRldmVudHM6IHtcblx0XHQnY2xpY2sgI3B1Ymxpc2gnOiAnYnV0dG9uQ2xpY2snLFxuXHRcdCdjbGljayAjc2F2ZS1wb3N0JzogJ2J1dHRvbkNsaWNrJ1xuXHRcdC8vJ3N1Ym1pdCc6ICdzdWJtaXQnXG5cdH0sXG5cblx0aW5pdGlhbGl6ZTogZnVuY3Rpb24oIG9wdGlvbnMgKSB7XG5cdFx0dGhpcy5sMTBuID0gb3B0aW9ucy5sMTBuO1xuXG5cdFx0dGhpcy5yZW5kZXIoKTtcblx0fSxcblxuXHRyZW5kZXI6IGZ1bmN0aW9uKCkge1xuXHRcdHRoaXMudmlld3MuYWRkKCAnI3ZpZGVvLWNlbnRyYWwtcGxheWxpc3QtcGxheWxpc3QtZWRpdG9yIC52aWRlby1jZW50cmFsLXBsYXlsaXN0LXBhbmVsLWJvZHknLCBbXG5cdFx0XHRuZXcgQWRkVmlkZW9zQnV0dG9uKHtcblx0XHRcdFx0Y29sbGVjdGlvbjogdGhpcy5jb2xsZWN0aW9uLFxuXHRcdFx0XHRsMTBuOiB0aGlzLmwxMG5cblx0XHRcdH0pLFxuXG5cdFx0XHRuZXcgVmlkZW9MaXN0KHtcblx0XHRcdFx0Y29sbGVjdGlvbjogdGhpcy5jb2xsZWN0aW9uXG5cdFx0XHR9KVxuXHRcdF0pO1xuXG5cdFx0cmV0dXJuIHRoaXM7XG5cdH0sXG5cblx0YnV0dG9uQ2xpY2s6IGZ1bmN0aW9uKCBlICkge1xuXHRcdHZhciBzZWxmID0gdGhpcyxcblx0XHRcdCRidXR0b24gPSAkKCBlLnRhcmdldCApO1xuXG5cdFx0aWYgKCAhIHNlbGYuc2F2ZWQgKSB7XG5cdFx0XHR0aGlzLmNvbGxlY3Rpb24uc2F2ZSgpLmRvbmUoZnVuY3Rpb24oIGRhdGEgKSB7XG5cdFx0XHRcdHNlbGYuc2F2ZWQgPSB0cnVlO1xuXHRcdFx0XHQkYnV0dG9uLmNsaWNrKCk7XG5cdFx0XHR9KTtcblx0XHR9XG5cblx0XHRyZXR1cm4gc2VsZi5zYXZlZDtcblx0fVxufSk7XG5cbm1vZHVsZS5leHBvcnRzID0gUG9zdEZvcm07XG4iLCJ2YXIgVmlkZW9MaXN0LFxuXHQkID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ2pRdWVyeSddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnalF1ZXJ5J10gOiBudWxsKSxcblx0XyA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93WydfJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWydfJ10gOiBudWxsKSxcblx0VmlkZW8gPSByZXF1aXJlKCAnLi92aWRlbycgKSxcblx0d3AgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snd3AnXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ3dwJ10gOiBudWxsKTtcblxuVmlkZW9MaXN0ID0gd3AuQmFja2JvbmUuVmlldy5leHRlbmQoe1xuXHRjbGFzc05hbWU6ICd2aWRlby1jZW50cmFsLXBsYXlsaXN0LXZpZGVvbGlzdCcsXG5cdHRhZ05hbWU6ICdvbCcsXG5cblx0aW5pdGlhbGl6ZTogZnVuY3Rpb24oKSB7XG5cdFx0dGhpcy5saXN0ZW5UbyggdGhpcy5jb2xsZWN0aW9uLCAnYWRkJywgdGhpcy5hZGRWaWRlbyApO1xuXHRcdHRoaXMubGlzdGVuVG8oIHRoaXMuY29sbGVjdGlvbiwgJ2FkZCByZW1vdmUnLCB0aGlzLnVwZGF0ZU9yZGVyICk7XG5cdFx0dGhpcy5saXN0ZW5UbyggdGhpcy5jb2xsZWN0aW9uLCAncmVzZXQnLCB0aGlzLnJlbmRlciApO1xuXHR9LFxuXG5cdHJlbmRlcjogZnVuY3Rpb24oKSB7XG5cdFx0dGhpcy4kZWwuZW1wdHkoKTtcblxuXHRcdHRoaXMuY29sbGVjdGlvbi5lYWNoKCB0aGlzLmFkZFZpZGVvLCB0aGlzICk7XG5cdFx0dGhpcy51cGRhdGVPcmRlcigpO1xuXG5cdFx0dGhpcy4kZWwuc29ydGFibGUoIHtcblx0XHRcdGF4aXM6ICd5Jyxcblx0XHRcdGRlbGF5OiAxNTAsXG5cdFx0XHRmb3JjZUhlbHBlclNpemU6IHRydWUsXG5cdFx0XHRmb3JjZVBsYWNlaG9sZGVyU2l6ZTogdHJ1ZSxcblx0XHRcdG9wYWNpdHk6IDAuNixcblx0XHRcdHN0YXJ0OiBmdW5jdGlvbiggZSwgdWkgKSB7XG5cdFx0XHRcdHVpLnBsYWNlaG9sZGVyLmNzcyggJ3Zpc2liaWxpdHknLCAndmlzaWJsZScgKTtcblx0XHRcdH0sXG5cdFx0XHR1cGRhdGU6IF8uYmluZChmdW5jdGlvbiggZSwgdWkgKSB7XG5cdFx0XHRcdHRoaXMudXBkYXRlT3JkZXIoKTtcblx0XHRcdH0sIHRoaXMgKVxuXHRcdH0gKTtcblxuXHRcdHJldHVybiB0aGlzO1xuXHR9LFxuXG5cdGFkZFZpZGVvOiBmdW5jdGlvbiggdmlkZW8gKSB7XG5cdFx0dmFyIHZpZGVvVmlldyA9IG5ldyBWaWRlbyh7IG1vZGVsOiB2aWRlbyB9KTtcblx0XHR0aGlzLiRlbC5hcHBlbmQoIHZpZGVvVmlldy5yZW5kZXIoKS5lbCApO1xuXHR9LFxuXG5cdHVwZGF0ZU9yZGVyOiBmdW5jdGlvbigpIHtcblx0XHRfLmVhY2goIHRoaXMuJGVsLmZpbmQoICcudmlkZW8tY2VudHJhbC1wbGF5bGlzdC12aWRlbycgKSwgZnVuY3Rpb24oIGl0ZW0sIGkgKSB7XG5cdFx0XHR2YXIgY2lkID0gJCggaXRlbSApLmRhdGEoICdjaWQnICk7XG5cdFx0XHR0aGlzLmNvbGxlY3Rpb24uZ2V0KCBjaWQgKS5zZXQoICdvcmRlcicsIGkgKTtcblx0XHR9LCB0aGlzICk7XG5cdH1cbn0pO1xuXG5tb2R1bGUuZXhwb3J0cyA9IFZpZGVvTGlzdDtcbiIsInZhciBWaWRlbyxcblx0JCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93WydqUXVlcnknXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ2pRdWVyeSddIDogbnVsbCksXG5cdFZpZGVvQXJ0d29yayA9IHJlcXVpcmUoICcuL3ZpZGVvL2FydHdvcmsnICksXG5cdFZpZGVvQXVkaW8gPSByZXF1aXJlKCAnLi92aWRlby9hdWRpbycgKSxcblx0d3AgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snd3AnXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ3dwJ10gOiBudWxsKTtcblxuVmlkZW8gPSB3cC5CYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG5cdHRhZ05hbWU6ICdsaScsXG5cdGNsYXNzTmFtZTogJ3ZpZGVvLWNlbnRyYWwtcGxheWxpc3QtdmlkZW8nLFxuXHR0ZW1wbGF0ZTogd3AudGVtcGxhdGUoICd2aWRlby1jZW50cmFsLXBsYXlsaXN0LXBsYXlsaXN0LXZpZGVvJyApLFxuXG5cdGV2ZW50czoge1xuXHRcdCdjaGFuZ2UgW2RhdGEtc2V0dGluZ10nOiAndXBkYXRlQXR0cmlidXRlJyxcblx0XHQnY2xpY2sgLmpzLXRvZ2dsZSc6ICd0b2dnbGVPcGVuU3RhdHVzJyxcblx0XHQnZGJsY2xpY2sgLnZpZGVvLWNlbnRyYWwtcGxheWxpc3QtdmlkZW8tdGl0bGUnOiAndG9nZ2xlT3BlblN0YXR1cycsXG5cdFx0J2NsaWNrIC5qcy1jbG9zZSc6ICdtaW5pbWl6ZScsXG5cdFx0J2NsaWNrIC5qcy1yZW1vdmUnOiAnZGVzdHJveSdcblx0fSxcblxuXHRpbml0aWFsaXplOiBmdW5jdGlvbigpIHtcblx0XHR0aGlzLmxpc3RlblRvKCB0aGlzLm1vZGVsLCAnY2hhbmdlOnRpdGxlJywgdGhpcy51cGRhdGVUaXRsZSApO1xuXHRcdHRoaXMubGlzdGVuVG8oIHRoaXMubW9kZWwsICdjaGFuZ2UnLCB0aGlzLnVwZGF0ZUZpZWxkcyApO1xuXHRcdHRoaXMubGlzdGVuVG8oIHRoaXMubW9kZWwsICdkZXN0cm95JywgdGhpcy5yZW1vdmUgKTtcblx0fSxcblxuXHRyZW5kZXI6IGZ1bmN0aW9uKCkge1xuXHRcdHRoaXMuJGVsLmh0bWwoIHRoaXMudGVtcGxhdGUoIHRoaXMubW9kZWwudG9KU09OKCkgKSApLmRhdGEoICdjaWQnLCB0aGlzLm1vZGVsLmNpZCApO1xuXG5cdFx0dGhpcy52aWV3cy5hZGQoICcudmlkZW8tY2VudHJhbC1wbGF5bGlzdC12aWRlby1jb2x1bW4tYXJ0d29yaycsIG5ldyBWaWRlb0FydHdvcmsoe1xuXHRcdFx0bW9kZWw6IHRoaXMubW9kZWwsXG5cdFx0XHRwYXJlbnQ6IHRoaXNcblx0XHR9KSk7XG5cblx0XHR0aGlzLnZpZXdzLmFkZCggJy52aWRlby1jZW50cmFsLXBsYXlsaXN0LXZpZGVvLWF1ZGlvLWdyb3VwJywgbmV3IFZpZGVvQXVkaW8oe1xuXHRcdFx0bW9kZWw6IHRoaXMubW9kZWwsXG5cdFx0XHRwYXJlbnQ6IHRoaXNcblx0XHR9KSk7XG5cblx0XHRyZXR1cm4gdGhpcztcblx0fSxcblxuXHRtaW5pbWl6ZTogZnVuY3Rpb24oIGUgKSB7XG5cdFx0ZS5wcmV2ZW50RGVmYXVsdCgpO1xuXHRcdHRoaXMuJGVsLnJlbW92ZUNsYXNzKCAnaXMtb3BlbicgKS5maW5kKCAnaW5wdXQ6Zm9jdXMnICkuYmx1cigpO1xuXHR9LFxuXG5cdHRvZ2dsZU9wZW5TdGF0dXM6IGZ1bmN0aW9uKCBlICkge1xuXHRcdGUucHJldmVudERlZmF1bHQoKTtcblx0XHR0aGlzLiRlbC50b2dnbGVDbGFzcyggJ2lzLW9wZW4nICkuZmluZCggJ2lucHV0OmZvY3VzJyApLmJsdXIoKTtcblxuXHRcdC8vIFRyaWdnZXIgYSByZXNpemUgc28gdGhlIG1lZGlhIGVsZW1lbnQgd2lsbCBmaWxsIHRoZSBjb250YWluZXIuXG5cdFx0aWYgKCB0aGlzLiRlbC5oYXNDbGFzcyggJ2lzLW9wZW4nICkgKSB7XG5cdFx0XHQkKCB3aW5kb3cgKS50cmlnZ2VyKCAncmVzaXplJyApO1xuXHRcdH1cblx0fSxcblxuXHQvKipcblx0ICogVXBkYXRlIGEgbW9kZWwgYXR0cmlidXRlIHdoZW4gYSBmaWVsZCBpcyBjaGFuZ2VkLlxuXHQgKlxuXHQgKiBGaWVsZHMgd2l0aCBhICdkYXRhLXNldHRpbmc9XCJ7e2tleX19XCInIGF0dHJpYnV0ZSB3aG9zZSB2YWx1ZVxuXHQgKiBjb3JyZXNwb25kcyB0byBhIG1vZGVsIGF0dHJpYnV0ZSB3aWxsIGJlIGF1dG9tYXRpY2FsbHkgc3luY2VkLlxuXHQgKlxuXHQgKiBAcGFyYW0ge09iamVjdH0gZSBFdmVudCBvYmplY3QuXG5cdCAqL1xuXHR1cGRhdGVBdHRyaWJ1dGU6IGZ1bmN0aW9uKCBlICkge1xuXHRcdHZhciBhdHRyaWJ1dGUgPSAkKCBlLnRhcmdldCApLmRhdGEoICdzZXR0aW5nJyApLFxuXHRcdFx0dmFsdWUgPSBlLnRhcmdldC52YWx1ZTtcblxuXHRcdGlmICggdGhpcy5tb2RlbC5nZXQoIGF0dHJpYnV0ZSApICE9PSB2YWx1ZSApIHtcblx0XHRcdHRoaXMubW9kZWwuc2V0KCBhdHRyaWJ1dGUsIHZhbHVlICk7XG5cdFx0fVxuXHR9LFxuXG5cdC8qKlxuXHQgKiBVcGRhdGUgYSBzZXR0aW5nIGZpZWxkIHdoZW4gYSBtb2RlbCdzIGF0dHJpYnV0ZSBpcyBjaGFuZ2VkLlxuXHQgKi9cblx0dXBkYXRlRmllbGRzOiBmdW5jdGlvbigpIHtcblx0XHR2YXIgdmlkZW8gPSB0aGlzLm1vZGVsLnRvSlNPTigpLFxuXHRcdFx0JHNldHRpbmdzID0gdGhpcy4kZWwuZmluZCggJ1tkYXRhLXNldHRpbmddJyApLFxuXHRcdFx0YXR0cmlidXRlLCB2YWx1ZTtcblxuXHRcdC8vIEEgY2hhbmdlIGV2ZW50IHNob3VsZG4ndCBiZSB0cmlnZ2VyZWQgaGVyZSwgc28gaXQgd29uJ3QgY2F1c2Vcblx0XHQvLyB0aGUgbW9kZWwgYXR0cmlidXRlIHRvIGJlIHVwZGF0ZWQgYW5kIGdldCBzdHVjayBpbiBhblxuXHRcdC8vIGluZmluaXRlIGxvb3AuXG5cdFx0Zm9yICggYXR0cmlidXRlIGluIHZpZGVvICkge1xuXHRcdFx0Ly8gRGVjb2RlIEhUTUwgZW50aXRpZXMuXG5cdFx0XHR2YWx1ZSA9ICQoICc8ZGl2Lz4nICkuaHRtbCggdmlkZW9bIGF0dHJpYnV0ZSBdICkudGV4dCgpO1xuXHRcdFx0JHNldHRpbmdzLmZpbHRlciggJ1tkYXRhLXNldHRpbmc9XCInICsgYXR0cmlidXRlICsgJ1wiXScgKS52YWwoIHZhbHVlICk7XG5cdFx0fVxuXHR9LFxuXG5cdHVwZGF0ZVRpdGxlOiBmdW5jdGlvbigpIHtcblx0XHR2YXIgdGl0bGUgPSB0aGlzLm1vZGVsLmdldCggJ3RpdGxlJyApO1xuXHRcdHRoaXMuJGVsLmZpbmQoICcudmlkZW8tY2VudHJhbC1wbGF5bGlzdC12aWRlby10aXRsZSAudGV4dCcgKS50ZXh0KCB0aXRsZSA/IHRpdGxlIDogJ1RpdGxlJyApO1xuXHR9LFxuXG5cdC8qKlxuXHQgKiBEZXN0cm95IHRoZSB2aWV3J3MgbW9kZWwuXG5cdCAqXG5cdCAqIEF2b2lkIHN5bmNpbmcgdG8gdGhlIHNlcnZlciBieSB0cmlnZ2VyaW5nIGFuIGV2ZW50IGluc3RlYWQgb2Zcblx0ICogY2FsbGluZyBkZXN0cm95KCkgZGlyZWN0bHkgb24gdGhlIG1vZGVsLlxuXHQgKi9cblx0ZGVzdHJveTogZnVuY3Rpb24oKSB7XG5cdFx0dGhpcy5tb2RlbC50cmlnZ2VyKCAnZGVzdHJveScsIHRoaXMubW9kZWwgKTtcblx0fSxcblxuXHRyZW1vdmU6IGZ1bmN0aW9uKCkge1xuXHRcdHRoaXMuJGVsLnJlbW92ZSgpO1xuXHR9XG59KTtcblxubW9kdWxlLmV4cG9ydHMgPSBWaWRlbztcbiIsInZhciBWaWRlb0FydHdvcmssXG5cdF8gPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snXyddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnXyddIDogbnVsbCksXG5cdHdvcmtmbG93cyA9IHJlcXVpcmUoICcuLi8uLi93b3JrZmxvd3MnICksXG5cdHdwID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ3dwJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWyd3cCddIDogbnVsbCk7XG5cblZpZGVvQXJ0d29yayA9IHdwLkJhY2tib25lLlZpZXcuZXh0ZW5kKHtcblx0dGFnTmFtZTogJ3NwYW4nLFxuXHRjbGFzc05hbWU6ICd2aWRlby1jZW50cmFsLXBsYXlsaXN0LXZpZGVvLWFydHdvcmsnLFxuXHR0ZW1wbGF0ZTogd3AudGVtcGxhdGUoICd2aWRlby1jZW50cmFsLXBsYXlsaXN0LXBsYXlsaXN0LXZpZGVvLWFydHdvcmsnICksXG5cblx0ZXZlbnRzOiB7XG5cdFx0J2NsaWNrJzogJ3NlbGVjdCdcblx0fSxcblxuXHRpbml0aWFsaXplOiBmdW5jdGlvbiggb3B0aW9ucyApIHtcblx0XHR0aGlzLnBhcmVudCA9IG9wdGlvbnMucGFyZW50O1xuXHRcdHRoaXMubGlzdGVuVG8oIHRoaXMubW9kZWwsICdjaGFuZ2U6YXJ0d29ya1VybCcsIHRoaXMucmVuZGVyICk7XG5cdH0sXG5cblx0cmVuZGVyOiBmdW5jdGlvbigpIHtcblx0XHR0aGlzLiRlbC5odG1sKCB0aGlzLnRlbXBsYXRlKCB0aGlzLm1vZGVsLnRvSlNPTigpICkgKTtcblx0XHR0aGlzLnBhcmVudC4kZWwudG9nZ2xlQ2xhc3MoICdoYXMtYXJ0d29yaycsICEgXy5pc0VtcHR5KCB0aGlzLm1vZGVsLmdldCggJ2FydHdvcmtVcmwnICkgKSApO1xuXHRcdHJldHVybiB0aGlzO1xuXHR9LFxuXG5cdHNlbGVjdDogZnVuY3Rpb24oKSB7XG5cdFx0d29ya2Zsb3dzLnNldE1vZGVsKCB0aGlzLm1vZGVsICkuZ2V0KCAnc2VsZWN0QXJ0d29yaycgKS5vcGVuKCk7XG5cdH1cbn0pO1xuXG5tb2R1bGUuZXhwb3J0cyA9IFZpZGVvQXJ0d29yaztcbiIsInZhciBWaWRlb0F1ZGlvLFxuXHQkID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ2pRdWVyeSddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnalF1ZXJ5J10gOiBudWxsKSxcblx0XyA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93WydfJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWydfJ10gOiBudWxsKSxcblx0c2V0dGluZ3MgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1sndmlkZW9fY2VudHJhbCddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsndmlkZW9fY2VudHJhbCddIDogbnVsbCkuc2V0dGluZ3MoKSxcblx0d29ya2Zsb3dzID0gcmVxdWlyZSggJy4uLy4uL3dvcmtmbG93cycgKSxcblx0d3AgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snd3AnXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ3dwJ10gOiBudWxsKTtcblxuVmlkZW9BdWRpbyA9IHdwLkJhY2tib25lLlZpZXcuZXh0ZW5kKHtcblx0dGFnTmFtZTogJ3NwYW4nLFxuXHRjbGFzc05hbWU6ICd2aWRlby1jZW50cmFsLXBsYXlsaXN0LXZpZGVvLWF1ZGlvJyxcblx0dGVtcGxhdGU6IHdwLnRlbXBsYXRlKCAndmlkZW8tY2VudHJhbC1wbGF5bGlzdC1wbGF5bGlzdC12aWRlby1hdWRpbycgKSxcblxuXHRldmVudHM6IHtcblx0XHQnY2xpY2sgLnZpZGVvLWNlbnRyYWwtcGxheWxpc3QtdmlkZW8tYXVkaW8tc2VsZWN0b3InOiAnc2VsZWN0J1xuXHR9LFxuXG5cdGluaXRpYWxpemU6IGZ1bmN0aW9uKCBvcHRpb25zICkge1xuXHRcdHRoaXMucGFyZW50ID0gb3B0aW9ucy5wYXJlbnQ7XG5cblx0XHR0aGlzLmxpc3RlblRvKCB0aGlzLm1vZGVsLCAnY2hhbmdlOmF1ZGlvVXJsJywgdGhpcy5yZWZyZXNoICk7XG5cdFx0dGhpcy5saXN0ZW5UbyggdGhpcy5tb2RlbCwgJ2Rlc3Ryb3knLCB0aGlzLmNsZWFudXAgKTtcblx0fSxcblxuXHRyZW5kZXI6IGZ1bmN0aW9uKCkge1xuXHRcdHZhciAkbWVkaWFFbCwgcGxheWVyU2V0dGluZ3MsXG5cdFx0XHR2aWRlbyA9IHRoaXMubW9kZWwudG9KU09OKCksXG5cdFx0XHRwbGF5ZXJJZCA9IHRoaXMuJGVsLmZpbmQoICcubWVqcy1hdWRpbycgKS5hdHRyKCAnaWQnICk7XG5cblx0XHQvLyBSZW1vdmUgdGhlIE1lZGlhRWxlbWVudCBwbGF5ZXIgb2JqZWN0IGlmIHRoZVxuXHRcdC8vIGF1ZGlvIGZpbGUgVVJMIGlzIGVtcHR5LlxuXHRcdGlmICggJycgPT09IHZpZGVvLmF1ZGlvVXJsICYmIHBsYXllcklkICkge1xuXHRcdFx0bWVqcy5wbGF5ZXJzWyBwbGF5ZXJJZCBdLnJlbW92ZSgpO1xuXHRcdH1cblxuXHRcdC8vIFJlbmRlciB0aGUgbWVkaWEgZWxlbWVudC5cblx0XHR0aGlzLiRlbC5odG1sKCB0aGlzLnRlbXBsYXRlKCB0aGlzLm1vZGVsLnRvSlNPTigpICkgKTtcblxuXHRcdC8vIFNldCB1cCBNZWRpYUVsZW1lbnQuanMuXG5cdFx0JG1lZGlhRWwgPSB0aGlzLiRlbC5maW5kKCAnLnZpZGVvLWNlbnRyYWwtcGxheWxpc3QtYXVkaW8nICk7XG5cblx0XHRyZXR1cm4gdGhpcztcblx0fSxcblxuXHRyZWZyZXNoOiBmdW5jdGlvbiggZSApIHtcblx0XHR2YXIgdmlkZW8gPSB0aGlzLm1vZGVsLnRvSlNPTigpLFxuXHRcdFx0cGxheWVySWQgPSB0aGlzLiRlbC5maW5kKCAnLm1lanMtYXVkaW8nICkuYXR0ciggJ2lkJyApLFxuXHRcdFx0cGxheWVyID0gcGxheWVySWQgPyBtZWpzLnBsYXllcnNbIHBsYXllcklkIF0gOiBudWxsO1xuXG5cdFx0aWYgKCBwbGF5ZXIgJiYgJycgIT09IHZpZGVvLmF1ZGlvVXJsICkge1xuXHRcdFx0cGxheWVyLnBhdXNlKCk7XG5cdFx0XHRwbGF5ZXIuc2V0U3JjKCB2aWRlby5hdWRpb1VybCApO1xuXHRcdH0gZWxzZSB7XG5cdFx0XHR0aGlzLnJlbmRlcigpO1xuXHRcdH1cblx0fSxcblxuXHRjbGVhbnVwOiBmdW5jdGlvbigpIHtcblx0XHR2YXIgcGxheWVySWQgPSB0aGlzLiRlbC5maW5kKCAnLm1lanMtYXVkaW8nICkuYXR0ciggJ2lkJyApLFxuXHRcdFx0cGxheWVyID0gcGxheWVySWQgPyBtZWpzLnBsYXllcnNbIHBsYXllcklkIF0gOiBudWxsO1xuXG5cdFx0aWYgKCBwbGF5ZXIgKSB7XG5cdFx0XHRwbGF5ZXIucmVtb3ZlKCk7XG5cdFx0fVxuXHR9LFxuXG5cdHNlbGVjdDogZnVuY3Rpb24oKSB7XG5cdFx0d29ya2Zsb3dzLnNldE1vZGVsKCB0aGlzLm1vZGVsICkuZ2V0KCAnc2VsZWN0QXVkaW8nICkub3BlbigpO1xuXHR9XG59KTtcblxubW9kdWxlLmV4cG9ydHMgPSBWaWRlb0F1ZGlvO1xuIiwidmFyIFdvcmtmbG93cyxcblx0XyA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93WydfJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWydfJ10gOiBudWxsKSxcblx0dmlkZW9fY2VudHJhbCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93Wyd2aWRlb19jZW50cmFsJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWyd2aWRlb19jZW50cmFsJ10gOiBudWxsKSxcblx0bDEwbiA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93Wyd2aWRlb19jZW50cmFsJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWyd2aWRlb19jZW50cmFsJ10gOiBudWxsKS5sMTBuLFxuXHRNZWRpYUZyYW1lID0gcmVxdWlyZSggJy4vdmlld3MvbWVkaWEtZnJhbWUnICksXG5cdHdwID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ3dwJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWyd3cCddIDogbnVsbCksXG5cdEF0dGFjaG1lbnQgPSB3cC5tZWRpYS5tb2RlbC5BdHRhY2htZW50O1xuXG5Xb3JrZmxvd3MgPSB7XG5cdGZyYW1lczogW10sXG5cdG1vZGVsOiB7fSxcblxuXHQvKipcblx0ICogU2V0IGEgbW9kZWwgZm9yIHRoZSBjdXJyZW50IHdvcmtmbG93LlxuXHQgKlxuXHQgKiBAcGFyYW0ge09iamVjdH0gZnJhbWVcblx0ICovXG5cdHNldE1vZGVsOiBmdW5jdGlvbiggbW9kZWwgKSB7XG5cdFx0dGhpcy5tb2RlbCA9IG1vZGVsO1xuXHRcdHJldHVybiB0aGlzO1xuXHR9LFxuXG5cdC8qKlxuXHQgKiBSZXRyaWV2ZSBvciBjcmVhdGUgYSBmcmFtZSBpbnN0YW5jZSBmb3IgYSBwYXJ0aWN1bGFyIHdvcmtmbG93LlxuXHQgKlxuXHQgKiBAcGFyYW0ge3N0cmluZ30gaWQgRnJhbWUgaWRlbnRpZmVyLlxuXHQgKi9cblx0Z2V0OiBmdW5jdGlvbiggaWQgKSAge1xuXHRcdHZhciBtZXRob2QgPSAnXycgKyBpZCxcblx0XHRcdGZyYW1lID0gdGhpcy5mcmFtZXNbIG1ldGhvZCBdIHx8IG51bGw7XG5cblx0XHQvLyBBbHdheXMgY2FsbCB0aGUgZnJhbWUgbWV0aG9kIHRvIHBlcmZvcm0gYW55IHJvdXRpbmUgc2V0IHVwLiBUaGVcblx0XHQvLyBmcmFtZSBtZXRob2Qgc2hvdWxkIHNob3J0LWNpcmN1aXQgYmVmb3JlIGJlaW5nIGluaXRpYWxpemVkIGFnYWluLlxuXHRcdGZyYW1lID0gdGhpc1sgbWV0aG9kIF0uY2FsbCggdGhpcywgZnJhbWUgKTtcblxuXHRcdC8vIFN0b3JlIHRoZSBmcmFtZSBmb3IgZnV0dXJlIHVzZS5cblx0XHR0aGlzLmZyYW1lc1sgbWV0aG9kIF0gPSBmcmFtZTtcblxuXHRcdHJldHVybiBmcmFtZTtcblx0fSxcblxuXHQvKipcblx0ICogV29ya2Zsb3cgZm9yIGFkZGluZyB2aWRlb3MgdG8gdGhlIHBsYXlsaXN0LlxuXHQgKlxuXHQgKiBAcGFyYW0ge09iamVjdH0gZnJhbWVcblx0ICovXG5cdF9hZGRWaWRlb3M6IGZ1bmN0aW9uKCBmcmFtZSApIHtcblx0XHQvLyBSZXR1cm4gdGhlIGV4aXN0aW5nIGZyYW1lIGZvciB0aGlzIHdvcmtmbG93LlxuXHRcdGlmICggZnJhbWUgKSB7XG5cdFx0XHRyZXR1cm4gZnJhbWU7XG5cdFx0fVxuXG5cdFx0Ly8gSW5pdGlhbGl6ZSB0aGUgYXVkaW8gZnJhbWUuXG5cdFx0ZnJhbWUgPSBuZXcgTWVkaWFGcmFtZSh7XG5cdFx0XHR0aXRsZTogbDEwbi53b3JrZmxvd3MuYWRkVmlkZW9zLmZyYW1lVGl0bGUsXG5cdFx0XHRsaWJyYXJ5OiB7XG5cdFx0XHRcdHR5cGU6ICdhdWRpbydcblx0XHRcdH0sXG5cdFx0XHRidXR0b246IHtcblx0XHRcdFx0dGV4dDogbDEwbi53b3JrZmxvd3MuYWRkVmlkZW9zLmZyYW1lQnV0dG9uVGV4dFxuXHRcdFx0fSxcblx0XHRcdG11bHRpcGxlOiAnYWRkJ1xuXHRcdH0pO1xuXG5cdFx0Ly8gU2V0IHRoZSBleHRlbnNpb25zIHRoYXQgY2FuIGJlIHVwbG9hZGVkLlxuXHRcdGZyYW1lLnVwbG9hZGVyLm9wdGlvbnMudXBsb2FkZXIucGx1cGxvYWQgPSB7XG5cdFx0XHRmaWx0ZXJzOiB7XG5cdFx0XHRcdG1pbWVfdHlwZXM6IFt7XG5cdFx0XHRcdFx0dGl0bGU6IGwxMG4ud29ya2Zsb3dzLmFkZFZpZGVvcy5maWxlVHlwZXMsXG5cdFx0XHRcdFx0ZXh0ZW5zaW9uczogJ200YSxtcDMsb2dnLHdtYSdcblx0XHRcdFx0fV1cblx0XHRcdH1cblx0XHR9O1xuXG5cdFx0Ly8gUHJldmVudCB0aGUgRW1iZWQgY29udHJvbGxlciBzY2FubmVyIGZyb20gY2hhbmdpbmcgdGhlIHN0YXRlLlxuXHRcdGZyYW1lLnN0YXRlKCAnZW1iZWQnICkucHJvcHMub2ZmKCAnY2hhbmdlOnVybCcsIGZyYW1lLnN0YXRlKCAnZW1iZWQnICkuZGVib3VuY2VkU2NhbiApO1xuXG5cdFx0Ly8gSW5zZXJ0IGVhY2ggc2VsZWN0ZWQgYXR0YWNobWVudCBhcyBhIG5ldyB2aWRlbyBtb2RlbC5cblx0XHRmcmFtZS5zdGF0ZSggJ2luc2VydCcgKS5vbiggJ2luc2VydCcsIGZ1bmN0aW9uKCBzZWxlY3Rpb24gKSB7XG5cdFx0XHRfLmVhY2goIHNlbGVjdGlvbi5tb2RlbHMsIGZ1bmN0aW9uKCBhdHRhY2htZW50ICkge1xuICAgICAgICAgICAgICAgIHZpZGVvX2NlbnRyYWwudmlkZW9zLnB1c2goIGF0dGFjaG1lbnQudG9KU09OKCkgKTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9KTtcbiAgICAgICAgXG5cdFx0Ly8gSW5zZXJ0IHRoZSBlbWJlZCBkYXRhIGFzIGEgbmV3IG1vZGVsLlxuXHRcdGZyYW1lLnN0YXRlKCAnZW1iZWQnICkub24oICdzZWxlY3QnLCBmdW5jdGlvbigpIHtcblxuXHRcdFx0dmFyIGVtYmVkID0gdGhpcy5wcm9wcy50b0pTT04oKSxcbiAgICAgICAgICAgICAgICB2aWRlbyA9IHtcblx0XHRcdFx0XHR2aWRlb0lkOiAnJyxcblx0XHRcdFx0XHRhdWRpb1VybDogZW1iZWQudXJsXG5cdFx0XHRcdH07XG5cblx0XHRcdGlmICggKCAndGl0bGUnIGluIGVtYmVkICkgJiYgJycgIT09IGVtYmVkLnRpdGxlICkge1xuXHRcdFx0XHR2aWRlby50aXRsZSA9IGVtYmVkLnRpdGxlO1xuXHRcdFx0fVxuXG5cdFx0XHR2aWRlb19jZW50cmFsLnZpZGVvcy5wdXNoKCB2aWRlbyApO1xuXHRcdH0pO1xuXG5cdFx0cmV0dXJuIGZyYW1lO1xuXHR9LFxuXG5cdC8qKlxuXHQgKiBXb3JrZmxvdyBmb3Igc2VsZWN0aW5nIHZpZGVvIGFydHdvcmsgaW1hZ2UuXG5cdCAqXG5cdCAqIEBwYXJhbSB7T2JqZWN0fSBmcmFtZVxuXHQgKi9cblx0X3NlbGVjdEFydHdvcms6IGZ1bmN0aW9uKCBmcmFtZSApIHtcblx0XHR2YXIgd29ya2Zsb3cgPSB0aGlzO1xuXG5cdFx0Ly8gUmV0dXJuIGV4aXN0aW5nIGZyYW1lIGZvciB0aGlzIHdvcmtmbG93LlxuXHRcdGlmICggZnJhbWUgKSB7XG5cdFx0XHRyZXR1cm4gZnJhbWU7XG5cdFx0fVxuXG5cdFx0Ly8gSW5pdGlhbGl6ZSB0aGUgYXJ0d29yayBmcmFtZS5cblx0XHRmcmFtZSA9IHdwLm1lZGlhKHtcblx0XHRcdHRpdGxlOiBsMTBuLndvcmtmbG93cy5zZWxlY3RBcnR3b3JrLmZyYW1lVGl0bGUsXG5cdFx0XHRsaWJyYXJ5OiB7XG5cdFx0XHRcdHR5cGU6ICdpbWFnZSdcblx0XHRcdH0sXG5cdFx0XHRidXR0b246IHtcblx0XHRcdFx0dGV4dDogbDEwbi53b3JrZmxvd3Muc2VsZWN0QXJ0d29yay5mcmFtZUJ1dHRvblRleHRcblx0XHRcdH0sXG5cdFx0XHRtdWx0aXBsZTogZmFsc2Vcblx0XHR9KTtcblxuXHRcdC8vIFNldCB0aGUgZXh0ZW5zaW9ucyB0aGF0IGNhbiBiZSB1cGxvYWRlZC5cblx0XHRmcmFtZS51cGxvYWRlci5vcHRpb25zLnVwbG9hZGVyLnBsdXBsb2FkID0ge1xuXHRcdFx0ZmlsdGVyczoge1xuXHRcdFx0XHRtaW1lX3R5cGVzOiBbe1xuXHRcdFx0XHRcdGZpbGVzOiBsMTBuLndvcmtmbG93cy5zZWxlY3RBcnR3b3JrLmZpbGVUeXBlcyxcblx0XHRcdFx0XHRleHRlbnNpb25zOiAnanBnLGpwZWcsZ2lmLHBuZydcblx0XHRcdFx0fV1cblx0XHRcdH1cblx0XHR9O1xuXG5cdFx0Ly8gQXV0b21hdGljYWxseSBzZWxlY3QgdGhlIGV4aXN0aW5nIGFydHdvcmsgaWYgcG9zc2libGUuXG5cdFx0ZnJhbWUub24oICdvcGVuJywgZnVuY3Rpb24oKSB7XG5cdFx0XHR2YXIgc2VsZWN0aW9uID0gdGhpcy5nZXQoICdsaWJyYXJ5JyApLmdldCggJ3NlbGVjdGlvbicgKSxcblx0XHRcdFx0YXJ0d29ya0lkID0gd29ya2Zsb3cubW9kZWwuZ2V0KCAnYXJ0d29ya0lkJyApLFxuXHRcdFx0XHRhdHRhY2htZW50cyA9IFtdO1xuXG5cdFx0XHRpZiAoIGFydHdvcmtJZCApIHtcblx0XHRcdFx0YXR0YWNobWVudHMucHVzaCggQXR0YWNobWVudC5nZXQoIGFydHdvcmtJZCApICk7XG5cdFx0XHRcdGF0dGFjaG1lbnRzWzBdLmZldGNoKCk7XG5cdFx0XHR9XG5cblx0XHRcdHNlbGVjdGlvbi5yZXNldCggYXR0YWNobWVudHMgKTtcblx0XHR9KTtcblxuXHRcdC8vIFNldCB0aGUgbW9kZWwncyBhcnR3b3JrIElEIGFuZCB1cmwgcHJvcGVydGllcy5cblx0XHRmcmFtZS5zdGF0ZSggJ2xpYnJhcnknICkub24oICdzZWxlY3QnLCBmdW5jdGlvbigpIHtcbiAgICAgICAgICAgIHZhciBhdHRhY2htZW50ID0gdGhpcy5nZXQoICdzZWxlY3Rpb24nICkuZmlyc3QoKS50b0pTT04oKTtcblxuXHRcdFx0d29ya2Zsb3cubW9kZWwuc2V0KHtcblx0XHRcdFx0YXJ0d29ya0lkOiBhdHRhY2htZW50LmlkLFxuXHRcdFx0XHRhcnR3b3JrVXJsOiBhdHRhY2htZW50LnVybFxuXHRcdFx0fSk7XG5cdFx0fSk7XG5cblx0XHRyZXR1cm4gZnJhbWU7XG5cdH0sXG5cblx0LyoqXG5cdCAqIFdvcmtmbG93IGZvciBzZWxlY3RpbmcgdmlkZW8gYXVkaW8uXG5cdCAqXG5cdCAqIEBwYXJhbSB7T2JqZWN0fSBmcmFtZVxuXHQgKi9cblx0X3NlbGVjdEF1ZGlvOiBmdW5jdGlvbiggZnJhbWUgKSB7XG5cdFx0dmFyIHdvcmtmbG93ID0gdGhpcztcblxuXHRcdC8vIFJldHVybiB0aGUgZXhpc3RpbmcgZnJhbWUgZm9yIHRoaXMgd29ya2Zsb3cuXG5cdFx0aWYgKCBmcmFtZSApIHtcblx0XHRcdHJldHVybiBmcmFtZTtcblx0XHR9XG5cblx0XHQvLyBJbml0aWFsaXplIHRoZSBhdWRpbyBmcmFtZS5cblx0XHRmcmFtZSA9IG5ldyBNZWRpYUZyYW1lKHtcblx0XHRcdHRpdGxlOiBsMTBuLndvcmtmbG93cy5zZWxlY3RBdWRpby5mcmFtZVRpdGxlLFxuXHRcdFx0bGlicmFyeToge1xuXHRcdFx0XHR0eXBlOiAnYXVkaW8nXG5cdFx0XHR9LFxuXHRcdFx0YnV0dG9uOiB7XG5cdFx0XHRcdHRleHQ6IGwxMG4ud29ya2Zsb3dzLnNlbGVjdEF1ZGlvLmZyYW1lQnV0dG9uVGV4dFxuXHRcdFx0fSxcblx0XHRcdG11bHRpcGxlOiBmYWxzZVxuXHRcdH0pO1xuXG5cdFx0Ly8gU2V0IHRoZSBleHRlbnNpb25zIHRoYXQgY2FuIGJlIHVwbG9hZGVkLlxuXHRcdGZyYW1lLnVwbG9hZGVyLm9wdGlvbnMudXBsb2FkZXIucGx1cGxvYWQgPSB7XG5cdFx0XHRmaWx0ZXJzOiB7XG5cdFx0XHRcdG1pbWVfdHlwZXM6IFt7XG5cdFx0XHRcdFx0dGl0bGU6IGwxMG4ud29ya2Zsb3dzLnNlbGVjdEF1ZGlvLmZpbGVUeXBlcyxcblx0XHRcdFx0XHRleHRlbnNpb25zOiAnbTRhLG1wMyxvZ2csd21hJ1xuXHRcdFx0XHR9XVxuXHRcdFx0fVxuXHRcdH07XG5cblx0XHQvLyBQcmV2ZW50IHRoZSBFbWJlZCBjb250cm9sbGVyIHNjYW5uZXIgZnJvbSBjaGFuZ2luZyB0aGUgc3RhdGUuXG5cdFx0ZnJhbWUuc3RhdGUoICdlbWJlZCcgKS5wcm9wcy5vZmYoICdjaGFuZ2U6dXJsJywgZnJhbWUuc3RhdGUoICdlbWJlZCcgKS5kZWJvdW5jZWRTY2FuICk7XG5cblx0XHQvLyBTZXQgdGhlIGZyYW1lIHN0YXRlIHdoZW4gb3BlbmluZyBpdC5cblx0XHRmcmFtZS5vbiggJ29wZW4nLCBmdW5jdGlvbigpIHtcblx0XHRcdHZhciBzZWxlY3Rpb24gPSB0aGlzLmdldCggJ2luc2VydCcgKS5nZXQoICdzZWxlY3Rpb24nICksXG5cdFx0XHRcdHZpZGVvSWQgPSB3b3JrZmxvdy5tb2RlbC5nZXQoICd2aWRlb0lkJyApLFxuXHRcdFx0XHRhdWRpb1VybCA9IHdvcmtmbG93Lm1vZGVsLmdldCggJ2F1ZGlvVXJsJyApLFxuXHRcdFx0XHRpc0VtYmVkID0gYXVkaW9VcmwgJiYgISB2aWRlb0lkLFxuXHRcdFx0XHRhdHRhY2htZW50cyA9IFtdO1xuXG5cdFx0XHQvLyBBdXRvbWF0aWNhbGx5IHNlbGVjdCB0aGUgZXhpc3RpbmcgYXVkaW8gZmlsZSBpZiBwb3NzaWJsZS5cblx0XHRcdGlmICggdmlkZW9JZCApIHtcblx0XHRcdFx0YXR0YWNobWVudHMucHVzaCggQXR0YWNobWVudC5nZXQoIHZpZGVvSWQgKSApO1xuXHRcdFx0XHRhdHRhY2htZW50c1swXS5mZXRjaCgpO1xuXHRcdFx0fVxuXG5cdFx0XHRzZWxlY3Rpb24ucmVzZXQoIGF0dGFjaG1lbnRzICk7XG5cblx0XHRcdC8vIFNldCB0aGUgZW1iZWQgc3RhdGUgcHJvcGVydGllcy5cblx0XHRcdGlmICggaXNFbWJlZCApIHtcblx0XHRcdFx0dGhpcy5nZXQoICdlbWJlZCcgKS5wcm9wcy5zZXQoe1xuXHRcdFx0XHRcdHVybDogYXVkaW9VcmwsXG5cdFx0XHRcdFx0dGl0bGU6IHdvcmtmbG93Lm1vZGVsLmdldCggJ3RpdGxlJyApXG5cdFx0XHRcdH0pO1xuXHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0dGhpcy5nZXQoICdlbWJlZCcgKS5wcm9wcy5zZXQoe1xuXHRcdFx0XHRcdHVybDogJycsXG5cdFx0XHRcdFx0dGl0bGU6ICcnXG5cdFx0XHRcdH0pO1xuXHRcdFx0fVxuXG5cdFx0XHQvLyBTZXQgdGhlIHN0YXRlIHRvICdlbWJlZCcgaWYgdGhlIG1vZGVsIGhhcyBhbiBhdWRpbyBVUkwgYnV0XG5cdFx0XHQvLyBub3QgYSBjb3JyZXNwb25kaW5nIGF0dGFjaG1lbnQgSUQuXG5cdFx0XHRmcmFtZS5zZXRTdGF0ZSggaXNFbWJlZCA/ICdlbWJlZCcgOiAnaW5zZXJ0JyApO1xuXHRcdH0pO1xuXG5cdFx0Ly8gQ29weSBkYXRhIGZyb20gdGhlIHNlbGVjdGVkIGF0dGFjaG1lbnQgdG8gdGhlIGN1cnJlbnQgbW9kZWwuXG5cdFx0ZnJhbWUuc3RhdGUoICdpbnNlcnQnICkub24oICdpbnNlcnQnLCBmdW5jdGlvbiggc2VsZWN0aW9uICkge1xuXHRcdFx0dmFyIGF0dGFjaG1lbnQgPSBzZWxlY3Rpb24uZmlyc3QoKS50b0pTT04oKS52aWRlb19jZW50cmFsLFxuXHRcdFx0XHRkYXRhID0ge30sXG5cdFx0XHRcdGtleXMgPSBfLmtleXMoIHdvcmtmbG93Lm1vZGVsLmF0dHJpYnV0ZXMgKTtcblxuXHRcdFx0Ly8gQXR0cmlidXRlcyB0aGF0IHNob3VsZG4ndCBiZSB1cGRhdGVkIHdoZW4gaW5zZXJ0aW5nIGFuXG5cdFx0XHQvLyBhdWRpbyBhdHRhY2htZW50LlxuXHRcdFx0Xy53aXRob3V0KCBrZXlzLCBbICdpZCcsICdvcmRlcicgXSApO1xuXG5cdFx0XHQvLyBVcGRhdGUgdGhlc2UgYXR0cmlidXRlcyBpZiB0aGV5J3JlIGVtcHR5LlxuXHRcdFx0Ly8gVGhleSBzaG91bGRuJ3Qgb3ZlcndyaXRlIGFueSBkYXRhIGVudGVyZWQgYnkgdGhlIHVzZXIuXG5cdFx0XHRfLmVhY2goIGtleXMsIGZ1bmN0aW9uKCBrZXkgKSB7XG5cdFx0XHRcdHZhciB2YWx1ZSA9IHdvcmtmbG93Lm1vZGVsLmdldCgga2V5ICk7XG5cblx0XHRcdFx0aWYgKCAhIHZhbHVlICYmICgga2V5IGluIGF0dGFjaG1lbnQgKSAmJiB2YWx1ZSAhPT0gYXR0YWNobWVudFsga2V5IF0gKSB7XG5cdFx0XHRcdFx0ZGF0YVsga2V5IF0gPSBhdHRhY2htZW50WyBrZXkgXTtcblx0XHRcdFx0fVxuXHRcdFx0fSk7XG5cblx0XHRcdC8vIEF0dHJpYnV0ZXMgdGhhdCBzaG91bGQgYWx3YXlzIGJlIHJlcGxhY2VkLlxuXHRcdFx0ZGF0YS52aWRlb0lkICA9IGF0dGFjaG1lbnQudmlkZW9JZDtcblx0XHRcdGRhdGEuYXVkaW9VcmwgPSBhdHRhY2htZW50LmF1ZGlvVXJsO1xuXG5cdFx0XHR3b3JrZmxvdy5tb2RlbC5zZXQoIGRhdGEgKTtcblx0XHR9KTtcblxuXHRcdC8vIENvcHkgdGhlIGVtYmVkIGRhdGEgdG8gdGhlIGN1cnJlbnQgbW9kZWwuXG5cdFx0ZnJhbWUuc3RhdGUoICdlbWJlZCcgKS5vbiggJ3NlbGVjdCcsIGZ1bmN0aW9uKCkge1xuXHRcdFx0dmFyIGVtYmVkID0gdGhpcy5wcm9wcy50b0pTT04oKSxcblx0XHRcdFx0ZGF0YSA9IHt9O1xuXG5cdFx0XHRkYXRhLnZpZGVvSWQgID0gJyc7XG5cdFx0XHRkYXRhLmF1ZGlvVXJsID0gZW1iZWQudXJsO1xuXG5cdFx0XHRpZiAoICggJ3RpdGxlJyBpbiBlbWJlZCApICYmICcnICE9PSBlbWJlZC50aXRsZSApIHtcblx0XHRcdFx0ZGF0YS50aXRsZSA9IGVtYmVkLnRpdGxlO1xuXHRcdFx0fVxuXG5cdFx0XHR3b3JrZmxvdy5tb2RlbC5zZXQoIGRhdGEgKTtcblx0XHR9KTtcblxuXHRcdC8vIFJlbW92ZSBhbiBlbXB0eSBtb2RlbCBpZiB0aGUgZnJhbWUgaXMgZXNjYXBlZC5cblx0XHRmcmFtZS5vbiggJ2VzY2FwZScsIGZ1bmN0aW9uKCkge1xuXHRcdFx0dmFyIG1vZGVsID0gd29ya2Zsb3cubW9kZWwudG9KU09OKCk7XG5cblx0XHRcdGlmICggISBtb2RlbC5hcnR3b3JrVXJsICYmICEgbW9kZWwuYXVkaW9VcmwgKSB7XG5cdFx0XHRcdHdvcmtmbG93Lm1vZGVsLmRlc3Ryb3koKTtcblx0XHRcdH1cblx0XHR9KTtcblxuXHRcdHJldHVybiBmcmFtZTtcblx0fVxufTtcblxubW9kdWxlLmV4cG9ydHMgPSBXb3JrZmxvd3M7XG4iXX0=
