<?php

/**
 * Video Central User Functions.
 */

/**
 * Redirect back to $url when attempting to use the login page.
 *
 * @since 1.0.0
 *
 * @param string $url     The url
 * @param string $raw_url Raw url
 * @param object $user    User object
 *
 * @uses is_wp_error() To check if the user param is a {@link WP_Error}
 * @uses admin_url() To get the admin url
 * @uses home_url() To get the home url
 * @uses esc_url() To escape the url
 * @uses wp_safe_redirect() To redirect
 */
function video_central_redirect_login($url = '', $raw_url = '', $user = '')
{

    // Raw redirect_to was passed, so use it
    if (!empty($raw_url)) {
        $url = $raw_url;
    }

    // $url was manually set in wp-login.php to redirect to admin
    elseif (admin_url() === $url) {
        $url = home_url();
    }

    // $url is empty
    elseif (empty($url)) {
        $url = home_url();
    }

    return apply_filters(__FUNCTION__, $url, $raw_url, $user);
}

/**
 * Is an anonymous video being made?
 *
 * @since 1.0.0
 *
 * @uses is_user_logged_in() Is the user logged in?
 * @uses video_central_allow_anonymous() Is anonymous posting allowed?
 * @uses apply_filters() Calls 'video_central_is_anonymous' with the return value
 *
 * @return bool True if anonymous is allowed and user is not logged in, false if
 *              anonymous is not allowed or user is logged in
 */
function video_central_is_anonymous()
{
    if (!is_user_logged_in() && video_central_allow_anonymous()) {
        $is_anonymous = true;
    } else {
        $is_anonymous = false;
    }

    return apply_filters(__FUNCTION__, $is_anonymous);
}

/**
 * Echoes the values for current poster (uses WP comment cookies).
 *
 * @since 1.0.0
 *
 * @param string $key Which value to echo?
 *
 * @uses video_central_get_current_anonymous_user_data() To get the current anonymous user
 *                                              data
 */
function video_central_current_anonymous_user_data($key = '')
{
    echo video_central_get_current_anonymous_user_data($key);
}

    /**
     * Get the cookies for current poster (uses WP comment cookies).
     *
     * @since 1.0.0
     *
     * @param string $key Optional. Which value to get? If not given, then
     *                    an array is returned.
     *
     * @uses sanitize_comment_cookies() To sanitize the current poster data
     * @uses wp_get_current_commenter() To get the current poster data	 *
     *
     * @return string|array Cookie(s) for current poster
     */
    function video_central_get_current_anonymous_user_data($key = '')
    {
        $cookie_names = array(
            'name' => 'comment_author',
            'email' => 'comment_author_email',
            'url' => 'comment_author_url',

            // Here just for the sake of them, use the above ones
            'comment_author' => 'comment_author',
            'comment_author_email' => 'comment_author_email',
            'comment_author_url' => 'comment_author_url',
        );

        sanitize_comment_cookies();

        $video_central_current_poster = wp_get_current_commenter();

        if (!empty($key) && in_array($key, array_keys($cookie_names))) {
            return $video_central_current_poster[$cookie_names[$key]];
        }

        return $video_central_current_poster;
    }

/**
 * Set the cookies for current poster (uses WP comment cookies).
 *
 * @since 1.0.0
 *
 * @param array $anonymous_data With keys 'video_central_anonymous_name',
 *                              'video_central_anonymous_email', 'video_central_anonymous_website'.
 *                              Should be sanitized (see
 *                              {@link video_central_filter_anonymous_post_data()} for
 *                              sanitization)
 *
 * @uses apply_filters() Calls 'comment_cookie_lifetime' for cookie lifetime.
 *                        Defaults to 30000000.
 */
function video_central_set_current_anonymous_user_data($anonymous_data = array())
{
    if (empty($anonymous_data) || !is_array($anonymous_data)) {
        return;
    }

    $comment_cookie_lifetime = apply_filters('comment_cookie_lifetime', 30000000);

    setcookie('comment_author_'.COOKIEHASH, $anonymous_data['video_central_anonymous_name'],    time() + $comment_cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN);
    setcookie('comment_author_email_'.COOKIEHASH, $anonymous_data['video_central_anonymous_email'],   time() + $comment_cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN);
    setcookie('comment_author_url_'.COOKIEHASH, $anonymous_data['video_central_anonymous_website'], time() + $comment_cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN);
}

/**
 * Get the poster IP address.
 *
 * @since 1.0.0
 *
 * @return string
 */
function video_central_current_author_ip()
{
    $retval = preg_replace('/[^0-9a-fA-F:., ]/', '', $_SERVER['REMOTE_ADDR']);

    return apply_filters(__FUNCTION__, $retval);
}

/**
 * Get the poster user agent.
 *
 * @since 1.0.0
 *
 * @return string
 */
function video_central_current_author_ua()
{
    $retval = !empty($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 254) : '';

    return apply_filters(__FUNCTION__, $retval);
}

/** Post Counts ***************************************************************/

/**
 * Return the raw database count of videos by a user.
 *
 * @since 1.0.0
 *
 * @global WPDB $wpdb
 *
 * @uses video_central_get_user_id()
 * @uses get_posts_by_author_sql()
 * @uses video_central_get_video_post_type()
 * @uses apply_filters()
 *
 * @return int Raw DB count of videos
 */
function video_central_get_user_video_count_raw($user_id = 0)
{
    $user_id = video_central_get_user_id($user_id);
    if (empty($user_id)) {
        return false;
    }

    global $wpdb;

    $where = get_posts_by_author_sql(video_central_get_video_post_type(), true, $user_id);
    $count = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} {$where}");

    return (int) apply_filters(__FUNCTION__, $count, $user_id);
}

/**
 * Return the raw database count of videos by a user.
 *
 * @since 1.0.0
 *
 * @global WPDB $wpdb
 *
 * @uses video_central_get_user_id()
 * @uses get_posts_by_author_sql()
 * @uses video_central_get_video_post_type()
 * @uses apply_filters()
 *
 * @return int Raw DB count of videos
 */
