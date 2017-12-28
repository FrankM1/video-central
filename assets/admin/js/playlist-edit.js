(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
(function (global){
var Tracks,
	_ = (typeof window !== "undefined" ? window['_'] : typeof global !== "undefined" ? global['_'] : null),
	Backbone = (typeof window !== "undefined" ? window['Backbone'] : typeof global !== "undefined" ? global['Backbone'] : null),
	settings = (typeof window !== "undefined" ? window['video_central'] : typeof global !== "undefined" ? global['video_central'] : null).settings(),
	Track = require( '../models/track' ),
	wp = (typeof window !== "undefined" ? window['wp'] : typeof global !== "undefined" ? global['wp'] : null);

Tracks = Backbone.Collection.extend({
	model: Track,

	comparator: function( track ) {
		return parseInt( track.get( 'order' ), 10 );
	},

	fetch: function() {
		var collection = this;

		return wp.ajax.post( 'video_central_get_playlist_tracks', {
			post_id: settings.postId
		}).done(function( tracks ) {
			collection.reset( tracks );
		});
	},

	save: function( data ) {
		this.sort();

		data = _.extend({}, data, {
			post_id: settings.postId,
			tracks: this.toJSON(),
			nonce: settings.saveNonce
		});

		return wp.ajax.post( 'video_central_save_playlist_tracks', data );
	}
});

module.exports = Tracks;

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{"../models/track":2}],2:[function(require,module,exports){
(function (global){
var Track,
	Backbone = (typeof window !== "undefined" ? window['Backbone'] : typeof global !== "undefined" ? global['Backbone'] : null);

Track = Backbone.Model.extend({
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

module.exports = Track;

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{}],3:[function(require,module,exports){
(function (global){
var video_central = (typeof window !== "undefined" ? window['video_central'] : typeof global !== "undefined" ? global['video_central'] : null);
var wp = (typeof window !== "undefined" ? window['wp'] : typeof global !== "undefined" ? global['wp'] : null);

video_central.data = videoCentralPlaylistConfig;
video_central.settings( videoCentralPlaylistConfig );

wp.media.view.settings.post.id = video_central.data.postId;
wp.media.view.settings.defaultProps = {};

video_central.model.Track = require( './models/track' );
video_central.model.Tracks = require( './collections/tracks' );

video_central.view.MediaFrame = require( './views/media-frame' );
video_central.view.PostForm = require( './views/post-form' );
video_central.view.AddTracksButton = require( './views/button/add-tracks' );
video_central.view.TrackList = require( './views/track-list' );
video_central.view.Track = require( './views/track' );
video_central.view.TrackArtwork = require( './views/track/artwork' );
video_central.view.TrackAudio = require( './views/track/audio' );

video_central.workflows = require( './workflows' );

( function( $ ) {
    var tracks;

	tracks = video_central.tracks = new video_central.model.Tracks( video_central.data.tracks );
	delete video_central.data.tracks;

	var postForm = new video_central.view.PostForm({
		collection: tracks,
		l10n: video_central.l10n
    });
    
} ( jQuery ));


}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{"./collections/tracks":1,"./models/track":2,"./views/button/add-tracks":4,"./views/media-frame":5,"./views/post-form":6,"./views/track":8,"./views/track-list":7,"./views/track/artwork":9,"./views/track/audio":10,"./workflows":11}],4:[function(require,module,exports){
(function (global){
var AddTracksButton,
	$ = (typeof window !== "undefined" ? window['jQuery'] : typeof global !== "undefined" ? global['jQuery'] : null),
	workflows = require( '../../workflows' ),
	wp = (typeof window !== "undefined" ? window['wp'] : typeof global !== "undefined" ? global['wp'] : null);

AddTracksButton = wp.Backbone.View.extend({
	id: 'add-tracks',
	tagName: 'p',

	events: {
		'click .button': 'click'
	},

	initialize: function( options ) {
		this.l10n = options.l10n;
	},

	render: function() {
		var $button = $( '<a />', {
			text: this.l10n.addTracks
		}).addClass( 'button button-secondary' );

		this.$el.html( $button );

		return this;
	},

	click: function( e ) {
		e.preventDefault();
		workflows.get( 'addTracks' ).open();
	}
});

module.exports = AddTracksButton;

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
	AddTracksButton = require( './button/add-tracks' ),
	TrackList = require( './track-list' ),
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
			new AddTracksButton({
				collection: this.collection,
				l10n: this.l10n
			}),

			new TrackList({
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

},{"./button/add-tracks":4,"./track-list":7}],7:[function(require,module,exports){
(function (global){
var TrackList,
	$ = (typeof window !== "undefined" ? window['jQuery'] : typeof global !== "undefined" ? global['jQuery'] : null),
	_ = (typeof window !== "undefined" ? window['_'] : typeof global !== "undefined" ? global['_'] : null),
	Track = require( './track' ),
	wp = (typeof window !== "undefined" ? window['wp'] : typeof global !== "undefined" ? global['wp'] : null);

TrackList = wp.Backbone.View.extend({
	className: 'video-central-playlist-tracklist',
	tagName: 'ol',

	initialize: function() {
		this.listenTo( this.collection, 'add', this.addTrack );
		this.listenTo( this.collection, 'add remove', this.updateOrder );
		this.listenTo( this.collection, 'reset', this.render );
	},

	render: function() {
		this.$el.empty();

		this.collection.each( this.addTrack, this );
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

	addTrack: function( track ) {
		var trackView = new Track({ model: track });
		this.$el.append( trackView.render().el );
	},

	updateOrder: function() {
		_.each( this.$el.find( '.video-central-playlist-track' ), function( item, i ) {
			var cid = $( item ).data( 'cid' );
			this.collection.get( cid ).set( 'order', i );
		}, this );
	}
});

module.exports = TrackList;

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{"./track":8}],8:[function(require,module,exports){
(function (global){
var Track,
	$ = (typeof window !== "undefined" ? window['jQuery'] : typeof global !== "undefined" ? global['jQuery'] : null),
	TrackArtwork = require( './track/artwork' ),
	TrackAudio = require( './track/audio' ),
	wp = (typeof window !== "undefined" ? window['wp'] : typeof global !== "undefined" ? global['wp'] : null);

Track = wp.Backbone.View.extend({
	tagName: 'li',
	className: 'video-central-playlist-track',
	template: wp.template( 'video-central-playlist-playlist-track' ),

	events: {
		'change [data-setting]': 'updateAttribute',
		'click .js-toggle': 'toggleOpenStatus',
		'dblclick .video-central-playlist-track-title': 'toggleOpenStatus',
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

		this.views.add( '.video-central-playlist-track-column-artwork', new TrackArtwork({
			model: this.model,
			parent: this
		}));

		this.views.add( '.video-central-playlist-track-audio-group', new TrackAudio({
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
		var track = this.model.toJSON(),
			$settings = this.$el.find( '[data-setting]' ),
			attribute, value;

		// A change event shouldn't be triggered here, so it won't cause
		// the model attribute to be updated and get stuck in an
		// infinite loop.
		for ( attribute in track ) {
			// Decode HTML entities.
			value = $( '<div/>' ).html( track[ attribute ] ).text();
			$settings.filter( '[data-setting="' + attribute + '"]' ).val( value );
		}
	},

	updateTitle: function() {
		var title = this.model.get( 'title' );
		this.$el.find( '.video-central-playlist-track-title .text' ).text( title ? title : 'Title' );
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

module.exports = Track;

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{"./track/artwork":9,"./track/audio":10}],9:[function(require,module,exports){
(function (global){
var TrackArtwork,
	_ = (typeof window !== "undefined" ? window['_'] : typeof global !== "undefined" ? global['_'] : null),
	workflows = require( '../../workflows' ),
	wp = (typeof window !== "undefined" ? window['wp'] : typeof global !== "undefined" ? global['wp'] : null);

TrackArtwork = wp.Backbone.View.extend({
	tagName: 'span',
	className: 'video-central-playlist-track-artwork',
	template: wp.template( 'video-central-playlist-playlist-track-artwork' ),

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

module.exports = TrackArtwork;

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{"../../workflows":11}],10:[function(require,module,exports){
(function (global){
var TrackAudio,
	$ = (typeof window !== "undefined" ? window['jQuery'] : typeof global !== "undefined" ? global['jQuery'] : null),
	_ = (typeof window !== "undefined" ? window['_'] : typeof global !== "undefined" ? global['_'] : null),
	settings = (typeof window !== "undefined" ? window['video_central'] : typeof global !== "undefined" ? global['video_central'] : null).settings(),
	workflows = require( '../../workflows' ),
	wp = (typeof window !== "undefined" ? window['wp'] : typeof global !== "undefined" ? global['wp'] : null);

TrackAudio = wp.Backbone.View.extend({
	tagName: 'span',
	className: 'video-central-playlist-track-audio',
	template: wp.template( 'video-central-playlist-playlist-track-audio' ),

	events: {
		'click .video-central-playlist-track-audio-selector': 'select'
	},

	initialize: function( options ) {
		this.parent = options.parent;

		this.listenTo( this.model, 'change:audioUrl', this.refresh );
		this.listenTo( this.model, 'destroy', this.cleanup );
	},

	render: function() {
		var $mediaEl, playerSettings,
			track = this.model.toJSON(),
			playerId = this.$el.find( '.mejs-audio' ).attr( 'id' );

		// Remove the MediaElement player object if the
		// audio file URL is empty.
		if ( '' === track.audioUrl && playerId ) {
			mejs.players[ playerId ].remove();
		}

		// Render the media element.
		this.$el.html( this.template( this.model.toJSON() ) );

		// Set up MediaElement.js.
		$mediaEl = this.$el.find( '.video-central-playlist-audio' );

		return this;
	},

	refresh: function( e ) {
		var track = this.model.toJSON(),
			playerId = this.$el.find( '.mejs-audio' ).attr( 'id' ),
			player = playerId ? mejs.players[ playerId ] : null;

		if ( player && '' !== track.audioUrl ) {
			player.pause();
			player.setSrc( track.audioUrl );
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

module.exports = TrackAudio;

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
	 * Workflow for adding tracks to the playlist.
	 *
	 * @param {Object} frame
	 */
	_addTracks: function( frame ) {
		// Return the existing frame for this workflow.
		if ( frame ) {
			return frame;
		}

		// Initialize the audio frame.
		frame = new MediaFrame({
			title: l10n.workflows.addTracks.frameTitle,
			library: {
				type: 'audio'
			},
			button: {
				text: l10n.workflows.addTracks.frameButtonText
			},
			multiple: 'add'
		});

		// Set the extensions that can be uploaded.
		frame.uploader.options.uploader.plupload = {
			filters: {
				mime_types: [{
					title: l10n.workflows.addTracks.fileTypes,
					extensions: 'm4a,mp3,ogg,wma'
				}]
			}
		};

		// Prevent the Embed controller scanner from changing the state.
		frame.state( 'embed' ).props.off( 'change:url', frame.state( 'embed' ).debouncedScan );

		// Insert each selected attachment as a new track model.
		frame.state( 'insert' ).on( 'insert', function( selection ) {
			_.each( selection.models, function( attachment ) {
                console.log(attachment.toJSON());
                video_central.tracks.push( attachment.toJSON().video_central );
                console.log( video_central.tracks );
			});
		});

		// Insert the embed data as a new model.
		frame.state( 'embed' ).on( 'select', function() {

			var embed = this.props.toJSON(),
				track = {
					videoId: '',
					audioUrl: embed.url
				};

			if ( ( 'title' in embed ) && '' !== embed.title ) {
				track.title = embed.title;
			}

			video_central.tracks.push( track );
		});

		return frame;
	},

	/**
	 * Workflow for selecting track artwork image.
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
				artworkUrl: attachment.sizes.video_central.url
			});
		});

		return frame;
	},

	/**
	 * Workflow for selecting track audio.
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
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJhc3NldHMvYWRtaW4vanMvc291cmNlL3BsYXlsaXN0L2NvbGxlY3Rpb25zL3RyYWNrcy5qcyIsImFzc2V0cy9hZG1pbi9qcy9zb3VyY2UvcGxheWxpc3QvbW9kZWxzL3RyYWNrLmpzIiwiYXNzZXRzL2FkbWluL2pzL3NvdXJjZS9wbGF5bGlzdC9wbGF5bGlzdC1lZGl0LmpzIiwiYXNzZXRzL2FkbWluL2pzL3NvdXJjZS9wbGF5bGlzdC92aWV3cy9idXR0b24vYWRkLXRyYWNrcy5qcyIsImFzc2V0cy9hZG1pbi9qcy9zb3VyY2UvcGxheWxpc3Qvdmlld3MvbWVkaWEtZnJhbWUuanMiLCJhc3NldHMvYWRtaW4vanMvc291cmNlL3BsYXlsaXN0L3ZpZXdzL3Bvc3QtZm9ybS5qcyIsImFzc2V0cy9hZG1pbi9qcy9zb3VyY2UvcGxheWxpc3Qvdmlld3MvdHJhY2stbGlzdC5qcyIsImFzc2V0cy9hZG1pbi9qcy9zb3VyY2UvcGxheWxpc3Qvdmlld3MvdHJhY2suanMiLCJhc3NldHMvYWRtaW4vanMvc291cmNlL3BsYXlsaXN0L3ZpZXdzL3RyYWNrL2FydHdvcmsuanMiLCJhc3NldHMvYWRtaW4vanMvc291cmNlL3BsYXlsaXN0L3ZpZXdzL3RyYWNrL2F1ZGlvLmpzIiwiYXNzZXRzL2FkbWluL2pzL3NvdXJjZS9wbGF5bGlzdC93b3JrZmxvd3MuanMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7O0FDQUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOzs7OztBQ3RDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7Ozs7QUNsQkE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOzs7OztBQ25DQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOzs7OztBQ2xDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7Ozs7QUN0R0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBOzs7OztBQ3JEQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7O0FDckRBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7O0FDaEhBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7Ozs7O0FDL0JBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7Ozs7QUN2RUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSIsImZpbGUiOiJnZW5lcmF0ZWQuanMiLCJzb3VyY2VSb290IjoiIiwic291cmNlc0NvbnRlbnQiOlsiKGZ1bmN0aW9uIGUodCxuLHIpe2Z1bmN0aW9uIHMobyx1KXtpZighbltvXSl7aWYoIXRbb10pe3ZhciBhPXR5cGVvZiByZXF1aXJlPT1cImZ1bmN0aW9uXCImJnJlcXVpcmU7aWYoIXUmJmEpcmV0dXJuIGEobywhMCk7aWYoaSlyZXR1cm4gaShvLCEwKTt2YXIgZj1uZXcgRXJyb3IoXCJDYW5ub3QgZmluZCBtb2R1bGUgJ1wiK28rXCInXCIpO3Rocm93IGYuY29kZT1cIk1PRFVMRV9OT1RfRk9VTkRcIixmfXZhciBsPW5bb109e2V4cG9ydHM6e319O3Rbb11bMF0uY2FsbChsLmV4cG9ydHMsZnVuY3Rpb24oZSl7dmFyIG49dFtvXVsxXVtlXTtyZXR1cm4gcyhuP246ZSl9LGwsbC5leHBvcnRzLGUsdCxuLHIpfXJldHVybiBuW29dLmV4cG9ydHN9dmFyIGk9dHlwZW9mIHJlcXVpcmU9PVwiZnVuY3Rpb25cIiYmcmVxdWlyZTtmb3IodmFyIG89MDtvPHIubGVuZ3RoO28rKylzKHJbb10pO3JldHVybiBzfSkiLCJ2YXIgVHJhY2tzLFxuXHRfID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ18nXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ18nXSA6IG51bGwpLFxuXHRCYWNrYm9uZSA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93WydCYWNrYm9uZSddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnQmFja2JvbmUnXSA6IG51bGwpLFxuXHRzZXR0aW5ncyA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93Wyd2aWRlb19jZW50cmFsJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWyd2aWRlb19jZW50cmFsJ10gOiBudWxsKS5zZXR0aW5ncygpLFxuXHRUcmFjayA9IHJlcXVpcmUoICcuLi9tb2RlbHMvdHJhY2snICksXG5cdHdwID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ3dwJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWyd3cCddIDogbnVsbCk7XG5cblRyYWNrcyA9IEJhY2tib25lLkNvbGxlY3Rpb24uZXh0ZW5kKHtcblx0bW9kZWw6IFRyYWNrLFxuXG5cdGNvbXBhcmF0b3I6IGZ1bmN0aW9uKCB0cmFjayApIHtcblx0XHRyZXR1cm4gcGFyc2VJbnQoIHRyYWNrLmdldCggJ29yZGVyJyApLCAxMCApO1xuXHR9LFxuXG5cdGZldGNoOiBmdW5jdGlvbigpIHtcblx0XHR2YXIgY29sbGVjdGlvbiA9IHRoaXM7XG5cblx0XHRyZXR1cm4gd3AuYWpheC5wb3N0KCAndmlkZW9fY2VudHJhbF9nZXRfcGxheWxpc3RfdHJhY2tzJywge1xuXHRcdFx0cG9zdF9pZDogc2V0dGluZ3MucG9zdElkXG5cdFx0fSkuZG9uZShmdW5jdGlvbiggdHJhY2tzICkge1xuXHRcdFx0Y29sbGVjdGlvbi5yZXNldCggdHJhY2tzICk7XG5cdFx0fSk7XG5cdH0sXG5cblx0c2F2ZTogZnVuY3Rpb24oIGRhdGEgKSB7XG5cdFx0dGhpcy5zb3J0KCk7XG5cblx0XHRkYXRhID0gXy5leHRlbmQoe30sIGRhdGEsIHtcblx0XHRcdHBvc3RfaWQ6IHNldHRpbmdzLnBvc3RJZCxcblx0XHRcdHRyYWNrczogdGhpcy50b0pTT04oKSxcblx0XHRcdG5vbmNlOiBzZXR0aW5ncy5zYXZlTm9uY2Vcblx0XHR9KTtcblxuXHRcdHJldHVybiB3cC5hamF4LnBvc3QoICd2aWRlb19jZW50cmFsX3NhdmVfcGxheWxpc3RfdHJhY2tzJywgZGF0YSApO1xuXHR9XG59KTtcblxubW9kdWxlLmV4cG9ydHMgPSBUcmFja3M7XG4iLCJ2YXIgVHJhY2ssXG5cdEJhY2tib25lID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ0JhY2tib25lJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWydCYWNrYm9uZSddIDogbnVsbCk7XG5cblRyYWNrID0gQmFja2JvbmUuTW9kZWwuZXh0ZW5kKHtcblx0ZGVmYXVsdHM6IHtcblx0XHRhcnRpc3Q6ICcnLFxuXHRcdGFydHdvcmtJZDogJycsXG5cdFx0YXJ0d29ya1VybDogJycsXG5cdFx0dmlkZW9JZDogJycsXG5cdFx0YXVkaW9Vcmw6ICcnLFxuXHRcdGZvcm1hdDogJycsXG5cdFx0bGVuZ3RoOiAnJyxcblx0XHR0aXRsZTogJycsXG5cdFx0b3JkZXI6IDBcblx0fVxufSk7XG5cbm1vZHVsZS5leHBvcnRzID0gVHJhY2s7XG4iLCJ2YXIgdmlkZW9fY2VudHJhbCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93Wyd2aWRlb19jZW50cmFsJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWyd2aWRlb19jZW50cmFsJ10gOiBudWxsKTtcbnZhciB3cCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93Wyd3cCddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnd3AnXSA6IG51bGwpO1xuXG52aWRlb19jZW50cmFsLmRhdGEgPSB2aWRlb0NlbnRyYWxQbGF5bGlzdENvbmZpZztcbnZpZGVvX2NlbnRyYWwuc2V0dGluZ3MoIHZpZGVvQ2VudHJhbFBsYXlsaXN0Q29uZmlnICk7XG5cbndwLm1lZGlhLnZpZXcuc2V0dGluZ3MucG9zdC5pZCA9IHZpZGVvX2NlbnRyYWwuZGF0YS5wb3N0SWQ7XG53cC5tZWRpYS52aWV3LnNldHRpbmdzLmRlZmF1bHRQcm9wcyA9IHt9O1xuXG52aWRlb19jZW50cmFsLm1vZGVsLlRyYWNrID0gcmVxdWlyZSggJy4vbW9kZWxzL3RyYWNrJyApO1xudmlkZW9fY2VudHJhbC5tb2RlbC5UcmFja3MgPSByZXF1aXJlKCAnLi9jb2xsZWN0aW9ucy90cmFja3MnICk7XG5cbnZpZGVvX2NlbnRyYWwudmlldy5NZWRpYUZyYW1lID0gcmVxdWlyZSggJy4vdmlld3MvbWVkaWEtZnJhbWUnICk7XG52aWRlb19jZW50cmFsLnZpZXcuUG9zdEZvcm0gPSByZXF1aXJlKCAnLi92aWV3cy9wb3N0LWZvcm0nICk7XG52aWRlb19jZW50cmFsLnZpZXcuQWRkVHJhY2tzQnV0dG9uID0gcmVxdWlyZSggJy4vdmlld3MvYnV0dG9uL2FkZC10cmFja3MnICk7XG52aWRlb19jZW50cmFsLnZpZXcuVHJhY2tMaXN0ID0gcmVxdWlyZSggJy4vdmlld3MvdHJhY2stbGlzdCcgKTtcbnZpZGVvX2NlbnRyYWwudmlldy5UcmFjayA9IHJlcXVpcmUoICcuL3ZpZXdzL3RyYWNrJyApO1xudmlkZW9fY2VudHJhbC52aWV3LlRyYWNrQXJ0d29yayA9IHJlcXVpcmUoICcuL3ZpZXdzL3RyYWNrL2FydHdvcmsnICk7XG52aWRlb19jZW50cmFsLnZpZXcuVHJhY2tBdWRpbyA9IHJlcXVpcmUoICcuL3ZpZXdzL3RyYWNrL2F1ZGlvJyApO1xuXG52aWRlb19jZW50cmFsLndvcmtmbG93cyA9IHJlcXVpcmUoICcuL3dvcmtmbG93cycgKTtcblxuKCBmdW5jdGlvbiggJCApIHtcbiAgICB2YXIgdHJhY2tzO1xuXG5cdHRyYWNrcyA9IHZpZGVvX2NlbnRyYWwudHJhY2tzID0gbmV3IHZpZGVvX2NlbnRyYWwubW9kZWwuVHJhY2tzKCB2aWRlb19jZW50cmFsLmRhdGEudHJhY2tzICk7XG5cdGRlbGV0ZSB2aWRlb19jZW50cmFsLmRhdGEudHJhY2tzO1xuXG5cdHZhciBwb3N0Rm9ybSA9IG5ldyB2aWRlb19jZW50cmFsLnZpZXcuUG9zdEZvcm0oe1xuXHRcdGNvbGxlY3Rpb246IHRyYWNrcyxcblx0XHRsMTBuOiB2aWRlb19jZW50cmFsLmwxMG5cbiAgICB9KTtcbiAgICBcbn0gKCBqUXVlcnkgKSk7XG5cbiIsInZhciBBZGRUcmFja3NCdXR0b24sXG5cdCQgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snalF1ZXJ5J10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWydqUXVlcnknXSA6IG51bGwpLFxuXHR3b3JrZmxvd3MgPSByZXF1aXJlKCAnLi4vLi4vd29ya2Zsb3dzJyApLFxuXHR3cCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93Wyd3cCddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnd3AnXSA6IG51bGwpO1xuXG5BZGRUcmFja3NCdXR0b24gPSB3cC5CYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG5cdGlkOiAnYWRkLXRyYWNrcycsXG5cdHRhZ05hbWU6ICdwJyxcblxuXHRldmVudHM6IHtcblx0XHQnY2xpY2sgLmJ1dHRvbic6ICdjbGljaydcblx0fSxcblxuXHRpbml0aWFsaXplOiBmdW5jdGlvbiggb3B0aW9ucyApIHtcblx0XHR0aGlzLmwxMG4gPSBvcHRpb25zLmwxMG47XG5cdH0sXG5cblx0cmVuZGVyOiBmdW5jdGlvbigpIHtcblx0XHR2YXIgJGJ1dHRvbiA9ICQoICc8YSAvPicsIHtcblx0XHRcdHRleHQ6IHRoaXMubDEwbi5hZGRUcmFja3Ncblx0XHR9KS5hZGRDbGFzcyggJ2J1dHRvbiBidXR0b24tc2Vjb25kYXJ5JyApO1xuXG5cdFx0dGhpcy4kZWwuaHRtbCggJGJ1dHRvbiApO1xuXG5cdFx0cmV0dXJuIHRoaXM7XG5cdH0sXG5cblx0Y2xpY2s6IGZ1bmN0aW9uKCBlICkge1xuXHRcdGUucHJldmVudERlZmF1bHQoKTtcblx0XHR3b3JrZmxvd3MuZ2V0KCAnYWRkVHJhY2tzJyApLm9wZW4oKTtcblx0fVxufSk7XG5cbm1vZHVsZS5leHBvcnRzID0gQWRkVHJhY2tzQnV0dG9uO1xuIiwidmFyIE1lZGlhRnJhbWUsXG5cdF8gPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snXyddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnXyddIDogbnVsbCksXG5cdGwxMG4gPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1sndmlkZW9fY2VudHJhbCddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsndmlkZW9fY2VudHJhbCddIDogbnVsbCkubDEwbixcblx0d3AgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snd3AnXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ3dwJ10gOiBudWxsKTtcblxuTWVkaWFGcmFtZSA9IHdwLm1lZGlhLnZpZXcuTWVkaWFGcmFtZS5Qb3N0LmV4dGVuZCh7XG5cdGNyZWF0ZVN0YXRlczogZnVuY3Rpb24oKSB7XG5cdFx0dmFyIG9wdGlvbnMgPSB0aGlzLm9wdGlvbnM7XG5cblx0XHQvLyBBZGQgdGhlIGRlZmF1bHQgc3RhdGVzLlxuXHRcdHRoaXMuc3RhdGVzLmFkZChbXG5cdFx0XHQvLyBNYWluIHN0YXRlcy5cblx0XHRcdG5ldyB3cC5tZWRpYS5jb250cm9sbGVyLkxpYnJhcnkoe1xuXHRcdFx0XHRpZDogJ2luc2VydCcsXG5cdFx0XHRcdHRpdGxlOiB0aGlzLm9wdGlvbnMudGl0bGUsXG5cdFx0XHRcdHByaW9yaXR5OiAyMCxcblx0XHRcdFx0dG9vbGJhcjogJ21haW4taW5zZXJ0Jyxcblx0XHRcdFx0ZmlsdGVyYWJsZTogJ3VwbG9hZGVkJyxcblx0XHRcdFx0bGlicmFyeTogd3AubWVkaWEucXVlcnkoIG9wdGlvbnMubGlicmFyeSApLFxuXHRcdFx0XHRtdWx0aXBsZTogb3B0aW9ucy5tdWx0aXBsZSA/ICdyZXNldCcgOiBmYWxzZSxcblx0XHRcdFx0ZWRpdGFibGU6IGZhbHNlLFxuXG5cdFx0XHRcdC8vIElmIHRoZSB1c2VyIGlzbid0IGFsbG93ZWQgdG8gZWRpdCBmaWVsZHMsXG5cdFx0XHRcdC8vIGNhbiB0aGV5IHN0aWxsIGVkaXQgaXQgbG9jYWxseT9cblx0XHRcdFx0YWxsb3dMb2NhbEVkaXRzOiB0cnVlLFxuXG5cdFx0XHRcdC8vIFNob3cgdGhlIGF0dGFjaG1lbnQgZGlzcGxheSBzZXR0aW5ncy5cblx0XHRcdFx0ZGlzcGxheVNldHRpbmdzOiBmYWxzZSxcblx0XHRcdFx0Ly8gVXBkYXRlIHVzZXIgc2V0dGluZ3Mgd2hlbiB1c2VycyBhZGp1c3QgdGhlXG5cdFx0XHRcdC8vIGF0dGFjaG1lbnQgZGlzcGxheSBzZXR0aW5ncy5cblx0XHRcdFx0ZGlzcGxheVVzZXJTZXR0aW5nczogZmFsc2Vcblx0XHRcdH0pLFxuXG5cdFx0XHQvLyBFbWJlZCBzdGF0ZXMuXG5cdFx0XHRuZXcgd3AubWVkaWEuY29udHJvbGxlci5FbWJlZCh7XG5cdFx0XHRcdHRpdGxlOiBsMTBuLmFkZEZyb21VcmwsXG5cdFx0XHRcdG1lbnVJdGVtOiB7IHRleHQ6IGwxMG4uYWRkRnJvbVVybCwgcHJpb3JpdHk6IDEyMCB9LFxuXHRcdFx0XHR0eXBlOiAnbGluaydcblx0XHRcdH0pXG5cdFx0XSk7XG5cdH0sXG5cblx0YmluZEhhbmRsZXJzOiBmdW5jdGlvbigpIHtcblx0XHR3cC5tZWRpYS52aWV3Lk1lZGlhRnJhbWUuU2VsZWN0LnByb3RvdHlwZS5iaW5kSGFuZGxlcnMuYXBwbHkoIHRoaXMsIGFyZ3VtZW50cyApO1xuXG5cdFx0dGhpcy5vbiggJ3Rvb2xiYXI6Y3JlYXRlOm1haW4taW5zZXJ0JywgdGhpcy5jcmVhdGVUb29sYmFyLCB0aGlzICk7XG5cdFx0dGhpcy5vbiggJ3Rvb2xiYXI6Y3JlYXRlOm1haW4tZW1iZWQnLCB0aGlzLm1haW5FbWJlZFRvb2xiYXIsIHRoaXMgKTtcblxuXHRcdHZhciBoYW5kbGVycyA9IHtcblx0XHRcdFx0bWVudToge1xuXHRcdFx0XHRcdCdkZWZhdWx0JzogJ21haW5NZW51J1xuXHRcdFx0XHR9LFxuXG5cdFx0XHRcdGNvbnRlbnQ6IHtcblx0XHRcdFx0XHQnZW1iZWQnOiAnZW1iZWRDb250ZW50Jyxcblx0XHRcdFx0XHQnZWRpdC1zZWxlY3Rpb24nOiAnZWRpdFNlbGVjdGlvbkNvbnRlbnQnXG5cdFx0XHRcdH0sXG5cblx0XHRcdFx0dG9vbGJhcjoge1xuXHRcdFx0XHRcdCdtYWluLWluc2VydCc6ICdtYWluSW5zZXJ0VG9vbGJhcidcblx0XHRcdFx0fVxuXHRcdFx0fTtcblxuXHRcdF8uZWFjaCggaGFuZGxlcnMsIGZ1bmN0aW9uKCByZWdpb25IYW5kbGVycywgcmVnaW9uICkge1xuXHRcdFx0Xy5lYWNoKCByZWdpb25IYW5kbGVycywgZnVuY3Rpb24oIGNhbGxiYWNrLCBoYW5kbGVyICkge1xuXHRcdFx0XHR0aGlzLm9uKCByZWdpb24gKyAnOnJlbmRlcjonICsgaGFuZGxlciwgdGhpc1sgY2FsbGJhY2sgXSwgdGhpcyApO1xuXHRcdFx0fSwgdGhpcyApO1xuXHRcdH0sIHRoaXMgKTtcblx0fSxcblxuXHQvLyBUb29sYmFycy5cblx0bWFpbkluc2VydFRvb2xiYXI6IGZ1bmN0aW9uKCB2aWV3ICkge1xuXHRcdHZhciBjb250cm9sbGVyID0gdGhpcztcblxuXHRcdHRoaXMuc2VsZWN0aW9uU3RhdHVzVG9vbGJhciggdmlldyApO1xuXG5cdFx0dmlldy5zZXQoICdpbnNlcnQnLCB7XG5cdFx0XHRzdHlsZTogJ3ByaW1hcnknLFxuXHRcdFx0cHJpb3JpdHk6IDgwLFxuXHRcdFx0dGV4dDogY29udHJvbGxlci5vcHRpb25zLmJ1dHRvbi50ZXh0LFxuXHRcdFx0cmVxdWlyZXM6IHtcblx0XHRcdFx0c2VsZWN0aW9uOiB0cnVlXG5cdFx0XHR9LFxuXHRcdFx0Y2xpY2s6IGZ1bmN0aW9uKCkge1xuXHRcdFx0XHR2YXIgc3RhdGUgPSBjb250cm9sbGVyLnN0YXRlKCksXG5cdFx0XHRcdFx0c2VsZWN0aW9uID0gc3RhdGUuZ2V0KCAnc2VsZWN0aW9uJyApO1xuXG5cdFx0XHRcdGNvbnRyb2xsZXIuY2xvc2UoKTtcblx0XHRcdFx0c3RhdGUudHJpZ2dlciggJ2luc2VydCcsIHNlbGVjdGlvbiApLnJlc2V0KCk7XG5cdFx0XHR9XG5cdFx0fSk7XG5cdH0sXG5cblx0bWFpbkVtYmVkVG9vbGJhcjogZnVuY3Rpb24oIHRvb2xiYXIgKSB7XG5cdFx0dG9vbGJhci52aWV3ID0gbmV3IHdwLm1lZGlhLnZpZXcuVG9vbGJhci5FbWJlZCh7XG5cdFx0XHRjb250cm9sbGVyOiB0aGlzLFxuXHRcdFx0dGV4dDogdGhpcy5vcHRpb25zLmJ1dHRvbi50ZXh0XG5cdFx0fSk7XG5cdH1cbn0pO1xuXG5tb2R1bGUuZXhwb3J0cyA9IE1lZGlhRnJhbWU7XG4iLCJ2YXIgUG9zdEZvcm0sXG5cdCQgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snalF1ZXJ5J10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWydqUXVlcnknXSA6IG51bGwpLFxuXHRBZGRUcmFja3NCdXR0b24gPSByZXF1aXJlKCAnLi9idXR0b24vYWRkLXRyYWNrcycgKSxcblx0VHJhY2tMaXN0ID0gcmVxdWlyZSggJy4vdHJhY2stbGlzdCcgKSxcblx0d3AgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snd3AnXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ3dwJ10gOiBudWxsKTtcblxuUG9zdEZvcm0gPSB3cC5CYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG5cdGVsOiAnI3Bvc3QnLFxuXHRzYXZlZDogZmFsc2UsXG5cblx0ZXZlbnRzOiB7XG5cdFx0J2NsaWNrICNwdWJsaXNoJzogJ2J1dHRvbkNsaWNrJyxcblx0XHQnY2xpY2sgI3NhdmUtcG9zdCc6ICdidXR0b25DbGljaydcblx0XHQvLydzdWJtaXQnOiAnc3VibWl0J1xuXHR9LFxuXG5cdGluaXRpYWxpemU6IGZ1bmN0aW9uKCBvcHRpb25zICkge1xuXHRcdHRoaXMubDEwbiA9IG9wdGlvbnMubDEwbjtcblxuXHRcdHRoaXMucmVuZGVyKCk7XG5cdH0sXG5cblx0cmVuZGVyOiBmdW5jdGlvbigpIHtcblx0XHR0aGlzLnZpZXdzLmFkZCggJyN2aWRlby1jZW50cmFsLXBsYXlsaXN0LXBsYXlsaXN0LWVkaXRvciAudmlkZW8tY2VudHJhbC1wbGF5bGlzdC1wYW5lbC1ib2R5JywgW1xuXHRcdFx0bmV3IEFkZFRyYWNrc0J1dHRvbih7XG5cdFx0XHRcdGNvbGxlY3Rpb246IHRoaXMuY29sbGVjdGlvbixcblx0XHRcdFx0bDEwbjogdGhpcy5sMTBuXG5cdFx0XHR9KSxcblxuXHRcdFx0bmV3IFRyYWNrTGlzdCh7XG5cdFx0XHRcdGNvbGxlY3Rpb246IHRoaXMuY29sbGVjdGlvblxuXHRcdFx0fSlcblx0XHRdKTtcblxuXHRcdHJldHVybiB0aGlzO1xuXHR9LFxuXG5cdGJ1dHRvbkNsaWNrOiBmdW5jdGlvbiggZSApIHtcblx0XHR2YXIgc2VsZiA9IHRoaXMsXG5cdFx0XHQkYnV0dG9uID0gJCggZS50YXJnZXQgKTtcblxuXHRcdGlmICggISBzZWxmLnNhdmVkICkge1xuXHRcdFx0dGhpcy5jb2xsZWN0aW9uLnNhdmUoKS5kb25lKGZ1bmN0aW9uKCBkYXRhICkge1xuXHRcdFx0XHRzZWxmLnNhdmVkID0gdHJ1ZTtcblx0XHRcdFx0JGJ1dHRvbi5jbGljaygpO1xuXHRcdFx0fSk7XG5cdFx0fVxuXG5cdFx0cmV0dXJuIHNlbGYuc2F2ZWQ7XG5cdH1cbn0pO1xuXG5tb2R1bGUuZXhwb3J0cyA9IFBvc3RGb3JtO1xuIiwidmFyIFRyYWNrTGlzdCxcblx0JCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93WydqUXVlcnknXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ2pRdWVyeSddIDogbnVsbCksXG5cdF8gPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snXyddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnXyddIDogbnVsbCksXG5cdFRyYWNrID0gcmVxdWlyZSggJy4vdHJhY2snICksXG5cdHdwID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ3dwJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWyd3cCddIDogbnVsbCk7XG5cblRyYWNrTGlzdCA9IHdwLkJhY2tib25lLlZpZXcuZXh0ZW5kKHtcblx0Y2xhc3NOYW1lOiAndmlkZW8tY2VudHJhbC1wbGF5bGlzdC10cmFja2xpc3QnLFxuXHR0YWdOYW1lOiAnb2wnLFxuXG5cdGluaXRpYWxpemU6IGZ1bmN0aW9uKCkge1xuXHRcdHRoaXMubGlzdGVuVG8oIHRoaXMuY29sbGVjdGlvbiwgJ2FkZCcsIHRoaXMuYWRkVHJhY2sgKTtcblx0XHR0aGlzLmxpc3RlblRvKCB0aGlzLmNvbGxlY3Rpb24sICdhZGQgcmVtb3ZlJywgdGhpcy51cGRhdGVPcmRlciApO1xuXHRcdHRoaXMubGlzdGVuVG8oIHRoaXMuY29sbGVjdGlvbiwgJ3Jlc2V0JywgdGhpcy5yZW5kZXIgKTtcblx0fSxcblxuXHRyZW5kZXI6IGZ1bmN0aW9uKCkge1xuXHRcdHRoaXMuJGVsLmVtcHR5KCk7XG5cblx0XHR0aGlzLmNvbGxlY3Rpb24uZWFjaCggdGhpcy5hZGRUcmFjaywgdGhpcyApO1xuXHRcdHRoaXMudXBkYXRlT3JkZXIoKTtcblxuXHRcdHRoaXMuJGVsLnNvcnRhYmxlKCB7XG5cdFx0XHRheGlzOiAneScsXG5cdFx0XHRkZWxheTogMTUwLFxuXHRcdFx0Zm9yY2VIZWxwZXJTaXplOiB0cnVlLFxuXHRcdFx0Zm9yY2VQbGFjZWhvbGRlclNpemU6IHRydWUsXG5cdFx0XHRvcGFjaXR5OiAwLjYsXG5cdFx0XHRzdGFydDogZnVuY3Rpb24oIGUsIHVpICkge1xuXHRcdFx0XHR1aS5wbGFjZWhvbGRlci5jc3MoICd2aXNpYmlsaXR5JywgJ3Zpc2libGUnICk7XG5cdFx0XHR9LFxuXHRcdFx0dXBkYXRlOiBfLmJpbmQoZnVuY3Rpb24oIGUsIHVpICkge1xuXHRcdFx0XHR0aGlzLnVwZGF0ZU9yZGVyKCk7XG5cdFx0XHR9LCB0aGlzIClcblx0XHR9ICk7XG5cblx0XHRyZXR1cm4gdGhpcztcblx0fSxcblxuXHRhZGRUcmFjazogZnVuY3Rpb24oIHRyYWNrICkge1xuXHRcdHZhciB0cmFja1ZpZXcgPSBuZXcgVHJhY2soeyBtb2RlbDogdHJhY2sgfSk7XG5cdFx0dGhpcy4kZWwuYXBwZW5kKCB0cmFja1ZpZXcucmVuZGVyKCkuZWwgKTtcblx0fSxcblxuXHR1cGRhdGVPcmRlcjogZnVuY3Rpb24oKSB7XG5cdFx0Xy5lYWNoKCB0aGlzLiRlbC5maW5kKCAnLnZpZGVvLWNlbnRyYWwtcGxheWxpc3QtdHJhY2snICksIGZ1bmN0aW9uKCBpdGVtLCBpICkge1xuXHRcdFx0dmFyIGNpZCA9ICQoIGl0ZW0gKS5kYXRhKCAnY2lkJyApO1xuXHRcdFx0dGhpcy5jb2xsZWN0aW9uLmdldCggY2lkICkuc2V0KCAnb3JkZXInLCBpICk7XG5cdFx0fSwgdGhpcyApO1xuXHR9XG59KTtcblxubW9kdWxlLmV4cG9ydHMgPSBUcmFja0xpc3Q7XG4iLCJ2YXIgVHJhY2ssXG5cdCQgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snalF1ZXJ5J10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWydqUXVlcnknXSA6IG51bGwpLFxuXHRUcmFja0FydHdvcmsgPSByZXF1aXJlKCAnLi90cmFjay9hcnR3b3JrJyApLFxuXHRUcmFja0F1ZGlvID0gcmVxdWlyZSggJy4vdHJhY2svYXVkaW8nICksXG5cdHdwID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ3dwJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWyd3cCddIDogbnVsbCk7XG5cblRyYWNrID0gd3AuQmFja2JvbmUuVmlldy5leHRlbmQoe1xuXHR0YWdOYW1lOiAnbGknLFxuXHRjbGFzc05hbWU6ICd2aWRlby1jZW50cmFsLXBsYXlsaXN0LXRyYWNrJyxcblx0dGVtcGxhdGU6IHdwLnRlbXBsYXRlKCAndmlkZW8tY2VudHJhbC1wbGF5bGlzdC1wbGF5bGlzdC10cmFjaycgKSxcblxuXHRldmVudHM6IHtcblx0XHQnY2hhbmdlIFtkYXRhLXNldHRpbmddJzogJ3VwZGF0ZUF0dHJpYnV0ZScsXG5cdFx0J2NsaWNrIC5qcy10b2dnbGUnOiAndG9nZ2xlT3BlblN0YXR1cycsXG5cdFx0J2RibGNsaWNrIC52aWRlby1jZW50cmFsLXBsYXlsaXN0LXRyYWNrLXRpdGxlJzogJ3RvZ2dsZU9wZW5TdGF0dXMnLFxuXHRcdCdjbGljayAuanMtY2xvc2UnOiAnbWluaW1pemUnLFxuXHRcdCdjbGljayAuanMtcmVtb3ZlJzogJ2Rlc3Ryb3knXG5cdH0sXG5cblx0aW5pdGlhbGl6ZTogZnVuY3Rpb24oKSB7XG5cdFx0dGhpcy5saXN0ZW5UbyggdGhpcy5tb2RlbCwgJ2NoYW5nZTp0aXRsZScsIHRoaXMudXBkYXRlVGl0bGUgKTtcblx0XHR0aGlzLmxpc3RlblRvKCB0aGlzLm1vZGVsLCAnY2hhbmdlJywgdGhpcy51cGRhdGVGaWVsZHMgKTtcblx0XHR0aGlzLmxpc3RlblRvKCB0aGlzLm1vZGVsLCAnZGVzdHJveScsIHRoaXMucmVtb3ZlICk7XG5cdH0sXG5cblx0cmVuZGVyOiBmdW5jdGlvbigpIHtcblx0XHR0aGlzLiRlbC5odG1sKCB0aGlzLnRlbXBsYXRlKCB0aGlzLm1vZGVsLnRvSlNPTigpICkgKS5kYXRhKCAnY2lkJywgdGhpcy5tb2RlbC5jaWQgKTtcblxuXHRcdHRoaXMudmlld3MuYWRkKCAnLnZpZGVvLWNlbnRyYWwtcGxheWxpc3QtdHJhY2stY29sdW1uLWFydHdvcmsnLCBuZXcgVHJhY2tBcnR3b3JrKHtcblx0XHRcdG1vZGVsOiB0aGlzLm1vZGVsLFxuXHRcdFx0cGFyZW50OiB0aGlzXG5cdFx0fSkpO1xuXG5cdFx0dGhpcy52aWV3cy5hZGQoICcudmlkZW8tY2VudHJhbC1wbGF5bGlzdC10cmFjay1hdWRpby1ncm91cCcsIG5ldyBUcmFja0F1ZGlvKHtcblx0XHRcdG1vZGVsOiB0aGlzLm1vZGVsLFxuXHRcdFx0cGFyZW50OiB0aGlzXG5cdFx0fSkpO1xuXG5cdFx0cmV0dXJuIHRoaXM7XG5cdH0sXG5cblx0bWluaW1pemU6IGZ1bmN0aW9uKCBlICkge1xuXHRcdGUucHJldmVudERlZmF1bHQoKTtcblx0XHR0aGlzLiRlbC5yZW1vdmVDbGFzcyggJ2lzLW9wZW4nICkuZmluZCggJ2lucHV0OmZvY3VzJyApLmJsdXIoKTtcblx0fSxcblxuXHR0b2dnbGVPcGVuU3RhdHVzOiBmdW5jdGlvbiggZSApIHtcblx0XHRlLnByZXZlbnREZWZhdWx0KCk7XG5cdFx0dGhpcy4kZWwudG9nZ2xlQ2xhc3MoICdpcy1vcGVuJyApLmZpbmQoICdpbnB1dDpmb2N1cycgKS5ibHVyKCk7XG5cblx0XHQvLyBUcmlnZ2VyIGEgcmVzaXplIHNvIHRoZSBtZWRpYSBlbGVtZW50IHdpbGwgZmlsbCB0aGUgY29udGFpbmVyLlxuXHRcdGlmICggdGhpcy4kZWwuaGFzQ2xhc3MoICdpcy1vcGVuJyApICkge1xuXHRcdFx0JCggd2luZG93ICkudHJpZ2dlciggJ3Jlc2l6ZScgKTtcblx0XHR9XG5cdH0sXG5cblx0LyoqXG5cdCAqIFVwZGF0ZSBhIG1vZGVsIGF0dHJpYnV0ZSB3aGVuIGEgZmllbGQgaXMgY2hhbmdlZC5cblx0ICpcblx0ICogRmllbGRzIHdpdGggYSAnZGF0YS1zZXR0aW5nPVwie3trZXl9fVwiJyBhdHRyaWJ1dGUgd2hvc2UgdmFsdWVcblx0ICogY29ycmVzcG9uZHMgdG8gYSBtb2RlbCBhdHRyaWJ1dGUgd2lsbCBiZSBhdXRvbWF0aWNhbGx5IHN5bmNlZC5cblx0ICpcblx0ICogQHBhcmFtIHtPYmplY3R9IGUgRXZlbnQgb2JqZWN0LlxuXHQgKi9cblx0dXBkYXRlQXR0cmlidXRlOiBmdW5jdGlvbiggZSApIHtcblx0XHR2YXIgYXR0cmlidXRlID0gJCggZS50YXJnZXQgKS5kYXRhKCAnc2V0dGluZycgKSxcblx0XHRcdHZhbHVlID0gZS50YXJnZXQudmFsdWU7XG5cblx0XHRpZiAoIHRoaXMubW9kZWwuZ2V0KCBhdHRyaWJ1dGUgKSAhPT0gdmFsdWUgKSB7XG5cdFx0XHR0aGlzLm1vZGVsLnNldCggYXR0cmlidXRlLCB2YWx1ZSApO1xuXHRcdH1cblx0fSxcblxuXHQvKipcblx0ICogVXBkYXRlIGEgc2V0dGluZyBmaWVsZCB3aGVuIGEgbW9kZWwncyBhdHRyaWJ1dGUgaXMgY2hhbmdlZC5cblx0ICovXG5cdHVwZGF0ZUZpZWxkczogZnVuY3Rpb24oKSB7XG5cdFx0dmFyIHRyYWNrID0gdGhpcy5tb2RlbC50b0pTT04oKSxcblx0XHRcdCRzZXR0aW5ncyA9IHRoaXMuJGVsLmZpbmQoICdbZGF0YS1zZXR0aW5nXScgKSxcblx0XHRcdGF0dHJpYnV0ZSwgdmFsdWU7XG5cblx0XHQvLyBBIGNoYW5nZSBldmVudCBzaG91bGRuJ3QgYmUgdHJpZ2dlcmVkIGhlcmUsIHNvIGl0IHdvbid0IGNhdXNlXG5cdFx0Ly8gdGhlIG1vZGVsIGF0dHJpYnV0ZSB0byBiZSB1cGRhdGVkIGFuZCBnZXQgc3R1Y2sgaW4gYW5cblx0XHQvLyBpbmZpbml0ZSBsb29wLlxuXHRcdGZvciAoIGF0dHJpYnV0ZSBpbiB0cmFjayApIHtcblx0XHRcdC8vIERlY29kZSBIVE1MIGVudGl0aWVzLlxuXHRcdFx0dmFsdWUgPSAkKCAnPGRpdi8+JyApLmh0bWwoIHRyYWNrWyBhdHRyaWJ1dGUgXSApLnRleHQoKTtcblx0XHRcdCRzZXR0aW5ncy5maWx0ZXIoICdbZGF0YS1zZXR0aW5nPVwiJyArIGF0dHJpYnV0ZSArICdcIl0nICkudmFsKCB2YWx1ZSApO1xuXHRcdH1cblx0fSxcblxuXHR1cGRhdGVUaXRsZTogZnVuY3Rpb24oKSB7XG5cdFx0dmFyIHRpdGxlID0gdGhpcy5tb2RlbC5nZXQoICd0aXRsZScgKTtcblx0XHR0aGlzLiRlbC5maW5kKCAnLnZpZGVvLWNlbnRyYWwtcGxheWxpc3QtdHJhY2stdGl0bGUgLnRleHQnICkudGV4dCggdGl0bGUgPyB0aXRsZSA6ICdUaXRsZScgKTtcblx0fSxcblxuXHQvKipcblx0ICogRGVzdHJveSB0aGUgdmlldydzIG1vZGVsLlxuXHQgKlxuXHQgKiBBdm9pZCBzeW5jaW5nIHRvIHRoZSBzZXJ2ZXIgYnkgdHJpZ2dlcmluZyBhbiBldmVudCBpbnN0ZWFkIG9mXG5cdCAqIGNhbGxpbmcgZGVzdHJveSgpIGRpcmVjdGx5IG9uIHRoZSBtb2RlbC5cblx0ICovXG5cdGRlc3Ryb3k6IGZ1bmN0aW9uKCkge1xuXHRcdHRoaXMubW9kZWwudHJpZ2dlciggJ2Rlc3Ryb3knLCB0aGlzLm1vZGVsICk7XG5cdH0sXG5cblx0cmVtb3ZlOiBmdW5jdGlvbigpIHtcblx0XHR0aGlzLiRlbC5yZW1vdmUoKTtcblx0fVxufSk7XG5cbm1vZHVsZS5leHBvcnRzID0gVHJhY2s7XG4iLCJ2YXIgVHJhY2tBcnR3b3JrLFxuXHRfID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ18nXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ18nXSA6IG51bGwpLFxuXHR3b3JrZmxvd3MgPSByZXF1aXJlKCAnLi4vLi4vd29ya2Zsb3dzJyApLFxuXHR3cCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93Wyd3cCddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnd3AnXSA6IG51bGwpO1xuXG5UcmFja0FydHdvcmsgPSB3cC5CYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG5cdHRhZ05hbWU6ICdzcGFuJyxcblx0Y2xhc3NOYW1lOiAndmlkZW8tY2VudHJhbC1wbGF5bGlzdC10cmFjay1hcnR3b3JrJyxcblx0dGVtcGxhdGU6IHdwLnRlbXBsYXRlKCAndmlkZW8tY2VudHJhbC1wbGF5bGlzdC1wbGF5bGlzdC10cmFjay1hcnR3b3JrJyApLFxuXG5cdGV2ZW50czoge1xuXHRcdCdjbGljayc6ICdzZWxlY3QnXG5cdH0sXG5cblx0aW5pdGlhbGl6ZTogZnVuY3Rpb24oIG9wdGlvbnMgKSB7XG5cdFx0dGhpcy5wYXJlbnQgPSBvcHRpb25zLnBhcmVudDtcblx0XHR0aGlzLmxpc3RlblRvKCB0aGlzLm1vZGVsLCAnY2hhbmdlOmFydHdvcmtVcmwnLCB0aGlzLnJlbmRlciApO1xuXHR9LFxuXG5cdHJlbmRlcjogZnVuY3Rpb24oKSB7XG5cdFx0dGhpcy4kZWwuaHRtbCggdGhpcy50ZW1wbGF0ZSggdGhpcy5tb2RlbC50b0pTT04oKSApICk7XG5cdFx0dGhpcy5wYXJlbnQuJGVsLnRvZ2dsZUNsYXNzKCAnaGFzLWFydHdvcmsnLCAhIF8uaXNFbXB0eSggdGhpcy5tb2RlbC5nZXQoICdhcnR3b3JrVXJsJyApICkgKTtcblx0XHRyZXR1cm4gdGhpcztcblx0fSxcblxuXHRzZWxlY3Q6IGZ1bmN0aW9uKCkge1xuXHRcdHdvcmtmbG93cy5zZXRNb2RlbCggdGhpcy5tb2RlbCApLmdldCggJ3NlbGVjdEFydHdvcmsnICkub3BlbigpO1xuXHR9XG59KTtcblxubW9kdWxlLmV4cG9ydHMgPSBUcmFja0FydHdvcms7XG4iLCJ2YXIgVHJhY2tBdWRpbyxcblx0JCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93WydqUXVlcnknXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ2pRdWVyeSddIDogbnVsbCksXG5cdF8gPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snXyddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnXyddIDogbnVsbCksXG5cdHNldHRpbmdzID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ3ZpZGVvX2NlbnRyYWwnXSA6IHR5cGVvZiBnbG9iYWwgIT09IFwidW5kZWZpbmVkXCIgPyBnbG9iYWxbJ3ZpZGVvX2NlbnRyYWwnXSA6IG51bGwpLnNldHRpbmdzKCksXG5cdHdvcmtmbG93cyA9IHJlcXVpcmUoICcuLi8uLi93b3JrZmxvd3MnICksXG5cdHdwID0gKHR5cGVvZiB3aW5kb3cgIT09IFwidW5kZWZpbmVkXCIgPyB3aW5kb3dbJ3dwJ10gOiB0eXBlb2YgZ2xvYmFsICE9PSBcInVuZGVmaW5lZFwiID8gZ2xvYmFsWyd3cCddIDogbnVsbCk7XG5cblRyYWNrQXVkaW8gPSB3cC5CYWNrYm9uZS5WaWV3LmV4dGVuZCh7XG5cdHRhZ05hbWU6ICdzcGFuJyxcblx0Y2xhc3NOYW1lOiAndmlkZW8tY2VudHJhbC1wbGF5bGlzdC10cmFjay1hdWRpbycsXG5cdHRlbXBsYXRlOiB3cC50ZW1wbGF0ZSggJ3ZpZGVvLWNlbnRyYWwtcGxheWxpc3QtcGxheWxpc3QtdHJhY2stYXVkaW8nICksXG5cblx0ZXZlbnRzOiB7XG5cdFx0J2NsaWNrIC52aWRlby1jZW50cmFsLXBsYXlsaXN0LXRyYWNrLWF1ZGlvLXNlbGVjdG9yJzogJ3NlbGVjdCdcblx0fSxcblxuXHRpbml0aWFsaXplOiBmdW5jdGlvbiggb3B0aW9ucyApIHtcblx0XHR0aGlzLnBhcmVudCA9IG9wdGlvbnMucGFyZW50O1xuXG5cdFx0dGhpcy5saXN0ZW5UbyggdGhpcy5tb2RlbCwgJ2NoYW5nZTphdWRpb1VybCcsIHRoaXMucmVmcmVzaCApO1xuXHRcdHRoaXMubGlzdGVuVG8oIHRoaXMubW9kZWwsICdkZXN0cm95JywgdGhpcy5jbGVhbnVwICk7XG5cdH0sXG5cblx0cmVuZGVyOiBmdW5jdGlvbigpIHtcblx0XHR2YXIgJG1lZGlhRWwsIHBsYXllclNldHRpbmdzLFxuXHRcdFx0dHJhY2sgPSB0aGlzLm1vZGVsLnRvSlNPTigpLFxuXHRcdFx0cGxheWVySWQgPSB0aGlzLiRlbC5maW5kKCAnLm1lanMtYXVkaW8nICkuYXR0ciggJ2lkJyApO1xuXG5cdFx0Ly8gUmVtb3ZlIHRoZSBNZWRpYUVsZW1lbnQgcGxheWVyIG9iamVjdCBpZiB0aGVcblx0XHQvLyBhdWRpbyBmaWxlIFVSTCBpcyBlbXB0eS5cblx0XHRpZiAoICcnID09PSB0cmFjay5hdWRpb1VybCAmJiBwbGF5ZXJJZCApIHtcblx0XHRcdG1lanMucGxheWVyc1sgcGxheWVySWQgXS5yZW1vdmUoKTtcblx0XHR9XG5cblx0XHQvLyBSZW5kZXIgdGhlIG1lZGlhIGVsZW1lbnQuXG5cdFx0dGhpcy4kZWwuaHRtbCggdGhpcy50ZW1wbGF0ZSggdGhpcy5tb2RlbC50b0pTT04oKSApICk7XG5cblx0XHQvLyBTZXQgdXAgTWVkaWFFbGVtZW50LmpzLlxuXHRcdCRtZWRpYUVsID0gdGhpcy4kZWwuZmluZCggJy52aWRlby1jZW50cmFsLXBsYXlsaXN0LWF1ZGlvJyApO1xuXG5cdFx0cmV0dXJuIHRoaXM7XG5cdH0sXG5cblx0cmVmcmVzaDogZnVuY3Rpb24oIGUgKSB7XG5cdFx0dmFyIHRyYWNrID0gdGhpcy5tb2RlbC50b0pTT04oKSxcblx0XHRcdHBsYXllcklkID0gdGhpcy4kZWwuZmluZCggJy5tZWpzLWF1ZGlvJyApLmF0dHIoICdpZCcgKSxcblx0XHRcdHBsYXllciA9IHBsYXllcklkID8gbWVqcy5wbGF5ZXJzWyBwbGF5ZXJJZCBdIDogbnVsbDtcblxuXHRcdGlmICggcGxheWVyICYmICcnICE9PSB0cmFjay5hdWRpb1VybCApIHtcblx0XHRcdHBsYXllci5wYXVzZSgpO1xuXHRcdFx0cGxheWVyLnNldFNyYyggdHJhY2suYXVkaW9VcmwgKTtcblx0XHR9IGVsc2Uge1xuXHRcdFx0dGhpcy5yZW5kZXIoKTtcblx0XHR9XG5cdH0sXG5cblx0Y2xlYW51cDogZnVuY3Rpb24oKSB7XG5cdFx0dmFyIHBsYXllcklkID0gdGhpcy4kZWwuZmluZCggJy5tZWpzLWF1ZGlvJyApLmF0dHIoICdpZCcgKSxcblx0XHRcdHBsYXllciA9IHBsYXllcklkID8gbWVqcy5wbGF5ZXJzWyBwbGF5ZXJJZCBdIDogbnVsbDtcblxuXHRcdGlmICggcGxheWVyICkge1xuXHRcdFx0cGxheWVyLnJlbW92ZSgpO1xuXHRcdH1cblx0fSxcblxuXHRzZWxlY3Q6IGZ1bmN0aW9uKCkge1xuXHRcdHdvcmtmbG93cy5zZXRNb2RlbCggdGhpcy5tb2RlbCApLmdldCggJ3NlbGVjdEF1ZGlvJyApLm9wZW4oKTtcblx0fVxufSk7XG5cbm1vZHVsZS5leHBvcnRzID0gVHJhY2tBdWRpbztcbiIsInZhciBXb3JrZmxvd3MsXG5cdF8gPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1snXyddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnXyddIDogbnVsbCksXG5cdHZpZGVvX2NlbnRyYWwgPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1sndmlkZW9fY2VudHJhbCddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsndmlkZW9fY2VudHJhbCddIDogbnVsbCksXG5cdGwxMG4gPSAodHlwZW9mIHdpbmRvdyAhPT0gXCJ1bmRlZmluZWRcIiA/IHdpbmRvd1sndmlkZW9fY2VudHJhbCddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsndmlkZW9fY2VudHJhbCddIDogbnVsbCkubDEwbixcblx0TWVkaWFGcmFtZSA9IHJlcXVpcmUoICcuL3ZpZXdzL21lZGlhLWZyYW1lJyApLFxuXHR3cCA9ICh0eXBlb2Ygd2luZG93ICE9PSBcInVuZGVmaW5lZFwiID8gd2luZG93Wyd3cCddIDogdHlwZW9mIGdsb2JhbCAhPT0gXCJ1bmRlZmluZWRcIiA/IGdsb2JhbFsnd3AnXSA6IG51bGwpLFxuXHRBdHRhY2htZW50ID0gd3AubWVkaWEubW9kZWwuQXR0YWNobWVudDtcblxuV29ya2Zsb3dzID0ge1xuXHRmcmFtZXM6IFtdLFxuXHRtb2RlbDoge30sXG5cblx0LyoqXG5cdCAqIFNldCBhIG1vZGVsIGZvciB0aGUgY3VycmVudCB3b3JrZmxvdy5cblx0ICpcblx0ICogQHBhcmFtIHtPYmplY3R9IGZyYW1lXG5cdCAqL1xuXHRzZXRNb2RlbDogZnVuY3Rpb24oIG1vZGVsICkge1xuXHRcdHRoaXMubW9kZWwgPSBtb2RlbDtcblx0XHRyZXR1cm4gdGhpcztcblx0fSxcblxuXHQvKipcblx0ICogUmV0cmlldmUgb3IgY3JlYXRlIGEgZnJhbWUgaW5zdGFuY2UgZm9yIGEgcGFydGljdWxhciB3b3JrZmxvdy5cblx0ICpcblx0ICogQHBhcmFtIHtzdHJpbmd9IGlkIEZyYW1lIGlkZW50aWZlci5cblx0ICovXG5cdGdldDogZnVuY3Rpb24oIGlkICkgIHtcblx0XHR2YXIgbWV0aG9kID0gJ18nICsgaWQsXG5cdFx0XHRmcmFtZSA9IHRoaXMuZnJhbWVzWyBtZXRob2QgXSB8fCBudWxsO1xuXG5cdFx0Ly8gQWx3YXlzIGNhbGwgdGhlIGZyYW1lIG1ldGhvZCB0byBwZXJmb3JtIGFueSByb3V0aW5lIHNldCB1cC4gVGhlXG5cdFx0Ly8gZnJhbWUgbWV0aG9kIHNob3VsZCBzaG9ydC1jaXJjdWl0IGJlZm9yZSBiZWluZyBpbml0aWFsaXplZCBhZ2Fpbi5cblx0XHRmcmFtZSA9IHRoaXNbIG1ldGhvZCBdLmNhbGwoIHRoaXMsIGZyYW1lICk7XG5cblx0XHQvLyBTdG9yZSB0aGUgZnJhbWUgZm9yIGZ1dHVyZSB1c2UuXG5cdFx0dGhpcy5mcmFtZXNbIG1ldGhvZCBdID0gZnJhbWU7XG5cblx0XHRyZXR1cm4gZnJhbWU7XG5cdH0sXG5cblx0LyoqXG5cdCAqIFdvcmtmbG93IGZvciBhZGRpbmcgdHJhY2tzIHRvIHRoZSBwbGF5bGlzdC5cblx0ICpcblx0ICogQHBhcmFtIHtPYmplY3R9IGZyYW1lXG5cdCAqL1xuXHRfYWRkVHJhY2tzOiBmdW5jdGlvbiggZnJhbWUgKSB7XG5cdFx0Ly8gUmV0dXJuIHRoZSBleGlzdGluZyBmcmFtZSBmb3IgdGhpcyB3b3JrZmxvdy5cblx0XHRpZiAoIGZyYW1lICkge1xuXHRcdFx0cmV0dXJuIGZyYW1lO1xuXHRcdH1cblxuXHRcdC8vIEluaXRpYWxpemUgdGhlIGF1ZGlvIGZyYW1lLlxuXHRcdGZyYW1lID0gbmV3IE1lZGlhRnJhbWUoe1xuXHRcdFx0dGl0bGU6IGwxMG4ud29ya2Zsb3dzLmFkZFRyYWNrcy5mcmFtZVRpdGxlLFxuXHRcdFx0bGlicmFyeToge1xuXHRcdFx0XHR0eXBlOiAnYXVkaW8nXG5cdFx0XHR9LFxuXHRcdFx0YnV0dG9uOiB7XG5cdFx0XHRcdHRleHQ6IGwxMG4ud29ya2Zsb3dzLmFkZFRyYWNrcy5mcmFtZUJ1dHRvblRleHRcblx0XHRcdH0sXG5cdFx0XHRtdWx0aXBsZTogJ2FkZCdcblx0XHR9KTtcblxuXHRcdC8vIFNldCB0aGUgZXh0ZW5zaW9ucyB0aGF0IGNhbiBiZSB1cGxvYWRlZC5cblx0XHRmcmFtZS51cGxvYWRlci5vcHRpb25zLnVwbG9hZGVyLnBsdXBsb2FkID0ge1xuXHRcdFx0ZmlsdGVyczoge1xuXHRcdFx0XHRtaW1lX3R5cGVzOiBbe1xuXHRcdFx0XHRcdHRpdGxlOiBsMTBuLndvcmtmbG93cy5hZGRUcmFja3MuZmlsZVR5cGVzLFxuXHRcdFx0XHRcdGV4dGVuc2lvbnM6ICdtNGEsbXAzLG9nZyx3bWEnXG5cdFx0XHRcdH1dXG5cdFx0XHR9XG5cdFx0fTtcblxuXHRcdC8vIFByZXZlbnQgdGhlIEVtYmVkIGNvbnRyb2xsZXIgc2Nhbm5lciBmcm9tIGNoYW5naW5nIHRoZSBzdGF0ZS5cblx0XHRmcmFtZS5zdGF0ZSggJ2VtYmVkJyApLnByb3BzLm9mZiggJ2NoYW5nZTp1cmwnLCBmcmFtZS5zdGF0ZSggJ2VtYmVkJyApLmRlYm91bmNlZFNjYW4gKTtcblxuXHRcdC8vIEluc2VydCBlYWNoIHNlbGVjdGVkIGF0dGFjaG1lbnQgYXMgYSBuZXcgdHJhY2sgbW9kZWwuXG5cdFx0ZnJhbWUuc3RhdGUoICdpbnNlcnQnICkub24oICdpbnNlcnQnLCBmdW5jdGlvbiggc2VsZWN0aW9uICkge1xuXHRcdFx0Xy5lYWNoKCBzZWxlY3Rpb24ubW9kZWxzLCBmdW5jdGlvbiggYXR0YWNobWVudCApIHtcbiAgICAgICAgICAgICAgICBjb25zb2xlLmxvZyhhdHRhY2htZW50LnRvSlNPTigpKTtcbiAgICAgICAgICAgICAgICB2aWRlb19jZW50cmFsLnRyYWNrcy5wdXNoKCBhdHRhY2htZW50LnRvSlNPTigpLnZpZGVvX2NlbnRyYWwgKTtcbiAgICAgICAgICAgICAgICBjb25zb2xlLmxvZyggdmlkZW9fY2VudHJhbC50cmFja3MgKTtcblx0XHRcdH0pO1xuXHRcdH0pO1xuXG5cdFx0Ly8gSW5zZXJ0IHRoZSBlbWJlZCBkYXRhIGFzIGEgbmV3IG1vZGVsLlxuXHRcdGZyYW1lLnN0YXRlKCAnZW1iZWQnICkub24oICdzZWxlY3QnLCBmdW5jdGlvbigpIHtcblxuXHRcdFx0dmFyIGVtYmVkID0gdGhpcy5wcm9wcy50b0pTT04oKSxcblx0XHRcdFx0dHJhY2sgPSB7XG5cdFx0XHRcdFx0dmlkZW9JZDogJycsXG5cdFx0XHRcdFx0YXVkaW9Vcmw6IGVtYmVkLnVybFxuXHRcdFx0XHR9O1xuXG5cdFx0XHRpZiAoICggJ3RpdGxlJyBpbiBlbWJlZCApICYmICcnICE9PSBlbWJlZC50aXRsZSApIHtcblx0XHRcdFx0dHJhY2sudGl0bGUgPSBlbWJlZC50aXRsZTtcblx0XHRcdH1cblxuXHRcdFx0dmlkZW9fY2VudHJhbC50cmFja3MucHVzaCggdHJhY2sgKTtcblx0XHR9KTtcblxuXHRcdHJldHVybiBmcmFtZTtcblx0fSxcblxuXHQvKipcblx0ICogV29ya2Zsb3cgZm9yIHNlbGVjdGluZyB0cmFjayBhcnR3b3JrIGltYWdlLlxuXHQgKlxuXHQgKiBAcGFyYW0ge09iamVjdH0gZnJhbWVcblx0ICovXG5cdF9zZWxlY3RBcnR3b3JrOiBmdW5jdGlvbiggZnJhbWUgKSB7XG5cdFx0dmFyIHdvcmtmbG93ID0gdGhpcztcblxuXHRcdC8vIFJldHVybiBleGlzdGluZyBmcmFtZSBmb3IgdGhpcyB3b3JrZmxvdy5cblx0XHRpZiAoIGZyYW1lICkge1xuXHRcdFx0cmV0dXJuIGZyYW1lO1xuXHRcdH1cblxuXHRcdC8vIEluaXRpYWxpemUgdGhlIGFydHdvcmsgZnJhbWUuXG5cdFx0ZnJhbWUgPSB3cC5tZWRpYSh7XG5cdFx0XHR0aXRsZTogbDEwbi53b3JrZmxvd3Muc2VsZWN0QXJ0d29yay5mcmFtZVRpdGxlLFxuXHRcdFx0bGlicmFyeToge1xuXHRcdFx0XHR0eXBlOiAnaW1hZ2UnXG5cdFx0XHR9LFxuXHRcdFx0YnV0dG9uOiB7XG5cdFx0XHRcdHRleHQ6IGwxMG4ud29ya2Zsb3dzLnNlbGVjdEFydHdvcmsuZnJhbWVCdXR0b25UZXh0XG5cdFx0XHR9LFxuXHRcdFx0bXVsdGlwbGU6IGZhbHNlXG5cdFx0fSk7XG5cblx0XHQvLyBTZXQgdGhlIGV4dGVuc2lvbnMgdGhhdCBjYW4gYmUgdXBsb2FkZWQuXG5cdFx0ZnJhbWUudXBsb2FkZXIub3B0aW9ucy51cGxvYWRlci5wbHVwbG9hZCA9IHtcblx0XHRcdGZpbHRlcnM6IHtcblx0XHRcdFx0bWltZV90eXBlczogW3tcblx0XHRcdFx0XHRmaWxlczogbDEwbi53b3JrZmxvd3Muc2VsZWN0QXJ0d29yay5maWxlVHlwZXMsXG5cdFx0XHRcdFx0ZXh0ZW5zaW9uczogJ2pwZyxqcGVnLGdpZixwbmcnXG5cdFx0XHRcdH1dXG5cdFx0XHR9XG5cdFx0fTtcblxuXHRcdC8vIEF1dG9tYXRpY2FsbHkgc2VsZWN0IHRoZSBleGlzdGluZyBhcnR3b3JrIGlmIHBvc3NpYmxlLlxuXHRcdGZyYW1lLm9uKCAnb3BlbicsIGZ1bmN0aW9uKCkge1xuXHRcdFx0dmFyIHNlbGVjdGlvbiA9IHRoaXMuZ2V0KCAnbGlicmFyeScgKS5nZXQoICdzZWxlY3Rpb24nICksXG5cdFx0XHRcdGFydHdvcmtJZCA9IHdvcmtmbG93Lm1vZGVsLmdldCggJ2FydHdvcmtJZCcgKSxcblx0XHRcdFx0YXR0YWNobWVudHMgPSBbXTtcblxuXHRcdFx0aWYgKCBhcnR3b3JrSWQgKSB7XG5cdFx0XHRcdGF0dGFjaG1lbnRzLnB1c2goIEF0dGFjaG1lbnQuZ2V0KCBhcnR3b3JrSWQgKSApO1xuXHRcdFx0XHRhdHRhY2htZW50c1swXS5mZXRjaCgpO1xuXHRcdFx0fVxuXG5cdFx0XHRzZWxlY3Rpb24ucmVzZXQoIGF0dGFjaG1lbnRzICk7XG5cdFx0fSk7XG5cblx0XHQvLyBTZXQgdGhlIG1vZGVsJ3MgYXJ0d29yayBJRCBhbmQgdXJsIHByb3BlcnRpZXMuXG5cdFx0ZnJhbWUuc3RhdGUoICdsaWJyYXJ5JyApLm9uKCAnc2VsZWN0JywgZnVuY3Rpb24oKSB7XG5cdFx0XHR2YXIgYXR0YWNobWVudCA9IHRoaXMuZ2V0KCAnc2VsZWN0aW9uJyApLmZpcnN0KCkudG9KU09OKCk7XG5cblx0XHRcdHdvcmtmbG93Lm1vZGVsLnNldCh7XG5cdFx0XHRcdGFydHdvcmtJZDogYXR0YWNobWVudC5pZCxcblx0XHRcdFx0YXJ0d29ya1VybDogYXR0YWNobWVudC5zaXplcy52aWRlb19jZW50cmFsLnVybFxuXHRcdFx0fSk7XG5cdFx0fSk7XG5cblx0XHRyZXR1cm4gZnJhbWU7XG5cdH0sXG5cblx0LyoqXG5cdCAqIFdvcmtmbG93IGZvciBzZWxlY3RpbmcgdHJhY2sgYXVkaW8uXG5cdCAqXG5cdCAqIEBwYXJhbSB7T2JqZWN0fSBmcmFtZVxuXHQgKi9cblx0X3NlbGVjdEF1ZGlvOiBmdW5jdGlvbiggZnJhbWUgKSB7XG5cdFx0dmFyIHdvcmtmbG93ID0gdGhpcztcblxuXHRcdC8vIFJldHVybiB0aGUgZXhpc3RpbmcgZnJhbWUgZm9yIHRoaXMgd29ya2Zsb3cuXG5cdFx0aWYgKCBmcmFtZSApIHtcblx0XHRcdHJldHVybiBmcmFtZTtcblx0XHR9XG5cblx0XHQvLyBJbml0aWFsaXplIHRoZSBhdWRpbyBmcmFtZS5cblx0XHRmcmFtZSA9IG5ldyBNZWRpYUZyYW1lKHtcblx0XHRcdHRpdGxlOiBsMTBuLndvcmtmbG93cy5zZWxlY3RBdWRpby5mcmFtZVRpdGxlLFxuXHRcdFx0bGlicmFyeToge1xuXHRcdFx0XHR0eXBlOiAnYXVkaW8nXG5cdFx0XHR9LFxuXHRcdFx0YnV0dG9uOiB7XG5cdFx0XHRcdHRleHQ6IGwxMG4ud29ya2Zsb3dzLnNlbGVjdEF1ZGlvLmZyYW1lQnV0dG9uVGV4dFxuXHRcdFx0fSxcblx0XHRcdG11bHRpcGxlOiBmYWxzZVxuXHRcdH0pO1xuXG5cdFx0Ly8gU2V0IHRoZSBleHRlbnNpb25zIHRoYXQgY2FuIGJlIHVwbG9hZGVkLlxuXHRcdGZyYW1lLnVwbG9hZGVyLm9wdGlvbnMudXBsb2FkZXIucGx1cGxvYWQgPSB7XG5cdFx0XHRmaWx0ZXJzOiB7XG5cdFx0XHRcdG1pbWVfdHlwZXM6IFt7XG5cdFx0XHRcdFx0dGl0bGU6IGwxMG4ud29ya2Zsb3dzLnNlbGVjdEF1ZGlvLmZpbGVUeXBlcyxcblx0XHRcdFx0XHRleHRlbnNpb25zOiAnbTRhLG1wMyxvZ2csd21hJ1xuXHRcdFx0XHR9XVxuXHRcdFx0fVxuXHRcdH07XG5cblx0XHQvLyBQcmV2ZW50IHRoZSBFbWJlZCBjb250cm9sbGVyIHNjYW5uZXIgZnJvbSBjaGFuZ2luZyB0aGUgc3RhdGUuXG5cdFx0ZnJhbWUuc3RhdGUoICdlbWJlZCcgKS5wcm9wcy5vZmYoICdjaGFuZ2U6dXJsJywgZnJhbWUuc3RhdGUoICdlbWJlZCcgKS5kZWJvdW5jZWRTY2FuICk7XG5cblx0XHQvLyBTZXQgdGhlIGZyYW1lIHN0YXRlIHdoZW4gb3BlbmluZyBpdC5cblx0XHRmcmFtZS5vbiggJ29wZW4nLCBmdW5jdGlvbigpIHtcblx0XHRcdHZhciBzZWxlY3Rpb24gPSB0aGlzLmdldCggJ2luc2VydCcgKS5nZXQoICdzZWxlY3Rpb24nICksXG5cdFx0XHRcdHZpZGVvSWQgPSB3b3JrZmxvdy5tb2RlbC5nZXQoICd2aWRlb0lkJyApLFxuXHRcdFx0XHRhdWRpb1VybCA9IHdvcmtmbG93Lm1vZGVsLmdldCggJ2F1ZGlvVXJsJyApLFxuXHRcdFx0XHRpc0VtYmVkID0gYXVkaW9VcmwgJiYgISB2aWRlb0lkLFxuXHRcdFx0XHRhdHRhY2htZW50cyA9IFtdO1xuXG5cdFx0XHQvLyBBdXRvbWF0aWNhbGx5IHNlbGVjdCB0aGUgZXhpc3RpbmcgYXVkaW8gZmlsZSBpZiBwb3NzaWJsZS5cblx0XHRcdGlmICggdmlkZW9JZCApIHtcblx0XHRcdFx0YXR0YWNobWVudHMucHVzaCggQXR0YWNobWVudC5nZXQoIHZpZGVvSWQgKSApO1xuXHRcdFx0XHRhdHRhY2htZW50c1swXS5mZXRjaCgpO1xuXHRcdFx0fVxuXG5cdFx0XHRzZWxlY3Rpb24ucmVzZXQoIGF0dGFjaG1lbnRzICk7XG5cblx0XHRcdC8vIFNldCB0aGUgZW1iZWQgc3RhdGUgcHJvcGVydGllcy5cblx0XHRcdGlmICggaXNFbWJlZCApIHtcblx0XHRcdFx0dGhpcy5nZXQoICdlbWJlZCcgKS5wcm9wcy5zZXQoe1xuXHRcdFx0XHRcdHVybDogYXVkaW9VcmwsXG5cdFx0XHRcdFx0dGl0bGU6IHdvcmtmbG93Lm1vZGVsLmdldCggJ3RpdGxlJyApXG5cdFx0XHRcdH0pO1xuXHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0dGhpcy5nZXQoICdlbWJlZCcgKS5wcm9wcy5zZXQoe1xuXHRcdFx0XHRcdHVybDogJycsXG5cdFx0XHRcdFx0dGl0bGU6ICcnXG5cdFx0XHRcdH0pO1xuXHRcdFx0fVxuXG5cdFx0XHQvLyBTZXQgdGhlIHN0YXRlIHRvICdlbWJlZCcgaWYgdGhlIG1vZGVsIGhhcyBhbiBhdWRpbyBVUkwgYnV0XG5cdFx0XHQvLyBub3QgYSBjb3JyZXNwb25kaW5nIGF0dGFjaG1lbnQgSUQuXG5cdFx0XHRmcmFtZS5zZXRTdGF0ZSggaXNFbWJlZCA/ICdlbWJlZCcgOiAnaW5zZXJ0JyApO1xuXHRcdH0pO1xuXG5cdFx0Ly8gQ29weSBkYXRhIGZyb20gdGhlIHNlbGVjdGVkIGF0dGFjaG1lbnQgdG8gdGhlIGN1cnJlbnQgbW9kZWwuXG5cdFx0ZnJhbWUuc3RhdGUoICdpbnNlcnQnICkub24oICdpbnNlcnQnLCBmdW5jdGlvbiggc2VsZWN0aW9uICkge1xuXHRcdFx0dmFyIGF0dGFjaG1lbnQgPSBzZWxlY3Rpb24uZmlyc3QoKS50b0pTT04oKS52aWRlb19jZW50cmFsLFxuXHRcdFx0XHRkYXRhID0ge30sXG5cdFx0XHRcdGtleXMgPSBfLmtleXMoIHdvcmtmbG93Lm1vZGVsLmF0dHJpYnV0ZXMgKTtcblxuXHRcdFx0Ly8gQXR0cmlidXRlcyB0aGF0IHNob3VsZG4ndCBiZSB1cGRhdGVkIHdoZW4gaW5zZXJ0aW5nIGFuXG5cdFx0XHQvLyBhdWRpbyBhdHRhY2htZW50LlxuXHRcdFx0Xy53aXRob3V0KCBrZXlzLCBbICdpZCcsICdvcmRlcicgXSApO1xuXG5cdFx0XHQvLyBVcGRhdGUgdGhlc2UgYXR0cmlidXRlcyBpZiB0aGV5J3JlIGVtcHR5LlxuXHRcdFx0Ly8gVGhleSBzaG91bGRuJ3Qgb3ZlcndyaXRlIGFueSBkYXRhIGVudGVyZWQgYnkgdGhlIHVzZXIuXG5cdFx0XHRfLmVhY2goIGtleXMsIGZ1bmN0aW9uKCBrZXkgKSB7XG5cdFx0XHRcdHZhciB2YWx1ZSA9IHdvcmtmbG93Lm1vZGVsLmdldCgga2V5ICk7XG5cblx0XHRcdFx0aWYgKCAhIHZhbHVlICYmICgga2V5IGluIGF0dGFjaG1lbnQgKSAmJiB2YWx1ZSAhPT0gYXR0YWNobWVudFsga2V5IF0gKSB7XG5cdFx0XHRcdFx0ZGF0YVsga2V5IF0gPSBhdHRhY2htZW50WyBrZXkgXTtcblx0XHRcdFx0fVxuXHRcdFx0fSk7XG5cblx0XHRcdC8vIEF0dHJpYnV0ZXMgdGhhdCBzaG91bGQgYWx3YXlzIGJlIHJlcGxhY2VkLlxuXHRcdFx0ZGF0YS52aWRlb0lkICA9IGF0dGFjaG1lbnQudmlkZW9JZDtcblx0XHRcdGRhdGEuYXVkaW9VcmwgPSBhdHRhY2htZW50LmF1ZGlvVXJsO1xuXG5cdFx0XHR3b3JrZmxvdy5tb2RlbC5zZXQoIGRhdGEgKTtcblx0XHR9KTtcblxuXHRcdC8vIENvcHkgdGhlIGVtYmVkIGRhdGEgdG8gdGhlIGN1cnJlbnQgbW9kZWwuXG5cdFx0ZnJhbWUuc3RhdGUoICdlbWJlZCcgKS5vbiggJ3NlbGVjdCcsIGZ1bmN0aW9uKCkge1xuXHRcdFx0dmFyIGVtYmVkID0gdGhpcy5wcm9wcy50b0pTT04oKSxcblx0XHRcdFx0ZGF0YSA9IHt9O1xuXG5cdFx0XHRkYXRhLnZpZGVvSWQgID0gJyc7XG5cdFx0XHRkYXRhLmF1ZGlvVXJsID0gZW1iZWQudXJsO1xuXG5cdFx0XHRpZiAoICggJ3RpdGxlJyBpbiBlbWJlZCApICYmICcnICE9PSBlbWJlZC50aXRsZSApIHtcblx0XHRcdFx0ZGF0YS50aXRsZSA9IGVtYmVkLnRpdGxlO1xuXHRcdFx0fVxuXG5cdFx0XHR3b3JrZmxvdy5tb2RlbC5zZXQoIGRhdGEgKTtcblx0XHR9KTtcblxuXHRcdC8vIFJlbW92ZSBhbiBlbXB0eSBtb2RlbCBpZiB0aGUgZnJhbWUgaXMgZXNjYXBlZC5cblx0XHRmcmFtZS5vbiggJ2VzY2FwZScsIGZ1bmN0aW9uKCkge1xuXHRcdFx0dmFyIG1vZGVsID0gd29ya2Zsb3cubW9kZWwudG9KU09OKCk7XG5cblx0XHRcdGlmICggISBtb2RlbC5hcnR3b3JrVXJsICYmICEgbW9kZWwuYXVkaW9VcmwgKSB7XG5cdFx0XHRcdHdvcmtmbG93Lm1vZGVsLmRlc3Ryb3koKTtcblx0XHRcdH1cblx0XHR9KTtcblxuXHRcdHJldHVybiBmcmFtZTtcblx0fVxufTtcblxubW9kdWxlLmV4cG9ydHMgPSBXb3JrZmxvd3M7XG4iXX0=
