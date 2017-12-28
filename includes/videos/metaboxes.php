<?php
add_filter( 'video_central_metaboxes', 'video_central_video_register_meta_boxes' );
/**
 * Register meta boxes
 *
 * Remember to change "video_central" to actual prefix in your project
 *
 * @param array $meta_boxes List of meta boxes
 *
 * @return array
 */
function video_central_video_register_meta_boxes( $meta_boxes ) {

    global $post;

    $id       = is_object($post) ? $post->ID : false;
    $id       = isset($_GET['post']) ?  $_GET['post'] : false;
    $duration = $id ? video_central_get_video_duration($id) : '0:00';

    $meta_boxes[] = array(
        'id'       => 'video-profile-settings',
        'title'    => __('Video Settings', 'video_central'),
        'pages'    => array( video_central_get_video_post_type() ),
        'context'  => 'normal',
        'priority' => 'high',
        'fields'   => array(

            array(
                'name'  => __('Featured Video', 'video_central'),
                'id'    => '_video_central_featured_video',
                'class' => '_video_central_featured_video',
                'std'   => 0,
                'type'  => 'checkbox',
            ),

            array(
                'name'    => __('Video Description', 'video_central'),
                'id'      => '_video_central_description',
                'type'    => 'wysiwyg',
                'options' => array(
                    'textarea_rows' => 4,
                    'teeny'         => true,
                    'media_buttons' => false,
                ),
            ),

            array(
                'name'             => __('Video Url (.mp4)', 'video_central'),
                'id'               => '_video_central_mp4',
                'class'            => '_video_central_mp4',
                'type'             => 'file_advanced',
                'max_file_uploads' => 1
            ),

            array(
                'name'             => __('Video Url (.webm)', 'video_central'),
                'id'               => '_video_central_webm',
                'class'            => '_video_central_webm',
                'type'             => 'file_advanced',
                'max_file_uploads' => 1
            ),

            array(
                'name'             => __('Video Url (.ogg)', 'video_central'),
                'id'               => '_video_central_ogg',
                'class'            => '_video_central_ogg',
                'type'             => 'file_advanced',
                'max_file_uploads' => 1
            ),

            array(
                'name'             => __('Video Url (.flv)', 'video_central'),
                'id'               => '_video_central_flv',
                'class'            => '_video_central_flv',
                'type'             => 'file_advanced',
                'max_file_uploads' => 1
            ),

            array(
                'name'             => __('Custom Thumbnail', 'video_central'),
                'desc'             => __('This will override the auto generated video thumbnail', 'video_central'),
                'id'               => '_video_poster',
                'class'            => '_video_poster',
                'type'             => 'image_advanced',
                'max_file_uploads' => 1
            ),

            array(
                'name'  => __('Video ID', 'video_central'),
                'desc'  => __('Get video id from the url eg http://youtube.com/watch?v=<strong>123456</strong>', 'video_central'),
                'id'    => '_video_central_video_id',
                'class' => '_video_central_video_id',
                'type'  => 'text',
            ),

            array(
                'name'  => __('Video Embed Code', 'video_central'),
                'desc'  => __('Add an embed code here.', 'video_central'),
                'id'    => '_video_central_embed_code',
                'class' => '_video_central_embed_code',
                'type'  => 'textarea',
            ),

            array(
                'name'    => __('Video Source', 'video_central'),
                'id'      => '_video_central_source',
                'class'   => '_video_central_source',
                'type'    => 'select',
                'options' => array(
                    'vimeo'   => 'Vimeo',
                    'youtube' => 'Youtube',
                    'self'    => __('Self Hosted', 'video_central'),
                    'embed'   => __('Embed Code', 'video_central'),
                ),
                'multiple' => false,
                'std'      => array( 'youtube' )
            ),

            array(
                'name'  => __('Video Duration', 'video_central'),
                'desc'  => __('The length if the video in minutes eg <strong>6:30</strong>', 'video_central'),
                'id'    => '_video_central_video_duration',
                'class' => '_video_central_video_duration',
                'std'   => $duration,
                'type'  => 'text',
            ),
        )
    );

    return $meta_boxes;
}
