<?php

/**
 * Shortcode class for Radium_MediaElements.
 *
 * @since 1.0.0
 *
 * @author	Franklin M Gitonga
 */
class Radium_MediaElements_Shortcode
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

        $this->helper = new Radium_MediaElements_Helper();

        add_shortcode('video_central_audio',  array($this, 'audio_sc'));
        add_shortcode('video_central',  array($this, 'video_sc'));
    }

    /**
     * Outputs Audio file data in a shortcode called '[video_central_audio]'.
     *
     * @since 1.0.0
     *
     * @Supports mp3, m4a, ogg, webma, wav
     * @usage [video_central_audio href="#" hide_title="false"]
     *
     * @param href= link to file
     * @param hide_title bool
     *
     * @return string $output Concatenated string
     */
    public function audio_sc($atts, $title = null)
    {
        extract(shortcode_atts(array('href' => ''), $atts));

        $html = $this->helper->get_audio(null, $href);

        return $html;
    }

    /**
     * Outputs Video file data in a shortcode called '[video_central_audio]'.
     *
     * @since 1.0.0
     *
     * @Supports mp3, m4a, ogg, webma, wav
     * @usage [audio href="#" hide_title="false"]
     *
     * @param href= link to file
     * @param hide_title bool
     *
     * @return string $output Concatenated string
     */
    public function video_sc($atts, $title = null)
    {
        extract(shortcode_atts(array('href' => ''), $atts));

        $html = $this->helper->get_video($href);

        return $html;
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
