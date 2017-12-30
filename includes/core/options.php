<?php

/**
 * Video Central Options.
 */

/**
 * Get the default site options and their values.
 *
 * These option
 *
 * @since 1.0.0
 *
 * @return array Filtered option names and values
 */
function video_central_get_default_options()
{

    // Default options
    return apply_filters(__FUNCTION__, array(

        /* DB Version ********************************************************/

        '_video_central_db_version' => video_central()->db_version,

        /* Settings **********************************************************/
        '_video_central_theme_package_id' => 'default',                  // The ID for the current theme package

        /* Video Root ********************************************************/
        '_video_central_root_slug' => 'videos',    // Videos archive slug
        '_video_central_show_on_root' => 'videos',    // What to show on root (index || latest)
        '_video_central_show_slider_root' => 1, //show slider on root
        '_video_central_include_root' => 1,           // Include video before single slugs
        '_video_central_allow_loop_actions' => 1, //allow loop action sorting videos
        '_video_central_allow_loop_sort_actions' => 1, //allow loop action sorting videos
        '_video_central_allow_loop_grid_actions' => 0, //allow loop actions on grid
        '_video_central_allow_search' => 1, // Allow video search
        '_video_central_videos_per_page' => 16, // Number of videos per page
        '_video_central_content_toggle' => 1, // toggle video content

        '_video_central_allow_video_categories' => 1,
        '_video_central_allow_video_tags' => 1,

        '_video_central_video_tag_slug' => 'video-category',
        '_video_central_video_category_slug' => 'video-tag',

        /* Other Slugs *******************************************************/

        '_video_central_view_slug' => 'view',      // View slug
        '_video_central_search_slug' => 'search',    // Search slug

        '_video_central_loop_item_size' => 'small-block-grid-4', //allow loop action default size

        /* Single Video View ********************************************************/
        '_video_central_allow_comments' => 1,           // allow comments in videos
        '_video_central_allow_video_meta' => 1,           // allow comments in videos
        '_video_central_allow_likes' => 1,           // allow comments in videos
        '_video_central_allow_social_links' => 1,           // allow comments in videos
        '_video_central_allow_related_videos' => 1,           // show related videos
        '_video_central_related_videos_count' => 12, //number of related videos to show
        '_video_central_randomize_related_videos' => 1,

        '_video_central_video_slug' => 'video',

        /* Single Slugs ******************************************************/

        /* API Keys ******************************************************/
        '_video_central_youtube_api_key' => '',
        '_video_central_youtube_show_api_daily_quota' => 1,
        '_video_central_youtube_api_client_id' => '',
        '_video_central_youtube_api_client_secret' => '',

    ));
}

/**
 * Register Image Sizes.
 *
 * @since 2.1.8
 */
function video_central_add_image_sizes($add_image_size = false)
{

    // Content Width
    $content_width = apply_filters('video_central_content_width', 1240); // Default width of primary content area

    // Crop sizes
    $sizes = array(
        'video_central_large' => array(
            'width' => $content_width,  // 940 => Full width thumb for 1-col page
            'height' => 9999,
            'crop' => false,
        ),
        'video_central_medium' => array(
            'width' => 750,             // 620 => Full width thumb for 2-col/3-col page
            'height' => 9999,
            'crop' => false,
        ),
        'video_central_small' => array(
            'width' => 195,             // Square'ish thumb floated left
            'height' => 195,
            'crop' => false,
        ),
    );
    $sizes = apply_filters(__FUNCTION__, $sizes);

    if ($add_image_size) {

        // Add image sizes
        foreach ($sizes as $size => $atts) {
            add_image_size($size, $atts['width'], $atts['height'], $atts['crop']);
        }
    }

    return apply_filters(__FUNCTION__, $sizes);
}

/**
 * Add default options.
 *
 * Hooked to video_central_activate, it is only called once when Video Central is activated.
 * This is non-destructive, so existing settings will not be overridden.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_default_options() To get default options
 * @uses add_option() Adds default options
 * @uses do_action() Calls 'video_central_add_options'
 */
