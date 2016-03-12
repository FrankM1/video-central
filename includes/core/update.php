<?php

/**
 * Video Central Updater.
 */

/**
 * If there is no raw DB version, this is the first installation.
 *
 * @since 1.0.0
 *
 * @uses get_option()
 * @uses video_central_get_db_version() To get Video Central's database version
 *
 * @return bool True if update, False if not
 */
function video_central_is_install()
{
    return !video_central_get_db_version_raw();
}

/**
 * Compare the Video Central version to the DB version to determine if updating.
 *
 * @since 1.0.0
 *
 * @uses get_option()
 * @uses video_central_get_db_version() To get Video Central's database version
 *
 * @return bool True if update, False if not
 */
function video_central_is_update()
{
    $raw = (int) video_central_get_db_version_raw();
    $cur = (int) video_central_get_db_version();
    $retval = (bool) ($raw < $cur);

    return $retval;
}

/**
 * Determine if Video Central is being activated.
 *
 * Note that this function currently is not used in Video Central core and is here
 * for third party plugins to use to check for Video Central activation.
 *
 * @since 1.0.0
 *
 * @return bool True if activating Video Central, false if not
 */
function video_central_is_activation($basename = '')
{
    global $pagenow;

    $video_central = video_central();
    $action = false;

    // Bail if not in admin/plugins
    if (!(is_admin() && ('plugins.php' === $pagenow))) {
        return false;
    }

    if (!empty($_REQUEST['action']) && ('-1' !== $_REQUEST['action'])) {
        $action = $_REQUEST['action'];
    } elseif (!empty($_REQUEST['action2']) && ('-1' !== $_REQUEST['action2'])) {
        $action = $_REQUEST['action2'];
    }

    // Bail if not activating
    if (empty($action) || !in_array($action, array('activate', 'activate-selected'))) {
        return false;
    }

    // The plugin(s) being activated
    if ($action === 'activate') {
        $plugins = isset($_GET['plugin']) ? array($_GET['plugin']) : array();
    } else {
        $plugins = isset($_POST['checked']) ? (array) $_POST['checked'] : array();
    }

    // Set basename if empty
    if (empty($basename) && !empty($video_central->basename)) {
        $basename = $video_central->basename;
    }

    // Bail if no basename
    if (empty($basename)) {
        return false;
    }

    // Is Video Central being activated?
    return in_array($basename, $plugins);
}

/**
 * Determine if Video Central is being deactivated.
 *
 * @since 1.0.0
 *
 * @return bool True if deactivating Video Central, false if not
 */
function video_central_is_deactivation($basename = '')
{
    global $pagenow;

    $video_central = video_central();
    $action = false;

    // Bail if not in admin/plugins
    if (!(is_admin() && ('plugins.php' === $pagenow))) {
        return false;
    }

    if (!empty($_REQUEST['action']) && ('-1' !== $_REQUEST['action'])) {
        $action = $_REQUEST['action'];
    } elseif (!empty($_REQUEST['action2']) && ('-1' !== $_REQUEST['action2'])) {
        $action = $_REQUEST['action2'];
    }

    // Bail if not deactivating
    if (empty($action) || !in_array($action, array('deactivate', 'deactivate-selected'))) {
        return false;
    }

    // The plugin(s) being deactivated
    if ($action === 'deactivate') {
        $plugins = isset($_GET['plugin']) ? array($_GET['plugin']) : array();
    } else {
        $plugins = isset($_POST['checked']) ? (array) $_POST['checked'] : array();
    }

    // Set basename if empty
    if (empty($basename) && !empty($video_central->basename)) {
        $basename = $video_central->basename;
    }

    // Bail if no basename
    if (empty($basename)) {
        return false;
    }

    // Is Video Central being deactivated?
    return in_array($basename, $plugins);
}

/**
 * Update the DB to the latest version.
 *
 * @since 1.0.0s
 *
 * @uses update_option()
 * @uses video_central_get_db_version() To get Video Central's database version
 */
function video_central_version_bump()
{
    update_option('_video_central_db_version', video_central_get_db_version());
}

/**
 * Setup the Video Central updater.
 *
 * @since 1.0.0
 *
 * @uses video_central_version_updater()
 * @uses video_central_version_bump()
 * @uses flush_rewrite_rules()
 */
function video_central_setup_updater()
{

    // Bail if no update needed
    if (!video_central_is_update()) {
        return;
    }

    // Call the automated updater
    video_central_version_updater();
}

/**
 * Video Central's version updater looks at what the current database version is, and
 * runs whatever other code is needed.
 *
 * This is most-often used when the data schema changes, but should also be used
 * to correct issues with Video Central meta-data silently on software update.
 *
 * @since 1.0.0
 */
function video_central_version_updater()
{

    // Get the raw database version
    $raw_db_version = (int) video_central_get_db_version_raw();

    // Bump the version
    video_central_version_bump();

    // Delete rewrite rules to force a flush
    video_central_delete_rewrite_rules();
}

/**
 * Redirect user to Video Central's What's New page on activation.
 *
 * @since 1.0.0
 *
 * @internal Used internally to redirect Video Central to the about page on activation
 *
 * @uses is_network_admin() To bail if being network activated
 * @uses set_transient() To drop the activation transient for 30 seconds
 *
 * @return If network admin or bulk activation
 */
function video_central_add_activation_redirect()
{

    // Bail if activating from network, or bulk
    if (is_network_admin() || isset($_GET['activate-multi'])) {
        return;
    }

    // Add the transient to redirect
    set_transient('_video_central_activation_redirect', true, 30);
}
