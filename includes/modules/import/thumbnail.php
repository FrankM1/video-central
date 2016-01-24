<?php
/**
 * Video Central Images Importer
 *
 */
class Video_Central_Import_Thumbnails
{
    /**
     * [$providers description].
     *
     * @var array
     */
    public $providers = array();

    /**
     * [$settings description].
     *
     * @var [type]
     */
    public $settings;

    /**
     * [$thumbnails_field description].
     *
     * @var string
     */
    public $thumbnails_field = '_video_thumbnail';

    /**
     * [$post_types description].
     *
     * @var string
     */
    public $post_types;

    /**
     *  __construct.
     */
    public function __construct()
    {
        $this->post_types = video_central_get_video_post_type();

        $this->custom_field = '_video_central_video_id';

        $this->settings->options['save_media'] = true;

        $this->settings->options['set_featured'] = true;

        // Create provider array
        $this->providers = apply_filters('video_central_thumbnail_providers', $this->providers);

        // Initialize meta box
        add_action('admin_init', array(&$this, 'meta_box_init'));

        // Add actions to save video thumbnails when saving
        add_action('save_post', array(&$this, 'save_video_thumbnail'), 100, 1);

        // Add action for Ajax reset script on edit pages
        if (in_array(basename($_SERVER['PHP_SELF']), apply_filters('video_central_thumbnails_editor_pages', array('post-new.php', 'page-new.php', 'post.php', 'page.php')))) {
            add_action('admin_head', array(&$this, 'ajax_reset_script'));
        }

        // Add action for Ajax reset callback
        add_action('wp_ajax_vide_central_reset_video_thumbnail', array(&$this, 'ajax_reset_callback'));

        // Get the posts to be scanned in bulk
        add_action('wp_ajax_video_thumbnails_bulk_posts_query', array(&$this, 'bulk_posts_query_callback'));

        // Get the thumbnail for an individual post
        add_action('wp_ajax_video_thumbnails_get_thumbnail_for_post', array(&$this, 'get_thumbnail_for_post_callback'));
    }

    // Initialize meta box on edit page
    public function meta_box_init()
    {
        add_meta_box('video_thumbnail', 'Video Thumbnail', array(&$this, 'meta_box'), 'video', 'side', 'low');
    }

    // Construct the meta box
    public function meta_box()
    {
        global $post;

        $custom_meta_data = get_post_custom($post->ID);

        if (isset($custom_meta_data[$this->thumbnails_field][0])) {
            $video_thumbnail = $custom_meta_data[$this->thumbnails_field][0];
        }

        if (isset($video_thumbnail) && $video_thumbnail != '') {
            echo '<p id="video-thumbnails-preview"><img src="'.$video_thumbnail.'" style="max-width:100%;" /></p>';
        }

        if (get_post_status() == 'publish' || get_post_status() == 'private') {
            if (isset($video_thumbnail) && $video_thumbnail != '') {
                echo '<p><a href="#" id="video-thumbnails-reset" onclick="video_thumbnails_reset(\''.$post->ID.'\' );return false;">Reset Video Thumbnail</a></p>';
            } else {
                echo '<p id="video-thumbnails-preview">No video thumbnail for this post.</p>';
                echo '<p><a href="#" id="video-thumbnails-reset" onclick="video_thumbnails_reset(\''.$post->ID.'\' );return false;">Search Again</a></p>';
            }
        } else {
            if (isset($video_thumbnail) && $video_thumbnail != '') {
                echo '<p><a href="#" id="video-thumbnails-reset" onclick="video_thumbnails_reset(\''.$post->ID.'\' );return false;">Reset Video Thumbnail</a></p>';
            } else {
                echo '<p>A video thumbnail will be add automatically to this post when it is published.</p>';
            }
        }
    }

    /**
     * A usort() callback that sorts videos by offset.
     */
    public function compare_by_offset($a, $b)
    {
        return $a['offset'] - $b['offset'];
    }

    /**
     * Find all the videos in a post.
     *
     * @param string $markup Markup to scan for videos
     *
     * @return array An array of video information
     */
    public function find_videos($markup, $video_id = null)
    {
        $videos = array();

        // Filter to modify providers immediately before scanning
        $providers = apply_filters('video_central_thumbnail_providers_pre_scan', $this->providers);

        foreach ($providers as $key => $provider) {

            $provider_videos = $provider->scan_for_videos($markup, $video_id);

            if (empty($provider_videos)) {
                continue;
            }

            foreach ($provider_videos as $video) {
                $videos[] = array(
                    'id' => $video[0],
                    'provider' => $key,
                    'offset' => $video[1],
                );
            }
        }

        usort($videos, array(&$this, 'compare_by_offset'));

        return $videos;
    }

