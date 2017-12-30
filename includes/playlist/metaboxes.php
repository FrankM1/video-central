<?php
//add_filter( 'video_central_metaboxes', 'video_central_playlist_register_meta_boxes' );
/**
 * Register meta boxes
 *
 * Remember to change "video_central" to actual prefix in your project
 *
 * @param array $meta_boxes List of meta boxes
 *
 * @return array
 */
function video_central_playlist_register_meta_boxes( $meta_boxes ) {

        //select videos
         $arg = array(
            array(
                'name' => __('Video Description', 'video_central'),
                'desc' => __('', 'video_central'),
                'id' => '_video_central_playlist_ids',
                'type' => 'text',
            ),
        );

        $meta_boxes[] = array(
            'id' => 'video-profile-settings',
            'title' => __('Playlist Settings', 'video_central'),
            'pages' => array(video_central_get_playlists_post_type()),
            'context' => 'normal',
            'priority' => 'high',
            'fields' => apply_filters('video_central_metaboxes_playlist', $arg),
        );

        $args = array(
            array(
                'name' => __( 'Post', 'video_central' ),
                'id' => '_video_playlist_video_ids',
                'type' => 'video_select',
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
            'pages' => array(video_central_get_playlists_post_type()),
            'context' => 'normal',
            'priority' => 'high',
            'fields' => apply_filters('video_central_metaboxes_playlist', $args),
        );

        // Make sure there's no errors when the plugin is deactivated or during upgrade
         return $meta_boxes;
    }
