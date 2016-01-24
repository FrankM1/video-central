<?php

/**
 * Playlist Central Playlist Functions
 *
 * @package Playlist Central
 * @subpackage Functions
 */

/** Video Playlist *****************************************************************/

/**
 * Output playlist id
 *
 * @since 1.2.0
 *
 * @param $playlist_id Optional. Used to check emptiness
 * @uses video_central_get_playlist_id() To get the playlist id
 */
function video_central_playlist_id( $playlist_id = 0 ) {
    echo video_central_get_playlist_id( $playlist_id );
}
    /**
     * Return the playlist id
     *
     * @since 1.2.0
     *
     * @param $playlist_id Optional. Used to check emptiness
     * @uses apply_filters() Calls 'video_central_get_playlist_id' with the playlist id and
     *                        supplied playlist id
     * @return int The playlist id
     */
    function video_central_get_playlist_id( $playlist_id = 0 ) {

        $video_central = video_central();

        // Easy empty checking
        if ( !empty( $playlist_id ) && is_numeric( $playlist_id ) ) {
            $video_central_playlist_id = $playlist_id;

        // Fallback
        } else {

            $video_central_playlist_id = $playlist_id ? $playlist_id : $video_central->playlist_instance;

        }

        return (int) apply_filters( __FUNCTION__, (int) $video_central_playlist_id, $playlist_id );
    }

/**
 * Output the playlist playlist
 *
 * @since 1.2.0
 * @uses video_central_get_playlist() To get the playlist player
 */
function video_central_playlist( $args = array() ) {
    echo video_central_get_playlist( $args );
}
    /**
     * Return the unique id of the custom post type for playlists
     *
     * @since 1.2.0
     *
     */
    function video_central_get_playlist( $args = array() ) {

        $args['ids'] = get_post_meta( $args['id'], '_video_central_playlist_ids', true);

        if ( ! empty( $args['ids'] ) ) {
            $videos = explode(',', $args['ids'] );
        }

        if( empty( $videos ) ) {
            return '';
        }

        $output = '<div id="video-central-playlist-'. video_central_get_playlist_id() .'" class="video-central-playlist-'. video_central_get_playlist_id() .' default" >';

            $output .= '<div class="video-central-player">';

            $output .= video_central_get_player($videos[0]);

            $output .= '</div>';

            $output .= '<div class="video-central-playlist-wrap">';

                $output .= '<div class="video-central-playlist">';

                    $count = 0;

                    foreach( $videos as $video_id ) :

                        $count++;

                        $playlist_class = $count == 1 ? 'active' : '';

                        $output .= '<div class="video-central-playlist-item '. $playlist_class .'">';

                            $output .= '<a href="'. video_central_get_video_permalink($video_id) . '">';

                                 $output .= video_central_get_video_title($video_id);

                             $output .= '</a>';

                         $output .= '</div>';

                    endforeach;

                 $output .= '</div>';

                 $output .= '<a href="#" class="playlist-visibility collapse"></a>';

            $output .= '</div>';

        $output .= '</div>';

        return $output;

    }
