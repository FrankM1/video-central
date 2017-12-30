<?php

/**
 * Video Central User Template Tags.
 */

/** Users *********************************************************************/

/**
 * Output a validated user id.
 *
 * @since 1.0.0
 *
 * @param int  $user_id                 Optional. User id
 * @param bool $displayed_user_fallback Fallback on displayed user?
 * @param bool $current_user_fallback   Fallback on current user?
 *
 * @uses video_central_get_user_id() To get the user id
 */
function video_central_user_id($user_id = 0, $displayed_user_fallback = true, $current_user_fallback = false)
{
    echo video_central_get_user_id($user_id, $displayed_user_fallback, $current_user_fallback);
}
    /**
     * Return a validated user id.
     *
     * @since 1.0.0
     *
     * @param int  $user_id                 Optional. User id
     * @param bool $displayed_user_fallback Fallback on displayed user?
     * @param bool $current_user_fallback   Fallback on current user?
     *
     * @uses get_query_var() To get the 'video_central_user_id' query var
     * @uses apply_filters() Calls 'video_central_get_user_id' with the user id
     *
     * @return int Validated user id
     */
    function video_central_get_user_id($user_id = 0, $displayed_user_fallback = true, $current_user_fallback = false)
    {
        $video_central = video_central();

        // Easy empty checking
        if (!empty($user_id) && is_numeric($user_id)) {
            $video_central_user_id = $user_id;

        // Currently viewing or editing a user
        } elseif ((true === $displayed_user_fallback) && !empty($video_central->displayed_user->ID)) {
            $video_central_user_id = $video_central->displayed_user->ID;

        // Maybe fallback on the current_user ID
        } elseif ((true === $current_user_fallback) && !empty($video_central->current_user->ID)) {
            $video_central_user_id = $video_central->current_user->ID;

        // Failsafe
        } else {
            $video_central_user_id = 0;
        }

        return (int) apply_filters(__FUNCTION__, (int) $video_central_user_id, $displayed_user_fallback, $current_user_fallback);
    }

/**
 * Output ID of current user.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_current_user_id() To get the current user id
 */
function video_central_current_user_id()
{
    echo video_central_get_current_user_id();
}
    /**
     * Return ID of current user.
     *
     * @since 1.0.0
     *
     * @uses video_central_get_user_id() To get the current user id
     * @uses apply_filters() Calls 'video_central_get_current_user_id' with the id
     *
     * @return int Current user id
     */
    function video_central_get_current_user_id()
    {
        return apply_filters(__FUNCTION__, video_central_get_user_id(0, false, true));
    }

/**
 * Output ID of displayed user.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_displayed_user_id() To get the displayed user id
 */
function video_central_displayed_user_id()
{
    echo video_central_get_displayed_user_id();
}
    /**
     * Return ID of displayed user.
     *
     * @since 1.0.0
     *
     * @uses video_central_get_user_id() To get the displayed user id
     * @uses apply_filters() Calls 'video_central_get_displayed_user_id' with the id
     *
     * @return int Displayed user id
     */
    function video_central_get_displayed_user_id()
    {
        return apply_filters(__FUNCTION__, video_central_get_user_id(0, true, false));
    }

/**
 * Output a sanitized user field value.
 *
 * This function relies on the $filter parameter to decide how to sanitize
 * the field value that it finds. Since it uses the WP_User object's magic
 * __get() method, it can also be used to get user_meta values.
 *
 * @since 1.0.0
 *
 * @param string $field  Field to get
 * @param string $filter How to filter the field value (null|raw|db|display|edit)
 *
 * @uses video_central_get_displayed_user_field() To get the field
 */
function video_central_displayed_user_field($field = '', $filter = 'display')
{
    echo video_central_get_displayed_user_field($field, $filter);
}
    /**
     * Return a sanitized user field value.
     *
     * This function relies on the $filter parameter to decide how to sanitize
     * the field value that it finds. Since it uses the WP_User object's magic
     * __get() method, it can also be used to get user_meta values.
     *
     * @since 1.0.0
     *
     * @param string $field  Field to get
     * @param string $filter How to filter the field value (null|raw|db|display|edit)
     *
     * @see WP_User::__get() for more on how the value is retrieved
     * @see sanitize_user_field() for more on how the value is sanitized
     *
     * @uses apply_filters() Calls 'video_central_get_displayed_user_field' with the value
     *
     * @return string|bool Value of the field if it exists, else false
     */
    function video_central_get_displayed_user_field($field = '', $filter = 'display')
    {

        // Get the displayed user
        $user = video_central()->displayed_user;

        // Juggle the user filter property because we don't want to muck up how
        // other code might interact with this object.
        $old_filter = $user->filter;
        $user->filter = $filter;

        // Get the field value from the WP_User object. We don't need to perform
        // an isset() because the WP_User::__get() does it for us.
        $value = $user->$field;

        // Put back the user filter property that was previously juggled above.
        $user->filter = $old_filter;

        // Return empty
        return apply_filters(__FUNCTION__, $value, $field);
    }

/**
 * Output name of current user.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_current_user_name() To get the current user name
 */
function video_central_current_user_name()
{
    echo video_central_get_current_user_name();
}
    /**
     * Return name of current user.
     *
     * @since 1.0.0
     *
     * @uses apply_filters() Calls 'video_central_get_current_user_name' with the
     *                        current user name
     *
     * @return string
     */
    function video_central_get_current_user_name()
    {
        global $user_identity;

        $current_user_name = is_user_logged_in() ? $user_identity : __('Anonymous', 'video_central');

        return apply_filters(__FUNCTION__, $current_user_name);
    }

/**
 * Output avatar of current user.
 *
 * @since 1.0.0
 *
 * @param int $size Size of the avatar. Defaults to 40
 *
 * @uses video_central_get_current_user_avatar() To get the current user avatar
 */
function video_central_current_user_avatar($size = 40)
{
    echo video_central_get_current_user_avatar($size);
}

    /**
     * Return avatar of current user.
     *
     * @since 1.0.0
     *
     * @param int $size Size of the avatar. Defaults to 40
     *
     * @uses video_central_get_current_user_id() To get the current user id
     * @uses video_central_get_current_anonymous_user_data() To get the current
     *                                              anonymous user's email
     * @uses get_avatar() To get the avatar
     * @uses apply_filters() Calls 'video_central_get_current_user_avatar' with the
     *                        avatar and size
     *
     * @return string Current user avatar
     */
    function video_central_get_current_user_avatar($size = 40)
    {
        $user = video_central_get_current_user_id();
        if (empty($user)) {
            $user = video_central_get_current_anonymous_user_data('email');
        }

        $avatar = get_avatar($user, $size);

        return apply_filters(__FUNCTION__, $avatar, $size);
    }

/**
 * Output link to the profile page of a user.
 *
 * @since 1.0.0
 *
 * @param int $user_id Optional. User id
 *
 * @uses video_central_get_user_profile_link() To get user profile link
 */
function video_central_user_profile_link($user_id = 0)
{
    echo video_central_get_user_profile_link($user_id);
}
    /**
     * Return link to the profile page of a user.
     *
     * @since 1.0.0
     *
     * @param int $user_id Optional. User id
     *
     * @uses video_central_get_user_id() To get user id
     * @uses get_userdata() To get user data
     * @uses video_central_get_user_profile_url() To get user profile url
     * @uses apply_filters() Calls 'video_central_get_user_profile_link' with the user
     *                        profile link and user id
     *
     * @return string User profile link
     */
    function video_central_get_user_profile_link($user_id = 0)
    {

        // Validate user id
        $user_id = video_central_get_user_id($user_id);
        if (empty($user_id)) {
            return false;
        }

        $user = get_userdata($user_id);
        $user_link = '<a href="'.esc_url(video_central_get_user_profile_url($user_id)).'">'.esc_html($user->display_name).'</a>';

        return apply_filters(__FUNCTION__, $user_link, $user_id);
    }

