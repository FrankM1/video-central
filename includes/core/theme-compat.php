<?php

/**
 * Video Central Core Theme Compatibility.
 */

/** Theme Compat **************************************************************/

/**
 * What follows is an attempt at intercepting the natural page load process
 * to replace the_content() with the appropriate Video Central content.
 *
 * To do this, Video Central does several direct manipulations of global variables
 * and forces them to do what they are not supposed to be doing.
 *
 * Don't try anything you're about to witness here, at home. Ever.
 */

/** Base Class ****************************************************************/

/**
 * Theme Compatibility base class.
 *
 * This is only intended to be extended, and is included here as a basic guide
 * for future Theme Packs to use. @link video_central_Twenty_Ten is a good example of
 * extending this class, as is @link video_central_setup_theme_compat()
 *
 * @since 1.0.0
 */
class Video_Central_Theme_Compat
{
    /**
     * Should be like this:.
     *
     * array(
     *     'id'      => ID of the theme (should be unique)
     *     'name'    => Name of the theme (should match style.css)
     *     'version' => Theme version for cache busting scripts and styling
     *     'dir'     => Path to theme
     *     'url'     => URL to theme
     * );
     *
     * @var array
     */
    private $_data = array();

    /**
     * Pass the $properties to the object on creation.
     *
     * @since 1.0.0
     *
     * @param array $properties
     */
    public function __construct(Array $properties = array())
    {
        $this->_data = $properties;
    }

    /**
     * Set a theme's property.
     *
     * @since 1.0.0
     *
     * @param string $property
     * @param mixed  $value
     *
     * @return mixed
     */
    public function __set($property, $value)
    {
        return $this->_data[$property] = $value;
    }

    /**
     * Get a theme's property.
     *
     * @since 1.0.0
     *
     * @param string $property
     * @param mixed  $value
     *
     * @return mixed
     */
    public function __get($property)
    {
        return array_key_exists($property, $this->_data) ? $this->_data[$property] : '';
    }
}

/** Functions *****************************************************************/

/**
 * Setup the default theme compat theme.
 *
 * @since 1.0.0
 *
 * @param Video_Central_Theme_Compat $theme
 */
function video_central_setup_theme_compat($theme = '')
{
    $video_central = video_central();

    // Make sure theme package is available, set to default if not
    if (!isset($video_central->theme_compat->packages[$theme]) || !is_a($video_central->theme_compat->packages[$theme], 'Video_Central_Theme_Compat')) {
        $theme = 'default';
    }

    // Set the active theme compat theme
    $video_central->theme_compat->theme = $video_central->theme_compat->packages[$theme];
}

/**
 * Gets the name of the Video Central compatible theme used, in the event the
 * currently active WordPress theme does not explicitly support Video Central.
 * This can be filtered or set manually. Tricky theme authors can override the
 * default and include their own Video Central compatibility layers for their themes.
 *
 * @since 1.0.0
 *
 * @uses apply_filters()
 *
 * @return string
 */
function video_central_get_theme_compat_id()
{
    return apply_filters(__FUNCTION__, video_central()->theme_compat->theme->id);
}

/**
 * Gets the name of the Video Central compatible theme used, in the event the
 * currently active WordPress theme does not explicitly support Video Central.
 * This can be filtered or set manually. Tricky theme authors can override the
 * default and include their own Video Central compatibility layers for their themes.
 *
 * @since 1.0.0
 *
 * @uses apply_filters()
 *
 * @return string
 */
function video_central_get_theme_compat_name()
{
    return apply_filters(__FUNCTION__, video_central()->theme_compat->theme->name);
}

/**
 * Gets the version of the Video Central compatible theme used, in the event the
 * currently active WordPress theme does not explicitly support Video Central.
 * This can be filtered or set manually. Tricky theme authors can override the
 * default and include their own Video Central compatibility layers for their themes.
 *
 * @since 1.0.0
 *
 * @uses apply_filters()
 *
 * @return string
 */
