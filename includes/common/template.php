<?php

/**
 * Common Template Tags.
 *
 * Common template tags are ones that are used by more than one component, like
 * videos, users, video tags, etc...
 */

/** URLs **********************************************************************/

/**
 * Ouput the video URL.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_videos_archive_url() To get the videos archive URL
 *
 * @param string $path Additional path with leading slash
 */
function video_central_archive_url($path = '/')
{
    echo esc_url(video_central_get_videos_archive_url($path));
}
    /**
     * Return the video URL.
     *
     * @since 1.0.0
     *
     * @uses home_url() To get the home URL
     * @uses video_central_get_root_slug() To get the video root location
     *
     * @param string $path Additional path with leading slash
     */
    function video_central_get_archive_url($path = '/')
    {
        return home_url(video_central_get_root_slug().$path);
    }

/** is_ ***********************************************************************/

/**
 * Check if current site is public.
 *
 * @since 1.0.0
 *
 * @param int $site_id
 *
 * @uses get_current_blog_id()
 * @uses get_blog_option()
 * @uses apply_filters()
 *
 * @return bool True if site is public, false if private
 */
function video_central_is_site_public($site_id = 0)
{

    // Get the current site ID
    if (empty($site_id)) {
        $site_id = get_current_blog_id();
    }

    // Get the site visibility setting
    $public = get_blog_option($site_id, 'blog_public', 1);

    return (bool) apply_filters(__FUNCTION__, $public, $site_id);
}

/**
 * Check if current page is a video archive.
 *
 * @since 1.0.0
 *
 * @param int $post_id Possible post_id to check
 *
 * @uses video_central_get_video_post_type() To get the video post type
 *
 * @return bool True if it's a video page, false if not
 */
function video_central_is_video($post_id = 0)
{

    // Assume false
    $retval = false;

    // Supplied ID is a video
    if (!empty($post_id) && (video_central_get_video_post_type() === get_post_type($post_id))) {
        $retval = true;
    }

    return (bool) apply_filters(__FUNCTION__, $retval, $post_id);
}

/**
 * Check if we are viewing a video archive.
 *
 * @since 1.0.0
 *
 * @uses is_post_type_archive() To check if we are looking at the video archive
 * @uses video_central_get_video_post_type() To get the video post type ID
 *
 * @return bool
 */
function video_central_is_video_archive()
{
    global $wp_query;

    // Default to false
    $retval = false;

    // In video archive
    if (is_post_type_archive(video_central_get_video_post_type()) || video_central_is_query_name('video_central_video_archive') || !empty($wp_query->video_central_show_videos_on_root) ||  video_central_is_video_category() || video_central_is_video_tag()) {
        $retval = true;
    }

    return (bool) apply_filters(__FUNCTION__, $retval);
}

/**
 * Viewing a single video.
 *
 * @since 1.0.0
 *
 * @uses is_single()
 * @uses video_central_get_video_post_type()
 * @uses get_post_type()
 * @uses apply_filters()
 *
 * @return bool
 */
function video_central_is_single_video()
{

    // Assume false
    $retval = false;

    // Single and a match
    if (is_singular(video_central_get_video_post_type()) || video_central_is_query_name('video_central_single_video')) {
        $retval = true;
    }

    return (bool) apply_filters(__FUNCTION__, $retval);
}

/**
 * Check if current page is a view page.
 *
 * @since 1.0.0
 *
 * @global WP_Query $wp_query To check if WP_Query::video_central_is_view is true
 *
 * @uses video_central_is_query_name() To get the query name
 *
 * @return bool Is it a view page?
 */
function video_central_is_single_view()
{
    global $wp_query;

    // Assume false
    $retval = false;

    // Check query
    if (!empty($wp_query->video_central_is_view) && (true === $wp_query->video_central_is_view)) {
        $retval = true;
    }

    // Check query name
    if (empty($retval) && video_central_is_query_name('video_central_single_view')) {
        $retval = true;
    }

    return (bool) apply_filters(__FUNCTION__, $retval);
}

/**
 * Check if current page is a search page.
 *
 * @since 1.0.0
 *
 * @global WP_Query $wp_query To check if WP_Query::video_central_is_search is true
 *
 * @uses video_central_is_query_name() To get the query name
 *
 * @return bool Is it a search page?
 */