/**
 * Output a users nicename to the screen.
 *
 * @since 1.0.0
 *
 * @param int   $user_id User ID whose nicename to get
 * @param array $args    before|after|user_id|force
 */
function video_central_user_nicename($user_id = 0, $args = array())
{
    echo video_central_get_user_nicename($user_id, $args);
}
    /**
     * Return a users nicename to the screen.
     *
     * @since 1.0.0
     *
     * @param int   $user_id User ID whose nicename to get
     * @param array $args    before|after|user_id|force
     *
     * @return string User nicename, maybe wrapped in before/after strings
     */
    function video_central_get_user_nicename($user_id = 0, $args = array())
    {

        // Bail if no user ID passed
        $user_id = video_central_get_user_id($user_id);
        if (empty($user_id)) {
            return false;
        }

        // Parse default arguments
        $r = video_central_parse_args($args, array(
            'user_id' => $user_id,
            'before' => '',
            'after' => '',
            'force' => '',
        ), 'get_user_nicename');

        // Get the user data and nicename
        if (empty($r['force'])) {
            $user = get_userdata($user_id);
            $nicename = $user->user_nicename;

        // Force the nicename to something else
        } else {
            $nicename = (string) $r['force'];
        }

        // Maybe wrap the nicename
        $retval = !empty($nicename) ? ($r['before'].$nicename.$r['after']) : '';

        // Filter and return
        return (string) apply_filters(__FUNCTION__, $retval, $user_id, $r);
    }

/**
 * Output URL to the profile page of a user.
 *
 * @since 1.0.0
 *
 * @param int    $user_id       Optional. User id
 * @param string $user_nicename Optional. User nicename
 *
 * @uses video_central_get_user_profile_url() To get user profile url
 */
function video_central_user_profile_url($user_id = 0, $user_nicename = '')
{
    echo esc_url(video_central_get_user_profile_url($user_id, $user_nicename));
}
    /**
     * Return URL to the profile page of a user.
     *
     * @since 1.0.0
     *
     * @param int    $user_id       Optional. User id
     * @param string $user_nicename Optional. User nicename
     *
     * @uses video_central_get_user_id() To get user id
     * @uses WP_Rewrite::using_permalinks() To check if the blog is using
     *                                       permalinks
     * @uses add_query_arg() To add custom args to the url
     * @uses home_url() To get blog home url
     * @uses apply_filters() Calls 'video_central_get_user_profile_url' with the user
     *                        profile url, user id and user nicename
     *
     * @return string User profile url
     */
    function video_central_get_user_profile_url($user_id = 0, $user_nicename = '')
    {
        global $wp_rewrite;

        // Use displayed user ID if there is one, and one isn't requested
        $user_id = video_central_get_user_id($user_id);
        if (empty($user_id)) {
            return false;
        }

        // Allow early overriding of the profile URL to cut down on processing
        $early_profile_url = apply_filters('video_central_pre_get_user_profile_url', (int) $user_id);
        if (is_string($early_profile_url)) {
            return $early_profile_url;
        }

        // Pretty permalinks
        if ($wp_rewrite->using_permalinks()) {
            $url = $wp_rewrite->root.video_central_get_user_slug().'/%'.video_central_get_user_rewrite_id().'%';

            // Get username if not passed
            if (empty($user_nicename)) {
                $user_nicename = video_central_get_user_nicename($user_id);
            }

            $url = str_replace('%'.video_central_get_user_rewrite_id().'%', $user_nicename, $url);
            $url = home_url(user_trailingslashit($url));

        // Unpretty permalinks
        } else {
            $url = add_query_arg(array(video_central_get_user_rewrite_id() => $user_id), home_url('/'));
        }

        return apply_filters(__FUNCTION__, $url, $user_id, $user_nicename);
    }

/**
 * Output link to the profile edit page of a user.
 *
 * @since 1.0.0
 *
 * @param int $user_id Optional. User id
 *
 * @uses video_central_get_user_profile_edit_link() To get user profile edit link
 */
function video_central_user_profile_edit_link($user_id = 0)
{
    echo video_central_get_user_profile_edit_link($user_id);
}
    /**
     * Return link to the profile edit page of a user.
     *
     * @since 1.0.0
     *
     * @param int $user_id Optional. User id
     *
     * @uses video_central_get_user_id() To get user id
     * @uses get_userdata() To get user data
     * @uses video_central_get_user_profile_edit_url() To get user profile edit url
     * @uses apply_filters() Calls 'video_central_get_user_profile_link' with the edit
     *                        link and user id
     *
     * @return string User profile edit link
     */
    function video_central_get_user_profile_edit_link($user_id = 0)
    {

        // Validate user id
        $user_id = video_central_get_user_id($user_id);
        if (empty($user_id)) {
            return false;
        }

        $user = get_userdata($user_id);
        $edit_link = '<a href="'.esc_url(video_central_get_user_profile_url($user_id)).'">'.esc_html($user->display_name).'</a>';

        return apply_filters(__FUNCTION__, $edit_link, $user_id);
    }

/**
 * Output URL to the profile edit page of a user.
 *
 * @since 1.0.0
 *
 * @param int    $user_id       Optional. User id
 * @param string $user_nicename Optional. User nicename
 *
 * @uses video_central_get_user_profile_edit_url() To get user profile edit url
 */
function video_central_user_profile_edit_url($user_id = 0, $user_nicename = '')
{
    echo esc_url(video_central_get_user_profile_edit_url($user_id, $user_nicename));
}
    /**
     * Return URL to the profile edit page of a user.
     *
     * @since 1.0.0
     *
     * @param int    $user_id       Optional. User id
     * @param string $user_nicename Optional. User nicename
     *
     * @uses video_central_get_user_id() To get user id
     * @uses WP_Rewrite::using_permalinks() To check if the blog is using
     *                                       permalinks
     * @uses add_query_arg() To add custom args to the url
     * @uses home_url() To get blog home url
     * @uses apply_filters() Calls 'video_central_get_user_edit_profile_url' with the
     *                        edit profile url, user id and user nicename
     *
     * @return string
     */
    function video_central_get_user_profile_edit_url($user_id = 0, $user_nicename = '')
    {
        global $wp_rewrite;

        $video_central = video_central();
        $user_id = video_central_get_user_id($user_id);
        if (empty($user_id)) {
            return false;
        }

        // Pretty permalinks
        if ($wp_rewrite->using_permalinks()) {
            $url = $wp_rewrite->root.video_central_get_user_slug().'/%'.$video_central->user_id.'%/'.$video_central->edit_id;

            // Get username if not passed
            if (empty($user_nicename)) {
                $user = get_userdata($user_id);
                if (!empty($user->user_nicename)) {
                    $user_nicename = $user->user_nicename;
                }
            }

            $url = str_replace('%'.$video_central->user_id.'%', $user_nicename, $url);
            $url = home_url(user_trailingslashit($url));

        // Unpretty permalinks
        } else {
            $url = add_query_arg(array($video_central->user_id => $user_id, $video_central->edit_id => '1'), home_url('/'));
        }

        return apply_filters(__FUNCTION__, $url, $user_id, $user_nicename);
    }

/**
 * Output a user's main role for display.
 *
 * @since 1.0.0
 *
 * @param int $user_id
 *
 * @uses video_central_get_user_display_role To get the user display role
 */
