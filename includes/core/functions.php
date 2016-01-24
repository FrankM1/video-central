<?php

/**
 * Video Central Core Functions.
 */

/** Versions ******************************************************************/

/**
 * Output the Video Central version.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_version() To get the Video Central version
 */
function video_central_version()
{
    echo video_central_get_version();
}
    /**
     * Return the Video Central version.
     *
     * @since 1.0.0
     * @retrun string The Video Central version
     */
    function video_central_get_version()
    {
        return video_central()->version;
    }

/**
 * Output the Video Central database version.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_version() To get the Video Central version
 */
function video_central_db_version()
{
    echo video_central_get_db_version();
}
    /**
     * Return the Video Central database version.
     *
     * @since 1.0.0
     * @retrun string The Video Central version
     */
    function video_central_get_db_version()
    {
        return video_central()->db_version;
    }

/**
 * Output the Video Central database version directly from the database.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_version() To get the current Video Central version
 */
function video_central_db_version_raw()
{
    echo video_central_get_db_version_raw();
}
    /**
     * Return the Video Central database version directly from the database.
     *
     * @since 1.0.0
     * @retrun string The current Video Central version
     */
    function video_central_get_db_version_raw()
    {
        return get_option('_video_central_db_version', '');
    }

/** Views *********************************************************************/

/**
 * Get the registered views.
 *
 * Does nothing much other than return the {@link $video_central->views} variable
 *
 * @since 1.0.0
 *
 * @return array Views
 */
function video_central_get_views()
{
    return video_central()->views;
}

/**
 * Register a Video Central view.
 *
 * @since 1.0.0
 *
 * @param string $view       View name
 * @param string $title      View title
 * @param mixed  $query_args {@link video_central_has_videos()} arguments.
 * @param bool   $feed       Have a feed for the view? Defaults to true. NOT IMPLEMENTED
 * @param string $capability Capability that the current user must have
 *
 * @uses sanitize_title() To sanitize the view name
 * @uses esc_html() To sanitize the view title
 *
 * @return array The just registered (but processed) view
 */
function video_central_register_view($view, $title, $query_args = '', $feed = true, $capability = '')
{

    // Bail if user does not have capability
    if (!empty($capability) && !current_user_can($capability)) {
        return false;
    }

    $video_central = video_central();
    $view = sanitize_title($view);
    $title = esc_html($title);

    if (empty($view) || empty($title)) {
        return false;
    }

    $query_args = video_central_parse_args($query_args, '', 'register_view');

    // Set show_stickies to false if it wasn't supplied
    if (!isset($query_args['show_stickies'])) {
        $query_args['show_stickies'] = false;
    }

    $video_central->views[$view] = array(
        'title' => $title,
        'query' => $query_args,
        'feed' => $feed,
    );

    return $video_central->views[$view];
}

/**
 * Deregister a Video Central view.
 *
 * @since 1.0.0
 *
 * @param string $view View name
 *
 * @uses sanitize_title() To sanitize the view name
 *
 * @return bool False if the view doesn't exist, true on success
 */
function video_central_deregister_view($view)
{
    $video_central = video_central();
    $view = sanitize_title($view);

    if (!isset($video_central->views[$view])) {
        return false;
    }

    unset($video_central->views[$view]);

    return true;
}

/**
 * Run the view's query.
 *
 * @since 1.0.0
 *
 * @param string $view     Optional. View id
 * @param mixed  $new_args New arguments. See {@link video_central_has_videos()}
 *
 * @uses video_central_get_view_id() To get the view id
 * @uses video_central_get_view_query_args() To get the view query args
 * @uses sanitize_title() To sanitize the view name
 * @uses video_central_has_videos() To make the videos query
 *
 * @return bool False if the view doesn't exist, otherwise if videos are there
 */
function video_central_view_query($view = '', $new_args = '')
{
    $view = video_central_get_view_id($view);

    if (empty($view)) {
        return false;
    }

    $query_args = video_central_get_view_query_args($view);

    if (!empty($new_args)) {
        $new_args = video_central_parse_args($new_args, '', 'view_query');
        $query_args = array_merge($query_args, $new_args);
    }

    return video_central_has_videos($query_args);
}

/**
 * Return the view's query arguments.
 *
 * @since 1.0.0s
 *
 * @param string $view View name
 *
 * @uses video_central_get_view_id() To get the view id
 *
 * @return array Query arguments
 */
function video_central_get_view_query_args($view)
{
    $view = video_central_get_view_id($view);
    $retval = !empty($view) ? video_central()->views[$view]['query'] : false;

    return apply_filters(__FUNCTION__, $retval, $view);
}

/** Errors ********************************************************************/

/**
 * Adds an error message to later be output in the theme.
 *
 * @since 1.0.0
 * @see WP_Error()
 *
 * @uses WP_Error::add();
 *
 * @param string $code    Unique code for the error message
 * @param string $message Translated error message
 * @param string $data    Any additional data passed with the error message
 */
