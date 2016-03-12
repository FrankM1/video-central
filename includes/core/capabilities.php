<?php

/**
 * Video Central Capabilites.
 *
 * The functions in this file are used primarily as convenient wrappers for
 * capability output in user profiles. This includes mapping capabilities and
 * groups to human readable strings,
 */

/** Mapping *******************************************************************/

/**
 * Returns an array of capabilities based on the role that is being requested.
 *
 * @since 1.0.0
 *
 * @todo Map all of these and deprecate
 *
 * @param string $role Optional. Defaults to The role to load caps for
 *
 * @uses apply_filters() Allow return value to be filtered
 *
 * @return array Capabilities for $role
 */
function video_central_get_caps_for_role($role = '')
{

    // Which role are we looking for?
    switch ($role) {

        // Keymaster
        case video_central_get_keymaster_role() :
            $caps = array(

                // Keymasters only
                'keep_gate' => true,

                // Primary caps
                'spectate' => true,
                'participate' => true,
                'moderate' => true,
                'throttle' => true,
                'view_trash' => true,

                // Video caps
                'publish_videos' => true,
                'edit_videos' => true,
                'edit_others_videos' => true,
                'delete_videos' => true,
                'delete_others_videos' => true,
                'read_private_videos' => true,
                'read_hidden_videos' => true,

                // Video tag caps
                'manage_video_tags' => true,
                'edit_video_tags' => true,
                'delete_video_tags' => true,
                'assign_video_tags' => true,

                // Video tag caps
                'manage_video_categories' => true,
                'edit_video_categories' => true,
                'delete_video_categories' => true,
                'assign_video_categories' => true,
            );

            break;

        // Moderator
        case video_central_get_moderator_role() :
            $caps = array(

                // Primary caps
                'spectate' => true,
                'participate' => true,
                'moderate' => true,
                'throttle' => true,
                'view_trash' => true,

                // Video caps
                'publish_videos' => true,
                'edit_videos' => true,
                'read_private_videos' => true,
                'read_hidden_videos' => true,

                // Video caps
                'publish_videos' => true,
                'edit_videos' => true,
                'edit_others_videos' => true,
                'delete_videos' => true,
                'delete_others_videos' => true,
                'read_private_videos' => true,

                // Video tag caps
                'manage_video_tags' => true,
                'edit_video_tags' => true,
                'delete_video_tags' => true,
                'assign_video_tags' => true,

                // Video tag caps
                'manage_video_categories' => true,
                'edit_video_categories' => true,
                'delete_video_categories' => true,
                'assign_video_categories' => true,
            );

            break;

        // Spectators can only read
        case video_central_get_spectator_role()   :
            $caps = array(

                // Primary caps
                'spectate' => true,
            );

            break;

        // Explicitly blocked
        case video_central_get_blocked_role() :
            $caps = array(

                // Primary caps
                'spectate' => false,
                'participate' => false,
                'moderate' => false,
                'throttle' => false,
                'view_trash' => false,

                // Video caps
                'publish_videos' => false,
                'edit_videos' => false,
                'edit_others_videos' => false,
                'delete_videos' => false,
                'delete_others_videos' => false,
                'read_private_videos' => false,
                'read_hidden_videos' => false,

                // Video caps
                'publish_videos' => false,
                'edit_videos' => false,
                'edit_others_videos' => false,
                'delete_videos' => false,
                'delete_others_videos' => false,
                'read_private_videos' => false,

                // Comments caps
                'publish_replies' => false,
                'edit_replies' => false,
                'edit_others_replies' => false,
                'delete_replies' => false,
                'delete_others_replies' => false,
                'read_private_replies' => false,

                // Video tag caps
                'manage_video_tags' => false,
                'edit_video_tags' => false,
                'delete_video_tags' => false,
                'assign_video_tags' => false,

                // Video tag caps
                'manage_video_categories' => false,
                'edit_video_categories' => false,
                'delete_video_categories' => false,
                'assign_video_categories' => false,
            );

            break;

        // Participant/Default
        case video_central_get_participant_role() :
        default :
            $caps = array(

                // Primary caps
                'spectate' => true,
                'participate' => true,

                // Video caps
                'read_private_videos' => true,

                // Video caps
                'publish_videos' => true,
                'edit_videos' => true,

                // Video tag caps
                'assign_video_tags' => true,

                // Video category caps
                'assign_video_categories' => true,
            );

            break;
    }

    return apply_filters(__FUNCTION__, $caps, $role);
}