function video_central_add_options()
{

    // Add default options
    foreach (video_central_get_default_options() as $key => $value) {
        add_option($key, $value);
    }

    // Allow previously activated plugins to append their own options.
    do_action(__FUNCTION__);
}

/**
 * Delete default options.
 *
 * Hooked to video_central_uninstall, it is only called once when Video Central is uninstalled.
 * This is destructive, so existing settings will be destroyed.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_default_options() To get default options
 * @uses delete_option() Removes default options
 * @uses do_action() Calls 'video_central_delete_options'
 */
function video_central_delete_options()
{

    // Add default options
    foreach (array_keys(video_central_get_default_options()) as $key) {
        delete_option($key);
    }

    // Allow previously activated plugins to append their own options.
    do_action(__FUNCTION__);
}

/**
 * Add filters to each Video Central option and allow them to be overloaded from
 * inside the $video_central->options array.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_default_options() To get default options
 * @uses add_filter() To add filters to 'pre_option_{$key}'
 * @uses do_action() Calls 'video_central_add_option_filters'
 */
function video_central_setup_option_filters()
{

    // Add filters to each Video Central option
    foreach (array_keys(video_central_get_default_options()) as $key) {
        add_filter('pre_option_'.$key, 'video_central_pre_get_option');
    }

    // Allow previously activated plugins to append their own options.
    do_action(__FUNCTION__);
}

/**
 * Filter default options and allow them to be overloaded from inside the
 * $video_central->options array.
 *
 * @since 1.0.0
 *
 * @param bool $value Optional. Default value false
 *
 * @return mixed false if not overloaded, mixed if set
 */
function video_central_pre_get_option($value = '')
{

    // Remove the filter prefix
    $option = str_replace('pre_option_', '', current_filter());

    // Check the options global for preset value
    if (isset(video_central()->options[$option])) {
        $value = video_central()->options[$option];
    }

    // Always return a value, even if false
    return $value;
}

/**
 * Get the current theme package ID.
 *
 * @since 1.0.0
 *
 * @param $default string Optional. Default value 'default'
 *
 * @uses get_option() To get the subtheme option
 *
 * @return string ID of the subtheme
 */
function video_central_get_theme_package_id($default = 'default')
{
    return apply_filters(__FUNCTION__, get_option('_video_central_theme_package_id', $default));
}

/** Slugs *********************************************************************/

/**
 * Return the root slug.
 *
 * @since 1.0.0
 *
 * @return string
 */
function video_central_get_root_slug($default = 'videos')
{
    return apply_filters(__FUNCTION__, get_option('_video_central_root_slug', $default));
}

/**
 * Are we including the root slug in front of video pages?
 *
 * @since 1.0.0
 *
 * @return bool
 */
function video_central_include_root_slug($default = 1)
{
    return (bool) apply_filters(__FUNCTION__, (bool) get_option('_video_central_include_root', $default));
}

/**
 * Return what to show on root.
 *
 * @since 1.0.0
 *
 * @return string
 */
function video_central_show_on_root($default = 'videos')
{
    return apply_filters(__FUNCTION__, get_option('_video_central_show_on_root', $default));
}

/**
 * Return what to show slider on root.
 *
 * @since 1.0.0
 *
 * @return string
 */
function video_central_show_slider_on_root($default = 1)
{
    return apply_filters(__FUNCTION__, (bool) get_option('_video_central_show_slider_on_root', $default));
}

/**
 * Maybe return the root slug, based on whether or not it's included in the url.
 *
 * @since 1.0.0
 *
 * @return string
 */
function video_central_maybe_get_root_slug()
{
    $retval = '';

    if (video_central_get_root_slug() && video_central_include_root_slug()) {
        $retval = trailingslashit(video_central_get_root_slug());
    }

    return apply_filters(__FUNCTION__, $retval);
}

