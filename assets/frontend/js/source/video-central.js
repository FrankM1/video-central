// JavaScript Document
jQuery(document).ready(function($) {
	 
	$('.video-central-player').each(function() {
		$(this).removeClass('loading');
		
		//$(this).find('.video-js, ').css('padding', '');
	});
	            
    // Once the video is ready make it responsive
    $("video-js").ready(function(){

        var myPlayer = this;    // Store the video object
        var aspectRatio = 9/16; // Make up an aspect ratio

        function resizeVideoJS(){
           	
           	if ( !myPlayer.id ) { return;  }
           	
            // Get the parent element's actual width
            var width = document.getElementById(myPlayer.id).parentElement.offsetWidth;
            
            // Set width to fill parent element, Set height
            myPlayer.width(width).height( width * aspectRatio );
                        
           	//fix video js progressbar
            var player_width = $('.video-central-player').width();
            var player_progress_width = player_width - 300;
        
            if( player_width > 800 ) {
                $('.vjs-progress-control').width(player_progress_width);
            }
             
        }
		
        resizeVideoJS(); // Initialize the function
        window.onresize = resizeVideoJS; // Call the function on resize
       
 		
 		//$(".video-js").unFitVids();
 		
    });
    
    /*

    var topcarousel;
    var visible;
    var align;
    var tcarousel;

    var carousel_id     = jQuery(this).attr('id');
    var carousel_effect = jQuery(this).data('effect')   ? jQuery(this).data('effect') : 'scroll';
    var carousel_auto   = jQuery(this).data('notauto')  ? false : false;

    //top carousel
    topcarousel = jQuery(this).find(".carousel-content");

    if( topcarousel.length ){

        if( carousel_id === 'big-carousel' ){

            visible = 3;
            align = "center";

        } else {

            visible = 0;
            align = false;

        }

        tcarousel = topcarousel.carouFredSel({

            responsive  : false,
            items       : {
                visible : function(visibleItems){
                    if(visible>0){
                        if(visibleItems>=3){
                            return 5;
                        }else{
                            return 3;
                        }
                    }else{return visibleItems+1;}
                },
                minimum : 1,
            },
            circular: true,
            infinite: true,
            width   : "100%",
            auto    : {
                play    : carousel_auto,
                timeoutDuration : 2600,
                duration        : 800,
                pauseOnHover: "immediate-resume"
            },
            align   : align,
            prev    : {
                button  : "#"+carousel_id+" .prev",
                key     : "left"
            },
            next    : {
                button  : "#"+carousel_id+" .next",
                key     : "right"
            },
            scroll : {
                items : 1,
                fx : "scroll",
                easing : 'quadratic',
                onBefore : function( data ) {
                    jQuery(".video-item").removeClass('current-carousel-item').removeClass('current-carousel-item2');
                    var current_item_count=0;
                    data.items.visible.each(function(){
                        current_item_count++;
                        if(current_item_count===2){jQuery(this).addClass( "current-carousel-item2" );}
                        jQuery(this).addClass( "current-carousel-item" );
                    });
                }
            },
            swipe       : {
                onTouch : false,
                onMouse : false,
            }
        }).imagesLoaded( function() {
            tcarousel.trigger("updateSizes");
            tcarousel.trigger("configuration", {
                    items       : {
                        visible : function(visibleItems){
                            if(visible>0){
                                if(visibleItems>=3){
                                    return 5;
                                }else{
                                    return 3;
                                }
                            }else{return visibleItems+1;}
                        },
                    },
                }
            );
        });

        jQuery(".carousel-content").trigger("currentVisible", function( current_items ) {
            var current_item_count=0;
            current_items.each(function(){
                current_item_count++;
                if(current_item_count===2){jQuery(this).addClass( "current-carousel-item2" );}
                jQuery(this).addClass( "current-carousel-item" );
            });
        });

    }//if length
    
    */


 });
 
 //unfit vid http://stackoverflow.com/questions/15961004/is-there-any-way-to-remove-fitvids-from-an-element-after-its-been-applied
jQuery.fn.unFitVids = function () {
     var id = jQuery(this).attr("id");
     var $children = jQuery("#" + id + " .fluid-width-video-wrapper").children().clone();
     jQuery("#" + id + " .fluid-width-video-wrapper").remove(); //removes the element
     jQuery("#" + id).append($children); //adds it to the parent
 };