function video_central_get_user_videos_count_raw($user_id = 0)
{
    $user_id = video_central_get_user_id($user_id);
    if (empty($user_id)) {
        return false;
    }

    global $wpdb;

    $where = get_posts_by_author_sql(video_central_get_video_post_type(), true, $user_id);
    $count = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} {$where}");

    return (int) apply_filters(__FUNCTION__, $count, $user_id);
}

/** Favorites *****************************************************************/

/**
 * Get the users who have made the video favorite.
 *
 * @since 1.0.0
 *
 * @param int $video_id Optional. Video id
 *
 * @uses wpdb::get_col() To execute our query and get the column back
 * @uses apply_filters() Calls 'video_central_get_video_favoriters' with the users and
 *                        video id
 *
 * @return array|bool Results if the video has any favoriters, otherwise false
 */
function video_central_get_video_favoriters($video_id = 0)
{
    $video_id = video_central_get_video_id($video_id);
    if (empty($video_id)) {
        return;
    }

    global $wpdb;

    $key = $wpdb->prefix.'_video_central_favorites';
    $users = wp_cache_get('video_central_get_video_favoriters_'.$video_id, 'video_central_users');
    if (false === $users) {
        $users = $wpdb->get_col("SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = '{$key}' and FIND_IN_SET('{$video_id}', meta_value) > 0");
        wp_cache_set('video_central_get_video_favoriters_'.$video_id, $users, 'video_central_users');
    }

    return apply_filters(__FUNCTION__, $users);
}

/**
 * Get a user's favorite videos.
 *
 * @since 1.0.0
 *
 * @param int $user_id Optional. User id
 *
 * @uses video_central_get_user_favorites_video_ids() To get the user's favorites
 * @uses video_central_has_videos() To get the videos
 * @uses apply_filters() Calls 'video_central_get_user_favorites' with the video query and
 *                        user id
 *
 * @return array|bool Results if user has favorites, otherwise false
 */
function video_central_get_user_favorites($user_id = 0)
{
    $user_id = video_central_get_user_id($user_id);
    if (empty($user_id)) {
        return false;
    }

    // If user has favorites, load them
    $favorites = video_central_get_user_favorites_video_ids($user_id);
    if (!empty($favorites)) {
        $query = video_central_has_videos(array('post__in' => $favorites));
    } else {
        $query = false;
    }

    return apply_filters(__FUNCTION__, $query, $user_id, $favorites);
}

/**
 * Get a user's favorite videos' ids.
 *
 * @since 1.0.0
 *
 * @param int $user_id Optional. User id
 *
 * @uses video_central_get_user_id() To get the user id
 * @uses get_user_option() To get the user favorites
 * @uses apply_filters() Calls 'video_central_get_user_favorites_video_ids' with
 *                        the favorites and user id
 *
 * @return array|bool Results if user has favorites, otherwise false
 */
function video_central_get_user_favorites_video_ids($user_id = 0)
{
    $user_id = video_central_get_user_id($user_id);
    if (empty($user_id)) {
        return false;
    }

    $favorites = get_user_option('_video_central_favorites', $user_id);
    $favorites = array_filter(wp_parse_id_list($favorites));

    return (array) apply_filters(__FUNCTION__, $favorites, $user_id);
}

/**
 * Check if a video is in user's favorites or not.
 *
 * @since 1.0.0
 *
 * @param int $user_id  Optional. User id
 * @param int $video_id Optional. Video id
 *
 * @uses video_central_get_user_id() To get the user id
 * @uses video_central_get_user_favorites_video_ids() To get the user favorites
 * @uses video_central_get_video() To get the video
 * @uses video_central_get_video_id() To get the video id
 * @uses apply_filters() Calls 'video_central_is_user_favorite' with the bool, user id,
 *                        video id and favorites
 *
 * @return bool True if the video is in user's favorites, otherwise false
 */
function video_central_is_user_favorite($user_id = 0, $video_id = 0)
{
    $user_id = video_central_get_user_id($user_id, true, true);
    if (empty($user_id)) {
        return false;
    }

    $retval = false;
    $favorites = video_central_get_user_favorites_video_ids($user_id);

    if (!empty($favorites)) {

        // Checking a specific video id
        if (!empty($video_id)) {
            $video = video_central_get_video($video_id);
            $video_id = !empty($video) ? $video->ID : 0;

        // Using the global video id
        } elseif (video_central_get_video_id()) {
            $video_id = video_central_get_video_id();

        // Use the current post id
        } elseif (!video_central_get_video_id()) {
            $video_id = get_the_ID();
        }

        // Is video_id in the user's favorites
        if (!empty($video_id)) {
            $retval = in_array($video_id, $favorites);
        }
    }

    return (bool) apply_filters(__FUNCTION__, (bool) $retval, $user_id, $video_id, $favorites);
}

/**
 * Add a video to user's favorites.
 *
 * @since 1.0.0
 *
 * @param int $user_id  Optional. User id
 * @param int $video_id Optional. Video id
 *
 * @uses video_central_get_user_favorites_video_ids() To get the user favorites
 * @uses update_user_option() To update the user favorites
 * @uses do_action() Calls 'video_central_add_user_favorite' with the user id and video id
 *
 * @return bool Always true
 */
function video_central_add_user_favorite($user_id = 0, $video_id = 0)
{
    if (empty($user_id) || empty($video_id)) {
        return false;
    }

    $video = video_central_get_video($video_id);
    if (empty($video)) {
        return false;
    }

    $favorites = video_central_get_user_favorites_video_ids($user_id);
    if (!in_array($video_id, $favorites)) {
        $favorites[] = $video_id;
        $favorites = implode(',', wp_parse_id_list(array_filter($favorites)));
        update_user_option($user_id, '_video_central_favorites', $favorites);
    }

    do_action(__FUNCTION__, $user_id, $video_id);

    return true;
}

