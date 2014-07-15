<?php

/* Prevent mixed content warnings for the self-hosted version */
function video_central__player_js_swf(){
    $options = get_option('videojs_options');
    if($options['videojs_cdn'] != 'on') {
        echo '
        <script type="text/javascript">
            if(typeof videojs != "undefined") {
                videojs.options.flash.swf = "'. plugins_url( 'videojs/video-js.swf' , __FILE__ ) .'";
            }
            document.createElement("video");document.createElement("audio");document.createElement("track");
        </script>
        ';
    } else {
        echo '
        <script type="text/javascript"> document.createElement("video");document.createElement("audio");document.createElement("track"); </script>
        ';
    }
}