function video_central_add_error($code = '', $message = '', $data = '')
{
    video_central()->errors->add($code, $message, $data);
}

/**
 * Check if error messages exist in queue.
 *
 * @since 1.0.0
 * @see WP_Error()
 *
 * @uses is_wp_error()
 * @usese WP_Error::get_error_codes()
 */
function video_central_has_errors()
{
    $has_errors = video_central()->errors->get_error_codes() ? true : false;

    return apply_filters(__FUNCTION__, $has_errors, video_central()->errors);
}

/** Post Statuses *************************************************************/

/**
 * Return the public post status ID.
 *
 * @since 1.0.0
 *
 * @return string
 */
function video_central_get_public_status_id()
{
    return video_central()->public_status_id;
}

/**
 * Return the pending post status ID.
 *
 * @since 1.0.0
 *
 * @return string
 */
function video_central_get_pending_status_id()
{
    return video_central()->pending_status_id;
}

/**
 * Return the private post status ID.
 *
 * @since 1.0.0
 *
 * @return string
 */
function video_central_get_private_status_id()
{
    return video_central()->private_status_id;
}

/**
 * Return the hidden post status ID.
 *
 * @since 1.0.0
 *
 * @return string
 */
function video_central_get_hidden_status_id()
{
    return video_central()->hidden_status_id;
}

/**
 * Return the closed post status ID.
 *
 * @since 1.0.0
 *
 * @return string
 */
function video_central_get_closed_status_id()
{
    return video_central()->closed_status_id;
}

/**
 * Return the spam post status ID.
 *
 * @since 1.0.0
 *
 * @return string
 */
function video_central_get_spam_status_id()
{
    return video_central()->spam_status_id;
}

/**
 * Return the trash post status ID.
 *
 * @since 1.0.0
 *
 * @return string
 */
function video_central_get_trash_status_id()
{
    return video_central()->trash_status_id;
}

/**
 * Return the orphan post status ID.
 *
 * @since 1.0.0
 *
 * @return string
 */
function video_central_get_orphan_status_id()
{
    return video_central()->orphan_status_id;
}

/** Rewrite IDs ***************************************************************/

/**
 * Return the unique ID for user profile rewrite rules.
 *
 * @since 1.0.0
 *
 * @return string
 */
function video_central_get_user_rewrite_id()
{
    return video_central()->user_id;
}

/**
 * Return the unique ID for all edit rewrite rules (video|category|tag|user).
 *
 * @since 1.0.0
 *
 * @return string
 */
function video_central_get_edit_rewrite_id()
{
    return video_central()->edit_id;
}

/**
 * Return the unique ID for all search rewrite rules.
 *
 * @since 1.0.0
 *
 * @return string
 */
function video_central_get_search_rewrite_id()
{
    return video_central()->search_id;
}

/**
 * Return the unique ID for user caps rewrite rules.
 *
 * @since 1.0.0
 *
 * @return string
 */
function video_central_get_user_favorites_rewrite_id()
{
    return video_central()->favs_id;
}

/**
 * Return the unique ID for user caps rewrite rules.
 *
 * @since 1.0.0
 *
 * @return string
 */
function video_central_get_user_subscriptions_rewrite_id()
{
    return video_central()->subs_id;
}

/**
 * Return the unique ID for video view rewrite rules.
 *
 * @since 1.0.0
 *
 * @return string
 */
function video_central_get_view_rewrite_id()
{
    return video_central()->view_id;
}

/** Rewrite Extras ************************************************************/

/**
 * Get the id used for paginated requests.
 *
 * @since 1.0.0
 *
 * @return string
 */
function video_central_get_paged_rewrite_id()
{
    return video_central()->paged_id;
}

/**
 * Get the slug used for paginated requests.
 *
 * @since 1.0.0
 *
 * @global object $wp_rewrite The WP_Rewrite object
 *
 * @return string
 */
function video_central_get_paged_slug()
{
    global $wp_rewrite;

    return $wp_rewrite->pagination_base;
}

/**
 * Delete a blogs rewrite rules, so that they are automatically rebuilt on
 * the subsequent page load.
 *
 * @since 1.0.0
 */
function video_central_delete_rewrite_rules()
{
    delete_option('rewrite_rules');
}

/** Requests ******************************************************************/

/**
 * Return true|false if this is a POST request.
 *
 * @since 1.0.0
 *
 * @return bool
 */
function video_central_is_post_request()
{
    return (bool) ('POST' === strtoupper($_SERVER['REQUEST_METHOD']));
}

/**
 * Return true|false if this is a GET request.
 *
 * @since 1.0.0
 *
 * @return bool
 */
function video_central_is_get_request()
{
    return (bool) ('GET' === strtoupper($_SERVER['REQUEST_METHOD']));
}