/**
 * Adds capabilities to WordPress user roles.
 *
 * @since 1.0.0
 */
function video_central_add_caps()
{

    // Loop through available roles and add caps
    foreach (video_central_get_wp_roles()->role_objects as $role) {
        foreach (video_central_get_caps_for_role($role->name) as $cap => $value) {
            $role->add_cap($cap, $value);
        }
    }

    do_action(__FUNCTION__);
}

/**
 * Removes capabilities from WordPress user roles.
 *
 * @since 1.0.0
 */
function video_central_remove_caps()
{

    // Loop through available roles and remove caps
    foreach (video_central_get_wp_roles()->role_objects as $role) {
        foreach (array_keys(video_central_get_caps_for_role($role->name)) as $cap) {
            $role->remove_cap($cap);
        }
    }

    do_action(__FUNCTION__);
}

/**
 * Get the $wp_roles global without needing to declare it everywhere.
 *
 * @since 1.0.0
 *
 * @global WP_Roles $wp_roles
 *
 * @return WP_Roles
 */
function video_central_get_wp_roles()
{
    global $wp_roles;

    // Load roles if not set
    if (!isset($wp_roles)) {
        $wp_roles = new WP_Roles();
    }

    return $wp_roles;
}

/**
 * Get the available roles minus Video Central's dynamic roles.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_wp_roles() To load and get the $wp_roles global
 *
 * @return array
 */
function video_central_get_blog_roles()
{

    // Get WordPress's roles (returns $wp_roles global)
    $wp_roles = video_central_get_wp_roles();

    // Apply the WordPress 'editable_roles' filter to let plugins ride along.
    //
    // We use this internally via video_central_filter_blog_editable_roles() to remove
    // any custom Video Central roles that are added to the global.
    $the_roles = isset($wp_roles->roles) ? $wp_roles->roles : false;
    $all_roles = apply_filters('editable_roles', $the_roles);

    return apply_filters(__FUNCTION__, $all_roles, $wp_roles);
}

/** Video Roles ***************************************************************/

/**
 * Add the Video Central roles to the $wp_roles global.
 *
 * We do this to avoid adding these values to the database.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_wp_roles() To load and get the $wp_roles global
 * @uses video_central_get_dynamic_roles() To get and add Video Central's roles to $wp_roles
 *
 * @return WP_Roles The main $wp_roles global
 */
function video_central_add_videos_roles()
{
    $wp_roles = video_central_get_wp_roles();

    foreach (video_central_get_dynamic_roles() as $role_id => $details) {
        $wp_roles->roles[$role_id] = $details;
        $wp_roles->role_objects[$role_id] = new WP_Role($role_id, $details['capabilities']);
        $wp_roles->role_names[$role_id] = $details['name'];
    }

    return $wp_roles;
}

/**
 * Helper function to add filter to option_wp_user_roles.
 *
 * @since 1.0.0
 * @see _video_central_reinit_dynamic_roles()
 *
 * @global WPDB $wpdb Used to get the database prefix
 */
function video_central_filter_user_roles_option()
{
    global $wpdb;

    $role_key = $wpdb->prefix.'user_roles';

    add_filter('option_'.$role_key, '_video_central_reinit_dynamic_roles');
}

/**
 * This is necessary because in a few places (noted below) WordPress initializes
 * a blog's roles directly from the database option. When this happens, the
 * $wp_roles global gets flushed, causing a user to magically lose any
 * dynamically assigned roles or capabilities when $current_user in refreshed.
 *
 * Because dynamic multiple roles is a new concept in WordPress, we work around
 * it here for now, knowing that improvements will come to WordPress core later.
 *
 * Also note that if using the $wp_user_roles global non-database approach,
 * Video Central does not have an intercept point to add its dynamic roles.
 *
 * @see switch_to_blog()
 * @see restore_current_blog()
 * @see WP_Roles::_init()
 * @since 1.0.0
 *
 * @internal Used by Video Central to reinitialize dynamic roles on blog switch
 *
 * @param array $roles
 *
 * @return array Combined array of database roles and dynamic Video Central roles
 */
