<?php

/**
 *  Vimeo Importer class for Video Central.
 *
 * @since 1.0.0
 *
 * @author  Franklin M Gitonga
 */
class Video_Central_Vimeo_Importer
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
        add_action('wp_ajax_video_central_query_vimeo_video', array(&$this, 'ajax_video_query'));

        //add on load event
        add_action('load-video_page_video_central_import', array(&$this, 'import_onload'));
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

        // search videos result
        if (isset($_GET['video_central_search_nonce'])) {
            if (check_admin_referer('video-central-import', 'video_central_search_nonce')) {
                $screen = get_current_screen();

                video_central()->admin->video_central_list_table = new Video_Central_Vimeo_Importer_ListTable(array('screen' => $screen->id));
            }
        }

        // import videos / alternative to AJAX import
        if (isset($_REQUEST['video_central_import_nonce'])) {
            if (check_admin_referer('video-central-import-videos-to-wp', 'video_central_import_nonce')) {
                if ('import' == $_REQUEST['action_top'] || 'import' == $_REQUEST['action2']) {
                    $this->import_videos();
                }

                wp_redirect('edit.php?post_status='.video_central_import_status().'&post_type='.$this->post_type);

                wp_die();
            }
        }
    }

    /**
     * Import videos to WordPress.
     *
     * @since 1.0.0
     */
    private function import_videos()
    {
        if (!isset($_POST['video_central_import'])) {
            return false;
        }

        $videos = (array) $_POST['video_central_import'];

        $total_videos = count($videos);

        $this->result = array(
            'imported' => 0,
            'skipped' => 0,
            'total' => $total_videos,
        );

        $import_progress = array(
            'current' => 0,
            'total' => $total_videos,
         );

        $statuses = array('publish', 'draft', 'pending');
        $status = in_array(video_central_import_status(), $statuses) ? video_central_import_status() : 'draft';

        $category = false;

        if (isset($_REQUEST['cat_top']) && 'import' == $_REQUEST['action_top']) {
            $category = $_REQUEST['cat_top'];
        } elseif (isset($_REQUEST['cat2']) && 'import' == $_REQUEST['action2']) {
            $category = $_REQUEST['cat2'];
        }

        if (-1 == $category || 0 == $category) {
            $category = false;
        }

        $counter = 1;

        foreach ($videos as $video_id) :

            // search if video already exists
            $posts = get_posts(array(
                'post_type' => $this->post_type,
                'meta_key' => '_video_central_video_id',
                'meta_value' => $video_id,
                'post_status' => array('publish', 'pending', 'draft', 'future', 'private'),
            ));

        $import_progress['current'] = $counter++;

            //log progress
            set_transient('video_central_import_progress', $import_progress, 60 * 5);

            // video already exists, don't do anything
            if ($posts) {
                $this->result['skipped'] += 1;
                continue;
            }

            // get video details
            $request = video_central_query_vimeo_video($video_id);

        if (!empty($request) && is_array($request)) {
            if (200 == $request['response']['code']) {
                $data = json_decode($request['body'], true);
                $video = video_central_format_vimeo_video_entry($data['data']);

                if (video_central_import_categories() && !$category) {

                        // check if category exists
                        $term = term_exists($video['category'], $this->taxonomy);

                    if (0 === $term || null === $term) {

                            // create the category
                            $term = wp_insert_term($video['category'], $this->taxonomy);
                    }
                }

                $post_content = $post_excerpt = '';

                if ('content' == video_central_import_description_key() || 'content_excerpt' == video_central_import_description_key()) {
                    $post_content = $video['description'];
                }

                if ('excerpt' == video_central_import_description_key() || 'content_excerpt' == video_central_import_description_key()) {
                    $post_excerpt = $video['description'];
                }

                    // insert the post
                    $post_data = array(
                        'post_title' => video_central_import_title() ? $video['title'] : '',
                        'post_content' => $post_content,
                        'post_excerpt' => $post_excerpt,
                        'post_type' => $this->post_type,
                        'post_status' => $status,
                    );

                $post_id = wp_insert_post($post_data, true);

                    // check if post was created
                    if (!is_wp_error($post_id)) {
                        $this->result['imported'] += 1;

                        if ($category) {
                            wp_set_post_terms($post_id, array($category), $this->taxonomy);
                        } elseif (video_central_import_categories()) {

                           // add category to video
                           wp_set_post_terms($post_id, array($term['term_id']), $this->taxonomy);
                        }

                        if (video_central_import_description_key() == '_video_central_description') {
                            update_post_meta($post_id, '_video_central_description', $video['description']);
                        }

                        // set some meta on video post
                        unset($video['title']);
                        unset($video['description']);

                        update_post_meta($post_id, '_video_central_video_id', $video['video_id']);
                        update_post_meta($post_id, '_video_central_video_data', $video);
                        update_post_meta($post_id, '_video_central_video_duration', $video['duration']);
                        update_post_meta($post_id, '_video_central_source', $_POST['video_central_source']);

                        video_central_get_thumbnail($post_id);
                    } //check error
            } // check response ode
        } //check array

        endforeach;

        return $this->result;
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

video_central()->video_central_vimeo_importer = new Video_Central_Vimeo_Importer();
