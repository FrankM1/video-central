<?php

/**
 * Video Central Template Loader.
 */

/**
 * Possibly intercept the template being loaded.
 *
 * Listens to the 'template_include' filter and waits for any Video Central specific
 * template condition to be met. If one is met and the template file exists,
 * it will be used; otherwise
 *
 * @since 1.0.0
 *
 * @param string $template
 *
 * @uses video_central_is_single_user() To check if page is single user
 *
 * @return string The path to the template file that is being used
 */
function video_central_template_include_theme_supports($template = '')
{

    // Viewing a video tag
    if (video_central_is_video_tag() && ($new_template = video_central_get_video_tag_template())) :

    // Editing a video tag
    elseif (video_central_is_video_tag_edit() && ($new_template = video_central_get_video_tag_edit_template())) :

    // Editing a video tag
    elseif (video_central_is_video_category_edit() && ($new_template = video_central_get_video_category_edit_template())) :

    // Viewing a video category
    elseif (video_central_is_video_category() && ($new_template = video_central_get_video_category_template())) :

    // Viewing a video archive
    elseif (video_central_is_video_archive() && ($new_template = video_central_get_video_archive_template())) :

    //single page
    elseif (video_central_is_single_video() && ($new_template = video_central_get_single_video_template())) :

    // Single View
    elseif (video_central_is_single_view() && ($new_template = video_central_get_single_view_template())) :

    // Search
    elseif (video_central_is_search() && ($new_template = video_central_get_search_template())) :

    endif;

    // A Video Central template file was located, so override the WordPress template
    // and use it to switch off Video Central's theme compatibility.
    if (!empty($new_template)) {
        $template = video_central_set_template_included($new_template);
    }

    return apply_filters(__FUNCTION__, $template);
}

/**
 * Set the included template.
 *
 * @since 1.0.0
 *
 * @param mixed $template Default false
 *
 * @return mixed False if empty. Template name if template included
 */
function video_central_set_template_included($template = false)
{
    video_central()->theme_compat->video_central_template = $template;

    return video_central()->theme_compat->video_central_template;
}

/**
 * Is a Video Central template being included?
 *
 * @since 1.0.0
 *
 * @return bool True if yes, false if no
 */
function video_central_is_template_included()
{
    return !empty(video_central()->theme_compat->video_central_template);
}

/** Custom Functions **********************************************************/

/**
 * Attempt to load a custom Video Central functions file, similar to each themes
 * functions.php file.
 *
 * @since 1.0.0
 *
 * @global string $pagenow
 *
 * @uses video_central_locate_template()
 */
function video_central_load_theme_functions()
{
    global $pagenow;

    // If Video Central is being deactivated, do not load any more files
    if (video_central_is_deactivation()) {
        return;
    }

    if (!defined('WP_INSTALLING') || (!empty($pagenow) && ('wp-activate.php' !== $pagenow))) {
        video_central_locate_template('video-central-functions.php', true);
    }
}

/** Individual Templates ******************************************************/

/**
 * Get the single video template.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_video_post_type()
 * @uses video_central_get_query_template()
 *
 * @return string Path to template file
 */
function video_central_get_single_video_template()
{
    $templates = array(
        'single-video.php', // Single Video
    );

    return video_central_get_query_template('single_video', $templates);
}

/**
 * Get the video archive template.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_video_post_type()
 * @uses video_central_get_query_template()
 *
 * @return string Path to template file
 */
function video_central_get_video_archive_template()
{
    $templates = array(
        'archive-video.php', // Video Archive
    );

    return video_central_get_query_template('video_archive', $templates);
}

/**
 * Get the view template.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_view_id()
 * @uses video_central_get_query_template()
 *
 * @return string Path to template file
 */