/**
 * Remove a video from user's favorites.
 *
 * @since 1.0.0
 *
 * @param int $user_id  Optional. User id
 * @param int $video_id Optional. Video id
 *
 * @uses video_central_get_user_favorites_video_ids() To get the user favorites
 * @uses update_user_option() To update the user favorites
 * @uses delete_user_option() To delete the user favorites meta
 * @uses do_action() Calls 'video_central_remove_user_favorite' with the user & video id
 *
 * @return bool True if the video was removed from user's favorites, otherwise
 *              false
 */
function video_central_remove_user_favorite($user_id, $video_id)
{
    if (empty($user_id) || empty($video_id)) {
        return false;
    }

    $favorites = (array) video_central_get_user_favorites_video_ids($user_id);
    if (empty($favorites)) {
        return false;
    }

    $pos = array_search($video_id, $favorites);
    if (is_numeric($pos)) {
        array_splice($favorites, $pos, 1);
        $favorites = array_filter($favorites);

        if (!empty($favorites)) {
            $favorites = implode(',', wp_parse_id_list($favorites));
            update_user_option($user_id, '_video_central_favorites', $favorites);
        } else {
            delete_user_option($user_id, '_video_central_favorites');
        }
    }

    do_action(__FUNCTION__, $user_id, $video_id);

    return true;
}

/**
 * Handles the front end adding and removing of favorite videos.
 *
 * @param string $action The requested action to compare this function to
 *
 * @uses video_central_get_user_id() To get the user id
 * @uses video_central_verify_nonce_request() To verify the nonce and check the request
 * @uses current_user_can() To check if the current user can edit the user
 * @uses video_central:errors:add() To log the error messages
 * @uses video_central_is_user_favorite() To check if the video is in user's favorites
 * @uses video_central_remove_user_favorite() To remove the user favorite
 * @uses video_central_add_user_favorite() To add the user favorite
 * @uses do_action() Calls 'video_central_favorites_handler' with success, user id, video
 *                    id and action
 * @uses video_central_is_favorites() To check if it's the favorites page
 * @uses video_central_get_favorites_link() To get the favorites page link
 * @uses video_central_get_video_permalink() To get the video permalink
 * @uses wp_safe_redirect() To redirect to the url
 */
function video_central_favorites_handler($action = '')
{
    if (!video_central_is_favorites_active()) {
        return false;
    }

    // Bail if no video ID is passed
    if (empty($_GET['video_id'])) {
        return;
    }

    // Setup possible get actions
    $possible_actions = array(
        'video_central_favorite_add',
        'video_central_favorite_remove',
    );

    // Bail if actions aren't meant for this function
    if (!in_array($action, $possible_actions)) {
        return;
    }

    // What action is taking place?
    $video_id = intval($_GET['video_id']);
    $user_id = video_central_get_user_id(0, true, true);

    // Check for empty video
    if (empty($video_id)) {
        video_central_add_error('video_central_favorite_video_id', __('<strong>ERROR</strong>: No video was found! Which video are you marking/unmarking as favorite?', 'video_central'));

    // Check nonce
    } elseif (!video_central_verify_nonce_request('toggle-favorite_'.$video_id)) {
        video_central_add_error('video_central_favorite_nonce', __('<strong>ERROR</strong>: Are you sure you wanted to do that?', 'video_central'));

    // Check current user's ability to edit the user
    } elseif (!current_user_can('edit_user', $user_id)) {
        video_central_add_error('video_central_favorite_permissions', __('<strong>ERROR</strong>: You don\'t have the permission to edit favorites of that user!', 'video_central'));
    }

    // Bail if errors
    if (video_central_has_errors()) {
        return;
    }

    /* No errors *************************************************************/

    $is_favorite = video_central_is_user_favorite($user_id, $video_id);
    $success = false;

    if (true === $is_favorite && 'video_central_favorite_remove' === $action) {
        $success = video_central_remove_user_favorite($user_id, $video_id);
    } elseif (false === $is_favorite && 'video_central_favorite_add' === $action) {
        $success = video_central_add_user_favorite($user_id, $video_id);
    }

    // Do additional favorites actions
    do_action(__FUNCTION__, $success, $user_id, $video_id, $action);

    // Success!
    if (true === $success) {

        // Redirect back from whence we came
        if (video_central_is_favorites()) {
            $redirect = video_central_get_favorites_permalink($user_id);
        } elseif (video_central_is_single_user()) {
            $redirect = video_central_get_user_profile_url();
        } elseif (is_singular(video_central_get_video_post_type())) {
            $redirect = video_central_get_video_permalink($video_id);
        } elseif (is_single() || is_page()) {
            $redirect = get_permalink();
        } else {
            $redirect = get_permalink($video_id);
        }

        wp_safe_redirect($redirect);

        // For good measure
        wp_die();

    // Fail! Handle errors
    } elseif (true === $is_favorite && 'video_central_favorite_remove' === $action) {
        video_central_add_error('video_central_favorite_remove', __('<strong>ERROR</strong>: There was a problem removing that video from favorites!', 'video_central'));
    } elseif (false === $is_favorite && 'video_central_favorite_add' === $action) {
        video_central_add_error('video_central_favorite_add',    __('<strong>ERROR</strong>: There was a problem favoriting that video!', 'video_central'));
    }
}

/** Subscriptions *************************************************************/

/**
 * Get the users who have subscribed to the video.
 *
 * @since 1.0.0
 *
 * @param int $video_id Optional. video id
 *
 * @uses wpdb::get_col() To execute our query and get the column back
 * @uses apply_filters() Calls 'video_central_get_video_subscribers' with the subscribers
 *
 * @return array|bool Results if the video has any subscribers, otherwise false
 */