function video_central_get_theme_compat_version()
{
    return apply_filters(__FUNCTION__, video_central()->theme_compat->theme->version);
}

/**
 * Gets the Video Central compatible theme used in the event the currently active
 * WordPress theme does not explicitly support Video Central. This can be filtered,
 * or set manually. Tricky theme authors can override the default and include
 * their own Video Central compatibility layers for their themes.
 *
 * @since 1.0.0
 *
 * @uses apply_filters()
 *
 * @return string
 */
function video_central_get_theme_compat_dir()
{
    return apply_filters(__FUNCTION__, video_central()->theme_compat->theme->dir);
}

/**
 * Gets the Video Central compatible theme used in the event the currently active
 * WordPress theme does not explicitly support Video Central. This can be filtered,
 * or set manually. Tricky theme authors can override the default and include
 * their own Video Central compatibility layers for their themes.
 *
 * @since 1.0.0
 *
 * @uses apply_filters()
 *
 * @return string
 */
function video_central_get_theme_compat_url()
{
    return apply_filters(__FUNCTION__, video_central()->theme_compat->theme->url);
}

/**
 * Gets true/false if page is currently inside theme compatibility.
 *
 * @since 1.0.0
 *
 * @return bool
 */
function video_central_is_theme_compat_active()
{
    $video_central = video_central();

    if (empty($video_central->theme_compat->active)) {
        return false;
    }

    return $video_central->theme_compat->active;
}

/**
 * Sets true/false if page is currently inside theme compatibility.
 *
 * @since 1.0.0
 *
 * @param bool $set
 *
 * @return bool
 */
function video_central_set_theme_compat_active($set = true)
{
    video_central()->theme_compat->active = $set;

    return (bool) video_central()->theme_compat->active;
}

/**
 * Set the theme compat templates global.
 *
 * Stash possible template files for the current query. Useful if plugins want
 * to override them, or see what files are being scanned for inclusion.
 *
 * @since 1.0.0
 */
function video_central_set_theme_compat_templates($templates = array())
{
    video_central()->theme_compat->templates = $templates;

    return video_central()->theme_compat->templates;
}

/**
 * Set the theme compat template global.
 *
 * Stash the template file for the current query. Useful if plugins want
 * to override it, or see what file is being included.
 *
 * @since 1.0.0
 */
function video_central_set_theme_compat_template($template = '')
{
    video_central()->theme_compat->template = $template;

    return video_central()->theme_compat->template;
}

/**
 * Set the theme compat original_template global.
 *
 * Stash the original template file for the current query. Useful for checking
 * if Video Central was able to find a more appropriate template.
 *
 * @since 1.0.0
 */
function video_central_set_theme_compat_original_template($template = '')
{
    video_central()->theme_compat->original_template = $template;

    return video_central()->theme_compat->original_template;
}

/**
 * Set the theme compat original_template global.
 *
 * Stash the original template file for the current query. Useful for checking
 * if Video Central was able to find a more appropriate template.
 *
 * @since 1.0.0
 */
function video_central_is_theme_compat_original_template($template = '')
{
    $video_central = video_central();

    if (empty($video_central->theme_compat->original_template)) {
        return false;
    }

    return (bool) ($video_central->theme_compat->original_template === $template);
}

/**
 * Register a new Video Central theme package to the active theme packages array.
 *
 * @since 1.0.0
 *
 * @param array $theme
 */
function video_central_register_theme_package($theme = array(), $override = true)
{

    // Create new Video_Central_Theme_Compat object from the $theme array
    if (is_array($theme)) {
        $theme = new Video_Central_Theme_Compat($theme);
    }

    // Bail if $theme isn't a proper object
    if (!is_a($theme, 'Video_Central_Theme_Compat')) {
        return;
    }

    // Load up Video Central
    $video_central = video_central();

    // Only override if the flag is set and not previously registered
    if (empty($video_central->theme_compat->packages[$theme->id]) || (true === $override)) {
        $video_central->theme_compat->packages[$theme->id] = $theme;
    }
}
/**
 * This fun little function fills up some WordPress globals with dummy data to
 * stop your average page template from complaining about it missing.
 *
 * @since 1.0.0
 *
 * @global WP_Query $wp_query
 * @global object $post
 *
 * @param array $args
 */
