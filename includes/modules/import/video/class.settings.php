<?php

/**
 *  Import Settings class for Video Central.
 *
 * @since 1.0.0
 *
 * @package Video Central
 * @author  Franklin M Gitonga
 */

class Video_Central_Import_Video_Settings {

    /**
     * Constructor. Hooks all interactions to initialize the class.
     *
     * @since 1.0.0
     */
    public function __construct(){

        add_filter('video_central_map_settings_meta_caps',      array($this, 'set_caps'), 10, 4 );
        add_filter('video_central_admin_get_settings_sections', array($this, 'settings_sections'));
        add_filter('video_central_admin_get_settings_fields',   array($this, 'settings_fields'));

    }

    /**
     * Maps settings capabilities
     *
     * @since 1.0.0
     *
     * @param array $caps Capabilities for meta capability
     * @param string $cap Capability name
     * @param int $user_id User id
     * @param mixed $args Arguments
     * @uses get_post() To get the post
     * @uses apply_filters() Calls 'video_central_map_import_settings_meta_caps' with caps, cap, user id and
     *                        args
     * @return array Actual capabilities for meta capability
     */
    function set_caps( $caps = array(), $cap = '', $user_id = 0, $args = array() ){

        // What capability is being checked?
        switch ( $cap ) {

            // User import capabilities
            case 'video_central_settings_import_videos' : // Settings - Per page
                $caps = array( video_central()->admin->minimum_capability );
                break;
        }

        return apply_filters( 'video_central_map_import_settings_meta_caps', $caps, $cap, $user_id, $args );

    }

    /**
     * Add subpages on our custom post type
     *
     * @since 1.0.0
     */
    function settings_sections( $sections ){

        $new_sections = array (

            'video_central_settings_import_videos' => array (
                'title'    => __( 'Video Central Import Settings', 'video_central' ),
                'callback' => array($this, 'setting_section_callback'),
                'page'     => 'discussion'
            )

        );

        return array_merge($new_sections, $sections);

    }

    /**
     * User settings section description for the settings page
     *
     * @since 1.0.0
     */
    function setting_section_callback(){
        ?><p><?php esc_html_e( 'Settings for importing videos form youtube and other sources.', 'video_central' ); ?></p>
    <?php
    }

    /**
     * Add subpages on our custom post type
     *
     * @since 1.0.0
     */
    function settings_fields( $fields ){

        $fields['video_central_settings_import_videos'] = array (

            /** Features Section **************************************************/

            // Allow topic tags
            '_video_central_allow_video_imports' => array(
                'title'             => __( 'Allow Video Imports', 'video_central' ),
                'callback'          => array($this, 'setting_callback_allow_video_imports'),
                'sanitize_callback' => 'intval',
                'args'              => array()
            ),

        );

        return $fields;

    }

    /**
     * Add subpages on our custom post type
     *
     * @since 1.0.0
     */
    public function setting_callback_allow_video_imports( ){ ?>
        <input name="_video_central_allow_video_imports" id="_video_central_allow_video_imports" type="checkbox" value="1" <?php checked( video_central_allow_video_imports( true ) ); video_central_maybe_admin_setting_disabled( '_video_central_allow_video_imports' ); ?> />
        <label for="_video_central_allow_video_imports"><?php esc_html_e( 'Allow video imports', 'video_central' ); ?></label>
      <?php  
    }

}
video_central()->admin->importer_settings = new Video_Central_Import_Video_Settings;