function video_central_get_single_view_template()
{
    $view_id = video_central_get_view_id();
    $templates = array(
        'single-view-'.$view_id.'.php', // Single View ID
        'view-'.$view_id.'.php', // View ID
        'single-view.php',                  // Single View
        'view.php',                         // View
    );

    return video_central_get_query_template('single_view', $templates);
}

/**
 * Get the search template.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_query_template()
 *
 * @return string Path to template file
 */
function video_central_get_search_template()
{
    $templates = array(
        'page-search-video.php', // Single Search
        'search-video.php',      // Search
    );

    return video_central_get_query_template('single_search', $templates);
}

/**
 * Get the topic template.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_video_tag_tax_id()
 * @uses video_central_get_query_template()
 *
 * @return string Path to template file
 */
function video_central_get_video_tag_template()
{
    $tt_slug = video_central_get_video_tag_slug();
    $tt_id = video_central_get_video_tag_tax_id();
    $templates = array(
        'taxonomy-'.$tt_slug.'.php', // Single Video Tag slug
        'taxonomy-'.$tt_id.'.php', // Single Video Tag ID
    );

    return video_central_get_query_template('video_tag', $templates);
}

/**
 * Get the topic edit template.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_video_tag_tax_id()
 * @uses video_central_get_query_template()
 *
 * @return string Path to template file
 */
function video_central_get_video_tag_edit_template()
{
    $tt_slug = video_central_get_video_tag_slug();
    $tt_id = video_central_get_video_tag_tax_id();
    $templates = array(
        'taxonomy-'.$tt_slug.'-edit.php', // Single Video Tag Edit slug
        'taxonomy-'.$tt_id.'-edit.php',  // Single Video Tag Edit ID
    );

    return video_central_get_query_template('video_tag_edit', $templates);
}

/**
 * Get the topic template.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_video_tag_tax_id()
 * @uses video_central_get_query_template()
 *
 * @return string Path to template file
 */
function video_central_get_video_category_template()
{
    $tt_slug = video_central_get_video_category_slug();
    $tt_id = video_central_get_video_category_tax_id();

    $templates = array(
        'taxonomy-'.$tt_slug.'.php', // Single Video Category slug
        'taxonomy-'.$tt_id.'.php', // Single Video Category ID
    );

    return video_central_get_query_template('video_category', $templates);
}

/**
 * Get the topic edit template.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_video_category_tax_id()
 * @uses video_central_get_query_template()
 *
 * @return string Path to template file
 */
function video_central_get_video_category_edit_template()
{
    $tt_slug = video_central_get_video_category_slug();
    $tt_id = video_central_get_video_category_tax_id();
    $templates = array(
        'taxonomy-'.$tt_slug.'-edit.php', // Single Video Category Edit slug
        'taxonomy-'.$tt_id.'-edit.php',  // Single Video Category Edit ID
    );

    return video_central_get_query_template('video_category_edit', $templates);
}

/**
 * Get the templates to use as the endpoint for Video Central template parts.
 *
 * @since 1.0.0
 *
 * @uses video_central_set_theme_compat_templates()
 * @uses video_central_get_query_template()
 *
 * @return string Path to template file
 */
function video_central_get_theme_compat_templates()
{
    $templates = array(
        'plugin-video-central.php',
        'videos.php',
        'video.php',
        'generic.php',
        'page.php',
        'single.php',
        'index.php',
    );

    return video_central_get_query_template('video_central', $templates);
}

 /**
  * Load the sidebar templates.
  *
  * @since 1.0.0
  *
  * @uses video_central_get_sidebar()
  */
 function video_central_sidebar()
 {
     do_action('get_sidebar', 'video');

     load_template(video_central_get_sidebar());
 }

/**
 * Get the templates to use as the endpoint for Video Central template sidebar.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_query_template()
 *
 * @return string Path to template file
 */
function video_central_get_sidebar()
{
    $templates = array(
        'sidebar-video.php',
        'sidebar.php',
    );

    return video_central_get_query_template('video_central', $templates);
}
