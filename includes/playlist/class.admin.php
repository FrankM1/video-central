<?php
/**
 * Video Central Playlist administration.
 *
 * @copyright Copyright (c) 2015, RadiumThemes
 * @license GPL-2.0+
 *
 * @since 1.2.2
 */

/**
 * Plugin administration setup class.
 *
 * @since 1.2.2
 */
class Video_Central_Playlist_Admin
{
    /**
     * Load administration functionality.
     *
     * @since 1.2.2
     */
    public function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_edit_assets'));
        add_filter('wp_prepare_attachment_for_js', array($this, 'prepare_audio_attachment_for_js'), 20, 3);
        add_filter('wp_prepare_attachment_for_js', array($this, 'prepare_image_attachment_for_js'), 20, 3);

        add_action('add_meta_boxes_'.video_central_get_playlist_post_type(), array($this, 'load_playlist_edit_screen'));
        add_filter('post_updated_messages', array($this, 'playlist_updated_messages'));

        add_action('wp_ajax_video_central_get_playlist', 'video_central_ajax_get_playlist');
        add_action('wp_ajax_video_central_save_playlist_tracks', 'video_central_ajax_save_playlist_video_ids');
        add_action('wp_ajax_video_central_playlist_parse_shortcode', 'video_central_ajax_parse_shortcode');
        add_action('wp_ajax_video_central_get_playlist_video_list', array(&$this, 'ajax_videos'));
        add_action('wp_ajax_video_central_get_playlist_video_details', array(&$this, 'video_data'));

