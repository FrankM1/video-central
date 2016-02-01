<?php

/**
 * Video Importer Class.
 */
abstract class Video_Central_Video_Importer
{
    /**
     * $result import progress data.
     *
     * @since 1.0.0
     */
    public $result = array();

    /**
     * $post_type post type in use.
     *
     * @since 1.0.0
     */
    private $post_type;

    /**
     * Constructor. Hooks all interactions to initialize the class.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        if (!video_central_allow_video_imports()) {
            return;
        }

        $this->post_type = video_central_get_video_post_type();

        $this->taxonomy = video_central()->video_cat_tax_id;

        add_filter('post_updated_messages', array(&$this, 'updated_messages'));

        // for empty imported posts, skip $maybe_empty verification
        add_filter('wp_insert_post_empty_content', array(&$this, 'force_empty_insert'), 999, 2);

        //import
        add_action('video_central_import_videos', array(&$this, 'import_videos'));

        // response to ajax import
        add_action('wp_ajax_video_central_import_videos', array(&$this, 'ajax_track_video_import'));

        // response to ajax import
        add_action('wp_ajax_video_central_import_progress', array(&$this, 'ajax_get_import_progress'));

        // response to new video ajax query
        add_action('wp_ajax_video_central_query_youtube_video', array(&$this, 'ajax_video_query'));

        //add on load event
        add_action('load-video_page_video_central_import', array(&$this, 'import_onload'));

        // response to new video ajax query
        add_action('wp_ajax_video_central_check_remote_video_status', array(&$this, 'ajax_check_remote_status'));
    }

    /**
     * Custom post type messages on edit, update, create, etc.
     *
     * @since 1.0.0
     *
     * @param array $messages
     */
    public function updated_messages($messages)
    {
        global $post, $post_ID;

        $messages['video'] = array(
            0 => '', // Unused. Messages start at index 1.
            1 => sprintf(__('Video updated <a href="%s">See video</a>', 'video_central'), esc_url(get_permalink($post_ID))),
            2 => __('Custom field updated.', 'video_central'),
            3 => __('Custom field deleted.', 'video_central'),
            4 => __('Video updated.', 'video_central'),
            /* translators: %s: date and time of the revision */
            5 => isset($_GET['revision']) ? sprintf(__('Video restored to version %s', 'video_central'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
            6 => sprintf(__('Video published. <a href="%s">See video</a>', 'video_central'), esc_url(get_permalink($post_ID))),
            7 => __('Video saved.', 'video_central'),
            8 => sprintf(__('Video saved. <a target="_blank" href="%s">See video</a>', 'video_central'), esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))),
            9 => sprintf(__('Video will be published at: <strong>%1$s</strong>. <a target="_blank" href="%2$s">See video</a>', 'video_central'),
            // translators: Publish box date format, see http://php.net/date
            date_i18n('M j, Y @ G:i', strtotime($post->post_date)), esc_url(get_permalink($post_ID))),
            10 => sprintf(__('Video draft saved. <a target="_blank" href="%s">See video</a>', 'video_central'), esc_url(add_query_arg('preview', 'true', get_permalink($post_ID)))),

            101 => __('Please select a source', 'video_central'),

        );

        return $messages;
    }

    /**
     * On video import page load, perform actions.
     *
     * @since 1.0.0
     */
    public function import_onload()
    {
    }