/**
 * Return the single video slug.
 *
 * @since 1.0.0
 *
 * @return string
 */
function video_central_get_video_slug($default = 'video')
{
    ;

    return apply_filters(__FUNCTION__, video_central_maybe_get_root_slug().get_option('_video_central_video_slug', $default));
}

/**
 * Return the video-tag taxonomy slug.
 *
 * @since 1.0.0
 *
 * @return string
 */
function video_central_get_video_tag_tax_slug($default = 'video-tag')
{
    return apply_filters('video_central_get_video_tag_tax_slug', video_central_maybe_get_root_slug().get_option('_video_central_video_tag_slug', $default));
}

/**
 * Return the video-category taxonomy slug.
 *
 * @since 1.0.0
 *
 * @return string
 */
function video_central_get_video_category_tax_slug($default = 'video-category')
{
    return apply_filters('video_central_get_video_category_tax_slug', video_central_maybe_get_root_slug().get_option('_video_central_video_category_slug', $default));
}

/**
 * Return the single user slug.
 *
 * @since 1.0.0
 *
 * @return string
 */
function video_central_get_user_slug($default = 'user')
{
    return apply_filters(__FUNCTION__, video_central_maybe_get_root_slug().get_option('_video_central_user_slug', $default));
}

/**
 * Return the single user favorites slug.
 *
 * @since 1.0.0
 *
 * @return string
 */
function video_central_get_user_favorites_slug($default = 'favorites')
{
    return apply_filters(__FUNCTION__, get_option('_video_central_user_favs_slug', $default));
}

/**
 * Return the single user subscriptions slug.
 *
 * @since 1.0.0
 *
 * @return string
 */
function video_central_get_user_subscriptions_slug($default = 'subscriptions')
{
    return apply_filters(__FUNCTION__, get_option('_video_central_user_subs_slug', $default));
}

/**
 * Return the video view slug.
 *
 * @since 1.0.0
 *
 * @return string
 */
function video_central_get_view_slug($default = 'view')
{
    return apply_filters(__FUNCTION__, video_central_maybe_get_root_slug().get_option('_video_central_view_slug', $default));
}

/**
 * Return the search slug.
 *
 * @since 1.0.0
 *
 * @return string
 */
function video_central_get_search_slug($default = 'search')
{
    return apply_filters(__FUNCTION__, video_central_maybe_get_root_slug().get_option('_video_central_search_slug', $default));
}

/**
 * Return the playlist slug.
 *
 * @since 1.2.0
 *
 * @return string
 */
function video_central_get_playlist_slug($default = 'playlists')
{
    return apply_filters(__FUNCTION__, get_option('_video_central_playlist_slug', $default));
}

/** Active? *******************************************************************/

/**
 * Is video searching allowed.
 *
 * @since 1.0.0
 *
 * @param $default bool Optional. Default value true
 *
 * @uses get_option() To get the video search setting
 *
 * @return bool Is video searching allowed?
 */
function video_central_allow_search($default = 1)
{
    return (bool) apply_filters(__FUNCTION__, (bool) get_option('_video_central_allow_search', $default));
}

/**
 * Are video tags allowed.
 *
 * @since 1.0.0
 *
 * @param $default bool Optional. Default value true
 *
 * @uses get_option() To get the allow tags
 *
 * @return bool Are tags allowed?
 */
function video_central_allow_video_tags($default = 1)
{
    return (bool) apply_filters(__FUNCTION__, (bool) get_option('_video_central_allow_video_tags', $default));
}

/**
 * Are video tags allowed.
 *
 * @since 1.0.0
 *
 * @param $default bool Optional. Default value true
 *
 * @uses get_option() To get the allow tags
 *
 * @return bool Are tags allowed?
 */
function video_central_allow_video_categories($default = 1)
{
    return (bool) apply_filters(__FUNCTION__, (bool) get_option('_video_central_allow_video_categories', $default));
}

