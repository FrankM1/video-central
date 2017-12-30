<?php

/** Settings ******************************************************************/

/**
 * Return the videos per page setting.
 *
 * @since 1.0.0
 *
 * @param int $default Default videos per page (15)
 *
 * @uses get_option() To get the setting
 * @uses apply_filters() To allow the return value to be manipulated
 *
 * @return int
 */
function video_central_get_videos_per_page($default = 16)
{

    // Get database option and cast as integer
    $retval = get_option('_video_central_videos_per_page', $default);

    // If return val is empty, set it to default
    if (empty($retval)) {
        $retval = $default;
    }

    // Filter and return
    return (int) apply_filters(__FUNCTION__, $retval, $default);
}

/**
 * Return the videos per RSS page setting.
 *
 * @since 1.0.0
 *
 * @param int $default Default videos per page (25)
 *
 * @uses get_option() To get the setting
 * @uses apply_filters() To allow the return value to be manipulated
 *
 * @return int
 */
function video_central_get_videos_per_rss_page($default = 35)
{

    // Get database option and cast as integer
    $retval = get_option('_video_central_videos_per_rss_page', $default);

    // If return val is empty, set it to default
    if (empty($retval)) {
        $retval = $default;
    }

    // Filter and return
    return (int) apply_filters(__FUNCTION__, $retval, $default);
}

/**
 * Return the related videos per page setting.
 *
 * @since 1.0.0
 *
 * @param int $default Default videos per page (15)
 *
 * @uses get_option() To get the setting
 * @uses apply_filters() To allow the return value to be manipulated
 *
 * @return int
 */
function video_central_get_related_videos_per_page($default = 12)
{

    // Get database option and cast as integer
    $retval = get_option('_video_central_related_videos_count', $default);

    // If return val is empty, set it to default
    if (empty($retval)) {
        $retval = $default;
    }

    // Filter and return
    return (int) apply_filters(__FUNCTION__, $retval, $default);
}

/**
 * Return the related videos per page setting.
 *
 * @since 1.2.0
 *
 * @param int $default Default videos per page (15)
 *
 * @uses get_option() To get the setting
 * @uses apply_filters() To allow the return value to be manipulated
 *
 * @return int
 */
function video_central_get_randomize_related_videos($default = false)
{
    return (bool) apply_filters(__FUNCTION__, (bool) get_option('_video_central_randomize_related_videos', $default));
}

/**
 * Force comments_status to 'closed' for Radium Video post types.
 *
 * @since 1.0.0
 *
 * @param bool $open    True if open, false if closed
 * @param int  $post_id ID of the post to check
 *
 * @return bool True if open, false if closed
 */
function video_central_force_comment_status($open, $post_id = 0)
{

    // Get the post type of the post ID
    $post_type = get_post_type($post_id);

    // Default return value is what is passed in $open
    $retval = $open;

    // Only force for Video Central post types
    switch ($post_type) {
        case video_central_get_video_post_type() :

            if (video_central_allow_comments()) {
                $retval = true;
            } else {
                $retval = false;
            }

            break;
    }

    // Allow override of the override
    return apply_filters(__FUNCTION__, $retval, $open, $post_id, $post_type);
}