    /**
     * Import a single video based on the passed data.
     */
    protected function import_video($args = array())
    {

        // Add Remove sample permalink filter
        remove_filter('post_type_link', 'video_central_filter_sample_permalink', 10, 4);

        $defaults = array(
            'video' => array(), // video details retrieved from YouTube
            'category' => false, // category name (if any) - will be created if category_id is false
            'post_type' => false, // what post type to import as
            'taxonomy' => false, // what taxonomy should be used
            'user' => false, // save as a given user if any
            'post_format' => 'video', // post format will default to video
            'status' => 'draft', // post status
            'theme_import' => false,
        );
        extract(wp_parse_args($args, $defaults), EXTR_SKIP);

        // if no video details or post type, bail out
        if (!$video || !$post_type) {
            return false;
        }

        /*
         * Filter that allows video imports. Can be used to prevent importing of
         * videos.
         *
         * @param $video - video details array
         * @param $post_type - post type that should be created from the video details
         * @param $theme_import - if video should be imported as theme compatible post, holds theme details array
         */
        $allow_import = apply_filters('video_central_allow_video_import', true, $video, $post_type, $theme_import);
        if (!$allow_import) {
            return false;
        }

        /*
         * Import category if not set to an existing one
         */
        if (!$category && video_central_import_categories() && !empty($video['category'])) {
            $cat = term_exists($video['category'], $taxonomy);
            // if not existing, create it
            if (0 === $cat || null === $cat) {
                $cat = wp_insert_term($video['category'], $taxonomy);
            }
            // set category to newly inserted term
            if (isset($cat['term_id'])) {
                $category = $cat['term_id'];
            }
        }

        /*
         * Filter on video description
         *
         * @param string - video description
         * @param bool - import description value as set by the user in plugin settings
         *
         */

        $description = isset($video['description']) ? $video['description'] : '';

        $description = apply_filters('video_central_import_video_description', $description, video_central_import_video_description());

        // post content
        $post_content = '';
        if (('content' == video_central_import_description_key() || 'content_excerpt' == video_central_import_description_key()) && video_central_import_video_description()) {
            $post_content = $description;
        }

        // post excerpt
        $post_excerpt = '';
        if (('excerpt' == video_central_import_description_key() || 'content_excerpt' == video_central_import_description_key()) && video_central_import_video_description()) {
            $post_excerpt = $description;
        }

        // post title
        $video['title'] = apply_filters('video_central_import_video_title', $video['title'], video_central_import_title());
        $post_title = video_central_import_title() ? $video['title'] : '';

        // action on post insert that allows setting of different meta on post
        do_action('video_central_before_post_insert', $video, $theme_import);

        // set post data
        $post_data = array(
            /*
             * Filter on post title
             *
             * @param string - the post title
             * @param array - the video details
             * @param bool/array - false if not imported as theme, array if imported as theme and theme is active
             */
            'post_title' => apply_filters('video_central_video_post_title', $post_title, $video, $theme_import),
            /*
             * Filter on post content
             *
             * @param string - the post content
             * @param array - the video details
             * @param bool/array - false if not imported as theme, array if imported as theme and theme is active
             */
            'post_content' => apply_filters('video_central_video_post_content', $post_content, $video, $theme_import),
            /*
             * Filter on post excerpt
             *
             * @param string - the post excerpt
             * @param array - the video details
             * @param bool/array - false if not imported as theme, array if imported as theme and theme is active
             */
            'post_excerpt' => apply_filters('video_central_video_post_excerpt', $post_excerpt, $video, $theme_import),
            'post_type' => $post_type,
            'post_status' => apply_filters('video_central_video_post_status', $status, $video, $theme_import),
        );

        $pd = video_central_import_video_date() ? date('Y-m-d H:i:s', strtotime($video['published'])) : current_time('mysql');
        /*
         * Filter on post date
         *
         * @param string - the post date
         * @param array - the video details
         * @param bool/array - false if not imported as theme, array if imported as theme and theme is active
         */
        $post_date = apply_filters('video_central_video_post_date', $pd, $video, $theme_import);

        if (isset($options['import_date']) && $options['import_date']) {
            $post_data['post_date_gmt'] = $post_date;
            $post_data['edit_date'] = $post_date;
            $post_data['post_date'] = $post_date;
        }

        // set user
        if ($user) {
            $post_data['post_author'] = $user;
        }

        $post_id = wp_insert_post($post_data, true);

        // set post format
        if ($post_format) {
            set_post_format($post_id, $post_format);
        }

        // check if post was created
        if (!is_wp_error($post_id)) {

            // set post category
            if ($category) {
                wp_set_post_terms($post_id, array($category), $taxonomy);
            }

            // action on post insert that allows setting of different meta on post
            do_action('video_central_post_insert', $post_id, $video, $theme_import, $post_type);

            // if importing as theme post, there might be some meta fields to be set
            if ($theme_import) {

                // video URL
                if( $video['source'] == 'youtube') {
                    $video_url = 'https://www.youtube.com/watch?v='.$video['video_id'];
                } elseif( $video['source'] == 'vimeo' ) {
                    $video_url = 'https://www.vimeo.com/'.$video['video_id'];
                }

                // video thumbnail
                $thumb = end($video['thumbnails']);
                $thumbnail = $thumb['url'];

                if (isset($options['image_size']) && isset($video['thumbnails'][ $options['image_size'] ]['url'])) {
                    $thumbnail = $video['thumbnails'][ $options['image_size'] ]['url'];
                }
                // video embed
                $ps = video_central_get_player_settings();

                $customize = implode('&', array(
                    'controls='.$ps['controls'],
                    'autohide='.$ps['autohide'],
                    'fs='.$ps['fs'],
                    'theme='.$ps['theme'],
                    'color='.$ps['color'],
                    'iv_load_policy='.$ps['iv_load_policy'],
                    'modestbranding='.$ps['modestbranding'],
                    'rel='.$ps['rel'],
                    'showinfo='.$ps['showinfo'],
                    'autoplay='.$ps['autoplay'],
                ));

                $embed_code = '<iframe width="'.$ps['width'].'" height="'.video_central_player_height($ps['aspect_ratio'], $ps['width']).'" src="https://www.youtube.com/embed/'.$video['video_id'].'?'.$customize.'" frameborder="0" allowfullscreen></iframe>';

                foreach ($theme_import['post_meta'] as $k => $meta_key) {
                    switch ($k) {
                        case 'url' :
                            update_post_meta($post_id, $meta_key, $video_url);
                        break;
                        case 'thumbnail':
                            update_post_meta($post_id, $meta_key, $thumbnail);
                        break;
                        case 'embed':
                            update_post_meta($post_id, $meta_key, $embed_code);
                        break;
                    }
                }
            }

            //set source first before image import
            update_post_meta($post_id, '_video_central_source', $video['source']);

              // set video URL; most likely it will be needed by other plugins
            if ($video['source'] == 'youtube') {

                   // set video ID meta to identify the video as imported
                   update_post_meta($post_id, '_video_central_video_id', $video['video_id']);
                   update_post_meta($post_id, '_video_central_video_url', 'https://www.youtube.com/watch?v='.$video['video_id']);
                   $this->import_featured_image($post_id);

            } elseif ($video['source'] == 'vimeo') {

                // set video ID meta to identify the video as imported
                update_post_meta($post_id, '_video_central_video_id', $video['video_id']);
                update_post_meta($post_id, '_video_central_video_url', 'https://www.youtube.com/watch?v='.$video['video_id']);
                $this->import_featured_image($post_id);

            } else {

                update_post_meta($post_id, '_video_central_video_url', $video['video_url']);

                if (!empty($video['poster'])) {
                    $this->upload_featured_image($post_id, $video['poster']);
                }

            }

            if (video_central_import_description_key() == '_video_central_description' && video_central_import_video_description()) {
                update_post_meta($post_id, '_video_central_description', $description);
            }

            // if imported as regular post, flag it as video
            if (!$theme_import && video_central_import_as_post()) {
                // flag post as video post
                update_post_meta($post_id, '_video_central_is_video', true);
            }

            update_post_meta($post_id, '_video_central_video_duration', $video['duration']);
            update_post_meta($post_id, '_video_central_remote_status', $video['privacy']['status']);

            // store the video data for later use
            update_post_meta($post_id, '_video_central_video_data', $video);

            return true;

        }// end checking if not wp error on post insert

        return false;
    }

