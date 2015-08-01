<?php
/**
 * Admin class for Radium Video.
 *
 * @since 1.0.0
 *
 * @package	Radium_Video
 * @author	Franklin M Gitonga
 */

class Radium_Video_Metaboxes {

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
	public function __construct() {

		self::$instance = $this;

        add_action( 'admin_init', array( $this, 'register_meta_boxes') );

	}

    /**
     * Register meta boxes
     *
     * @since 2.1.0
     * @return void
     */
    function register_meta_boxes() {

        /* ---------------------------------------------------------------------- */
        /*  Video Sidebar
        /* ---------------------------------------------------------------------- */
         $arg = array(

				array(
				    'name' =>__('Featured Video','video_central'),
				    'desc' => __('','video_central'),
				    'id'   => '_video_central_featured_video',
				    'std'  => 0,
				    'type' => 'Checkbox',
				),

				array(
				    'name' =>__('Video Description','video_central'),
				    'desc' => __('','video_central'),
				    'id'   => '_video_central_description',
				    'type' => 'TextArea',
				),

                array(
                    'name' =>__('Video Url (.mp4)','video_central'),
                    'desc' => __('','video_central'),
                    'id'   => '_video_central_mp4',
                    'std'  => '',
                    'type' => 'FileAdvanced',
				    'max_file_uploads' => 1
                ),

                array(
                    'name' =>__('Video Url (.webm)','video_central'),
                    'desc' => __('','video_central'),
                    'id'   => '_video_central_webm',
                    'std'  => '',
                    'type' => 'FileAdvanced',
				    'max_file_uploads' => 1
                ),

                array(
                    'name' =>__('Video Url (.ogg)','video_central'),
                    'desc' => __('','video_central'),
                    'id'   => '_video_central_ogg',
                    'std'  => '',
                    'type' => 'FileAdvanced',
				    'max_file_uploads' => 1
                ),

				array(
				    'name' =>__('Custom Thumbnail','video_central'),
				    'desc' => __('This will override the auto generated video thumbnail','video_central'),
				    'id'   => '_video_poster',
				    'std'  => '',
				    'type' => 'ImageAdvanced',
				    'max_file_uploads' => 1
				),

                array(
                    'name' =>__('Video ID','video_central'),
                    'desc' => __('Get video id from the url eg http://youtube.com/watch?v=<strong>123456</strong>','video_central'),
                    'id'   => '_video_central_video_id',
                    'std'  => '',
                    'type' => 'Text',
                ),

                array(
                    'name' =>__('Video Embed Code','video_central'),
                    'desc' => __('Add an embed code here.','video_central'),
                    'id'   => '_video_central_embed_code',
                    'std'  => '',
                    'type' => 'TextArea',
                ),

                array(
                    'name' =>__('Video Source','video_central'),
                    'desc' => __('','video_central'),
                    'id'   => '_video_central_source',
                    'type' => 'Select',
                    'options' => array(
                    	'vimeo' 	=> 'Vimeo',
                    	'youtube' 	=> 'Youtube',
                    	'self' 		=> __('Self Hosted', 'video_central'),
                    	'embed' 	=> __('Embed Code', 'video_central'),
                    ),
                    'multiple'  => false,
                    'std'       => array( 'self' )
                ),

        );

        $meta_boxes[] = array(
            'id'       => 'video-profile-settings',
            'title'    => __('Profile Settings', 'video_central'),
            'pages'    => array( video_central_get_video_post_type() ),
            'context'  => 'normal',
            'priority' => 'high',
            'fields'   => apply_filters( 'video_central_metaboxes', $arg )
        );

        // Make sure there's no errors when the plugin is deactivated or during upgrade
        foreach ( $meta_boxes as $meta_box ) {

            new Radium_Video_Metaboxes_Init( $meta_box );

        }

    }

}