    /**
     * Finds the first video in markup and retrieves a thumbnail.
     *
     * @param string $markup Post markup to scan
     *
     * @return mixed Null if no thumbnail or a string with a remote URL
     */
    public function get_first_thumbnail_url($markup, $video_id = null, $provider = null)
    {
        $thumbnail = null;

        $videos = $this->find_videos($markup, $video_id);

        foreach ($videos as $video) {

            //override provider
            $provider = $provider ? $provider : $video['provider'];

            $thumbnail = $this->providers[$provider]->get_thumbnail_url($video['id']);

            if ($thumbnail !== null) {
                break;
            }
        }

        return $thumbnail;
    }

    // The main event
    public function get_video_thumbnail($post_id = null)
    {
        $markup = $video_id = null;

        // Get the post ID if none is provided
        if ($post_id === null || $post_id === '') {
            $post_id = get_the_ID();
        }

        // Check to see if thumbnail has already been found
        if (($thumbnail_meta = get_post_meta($post_id, $this->thumbnails_field, true)) != '') {
            return $thumbnail_meta;
        }
        // If the thumbnail isn't stored in custom meta, fetch a thumbnail
        else {

            $new_thumbnail = null;

            // Filter for extensions to set thumbnail
            $new_thumbnail = apply_filters('video_central_new_video_thumbnail_url', $new_thumbnail, $post_id);

            if ($new_thumbnail === null) {

                // Get the post or custom field to search
                if ($this->custom_field == '_video_central_video_id') {
                    $video_id = get_post_meta($post_id, $this->custom_field, true);
                    $provider = get_post_meta($post_id, '_video_central_source', true);
                } elseif ($this->custom_field) {
                    $markup = get_post_meta($post_id, $this->custom_field, true);
                } else {
                    $post_array = get_post($post_id);
                    $markup = $post_array->post_content;
                    $markup = apply_filters('the_content', $markup);
                }

                // Filter for extensions to modify what markup is scanned
                $markup = apply_filters('video_central_thumbnail_markup', $markup, $post_id);
                $video_id = apply_filters('video_central_thumbnail_video_id', $video_id, $post_id);

                $new_thumbnail = $this->get_first_thumbnail_url(null, $video_id, $provider);

            }

            // Return the new thumbnail variable and update meta if one is found
            if ( $new_thumbnail !== null && !is_wp_error($new_thumbnail)) {

                // Save as Attachment if enabled
                if ($this->settings->options['save_media'] == 1) {
                    $attachment_id = $this->save_to_media_library($new_thumbnail, $post_id);
                    $new_thumbnail = wp_get_attachment_image_src($attachment_id, 'full');
                    $new_thumbnail = $new_thumbnail[0];
                }

                // Add hidden custom field with thumbnail URL
                if (!update_post_meta($post_id, $this->thumbnails_field, $new_thumbnail)) {
                    add_post_meta($post_id, $this->thumbnails_field, $new_thumbnail, true);
                }

                // Set attachment as featured image if enabled
                if ($this->settings->options['set_featured'] == 1 && $this->settings->options['save_media'] == 1) {

                    // Make sure there isn't already a post thumbnail
                    if (!ctype_digit(get_post_thumbnail_id($post_id))) {
                        set_post_thumbnail($post_id, $attachment_id);
                    }
                }
            }

            return $new_thumbnail;
        }
    }

    /**
     * Gets a video thumbnail when a published post is saved.
     *
     * @param int $post_id The post ID
     */
    public function save_video_thumbnail($post_id)
    {

        // Don't save video thumbnails during autosave or for unpublished posts
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (get_post_status($post_id) != 'publish') {
            return;
        }

        // Check that Video Thumbnails are enabled for current post type
        $post_type = get_post_type($post_id);

        if (in_array($post_type, (array) $this->post_types) || $post_type == $this->post_types) {
            $this->get_video_thumbnail($post_id);
        } else {
            return;
        }
    }

    /**
     * Creates a file name for use when saving an image to the media library.
     * It will either use a sanitized version of the title or the post ID.
     *
     * @param int $post_id The ID of the post to create the filename for
     *
     * @return string A filename (without the extension)
     */
    public static function construct_filename($post_id)
    {
        $filename = get_the_title($post_id);
        $filename = sanitize_title($filename, $post_id);
        $filename = urldecode($filename);
        $filename = preg_replace('/[^a-zA-Z0-9\-]/', '', $filename);
        $filename = substr($filename, 0, 32);
        $filename = trim($filename, '-');
        if ($filename == '') {
            $filename = (string) $post_id;
        }

        return $filename;
    }

