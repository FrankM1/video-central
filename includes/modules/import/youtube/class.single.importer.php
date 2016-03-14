<?php
/**
 *  Youtube Importer class for Video Central.
 *
 * @since 1.0.0
 *
 * @author  Franklin M Gitonga
 */
class Video_Central_Single_Youtube_Importer extends Video_Central_Video_Importer {

    /**
     * On video import page load, perform actions.
     *
     * @since 1.0.0
     */
    public function import_onload() {

        // search videos result
        if (isset($_GET['video_central_search_nonce'])) {
            if (check_admin_referer('video-central-import', 'video_central_search_nonce')) {
                $screen = get_current_screen();

                video_central()->admin->video_central_list_table = new Video_Central_Youtube_Importer_ListTable(array('screen' => $screen->id));
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
    public function import_videos() {

        $this->post_type = video_central_get_video_post_type();

        if (!isset($_POST['video_central_import']) || !$_POST['video_central_import']) {
            return false;
        }

        // check if importing for theme
        $theme_import = false;
        if (isset($_POST['video_central_theme_import'])) {
            $theme_import = video_central_check_theme_support();
        }

        // set post type and taxonomy
        if ($theme_import) {
            $this->post_type = $theme_import['post_type']; // set post type
            $taxonomy = (!$theme_import['taxonomy'] && 'post' == $theme_import['post_type']) ?
                        'category' : // if taxonomy is false and is post type post, set it to category
                        $theme_import['taxonomy'];
            $post_format = isset($theme_import['post_format']) && $theme_import['post_format'] ?
                            $theme_import['post_format'] :
                            'video';
        } else {

            // should imports be made as regular posts?
            $as_post = video_central_import_as_post();
            $this->post_type = $as_post ? 'post' : $this->post_type;
            $taxonomy = $as_post ? 'category' : $this->taxonomy;
            $post_format = 'video';
        }

        // prepare array of video IDs
        $video_ids = array_reverse((array) $_POST['video_central_import']);

        $total_videos = count($video_ids);

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

        //set category
        if (isset($_REQUEST['cat_top']) && 'import' == $_REQUEST['action_top']) {
            $category = $_REQUEST['cat_top'];
        } elseif (isset($_REQUEST['cat2']) && 'import' == $_REQUEST['action2']) {
            $category = $_REQUEST['cat2'];
        }

        if (-1 == $category || 0 == $category) {
            $category = false;
        }

        // set user
        $user = false;
        if (isset($_REQUEST['user_top']) && $_REQUEST['user_top']) {
            $user = (int) $_REQUEST['user_top'];
        } elseif (isset($_REQUEST['user2']) && $_REQUEST['user2']) {
            $user = (int) $_REQUEST['user2'];
        }

        if ($user) {
            $user_data = get_userdata($user);
            $user = !$user_data ? false : $user_data->ID;
        }

        $counter = 1;

        $videos = video_central_youtube_api_get_videos($video_ids);

        if (is_wp_error($videos)) {
            return $videos;
        }

        foreach ($videos as $video) :

            // search if video already exists
            $posts = get_posts(array(
                'post_type' => $this->post_type,
                'meta_key' => '_video_central_video_id',
                'meta_value' => $video['video_id'],
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

        if (isset($_POST['video_central_source'])) {
            $video['source'] = $_POST['video_central_source'];
        }

        $video_id = $video['video_id'];

        if (isset($_POST['video_cental_title'][ $video_id ])) {
            $video['title'] = $_POST['video_cental_title'][ $video_id ];
        }

        if (isset($_POST['video_cental_text'][ $video_id ])) {
            $video['description'] = $_POST['video_cental_text'][ $video_id ];
        }

        $this->import_video(array(
                'video' => $video, // video details retrieved from YouTube
                'category' => $category, // category name (if any); if false, it will create categories from YouTube
                'post_type' => $this->post_type, // what post type to import as
                'taxonomy' => $taxonomy, // what taxonomy should be used
                'user' => $user, // save as a given user if any
                'post_format' => $post_format, // post format will default to video
                'status' => $status, // post status
                'theme_import' => $theme_import, // to check in callbacks if importing as theme post
            ));

        $this->result['imported'] += 1;

        endforeach;

        return $this->result;
    }
}