        add_action('admin_footer-post-new.php', array(&$this, 'add_templates'));
        add_action('admin_footer-post.php',  array(&$this, 'add_templates'));
    }

    /**
     * Set up the playlist admin.
     *
     * @since 1.2.2
     *
     * @param WP_Post $post Playlist post object.
     */
    public function load_playlist_edit_screen($post)
    {
        add_meta_box( 'videoplaylistshortcodediv', __('Shortcode', 'video_central'), array($this, 'display_playlist_shortcode_meta_box'), video_central_get_playlist_post_type(), 'side', 'default' );

        add_action('admin_enqueue_scripts', array($this, 'enqueue_playlist_edit_assets'));
        add_action('edit_form_after_title', array($this, 'display_playlist_edit_view'));
        add_action('admin_footer', array($this, 'print_playlist_edit_templates'));
    }

    /**
     * Enqueue a script for rendering the Cue shortcode in the editor.
     *
     * @since 1.2.9
     */
    public function enqueue_edit_assets($hook_suffix)
    {
        if ('post.php' !== $hook_suffix && 'post-new.php' !== $hook_suffix) {
            return;
        }

        $base = Video_Central::get_url();

        /* wp_enqueue_script('video-central-playlist-mce-c', $base.'/assets/admin/js/modal-playlist-model.js',
            array('jquery', 'underscore'),
            '1.2.2',
            true
        ); */

        wp_enqueue_script('video_central_playlist_view', $base.'/assets/admin/js/source/modal-playlist-view.js',
            array('jquery', 'backbone', 'underscore', 'wp-util'),
            '1.2.2',
            true
        );

        wp_enqueue_script('video-central-playlist-mce-view', $base.'/assets/admin/js/mce-playlist-view.js',
            array('jquery', 'mce-view', 'underscore'),
            '1.2.2',
            true
        );

        wp_enqueue_style('video-central-playlist-modal', $base.'/assets/admin/css/playlist-modal.css');
    }

    /**
     * Enqueue scripts and styles on the playlist edit screen.
     *
     * @since 1.1.0
     */
    public function enqueue_playlist_edit_assets()
    {
        $post = get_post();

        $base = Video_Central::get_url();

        wp_enqueue_style('video-central-playlist-admin', Video_Central::get_url().'/assets/admin/css/playlist.css', array('mediaelement'));

        wp_enqueue_script('video-central-playlist-models', $base.'/assets/admin/js/source/playlist-models.js',
            array('jquery', 'backbone', 'underscore', 'wp-util'),
            '1.2.2',
            true
        );

        wp_enqueue_script('video-central-playlist-sort-view', $base.'/assets/admin/js/source/playlist-sort-view.js',
            array('jquery', 'backbone', 'underscore', 'wp-util'),
            '1.2.2',
            true
        );

        wp_enqueue_script('video-central-playlist-admin', Video_Central::get_url().'/assets/admin/js/source/playlist.js',
            array('backbone', 'jquery-ui-sortable', 'media-upload', 'media-views', 'mediaelement', 'wp-util'),
            '1.2.2',
            true
        );

        wp_localize_script('video-central-playlist-admin', '_cueSettings', array(
            'tracks' => get_cue_playlist_tracks($post->ID, 'edit'),
            'settings' => array(
                'pluginPath' => includes_url('js/mediaelement/', 'relative'),
                'postId' => $post->ID,
                'saveNonce' => wp_create_nonce('save-playlist-video-ids_'.$post->ID),
            ),
            'l10n' => array(
                'addTracks' => __('Add Videos', 'video_central'),
                'addFromUrl' => __('Add from URL', 'video_central'),
                'workflows' => array(
                    'selectArtwork' => array(
                        'fileTypes' => __('Image Files', 'video_central'),
                        'frameTitle' => __('Choose an Image', 'video_central'),
                        'frameButtonText' => __('Update Image', 'video_central'),
                    ),
                    'selectAudio' => array(
                        'fileTypes' => __('Audio Files', 'video_central'),
                        'frameTitle' => __('Choose an Audio File', 'video_central'),
                        'frameButtonText' => __('Update Audio', 'video_central'),
                    ),
                    'addTracks' => array(
                        'fileTypes' => __('Audio Files', 'video_central'),
                        'frameTitle' => __('Choose Tracks', 'video_central'),
                        'frameButtonText' => __('Add Tracks', 'video_central'),
                    ),
                ),
            ),
        ));
    }

    /**
     * Display the basic starting view.
     *
     * @since 1.2.2
     *
     * @param WP_Post $post Playlist post object.
     */
    public function display_playlist_edit_view($post)
    {
        ?>
        <div id="video-central-playlist-section" class="video-central-playlist-section">
            <h3 class="video-central-playlist-section-title"><?php _e('Videos', 'video_central');
        ?></h3>
            <p><?php _e('Add videos to the playlist, then drag and drop to reorder them. Click the arrow on the right of each item to reveal more configuration options.', 'video_central');
        ?></p>
            <p id="add-videos"><a class="button button-secondary"><?php _e('Add Videos', 'video_central');
        ?></a></p>
        </div>

        <?php

    }

    /**
     * Display a meta box with instructions to embed the playlist.
     *
     * @since 1.2.2
     *
     * @param WP_Post $post Post object.
     */
    public function display_playlist_shortcode_meta_box($post)
    {
        ?>
        <p>
            <?php _e('Copy and paste the following shortcode into a post or page to embed this playlist.', 'video_central');
        ?>
        </p>
        <p>
            <input type="text" value="<?php echo esc_attr('[video-central-playlist id="'.$post->ID.'"]');
        ?>" readonly>
        </p>
        <?php

    }

    /**
     * Playlist update messages.
     *
     * @since 1.2.2
     * @see /wp-admin/edit-form-advanced.php
     *
     * @param array $messages The array of post update messages.
     *
     * @return array
     */
    public function playlist_updated_messages($messages)
    {
        global $post;

        $post_type = video_central_get_playlist_post_type();
        $post_type_object = get_post_type_object($post_type);

        $messages[ $post_type ] = array(
            0 => '', // Unused. Messages start at index 1.
            1 => __('Playlist updated.', 'video_central'),
            2 => __('Custom field updated.', 'video_central'),
            3 => __('Custom field deleted.', 'video_central'),
            4 => __('Playlist updated.', 'video_central'),
            // translators: %s: date and time of the revision
            5 => isset($_GET['revision']) ? sprintf(__('Playlist restored to revision from %s', 'video_central'),
                  wp_post_revision_title((int) $_GET['revision'], false)) : false,
            6 => __('Playlist published.', 'video_central'),
            7 => __('Playlist saved.', 'video_central'),
            8 => __('Playlist submitted.', 'video_central'),
            9 => sprintf(__('Playlist scheduled for: <strong>%1$s</strong>.', 'video_central'),
                  // translators: Publish box date format, see http://php.net/date
                  date_i18n(__('M j, Y @ G:i', 'video_central'), strtotime($post->post_date))),
            10 => __('Playlist draft updated.', 'video_central'),
        );

        if ($post_type_object->publicly_queryable) {
            $view_link = sprintf(
                ' <a href="%s">%s</a>',
                esc_url(get_permalink($post->ID)),
                __('View playlist', 'video_central')
            );
            $messages[ $post_type ][1] .= $view_link;
            $messages[ $post_type ][6] .= $view_link;
            $messages[ $post_type ][9] .= $view_link;

            $preview_link = sprintf(
                ' <a target="_blank" href="%s">%s</a>',
                esc_url(add_query_arg('preview', 'true', get_permalink($post->ID))),
                __('Preview Playlist', 'video_central')
            );
            $messages[ $post_type ][8]  .= $preview_link;
            $messages[ $post_type ][10] .= $preview_link;
        }

        return $messages;
    }

    /**
     * Include the HTML templates.
     *
     * @since 1.2.2
     */
    public function print_playlist_edit_templates()
    {
        include Video_Central::get_dir().'includes/playlist/views/playlist-edit.php';
    }

    /**
     * Dumps the contents of modal-template.php into the foot of the document.
     * WordPress itself function-wraps the script tags rather than including them directly
     * ( example: https://github.com/WordPress/WordPress/blob/master/wp-includes/media-template.php )
     * but this isn't necessary for this example.
     */
    public function add_templates()
    {
        include 'views/modal-template.php';
    }

    /**
     * Prepare an audio attachment for JavaScript.
     *
     * Filters the core method and inserts data using 'video_central' as the top level key.
     *
     * @since 1.2.2
     *
     * @param array   $response   Response data.
     * @param WP_Post $attachment Attachment object.
     * @param array   $meta       Attachment metadata.
     *
     * @return array
     */
    public function prepare_audio_attachment_for_js($response, $attachment, $meta)
    {
        if ('audio' !== $response['type']) {
            return $response;
        }

        $data = array();

        // Fall back to the attachment title if the audio meta doesn't have one.
        $data['title'] = empty($meta['title']) ? $response['title'] : $meta['title'];
        $data['artist'] = empty($meta['artist']) ? '' : $meta['artist'];
        $data['audioId'] = $attachment->ID;
        $data['audioUrl'] = $response['url'];
        $data['format'] = empty($meta['dataformat']) ? '' : $meta['dataformat'];
        $data['length'] = empty($response['fileLength']) ? '' : $response['fileLength'];
        $data['length'] = empty($data['length']) && !empty($meta['length_formatted']) ? $meta['length_formatted'] : $data['length'];

        if (has_post_thumbnail($attachment->ID)) {
            $thumbnail_id = get_post_thumbnail_id($attachment->ID);
            $size = apply_filters('cue_artwork_size', array(300, 300));
            $image = image_downsize($thumbnail_id, $size);

            $data['artworkId'] = $thumbnail_id;
            $data['artworkUrl'] = $image[0];
        }

        $response['video_central'] = $data;

        return $response;
    }

    /**
     * Prepare an image attachment for JavaScript.
     *
     * Adds an image size to use for artwork.
     *
     * @since 1.2.2
     *
     * @param array   $response   Response data.
     * @param WP_Post $attachment Attachment object.
     * @param array   $meta       Attachment metadata.
     *
     * @return array
     */
    public function prepare_image_attachment_for_js($response, $attachment, $meta)
    {
        if ('image' !== $response['type']) {
            return $response;
        }

        $size = apply_filters('cue_artwork_size', array(300, 300));
        $image = image_downsize($attachment->ID, $size);

        $response['sizes']['video_central'] = array(
            'height' => $image[2],
            'width' => $image[1],
            'url' => $image[0],
            'orientation' => $image[2] > $image[1] ? 'portrait' : 'landscape',
        );

        return $response;
    }

    public function ajax_videos_has_more()
    {
    }

    public function ajax_videos()
    {
        $page = $_GET['page'];
        $page = ($page < 1) ? 1 : $page;

        $posts_per_page = $_GET['posts_per_page'];

        $output = null;

        if (video_central_has_videos(array('posts_per_page' => $posts_per_page, 'paged' => $page))) :

        $items = '';
        $i = 0;

        $image_sizes = array(
            'width' => 300,
            'height' => 169,
        );
        $image_args['wrap'] = '<img %SRC% %IMG_CLASS% %SIZE% %ALT% %IMG_TITLE% />';

        while (video_central_videos()): video_central_the_video();

        $output .= '<li tabindex="0" role="checkbox" aria-label="'.get_the_title().'" aria-checked="false" data-id="'.video_central_get_video_id().'" class="attachment save-ready">';

        $output .= '<div class="attachment-preview js--select-attachment type-image subtype-jpeg landscape">';
        $output .= '<div class="thumbnail">';
        $output .= '<div class="centered">';
        $output .= '<img src="'.video_central_get_featured_image_url(video_central_get_video_id(), $image_sizes).'" alt="'.the_title_attribute(array('echo' => false)).' " title="'.the_title_attribute(array('echo' => false)).'">';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';

        $output .= '<button type="button" class="button-link check" tabindex="-1"><span class="media-modal-icon"></span><span class="screen-reader-text">Deselect</span></button>';

        $output .= '</li>';

        endwhile;

        endif;

        // Return the String
        wp_die(json_encode($output));
    }

    public function video_data() {

        $id = video_central_get_video_id($_GET['post_id']);

        $data =  array(
            'title' => video_central_get_video_title( $id ),
            'thumbnail' => ''
        );

        // Return the String
        wp_die(json_encode($data));

    }

}