function _video_central_reinit_dynamic_roles($roles = array())
{
    foreach (video_central_get_dynamic_roles() as $role_id => $details) {
        $roles[$role_id] = $details;
    }

    return $roles;
}

/**
 * Fetch a filtered list of video roles that the current user is
 * allowed to have.
 *
 * Simple function who's main purpose is to allow filtering of the
 * list of video roles so that plugins can remove inappropriate ones depending
 * on the situation or user making edits.
 *
 * Specifically because without filtering, anyone with the edit_users
 * capability can edit others to be administrators, even if they are
 * only editors or authors. This filter allows admins to delegate
 * user management.
 *
 * @since 1.0.0
 *
 * @return array
 */
function video_central_get_dynamic_roles()
{
    return (array) apply_filters(__FUNCTION__, array(

        // Keymaster
        video_central_get_keymaster_role() => array(
            'name' => __('Keymaster', 'video_central'),
            'capabilities' => video_central_get_caps_for_role(video_central_get_keymaster_role()),
        ),

        // Moderator
        video_central_get_moderator_role() => array(
            'name' => __('Moderator', 'video_central'),
            'capabilities' => video_central_get_caps_for_role(video_central_get_moderator_role()),
        ),

        // Participant
        video_central_get_participant_role() => array(
            'name' => __('Participant', 'video_central'),
            'capabilities' => video_central_get_caps_for_role(video_central_get_participant_role()),
        ),

        // Spectator
        video_central_get_spectator_role() => array(
            'name' => __('Spectator', 'video_central'),
            'capabilities' => video_central_get_caps_for_role(video_central_get_spectator_role()),
        ),

        // Blocked
        video_central_get_blocked_role() => array(
            'name' => __('Blocked', 'video_central'),
            'capabilities' => video_central_get_caps_for_role(video_central_get_blocked_role()),
        ),
    ));
}

/**
 * Gets a translated role name from a role ID.
 *
 * @since 1.0.0
 *
 * @param string $role_id
 *
 * @return string Translated role name
 */
function video_central_get_dynamic_role_name($role_id = '')
{
    $roles = video_central_get_dynamic_roles();
    $role = isset($roles[$role_id]) ? $roles[$role_id]['name'] : '';

    return apply_filters(__FUNCTION__, $role, $role_id, $roles);
}

/**
 * Removes the Video Central roles from the editable roles array.
 *
 * This used to use array_diff_assoc() but it randomly broke before 2.2 release.
 * Need to research what happened, and if there's a way to speed this up.
 *
 * @since 1.0.0
 *
 * @param array $all_roles All registered roles
 *
 * @return array
 */
function video_central_filter_blog_editable_roles($all_roles = array())
{

    // Loop through Video Central roles
    foreach (array_keys(video_central_get_dynamic_roles()) as $video_central_role) {

        // Loop through WordPress roles
        foreach (array_keys($all_roles) as $wp_role) {

            // If keys match, unset
            if ($wp_role === $video_central_role) {
                unset($all_roles[$wp_role]);
            }
        }
    }

    return $all_roles;
}

/**
 * The keymaster role for Video Central users.
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Allow override of hardcoded keymaster role
 *
 * @return string
 */
function video_central_get_keymaster_role()
{
    return apply_filters(__FUNCTION__, 'video_central_keymaster');
}

/**
 * The moderator role for Video Central users.
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Allow override of hardcoded moderator role
 *
 * @return string
 */
function video_central_get_moderator_role()
{
    return apply_filters(__FUNCTION__, 'video_central_moderator');
}

/**
 * The participant role for registered user that can participate in videos.
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Allow override of hardcoded participant role
 *
 * @return string
 */
function video_central_get_participant_role()
{
    return apply_filters(__FUNCTION__, 'video_central_participant');
}

/**
 * The spectator role is for registered users without any capabilities.
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Allow override of hardcoded spectator role
 *
 * @return string
 */
function video_central_get_spectator_role()
{
    return apply_filters(__FUNCTION__, 'video_central_spectator');
}

/**
 * The blocked role is for registered users that cannot spectate or participate.
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Allow override of hardcoded blocked role
 *
 * @return string
 */
function video_central_get_blocked_role()
{
    return apply_filters(__FUNCTION__, 'video_central_blocked');
}
