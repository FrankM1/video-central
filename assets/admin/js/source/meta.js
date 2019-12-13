
jQuery(document).ready(function() {

	var currentType;

    var VideoCentralTypeTrigger = '#_video_central_source',
        _video_thumbnail = '#video_thumbnail',
        _video_central_video_id = '._video_central_video_id',
        _video_central_webm = '._video_central_webm',
        _video_central_upload = '._video_central_upload',
        _video_central_mp4 = '._video_central_mp4',
        _video_central_ogg = '._video_central_ogg',
        _video_central_flv = '._video_central_flv',
        _video_central_embed_code = '._video_central_embed_code';

	currentType = jQuery(VideoCentralTypeTrigger).val();

    function radiumShowYoutube() {

        jQuery( _video_thumbnail ).show();
        jQuery( _video_central_video_id ).show();
     	jQuery( _video_central_embed_code ).show();

     	jQuery( _video_central_webm ).hide();
     	jQuery( _video_central_mp4 ).hide();
     	jQuery( _video_central_ogg ).hide();
     	jQuery( _video_central_flv ).hide();
        jQuery( _video_central_upload ).hide();

    }

    function radiumShowVimeo() {

        jQuery( _video_thumbnail ).show();
        jQuery( _video_central_video_id ).show();
        jQuery( _video_central_embed_code ).show();
        jQuery( _video_central_upload ).show();

        jQuery( _video_central_webm ).hide();
        jQuery( _video_central_mp4 ).hide();
        jQuery( _video_central_ogg ).hide();
        jQuery( _video_central_flv ).hide();

    }

    function radiumShowSelf() {

		jQuery( _video_central_webm ).show();
		jQuery( _video_central_mp4 ).show();
		jQuery( _video_central_ogg ).show();
		jQuery( _video_central_flv ).show();

		jQuery( _video_central_embed_code ).show();

    	jQuery( _video_thumbnail ).hide();
    	jQuery( _video_central_video_id ).hide();
        jQuery( _video_central_upload ).hide();

    }

    function radiumShowEmbed() {

        jQuery( _video_central_webm ).show();
        jQuery( _video_central_mp4 ).show();
        jQuery( _video_central_ogg ).show();
        jQuery( _video_central_flv ).show();
        jQuery( _video_thumbnail ).show();
        jQuery( _video_central_video_id ).show();

        jQuery( _video_central_embed_code ).hide();
        jQuery( _video_central_upload ).hide();

   }

    function radiumSwitchVideo(currentType) {

        if( currentType === 'self' ) {

    		radiumShowSelf();

        } else if ( currentType === 'vimeo') {

          	radiumShowVimeo();

        } else if (currentType === 'youtube' ) {

            radiumShowYoutube();

        } else if ( currentType === 'embed' ) {

          	radiumShowEmbed();

        } else {
            radiumShowVimeo();
        }

    }

    radiumSwitchVideo(currentType);

    jQuery(VideoCentralTypeTrigger).change( function() {

       currentType = jQuery(this).val();

       radiumSwitchVideo(currentType);

   });

});