function video_central_is_search()
{
    global $wp_query;

    // Bail if search is disabled
    if (!video_central_allow_search()) {
        return false;
    }

    // Assume false
    $retval = false;

    // Check query
    if (!empty($wp_query->video_central_is_search) && (true === $wp_query->video_central_is_search)) {
        $retval = true;
    }

    // Check query name
    if (empty($retval) && video_central_is_query_name(video_central_get_search_rewrite_id())) {
        $retval = true;
    }

    // Check $_GET
    if (empty($retval) && isset($_REQUEST[ video_central_get_search_rewrite_id() ]) && empty($_REQUEST[ video_central_get_search_rewrite_id() ])) {
        $retval = true;
    }

    return (bool) apply_filters(__FUNCTION__, $retval);
}

/**
 * Check if current page is a search results page.
 *
 * @since 1.0.0
 *
 * @global WP_Query $wp_query To check if WP_Query::video_central_is_search is true
 *
 * @uses video_central_is_query_name() To get the query name
 *
 * @return bool Is it a search page?
 */
function video_central_is_search_results()
{
    global $wp_query;

    // Bail if search is disabled
    if (!video_central_allow_search()) {
        return false;
    }

    // Assume false
    $retval = false;

    // Check query
    if (!empty($wp_query->video_central_search_terms)) {
        $retval = true;
    }

    // Check query name
    if (empty($retval) && video_central_is_query_name('video_central_search_results')) {
        $retval = true;
    }

    // Check $_REQUEST
    if (empty($retval) && !empty($_REQUEST[ video_central_get_search_rewrite_id() ])) {
        $retval = true;
    }

    return (bool) apply_filters(__FUNCTION__, $retval);
}

/**
 * Check if the current page is a video tag.
 *
 * @since 1.0.0
 *
 * @return bool True if it's a video tag, false if not
 */
function video_central_is_video_tag()
{

    // Bail if video-tags are off
    if (!video_central_allow_video_tags()) {
        return false;
    }

    // Return false if editing a video tag
    if (video_central_is_video_tag_edit()) {
        return false;
    }

    // Assume false
    $retval = false;

    // Check tax and query vars
    if (is_tax(video_central_get_video_tag_tax_id()) || !empty(video_central()->video_query->is_tax) || get_query_var('video_central_tag')) {
        $retval = true;
    }

    return (bool) apply_filters(__FUNCTION__, $retval);
}

/**
 * Check if the current page is editing a video tag.
 *
 * @since 1.0.0
 *
 * @uses WP_Query Checks if WP_Query::video_central_is_video_tag_edit is true
 *
 * @return bool True if editing a video tag, false if not
 */
function video_central_is_video_tag_edit()
{
    global $wp_query, $pagenow, $taxnow;

    // Bail if video-tags are off
    if (!video_central_allow_video_tags()) {
        return false;
    }

    // Assume false
    $retval = false;

    // Check query
    if (!empty($wp_query->video_central_is_video_tag_edit) && (true === $wp_query->video_central_is_video_tag_edit)) {
        $retval = true;
    }

    // Editing in admin
    elseif (is_admin() && ('edit-tags.php' === $pagenow) && (video_central_get_video_tag_tax_id() === $taxnow) && (!empty($_GET['action']) && ('edit' === $_GET['action']))) {
        $retval = true;
    }

    return (bool) apply_filters(__FUNCTION__, $retval);
}

/**
 * Check if the current page is a video tag.
 *
 * @since 1.0.0
 *
 * @return bool True if it's a video tag, false if not
 */
function video_central_is_video_category()
{

    // Bail if video-tags are off
    if (!video_central_allow_video_categories()) {
        return false;
    }

    // Return false if editing a video tag
    if (video_central_is_video_category_edit()) {
        return false;
    }

    // Assume false
    $retval = false;

    // Check tax and query vars
    if (is_tax(video_central_get_video_category_tax_id()) || !empty(video_central()->video_query->is_tax) || get_query_var('video_central_category')) {
        $retval = true;
    }

    return (bool) apply_filters(__FUNCTION__, $retval);
}

/**
 * Check if the current page is editing a video tag.
 *
 * @since 1.0.0
 *
 * @uses WP_Query Checks if WP_Query::video_central_is_video_category_edit is true
 *
 * @return bool True if editing a video tag, false if not
 */
