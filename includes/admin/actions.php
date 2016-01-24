<?php

/**
 * Video_Central Admin Actions.
 *
 *
 * @see video-central-core-actions.php
 * @see video-central-core-filters.php
 */

/*
 * Attach Video_Central to WordPress
 *
 * Video_Central uses its own internal actions to help aid in third-party plugin
 * development, and to limit the amount of potential future code changes when
 * updates to WordPress core occur.
 *
 * These actions exist to create the concept of 'plugin dependencies'. They
 * provide a safe way for plugins to execute code *only* when Video_Central is
 * installed and activated, without needing to do complicated guesswork.
 *
 * For more information on how this works, see the 'Plugin Dependency' section
 * near the bottom of this file.
 *
 *           v--WordPress Actions       v--Video_Central Sub-actions
 */
add_action('admin_menu',              'video_central_admin_menu');
add_action('admin_init',              'video_central_admin_init');
add_action('admin_head',              'video_central_admin_head');
add_action('admin_notices',           'video_central_admin_notices');

add_action('wpmu_new_blog',           'video_central_new_site',               10, 6);

// Hook on to admin_init
add_action('video_central_admin_init', 'video_central_setup_updater',          999);

add_action('video_central_admin_init', 'video_central_register_admin_style');
add_action('video_central_admin_init', 'video_central_register_admin_settings');
add_action('video_central_admin_init', 'video_central_do_activation_redirect', 1);

// Initialize the admin area
add_action('video_central_init', 'video_central_admin');

// Activation
add_action('video_central_activation', 'video_central_delete_rewrite_rules');
//add_action( 'video_central_activation', 'video_central_make_current_user_keymaster' );

// Deactivation
//add_action( 'video_central_deactivation', 'video_central_remove_caps'          );
//add_action( 'video_central_deactivation', 'video_central_delete_rewrite_rules' );

// New Site
add_action('video_central_new_site', 'video_central_create_initial_content', 8);

// Add sample permalink filter
add_filter('post_type_link', 'video_central_filter_sample_permalink', 10, 4);

/**
 * When a new site is created in a multisite installation, run the activation
 * routine on that site.
 *
 * @since 1.0.0
 *
 * @param int     $blog_id
 * @param int     $user_id
 * @param string  $domain
 * @param string  $path
 * @param int     $site_id
 * @param array() $meta
 */
function video_central_new_site($blog_id, $user_id, $domain, $path, $site_id, $meta)
{

    // Bail if plugin is not network activated
    if (!is_plugin_active_for_network(video_central()->basename)) {
        return;
    }

    // Switch to the new blog
    switch_to_blog($blog_id);

    // Do the Video_Central activation routine
    do_action('video_central_new_site', $blog_id, $user_id, $domain, $path, $site_id, $meta);

    // restore original blog
    restore_current_blog();
}

/** Sub-Actions ***************************************************************/

/**
 * Piggy back admin_init action.
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'video_central_admin_init'
 */
function video_central_admin_init()
{
    do_action('video_central_admin_init');
}

/**
 * Piggy back admin_menu action.
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'video_central_admin_menu'
 */
function video_central_admin_menu()
{
    do_action('video_central_admin_menu');
}

/**
 * Piggy back admin_head action.
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'video_central_admin_head'
 */
function video_central_admin_head()
{
    do_action('video_central_admin_head');
}

/**
 * Piggy back admin_notices action.
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'video_central_admin_notices'
 */
function video_central_admin_notices()
{
    do_action('video_central_admin_notices');
}

/**
 * Dedicated action to register admin styles.
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'video_central_admin_notices'
 */
function video_central_register_admin_style()
{
    do_action('video_central_register_admin_style');
}

/**
 * Dedicated action to register admin settings.
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'video_central_register_admin_settings'
 */
function video_central_register_admin_settings()
{
    do_action('video_central_register_admin_settings');
}
