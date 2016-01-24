<?php

/**
 * Get the default site options and their values.
 *
 * These option
 *
 * @since 1.0.0
 *
 * @return array Filtered option names and values
 */
function video_central_import_get_default_options($options = array())
{
    return apply_filters(__FUNCTION__, array(
        '_video_central_allow_video_imports' => true,
        'public' => true, // post type is public or not
        'import_categories' => true, // import categories from YouTube
        'import_title' => true, // import titles on custom posts
        'import_video_description' => true, // import descriptions on custom posts
        'import_description' => '_video_central_import_description', // import descriptions key on custom posts
        'import_results' => 50, // default number of feed results to display
        'import_status' => 'publish', // default import status of videos
   ));
}

/**
 * Allow video imports.
 *
 * @since 1.0.0
 *
 * @param $default bool Optional. Default value true
 *
 * @uses get_option() To get view status
 *
 * @return bool Are tags allowed?
 */
function video_central_allow_video_imports($default = 1)
{
    return (bool) apply_filters(__FUNCTION__, (bool) get_option('_video_central_allow_video_imports', $default));
}

/**
 * post type is public or not.
 *
 * @since 1.0.0
 *
 * @param $default bool Optional. Default value true
 *
 * @uses get_option() To get view status
 *
 * @return bool Are tags allowed?
 */
function video_central_import_public($default = 1)
{
    return (bool) apply_filters(__FUNCTION__, (bool) get_option('_video_central_import_public', $default));
}

/**
 * Import categories.
 *
 * @since 1.0.0
 *
 * @param $default bool Optional. Default value true
 *
 * @uses get_option() To check whether to import categories
 *
 * @return bool Are tags allowed?
 */
function video_central_import_categories($default = 1)
{
    return (bool) apply_filters(__FUNCTION__, (bool) get_option('_video_central_import_categories', $default));
}

/**
 * Import the title.
 *
 * @since 1.0.0
 *
 * @param $default bool Optional. Default value true
 *
 * @uses get_option() To get the title
 *
 * @return bool Are tags allowed?
 */
function video_central_import_title($default = 1)
{
    return (bool) apply_filters(__FUNCTION__, (bool) get_option('_video_central_import_title', $default));
}

/**
 * Save video description.
 *
 * @since 1.0.0
 *
 * @param $default string .
 *
 * @uses get_option() To get the allow tags
 *
 * @return bool Are tags allowed?
 */
function video_central_import_video_description($default = true)
{
    return apply_filters(__FUNCTION__, get_option('_video_central_import_video_description', $default));
}

/**
 * Where to save the description.
 *
 * @since 1.0.0
 *
 * @param $default string .
 *
 * @uses get_option() To get the allow tags
 *
 * @return bool Are tags allowed?
 */
function video_central_import_description_key($default = '_video_central_description')
{
    if (video_central_import_as_post()) {
        $default = 'content';
    }

    return apply_filters(__FUNCTION__, get_option('_video_central_import_description_key', $default));
}

/**
 * How many results to get.
 *
 * @since 1.2.0
 *
 * @param $default bool Optional. Default value 50
 *
 * @uses get_option() To get the results
 *
 * @return bool Are tags allowed?
 */
function video_central_import_results_per_page($default = 50)
{
    return apply_filters(__FUNCTION__, get_option('_video_central_import_results_per_page', $default));
}

/**
 * How many results to get.
 *
 * @since 1.0.0
 *
 * @param $default bool Optional. Default value 50
 *
 * @uses get_option() To get the results
 *
 * @return bool Are tags allowed?
 */
function video_central_auto_import_results_per_page($default = 50)
{
    return apply_filters(__FUNCTION__, get_option('_video_central_auto_import_results_per_page', $default));
}

/**
 * Import status.
 *
 * @since 1.0.0
 *
 * @param $default bool Optional. Default value true
 *
 * @uses get_option() To get the allow tags
 *
 * @return bool Are tags allowed?
 */
function video_central_import_status($default = 'publish')
{
    return apply_filters(__FUNCTION__, get_option('_video_central_import_status', $default));
}
/**
 * Import date.
 *
 * @since 1.2.0
 *
 * @param $default bool Optional. Default value true
 *
 * @uses get_option() To get the allow tags
 *
 * @return bool import date
 */
function video_central_import_video_date($default = false)
{
    return apply_filters(__FUNCTION__, get_option('_video_central_import_video_date', $default));
}

/**
 * Import as post.
 *
 * @since 1.2.0
 *
 * @param $default bool Optional. Default value true
 *
 * @uses get_option() To get the allow tags
 *
 * @return bool Are tags allowed?
 */
function video_central_import_as_post($default = false)
{
    return apply_filters(__FUNCTION__, get_option('_video_central_import_as_post', $default));
}

/**
 * Youtube api daily quota.
 *
 * @since 1.2.0
 *
 * @param $default bool Optional. Default value true
 *
 * @uses get_option() To get the allow tags
 *
 * @return bool Are tags allowed?
 */
function video_central_get_youtube_api_daily_quota($default = true)
{
    return (bool) apply_filters(__FUNCTION__, get_option('_video_central_youtube_api_daily_quota', $default));
}