function video_central_is_video_category_edit()
{
    global $wp_query, $pagenow, $taxnow;

    // Bail if video-tags are off
    if (!video_central_allow_video_categories()) {
        return false;
    }

    // Assume false
    $retval = false;

    // Check query
    if (!empty($wp_query->video_central_is_video_category_edit) && (true === $wp_query->video_central_is_video_category_edit)) {
        $retval = true;
    }

    // Editing in admin
    elseif (is_admin() && ('edit-categories.php' === $pagenow) && (video_central_get_video_category_tax_id() === $taxnow) && (!empty($_GET['action']) && ('edit' === $_GET['action']))) {
        $retval = true;
    }

    return (bool) apply_filters(__FUNCTION__, $retval);
}

/**
 * Check if the current post type is used by Video Central.
 *
 * @since 1.0.0
 *
 * @param mixed $the_post Optional. Post object or post ID.
 *
 * @uses get_post_type()
 * @uses video_central_get_video_post_type()
 *
 * @return bool
 */
function video_central_is_custom_post_type($the_post = false)
{

    // Assume false
    $retval = false;

    // Viewing one of the video_central post types
    if (in_array(get_post_type($the_post), array(video_central_get_video_post_type()))) {
        $retval = true;
    }

    return (bool) apply_filters(__FUNCTION__, $retval, $the_post);
}

/**
 * Use the above is_() functions to output a body class for each scenario.
 *
 * @since 1.0.1
 *
 * @param array $wp_classes
 * @param array $custom_classes
 *
 * @uses video_central_is_single_video()
 * @uses video_central_is_single_view()
 * @uses video_central_is_video_archive()
 * @uses video_central_is_video_category()
 * @uses video_central_get_video_category_tax_id()
 * @uses video_central_get_video_category_slug()
 * @uses video_central_get_video_category_id()
 * @uses video_central_is_video_tag()
 * @uses video_central_get_video_tag_tax_id()
 * @uses video_central_get_video_tag_slug()
 * @uses video_central_get_video_tag_id()
 *
 * @return array Body Classes
 */
function video_central_body_class($wp_classes, $custom_classes = false)
{
    $video_central_classes = array();

    /* Archives **************************************************************/

    if (video_central_is_video_archive()) {
        $video_central_classes[] = video_central_get_video_post_type().'-archive';

    /* Video Categories ************************************************************/
    } elseif (video_central_is_video_category()) {
        $video_central_classes[] = video_central_get_video_category_tax_id();
        $video_central_classes[] = video_central_get_video_category_tax_id().'-'.video_central_get_video_category_slug();
        $video_central_classes[] = video_central_get_video_category_tax_id().'-'.video_central_get_video_category_id();

    /* Video Tags ************************************************************/
    } elseif (video_central_is_video_tag()) {
        $video_central_classes[] = video_central_get_video_tag_tax_id();
        $video_central_classes[] = video_central_get_video_tag_tax_id().'-'.video_central_get_video_tag_slug();
        $video_central_classes[] = video_central_get_video_tag_tax_id().'-'.video_central_get_video_tag_id();

    /* Components ************************************************************/
    } elseif (video_central_is_single_video()) {
        $video_central_classes[] = video_central_get_video_post_type();
    } elseif (video_central_is_single_view()) {
        $video_central_classes[] = 'video-central-view';

    /* Search ****************************************************************/
    } elseif (video_central_is_search()) {
        $video_central_classes[] = 'video-central-search';
        $video_central_classes[] = 'video-search';
    } elseif (video_central_is_search_results()) {
        $video_central_classes[] = 'video-central-search-results';
        $video_central_classes[] = 'video-search-results';
    }

    /* Add theme class **************************************************************/
    $main_theme = wp_get_theme();

    $main_theme = sanitize_title_with_dashes($main_theme->get('Name'));

    $video_central_classes[] = 'video-central-'.strtolower($main_theme);

    /* Clean up **************************************************************/

    // Add Video Central class if we are within a video page
    if (!empty($video_central_classes)) {
        $video_central_classes[] = 'video-central';
    }

    // Merge WP classes with Video Central classes and remove any duplicates
    $classes = array_unique(array_merge((array) $video_central_classes, (array) $wp_classes));

    return apply_filters('video_central_body_class', $classes, $video_central_classes, $wp_classes, $custom_classes);
}

