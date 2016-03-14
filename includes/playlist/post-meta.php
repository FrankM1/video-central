<?php

/**
 * Admin class for Radium Video.
 *
 * @since 1.0.0
 *
 * @author	Franklin M Gitonga
 */
class Radium_Video_Playlist_Metaboxes
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

        add_action('admin_init', array($this, 'register_meta_boxes'));
    }

    /**
     * Register meta boxes.
     *
     * @since 2.1.0
     */
    public function register_meta_boxes()
    {
        $args = array(

                array(
                    'name' => __('Post', 'video_central'),
                    'id' => '_video_playlist_video_ids',
                    'type' => 'VideoSelect',
                    'multiple' => true,
                    'post_type' => video_central_get_video_post_type(),
                    // Default selected value (post ID(s))
                    'std' => '',
                    'placeholder' => __('Select an Item', 'video_central'),
                    // Query arguments (optional). No settings means get all published posts
                    // @see https://codex.wordpress.org/Class_Reference/WP_Query
                    'query_args' => array(
                        'post_status' => 'publish',
                        'posts_per_page' => -1,
                    ),
                ),

        );

        $meta_boxes[] = array(
            'id' => 'video-playlist-settings',
            'title' => __('Select Videos', 'video_central'),
            'pages' => array(video_central_get_playlist_post_type()),
            'context' => 'normal',
            'priority' => 'high',
            'fields' => apply_filters('video_central_metaboxes_playlist', $args),
        );

        // Make sure there's no errors when the plugin is deactivated or during upgrade
        foreach ($meta_boxes as $meta_box) {
            new Video_Central_Metaboxes_Init($meta_box);
        }
    }
}