/**
 * Are video tags allowed.
 *
 * @since 1.0.0
 *
 * @param $default bool Optional. Default value true
 *
 * @uses get_option() To get the allow tags
 *
 * @return bool Are tags allowed?
 */
function video_central_allow_playlist_tags($default = 1)
{
    return (bool) apply_filters(__FUNCTION__, (bool) get_option('_video_central_allow_playlist_tags', $default));
}

/**
 * Are video tags allowed.
 *
 * @since 1.0.0
 *
 * @param $default bool Optional. Default value true
 *
 * @uses get_option() To get the allow tags
 *
 * @return bool Are tags allowed?
 */
function video_central_allow_playlist_categories($default = 1)
{
    return (bool) apply_filters(__FUNCTION__, (bool) get_option('_video_central_allow_playlist_categories', $default));
}

/**
 * Are related videos allowed.
 *
 * @since 1.0.0
 *
 * @param $default bool Optional. Default value true
 *
 * @uses get_option() To get the allow tags
 *
 * @return bool Are tags allowed?
 */
function video_central_allow_related_videos($default = 1)
{
    return (bool) apply_filters(__FUNCTION__, (bool) get_option('_video_central_allow_related_videos', $default));
}

/**
 * Are comments allowed.
 *
 * @since 1.0.0
 *
 * @param $default bool Optional. Default value true
 *
 * @uses get_option() To get the allow tags
 *
 * @return bool Are tags allowed?
 */
function video_central_allow_comments($default = 1)
{
    return (bool) apply_filters(__FUNCTION__, (bool) get_option('_video_central_allow_comments', $default));
}

/**
 * Are allowed video meta.
 *
 * @since 1.2.0
 *
 * @param $default bool Optional. Default value true
 *
 * @uses get_option() To get the allow tags
 *
 * @return bool Are tags allowed?
 */
function video_central_allow_video_meta($default = 1)
{
    return (bool) apply_filters(__FUNCTION__, (bool) get_option('_video_central_allow_video_meta', $default));
}

/**
 * Are social links allowed.
 *
 * @since 1.2.0
 *
 * @param $default bool Optional. Default value true
 *
 * @uses get_option() To get the allow tags
 *
 * @return bool Are tags allowed?
 */
function video_central_allow_social_links($default = 1)
{
    return (bool) apply_filters(__FUNCTION__, (bool) get_option('_video_central_allow_social_links', $default));
}

/**
 * Are loop actions allowed.
 *
 * @since 1.0.0
 *
 * @param $default bool Optional. Default value true
 *
 * @uses get_option() To get the allow tags
 *
 * @return bool Are tags allowed?
 */
function video_central_allow_loop_actions($default = 1)
{
    return (bool) apply_filters(__FUNCTION__, (bool) get_option('_video_central_allow_loop_actions', $default));
}

/**
 * Are loop sort actions allowed.
 *
 * @since 1.0.0
 *
 * @param $default bool Optional. Default value true
 *
 * @uses get_option() To get the allow tags
 *
 * @return bool Are tags allowed?
 */
function video_central_allow_loop_sort_actions($default = 0)
{
    return (bool) apply_filters(__FUNCTION__, (bool) get_option('_video_central_allow_loop_sort_actions', $default));
}

/**
 * Are loop grid actions allowed.
 *
 * @since 1.0.0
 *
 * @param $default bool Optional. Default value true
 *
 * @uses get_option() To get the allow tags
 *
 * @return bool Are tags allowed?
 */
function video_central_allow_loop_grid_actions($default = 0)
{
    return (bool) apply_filters(__FUNCTION__, (bool) get_option('_video_central_allow_loop_grid_actions', $default));
}

/**
 * Set loop grid class.
 *
 * @since 1.0.0
 *
 * @param $default bool Optional. Default value true
 *
 * @uses get_option() To get the allow tags
 *
 * @return bool Are tags allowed?
 */
function video_central_loop_item_size($default = 'small-block-grid-2 large-block-grid-4')
{
    return apply_filters(__FUNCTION__, get_option('_video_central_loop_item_size', $default));
}

