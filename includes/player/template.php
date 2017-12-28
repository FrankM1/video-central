<?php

/**
 * Video Central Video Template Tags.
 */

/** Video Player*****************************************************************/

/**
 * Output the video player.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_player() To get the video player
 */
function video_central_player($video_id = 0)
{
    echo video_central_get_player($video_id);
}
    /**
     * Return the unique id of the custom post type for videos.
     *
     * @since 1.0.0
     *
     * @uses apply_filters() Calls 'video_central_get_video_post_type' with the video
     *                        post type id
     *
     * @return string The unique video post type id
     */
    function video_central_get_player($video_id = 0)
    {

        $source = null;

        $video_id = video_central_get_video_id($video_id);

        $upload_video_id = get_post_meta($video_id, '_video_central_video_id',    true);
        $upload_source = get_post_meta($video_id, '_video_central_source',      true);
        $embed_code = get_post_meta($video_id, '_video_central_embed_code', true);

        $poster = video_central_get_featured_image_url($video_id, array('height' => '800', 'width' => '600'));
        $origin = site_url();

        if ($upload_source === 'youtube') {
            $url = 'https://www.youtube.com/embed/'.$upload_video_id . '?version=3&enablejsapi=1';

            $dataSetup['forceSSL'] = 'true';
            $dataSetup['techOrder'] = array('youtube');
            $dataSetup['quality'] = '720p';
            $dataSetup['ytcontrols'] = 'true';
            $dataSetup['forceHTML5'] = 'true';

            $jsonDataSetup = str_replace('\\/', '/', json_encode($dataSetup));

            $output = <<<_end_
<iframe id="video-central-ytplayer" type="text/html" width="auto" height="auto" src="{$url}" frameborder="0" showinfo="0" enablejsapi="1" rel="0" origin="{$origin}" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
_end_;

            return $output;
        } elseif ($upload_source == 'vimeo') {
            $url = 'http://vimeo.com/'.$upload_video_id;

            $output = <<<_end_
<iframe src="//player.vimeo.com/video/{$upload_video_id}" width="auto" height="auto" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
_end_;

            return $output;

        } elseif ($upload_source == 'embed') {

            return $embed_code;

        } else {

            $file_id_mp4 = get_post_meta($video_id, '_video_central_mp4', true);
            $file_id_webm = get_post_meta($video_id, '_video_central_webm', true);
            $file_id_ogg = get_post_meta($video_id, '_video_central_ogg', true);
            $file_id_flv = get_post_meta($video_id, '_video_central_flv', true);
            $file_extension = get_post_meta($video_id, '_video_central_video_file', true);

            $file_mp4 = wp_get_attachment_url($file_id_mp4);
            $file_webm = wp_get_attachment_url($file_id_webm);
            $file_ogg = wp_get_attachment_url($file_id_ogg);
            $file_flv = wp_get_attachment_url($file_id_flv);

            $dataSetup['fluid']     = 'true';
            $dataSetup['controls']  = 'true';
            $dataSetup['preload']   = 'auto';
            $dataSetup['poster']    = $poster;
            $dataSetup['width']     = 'auto';
            $dataSetup['height']    = 'auto';

            if ($file_extension == 'flv') {
                $video_url = get_post_meta($video_id, '_video_central_video_url', true);

                $dataSetup['techOrder'] = array('flash');

                $jsonDataSetup = str_replace('\\/', '/', json_encode($dataSetup));

                $video_url = $file_flv ? $file_flv : $video_url;

                if ($video_url) {
                    $source = '<source src="'.$video_url.'" type="video/flv" />';
                }

            } else {

                if ($file_mp4) {
                    $source .= '<source src="'.$file_mp4.'" type="video/mp4" />';
                }
                if ($file_webm) {
                    $source .= '<source src="'.$file_webm.'" type="video/webm" />';
                }
                if ($file_ogg) {
                    $source .= '<source src="'.$file_ogg.'" type="video/ogg" />';
                }

            }

            $jsonDataSetup = str_replace('\\/', '/', json_encode($dataSetup));

            if( $source ) {

                $output = "<video class='video-js vjs-default-skin' data-setup='".$jsonDataSetup."'>";
                    $output .= $source;

                $output .= '</video>';

            } else {

                $output = '<div class="alert error">'. __('Failed to retrieve video file', 'video_central') .'</div>';

            }

        }

        return do_shortcode($output);
    }