/**
 * Use the above is_() functions to return if in any Video Central page.
 *
 * @since 1.0.0
 *
 * @uses video_central_is_single_video()
 * @uses video_central_is_video_edit()
 * @uses video_central_is_single_view()
 * @uses video_central_is_single_user_edit()
 * @uses video_central_is_single_user()
 * @uses video_central_is_user_home()
 * @uses video_central_is_subscriptions()
 * @uses video_central_is_favorites()
 * @uses video_central_is_videos_created()
 *
 * @return bool In a Video Central page
 */
function is_video_central()
{

    // Defalt to false
    $retval = false;

    /* Archives **************************************************************/

    if (video_central_is_video_archive()) {
        $retval = true;
    } elseif (video_central_is_single_video()) {
        $retval = true;
    } elseif (video_central_is_single_view()) {
        $retval = true;

    /* Search ****************************************************************/
    } elseif (video_central_is_search()) {
        $retval = true;
    } elseif (video_central_is_search_results()) {
        $retval = true;
    }

    /* Done ******************************************************************/

    return (bool) apply_filters(__FUNCTION__, $retval);
}

/** Views *********************************************************************/

/**
 * Output the view id.
 *
 * @since 1.0.0
 *
 * @param string $view Optional. View id
 *
 * @uses video_central_get_view_id() To get the view id
 */
function video_central_view_id($view = '')
{
    echo video_central_get_view_id($view);
}

    /**
     * Get the view id.
     *
     * Use view id if supplied, otherwise video_central_get_view_rewrite_id() query var.
     *
     * @since 1.0.0
     *
     * @param string $view Optional. View id.
     *
     * @uses sanitize_title() To sanitize the view id
     * @uses get_query_var() To get the view id query variable
     * @uses video_central_get_view_rewrite_id() To get the view rewrite ID
     *
     * @return bool|string ID on success, false on failure
     */
    function video_central_get_view_id($view = '')
    {
        $video_central = video_central();

        if (!empty($view)) {
            $view = sanitize_title($view);
        } elseif (!empty($video_central->current_view_id)) {
            $view = $video_central->current_view_id;
        } else {
            $view = get_query_var(video_central_get_view_rewrite_id());
        }

        if (array_key_exists($view, $video_central->views)) {
            return $view;
        }

        return false;
    }

/**
 * Output the view name aka title.
 *
 * @since 1.0.0
 *
 * @param string $view Optional. View id
 *
 * @uses video_central_get_view_title() To get the view title
 */
function video_central_view_title($view = '')
{
    echo video_central_get_view_title($view);
}

    /**
     * Get the view name aka title.
     *
     * If a view id is supplied, that is used. Otherwise the video_central_view
     * query var is checked for.
     *
     * @since 1.0.0
     *
     * @param string $view Optional. View id
     *
     * @uses video_central_get_view_id() To get the view id
     *
     * @return bool|string Title on success, false on failure
     */
    function video_central_get_view_title($view = '')
    {
        $video_central = video_central();

        $view = video_central_get_view_id($view);
        if (empty($view)) {
            return false;
        }

        return $video_central->views[$view]['title'];
    }

/**
 * Output the view url.
 *
 * @since 1.0.0
 *
 * @param string $view Optional. View id
 *
 * @uses video_central_get_view_url() To get the view url
 */
function video_central_view_url($view = false)
{
    echo esc_url(video_central_get_view_url($view));
}
    /**
     * Return the view url.
     *
     * @since 1.0.0
     *
     * @param string $view Optional. View id
     *
     * @uses sanitize_title() To sanitize the view id
     * @uses home_url() To get blog home url
     * @uses add_query_arg() To add custom args to the url
     * @uses apply_filters() Calls 'video_central_get_view_url' with the view url,
     *                        used view id
     *
     * @return string View url (or home url if the view was not found)
     */
    function video_central_get_view_url($view = false)
    {
        global $wp_rewrite;

        $view = video_central_get_view_id($view);
        if (empty($view)) {
            return home_url();
        }

        // Pretty permalinks
        if ($wp_rewrite->using_permalinks()) {
            $url = $wp_rewrite->root.video_central_get_view_slug().'/'.$view;
            $url = home_url(user_trailingslashit($url));

        // Unpretty permalinks
        } else {
            $url = add_query_arg(array(video_central_get_view_rewrite_id() => $view), home_url('/'));
        }

        return apply_filters(__FUNCTION__, $url, $view);
    }

/** Query *********************************************************************/

