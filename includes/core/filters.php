<?php

/**
 * Video Central Filters.
 *
 *
 * @see /core/actions.php
 */

/*
 * Attach Video Central to WordPress
 *
 * Video Central uses its own internal actions to help aid in third-party plugin
 * development, and to limit the amount of potential future code changes when
 * updates to WordPress core occur.
 *
 * These actions exist to create the concept of 'plugin dependencies'. They
 * provide a safe way for plugins to execute code *only* when Video Central is
 * installed and activated, without needing to do complicated guesswork.
 *
 * For more information on how this works, see the 'Plugin Dependency' section
 * near the bottom of this file.
 *
 *           v--WordPress Actions       v--Video Central Sub-actions
 */
add_filter('template_include',        'video_central_template_include',     10);
add_filter('wp_title',                'video_central_title',                10, 3);
add_filter('body_class',              'video_central_body_class',              10, 2);
add_filter('map_meta_cap',            'video_central_map_meta_caps',        10, 4);
add_filter('allowed_themes',          'video_central_allowed_themes',       10);
add_filter('redirect_canonical',      'video_central_redirect_canonical',   10);
add_filter('plugin_locale',           'video_central_plugin_locale',        10, 2);

/*
 * Template Compatibility
 *
 * If you want to completely bypass this and manage your own custom Video Central
 * template hierarchy, start here by removing this filter, then look at how
 * video_central_template_include() works and do something similar. :)
 */
add_filter('video_central_template_include',   'video_central_template_include_theme_supports', 2, 1);
add_filter('video_central_template_include',   'video_central_template_include_theme_compat',   4, 2);

// Force comments_status on video central post types
add_filter('comments_open', 'video_central_force_comment_status');

// Filter Video Central template locations
add_filter('video_central_get_template_stack', 'video_central_add_template_stack_locations');

//Custom Video order
add_filter('video_central_before_has_videos_parse_args', 'video_central_get_custom_video_order', 1, 10);

//make urls in description clickable
add_filter('video_central_get_content', 'video_central_url_make_clickable', 2, 10);

//override wordpress video shortcode
//add_filter( 'wp_video_shortcode_handler', 'video_central_shortcode' , 99);
//add_filter( 'video_central_shortcode_class', 'video-central-shortcode' );

// Capabilities
add_filter('video_central_map_meta_caps', 'video_central_map_primary_meta_caps',   10, 4); // Primary caps
add_filter('video_central_map_meta_caps', 'video_central_map_video_meta_caps',     10, 4); // Videos