function video_central_user_display_role($user_id = 0)
{
    echo video_central_get_user_display_role($user_id);
}
    /**
     * Return a user's main role for display.
     *
     * @since 1.0.0
     *
     * @param int $user_id
     *
     * @uses video_central_get_user_id() to verify the user ID
     * @uses video_central_is_user_inactive() to check if user is inactive
     * @uses user_can() to check if user has special capabilities
     * @uses apply_filters() Calls 'video_central_get_user_display_role' with the
     *                        display role, user id, and user role
     *
     * @return string
     */
    function video_central_get_user_display_role($user_id = 0)
    {

        // Validate user id
        $user_id = video_central_get_user_id($user_id);

        // User is not registered
        if (empty($user_id)) {
            $role = __('Guest', 'video_central');

        // User is not active
        } elseif (video_central_is_user_inactive($user_id)) {
            $role = __('Inactive', 'video_central');

        // User have a role
        } else {
            $role_id = video_central_get_user_role($user_id);
            $role = video_central_get_dynamic_role_name($role_id);
        }

        // No role found so default to generic "Member"
        if (empty($role)) {
            $role = __('Member', 'video_central');
        }

        return apply_filters(__FUNCTION__, $role, $user_id);
    }

/**
 * Output the link to the admin section.
 *
 * @since 1.0.0
 *
 * @param mixed $args Optional. See {@link video_central_get_admin_link()}
 *
 * @uses video_central_get_admin_link() To get the admin link
 */
function video_central_admin_link($args = '')
{
    echo video_central_get_admin_link($args);
}
    /**
     * Return the link to the admin section.
     *
     * @since 1.0.0
     *
     * @param mixed $args Optional. This function supports these arguments:
     *                    - text: The text
     *                    - before: Before the lnk
     *                    - after: After the link
     *
     * @uses current_user_can() To check if the current user can moderate
     * @uses admin_url() To get the admin url
     * @uses apply_filters() Calls 'video_central_get_admin_link' with the link & args
     *
     * @return The link
     */
    function video_central_get_admin_link($args = '')
    {
        if (!current_user_can('moderate')) {
            return;
        }

        if (!empty($args) && is_string($args) && (false === strpos($args, '='))) {
            $args = array('text' => $args);
        }

        // Parse arguments against default values
        $r = video_central_parse_args($args, array(
            'text' => __('Admin', 'video_central'),
            'before' => '',
            'after' => '',
        ), 'get_admin_link');

        $retval = $r['before'].'<a href="'.esc_url(admin_url()).'">'.$r['text'].'</a>'.$r['after'];

        return apply_filters(__FUNCTION__, $retval, $r);
    }

/** User IP *******************************************************************/

/**
 * Output the author IP address of a post.
 *
 * @since 1.0.0
 *
 * @param mixed $args Optional. If it is an integer, it is used as post id.
 *
 * @uses video_central_get_author_ip() To get the post author link
 */
function video_central_author_ip($args = '')
{
    echo video_central_get_author_ip($args);
}
    /**
     * Return the author IP address of a post.
     *
     * @since 1.0.0
     *
     * @param mixed $args Optional. If an integer, it is used as reply id.
     *
     * @uses get_post_meta() To check if it's a video page
     *
     * @return string Author link of reply
     */
    function video_central_get_author_ip($args = '')
    {

        // Used as post id
        $post_id = is_numeric($args) ? (int) $args : 0;

        // Parse arguments against default values
        $r = video_central_parse_args($args, array(
            'post_id' => $post_id,
            'before' => '<span class="video-central-author-ip">(',
            'after' => ')</span>',
        ), 'get_author_ip');

        // Get the author IP meta value
        $author_ip = get_post_meta($r['post_id'], '_video_central_author_ip', true);
        if (!empty($author_ip)) {
            $author_ip = $r['before'].$author_ip.$r['after'];

        // No IP address
        } else {
            $author_ip = '';
        }

        return apply_filters(__FUNCTION__, $author_ip, $r);
    }

/** Anonymous Fields **********************************************************/

/**
 * Output the author display-name of a video or reply.
 *
 * Convenience function to ensure proper template functions are called
 * and correct filters are executed. Used primarily to display video
 * and reply author information in the anonymous form template-part.
 *
 * @since 1.0.0
 *
 * @param int $post_id
 *
 * @uses video_central_get_author_display_name() to get the author name
 */
function video_central_author_display_name($post_id = 0)
{
    echo video_central_get_author_display_name($post_id);
}

    /**
     * Return the author name of a video or reply.
     *
     * Convenience function to ensure proper template functions are called
     * and correct filters are executed. Used primarily to display video
     * and reply author information in the anonymous form template-part.
     *
     * @since 1.0.0
     *
     * @param int $post_id
     *
     * @uses video_central_is_video_edit()
     * @uses video_central_get_video_author_display_name()
     * @uses video_central_is_reply_edit()
     * @uses video_central_get_reply_author_display_name()
     * @uses video_central_current_anonymous_user_data()
     *
     * @return string The name of the author
     */
    function video_central_get_author_display_name($post_id = 0)
    {

        // Define local variable(s)
        $retval = '';

        // Topic edit
        if (video_central_is_video_edit()) {
            $retval = video_central_get_video_author_display_name($post_id);

        // Reply edit
        } elseif (video_central_is_reply_edit()) {
            $retval = video_central_get_reply_author_display_name($post_id);

        // Not an edit, so rely on current user cookie data
        } else {
            $retval = video_central_current_anonymous_user_data('name');
        }

        return apply_filters(__FUNCTION__, $retval, $post_id);
    }

/**
 * Output the author email of a video or reply.
 *
 * Convenience function to ensure proper template functions are called
 * and correct filters are executed. Used primarily to display video
 * and reply author information in the anonymous user form template-part.
 *
 * @since 1.0.0
 *
 * @param int $post_id
 *
 * @uses video_central_get_author_email() to get the author email
 */
function video_central_author_email($post_id = 0)
{
    echo video_central_get_author_email($post_id);
}

    /**
     * Return the author email of a video or reply.
     *
     * Convenience function to ensure proper template functions are called
     * and correct filters are executed. Used primarily to display video
     * and reply author information in the anonymous user form template-part.
     *
     * @since 1.0.0
     *
     * @param int $post_id
     *
     * @uses video_central_is_video_edit()
     * @uses video_central_get_video_author_email()
     * @uses video_central_is_reply_edit()
     * @uses video_central_get_reply_author_email()
     * @uses video_central_current_anonymous_user_data()
     *
     * @return string The email of the author
     */
    function video_central_get_author_email($post_id = 0)
    {

        // Define local variable(s)
        $retval = '';

        // Topic edit
        if (video_central_is_video_edit()) {
            $retval = video_central_get_video_author_email($post_id);

        // Reply edit
        } elseif (video_central_is_reply_edit()) {
            $retval = video_central_get_reply_author_email($post_id);

        // Not an edit, so rely on current user cookie data
        } else {
            $retval = video_central_current_anonymous_user_data('email');
        }

        return apply_filters(__FUNCTION__, $retval, $post_id);
    }

/**
 * Output the author url of a video or reply.
 *
 * Convenience function to ensure proper template functions are called
 * and correct filters are executed. Used primarily to display video
 * and reply author information in the anonymous user form template-part.
 *
 * @since 1.0.0
 *
 * @param int $post_id
 *
 * @uses video_central_get_author_url() to get the author url
 */
