<?php
/**
 * Functions of Video Central's Default theme.
 *
 * @since 1.0.0
 */

/* Theme Setup ***************************************************************/

if (!class_exists('Video_Central_Default_Theme')) :

/**
 * Loads Video Central Default Theme functionality.
 *
 * This is not a real theme by WordPress standards, and is instead used as the
 * fallback for any WordPress theme that does not have Video Central templates in it.
 *
 * To make your custom theme Video Central compatible and customize the templates, you
 * can copy these files into your theme without needing to merge anything
 * together; Video Central should safely handle the rest.
 *
 * See @link Video_Central_Theme_Compat() for more.
 *
 * @since 1.0.0
 */
class Video_Central_Default_Theme extends Video_Central_Theme_Compat
{
    /** Functions *************************************************************/

    /**
     * The main Video Central (Default) Loader.
     *
     * @since 1.0.0
     *
     * @uses Video_Central_Default::setup_globals()
     * @uses Video_Central_Default::setup_actions()
     */
    public function __construct($properties = array())
    {
        parent::__construct(video_central_parse_args($properties, array(
            'id' => 'default',
            'name' => __('Video Central Default', 'video_central'),
            'version' => video_central_get_version(),
            'dir' => trailingslashit(video_central()->themes_dir.'default'),
            'url' => trailingslashit(video_central()->themes_url.'default'),
        ), 'default_theme'));

        $this->setup_actions();
    }

    /**
     * Setup the theme hooks.
     *
     * @since 1.0.0
     *
     * @uses add_filter() To add various filters
     * @uses add_action() To add various actions
     */
    private function setup_actions()
    {

        /* Scripts ***********************************************************/

        add_action('video_central_enqueue_scripts',         array($this, 'enqueue_styles')); // Enqueue theme CSS
        add_action('video_central_enqueue_scripts',         array($this, 'enqueue_scripts')); // Enqueue theme JS
        add_filter('video_central_enqueue_scripts',         array($this, 'localize_video_script')); // Enqueue theme script localization
    }

    /**
     * Load the theme CSS.
     *
     * @since 1.0.0
     *
     * @uses wp_enqueue_style() To enqueue the styles
     */
    public function enqueue_styles()
    {

        // Setup styles array
        $styles = array();

        $styles['video-central-grid'] = array(
            'file' => 'css/grid.css',
            'dependencies' => array(),
        );

        $styles['video-central-style'] = array(
            'file' => 'css/style.css',
            'dependencies' => array(),
        );

        $styles['video-central-font-awesome'] = array(
            'file' => 'css/font-awesome.css',
            'dependencies' => array(),
        );

        // Filter the scripts
        $styles = apply_filters('video_central_default_theme_styles', $styles);

        // Enqueue the styles
        foreach ($styles as $handle => $attributes) {
            video_central_enqueue_style($handle, $attributes['file'], $attributes['dependencies'], $this->version, 'screen');
        }
    }

    /**
     * Enqueue the required Javascript files.
     *
     * @since 1.0.0
     *
     * @uses wp_enqueue_script() To enqueue the scripts
     */
    public function enqueue_scripts()
    {

        // Setup scripts array
        $scripts = array();

        $scripts['video-central-plugins'] = array(
            'file' => 'js/plugins.min.js',
            'dependencies' => array('jquery'),
        );

        $scripts['video-central-js'] = array(
            'file' => 'js/main.min.js',
            'dependencies' => array('jquery', 'video-central-plugins'),
        );

        // Filter the scripts
        $scripts = apply_filters('video_central_default_theme_scripts', $scripts);

        // Enqueue the scripts
        foreach ($scripts as $handle => $attributes) {
            video_central_enqueue_script($handle, $attributes['file'], $attributes['dependencies'], $this->version, 'screen');
        }
    }

    /**
     * Load localizations for video script.
     *
     * These localizations require information that may not be loaded even by init.
     *
     * @since 1.0.0
     *
     * @uses wp_localize_script() To localize the script
     */
    public function localize_video_script()
    {

        // Single Video
        wp_localize_script('video-central', 'video_central_js', array(
            'generic_ajax_error' => __('Something went wrong. Refresh your browser and try again.', 'video_central'),
        ));

        wp_localize_script('video-central-js', 'likesdata', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('likes-nonce'),
            'refresh' => isset($options['likes_refresh' ]) ? "'".$options['likes_refresh' ]."'" : '0',
            'lifetime' => isset($options['likes_lifetime']) ? "'".$options['likes_lifetime']."'" : '1460',
            'unlike' => isset($options['likes_unlike'  ]) && "'".is_bool($options['likes_unlike'  ])."'" ? $options['likes_unlike'] : '1',
        ));
    }
}
new Video_Central_Default_Theme();
endif;
