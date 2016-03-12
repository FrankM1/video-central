<?php

/* Prevent mixed content warnings for the self-hosted version */
function video_central__player_js_swf()
{
        echo '<script type="text/javascript">document.createElement("video");document.createElement("audio");document.createElement("track"); </script>';
}