function video_central_get_video_subscribers($video_id = 0)
{
    $video_id = video_central_get_video_id($video_id);
    if (empty($video_id)) {
        return;
    }

    global $wpdb;

    $key = $wpdb->prefix.'_video_central_video_subscriptions';
    $users = wp_cache_get('video_central_get_video_subscribers_'.$video_id, 'video_central_users');
    if (false === $users) {
        $users = $wpdb->get_col("SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = '{$key}' and FIND_IN_SET('{$video_id}', meta_value) > 0");
        wp_cache_set('video_central_get_video_subscribers_'.$video_id, $users, 'video_central_users');
    }

    return apply_filters(__FUNCTION__, $users);
}

/**
 * Get a user's subscribed videos.
 *
 * @since 1.0.0
 *
 * @param int $user_id Optional. User id
 *
 * @uses video_central_get_user_subscribed_video_ids() To get the user's subscriptions
 * @uses video_central_has_videos() To get the videos
 * @uses apply_filters() Calls 'video_central_get_user_subscriptions' with the video query
 *                        and user id
 *
 * @return array|bool Results if user has subscriptions, otherwise false
 */
function video_central_get_user_video_subscriptions($user_id = 0)
{

    // Default to the displayed user
    $user_id = video_central_get_user_id($user_id);
    if (empty($user_id)) {
        return false;
    }

    // If user has subscriptions, load them
    $subscriptions = video_central_get_user_subscribed_video_ids($user_id);
    if (!empty($subscriptions)) {
        $query = video_central_has_videos(array('post__in' => $subscriptions));
    } else {
        $query = false;
    }

    return apply_filters(__FUNCTION__, $query, $user_id);
}

/**
 * Get a user's subscribed video ids.
 *
 * @since 1.0.0
 *
 * @param int $user_id Optional. User id
 *
 * @uses video_central_get_user_id() To get the user id
 * @uses get_user_option() To get the user's subscriptions
 * @uses apply_filters() Calls 'video_central_get_user_subscribed_video_ids' with
 *                        the subscriptions and user id
 *
 * @return array|bool Results if user has subscriptions, otherwise false
 */
function video_central_get_user_subscribed_video_ids($user_id = 0)
{
    $user_id = video_central_get_user_id($user_id);
    if (empty($user_id)) {
        return false;
    }

    $subscriptions = get_user_option('_video_central_video_subscriptions', $user_id);
    $subscriptions = array_filter(wp_parse_id_list($subscriptions));

    return (array) apply_filters(__FUNCTION__, $subscriptions, $user_id);
}

/**
 * Check if a video or video is in user's subscription list or not.
 *
 * @since 1.0.0
 *
 * @param int $user_id  Optional. User id
 * @param int $video_id Optional. Video id
 *
 * @uses get_post() To get the post object
 * @uses video_central_get_user_subscribed_video_ids() To get the user's video subscriptions
 * @uses video_central_get_user_subscribed_video_ids() To get the user's video subscriptions
 * @uses video_central_get_video_post_type() To get the video post type
 * @uses video_central_get_video_post_type() To get the video post type
 * @uses apply_filters() Calls 'video_central_is_user_subscribed' with the bool, user id,
 *                        video/video id and subsriptions
 *
 * @return bool True if the video or video is in user's subscriptions, otherwise false
 */
function video_central_is_user_subscribed($user_id = 0, $object_id = 0)
{

    // Assume user is not subscribed
    $retval = false;

    // Setup ID's array
    $subscribed_ids = array();

    // User and object ID's are passed
    if (!empty($user_id) && !empty($object_id)) {

        // Get the post type
        $post_type = get_post_type($object_id);

        // Post exists, so check the types
        if (!empty($post_type)) {
            switch ($post_type) {

                // Video
                case video_central_get_video_post_type() :
                    $subscribed_ids = video_central_get_user_subscribed_video_ids($user_id);
                    $retval = video_central_is_user_subscribed_to_video($user_id, $object_id, $subscribed_ids);
                    break;

                default :
                    $subscribed_ids = video_central_get_user_subscribed_video_ids($user_id);
                    $retval = video_central_is_user_subscribed_to_video($user_id, $object_id, $subscribed_ids);
                    break;
            }
        }
    }

    return (bool) apply_filters(__FUNCTION__, $retval, $user_id, $object_id, $subscribed_ids);
}

/**
 * Check if a video is in user's subscription list or not.
 *
 * @since 1.0.0
 *
 * @param int   $user_id        Optional. User id
 * @param int   $video_id       Optional. Video id
 * @param array $subscribed_ids Optional. Array of video ID's to check
 *
 * @uses video_central_get_user_id() To get the user id
 * @uses video_central_get_user_subscribed_video_ids() To get the user's subscriptions
 * @uses video_central_get_video() To get the video
 * @uses video_central_get_video_id() To get the video id
 * @uses apply_filters() Calls 'video_central_is_user_subscribed' with the bool, user id,
 *                        video id and subsriptions
 *
 * @return bool True if the video is in user's subscriptions, otherwise false
 */
function video_central_is_user_subscribed_to_video($user_id = 0, $video_id = 0, $subscribed_ids = array())
{

    // Assume user is not subscribed
    $retval = false;

    // Validate user
    $user_id = video_central_get_user_id($user_id, true, true);
    if (!empty($user_id)) {

        // Get subscription ID's if none passed
        if (empty($subscribed_ids)) {
            $subscribed_ids = video_central_get_user_subscribed_video_ids($user_id);
        }

        // User has video subscriptions
        if (!empty($subscribed_ids)) {

            // Checking a specific video id
            if (!empty($video_id)) {
                $video = video_central_get_video($video_id);
                $video_id = !empty($video) ? $video->ID : 0;

            // Using the global video id
            } elseif (video_central_get_video_id()) {
                $video_id = video_central_get_video_id();

            // Use the current post id
            } elseif (!video_central_get_video_id()) {
                $video_id = get_the_ID();
            }

            // Is video_id in the user's favorites
            if (!empty($video_id)) {
                $retval = in_array($video_id, $subscribed_ids);
            }
        }
    }

    return (bool) apply_filters(__FUNCTION__, (bool) $retval, $user_id, $video_id, $subscribed_ids);
}

