<?php

/**
 * Video Central Actions.
 *
 *
 * @see /core/filters.php
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
 *           v--WordPress Actions        v--Video Central Sub-actions
 */
add_action('plugins_loaded',           'video_central_loaded',                 10);
add_action('init',                     'video_central_init',                   0); // Early for video_central_register
add_action('parse_query',              'video_central_parse_query',            2); // Early for overrides
add_action('widgets_init',             'video_central_widgets_init',           10);
add_action('wp_enqueue_scripts',       'video_central_enqueue_scripts',        10);
add_action('setup_theme',              'video_central_init_classes',           10);
add_action('setup_theme',              'video_central_setup_theme',            11);
add_action('after_setup_theme',        'video_central_after_setup_theme',      10);
//add_action( 'wp_head',                'video_central__player_js_swf', 10); //player fallback

/*
 * video_central_loaded - Attached to 'plugins_loaded' above
 *
 * Attach various loader actions to the video_central_loaded action.
 * The load order helps to execute code at the correct time.
 *                                                         v---Load order
 */
add_action('video_central_loaded', 'video_central_register_theme_packages',   14);

/*
 * video_central_init - Attached to 'init' above
 *
 * Attach various initialization actions to the init action.
 * The load order helps to execute code at the correct time.
 *                                               v---Load order
 */
add_action('video_central_init', 'video_central_load_textdomain',   0);
add_action('video_central_init', 'video_central_register',          0);
add_action('video_central_init', 'video_central_add_rewrite_tags',  20);
add_action('video_central_init', 'video_central_add_rewrite_categories',  20);
add_action('video_central_init', 'video_central_add_rewrite_rules', 30);
add_action('video_central_init', 'video_central_add_permastructs',  40);

/*
 * video_central_register - Attached to 'init' above on 0 priority
 *
 * Attach various initialization actions early to the init action.
 * The load order helps to execute code at the correct time.
 *                                                         v---Load order
 */

add_action('video_central_register', 'video_central_register_post_types',     2);
add_action('video_central_register', 'video_central_register_taxonomies',     3);
add_action('video_central_register', 'video_central_register_views',          8);
add_action('video_central_register', 'video_central_register_shortcodes',     10);

// Try to load the video-central-functions.php file from the active themes
add_action('video_central_after_setup_theme', 'video_central_load_theme_functions', 10);

add_action('video_central_activation',    'video_central_add_activation_redirect');

/* Widgets */
add_action('video_central_widgets_init', array('Video_Central_Widget_Categories', 'register_widget'), 10);
add_action('video_central_widgets_init', array('Video_Central_Featured_Widget',    'register_widget'), 10);
add_action('video_central_widgets_init', array('Video_Central_Popular_Widget',    'register_widget'), 10);
add_action('video_central_widgets_init', array('Video_Central_Recent_Widget',    'register_widget'), 10);
add_action('video_central_widgets_init', array('Video_Central_Widget_Search',    'register_widget'), 10);
add_action('video_central_widgets_init', array('Video_Central_Widget_Tags',        'register_widget'), 10);

//add views counter
add_action('video_central_template_after_video_player', 'video_central_set_video_views');

//add video duration
add_action('video_central_video_duration', 'video_central_video_duration');

//add likes
add_action('video_central_video_sentiment', 'video_central_likes');

//categories list
add_action('video_central_template_content_footer', 'video_central_categories_list');

//Tags list
add_action('video_central_template_content_footer', 'video_central_tags_list');

//add comments
add_action('video_central_template_after_video_content', 'video_central_add_comments');

//add sidebar
add_action('video_central_sidebar', 'video_central_sidebar');

//search redirect
add_action('video_central_get_request', 'video_central__search_results_redirect',     10);