function video_central_author_url($post_id = 0)
{
    echo video_central_get_author_url($post_id);
}

    /**
     * Return the author url of a video or reply.
     *
     * Convenience function to ensure proper template functions are called
     * and correct filters are executed. Used primarily to display video
     * and reply author information in the anonymous user form template-part.
     *
     * @since 1.0.0
     *
     * @param int $post_id
     *
     * @uses video_central_is_video_edit()
     * @uses video_central_get_video_author_url()
     * @uses video_central_is_reply_edit()
     * @uses video_central_get_reply_author_url()
     * @uses video_central_current_anonymous_user_data()
     *
     * @return string The url of the author
     */
    function video_central_get_author_url($post_id = 0)
    {

        // Define local variable(s)
        $retval = '';

        // Topic edit
        if (video_central_is_video_edit()) {
            $retval = video_central_get_video_author_url($post_id);

        // Reply edit
        } elseif (video_central_is_reply_edit()) {
            $retval = video_central_get_reply_author_url($post_id);

        // Not an edit, so rely on current user cookie data
        } else {
            $retval = video_central_current_anonymous_user_data('url');
        }

        return apply_filters(__FUNCTION__, $retval, $post_id);
    }

/** Favorites *****************************************************************/

/**
 * Output the link to the user's favorites page (profile page).
 *
 * @since 1.0.0
 *
 * @param int $user_id Optional. User id
 *
 * @uses video_central_get_favorites_permalink() To get the favorites permalink
 */
function video_central_favorites_permalink($user_id = 0)
{
    echo esc_url(video_central_get_favorites_permalink($user_id));
}
    /**
     * Return the link to the user's favorites page (profile page).
     *
     * @since 1.0.0
     *
     * @param int $user_id Optional. User id
     *
     * @uses video_central_get_user_profile_url() To get the user profile url
     * @uses apply_filters() Calls 'video_central_get_favorites_permalink' with the
     *                        user profile url and user id
     *
     * @return string Permanent link to user profile page
     */
    function video_central_get_favorites_permalink($user_id = 0)
    {
        global $wp_rewrite;

        // Use displayed user ID if there is one, and one isn't requested
        $user_id = video_central_get_user_id($user_id);
        if (empty($user_id)) {
            return false;
        }

        // Allow early overriding of the profile URL to cut down on processing
        $early_profile_url = apply_filters('video_central_pre_get_favorites_permalink', (int) $user_id);
        if (is_string($early_profile_url)) {
            return $early_profile_url;
        }

        // Pretty permalinks
        if ($wp_rewrite->using_permalinks()) {
            $url = $wp_rewrite->root.video_central_get_user_slug().'/%'.video_central_get_user_rewrite_id().'%/%'.video_central_get_user_favorites_rewrite_id().'%';
            $user = get_userdata($user_id);
            if (!empty($user->user_nicename)) {
                $user_nicename = $user->user_nicename;
            } else {
                $user_nicename = $user->user_login;
            }
            $url = str_replace('%'.video_central_get_user_rewrite_id().'%', $user_nicename, $url);
            $url = str_replace('%'.video_central_get_user_favorites_rewrite_id().'%', video_central_get_user_favorites_slug(), $url);
            $url = home_url(user_trailingslashit($url));

        // Unpretty permalinks
        } else {
            $url = add_query_arg(array(
                video_central_get_user_rewrite_id() => $user_id,
                video_central_get_user_favorites_rewrite_id() => video_central_get_user_favorites_slug(),
            ), home_url('/'));
        }

        return apply_filters(__FUNCTION__, $url, $user_id);
    }

/**
 * Output the link to make a video favorite/remove a video from favorites.
 *
 * @since 1.0.0
 *
 * @param mixed $args    See {@link video_central_get_user_favorites_link()}
 * @param int   $user_id Optional. User id
 * @param bool  $wrap    Optional. If you want to wrap the link in <span id="favorite-toggle">.
 *
 * @uses video_central_get_user_favorites_link() To get the user favorites link
 */
function video_central_user_favorites_link($args = array(), $user_id = 0, $wrap = true)
{
    echo video_central_get_user_favorites_link($args, $user_id, $wrap);
}
    /**
     * User favorites link.
     *
     * Return the link to make a video favorite/remove a video from
     * favorites
     *
     * @since 1.0.0
     *
     * @param mixed $args     This function supports these arguments:
     *                        - subscribe: Favorite text
     *                        - unsubscribe: Unfavorite text
     *                        - user_id: User id
     *                        - video_id: Topic id
     *                        - before: Before the link
     *                        - after: After the link
     * @param int   $user_id  Optional. User id
     * @param int   $video_id Optional. Topic id
     * @param bool  $wrap     Optional. If you want to wrap the link in <span id="favorite-toggle">. See ajax_favorite()
     *
     * @uses video_central_get_user_id() To get the user id
     * @uses current_user_can() If the current user can edit the user
     * @uses video_central_get_video_id() To get the video id
     * @uses video_central_is_user_favorite() To check if the video is user's favorite
     * @uses video_central_get_favorites_permalink() To get the favorites permalink
     * @uses video_central_get_video_permalink() To get the video permalink
     * @uses video_central_is_favorites() Is it the favorites page?
     * @uses apply_filters() Calls 'video_central_get_user_favorites_link' with the
     *                        html, add args, remove args, user & video id
     *
     * @return string User favorites link
     */
    function video_central_get_user_favorites_link($args = '', $user_id = 0, $wrap = true)
    {
        if (!video_central_is_favorites_active()) {
            return false;
        }

        // Parse arguments against default values
        $r = video_central_parse_args($args, array(
            'favorite' => __('Favorite',  'video_central'),
            'favorited' => __('Favorited', 'video_central'),
            'user_id' => 0,
            'video_id' => 0,
            'before' => '',
            'after' => '',
        ), 'get_user_favorites_link');

        // Validate user and video ID's
        $user_id = video_central_get_user_id($r['user_id'], true, true);
        $video_id = video_central_get_video_id($r['video_id']);
        if (empty($user_id) || empty($video_id)) {
            return false;
        }

        // No link if you can't edit yourself
        if (!current_user_can('edit_user', (int) $user_id)) {
            return false;
        }

        // Decide which link to show
        $is_fav = video_central_is_user_favorite($user_id, $video_id);
        if (!empty($is_fav)) {
            $text = $r['favorited'];
            $query_args = array('action' => 'video_central_favorite_remove', 'video_id' => $video_id);
        } else {
            $text = $r['favorite'];
            $query_args = array('action' => 'video_central_favorite_add',    'video_id' => $video_id);
        }

        // Create the link based where the user is and if the video is
        // already the user's favorite
        if (video_central_is_favorites()) {
            $permalink = video_central_get_favorites_permalink($user_id);
        } elseif (video_central_is_single_video() || video_central_is_single_reply()) {
            $permalink = video_central_get_video_permalink($video_id);
        } else {
            $permalink = get_permalink();
        }

        $url = esc_url(wp_nonce_url(add_query_arg($query_args, $permalink), 'toggle-favorite_'.$video_id));
        $sub = $is_fav ? ' class="is-favorite"' : '';
        $html = sprintf('%s<span id="favorite-%d"  %s><a href="%s" class="favorite-toggle" data-video="%d">%s</a></span>%s', $r['before'], $video_id, $sub, $url, $video_id, $text, $r['after']);

        // Initial output is wrapped in a span, ajax output is hooked to this
        if (!empty($wrap)) {
            $html = '<span id="favorite-toggle">'.$html.'</span>';
        }

        // Return the link
        return apply_filters(__FUNCTION__, $html, $r, $user_id, $video_id);
    }

/** Subscriptions *************************************************************/

/**
 * Output the link to the user's subscriptions page (profile page).
 *
 * @since 1.0.0
 *
 * @param int $user_id Optional. User id
 *
 * @uses video_central_get_subscriptions_permalink() To get the subscriptions link
 */