/**
 * Add a video to user's subscriptions.
 *
 * @since 1.0.0
 *
 * @param int $user_id  Optional. User id
 * @param int $video_id Optional. Video id
 *
 * @uses get_post() To get the post object
 * @uses video_central_get_user_subscribed_video_ids() To get the user's video subscriptions
 * @uses video_central_get_user_subscribed_video_ids() To get the user's video subscriptions
 * @uses video_central_get_video_post_type() To get the video post type
 * @uses video_central_get_video_post_type() To get the video post type
 * @uses update_user_option() To update the user's subscriptions
 * @uses do_action() Calls 'video_central_add_user_subscription' with the user & video id
 *
 * @return bool Always true
 */
function video_central_add_user_subscription($user_id = 0, $object_id = 0)
{
    if (empty($user_id) || empty($object_id)) {
        return false;
    }

    // Get the post type
    $post_type = get_post_type($object_id);
    if (empty($post_type)) {
        return false;
    }

    switch ($post_type) {

        // Video
        case video_central_get_video_post_type() :
            video_central_add_user_video_subscription($user_id, $object_id);
            break;

        // Topic
        case video_central_get_video_post_type() :
        default :
            video_central_add_user_video_subscription($user_id, $object_id);
            break;
    }

    do_action(__FUNCTION__, $user_id, $object_id, $post_type);

    return true;
}

/**
 * Add a video to user's subscriptions.
 *
 * @since 1.0.0
 *
 * @param int $user_id  Optional. User id
 * @param int $video_id Optional. video id
 *
 * @uses video_central_get_user_subscribed_video_ids() To get the user's subscriptions
 * @uses video_central_get_video() To get the video
 * @uses update_user_option() To update the user's subscriptions
 * @uses do_action() Calls 'video_central_add_user_subscription' with the user & video id
 *
 * @return bool Always true
 */
function video_central_add_user_video_subscription($user_id = 0, $video_id = 0)
{
    if (empty($user_id) || empty($video_id)) {
        return false;
    }

    $video = video_central_get_video($video_id);
    if (empty($video)) {
        return false;
    }

    $subscriptions = (array) video_central_get_user_subscribed_video_ids($user_id);
    if (!in_array($video_id, $subscriptions)) {
        $subscriptions[] = $video_id;
        $subscriptions = implode(',', wp_parse_id_list(array_filter($subscriptions)));
        update_user_option($user_id, '_video_central_video_subscriptions', $subscriptions);

        wp_cache_delete('video_central_get_video_subscribers_'.$video_id, 'video_central_users');
    }

    do_action(__FUNCTION__, $user_id, $video_id);

    return true;
}

/**
 * Remove a video from user's subscriptions.
 *
 * @since 1.0.0
 *
 * @param int $user_id  Optional. User id
 * @param int $video_id Optional. Video id
 *
 * @uses get_post() To get the post object
 * @uses video_central_get_video_post_type() To get the video post type
 * @uses video_central_get_video_post_type() To get the video post type
 * @uses video_central_remove_user_video_subscription() To remove the user's subscription
 * @uses video_central_remove_user_video_subscription() To remove the user's subscription
 * @uses do_action() Calls 'video_central_remove_user_subscription' with the user id and
 *                    video id
 *
 * @return bool True if the video was removed from user's subscriptions,
 *              otherwise false
 */
function video_central_remove_user_subscription($user_id = 0, $object_id = 0)
{
    if (empty($user_id) || empty($object_id)) {
        return false;
    }

    $post_type = get_post_type($object_id);
    if (empty($post_type)) {
        return false;
    }

    switch ($post_type) {

        // Video
        case video_central_get_video_post_type() :
            video_central_remove_user_video_subscription($user_id, $object_id);
            break;

        // Topic
        case video_central_get_video_post_type() :
        default :
            video_central_remove_user_video_subscription($user_id, $object_id);
            break;
    }

    do_action(__FUNCTION__, $user_id, $object_id, $post_type);

    return true;
}

/**
 * Remove a video from user's subscriptions.
 *
 * @since 1.0.0
 *
 * @param int $user_id  Optional. User id
 * @param int $video_id Optional. video id
 *
 * @uses video_central_get_user_subscribed_video_ids() To get the user's subscriptions
 * @uses update_user_option() To update the user's subscriptions
 * @uses delete_user_option() To delete the user's subscriptions meta
 * @uses do_action() Calls 'video_central_remove_user_subscription' with the user id and
 *                    video id
 *
 * @return bool True if the video was removed from user's subscriptions,
 *              otherwise false
 */
function video_central_remove_user_video_subscription($user_id, $video_id)
{
    if (empty($user_id) || empty($video_id)) {
        return false;
    }

    $subscriptions = (array) video_central_get_user_subscribed_video_ids($user_id);
    if (empty($subscriptions)) {
        return false;
    }

    $pos = array_search($video_id, $subscriptions);
    if (false === $pos) {
        return false;
    }

    array_splice($subscriptions, $pos, 1);
    $subscriptions = array_filter($subscriptions);

    if (!empty($subscriptions)) {
        $subscriptions = implode(',', wp_parse_id_list($subscriptions));
        update_user_option($user_id, '_video_central_video_subscriptions', $subscriptions);
    } else {
        delete_user_option($user_id, '_video_central_video_subscriptions');
    }

    wp_cache_delete('video_central_get_video_subscribers_'.$video_id, 'video_central_users');

    do_action(__FUNCTION__, $user_id, $video_id);

    return true;
}