/**
 * Integrate settings into existing WordPress pages.
 *
 * @since 1.0.0
 *
 * @param $default bool Optional. Default value false
 *
 * @uses get_option() To get the admin integration setting
 *
 * @return bool To deeply integrate settings, or not
 */
function video_central_settings_integration($default = 0)
{
    return (bool) apply_filters(__FUNCTION__, (bool) get_option('_video_central_settings_integration', $default));
}

/**
 * Toggle Video Content.
 *
 * @since 1.0.0
 *
 * @param $default bool Optional. Default value false
 *
 * @uses get_option() To get the admin integration setting
 *
 * @return bool To deeply integrate settings, or not
 */
function video_central_content_toggle($default = 1)
{
    return (bool) apply_filters(__FUNCTION__, (bool) get_option('_video_central_content_toggle', $default));
}

/**
 * Video Thumbnail Dimensions.
 *
 * @since 1.0.0
 *
 * @param $default array Optional. Default values width= 300px, height = 150px, crop = true
 *
 * @uses get_option() To get the admin integration setting
 *
 * @return bool To deeply integrate settings, or not
 */
function video_central_thumbnail_dimensions($default = array('width' => 300, 'height' => 150, 'crop' => true))
{
    return apply_filters(__FUNCTION__, get_option('_video_central_thumbnail_dimensions', $default));
}

/**
 * Video Short Title Length.
 *
 * @since 1.0.0
 *
 * @param $default array Optional. Default values 65 characters
 *
 * @uses get_option() To get the admin integration setting
 *
 * @return int
 */
function video_central_short_title_length($default = 65)
{
    return apply_filters(__FUNCTION__, get_option('_video_central_short_title_length', $default));
}

/**
 * Youtube Api Key.
 *
 * @since 1.0.0
 *
 * @param $default string Optional.
 *
 * @uses get_option() To get the admin integration setting
 *
 * @return string
 */
function video_central_youtube_api_key($default = '')
{
    return apply_filters(__FUNCTION__, get_option('_video_central_youtube_api_key', $default));
}

/**
 * Youtube Client ID.
 *
 * @since 1.0.0
 *
 * @param $default string Optional.
 *
 * @uses get_option() To get the admin integration setting
 *
 * @return string
 */
function video_central_youtube_api_client_id($default = '')
{
    return apply_filters(__FUNCTION__, get_option('_video_central_youtube_api_client_id', $default));
}

/**
 * Youtube Client Secret.
 *
 * @since 1.0.0
 *
 * @param $default string Optional.
 *
 * @uses get_option() To get the admin integration setting
 *
 * @return string
 */
function video_central_youtube_api_client_secret($default = '')
{
    return apply_filters(__FUNCTION__, get_option('_video_central_youtube_api_client_secret', $default));
}

/**
 * Youtube Api Daily Quota Secret.
 *
 * @since 1.0.0
 *
 * @param $default string Optional.
 *
 * @uses get_option() To get the admin integration setting
 *
 * @return bool
 */
function video_central_youtube_api_daily_quota($default = true)
{
    return (bool) apply_filters(__FUNCTION__, get_option('_video_central_youtube_api_daily_quota', $default));
}

/**
 * Returns OAuth credentials registered by user.
 *
 * @since 1.0.0
 *
 * @param $default string Optional.
 *
 * @uses get_option() To get the admin integration setting
 *
 * @return array
 */
function video_central_youtube_api_oauth_details($default = array())
{
    $args = array(
        'client_id' => video_central_youtube_api_client_id(),
        'client_secret' => video_central_youtube_api_client_secret(),
        'token' => array(
            'value' => '',
            'valid' => 0,
            'time' => time(),
        ),
    );

    $arg = video_central_parse_args($args, $default, 'youtube_api_oauth_details');

    return apply_filters(__FUNCTION__, get_option('_video_central_youtube_api_oauth_details', $arg));
}
