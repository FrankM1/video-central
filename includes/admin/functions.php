<?php

/**
 * Video Central Admin Functions.
 */

/**
 * Filter sample permalinks so that certain languages display properly.
 *
 * @since  1.0.0
 *
 * @param string $post_link Custom post type permalink
 * @param object $_post     Post data object
 * @param bool   $leavename Optional, defaults to false. Whether to keep post name or page name.
 * @param bool   $sample    Optional, defaults to false. Is it a sample permalink.
 *
 * @uses is_admin() To make sure we're on an admin page
 * @uses video_central_is_custom_post_type() To get the video post type
 *
 * @return string The custom post type permalink
 */
function video_central_filter_sample_permalink($post_link, $_post, $leavename = false, $sample = false)
{

    // Bail if not on an admin page and not getting a sample permalink
    if (!empty($sample) && is_admin() && video_central_is_custom_post_type()) {
        return urldecode($post_link);
    }

    // Return post link
    return $post_link;
}

/**
 * Redirect user to Video Central's What's New page on activation.
 *
 * @since 1.0.0
 *
 * @internal Used internally to redirect Video Central to the about page on activation
 *
 * @uses get_transient() To see if transient to redirect exists
 * @uses delete_transient() To delete the transient if it exists
 * @uses is_network_admin() To bail if being network activated
 * @uses wp_safe_redirect() To redirect
 * @uses add_query_arg() To help build the URL to redirect to
 * @uses admin_url() To get the admin URL to index.php
 *
 * @return If no transient, or in network admin, or is bulk activation
 */
function video_central_do_activation_redirect()
{

    // Bail if no activation redirect
    if (!get_transient('_video_central_activation_redirect')) {
        return;
    }

    // Delete the redirect transient
    delete_transient('_video_central_activation_redirect');

    // Bail if activating from network, or bulk
    if (is_network_admin() || isset($_GET['activate-multi'])) {
        return;
    }

    // Bail if the current user cannot see the about page
    if (!current_user_can('video_central_about_page')) {
        return;
    }

    // Redirect to Video Central about page
    wp_safe_redirect(add_query_arg(array('page' => 'video-central-about'), admin_url('index.php')));
}