/**
 * Handles the front end subscribing and unsubscribing videos.
 *
 * @since 1.0.0
 *
 * @param string $action The requested action to compare this function to
 *
 * @uses video_central_is_subscriptions_active() To check if the subscriptions are active
 * @uses video_central_get_user_id() To get the user id
 * @uses video_central_verify_nonce_request() To verify the nonce and check the request
 * @uses current_user_can() To check if the current user can edit the user
 * @uses video_central:errors:add() To log the error messages
 * @uses video_central_is_user_subscribed() To check if the video is in user's
 *                                 subscriptions
 * @uses video_central_remove_user_subscription() To remove the user subscription
 * @uses video_central_add_user_subscription() To add the user subscription
 * @uses do_action() Calls 'video_central_subscriptions_handler' with success, user id,
 *                    video id and action
 * @uses video_central_is_subscription() To check if it's the subscription page
 * @uses video_central_get_video_permalink() To get the video permalink
 * @uses wp_safe_redirect() To redirect to the url
 */
function video_central_video_subscriptions_handler($action = '')
{
    if (!video_central_is_subscriptions_active()) {
        return false;
    }

    // Bail if no video ID is passed
    if (empty($_GET['video_id'])) {
        return;
    }

    // Setup possible get actions
    $possible_actions = array(
        'video_central_subscribe',
        'video_central_unsubscribe',
    );

    // Bail if actions aren't meant for this function
    if (!in_array($action, $possible_actions)) {
        return;
    }

    // Get required data
    $user_id = video_central_get_user_id(0, true, true);
    $video_id = intval($_GET['video_id']);

    // Check for empty video
    if (empty($video_id)) {
        video_central_add_error('video_central_subscription_video_id', __('<strong>ERROR</strong>: No video was found! Which video are you subscribing/unsubscribing to?', 'video_central'));

    // Check nonce
    } elseif (!video_central_verify_nonce_request('toggle-subscription_'.$video_id)) {
        video_central_add_error('video_central_subscription_video_id', __('<strong>ERROR</strong>: Are you sure you wanted to do that?', 'video_central'));

    // Check current user's ability to edit the user
    } elseif (!current_user_can('edit_user', $user_id)) {
        video_central_add_error('video_central_subscription_permissions', __('<strong>ERROR</strong>: You don\'t have the permission to edit favorites of that user!', 'video_central'));
    }

    // Bail if we have errors
    if (video_central_has_errors()) {
        return;
    }

    /* No errors *************************************************************/

    $is_subscription = video_central_is_user_subscribed($user_id, $video_id);
    $success = false;

    if (true === $is_subscription && 'video_central_unsubscribe' === $action) {
        $success = video_central_remove_user_subscription($user_id, $video_id);
    } elseif (false === $is_subscription && 'video_central_subscribe' === $action) {
        $success = video_central_add_user_subscription($user_id, $video_id);
    }

    // Do additional subscriptions actions
    do_action('video_central_subscriptions_handler', $success, $user_id, $video_id, $action);

    // Success!
    if (true === $success) {

        // Redirect back from whence we came
        if (video_central_is_subscriptions()) {
            $redirect = video_central_get_subscriptions_permalink($user_id);
        } elseif (video_central_is_single_user()) {
            $redirect = video_central_get_user_profile_url();
        } elseif (is_singular(video_central_get_video_post_type())) {
            $redirect = video_central_get_video_permalink($video_id);
        } elseif (is_single() || is_page()) {
            $redirect = get_permalink();
        } else {
            $redirect = get_permalink($video_id);
        }

        wp_safe_redirect($redirect);

        // For good measure
        wp_die();

    // Fail! Handle errors
    } elseif (true === $is_subscription && 'video_central_unsubscribe' === $action) {
        video_central_add_error('video_central_unsubscribe', __('<strong>ERROR</strong>: There was a problem unsubscribing from that video!', 'video_central'));
    } elseif (false === $is_subscription && 'video_central_subscribe' === $action) {
        video_central_add_error('video_central_subscribe',    __('<strong>ERROR</strong>: There was a problem subscribing to that video!', 'video_central'));
    }
}

/**
 * Handles the front end subscribing and unsubscribing videos.
 *
 * @param string $action The requested action to compare this function to
 *
 * @uses video_central_is_subscriptions_active() To check if the subscriptions are active
 * @uses video_central_get_user_id() To get the user id
 * @uses video_central_verify_nonce_request() To verify the nonce and check the request
 * @uses current_user_can() To check if the current user can edit the user
 * @uses video_central:errors:add() To log the error messages
 * @uses video_central_is_user_subscribed() To check if the video is in user's
 *                                 subscriptions
 * @uses video_central_remove_user_subscription() To remove the user subscription
 * @uses video_central_add_user_subscription() To add the user subscription
 * @uses do_action() Calls 'video_central_subscriptions_handler' with success, user id,
 *                    video id and action
 * @uses video_central_is_subscription() To check if it's the subscription page
 * @uses video_central_get_video_permalink() To get the video permalink
 * @uses wp_safe_redirect() To redirect to the url
 */