function video_central_theme_compat_reset_post($args = array())
{
    global $wp_query, $post;

    // Switch defaults if post is set
    if (isset($wp_query->post)) {
        $dummy = video_central_parse_args($args, array(
            'ID' => $wp_query->post->ID,
            'post_status' => $wp_query->post->post_status,
            'post_author' => $wp_query->post->post_author,
            'post_parent' => $wp_query->post->post_parent,
            'post_type' => $wp_query->post->post_type,
            'post_date' => $wp_query->post->post_date,
            'post_date_gmt' => $wp_query->post->post_date_gmt,
            'post_modified' => $wp_query->post->post_modified,
            'post_modified_gmt' => $wp_query->post->post_modified_gmt,
            'post_content' => $wp_query->post->post_content,
            'post_title' => $wp_query->post->post_title,
            'post_excerpt' => $wp_query->post->post_excerpt,
            'post_content_filtered' => $wp_query->post->post_content_filtered,
            'post_mime_type' => $wp_query->post->post_mime_type,
            'post_password' => $wp_query->post->post_password,
            'post_name' => $wp_query->post->post_name,
            'guid' => $wp_query->post->guid,
            'menu_order' => $wp_query->post->menu_order,
            'pinged' => $wp_query->post->pinged,
            'to_ping' => $wp_query->post->to_ping,
            'ping_status' => $wp_query->post->ping_status,
            'comment_status' => $wp_query->post->comment_status,
            'comment_count' => $wp_query->post->comment_count,
            'filter' => $wp_query->post->filter,

            'is_404' => false,
            'is_page' => false,
            'is_single' => false,
            'is_archive' => false,
            'is_tax' => false,
        ), 'theme_compat_reset_post');
    } else {
        $dummy = video_central_parse_args($args, array(
            'ID' => -9999,
            'post_status' => 'publish',
            'post_author' => 0,
            'post_parent' => 0,
            'post_type' => 'page',
            'post_date' => 0,
            'post_date_gmt' => 0,
            'post_modified' => 0,
            'post_modified_gmt' => 0,
            'post_content' => '',
            'post_title' => '',
            'post_excerpt' => '',
            'post_content_filtered' => '',
            'post_mime_type' => '',
            'post_password' => '',
            'post_name' => '',
            'guid' => '',
            'menu_order' => 0,
            'pinged' => '',
            'to_ping' => '',
            'ping_status' => '',
            'comment_status' => 'closed',
            'comment_count' => 0,
            'filter' => 'raw',

            'is_404' => false,
            'is_page' => false,
            'is_single' => false,
            'is_archive' => false,
            'is_tax' => false,
        ), 'theme_compat_reset_post');
    }

    // Bail if dummy post is empty
    if (empty($dummy)) {
        return;
    }

    // Set the $post global
    $post = new WP_Post((object) $dummy);

    // Copy the new post global into the main $wp_query
    $wp_query->post = $post;
    $wp_query->posts = array($post);

    // Prevent comments form from appearing
    $wp_query->post_count = 1;
    $wp_query->is_404 = $dummy['is_404'];
    $wp_query->is_page = $dummy['is_page'];
    $wp_query->is_single = $dummy['is_single'];
    $wp_query->is_archive = $dummy['is_archive'];
    $wp_query->is_tax = $dummy['is_tax'];

    // Clean up the dummy post
    unset($dummy);

    /*
     * Force the header back to 200 status if not a deliberate 404
     */
    if (!$wp_query->is_404()) {
        status_header(200);
    }

    // If we are resetting a post, we are in theme compat
    video_central_set_theme_compat_active(true);
}

