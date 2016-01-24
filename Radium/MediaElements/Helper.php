<?php

/**
 * Helper class for Radium_MediaElements.
 *
 * @since 1.0.0
 *
 * @author	Franklin M Gitonga
 */
class Radium_MediaElements_Helper
{
    /**
       * Holds a copy of the object for easy reference.
       *
       * @since 1.0.0
       *
       * @var object
       */
      private static $instance;

      /**
       * Constructor. Hooks all interactions to initialize the class.
       *
       * @since 1.0.0
       */
      public function __construct()
      {
          self::$instance = $this;
      }

    /**
     * Output Audio.
     *
     * @since 1.0.0
     * @returns string
     */
    public function get_audio($postid, $audio_file_url = null, $poster = null)
    {
        $output = null;

        if ($audio_file_url) {
            $postid = rand(1, 10000); //create a random id
        } else {
            $audio_file_url = get_post_meta($postid, '_video_central_audio_file', true);
        }

        if (filter_var($audio_file_url, FILTER_VALIDATE_URL)) {
            switch (pathinfo($audio_file_url, PATHINFO_EXTENSION)) {
                case 'mp3':  //mp3
                    $media = "mp3: '$audio_file_url'";
                    $supplied = 'supplied: "mp3",';
                        break;
                case 'm4a':  //mp4
                    $media = "m4a: '$audio_file_url'";
                    $supplied = 'supplied: "m4a, mp3",';
                    break;
                case 'ogg': //ogg
                    $media = "oga: '$audio_file_url'";
                    $supplied = 'supplied: "oga, ogg, mp3",';
                        break;
                case 'oga': //oga
                    $media = "oga: '$audio_file_url'";
                    $supplied = 'supplied: "oga, ogg, mp3",';
                    break;
                case 'webma': //webma
                    $media = "webma: '$audio_file_url'";
                    $supplied = 'supplied: "webma, mp3",';
                    break;
                case 'webm': //webma
                    $media = "webma: '$audio_file_url'";
                    $supplied = 'supplied: "webma, mp3",';
                    break;
                case 'wav':
                    $media = "wav: '$audio_file_url'";
                    $supplied = 'supplied: "wav, mp3",';
                    break;
                default:
                    // audio format not supported
                    return;
                    break;
            }
        }

        if ($audio_file_url):

            $output .= '<script type="text/javascript">
                jQuery(document).ready(function(){
                    if(jQuery().jPlayer) {
                        jQuery("#jquery_jplayer_'.$postid.'").jPlayer( {
                            ready : function () {
                                jQuery(this).jPlayer("setMedia", {';

        $output .= $media.',';

        if ($poster != '') {
            $output .= 'poster: "'.$poster.'",';
        }

        $output .= 'end: "" }); },';

        $output .= 'play: function() { jQuery(this).jPlayer("pauseOthers"); },';

        if (!empty($poster)) {
            $output .= 'size: { width: "100%", height: "100%" },';
        }

        $output .= 'swfPath: "'.RADIUM_HTML5_MEDIA_ASSETS_URL.'/jplayer",';

        $output .= 'cssSelectorAncestor: "#jp_container_'.$postid.'",';

        $output .= $supplied;

        $output .= 'solution: "html, flash", ';

        $output .= 'preload: "metadata",';

        $output .= 'wmode: "window",';

        $output .= '});';

        $output .= '}';

        $output .= 'jQuery("#jp_container_'.$postid.' .jp-interface").css("display", "block");';

        $output .= '});';

        $output .= '</script>';

        $output .= '<style>';

        $output .= '/* Fix Jplayer Height if no poster is uploaded */';
        if (empty($poster)) {
            $output .= '#jp_container_'.$postid.'.jp-audio.fullwidth {
                      padding-bottom: 1px !important;
                     margin-bottom: 20px !important;
                     height: 40px;
                }';
        }
        $output .= '</style>';
        $output .= '<div id="jp_container_'.$postid.'" class="jp-audio fullwidth">';

        $output .= '<div class="jp-type-single">';

        $output .= '<div id="jquery_jplayer_'.$postid.'" class="jp-jplayer"></div>';

        $output .= '<div class="jp-gui">';

        $output .= '<div class="jp-audio-play"><a href="javascript:;" tabindex="1" title="Play"></a></div>';

        $output .= '<div class="jp-interface" style="display: none;">';

        $output .= '<div class="jp-progress">
                            <div class="jp-seek-bar">
                                <div class="jp-play-bar"></div>
                            </div>
                        </div>

                        <div class="jp-duration"></div>
                        <div class="jp-time-sep">/</div>
                        <div class="jp-current-time"></div>

                        <div class="jp-controls-holder">

                            <ul class="jp-controls">
                                <li><a href="javascript:;" class="jp-play" tabindex="1" title="Play"><span>Play</span></a></li>
                                <li><a href="javascript:;" class="jp-pause" tabindex="1" title="Pause"><span>Pause</span></a></li>
                                <li class="li-jp-stop"><a href="javascript:;" class="jp-stop" tabindex="1" title="Stop"><span>Stop</span></a></li>
                            </ul>

                            <div class="jp-volume-bar">
                                <div class="jp-volume-bar-value"></div>
                            </div>

                            <ul class="jp-toggles">
                                <li><a href="javascript:;" class="jp-mute" tabindex="1" title="Mute"><span>Mute</span></a></li>
                                <li><a href="javascript:;" class="jp-unmute" tabindex="1" title="Unmute"><span>Unmute</span></a></li>
                            </ul>

                            <div class="jp-title"><ul><li></li></ul></div>

                        </div>

                    </div>

                    <div class="jp-no-solution">';
        $output .= __('<span>Update Required</span>To play the audio you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.', 'video_central');
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';

        endif;

        return $output;
    }

    /**
     * Echo Audio HTML.
     *
     * @since 1.0.0
     * @returns string
     */
    public function audio($postid)
    {
        echo get_audio($postid);
    }

    /**
     * Output video.
     *
     * @since 1.0.0
     *
     * @Supports M4V, OGV, WEBMV, FLV, RTMPV,
     * @returns string
     */
    public function get_video($video_file_url, $poster = null, $height = null)
    {
        $output = null;

        if ($video_file_url) {
            $postid = rand(1, 10000); //create a random id
        } else {
            $video_file_url = get_post_meta($postid, '_video_central_file', true);
        }

        if (filter_var($video_file_url, FILTER_VALIDATE_URL)) {
            switch (pathinfo($video_file_url, PATHINFO_EXTENSION)) {

                case 'mp4':  //mp3
                    $media = "mp4: '$video_file_url'";
                    $supplied = 'supplied: "mp4, all"';
                        break;
                case 'm4v':  //mp4
                    $media = "m4v: '$video_file_url'";
                    $supplied = 'supplied: "m4v, all",';
                    break;
                case 'flv': //flv
                    $media = "flv: '$video_file_url'";
                    $supplied = 'supplied: "flv, all",';
                        break;
                case 'ogg': //ogg
                    $media = "oga: '$video_file_url'";
                    $supplied = 'supplied: "oga, ogg, mp3",';
                        break;
                case 'ogv': //oga
                    $media = "ogv: '$video_file_url'";
                    $supplied = 'supplied: "ogv, all",';
                    break;
                case 'webma': //webma
                    $media = "webma: '$video_file_url'";
                    $supplied = 'supplied: "webma, mp3",';
                    break;
                case 'webm': //webma
                    $media = "webma: '$video_file_url'";
                    $supplied = 'supplied: "webma, mp3",';
                    break;
                case 'wav':
                    $media = "wav: '$video_file_url'";
                    $supplied = 'supplied: "wav, mp3",';
                    break;
                default:
                    // audio format not supported
                    return;
                    break;
            }
        }

        //$width = get_post_meta($postid, '_video_central_width', true);
        //$height = get_post_meta($postid, '_video_central_height', true);
        $poster = get_post_meta($postid, '_video_central_poster', true);

        if ($video_file_url):

         $output .= '<script type="text/javascript">';

        $output .= 'jQuery(document).ready(function () {';

        $output .= 'jQuery("#jquery_jplayer_'.$postid.'").jPlayer( { ready : function () { ';

        $output .= 'jQuery(this).jPlayer( "setMedia", { ';

        $output .= $media.',';

        if ($poster != '') {
            $output .= 'poster: "'.$poster.'"';
        }
        $output .= '}
                             );
                         },';

        $output .= 'cssSelectorAncestor : "#jp_container_'.$postid.'",';
        $output .= 'swfPath : "'.RADIUM_HTML5_MEDIA_ASSETS_URL.'/jplayer",';
        $output .= 'supplied: "';
        $output .= 'm4v, ';
        //if($ogv != '') $output .= 'ogv,';
        $output .= 'all",';
        $output .= 'size : {
                             width : "100%",
                             height : "100%"
                         },
                         wmode : "window"

                     }
                 );';

        $output .= 'jQuery("#jp_container_'.$postid.' .jp-interface").css("display", "block");';

        $output .= '});';

        $output .= '</script>';

        $output .= '<div id="jp_container_'.$postid.'" class="jp-video fullwidth">

                <div class="jp-type-single">

                <div id="jquery_jplayer_'.$postid.'" class="jp-jplayer"></div>

                <div class="jp-gui">

                    <div class="jp-video-play"><a href="javascript:;" class="jp-video-play-icon" tabindex="1" title="Play">Play</a></div>

                    <div class="jp-interface" style="display: none;">
                        <div class="jp-progress">
                            <div class="jp-seek-bar">
                                <div class="jp-play-bar"></div>
                            </div>
                        </div>

                        <div class="jp-duration"></div>
                        <div class="jp-time-sep">/</div>
                        <div class="jp-current-time"></div>

                        <div class="jp-controls-holder">
                            <ul class="jp-controls">
                                <li><a href="javascript:;" class="jp-play" tabindex="1" title="Play"><span>Play</span></a></li>
                                <li><a href="javascript:;" class="jp-pause" tabindex="1" title="Pause"><span>Pause</span></a></li>
                                <li class="li-jp-stop"><a href="javascript:;" class="jp-stop" tabindex="1" title="Stop"><span>Stop</span></a></li>
                            </ul>
                            <div class="jp-volume-bar">
                                <div class="jp-volume-bar-value"></div>
                            </div>

                            <ul class="jp-toggles">
                                <li><a href="javascript:;" class="jp-mute" tabindex="1" title="Mute"><span>Mute</span></a></li>
                                <li><a href="javascript:;" class="jp-unmute" tabindex="1" title="Unmute"><span>Unmute</span></a></li>
                                <li class="li-jp-full-screen"><a href="javascript:;" class="jp-full-screen" tabindex="1" title="Full Screen"><span>Full Screen</span></a></li>
                                <li class="li-jp-restore-screen"><a href="javascript:;" class="jp-restore-screen" tabindex="1" title="Restore Screen"><span>Restore Screen</span></a></li>
                            </ul>

                            <div class="jp-title">
                                <ul>
                                    <li></li>
                                </ul>
                            </div>

                        </div>

                    </div>

                    <div class="jp-no-solution">';
        $output .= __('<span>Update Required</span>To play the video you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.', 'video_central');
        $output .= '</div>

                </div>

            </div>
        </div>';

        endif;

        return $output;
    }

    /**
     * Echo Video HTML.
     *
     * @since 1.0.0
     * @returns string
     */
    public function video($postid)
    {
        echo $this->get_video($postid);
    }

    /**
     * Getter method for retrieving the object instance.
     *
     * @since 1.0.0
     */
    public static function get_instance()
    {
        return self::$instance;
    }
}
