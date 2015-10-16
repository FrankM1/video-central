/**
 * @type {Object} JavaScript namespace for our application.
 */
var Playlist = {
    video_central_modal: {
        __instance: undefined
    }
};

/**
 * Primary Modal Application Class
 */
Playlist.video_central_modal.Application = Backbone.View.extend({
    id: "backbone_modal_dialog",
    options: {},
    events: {
        "click .media-modal-close": "closeModal",
        "click #btn-cancel": "closeModal",
        "click #btn-ok": "saveModal",
        "click .navigation-bar a": "doNothing"
    },
    loopArgs: {
        'posts_per_page': 40,
        'page': 1,
        'view': 'latest'
    },

    selectedVideos: [],


    /**
     * Simple object to store any UI elements we need to use over the life of the application.
     */
    ui: {
        nav: undefined,
        content: undefined
    },

    /**
     * Container to store our compiled templates. Not strictly necessary in such a simple example
     * but might be useful in a larger one.
     */
    templates: {},

    /**
     * Instantiates the Template object and triggers load.
     */
    initialize: function() {
        "use strict";

        _.bindAll(this, 'render', 'preserveFocus', 'closeModal', 'saveModal', 'doNothing');
        this.videoPlaylistAddToolbar();
        this.initialize_templates();
        this.render();
    },

    /**
     * Creates compiled implementations of the templates. These compiled versions are created using
     * the wp.template class supplied by WordPress in 'wp-util'. Each template name maps to the ID of a
     * script tag ( without the 'tmpl-' namespace ) created in template-data.php.
     */
    initialize_templates: function() {

        this.templates.window = window.wp.template("video-central-playlist-modal-window");
        this.templates.backdrop = window.wp.template("video-central-playlist-modal-backdrop");
        this.templates.menuItem = window.wp.template("video-central-playlist-modal-menu-item");
        this.templates.menuItemSeperator = window.wp.template("video-central-playlist-modal-menu-item-separator");

    },

    getfilters: function() {

    },

    /**
     * Assembles the UI from loaded templates.
     * @internal Obviously, if the templates fail to load, our modal never launches.
     */
    render: function() {
        "use strict";

        // Build the base window and backdrop, attaching them to the $el.
        // Setting the tab index allows us to capture focus and redirect it in Application.preserveFocus
        this.$el.attr('tabindex', '0')
            .append(this.templates.window())
            .append(this.templates.backdrop());

        //get list of menu items
        this.getfilters();

        //find playlist option field and get data
        var selectedVideos = jQuery('body').find("#_video_central_playlist_ids").val();

        this.selectedVideos = selectedVideos.split(',');

        // Save a reference to the navigation bar's unordered list and populate it with items.
        // This is here mostly to demonstrate the use of the template class.
        this.ui.nav = this.$('.navigation-bar nav ul');

        // The l10n object generated by wp_localize_script() should be available, but check to be sure.
        // Again, this is a trivial example for demonstration.
        //if (typeof aut0poietic_backbone_modal_l10n === "object") {
        this.ui.content = this.$('.media-frame-content .attachments-browser');

        this.getVideos();

        // Handle any attempt to move focus out of the modal.
        jQuery(document).on("focusin", this.preserveFocus);

        // set overflow to "hidden" on the body so that it ignores any scroll events while the modal is active
        // and append the modal to the body.
        // TODO: this might better be represented as a class "modal-open" rather than a direct style declaration.
        jQuery("body").css({
            "overflow": "hidden"
        }).append(this.$el);

        // Set focus on the modal to prevent accidental actions in the underlying page
        // Not strictly necessary, but nice to do.
        this.$el.focus();
    },

    eventsHandler: function() {

        //rudimentary infinite scroll
        this.options.scrollElement = jQuery('.attachments-browser .attachments');

        // Throttle the scroll handler and bind this.
        this.scroll = _.chain( this.scroll ).bind( this ).throttle( 200 ).value();

        jQuery( this.options.scrollElement ).on( 'scroll', this.scroll );

        /* jQuery('.attachments-browser .attachments').on('scroll', function() {
             alert('end');

            if( jQuery(this).scrollTop() + jQuery(this).innerHeight() >= this.scrollHeight) {
                alert('end reached');
            }
        }); */

        //handle selection
        this.preSelected();
        this.itemSelection();
        this.removeitemSelection();
    },

    attachmentFocus: function() {
        this.jQuery( 'li:first' ).focus();
    },

    restoreFocus: function() {
        this.jQuery( 'li.selected:first' ).focus();
    },

    scroll: function() {

        var view = this,
            el = this.options.scrollElement,
            el1,
            scrollTop,
            toolbar;

            el1 = jQuery('.attachments-browser .attachments');
            el = el1[0]; //get DOM element itself
            scrollTop = el.scrollTop;

        if( el1.scrollTop() + el1.innerHeight() >= el.scrollHeight) {
            this.paginateVideos();
        }

    },

    getVideos: function() {

        var $that = this,
            results,
            responseHTML,
            toolbar = this.toolbar;

        this.playlistAjax = jQuery.ajax({
            type: "GET",
            url: window.ajaxurl,
            cache: true,
            data: {
                action: "video_central_get_playlist_video_list",
                page: $that.loopArgs.page,
                posts_per_page: $that.loopArgs.posts_per_page,
                filter: '',
                filter_id: '',
                view: $that.loopArgs.view
            },
            success: function(data, textStatus, jqXHR) {

                responseHTML = jQuery.parseJSON(data);
                $that.ui.content.find('ul.attachments').append( responseHTML );
                $that.eventsHandler();

                //toolbar.find('.spinner').hide();

                console.log($that.loopArgs.page);

            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus);
            }
        });

        return responseHTML; //return object

    },

    paginateVideos: function() {

        this.loopArgs.page++; //increment page by once
        this.getVideos();

    },

    //mark selected on load
    preSelected: function() {

        var that = this;

        this.ui.content.find('li').each(function(index) {

            var video_id = jQuery(this).data('id');
            console.log(video_id);
            console.log(jQuery.inArray(video_id, that.selectedVideos));

            if( jQuery.inArray(video_id, that.selectedVideos) ){
                jQuery(this).trigger('click');
            }

        });

    },

    itemSelection: function() {

        var that = this;

        this.ui.content.find('li').on('click', function(e) {

            jQuery(this).addClass('selected details');

            var video_id = jQuery(this).data('id');

            if( video_id ) {
                that.selectedVideos.push(video_id);
            }

        });

    },

    removeitemSelection: function() {

        var $selectedVideos = this.selectedVideos;

        this.ui.content.find('.button-link').on('click', function(e) {

            var $parent = jQuery(this).parent();

            if( $parent.hasClass('selected') ) {

                $parent.removeClass('selected');

                jQuery(this).remove();

                var video_id = $parent.data('id');

                var index = $selectedVideos.indexOf(video_id);

                if (index > -1) {
                    $selectedVideos.splice(index, 1);
                }

            }

        });

    },


    //make video ids unique
    UniqueIds: function(){
        this.selectedVideos = jQuery.unique( this.selectedVideos );
        console.log(this.selectedVideos);
    },

    //update input field
    updatePlaylist: function() {

        this.UniqueIds();
        jQuery('body').find("#_video_central_playlist_ids").attr( 'value', this.selectedVideos.toString() );

    },

    //toolbar
    videoPlaylistAddToolbar: function() {

    },

    /**
     * Ensures that keyboard focus remains within the Modal dialog.
     * @param e {object} A jQuery-normalized event object.
     */
    preserveFocus: function(e) {
        "use strict";
        if (this.$el[0] !== e.target && !this.$el.has(e.target).length) {
            this.$el.focus();
        }
    },

    /**
     * Closes the modal and cleans up after the instance.
     * @param e {object} A jQuery-normalized event object.
     */
    closeModal: function(e) {
        "use strict";

        e.preventDefault();
        this.undelegateEvents();

        jQuery(document).off("focusin");
        jQuery("body").css({
            "overflow": "auto"
        });

        this.remove();
        this.playlistAjax.abort(); //kill ajax request
        this.loopArgs.page = 1; //reset page

        Playlist.video_central_modal.__instance = undefined;

    },

    /**
     * Responds to the btn-ok.click event
     * @param e {object} A jQuery-normalized event object.
     */
    saveModal: function(e) {
        "use strict";
        this.updatePlaylist();
        this.closeModal(e);
    },

    /**
     * Ensures that events do nothing.
     * @param e {object} A jQuery-normalized event object.
     * @todo You should probably delete this and add your own handlers.
     */
    doNothing: function(e) {
        "use strict";
        e.preventDefault();
    }

});

jQuery(function($) {
    "use strict";
    /**
     * Attach a click event to the meta-box button that instantiates the Application object, if it's not already open.
     */
    $("#add-videos a").click(function(e) {
        e.preventDefault();
        if (Playlist.video_central_modal.__instance === undefined) {
            Playlist.video_central_modal.__instance = new Playlist.video_central_modal.Application();
        }
    });
});