function video_central_subscriptions_permalink($user_id = 0)
{
    echo esc_url(video_central_get_subscriptions_permalink($user_id));
}
    /**
     * Return the link to the user's subscriptions page (profile page).
     *
     * @since 1.0.0
     *
     * @param int $user_id Optional. User id
     *
     * @uses video_central_get_user_profile_url() To get the user profile url
     * @uses apply_filters() Calls 'video_central_get_subscriptions_permalink' with
     *                        the user profile url and user id
     *
     * @return string Permanent link to user subscriptions page
     */
    function video_central_get_subscriptions_permalink($user_id = 0)
    {
        global $wp_rewrite;

        // Use displayed user ID if there is one, and one isn't requested
        $user_id = video_central_get_user_id($user_id);
        if (empty($user_id)) {
            return false;
        }

        // Allow early overriding of the profile URL to cut down on processing
        $early_profile_url = apply_filters('video_central_pre_get_subscriptions_permalink', (int) $user_id);
        if (is_string($early_profile_url)) {
            return $early_profile_url;
        }

        // Pretty permalinks
        if ($wp_rewrite->using_permalinks()) {
            $url = $wp_rewrite->root.video_central_get_user_slug().'/%'.video_central_get_user_rewrite_id().'%/%'.video_central_get_user_subscriptions_rewrite_id().'%';
            $user = get_userdata($user_id);
            if (!empty($user->user_nicename)) {
                $user_nicename = $user->user_nicename;
            } else {
                $user_nicename = $user->user_login;
            }
            $url = str_replace('%'.video_central_get_user_rewrite_id().'%', $user_nicename,                    $url);
            $url = str_replace('%'.video_central_get_user_subscriptions_rewrite_id().'%', video_central_get_user_subscriptions_slug(), $url);
            $url = home_url(user_trailingslashit($url));

        // Unpretty permalinks
        } else {
            $url = add_query_arg(array(
                video_central_get_user_rewrite_id() => $user_id,
                video_central_get_user_subscriptions_rewrite_id() => video_central_get_user_subscriptions_slug(),
            ), home_url('/'));
        }

        return apply_filters(__FUNCTION__, $url, $user_id);
    }

/**
 * Output the link to subscribe/unsubscribe from a video.
 *
 * @since 1.0.0
 *
 * @param mixed $args    See {@link video_central_get_user_subscribe_link()}
 * @param int   $user_id Optional. User id
 * @param bool  $wrap    Optional. If you want to wrap the link in <span id="subscription-toggle">.
 *
 * @uses video_central_get_user_subscribe_link() To get the subscribe link
 */
function video_central_user_subscribe_link($args = '', $user_id = 0, $wrap = true)
{
    echo video_central_get_user_subscribe_link($args, $user_id, $wrap);
}
    /**
     * Return the link to subscribe/unsubscribe from a video or video.
     *
     * @since 1.0.0
     *
     * @param mixed $args    This function supports these arguments:
     *                       - subscribe: Subscribe text
     *                       - unsubscribe: Unsubscribe text
     *                       - user_id: User id
     *                       - video_id: Topic id
     *                       - video_id: Forum id
     *                       - before: Before the link
     *                       - after: After the link
     * @param int   $user_id Optional. User id
     * @param bool  $wrap    Optional. If you want to wrap the link in <span id="subscription-toggle">.
     *
     * @uses video_central_is_subscriptions_active() to check if subscriptions are active
     * @uses video_central_get_user_id() To get the user id
     * @uses video_central_get_user_id() To get the user id
     * @uses video_central_get_video_id() To get the video id
     * @uses video_central_get_video_id() To get the video id
     * @uses current_user_can() To check if the current user can edit user
     * @uses video_central_is_user_subscribed_to_video() To check if the user is subscribed to the video
     * @uses video_central_is_user_subscribed_to_video() To check if the user is subscribed to the video
     * @uses video_central_is_subscriptions() To check if it's the subscriptions page
     * @uses video_central_get_subscriptions_permalink() To get subscriptions link
     * @uses video_central_get_video_permalink() To get video link
     * @uses apply_filters() Calls 'video_central_get_user_subscribe_link' with the
     *                        link, args, user id & video id
     *
     * @return string Permanent link to video
     */
    function video_central_get_user_subscribe_link($args = '', $user_id = 0, $wrap = true)
    {
        if (!video_central_is_subscriptions_active()) {
            return;
        }

        // Parse arguments against default values
        $r = video_central_parse_args($args, array(
            'subscribe' => __('Subscribe',   'video_central'),
            'unsubscribe' => __('Unsubscribe', 'video_central'),
            'user_id' => 0,
            'video_id' => 0,
            'video_id' => 0,
            'before' => '&nbsp;|&nbsp;',
            'after' => '',
        ), 'get_user_subscribe_link');

        // Validate user and object ID's
        $user_id = video_central_get_user_id($r['user_id'], true, true);
        $video_id = video_central_get_video_id($r['video_id']);
        $video_id = video_central_get_video_id($r['video_id']);
        if (empty($user_id) || (empty($video_id) && empty($video_id))) {
            return false;
        }

        // No link if you can't edit yourself
        if (!current_user_can('edit_user', (int) $user_id)) {
            return false;
        }

        // Check if viewing a single video
        if (empty($video_id) && !empty($video_id)) {

            // Decide which link to show
            $is_subscribed = video_central_is_user_subscribed_to_video($user_id, $video_id);
            if (!empty($is_subscribed)) {
                $text = $r['unsubscribe'];
                $query_args = array('action' => 'video_central_unsubscribe', 'video_id' => $video_id);
            } else {
                $text = $r['subscribe'];
                $query_args = array('action' => 'video_central_subscribe',   'video_id' => $video_id);
            }

            // Create the link based where the user is and if the user is
            // subscribed already
            if (video_central_is_subscriptions()) {
                $permalink = video_central_get_subscriptions_permalink($user_id);
            } elseif (video_central_is_single_video() || video_central_is_single_reply()) {
                $permalink = video_central_get_video_permalink($video_id);
            } else {
                $permalink = get_permalink();
            }

            $url = esc_url(wp_nonce_url(add_query_arg($query_args, $permalink), 'toggle-subscription_'.$video_id));
            $sub = $is_subscribed ? ' class="is-subscribed"' : '';
            $html = sprintf('%s<span id="subscribe-%d"  %s><a href="%s" class="subscription-toggle" data-video="%d">%s</a></span>%s', $r['before'], $video_id, $sub, $url, $video_id, $text, $r['after']);

            // Initial output is wrapped in a span, ajax output is hooked to this
            if (!empty($wrap)) {
                $html = '<span id="subscription-toggle">'.$html.'</span>';
            }
        } else {

            // Decide which link to show
            $is_subscribed = video_central_is_user_subscribed_to_video($user_id, $video_id);
            if (!empty($is_subscribed)) {
                $text = $r['unsubscribe'];
                $query_args = array('action' => 'video_central_unsubscribe', 'video_id' => $video_id);
            } else {
                $text = $r['subscribe'];
                $query_args = array('action' => 'video_central_subscribe',   'video_id' => $video_id);
            }

            // Create the link based where the user is and if the user is
            // subscribed already
            if (video_central_is_subscriptions()) {
                $permalink = video_central_get_subscriptions_permalink($user_id);
            } elseif (video_central_is_single_video() || video_central_is_single_reply()) {
                $permalink = video_central_get_video_permalink($video_id);
            } else {
                $permalink = get_permalink();
            }

            $url = esc_url(wp_nonce_url(add_query_arg($query_args, $permalink), 'toggle-subscription_'.$video_id));
            $sub = $is_subscribed ? ' class="is-subscribed"' : '';
            $html = sprintf('%s<span id="subscribe-%d"  %s><a href="%s" class="subscription-toggle" data-video="%d">%s</a></span>%s', $r['before'], $video_id, $sub, $url, $video_id, $text, $r['after']);

            // Initial output is wrapped in a span, ajax output is hooked to this
            if (!empty($wrap)) {
                $html = '<span id="subscription-toggle">'.$html.'</span>';
            }
        }

        // Return the link
        return apply_filters(__FUNCTION__, $html, $r, $user_id, $video_id);
    }

