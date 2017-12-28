<?php

/**
 * Playlist Central Playlist Functions.
 */

/** Video Playlist *****************************************************************/

/**
 * Output playlist id.
 *
 * @since 1.2.0
 *
 * @param $playlist_id Optional. Used to check emptiness
 *
 * @uses video_central_get_playlist_id() To get the playlist id
 */
function video_central_playlist_id($playlist_id = 0)
{
    echo video_central_get_playlist_id($playlist_id);
}
    /**
     * Return the playlist id.
     *
     * @since 1.2.0
     *
     * @param $playlist_id Optional. Used to check emptiness
     *
     * @uses apply_filters() Calls 'video_central_get_playlist_id' with the playlist id and
     *                        supplied playlist id
     *
     * @return int The playlist id
     */
    function video_central_get_playlist_id($playlist_id = 0)
    {
        $video_central = video_central();

        // Easy empty checking
        if (!empty($playlist_id) && is_numeric($playlist_id)) {
            $video_central_playlist_id = $playlist_id;

        // Fallback
        } else {
            $video_central_playlist_id = $playlist_id ? $playlist_id : $video_central->playlist_instance;
        }

        return (int) apply_filters( __FUNCTION__, (int) $video_central_playlist_id, $playlist_id );
    }

/**
 * Output the playlist playlist.
 *
 * @since 1.2.0
 *
 * @uses video_central_get_playlist() To get the playlist player
 */
function video_central_playlist( $post, $args = array())
{
    echo video_central_get_playlist( $post, $args );
}

    /**
     * Display a playlist.
     *
     * @since 1.0.0
     * @todo Add an arg to specify a template path that doesn't exist in the /video-central directory.
     *
     * @param mixed $post A post ID, WP_Post object or post slug.
     * @param array $args Playlist arguments.
     */
    function video_central_get_playlist( $post, $args = array() ) {
        if ( is_string( $post ) && ! is_numeric( $post ) ) {
            // Get a playlist by its slug.
            $post = get_page_by_path( $post, OBJECT, video_central_get_playlist_post_type() );
        } else {
            $post = get_post( $post );
        }

        if ( ! $post || video_central_get_playlist_post_type() !== get_post_type( $post ) ) {
            return;
        }

        $videos = get_video_central_playlist_videos( $post );

        if ( empty( $videos ) ) {
            return;
        }

        $args = wp_parse_args( $args, array(
            'container'     => true,
            'enqueue'       => true,
            'print_data'    => true,
            'show_playlist' => true,
            'player'        => '',
            'theme'         => get_video_central_default_theme(),
            'template'      => '',
        ) );

        if ( $args['enqueue'] ) {
            VideoCentral::enqueue_assets();
        }

        $template_names = array(
            "playlist-{$post->ID}.php",
            "playlist-{$post->post_name}.php",
            'playlist.php',
        );

        // Prepend custom templates.
        if ( ! empty( $args['template'] ) ) {
            $add_templates = array_filter( (array) $args['template'] );
            $template_names = array_merge( $add_templates, $template_names );
        }

        $template_loader = new VideoCentral_Template_Loader();
        $template = $template_loader->locate_template( $template_names );

        $themes = get_video_central_themes();
        if ( ! isset( $themes[ $args['theme'] ] ) ) {
            $args['theme'] = 'default';
        }

        $classes   = array( 'video-central-playlist-playlist' );
        $classes[] = $args['show_playlist'] ? '' : 'is-playlist-hidden';
        $classes[] = sprintf( 'video-central-playlist-theme-%s', sanitize_html_class( $args['theme'] ) );
        $classes   = implode( ' ', array_filter( $classes ) );

        if ( $args['container'] ) {
            echo '<div class="video-central-playlist-playlist-container">';
        }

        do_action( 'video_central_before_playlist', $post, $videos, $args );

        include( $template );

        do_action( 'video_central_after_playlist', $post, $videos, $args );

        if ( $args['container'] ) {
            echo '</div>';
        }
    }
    