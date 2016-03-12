<?php

/**
 * Posttype class for Radium_Video.
 *
 * @since 1.2.0
 *
 * @author  Franklin M Gitonga
 */
class Video_Central_Playlist_Posttype
{
    /**
     * Holds a copy of the object for easy reference.
     *
     * @since 1.2.0
     *
     * @var object
     */
    private static $instance;

    /**
     * Constructor. Hooks all interactions to initialize the class.
     *
     * @since 1.2.0
     */
    public function __construct()
    {
        self::$instance = $this;

        $this->post_type = video_central_get_playlist_post_type();

        $this->playlist_init();

        add_action('admin_init', array($this, 'register_meta_boxes'));
    }

    public function playlist_init()
    {

        // Register playlist content type
        register_post_type(
            video_central_get_playlist_post_type(),
            apply_filters('video_central_register_playlist_post_type', array(
                'labels' => video_central_get_playlist_post_type_labels(),
                'rewrite' => video_central_get_playlist_post_type_rewrite(),
                'supports' => video_central_get_playlist_post_type_supports(),
                'description' => __('Video Central Playlist', 'video_central'),
                //'capabilities'        => video_central_get_video_caps(),
                //'capability_type'     => array( 'video', 'videos' ),
                'menu_position' => 5,
                'has_archive' => video_central_get_root_slug(),
                'exclude_from_search' => false,
                'show_in_nav_menus' => true,
                'show_in_menu' => 'edit.php?post_type='.video_central_get_video_post_type(),
                'public' => true,
                //'show_ui'             => current_user_can( 'video_central_admin' ),
                'can_export' => true,
                'hierarchical' => true,
                'query_var' => true,
                'menu_icon' => '',
            ))
        );
    }

    public function register_meta_boxes()
    {

        //select videos

         $arg = array(

            array(
                'name' => __('Video Description', 'video_central'),
                'desc' => __('', 'video_central'),
                'id' => '_video_central_playlist_ids',
                'type' => 'Text',
            ),

        );

        $meta_boxes[] = array(
            'id' => 'video-profile-settings',
            'title' => __('Playlist Settings', 'video_central'),
            'pages' => array(video_central_get_playlist_post_type()),
            'context' => 'normal',
            'priority' => 'high',
            'fields' => apply_filters('video_central_metaboxes_playlist', $arg),
        );

        // Make sure there's no errors when the plugin is deactivated or during upgrade
        foreach ($meta_boxes as $meta_box) {
            new Radium_Video_Metaboxes_Init($meta_box);
        }
    }

    /**
     * Getter method for retrieving the object instance.
     *
     * @since 1.2.0
     */
    public static function get_instance()
    {
        return self::$instance;
    }
}
