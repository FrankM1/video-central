var VideoCentral = {

    init: function(){

        var content = jQuery('.video-central-single-content');
        var toggle = content.data('toggle');

        VideoCentral.loopViewSwitcher();
        VideoCentral.lessMore();

        // Change event on select element
        jQuery('.orderby-select').change(function() {
            location.href = this.options[this.selectedIndex].value;
        });

        if ( toggle ) {
            jQuery('.video-central-single-content').readmore({
                speed: 75,
                collapsedHeight: 200, // in pixels
                lessLink: '<a href="#">Read less</a>',
                moreLink: '<a href="#">Read more</a>'
            }).append('<a href="#">Read more</a>');
        }

    },

    connectedControl: function(carouselStage, carouselNav){

        var controlheight = 449;
        var controlwidth = jQuery('.video-home-slider-grid').width();

        var new_width = (controlwidth - 304);

        //calculate height
        var new_height = (controlheight / controlwidth) * (controlwidth - 304);

        //jQuery('.video-home-slider-grid .video-wall-wrap .video-wall-wrap-inner').css({'width': new_width, 'height': new_height});

        //jQuery('.video-home-slider-grid .video-wall-wrap .video-wall-wrap-inner .thumb img').css({'width': new_width});

        //jQuery('.video-home-slider-grid .video-wall-wrap .carousel-nav').css('width', '304');

        if( jQuery().jcarousel === undefined ) {
            return;
        }

        carouselNav.jcarousel('items').each(function() {
            var item = jQuery(this),
                target = carouselStage.jcarousel('items').eq(item.index());

            item
            .on('click', function(){
                // Reinit auto scrolling
                if( carouselStage.data('jcarouselautoscroll') === 'stopped' ) {
                    carouselStage.jcarouselAutoscroll('start');
                    carouselStage.data('jcarouselautoscroll', true);
                }
            })
            .on('active.jcarouselcontrol', function() {
                carouselNav.jcarousel('scrollIntoView', this);
                item.addClass('active');
            })
            .on('inactive.jcarouselcontrol', function() {
                item.removeClass('active');
            })
            .jcarouselControl({
                target: target,
                carousel: carouselStage
            });
        });
    },

    clickAjax: function(link, stage, carousel){

        if(!stage.data('ajaxload')) {
            return false;
        }

        jQuery(link).on('click', function(e){
            e.preventDefault();

            // Stop autoscrolling
            if(carousel.data('jcarouselautoscroll')) {
                carousel.jcarouselAutoscroll('stop').data('jcarouselautoscroll', 'stopped');
            }

            return false;
        });
    },

    stageSetup: function(stage){
        stage.find('.item-video').each(function(){
            // Hide thumb and caption when the video is found
            if(jQuery(this).find('.video').length) {
                jQuery(this).find('.thumb, .caption').hide();
            }
        });
    },

    autoScroll: function(stage){

        // Add the autoscrolling for stage carousel
        var interval = stage.data('autoscroll-interval');

        if(interval > 0) {

            stage.jcarouselAutoscroll({
                'interval': interval,
                'autostart': true
            });

        }

    },

    targetedStage: function(carousel){

        carousel.on('itemtargetin.jcarousel', '.video-item', function(event, carousel) {

            var item = jQuery(this);

            // Display the thumb and caption of current item
            // item.find('.screen').show();
            item.find('.thumb').show();
            item.find('.caption').show();

            // Remove the video of other items
            item.siblings('.video-item').find('.video').remove();

            // Switch to the entry-header of current item
            item.parents('.video-wall-wrap').find('.entry-header').hide();
            item.parents('.video-wall-wrap').find('.entry-header[data-id="'+item.data('id')+'"]').fadeIn();

        }).on('itemtargetout.jcarousel', '.video-item', function(event, carousel) {

            var item = jQuery(this);

        });

    },

    prevNextControl: function(carousel){

        jQuery('.video-carousel-list-prev').on('inactive.jcarouselcontrol', function() {

            jQuery(this).addClass('inactive');

        }).on('active.jcarouselcontrol', function() {

            jQuery(this).removeClass('inactive');

        }).jcarouselControl({
            target: '-=1',
            carousel: carousel
        });

        jQuery('.video-carousel-list-next').on('inactive.jcarouselcontrol', function() {

            jQuery(this).addClass('inactive');

        }).on('active.jcarouselcontrol', function() {

            jQuery(this).removeClass('inactive');

        }).jcarouselControl({
            target: '+=1',
            carousel: carousel
        });
    },

    /* "More/less" Toggle */
    lessMore: function(){

        var wrapper = jQuery('.video-central-single-content'),
            lessHeight = wrapper.data('less-height') ? wrapper.data('less-height') : 200,
            trueHeight = wrapper.outerHeight(false);

        if(trueHeight > lessHeight) {

             wrapper.height(lessHeight);

        } else {

            //jQuery('.video-central-info-toggle-button').css('display', 'none');

        }

        jQuery('.video-central-info-toggle-button').on('click', function(e) {

            e.preventDefault();

            jQuery(this).parent().siblings('.video-central-single-content').toggleClass('toggled');

            jQuery(this).siblings('.video-central-content-gradient').toggleClass('hidden');

            jQuery(this).toggleClass('is-open');

        });

    },

    /*= Loop View Switcher */
    loopViewSwitcher: function() {

        var cookie_name = 'video_central_loop_view',
            grid_class_pattern = 'small-block-grid-',
            saved_loop_view = jQuery.cookie(cookie_name),
            loop = jQuery('.switchable-view'),
            loopView = loop.attr('data-view');

        if ( saved_loop_view ) {

            var classList = loop.attr('class').split(/\s+/);
            jQuery.each( classList, function(index, item){
                if(item.indexOf(grid_class_pattern) !== -1){ loop.removeClass(item); }
            });

            loop.stop().fadeOut(100, function(){

                if(loopView) { loop.removeClass(loopView); }

                loop.fadeIn().attr('data-view', saved_loop_view).addClass(saved_loop_view);

            });

        }

        jQuery('.loop-actions .view a').on('click', function(e) {

            e.preventDefault();

            var viewType = jQuery(this).attr('data-type');

            if(viewType === loopView) {
                return false;
            }

            jQuery(this).addClass('current').siblings('a').removeClass('current');

            loop.stop().fadeOut(100, function(){

                if ( loopView ) {

                    var classList = loop.attr('class').split(/\s+/);
                    jQuery.each( classList, function(index, item){
                        if(item.indexOf(grid_class_pattern) !== -1){ loop.removeClass(item); }
                    });

                }

                jQuery(this).fadeIn().attr('data-view', viewType).addClass(viewType);

            });

            jQuery.cookie(cookie_name, viewType, { path: '/', expires : 999});

        });

    },

    /**
      * Used for saving a list of ids (each seperated by an underscore) in multiple
      * cookies if list is longer than 4000 bytes. This is important due to cookie
      * size restrictions.
      *
      * @param  {string} likes list of post id's which have been like'd
      * @return {int}          number of created cookies
      */
      koodiesave : function (likes) { // read "cookiesave"

          var likesdata = window.likesdata;

        if (likes.length === 0){

          // remove koodies
          for(var i = 0; jQuery.removeCookie('likes'+i, { path: '/' }); i++) { }

          return 0;

        } else {

          // build array containing all our data, cookies have a max length of ~4096
          var koodies = [];

          while(likes.length > 4000){ // e.g. length 7634
            koodies.push( likes.substr(likes.length-4000) ); // 3635 is first elem
            likes = likes.substr(0,likes.length-4000); // 3634 is last of next elem
          }

          koodies.push(likes);

          // set cookies
          jQuery.each( koodies, function(idx,val){
            jQuery.cookie('likes'+idx, val, { expires: parseInt(likesdata.lifetime), path: '/' });
          });

          // remove cookies we do not longer need
          for(var i2 = koodies.length; jQuery.removeCookie('likes'+i2, { path: '/' }); i2++) { }

          // return number of created cookies
          return koodies.length;

        }

      },

};

