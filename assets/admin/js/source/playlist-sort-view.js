/*global _:false, cue:false, mejs:false, wp:false */

window.cue = window.cue || {};

/**
 * @type {Object} JavaScript namespace for our application.
 */
var VideoCentral = {
    playlist: {
        __instance: undefined
    }
};

(function(window, $, _, mejs, wp, undefined) {
    'use strict';

    VideoCentral.playlist.data = window._cueSettings;

    /**
     * ========================================================================
     * MODELS
     * ========================================================================
     */

    VideoCentral.playlist.Track = Backbone.Model.extend({
        defaults: {
            artist: '',
            artworkId: '',
            artworkUrl: '',
            audioId: '',
            audioUrl: '',
            format: '',
            length: '',
            title: '',
            order: 0,
            video_id: ''
        }
    });

    VideoCentral.playlist.TrackData = Backbone.Collection.extend({
        model: VideoCentral.playlist.Track,

        comparator: function(track) {
            return parseInt(track.get('order'), 10);
        },

        fetch: function( post_id ) {
            var collection = this;

            return wp.ajax.post('video_central_get_playlist_video_details', {
                post_id: post_id
            }).done(function(tracks) {
                collection.reset(tracks);
            });
        },

        save: function(data) {
            this.sort();

            data = _.extend({}, data, {
                post_id: VideoCentral.playlist.data.settings.postId,
                tracks: this.toJSON(),
                nonce: VideoCentral.playlist.data.settings.saveNonce
            });

            return wp.ajax.post('cue_save_playlist_tracks', data);
        }
    });

    /**
     * VideoCentral.playlist.PostForm
     */
    VideoCentral.playlist.PostForm = wp.Backbone.View.extend({
        el: '#post',
        saved: false,

        events: {
            //'click #publish': 'buttonClick',
            //'click #save-post': 'buttonClick'
        },

        initialize: function() {
            this.render();
        },

        render: function() {

            //find playlist option field and get data
            var selectedVideos = jQuery("#_video_central_playlist_ids").val();

            var $selectedVideos = selectedVideos.split(',');

            this.views.add('#video-central-playlist-section', [
                new VideoCentral.playlist.TrackList({
                    collection: $selectedVideos
                })
            ]);

            return this;
        },

        buttonClick: function(e) {
            var self = this,
                $button = $(e.target),
                $spinner = $button.siblings('.spinner');

            if (!self.saved) {
                this.collection.save().done(function(data) {
                    self.saved = true;
                    $button.click();
                }).fail(function() {
                    //$button.prop( 'disabled', false );
                    //$spinner.hide();
                });
            }

            return self.saved;
        }
    });

    /**
     * VideoCentral.playlist.TrackList
     */
    VideoCentral.playlist.TrackList = wp.Backbone.View.extend({
        className: 'cue-tracklist',
        tagName: 'ol',

        initialize: function() {

            //this.listenTo(this.collection, 'add', this.addTrack);
            //this.listenTo(this.collection, 'add remove', this.updateOrder);
            //this.listenTo(this.collection, 'reset', this.render);

            this.render().$el.sortable({
                axis: 'y',
                delay: 150,
                forceHelperSize: true,
                forcePlaceholderSize: true,
                opacity: 0.6,
                start: function(e, ui) {
                    ui.placeholder.css('visibility', 'visible');
                },
                update: _.bind(function(e, ui) {
                    this.updateOrder();
                }, this)
            });

        },

        render: function() {
            this.$el.empty();

            var that = this;

            _.each(this.collection, function(video_id, i) {
                that.addTrack(video_id);
            });

            //this.updateOrder();
            return this;
        },

        addTrack: function(video_id) {

            var trackView = new VideoCentral.playlist.Video({
                model: jQuery('<li />') //blank element
            });
            trackView.video_id = video_id;

            this.$el.append(trackView.render().el);
        },

        updateOrder: function() {
            _.each(this.$el.find('.cue-track'), function(item, i) {
                var cid = $(item).data('cid');
                this.collection.get(cid).set('order', i);
            }, this);
        }
    });

    /**
     * VideoCentral.playlist.Video
     */
    VideoCentral.playlist.Video = wp.Backbone.View.extend({
        tagName: 'li',
        className: 'cue-track',
        template: wp.template('video-central-playlist-video'),

        events: {
            'change [data-setting]': 'updateAttribute',
            'click .js-toggle': 'toggleOpenStatus',
            'dblclick .cue-track-title': 'toggleOpenStatus',
            'click .js-close': 'minimize',
            'click .js-remove': 'destroy'
        },

        initialize: function() {

            //this.listenTo(this.model, 'change:title', this.updateTitle);
            this.listenTo(this.model, 'change', this.updateFields);
            this.listenTo(this.model, 'destroy', this.remove);

        },

        render: function() {

            this.$el.html(this.template(this.model));

            this.$el.data('id', this.video_id);
            this.updateTitle();

            return this;
        },

        minimize: function(e) {
            e.preventDefault();
            this.$el.removeClass('is-open').find('input:focus').blur();
        },

        toggleOpenStatus: function(e) {
            e.preventDefault();
            this.$el.toggleClass('is-open').find('input:focus').blur();

            // Trigger a resize so the media element will fill the container.
            if (this.$el.hasClass('is-open')) {
                $(window).trigger('resize');
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
        updateAttribute: function(e) {
            var attribute = $(e.target).data('setting'),
                value = e.target.value;

            if (this.model.get(attribute) !== value) {
                this.model.set(attribute, value);
            }
        },

        /**
         * Update a setting field when a model's attribute is changed.
         */
        updateFields: function() {
            var track = this.model.toJSON(),
                $settings = this.$el.find('[data-setting]'),
                attribute, value;

            // A change event shouldn't be triggered here, so it won't cause
            // the model attribute to be updated and get stuck in an
            // infinite loop.
            for (attribute in track) {
                // Decode HTML entities.
                value = $('<div/>').html(track[attribute]).text();
                $settings.filter('[data-setting="' + attribute + '"]').val(value);
            }
        },

        getVideosData: function() {

            var that = this,
                response,
                value;

            this.playlistAjax = jQuery.ajax({
                type: "GET",
                url: window.ajaxurl,
                cache: true,
                data: {
                    action: "video_central_get_playlist_video_details",
                    post_id: that.video_id,
                },
                success: function(data, textStatus, jqXHR) {
                    response = jQuery.parseJSON(data);

                    // Decode HTML entities.
                    value = $('<div/>').html(response.title).text();

                    that.$el.find('.cue-track-title .text').text(value);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus);
                }
            });

            return response; //return object

        },

        updateTitle: function() {
            this.getVideosData();
        },

        /**
         * Destroy the view's model.
         *
         * Avoid syncing to the server by triggering an event instead of
         * calling destroy() directly on the model.
         */
        destroy: function() {
            this.model.trigger('destroy', this.model);
        },

        remove: function() {
            this.$el.remove();
        }
    });

    jQuery(function($) {
        var tracks;

        tracks = new VideoCentral.playlist.TrackData(VideoCentral.playlist.data.tracks);
        delete VideoCentral.playlist.data.tracks;

        new VideoCentral.playlist.PostForm({
            collection: tracks
        });

    });

})(this, jQuery, _, mejs, wp);
