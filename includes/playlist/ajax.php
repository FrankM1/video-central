<?php

/**
 * AJAX callbacks for privileged users.
 *
 * @copyright Copyright (c) 2015, RadiumThemes
 * @license GPL-2.0+
 *
 * @since 1.2.2
 */

/**
 * AJAX callback to retrieve a playlist's tracks.
 *
 * @since 1.2.2
 */
function video_central_ajax_get_playlist()
{
    wp_send_json_success( video_central_get_laylist_video_ids($_POST['post_id'], 'edit'));
}

/**
 * AJAX callback to save a playlist's video ids.
 *
 * Tracks are currently saved to post meta.
 *
 * @since 1.2.2
 */
function video_central_ajax_save_playlist_video_ids()
{
    $post_id = absint($_POST['post_id']);

    check_ajax_referer('save-playlist-video-ids_'.$post_id, 'nonce');

    if (!current_user_can('edit_post', $post_id)) {
        wp_send_json_error();
    }

    // Sanitize the list of tracks.
    $tracks = empty($_POST['tracks']) ? array() : stripslashes_deep($_POST['tracks']);
    foreach ((array) $tracks as $key => $track) {
        if (empty($track)) {
            unset($tracks[ $key ]);
            continue;
        }

        $tracks[ $key ] = sanitize_cue_track($track, 'save');
    }

    // Save the list of tracks to post meta.
    update_post_meta($post_id, 'tracks', $tracks);

    // Response data.
    $data = array(
        'nonce' => wp_create_nonce('save-playlist-video-ids_'.$post_id),
    );

    // Send the response.
    wp_send_json_success($data);
}

/**
 * Parse the Cue shortcode for display within a TinyMCE view.
 *
 * @since 1.3.0
 */
function video_central_ajax_parse_shortcode()
{
    global $wp_scripts;

    if (empty($_POST['shortcode'])) {
        wp_send_json_error();
    }

    $shortcode = do_shortcode(wp_unslash($_POST['shortcode']));

    if (empty($shortcode)) {
        wp_send_json_error(array(
            'type' => 'no-items',
            'message' => __('No items found.', 'video_central'),
        ));
    }

    $head = '';

    /* $styles = wpview_media_sandbox_styles();

    foreach ($styles as $style) {
        $head .= '<link type="text/css" rel="stylesheet" href="'.$style.'">';
    } */

    $head .= '<link rel="stylesheet" href="'. Video_Central::get_url() .'/assets/frontend/css/playlist.css'.'">';

    if (!empty($wp_scripts)) {
        $wp_scripts->done = array();
    }

    ob_start();
    echo $shortcode;
    wp_print_scripts('video-central-playlist-admin');

    wp_send_json_success(array(
        'head' => $head,
        'body' => ob_get_clean(),
    ));
}