/**
 * Check the passed parameter against the current _video_central_query_name.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_query_name() Get the query var '_video_central_query_name'
 *
 * @return bool True if match, false if not
 */
function video_central_is_query_name($name = '')
{
    return (bool) (video_central_get_query_name() === $name);
}

/**
 * Get the '_video_central_query_name' setting.
 *
 * @since 1.0.0
 *
 * @uses get_query_var() To get the query var '_video_central_query_name'
 *
 * @return string To return the query var value
 */
function video_central_get_query_name()
{
    return get_query_var('_video_central_query_name');
}

/**
 * Set the '_video_central_query_name' setting to $name.
 *
 * @since 1.0.0
 *
 * @param string $name What to set the query var to
 *
 * @uses set_query_var() To set the query var '_video_central_query_name'
 */
function video_central_set_query_name($name = '')
{
    set_query_var('_video_central_query_name', $name);
}

/**
 * Used to clear the '_video_central_query_name' setting.
 *
 * @since 1.0.0
 *
 * @uses video_central_set_query_name() To set the query var '_video_central_query_name' value to ''
 */
function video_central_reset_query_name()
{
    video_central_set_query_name();
}

/* Video Sorting **********************************************************/

/**
 * Get supported sort types.
 *
 * @since 1.0.0
 *
 * @uses apply_filters()
 */
function video_central_supported_sort_types()
{
    $types = array(
        'date' => array(
            'label' => __('Date', 'video_central'),
            'title' => __('Sort by Date', 'video_central'),
        ),
        'title' => array(
            'label' => __('Title', 'video_central'),
            'title' => __('Sort by Title', 'video_central'),
        ),
        'views' => array(
            'label' => __('Views', 'video_central'),
            'title' => __('Sort by Views', 'video_central'),
        ),
        'likes' => array(
            'label' => __('Likes', 'video_central'),
            'title' => __('Sort by Likes', 'video_central'),
        ),
        'comments' => array(
            'label' => __('Comments', 'video_central'),
            'title' => __('Sort by Comments', 'video_central'),
        ),
        'rand' => array(
            'label' => __('Random', 'video_central'),
            'title' => __('Sort Randomly', 'video_central'),
        ),
    );

    return apply_filters(__FUNCTION__, $types);
}

/**
 * Sorting options.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_selected_sort_types() To get the sorting options
 */
function video_central_get_selected_sort_types()
{
    $supported_types = video_central_supported_sort_types();

    return apply_filters(__FUNCTION__, $supported_types);
}

/**
 * Get supported view types.
 *
 * @since 1.0.0
 */
function video_central_supported_view_types()
{
    $types = array(
        'small-block-grid-4' => __('Grid View with Mini Thumbnail', 'video_central'),
        'small-block-grid-3' => __('Grid View with Small Thumbnail', 'video_central'),
        'small-block-grid-2' => __('Grid View with Medium Thumbnail', 'video_central'),
        //'list-small' => __('List View with Small Thumbnail', 'video_central'),
        //'list-medium' => __('List View with Medium Thumbnail', 'video_central'),
        //'list-large' => __('List View with Large Thumbnail', 'video_central'),
    );

    return apply_filters(__FUNCTION__, $types);
}

/**
 * Get selected view types.
 *
 * @since 1.0.0
 */
function video_central_get_selected_view_types()
{
    $supported_types = video_central_supported_view_types();

    return apply_filters(__FUNCTION__, $supported_types);
}
/**
 * Create Custom Video archive order queries.
 *
 * @since 1.0.0
 */
function video_central_get_custom_video_order($default)
{
    $default_video_order = (get_query_var('orderby')) ? get_query_var('orderby') : false;

    if ($default_video_order == 'views') {
        $default['meta_key'] = '_video_central_video_views_count';
        $default['orderby'] = 'meta_value_num';
    } elseif ($default_video_order == 'likes') {
        $default['meta_key'] = '_video_central_video_likes_count';
        $default['orderby'] = 'meta_value_num';
    } elseif ($default_video_order == 'comments') {
        $default['orderby'] = 'comment_count';
        $default['order'] = 'ASC';
    } elseif ($default_video_order == 'title') {
        $default['orderby'] = 'title';
        $default['order'] = 'ASC';
    } elseif ($default_video_order == 'date') {
        $default['orderby'] = 'date';
        $default['order'] = 'ASC';
    } elseif ($default_video_order == 'rand') {
        $default['orderby'] = 'rand';
        $default['order'] = 'ASC';
    }

    return apply_filters(__FUNCTION__, $default);
}

