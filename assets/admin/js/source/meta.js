 
jQuery(document).ready(function() {

	var currentType;

    var VideoCentralTypeTrigger = '#_video_central_source',
        _video_thumbnail = '#video_thumbnail',
        _video_central_video_id = '._video_central_video_id',
        _video_central_webm = '._video_central_webm',
        _video_central_mp4 = '._video_central_mp4',
        _video_central_ogg = '._video_central_ogg',
        _video_central_flv = '._video_central_flv',
        _video_central_embed_code = '._video_central_embed_code';
        
	currentType = jQuery(VideoCentralTypeTrigger).val();
    
    function radiumHideYoutubeVimeo() {
    
        jQuery( _video_thumbnail ).hide();
        jQuery( _video_central_video_id ).hide();
     	jQuery( _video_central_embed_code ).hide();

     	jQuery( _video_central_webm ).show();
     	jQuery( _video_central_mp4 ).show();
     	jQuery( _video_central_ogg ).show();
     	jQuery( _video_central_flv ).show();
    	
    }
    
    function radiumHideSelf() {
    
		jQuery( _video_central_webm ).hide();
		jQuery( _video_central_mp4 ).hide();
		jQuery( _video_central_ogg ).hide();
		jQuery( _video_central_flv ).hide();

		jQuery( _video_central_embed_code ).hide();

    	jQuery( _video_thumbnail ).show();
    	jQuery( _video_central_video_id ).show();
    	
    }
    
    function radiumHideEmbed() {
        
        jQuery( _video_central_webm ).hide();
        jQuery( _video_central_mp4 ).hide();
        jQuery( _video_central_ogg ).hide();
        jQuery( _video_central_flv ).hide();
        jQuery( _video_thumbnail ).hide();
        jQuery( _video_central_video_id ).hide();
        
        jQuery( _video_central_embed_code ).show();
        	
   }
        
    function radiumSwitchVideo(currentType) {
    	
        if( currentType === 'self' ) {
            	        
    		radiumHideYoutubeVimeo();
        
        } else if ( currentType === 'vimeo' || currentType === 'youtube' ) {
        	        	
          	radiumHideSelf();
        
        } else if ( currentType === 'embed' ) {
        	        	
          	radiumHideEmbed();
        
        } else {
           // radiumHideYoutubeVimeo();
        }
        
    }
    
    radiumSwitchVideo(currentType);	
	
    jQuery(VideoCentralTypeTrigger).change( function() {
    
       currentType = jQuery(this).val();
       
       radiumSwitchVideo(currentType);
       	       
   });

});