/** Edit User *****************************************************************/

/**
 * Edit profile success message.
 *
 * @since 1.0.0
 *
 * @uses video_central_is_single_user() To check if it's the profile page
 * @uses video_central_is_single_user_edit() To check if it's the profile edit page
 */
function video_central_notice_edit_user_success()
{
    if (isset($_GET['updated']) && (video_central_is_single_user() || video_central_is_single_user_edit())) : ?>

    <div class="video-central-template-notice updated">
        <p><?php esc_html_e('User updated.', 'video_central');
    ?></p>
    </div>

    <?php endif;
}

/**
 * Super admin privileges notice.
 *
 * @since 1.0.0
 *
 * @uses is_multisite() To check if the blog is multisite
 * @uses video_central_is_single_user() To check if it's the profile page
 * @uses video_central_is_single_user_edit() To check if it's the profile edit page
 * @uses current_user_can() To check if the current user can manage network
 *                           options
 * @uses video_central_get_displayed_user_id() To get the displayed user id
 * @uses is_super_admin() To check if the user is super admin
 * @uses video_central_is_user_home() To check if it's the user home
 * @uses video_central_is_user_home_edit() To check if it's the user home edit
 */
function video_central_notice_edit_user_is_super_admin()
{
    if (is_multisite() && (video_central_is_single_user() || video_central_is_single_user_edit()) && current_user_can('manage_network_options') && is_super_admin(video_central_get_displayed_user_id())) : ?>

    <div class="video-central-template-notice important">
        <p><?php video_central_is_user_home() || video_central_is_user_home_edit() ? esc_html_e('You have super admin privileges.', 'video_central') : esc_html_e('This user has super admin privileges.', 'video_central');
    ?></p>
    </div>

<?php endif;
}

/**
 * Drop down for selecting the user's display name.
 *
 * @since 1.0.0
 */
function video_central_edit_user_display_name()
{
    $video_central = video_central();
    $public_display = array();
    $public_display['display_username'] = $video_central->displayed_user->user_login;

    if (!empty($video_central->displayed_user->nickname)) {
        $public_display['display_nickname'] = $video_central->displayed_user->nickname;
    }

    if (!empty($video_central->displayed_user->first_name)) {
        $public_display['display_firstname'] = $video_central->displayed_user->first_name;
    }

    if (!empty($video_central->displayed_user->last_name)) {
        $public_display['display_lastname'] = $video_central->displayed_user->last_name;
    }

    if (!empty($video_central->displayed_user->first_name) && !empty($video_central->displayed_user->last_name)) {
        $public_display['display_firstlast'] = $video_central->displayed_user->first_name.' '.$video_central->displayed_user->last_name;
        $public_display['display_lastfirst'] = $video_central->displayed_user->last_name.' '.$video_central->displayed_user->first_name;
    }

    if (!in_array($video_central->displayed_user->display_name, $public_display)) { // Only add this if it isn't duplicated elsewhere
        $public_display = array('display_displayname' => $video_central->displayed_user->display_name) + $public_display;
    }

    $public_display = array_map('trim', $public_display);
    $public_display = array_unique($public_display);
    ?>

    <select name="display_name" id="display_name">

    <?php foreach ($public_display as $id => $item) : ?>

        <option id="<?php echo $id;
    ?>" value="<?php echo esc_attr($item);
    ?>"<?php selected($video_central->displayed_user->display_name, $item);
    ?>><?php echo $item;
    ?></option>

    <?php endforeach;
    ?>

    </select>

<?php

}

/**
 * Output blog role selector (for user edit).
 *
 * @since 1.0.0
 */
function video_central_edit_user_blog_role()
{

    // Return if no user is being edited
    if (!video_central_is_single_user_edit()) {
        return;
    }

    // Get users current blog role
    $user_role = video_central_get_user_blog_role(video_central_get_displayed_user_id());

    // Get the blog roles
    $blog_roles = video_central_get_blog_roles();
    ?>

    <select name="role" id="role">
        <option value=""><?php esc_html_e('&mdash; No role for this site &mdash;', 'video_central');
    ?></option>

        <?php foreach ($blog_roles as $role => $details) : ?>

            <option <?php selected($user_role, $role);
    ?> value="<?php echo esc_attr($role);
    ?>"><?php echo translate_user_role($details['name']);
    ?></option>

        <?php endforeach;
    ?>

    </select>

    <?php

}

/**
 * Output video role selector (for user edit).
 *
 * @since 1.0.0
 */
function video_central_edit_user_videos_role()
{

    // Return if no user is being edited
    if (!video_central_is_single_user_edit()) {
        return;
    }

    // Get the user's current video role
    $user_role = video_central_get_user_role(video_central_get_displayed_user_id());

    // Get the folum roles
    $dynamic_roles = video_central_get_dynamic_roles();

    // Only keymasters can set other keymasters
    if (!video_central_is_user_keymaster()) {
        unset($dynamic_roles[ video_central_get_keymaster_role() ]);
    }
    ?>

    <select name="video-central-videos-role" id="video-central-videos-role">
        <option value=""><?php esc_html_e('&mdash; No role for these videos &mdash;', 'video_central');
    ?></option>

        <?php foreach ($dynamic_roles as $role => $details) : ?>

            <option <?php selected($user_role, $role);
    ?> value="<?php echo esc_attr($role);
    ?>"><?php echo translate_user_role($details['name']);
    ?></option>

        <?php endforeach;
    ?>

    </select>

    <?php

}

/**
 * Return user contact methods Selectbox.
 *
 * @since 1.0.0
 *
 * @uses _wp_get_user_contactmethods() To get the contact methods
 * @uses apply_filters() Calls 'video_central_edit_user_contact_methods' with the methods
 *
 * @return string User contact methods
 */
function video_central_edit_user_contact_methods()
{

    // Get the core WordPress contact methods
    $contact_methods = _wp_get_user_contactmethods(video_central()->displayed_user);

    return apply_filters(__FUNCTION__, $contact_methods);
}

/** Videos Created ************************************************************/

/**
 * Output the link to the user's videos.
 *
 * @since 1.0.0
 *
 * @param int $user_id Optional. User id
 *
 * @uses video_central_get_favorites_permalink() To get the favorites permalink
 */
function video_central_user_videos_created_url($user_id = 0)
{
    echo esc_url(video_central_get_user_videos_created_url($user_id));
}
    /**
     * Return the link to the user's videos.
     *
     * @since 1.0.0
     *
     * @param int $user_id Optional. User id
     *
     * @uses video_central_get_user_profile_url() To get the user profile url
     * @uses apply_filters() Calls 'video_central_get_favorites_permalink' with the
     *                        user profile url and user id
     *
     * @return string Permanent link to user profile page
     */
    function video_central_get_user_videos_created_url($user_id = 0)
    {
        global $wp_rewrite;

        // Use displayed user ID if there is one, and one isn't requested
        $user_id = video_central_get_user_id($user_id);
        if (empty($user_id)) {
            return false;
        }

        // Allow early overriding of the profile URL to cut down on processing
        $early_url = apply_filters('video_central_pre_get_user_videos_created_url', (int) $user_id);
        if (is_string($early_url)) {
            return $early_url;
        }

        // Pretty permalinks
        if ($wp_rewrite->using_permalinks()) {
            $url = $wp_rewrite->root.video_central_get_user_slug().'/%'.video_central_get_user_rewrite_id().'%/'.video_central_get_video_archive_slug();
            $user = get_userdata($user_id);
            if (!empty($user->user_nicename)) {
                $user_nicename = $user->user_nicename;
            } else {
                $user_nicename = $user->user_login;
            }
            $url = str_replace('%'.video_central_get_user_rewrite_id().'%', $user_nicename, $url);
            $url = home_url(user_trailingslashit($url));

        // Unpretty permalinks
        } else {
            $url = add_query_arg(array(
                video_central_get_user_rewrite_id() => $user_id,
                video_central_get_user_videos_rewrite_id() => '1',
            ), home_url('/'));
        }

        return apply_filters(__FUNCTION__, $url, $user_id);
    }