/** Title *********************************************************************/

/**
 * Custom page title for Video Central pages.
 *
 * @since 1.0.0
 *
 * @param string $title       Optional. The title (not used).
 * @param string $sep         Optional, default is '&raquo;'. How to separate the
 *                            various items within the page title.
 * @param string $seplocation Optional. Direction to display title, 'right'.
 *
 * @uses video_central_is_single_user() To check if it's a user profile page
 * @uses video_central_is_single_user_edit() To check if it's a user profile edit page
 * @uses video_central_is_user_home() To check if the profile page is of the current user
 * @uses get_query_var() To get the user id
 * @uses get_userdata() To get the user data
 * @uses video_central_is_single_video() To check if it's a video
 * @uses video_central_get_video_title() To get the video title
 * @uses is_tax() To check if it's the tag page
 * @uses get_queried_object() To get the queried object
 * @uses video_central_is_single_view() To check if it's a view
 * @uses video_central_get_view_title() To get the view title
 * @uses apply_filters() Calls 'video_central_raw_title' with the title
 * @uses apply_filters() Calls 'video_central_profile_page_wp_title' with the title,
 *                        separator and separator location
 *
 * @return string The title
 */
function video_central_title($title = '', $sep = '&raquo;', $seplocation = '')
{

    // Title array
    $new_title = array();

    /* Archives **************************************************************/

    // Video Archive
    if (video_central_is_video_archive()) {
        $new_title['text'] = video_central_get_archive_title();

    /* Singles ***************************************************************/

    // Video page
    } elseif (video_central_is_single_video()) {
        $new_title['text'] = video_central_get_video_title();
        $new_title['format'] = esc_attr__('Videos: %s', 'video_central');

    /* Search ****************************************************************/

    // Search
    } elseif (video_central_is_search()) {
        $new_title['text'] = video_central_get_search_title();
    }

    // Set title array defaults
    $new_title = video_central_parse_args($new_title, array(
        'text' => $title,
        'format' => '%s',
    ), 'title');

    // Get the formatted raw title
    $new_title = sprintf($new_title['format'], $new_title['text']);

    // Filter the raw title
    $new_title = apply_filters('video_central_raw_title', $new_title, $sep, $seplocation);

    // Compare new title with original title
    if ($new_title === $title) {
        return $title;
    }

    // Temporary separator, for accurate flipping, if necessary
    $t_sep = '%WP_TITILE_SEP%';
    $prefix = '';

    if (!empty($new_title)) {
        $prefix = " $sep ";
    }

    // sep on right, so reverse the order
    if ('right' === $seplocation) {
        $new_title_array = array_reverse(explode($t_sep, $new_title));
        $new_title = implode(" $sep ", $new_title_array).$prefix;

    // sep on left, do not reverse
    } else {
        $new_title_array = explode($t_sep, $new_title);
        $new_title = $prefix.implode(" $sep ", $new_title_array);
    }

    // Filter and return
    return apply_filters(__FUNCTION__, $new_title, $sep, $seplocation);
}

/** Forms *********************************************************************/

/**
 * Output the login form action url.
 *
 * @since 1.0.0
 *
 * @param array args Pass a URL to redirect to
 *
 * @uses add_query_arg() To add a arg to the url
 * @uses site_url() Toget the site url
 * @uses apply_filters() Calls 'video_central_wp_login_action' with the url and args
 */

function video_central_wp_login_action( $args = '' ) {
    echo esc_url( video_central_get_wp_login_action( $args ) );
}

    /**
     * Return the login form action url
     *
     * @since 1.2.3
     *
     * @param array $args Pass a URL to redirect to
     * @uses add_query_arg() To add a arg to the url
     * @uses site_url() Toget the site url
     * @uses apply_filters() Calls 'video_central_wp_login_action' with the url and args
     */
function video_central_get_wp_login_action( $args = '' ) {

    // Parse arguments against default values
    $r = video_central_parse_args( $args, array(
        'action'  => '',
        'context' => '',
        'url'     => 'wp-login.php'
    ), 'login_action' );

    // Add action as query arg
    if ( !empty( $r['action'] ) ) {
        $login_url = add_query_arg( array( 'action' => $r['action'] ), $r['url'] );

    // No query arg
    } else {
        $login_url = $r['url'];
    }

    $login_url = site_url( $login_url, $r['context'] );

    return apply_filters( __FUNCTION__, $login_url, $r, $args );
}