/**
 * Reset main query vars and filter 'the_content' to output a Video Central
 * template part as needed.
 *
 * @since 1.0.0
 *
 * @param string $template
 *
 * @uses video_central_is_single_user() To check if page is single user
 * @uses video_central_get_single_user_template() To get user template
 * @uses video_central_is_single_user_edit() To check if page is single user edit
 * @uses video_central_get_single_user_edit_template() To get user edit template
 * @uses video_central_is_single_view() To check if page is single view
 * @uses video_central_get_single_view_template() To get view template
 * @uses video_central_is_search() To check if page is search
 * @uses video_central_get_search_template() To get search template
 * @uses video_central_is_video_edit() To check if page is video edit
 * @uses video_central_get_video_edit_template() To get video edit template
 * @uses video_central_get_video_edit_template() To get video edit template
 * @uses video_central_set_theme_compat_template() To set the global theme compat template
 */
function video_central_template_include_theme_compat($template = '')
{

    /*
     * Bail if a root template was already found. This prevents unintended
     * recursive filtering of 'the_content'.
     *
     * @link http://video_central.trac.wordpress.org/ticket/2429
     */
    if (video_central_is_template_included()) {
        return $template;
    }

    // Define local variable(s)
    $video_central_shortcodes = video_central()->shortcodes;

    // Bail if shortcodes are unset somehow
    if (!is_a($video_central_shortcodes, 'Radium_Video_Shortcodes')) {
        return $template;
    }

    if (video_central_is_video_archive()) {
        $new_content = $video_central_shortcodes->display_videos_index();

        /* Videos ************************************************************/

        // Reset post
        video_central_theme_compat_reset_post(array(
            'ID' => get_the_id(),
            'post_title' => get_the_title(),
            'post_author' => '1',
            'post_date' => 0,
            'post_content' => $new_content,
            'post_type' => 'video',
            'post_status' => 'publish',
            'is_single' => true,
            'comment_status' => 'closed',
        ));

    /* Views *************************************************************/
    } elseif (video_central_is_single_view()) {

        // Reset post
        video_central_theme_compat_reset_post(array(
            'ID' => 0,
            'post_title' => video_central_get_view_title(),
            'post_author' => 0,
            'post_date' => 0,
            'post_content' => $video_central_shortcodes->display_view(array('id' => get_query_var(video_central_get_view_rewrite_id()))),
            'post_type' => '',
            'post_status' => video_central_get_public_status_id(),
            'comment_status' => 'closed',
        ));

    /* Search ************************************************************/
    } elseif (video_central_is_search()) {

        // Reset post
        video_central_theme_compat_reset_post(array(
            'ID' => 0,
            'post_title' => video_central_get_search_title(),
            'post_author' => 0,
            'post_date' => 0,
            'post_content' => $video_central_shortcodes->display_search(array('search' => get_query_var(video_central_get_search_rewrite_id()))),
            'post_type' => '',
            'post_status' => video_central_get_public_status_id(),
            'comment_status' => 'closed',
        ));
    }

    /*
     * Bail if the template already matches a Video Central template. This includes
     * archive-* and single-* WordPress post_type matches (allowing
     * themes to use the expected format) as well as all Video Central specific
     * template files for users, videos, videos, etc...
     *
     * We do this after the above checks to prevent incorrect 404 body classes
     * and header statuses, as well as to set the post global as needed.
     *
     */
    if (video_central_is_template_included()) {
        return $template;

    /*
     * If we are relying on Video Central's built in theme compatibility to load
     * the proper content, we need to intercept the_content, replace the
     * output, and display ours instead.
     *
     * To do this, we first remove all filters from 'the_content' and hook
     * our own function into it, which runs a series of checks to determine
     * the context, and then uses the built in shortcodes to output the
     * correct results from inside an output buffer.
     *
     * Uses video_central_get_theme_compat_templates() to provide fall-backs that
     * should be coded without superfluous mark-up and logic (prev/next
     * navigation, comments, date/time, etc...)
     *
     * Hook into the 'video_central_get_video_central_template' to override the array of
     * possible templates, or 'video_central_video_central_template' to override the result.
     */
    } elseif (video_central_is_theme_compat_active()) {
        video_central_remove_all_filters('the_content');

        $template = video_central_get_theme_compat_templates();
    }

    return apply_filters(__FUNCTION__, $template);
}