/** Login *********************************************************************/

/**
 * Handle the login and registration template notices.
 *
 * @since 1.0.0
 *
 * @uses WP_Error Video_Central::errors::add() To add an error or message
 */
function video_central_login_notices()
{

    // loggedout was passed
    if (!empty($_GET['loggedout']) && (true === $_GET['loggedout'])) {
        video_central_add_error('loggedout', __('You are now logged out.', 'video_central'), 'message');

    // registration is disabled
    } elseif (!empty($_GET['registration']) && ('disabled' === $_GET['registration'])) {
        video_central_add_error('registerdisabled', __('New user registration is currently not allowed.', 'video_central'));

    // Prompt user to check their email
    } elseif (!empty($_GET['checkemail']) && in_array($_GET['checkemail'], array('confirm', 'newpass', 'registered'))) {
        switch ($_GET['checkemail']) {

            // Email needs confirmation
            case 'confirm' :
                video_central_add_error('confirm',    __('Check your e-mail for the confirmation link.',     'video_central'), 'message');
                break;

            // User requested a new password
            case 'newpass' :
                video_central_add_error('newpass',    __('Check your e-mail for your new password.',         'video_central'), 'message');
                break;

            // User is newly registered
            case 'registered' :
                video_central_add_error('registered', __('Registration complete. Please check your e-mail.', 'video_central'), 'message');
                break;
        }
    }
}

/**
 * Redirect a user back to their profile if they are already logged in.
 *
 * This should be used before {@link get_header()} is called in template files
 * where the user should never have access to the contents of that file.
 *
 * @since 1.0.0
 *
 * @param string $url The URL to redirect to
 *
 * @uses is_user_logged_in() Check if user is logged in
 * @uses wp_safe_redirect() To safely redirect
 * @uses video_central_get_user_profile_url() To get the profile url of the user
 * @uses video_central_get_current_user_id() To get the current user id
 */
function video_central_logged_in_redirect($url = '')
{

    // Bail if user is not logged in
    if (!is_user_logged_in()) {
        return;
    }

    // Setup the profile page to redirect to
    $redirect_to = !empty($url) ? $url : video_central_get_user_profile_url(video_central_get_current_user_id());

    // Do a safe redirect and exit
    wp_safe_redirect($redirect_to);
    wp_die();
}

/**
 * Output the required hidden fields when logging in.
 *
 * @since 1.0.0
 *
 * @uses apply_filters() To allow custom redirection
 * @uses video_central_redirect_to_field() To output the hidden request url field
 * @uses wp_nonce_field() To generate hidden nonce fields
 */
function video_central_user_login_fields()
{
    ?>

        <input type="hidden" name="user-cookie" value="1" />

        <?php

        // Allow custom login redirection
        $redirect_to = apply_filters('video_central_user_login_redirect_to', '');
    video_central_redirect_to_field($redirect_to);

        // Prevent intention hi-jacking of log-in form
        wp_nonce_field('video-central-user-login');
}

/** Register ******************************************************************/

/**
 * Output the required hidden fields when registering.
 *
 * @since 1.0.0
 *
 * @uses add_query_arg() To add query args
 * @uses video_central_login_url() To get the login url
 * @uses apply_filters() To allow custom redirection
 * @uses video_central_redirect_to_field() To output the redirect to field
 * @uses wp_nonce_field() To generate hidden nonce fields
 */
function video_central_user_register_fields()
{
    ?>

        <input type="hidden" name="action"      value="register" />
        <input type="hidden" name="user-cookie" value="1" />

        <?php

        // Allow custom registration redirection
        $redirect_to = apply_filters('video_central_user_register_redirect_to', '');
    video_central_redirect_to_field(add_query_arg(array('checkemail' => 'registered'), $redirect_to));

        // Prevent intention hi-jacking of sign-up form
        wp_nonce_field('video-central-user-register');
}

/** Lost Password *************************************************************/

/**
 * Output the required hidden fields when user lost password.
 *
 * @since 1.0.0
 *
 * @uses apply_filters() To allow custom redirection
 * @uses video_central_redirect_to_field() Set referer
 * @uses wp_nonce_field() To generate hidden nonce fields
 */
function video_central_user_lost_pass_fields()
{
    ?>

        <input type="hidden" name="user-cookie" value="1" />

        <?php

        // Allow custom lost pass redirection
        $redirect_to = apply_filters('video_central_user_lost_pass_redirect_to', get_permalink());
    video_central_redirect_to_field(add_query_arg(array('checkemail' => 'confirm'), $redirect_to));

        // Prevent intention hi-jacking of lost pass form
        wp_nonce_field('video-central-user-lost-pass');
}

/** Author Avatar *************************************************************/

/**
 * Output the author link of a post.
 *
 * @since 1.0.0
 *
 * @param mixed $args Optional. If it is an integer, it is used as post id.
 *
 * @uses video_central_get_author_link() To get the post author link
 */
function video_central_author_link($args = '')
{
    echo video_central_get_author_link($args);
}
    /**
     * Return the author link of the post.
     *
     * @since 1.0.0
     *
     * @param mixed $args Optional. If an integer, it is used as reply id.
     *
     * @uses video_central_is_video() To check if it's a video page
     * @uses video_central_get_video_author_link() To get the video author link
     * @uses video_central_is_reply() To check if it's a reply page
     * @uses video_central_get_reply_author_link() To get the reply author link
     * @uses get_post_field() To get the post author
     * @uses video_central_is_reply_anonymous() To check if the reply is by an
     *                                 anonymous user
     * @uses get_the_author_meta() To get the author name
     * @uses video_central_get_user_profile_url() To get the author profile url
     * @uses get_avatar() To get the author avatar
     * @uses apply_filters() Calls 'video_central_get_reply_author_link' with the
     *                        author link and args
     *
     * @return string Author link of reply
     */
    function video_central_get_author_link($args = '')
    {
        $post_id = is_numeric($args) ? (int) $args : 0;

        // Parse arguments against default values
        $r = video_central_parse_args($args, array(
            'post_id' => $post_id,
            'link_title' => '',
            'type' => 'both',
            'size' => 80,
        ), 'get_author_link');

        // Confirmed video
        if (video_central_is_video($r['post_id'])) {
            return video_central_get_video_author_link($r);

        // Confirmed reply
        } elseif (video_central_is_reply($r['post_id'])) {
            return video_central_get_reply_author_link($r);
        }

        // Get the post author and proceed
        $user_id = get_post_field('post_author', $r['post_id']);

        // Neither a reply nor a video, so could be a revision
        if (!empty($r['post_id'])) {

            // Generate title with the display name of the author
            if (empty($r['link_title'])) {
                $r['link_title'] = sprintf(!video_central_is_reply_anonymous($r['post_id']) ? __('View %s\'s profile', 'video_central') : __('Visit %s\'s website', 'video_central'), get_the_author_meta('display_name', $user_id));
            }

            // Assemble some link bits
            $link_title = !empty($r['link_title']) ? ' title="'.$r['link_title'].'"' : '';
            $anonymous = video_central_is_reply_anonymous($r['post_id']);

            // Get avatar
            if ('avatar' === $r['type'] || 'both' === $r['type']) {
                $author_links[] = get_avatar($user_id, $r['size']);
            }

            // Get display name
            if ('name' === $r['type'] || 'both' === $r['type']) {
                $author_links[] = get_the_author_meta('display_name', $user_id);
            }

            // Add links if not anonymous
            if (empty($anonymous) && video_central_user_has_profile($user_id)) {
                $author_url = video_central_get_user_profile_url($user_id);
                foreach ($author_links as $link_text) {
                    $author_link[] = sprintf('<a href="%1$s"%2$s>%3$s</a>', $author_url, $link_title, $link_text);
                }
                $author_link = implode('&nbsp;', $author_link);

            // No links if anonymous
            } else {
                $author_link = implode('&nbsp;', $author_links);
            }

        // No post so link is empty
        } else {
            $author_link = '';
        }

        return apply_filters(__FUNCTION__, $author_link, $r);
    }