    /**
     * Import Featured Image.
     *
     * @since 1.2.0
     */
    public function import_featured_image($post_id)
    {
        video_central_get_thumbnail($post_id);
    }

    /**
     * Upload and Import Featured Image.
     *
     * @since 1.2.1
     */
    public function upload_featured_image($post_id, $image_url)
    {
        Video_Central_Import_Thumbnails::save_to_media_library($image_url, $post_id);
    }

    /**
     * When trying to insert an empty post, WP is running a filter. Given the fact that
     * users are allowed to insert empty posts when importing, the filter will return
     * false on maybe_empty to allow insertion of video.
     *
     * Filter is activated inside function import_videos()
     *
     * @since 1.0.0
     *
     * @param bool  $maybe_empty
     * @param array $postarr
     */
    public function force_empty_insert($maybe_empty, $postarr)
    {
        if ($this->post_type == $postarr['post_type']) {
            return false;
        }
    }

    /**
     * Ajax response to video import action.
     *
     * @since 1.0.0
     */
    public function ajax_track_video_import()
    {

        // import videos
        $response = array(
            'success' => false,
            'error' => false,
        );

        if (isset($_POST['video_central_import_nonce'])) {
            if (check_admin_referer('video-central-import-videos-to-wp', 'video_central_import_nonce')) {
                if ('import' == $_REQUEST['action_top'] || 'import' == $_REQUEST['action2']) {
                    $this->result = $this->import_videos();

                    echo json_encode($this->result);

                    if ($this->result) {
                        $response['success'] = sprintf(
                            __('Out of %d videos, %d were successfully imported and %d were skipped.', 'video_central'),
                            $this->result['total'],
                            $this->result['imported'],
                            $this->result['skipped']
                        );
                    } else {
                        $response['error'] = __('No videos selected for importing. Please select some videos by checking the checkboxes next to video title.', 'video_central');
                    }
                } else {
                    $response['error'] = __('Please select an action.', 'video_central');
                }
            } else {
                $response['error'] = __("Cheatin' uh?", 'video_central');
            }
        } else {
            $response['error'] = __("Cheatin' uh?", 'video_central');
        }

        echo json_encode($response);

        wp_die();
    }

    /**
     * Log video import progress.
     *
     * @since 1.0.0
     */
    public function ajax_get_import_progress()
    {
        $result = get_transient('video_central_import_progress');

        echo json_encode($result);

        wp_die();
    }

    /**
     * Check video import progress.
     *
     * @since 1.0.0
     */
    public function ajax_check_remote_status()
    {
        wp_die();
    }

    /**
     * Check remote video status.
     *
     * @since 1.0.0
     */
    public function check_remote_status($url)
    {
        $headers = get_headers($url);

        if (strpos($headers[0], '200') === false) {
            $exists = false;
        } else {
            $exists = true;
        }

        return $exists;
    }

    /**
     * Return post type.
     *
     * @since 1.0.0
     */
    public function get_post_type()
    {
        return $this->post_type;
    }

    /**
     * Return taxonomy.
     *
     * @since 1.0.0
     */
    public function get_post_tax()
    {
        return $this->taxonomy;
    }
}