jQuery(document).ready(function($){

    var likesdata = window.likesdata,
        likes,
        nlikes,
        koodie = [];

    VideoCentral.init();

     jQuery(function() {

         var stage = jQuery('.video-home-featured .video-wall-wrap-inner');
         var carouselStage = stage.find('.video-carousel');

         VideoCentral.stageSetup(stage);

         if(jQuery().jcarousel) {

             carouselStage.jcarousel({wrap: 'circular'});
             VideoCentral.autoScroll(carouselStage);
             VideoCentral.targetedStage(carouselStage);
             VideoCentral.prevNextControl();

             // Setup the navigation carousel
             var carouselNav = jQuery('.video-home-featured .carousel-nav .video-carousel-clip').jcarousel({
                vertical: true,
                wrap: 'circular'
             });

             if ( $('body').hasClass('video-central-twenty')) {

                 // Setup controls for the navigation carousel
                 jQuery('.video-home-featured .video-carousel-prev').jcarouselControl({target: '-=2'});
                 jQuery('.video-home-featured .video-carousel-next').jcarouselControl({target: '+=2'});

             } else {

                 // Setup controls for the navigation carousel
                 jQuery('.video-home-featured .video-carousel-prev').jcarouselControl({target: '-=4'});
                 jQuery('.video-home-featured .video-carousel-next').jcarouselControl({target: '+=4'});

            }

             VideoCentral.connectedControl(carouselStage, carouselNav);

         }

     });


     jQuery(function() {

         var stage = jQuery('.video-home-popular .video-wall-wrap-inner');
         var carouselStage = stage.find('.video-carousel');

         VideoCentral.stageSetup(stage);

         if(jQuery().jcarousel) {

             carouselStage.jcarousel({wrap: 'circular'});
             VideoCentral.autoScroll(carouselStage);
             VideoCentral.targetedStage(carouselStage);

             // Setup the navigation carousel
             var carouselNav = jQuery('.video-home-popular .carousel-nav .video-carousel-clip').jcarousel({
                vertical: true,
                wrap: 'circular'
             });

             if ( $('body').hasClass('video-central-twenty')) {

                 // Setup controls for the navigation carousel
                 jQuery('.video-home-popular .video-carousel-prev').jcarouselControl({target: '-=2'});
                 jQuery('.video-home-popular .video-carousel-next').jcarouselControl({target: '+=2'});

             } else {

                 // Setup controls for the navigation carousel
                 jQuery('.video-home-popular .video-carousel-prev').jcarouselControl({target: '-=4'});
                 jQuery('.video-home-popular .video-carousel-next').jcarouselControl({target: '+=4'});

            }

             VideoCentral.connectedControl(carouselStage, carouselNav);

         }

     });

     /* Kudos
     -----------------------------------------------------------------------------*/
     // initialize our like buttons
     jQuery("figure.like").likeable();

     likes = '';

    // concatenate all cookie data in one string
    for(var i = 0; (koodie = jQuery.cookie('likes'+i)) !== undefined; i++) {
        likes += koodie;
    }

    // mark already like'd items as like'd
    if (likes.length > 0) {

        jQuery("figure.like").each( function() {

          if (likes.indexOf(jQuery(this).attr('data-id')+'_') > -1) {
              jQuery(this).removeClass("like-animate").addClass("like-complete");
          }

        });

    }

      // like'd
    jQuery("figure.like").bind("like:added", function(e){

        var id = jQuery(this).attr('data-id');

        // check if id of post has not already been like'd
        if (likes.indexOf(id+'_') === -1) {

            // run ajax request to increment like counter on database
            jQuery.post( likesdata.ajaxurl, {
                  action    : 'like',
                  nonce     : likesdata.nonce,
                  id        : id
            }, function(data) { // success callback

                // Legal nonce
                if(data.success) {
                    likes = likes+id+'_';
                    VideoCentral.koodiesave(likes);
                }

            }, "json" ).fail(function() { alert("failed"); }); // for development
         }

    });

    // unlike'd
    jQuery("figure.like").bind("like:removed", function(e){

        var id = jQuery(this).attr('data-id');

        // There are likes
        if (likes.length > 0) {
            nlikes = likes.replace( new RegExp( id+'_', 'g' ), '' ); // remove id and separator

            if (likes !== nlikes) {
                jQuery.post( likesdata.ajaxurl, {
                action    : 'unlike',
                nonce     : likesdata.nonce,
                id        : id
            },

            function(data) { // success callback

                // Legal nonce
                if(data.success) {
                    VideoCentral.koodiesave(nlikes);
                    likes = nlikes;
                }

            }, "json").fail(function() { alert("failed here"); });
        }

        }
    });

    if (parseInt(likesdata.refresh) > 0) {

        jQuery.ajaxSetup({ cache: false });
        var likeables = [];

        jQuery('.likeable').each(function() {
            likeables.push( jQuery(this).attr('data-id') ); // 3635 is first elem
        });

        setInterval(function() {

            jQuery.post( likesdata.ajaxurl, {
                action    : 'likecounts',
                nonce     : likesdata.nonce,
                ids       : likeables
            },
            function(data) { // success callback

                // Legal nonce
                if(data.success) {
                    jQuery.each(data.counts, function(idx, val) {
                    jQuery('.like-meta-'+idx+' .like-count').html(val);
                });
            }

            }, "json");

        }, parseInt(likesdata.refresh));

    }

 });