function video_central_subscriptions_handler($action = '')
{
    if (!video_central_is_subscriptions_active()) {
        return false;
    }

    // Bail if no video ID is passed
    if (empty($_GET['video_id'])) {
        return;
    }

    // Setup possible get actions
    $possible_actions = array(
        'video_central_subscribe',
        'video_central_unsubscribe',
    );

    // Bail if actions aren't meant for this function
    if (!in_array($action, $possible_actions)) {
        return;
    }

    // Get required data
    $user_id = video_central_get_user_id(0, true, true);
    $video_id = intval($_GET['video_id']);

    // Check for empty video
    if (empty($video_id)) {
        video_central_add_error('video_central_subscription_video_id', __('<strong>ERROR</strong>: No video was found! Which video are you subscribing/unsubscribing to?', 'video_central'));

    // Check nonce
    } elseif (!video_central_verify_nonce_request('toggle-subscription_'.$video_id)) {
        video_central_add_error('video_central_subscription_video_id', __('<strong>ERROR</strong>: Are you sure you wanted to do that?', 'video_central'));

    // Check current user's ability to edit the user
    } elseif (!current_user_can('edit_user', $user_id)) {
        video_central_add_error('video_central_subscription_permissions', __('<strong>ERROR</strong>: You don\'t have the permission to edit favorites of that user!', 'video_central'));
    }

    // Bail if we have errors
    if (video_central_has_errors()) {
        return;
    }

    /* No errors *************************************************************/

    $is_subscription = video_central_is_user_subscribed($user_id, $video_id);
    $success = false;

    if (true === $is_subscription && 'video_central_unsubscribe' === $action) {
        $success = video_central_remove_user_subscription($user_id, $video_id);
    } elseif (false === $is_subscription && 'video_central_subscribe' === $action) {
        $success = video_central_add_user_subscription($user_id, $video_id);
    }

    // Do additional subscriptions actions
    do_action(__FUNCTION__, $success, $user_id, $video_id, $action);

    // Success!
    if (true === $success) {

        // Redirect back from whence we came
        if (video_central_is_subscriptions()) {
            $redirect = video_central_get_subscriptions_permalink($user_id);
        } elseif (video_central_is_single_user()) {
            $redirect = video_central_get_user_profile_url();
        } elseif (is_singular(video_central_get_video_post_type())) {
            $redirect = video_central_get_video_permalink($video_id);
        } elseif (is_single() || is_page()) {
            $redirect = get_permalink();
        } else {
            $redirect = get_permalink($video_id);
        }

        wp_safe_redirect($redirect);

        // For good measure
        wp_die();

    // Fail! Handle errors
    } elseif (true === $is_subscription && 'video_central_unsubscribe' === $action) {
        video_central_add_error('video_central_unsubscribe', __('<strong>ERROR</strong>: There was a problem unsubscribing from that video!', 'video_central'));
    } elseif (false === $is_subscription && 'video_central_subscribe' === $action) {
        video_central_add_error('video_central_subscribe',    __('<strong>ERROR</strong>: There was a problem subscribing to that video!', 'video_central'));
    }
}

/** Edit **********************************************************************/

/**
 * Handles the front end user editing.
 *
 * @param string $action The requested action to compare this function to
 *
 * @uses is_multisite() To check if it's a multisite
 * @uses video_central_is_user_home() To check if the user is at home (the display page
 *                           is the one of the logged in user)
 * @uses get_option() To get the displayed user's new email id option
 * @uses wpdb::prepare() To sanitize our sql query
 * @uses wpdb::get_var() To execute our query and get back the variable
 * @uses wpdb::query() To execute our query
 * @uses wp_update_user() To update the user
 * @uses delete_option() To delete the displayed user's email id option
 * @uses video_central_get_user_profile_edit_url() To get the edit profile url
 * @uses wp_safe_redirect() To redirect to the url
 * @uses video_central_verify_nonce_request() To verify the nonce and check the request
 * @uses current_user_can() To check if the current user can edit the user
 * @uses do_action() Calls 'personal_options_update' or
 *                   'edit_user_options_update' (based on if it's the user home)
 *                   with the displayed user id
 * @uses edit_user() To edit the user based on the post data
 * @uses get_userdata() To get the user data
 * @uses is_email() To check if the string is an email id or not
 * @uses wpdb::get_blog_prefix() To get the blog prefix
 * @uses is_network_admin() To check if the user is the network admin
 * @uses revoke_super_admin() To revoke super admin priviledges
 * @uses grant_super_admin() To grant super admin priviledges
 * @uses is_wp_error() To check if the value retrieved is a {@link WP_Error}
 */
function video_central_edit_user_handler($action = '')
{

    // Bail if action is not 'video-central-update-user'
    if ('video-central-update-user' !== $action) {
        return;
    }

    // Get the displayed user ID
    $user_id = video_central_get_displayed_user_id();

    // Execute confirmed email change. See send_confirmation_on_profile_email().
    if (is_multisite() && video_central_is_user_home_edit() && isset($_GET['newuseremail'])) {
        $new_email = get_option($user_id.'_new_email');

        if ($new_email['hash'] === $_GET['newuseremail']) {
            $user = new WP_User();
            $user->ID = $user_id;
            $user->user_email = esc_html(trim($new_email['newemail']));

            global $wpdb;

            if ($wpdb->get_var($wpdb->prepare("SELECT user_login FROM {$wpdb->signups} WHERE user_login = %s", video_central_get_displayed_user_field('user_login', 'raw')))) {
                $wpdb->query($wpdb->prepare("UPDATE {$wpdb->signups} SET user_email = %s WHERE user_login = %s", $user->user_email, video_central_get_displayed_user_field('user_login', 'raw')));
            }

            wp_update_user(get_object_vars($user));
            delete_option($user_id.'_new_email');

            wp_safe_redirect(add_query_arg(array('updated' => 'true'), video_central_get_user_profile_edit_url($user_id)));
            wp_die();
        }

    // Delete new email address from user options
    } elseif (is_multisite() && video_central_is_user_home_edit() && !empty($_GET['dismiss']) && ($user_id.'_new_email' === $_GET['dismiss'])) {
        delete_option($user_id.'_new_email');
        wp_safe_redirect(add_query_arg(array('updated' => 'true'), video_central_get_user_profile_edit_url($user_id)));
        wp_die();
    }

    // Nonce check
    if (!video_central_verify_nonce_request('update-user_'.$user_id)) {
        video_central_add_error('video_central_update_user_nonce', __('<strong>ERROR</strong>: Are you sure you wanted to do that?', 'video_central'));

        return;
    }

    // Cap check
    if (!current_user_can('edit_user', $user_id)) {
        video_central_add_error('video_central_update_user_capability', __('<strong>ERROR</strong>: Are you sure you wanted to do that?', 'video_central'));

        return;
    }

    // Do action based on who's profile you're editing
    $edit_action = video_central_is_user_home_edit() ? 'personal_options_update' : 'edit_user_profile_update';
    do_action($edit_action, $user_id);

    // Prevent edit_user() from wiping out the user's Toolbar on front setting
    if (!isset($_POST['admin_bar_front']) && _get_admin_bar_pref('front', $user_id)) {
        $_POST['admin_bar_front'] = 1;
    }

    // Handle user edit
    $edit_user = edit_user($user_id);

    // Error(s) editng the user, so copy them into the global
    if (is_wp_error($edit_user)) {
        video_central()->errors = $edit_user;

    // Successful edit to redirect
    } elseif (is_integer($edit_user)) {

        // Maybe update super admin ability
        if (is_multisite() && !video_central_is_user_home_edit()) {
            empty($_POST['super_admin']) ? revoke_super_admin($edit_user) : grant_super_admin($edit_user);
        }

        $redirect = add_query_arg(array('updated' => 'true'), video_central_get_user_profile_edit_url($edit_user));

        wp_safe_redirect($redirect);
        wp_die();
    }
}