/** Helpers *******************************************************************/

/**
 * Remove the canonical redirect to allow pretty pagination.
 *
 * @since 1.0.0
 *
 * @param string $redirect_url Redirect url
 *
 * @uses WP_Rewrite::using_permalinks() To check if the blog is using permalinks
 * @uses video_central_get_paged() To get the current page number
 * @uses video_central_is_single_video() To check if it's a video page
 *
 * @return bool|string False if it's a video and first page,
 *                     otherwise the redirect url
 */
function video_central_redirect_canonical($redirect_url)
{
    global $wp_rewrite;

    // Canonical is for the beautiful
    if ($wp_rewrite->using_permalinks()) {

        // If viewing beyond page 1 of several
        if (1 < video_central_get_paged()) {
            if (video_central_is_single_video()) {
                $redirect_url = false;

            // ...and any single anything else...
            //
            // @todo - Find a more accurate way to disable paged canonicals for
            //          paged shortcode usage within other posts.
            } elseif (is_page() || is_singular()) {
                $redirect_url = false;
            }
        }
    }

    return $redirect_url;
}

/** Filters *******************************************************************/

/**
 * Removes all filters from a WordPress filter, and stashes them in the $video_central
 * global in the event they need to be restored later.
 *
 * @since 1.0.0
 *
 * @global WP_filter $wp_filter
 * @global array $merged_filters
 *
 * @param string $tag
 * @param int    $priority
 *
 * @return bool
 */
function video_central_remove_all_filters($tag, $priority = false)
{
    global $wp_filter, $merged_filters;

    $video_central = video_central();

    // Filters exist
    if (isset($wp_filter[$tag])) {

        // Filters exist in this priority
        if (!empty($priority) && isset($wp_filter[$tag][$priority])) {

            // Store filters in a backup
            $video_central->filters->wp_filter[$tag][$priority] = $wp_filter[$tag][$priority];

            // Unset the filters
            unset($wp_filter[$tag][$priority]);

        // Priority is empty
        } else {

            // Store filters in a backup
            $video_central->filters->wp_filter[$tag] = $wp_filter[$tag];

            // Unset the filters
            unset($wp_filter[$tag]);
        }
    }

    // Check merged filters
    if (isset($merged_filters[$tag])) {

        // Store filters in a backup
        $video_central->filters->merged_filters[$tag] = $merged_filters[$tag];

        // Unset the filters
        unset($merged_filters[$tag]);
    }

    return true;
}

/**
 * Restores filters from the $video_central global that were removed using
 * video_central_remove_all_filters().
 *
 * @since 1.0.0
 *
 * @global WP_filter $wp_filter
 * @global array $merged_filters
 *
 * @param string $tag
 * @param int    $priority
 *
 * @return bool
 */
function video_central_restore_all_filters($tag, $priority = false)
{
    global $wp_filter, $merged_filters;

    $video_central = video_central();

    // Filters exist
    if (isset($video_central->filters->wp_filter[$tag])) {

        // Filters exist in this priority
        if (!empty($priority) && isset($video_central->filters->wp_filter[$tag][$priority])) {

            // Store filters in a backup
            $wp_filter[$tag][$priority] = $video_central->filters->wp_filter[$tag][$priority];

            // Unset the filters
            unset($video_central->filters->wp_filter[$tag][$priority]);

        // Priority is empty
        } else {

            // Store filters in a backup
            $wp_filter[$tag] = $video_central->filters->wp_filter[$tag];

            // Unset the filters
            unset($video_central->filters->wp_filter[$tag]);
        }
    }

    // Check merged filters
    if (isset($video_central->filters->merged_filters[$tag])) {

        // Store filters in a backup
        $merged_filters[$tag] = $video_central->filters->merged_filters[$tag];

        // Unset the filters
        unset($video_central->filters->merged_filters[$tag]);
    }

    return true;
}
