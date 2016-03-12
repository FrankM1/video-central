<?php

/**
 * Plugin Dependency.
 *
 * The purpose of the following hooks is to mimic the behavior of something
 * called 'plugin dependency' which enables a plugin to have plugins of their
 * own in a safe and reliable way.
 *
 * We do this in Video Central by mirroring existing WordPress hooks in many places
 * allowing dependent plugins to hook into the Video Central specific ones, thus
 * guaranteeing proper code execution only when Video Central is active.
 *
 * The following functions are wrappers for hooks, allowing them to be
 * manually called and/or piggy-backed on top of other hooks if needed.
 *
 * @todo use anonymous functions when PHP minimum requirement allows (5.3)
 */

/** Activation Actions ********************************************************/

/**
 * Runs on Video Central activation.
 *
 * @since 1.0.0
 *
 * @uses register_uninstall_hook() To register our own uninstall hook
 * @uses do_action() Calls 'video_central_activation' hook
 */
function video_central_activation()
{
    do_action(__FUNCTION__);
}

/**
 * Runs on Video Central deactivation.
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'video_central_deactivation' hook
 */
function video_central_deactivation()
{
    do_action(__FUNCTION__);
}

/**
 * Runs when uninstalling Video Central.
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'video_central_uninstall' hook
 */
function video_central_uninstall()
{
    do_action(__FUNCTION__);
}

/** Main Actions **************************************************************/

/**
 * Main action responsible for constants, globals, and includes.
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'video_central_loaded'
 */
function video_central_loaded()
{
    do_action(__FUNCTION__);
}

/**
 * Register any objects before anything is initialized.
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'video_central_register'
 */
function video_central_register()
{
    do_action(__FUNCTION__);
}

/**
 * Initialize any code after everything has been loaded.
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'video_central_init'
 */
function video_central_init()
{
    do_action(__FUNCTION__);
}

/**
 * Initialize any code after everything has been loaded.
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'video_central_init'
 */
function video_central_init_classes()
{
    do_action(__FUNCTION__);
}

/**
 * Initialize widgets.
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'video_central_widgets_init'
 */
function video_central_widgets_init()
{
    do_action(__FUNCTION__);
}

/** Supplemental Actions ******************************************************/

/**
 * Load translations for current language.
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'video_central_load_textdomain'
 */
function video_central_load_textdomain()
{
    do_action(__FUNCTION__);
}

/**
 * Setup the post types.
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'video_central_register_post_type'
 */
function video_central_register_post_types()
{
    do_action(__FUNCTION__);
}

/**
 * Register the built in Video Central taxonomies.
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'video_central_register_taxonomies'
 */
function video_central_register_taxonomies()
{
    do_action(__FUNCTION__);
}

/**
 * Register the default Video Central views.
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'video_central_register_views'
 */
function video_central_register_views()
{
    do_action(__FUNCTION__);
}

/**
 * Register the default Video Central shortcodes.
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'video_central_register_shortcodes'
 */
function video_central_register_shortcodes()
{
    do_action(__FUNCTION__);
}

/**
 * Enqueue Video Central specific CSS and JS.
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'video_central_enqueue_scripts'
 */
function video_central_enqueue_scripts()
{
    do_action(__FUNCTION__);
}

/**
 * Add the Video Central-specific rewrite tags.
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'video_central_add_rewrite_tags'
 */
function video_central_add_rewrite_tags()
{
    do_action(__FUNCTION__);
}

/**
 * Add the Video Central-specific rewrite tags.
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'video_central_add_rewrite_categories'
 */
function video_central_add_rewrite_categories()
{
    do_action(__FUNCTION__);
}

/**
 * Add the Video Central-specific rewrite rules.
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'video_central_add_rewrite_rules'
 */
function video_central_add_rewrite_rules()
{
    do_action(__FUNCTION__);
}

/**
 * Add the Video Central specific permalink structures.
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'video_central_add_permastructs'
 */
function video_central_add_permastructs()
{
    do_action(__FUNCTION__);
}

/** Theme Helpers *************************************************************/

/**
 * The main action used for executing code before the theme has been setup.
 *
 * @since 1.0.0
 *
 * @uses do_action()
 */
function video_central_register_theme_packages()
{
    do_action(__FUNCTION__);
}

/**
 * The main action used for executing code before the theme has been setup.
 *
 * @since 1.0.0
 *
 * @uses do_action()
 */
function video_central_setup_theme()
{
    do_action(__FUNCTION__);
}

/**
 * The main action used for executing code after the theme has been setup.
 *
 * @since 1.0.0
 *
 * @uses do_action()
 */
function video_central_after_setup_theme()
{
    do_action(__FUNCTION__);
}

/**
 * The main action used for handling theme-side GET requests.
 *
 * @since 1.0.0
 *
 * @uses do_action()
 */
function video_central_get_request()
{

    // Bail if not a GET action
    if (!video_central_is_get_request()) {
        return;
    }

    // Bail if no action
    if (empty($_GET['action'])) {
        return;
    }

    // This dynamic action is probably the one you want to use. It narrows down
    // the scope of the 'action' without needing to check it in your function.
    do_action('video_central_get_request_'.$_GET['action']);

    // Use this static action if you don't mind checking the 'action' yourself.
    do_action(__FUNCTION__,   $_GET['action']);
}

/** Filters *******************************************************************/

/**
 * Filter the plugin locale and domain.
 *
 * @since 1.0.0
 *
 * @param string $locale
 * @param string $domain
 */
function video_central_plugin_locale($locale = '', $domain = '')
{
    return apply_filters(__FUNCTION__, $locale, $domain);
}

/**
 * The main filter used for theme compatibility and displaying custom Video Central
 * theme files.
 *
 * @since 1.0.0
 *
 * @uses apply_filters()
 *
 * @param string $template
 *
 * @return string Template file to use
 */
function video_central_template_include($template = '')
{
    return apply_filters(__FUNCTION__, $template);
}

/**
 * Filter the allowed themes list for Video Central specific themes.
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'video_central_allowed_themes' with the allowed themes list
 */
function video_central_allowed_themes($themes)
{
    return apply_filters(__FUNCTION__, $themes);
}

/**
 * Maps video caps to built in WordPress caps.
 *
 * @since 1.0.0
 *
 * @param array  $caps    Capabilities for meta capability
 * @param string $cap     Capability name
 * @param int    $user_id User id
 * @param mixed  $args    Arguments
 */
function video_central_map_meta_caps($caps = array(), $cap = '', $user_id = 0, $args = array())
{
    return apply_filters(__FUNCTION__, $caps, $cap, $user_id, $args);
}