/** Capabilities **************************************************************/

/**
 * Check if the user can access a specific video.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_current_user_id()
 * @uses video_central_get_video_id()
 * @uses video_central_allow_anonymous()
 * @uses video_central_parse_args()
 * @uses video_central_get_user_id()
 * @uses current_user_can()
 * @uses video_central_is_user_keymaster()
 * @uses video_central_is_video_public()
 * @uses video_central_is_video_private()
 * @uses video_central_is_video_hidden()
 * @uses current_user_can()
 * @uses apply_filters()
 *
 * @return bool
 */
function video_central_user_can_view_video($args = '')
{

    // Parse arguments against default values
    $r = video_central_parse_args($args, array(
        'user_id' => video_central_get_current_user_id(),
        'video_id' => video_central_get_video_id(),
        'check_ancestors' => false,
    ), 'user_can_view_video');

    // Validate parsed values
    $user_id = video_central_get_user_id($r['user_id'], false, false);
    $video_id = video_central_get_video_id($r['video_id']);
    $retval = false;

    // User is a keymaster
    if (!empty($user_id) && video_central_is_user_keymaster($user_id)) {
        $retval = true;

    // Forum is public, and user can read videos or is not logged in
    } elseif (video_central_is_video_public($video_id, $r['check_ancestors'])) {
        $retval = true;

    // Forum is private, and user can see it
    } elseif (video_central_is_video_private($video_id, $r['check_ancestors']) && user_can($user_id, 'read_private_videos')) {
        $retval = true;

    // Forum is hidden, and user can see it
    } elseif (video_central_is_video_hidden($video_id, $r['check_ancestors']) && user_can($user_id, 'read_hidden_videos')) {
        $retval = true;
    }

    return apply_filters(__FUNCTION__, $retval, $video_id, $user_id);
}

/**
 * Check if the current user can publish videos.
 *
 * @since 1.0.0
 *
 * @uses video_central_is_user_keymaster()
 * @uses is_user_logged_in()
 * @uses video_central_allow_anonymous()
 * @uses video_central_is_user_active()
 * @uses current_user_can()
 * @uses apply_filters()
 *
 * @return bool
 */
function video_central_current_user_can_publish_videos()
{

    // Users need to earn access
    $retval = false;

    // Always allow keymasters
    if (video_central_is_user_keymaster()) {
        $retval = true;

    // Do not allow anonymous if not enabled
    } elseif (!is_user_logged_in() && video_central_allow_anonymous()) {
        $retval = true;

    // User is logged in
    } elseif (current_user_can('publish_videos')) {
        $retval = true;
    }

    // Allow access to be filtered
    return (bool) apply_filters(__FUNCTION__, $retval);
}

/** Forms *********************************************************************/

/**
 * The following functions should be turned into mapped meta capabilities in a
 * future version. They exist only to remove complex logistical capability
 * checks from within template parts.
 */

/**
 * Get the videos the current user has the ability to see and post to.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_video_post_type()
 * @uses get_posts()
 *
 * @param type $args
 *
 * @return type
 */
function video_central_get_videos_for_current_user($args = array())
{

    // Setup arrays
    $private = $hidden = $post__not_in = array();

    // Private videos
    if (!current_user_can('read_private_videos')) {
        $private = video_central_get_private_video_ids();
    }

    // Hidden videos
    if (!current_user_can('read_hidden_videos')) {
        $hidden = video_central_get_hidden_video_ids();
    }

    // Merge private and hidden videos together and remove any empties
    $video_ids = (array) array_filter(wp_parse_id_list(array_merge($private, $hidden)));

    // There are videos that need to be ex
    if (!empty($video_ids)) {
        $post__not_in = implode(',', $video_ids);
    }

    // Parse arguments against default values
    $r = video_central_parse_args($args, array(
        'post_type' => video_central_get_video_post_type(),
        'post_status' => video_central_get_public_status_id(),
        'numberposts' => -1,
        'exclude' => $post__not_in,
    ), 'get_videos_for_current_user');

    // Get the videos
    $videos = get_posts($r);

    // No availabe videos
    if (empty($videos)) {
        $videos = false;
    }

    return apply_filters(__FUNCTION__, $videos);
}

/**
 * Performs a series of checks to ensure the current user can create videos.
 *
 * @since 1.0.0
 *
 * @uses video_central_is_user_keymaster()
 * @uses video_central_is_video_edit()
 * @uses current_user_can()
 * @uses video_central_get_video_id()
 * @uses video_central_allow_anonymous()
 * @uses is_user_logged_in()
 *
 * @return bool
 */
function video_central_current_user_can_access_create_video_form()
{

    // Users need to earn access
    $retval = false;

    // Always allow keymasters
    if (video_central_is_user_keymaster()) {
        $retval = true;

    // Looking at a single video & video is open
    } elseif ((video_central_is_single_video() || is_page() || is_single()) && video_central_is_video_open()) {
        $retval = video_central_current_user_can_publish_videos();

    // User can edit this video
    } elseif (video_central_is_video_edit()) {
        $retval = current_user_can('edit_video', video_central_get_video_id());
    }

    // Allow access to be filtered
    return (bool) apply_filters(__FUNCTION__, (bool) $retval);
}

/**
 * Performs a series of checks to ensure the current user can create videos.
 *
 * @since 1.0.0
 *
 * @uses video_central_is_user_keymaster()
 * @uses video_central_is_video_edit()
 * @uses current_user_can()
 * @uses video_central_get_video_id()
 * @uses video_central_allow_anonymous()
 * @uses is_user_logged_in()
 *
 * @return bool
 */
function video_central_current_user_can_access_create_reply_form()
{

    // Users need to earn access
    $retval = false;

    // Always allow keymasters
    if (video_central_is_user_keymaster()) {
        $retval = true;

    // Looking at a single video, video is open, and video is open
    } elseif ((video_central_is_single_video() || is_page() || is_single()) && video_central_is_video_open() && video_central_is_video_open()) {
        $retval = video_central_current_user_can_publish_replies();
    }

    // Allow access to be filtered
    return (bool) apply_filters(__FUNCTION__, (bool) $retval);
}

/**
 * Performs a series of checks to ensure the current user should see the
 * anonymous user form fields.
 *
 * @since 1.0.0
 *
 * @uses video_central_is_anonymous()
 * @uses video_central_is_video_edit()
 * @uses video_central_is_video_anonymous()
 *
 * @return bool
 */
function video_central_current_user_can_access_anonymous_user_form()
{

    // Users need to earn access
    $retval = false;

    // User is not logged in, and anonymous posting is allowed
    if (video_central_is_anonymous()) {
        $retval = true;

    // User is editing a video, and video is authored by anonymous user
    } elseif (video_central_is_video_edit() && video_central_is_video_anonymous()) {
        $retval = true;
    }

    // Allow access to be filtered
    return (bool) apply_filters(__FUNCTION__, (bool) $retval);
}