/**
 * Conditionally hook the core WordPress output actions to the end of the
 * default user's edit profile template.
 *
 * This allows clever plugin authors to conditionally unhook the WordPress core
 * output actions if they don't want any unexpected junk to appear there, and
 * also avoids needing to pollute the templates with additional logic and actions.
 *
 * @since 1.0.0
 *
 * @uses video_central_is_user_home_edit() To switch the action fired
 * @uses get_userdata() To get the current user's data
 * @uses video_central_get_displayed_user_id() To get the currently displayed user ID
 */
function video_central_user_edit_after()
{
    $action = video_central_is_user_home_edit() ? 'show_user_profile' : 'edit_user_profile';

    do_action($action, get_userdata(video_central_get_displayed_user_id()));
}

/** User Queries **************************************************************/

/**
 * Get the videos that a user created.
 *
 * @since 1.0.0
 *
 * @param int $user_id Optional. User id
 *
 * @uses video_central_get_user_id() To get the video id
 * @uses video_central_has_videos() To get the videos created by the user
 *
 * @return array|bool Results if the user has created videos, otherwise false
 */
function video_central_get_user_videos_posted($user_id = 0)
{

    // Validate user
    $user_id = video_central_get_user_id($user_id);
    if (empty($user_id)) {
        return false;
    }

    // Try to get the videos
    $query = video_central_has_videos(array(
        'author' => $user_id,
    ));

    return apply_filters(__FUNCTION__, $query, $user_id);
}

/**
 * Get the total number of users on the videos.
 *
 * @since 1.0.0
 *
 * @uses count_users() To execute our query and get the var back
 * @uses apply_filters() Calls 'video_central_get_total_users' with number of users
 *
 * @return int Total number of users
 */
function video_central_get_total_users()
{
    $user_count = count_users();

    return apply_filters(__FUNCTION__, (int) $user_count['total_users']);
}

/** Permissions ***************************************************************/

/**
 * Redirect if unathorized user is attempting to edit another user.
 *
 * This is hooked to 'video_central_template_redirect' and controls the conditions under
 * which a user can edit another user (or themselves.) If these conditions are
 * met. We assume a user cannot perform this task, and look for ways they can
 * earn the ability to access this template.
 *
 * @since 1.0.0
 *
 * @uses video_central_is_video_edit()
 * @uses current_user_can()
 * @uses video_central_get_video_id()
 * @uses wp_safe_redirect()
 * @uses video_central_get_video_permalink()
 */
function video_central_check_user_edit()
{

    // Bail if not editing a video
    if (!video_central_is_single_user_edit()) {
        return;
    }

    // Default to false
    $redirect = true;

    // Allow user to edit their own profile
    if (video_central_is_user_home_edit()) {
        $redirect = false;

    // Allow if current user can edit the displayed user
    } elseif (current_user_can('edit_user', video_central_get_displayed_user_id())) {
        $redirect = false;

    // Allow if user can manage network users, or edit-any is enabled
    } elseif (current_user_can('manage_network_users') || apply_filters('enable_edit_any_user_configuration', false)) {
        $redirect = false;
    }

    // Maybe redirect back to profile page
    if (true === $redirect) {
        wp_safe_redirect(video_central_get_user_profile_url(video_central_get_displayed_user_id()));
        wp_die();
    }
}

/**
 * Check if a user is blocked, or cannot spectate the videos.
 *
 * @since 1.0.0
 *
 * @uses is_user_logged_in() To check if user is logged in
 * @uses video_central_is_user_keymaster() To check if user is a keymaster
 * @uses current_user_can() To check if the current user can spectate
 * @uses is_video_central() To check if in a Video Central section of the site
 * @uses video_central_set_404() To set a 404 status
 */
function video_central_video_enforce_blocked()
{

    // Bail if not logged in or keymaster
    if (!is_user_logged_in() || video_central_is_user_keymaster()) {
        return;
    }

    // Set 404 if in Video Central and user cannot spectate
    if (is_video_central() && !current_user_can('spectate')) {
        video_central_set_404();
    }
}

/** Converter *****************************************************************/

/**
 * Convert passwords from previous platfrom encryption to WordPress encryption.
 *
 * @since 1.0.0
 *
 * @global WPDB $wpdb
 */
function video_central_user_maybe_convert_pass()
{

    // Bail if no username
    $username = !empty($_POST['log']) ? $_POST['log'] : '';
    if (empty($username)) {
        return;
    }

    global $wpdb;

    // Bail if no user password to convert
    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->users} INNER JOIN {$wpdb->usermeta} ON user_id = ID WHERE meta_key = '_video_central_class' AND user_login = '%s' LIMIT 1", $username));
    if (empty($row) || is_wp_error($row)) {
        return;
    }

    // Setup admin (to include converter)
    require_once video_central()->includes_dir.'admin/admin.php';

    // Create the admin object
    video_central_admin();

    // Convert password
    require_once video_central()->admin->admin_dir.'converter.php';
    require_once video_central()->admin->admin_dir.'converters/'.$row->meta_value.'.php';

    // Create the converter
    $converter = video_central_new_converter($row->meta_value);

    // Try to call the conversion method
    if (is_a($converter, 'Video_Central_Converter_Base') && method_exists($converter, 'callback_pass')) {
        $converter->callback_pass($username, $_POST['pwd']);
    }
}