    // Saves to media library
    public static function save_to_media_library($image_url, $post_id)
    {
        $error = '';
        $response = wp_remote_get($image_url, array('sslverify' => false, 'timeout' => 30));

        if (is_wp_error($response)) {
            $error = new WP_Error('thumbnail_retrieval', __('Error retrieving a thumbnail from the URL <a href="'.$image_url.'">'.$image_url.'</a> using <code>wp_remote_get()</code><br />If opening that URL in your web browser returns anything else than an error page, the problem may be related to your web server and might be something your host administrator can solve.<br />Details: '.$response->get_error_message(), 'video_central'));
        } else {
            $image_contents = $response['body'];
            $image_type = wp_remote_retrieve_header($response, 'content-type');
        }

        if ($error != '') {
            return $error;
        } else {

            // Translate MIME type into an extension
            if ($image_type == 'image/jpeg') {
                $image_extension = '.jpg';
            } elseif ($image_type == 'image/png') {
                $image_extension = '.png';
            } elseif ($image_type == 'image/gif') {
                $image_extension = '.gif';
            } else {
                return new WP_Error('thumbnail_upload', __('Unsupported MIME type:', 'video_central').' '.$image_type);
            }

            // Construct a file name with extension
            $new_filename = self::construct_filename($post_id).$image_extension;

            // Save the image bits using the new filename
            do_action('video_thumbnails/pre_upload_bits', $image_contents);
            $upload = wp_upload_bits($new_filename, null, $image_contents);
            do_action('video_thumbnails/after_upload_bits', $upload);

            // Stop for any errors while saving the data or else continue adding the image to the media library
            if ($upload['error']) {
                $error = new WP_Error('thumbnail_upload', __('Error uploading image data:', 'video_central').' '.$upload['error']);

                return $error;
            } else {
                do_action('video_thumbnails/image_downloaded', $upload['file']);

                $image_url = $upload['url'];

                $filename = $upload['file'];

                $wp_filetype = wp_check_filetype(basename($filename), null);

                $attachment = array(
                    'post_mime_type' => $wp_filetype['type'],
                    'post_title' => get_the_title($post_id),
                    'post_content' => '',
                    'post_status' => 'inherit',
                );
                $attach_id = wp_insert_attachment($attachment, $filename, $post_id);

                // you must first include the image.php file
                // for the function wp_generate_attachment_metadata() to work
                require_once ABSPATH.'wp-admin/includes/image.php';
                $attach_data = wp_generate_attachment_metadata($attach_id, $filename);
                wp_update_attachment_metadata($attach_id, $attach_data);

                // Add field to mark image as a video thumbnail
                update_post_meta($attach_id, 'video_thumbnail', '1');
            }
        }

        return $attach_id;
    } // End of save to media library function

    // Post editor Ajax reset script
    public function ajax_reset_script()
    {
        echo '<!-- Video Thumbnails Ajax Search -->'.PHP_EOL;
        echo '<script type="text/javascript">'.PHP_EOL;
        echo 'function video_thumbnails_reset(id) {'.PHP_EOL;
        echo '  var data = {'.PHP_EOL;
        echo '    action: "vide_central_reset_video_thumbnail",'.PHP_EOL;
        echo '    post_id: id'.PHP_EOL;
        echo '  };'.PHP_EOL;
        echo '  document.getElementById(\'video-thumbnails-preview\').innerHTML=\'Working... <img src="'.home_url('wp-admin/images/loading.gif').'"/>\';'.PHP_EOL;
        echo '  jQuery.post(ajaxurl, data, function(response){'.PHP_EOL;
        echo '    document.getElementById(\'video-thumbnails-preview\').innerHTML=response;'.PHP_EOL;

        //echo 'console.log(response);' . PHP_EOL;

        echo '  });'.PHP_EOL;
        echo '};'.PHP_EOL;
        echo '</script>'.PHP_EOL;
    }

    // Ajax reset callback
    public function ajax_reset_callback()
    {

        $post_id = $_POST['post_id'];

        delete_post_meta($post_id, $this->thumbnails_field);

        $video_thumbnail = video_central_get_thumbnail($post_id);

        if (is_wp_error($video_thumbnail)) {
            echo $video_thumbnail->get_error_message();
        } elseif ($video_thumbnail !== null) {
            echo '<img src="'.$video_thumbnail.'" style="max-width:100%;" />';
        } else {
            _e('No video thumbnail for this post.', 'video_central');
        }

        wp_die();
    }

    //Bulk query posts
    public function bulk_posts_query_callback()
    {

        // Some default args
        $args = array(
            'posts_per_page' => -1,
            'post_type' => $this->post_types,
            'fields' => 'ids',
        );

        // Setup an array for any form data and parse the jQuery serialized data
        $form_data = array();

        parse_str($_POST['params'], $form_data);

        $args = apply_filters('video_thumbnails/bulk_posts_query', $args, $form_data);

        $query = new WP_Query($args);

        echo json_encode($query->posts);

        wp_die();
    }

    public function get_thumbnail_for_post_callback()
    {
        $post_id = $_POST['post_id'];

        $thumb = get_post_meta($post_id, $this->thumbnails_field, true);

        if ($thumb == '') {
            $thumb = video_central()->import_thumbnails->get_video_thumbnail($post_id);
            if ($thumb) {
                $type = 'new';
            }
        } else {
            $type = 'existing';
        }

        if ($thumb != '') {
            $result = array(
                'type' => $type,
                'url' => $thumb,
            );
        } else {
            $result = array();
        }

        echo json_encode($result);

        wp_die();
    }
}

// Get video thumbnail function
function video_central_get_thumbnail($post_id = null)
{
    return video_central()->import_thumbnails->get_video_thumbnail($post_id);
}
