<?php

/**
 * Video Central Video Template Tags
 *
 * @package Video Central
 * @subpackage TemplateTags
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


/** Video Player*****************************************************************/

/**
 * Output the video player
 *
 * @since 1.0.0
 * @uses video_central_get_player() To get the video player
 */
function video_central_player( $video_id = 0 ) {
    echo video_central_get_player( $video_id );
}
    /**
     * Return the unique id of the custom post type for videos
     *
     * @since 1.0.0
     *
     * @uses apply_filters() Calls 'video_central_get_video_post_type' with the video
     *                        post type id
     * @return string The unique video post type id
     */
    function video_central_get_player( $video_id = 0 ) {

        $video_id = video_central_get_video_id( $video_id );

		$upload_video_id  = get_post_meta( $video_id, '_video_central_video_id',    true );
        $upload_source    = get_post_meta( $video_id, '_video_central_source',      true );

        $poster = video_central_get_featured_image_url( $video_id, array( 'height'=>'800', 'width'=>'600') );

        if ( $upload_source == 'youtube') {

            $url = 'http://www.youtube.com/watch?v=' . $upload_video_id;

            $dataSetup['forceSSL'] = 'true';
            $dataSetup['techOrder'] = array("youtube");
            $dataSetup['quality'] = '720p';

            $jsonDataSetup = str_replace('\\/', '/', json_encode($dataSetup));

            //Output the <video> tag
$output = <<<_end_

             <video class="video-js vjs-default-skin" controls preload="auto" width="auto" height="auto" data-setup={$jsonDataSetup}>
                <source src="{$url}" type="video/youtube" />
              </video>
_end_;

            return $output;

		} elseif ( $upload_source == 'vimeo') {

            $url = 'http://vimeo.com/' . $upload_video_id;

$output = <<<_end_

            <div class="video-central-player-video-wrapper">
                <iframe src="//player.vimeo.com/video/{$upload_video_id}" width="" height="" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
            </div>
_end_;
            return $output;

        } else {

            $file_id_mp4    = get_post_meta( $video_id, '_video_central_mp4', true );
            $file_id_webm   = get_post_meta( $video_id, '_video_central_webm', true );
            $file_id_ogg    = get_post_meta( $video_id, '_video_central_ogg', true );

            $file_mp4   = wp_get_attachment_url( $file_id_mp4 );
            $file_webm  = wp_get_attachment_url( $file_id_webm );
            $file_ogg   = wp_get_attachment_url( $file_id_ogg );

            $output = '<video class="video-js vjs-default-skin" controls preload="none" width="auto" height="auto" poster="' . $poster . '" data-setup="{}">';
                if( $file_mp4 ) $output .= '<source src="'. $file_mp4 .'" type="video/mp4" />';
                if( $file_webm ) $output .= '<source src="'. $file_webm .'" type="video/webm" />';
                if( $file_ogg ) $output .= '<source src="'. $file_ogg .'" type="video/ogg" />';
              $output .= '</video>';

        }

        return do_shortcode( $output );

    }