/**
 * Output hidden request URI field for user forms.
 *
 * The referer link is the current Request URI from the server super global. To
 * check the field manually, use video_central_get_redirect_to().
 *
 * @since 1.0.0
 *
 * @param string $redirect_to Pass a URL to redirect to
 *
 * @uses wp_get_referer() To get the referer
 * @uses esc_attr() To escape the url
 * @uses apply_filters() Calls 'video_central_redirect_to_field', passes field and to
 */
function video_central_redirect_to_field($redirect_to = '')
{

    // Make sure we are directing somewhere
    if (empty($redirect_to)) {
        if (isset($_SERVER['REQUEST_URI'])) {
            $redirect_to = (is_ssl() ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        } else {
            $redirect_to = wp_get_referer();
        }
    }

    // Remove loggedout query arg if it's there
    $redirect_to = (string) esc_attr(remove_query_arg('loggedout', $redirect_to));
    $redirect_field = '<input type="hidden" id="video_central_redirect_to" name="redirect_to" value="'. esc_url( $redirect_to ).'" />';

    echo apply_filters(__FUNCTION__, $redirect_field, $redirect_to);
}

/**
 * Echo sanitized $_REQUEST value.
 *
 * Use the $input_type parameter to properly process the value. This
 * ensures correct sanitization of the value for the receiving input.
 *
 * @since 1.0.0
 *
 * @param string $request    Name of $_REQUEST to look for
 * @param string $input_type Type of input. Default: text. Accepts:
 *                           textarea|password|select|radio|checkbox
 *
 * @uses video_central_get_sanitize_val() To sanitize the value.
 */
function video_central_sanitize_val($request = '', $input_type = 'text')
{
    echo video_central_get_sanitize_val($request, $input_type);
}
    /**
     * Return sanitized $_REQUEST value.
     *
     * Use the $input_type parameter to properly process the value. This
     * ensures correct sanitization of the value for the receiving input.
     *
     * @since 1.0.0
     *
     * @param string $request    Name of $_REQUEST to look for
     * @param string $input_type Type of input. Default: text. Accepts:
     *                           textarea|password|select|radio|checkbox
     *
     * @uses esc_attr() To escape the string
     * @uses apply_filters() Calls 'video_central_get_sanitize_val' with the sanitized
     *                        value, request and input type
     *
     * @return string Sanitized value ready for screen display
     */
    function video_central_get_sanitize_val($request = '', $input_type = 'text')
    {

        // Check that requested
        if (empty($_REQUEST[$request])) {
            return false;
        }

        // Set request varaible
        $pre_ret_val = $_REQUEST[$request];

        // Treat different kinds of fields in different ways
        switch ($input_type) {
            case 'text'     :
            case 'textarea' :
                $retval = esc_attr(stripslashes($pre_ret_val));
                break;

            case 'password' :
            case 'select'   :
            case 'radio'    :
            case 'checkbox' :
            default :
                $retval = esc_attr($pre_ret_val);
                break;
        }

        return apply_filters(__FUNCTION__, $retval, $request, $input_type);
    }

/**
 * Output the current tab index of a given form.
 *
 * Use this function to handle the tab indexing of user facing forms within a
 * template file. Calling this function will automatically increment the global
 * tab index by default.
 *
 * @since 1.0.0
 *
 * @param int $auto_increment Optional. Default true. Set to false to prevent
 *                            increment
 */
function video_central_tab_index($auto_increment = true)
{
    echo video_central_get_tab_index($auto_increment);
}

    /**
     * Output the current tab index of a given form.
     *
     * Use this function to handle the tab indexing of user facing forms
     * within a template file. Calling this function will automatically
     * increment the global tab index by default.
     *
     * @since 1.0.0
     *
     * @uses apply_filters Allows return value to be filtered
     *
     * @param int $auto_increment Optional. Default true. Set to false to
     *                            prevent the increment
     *
     * @return int $video_central->tab_index The global tab index
     */
    function video_central_get_tab_index($auto_increment = true)
    {
        $video_central = video_central();

        if (true === $auto_increment) {
            ++$video_central->tab_index;
        }

        return apply_filters(__FUNCTION__, (int) $video_central->tab_index);
    }
