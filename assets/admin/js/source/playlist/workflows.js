var Workflows,
	_ = require( 'underscore' ),
	video_central = require( 'video_central' ),
	l10n = require( 'video_central' ).l10n,
    AddVideosFrame = require( './views/frame/insert-videos' ),
	wp = require( 'wp' ),
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
