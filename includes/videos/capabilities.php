<?php

/**
 * Radiumm Video Capabilites.
 *
 * Used to map video capabilities to WordPress's existing capabilities.
 */

/**
 * Return video capabilities.
 *
 * @since Radium Videos 1.0.0
 *
 * @uses apply_filters() Calls 'video_central_get_video_caps' with the capabilities
 *
 * @return array Video capabilities
 */
function video_central_get_video_caps()
{
    return apply_filters(__FUNCTION__, array(
        'edit_posts' => 'edit_videos',
        'edit_others_posts' => 'edit_others_videos',
        'publish_posts' => 'publish_videos',
        'read_private_posts' => 'read_private_videos',
        'read_hidden_posts' => 'read_hidden_videos',
        'delete_posts' => 'delete_videos',
        'delete_others_posts' => 'delete_others_videos',
    ));
}

/**
 * Return video tag capabilities.
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'video_central_get_video_tag_caps' with the capabilities
 *
 * @return array video tag capabilities
 */
function video_central_get_video_tag_caps()
{
    return apply_filters('video_central_get_video_tag_caps', array(
        'manage_terms' => 'manage_video_tags',
        'edit_terms' => 'edit_video_tags',
        'delete_terms' => 'delete_video_tags',
        'assign_terms' => 'assign_video_tags',
    ));
}

/**
 * Return video category capabilities.
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'video_central_get_video_category_caps' with the capabilities
 *
 * @return array video tag capabilities
 */
function video_central_get_video_category_caps()
{
    return apply_filters(__FUNCTION__, array(
        'manage_terms' => 'manage_video_categories',
        'edit_terms' => 'edit_video_categories',
        'delete_terms' => 'delete_video_categories',
        'assign_terms' => 'assign_video_categories',
    ));
}

/**
 * Maps Video capabilities.
 *
 * @since 1.0.0
 *
 * @param array  $caps    Capabilities for meta capability
 * @param string $cap     Capability name
 * @param int    $user_id User id
 * @param mixed  $args    Arguments
 *
 * @uses get_post() To get the post
 * @uses get_post_type_object() To get the post type object
 * @uses apply_filters() Filter capability map results
 *
 * @return array Actual capabilities for meta capability
 */
function video_central_map_video_meta_caps($caps = array(), $cap = '', $user_id = 0, $args = array())
{

    // What capability is being checked?
    switch ($cap) {

        /* Reading ***********************************************************/

        case 'read_private_videos' :
        case 'read_hidden_videos'  :

            // Moderators can always read private/hidden videos
            if (user_can($user_id, 'moderate')) {
                $caps = array('moderate');
            }

            break;

        case 'read_video' :

            // User cannot spectate
            if (!user_can($user_id, 'spectate')) {
                $caps = array('do_not_allow');

            // Do some post ID based logic
            } else {

                // Get the post
                $_post = get_post($args[0]);
                if (!empty($_post)) {

                    // Get caps for post type object
                    $post_type = get_post_type_object($_post->post_type);

                    // Post is public
                    if (video_central_get_public_status_id() === $_post->post_status) {
                        $caps = array('spectate');

                    // User is author so allow read
                    } elseif ((int) $user_id === (int) $_post->post_author) {
                        $caps = array('spectate');

                    // Unknown so map to private posts
                    } else {
                        $caps = array($post_type->cap->read_private_posts);
                    }
                }
            }

            break;

        /* Publishing ********************************************************/

        case 'publish_videos'  :

            // Moderators can always edit
            if (user_can($user_id, 'moderate')) {
                $caps = array('moderate');
            }

            break;

        /* Editing ***********************************************************/

        // Used primarily in wp-admin
        case 'edit_videos'         :
        case 'edit_others_videos'  :

            // Moderators can always edit
            if (user_can($user_id, 'keep_gate')) {
                $caps = array('keep_gate');

            // Otherwise, block
            } else {
                $caps = array('do_not_allow');
            }

            break;

        // Used everywhere
        case 'edit_video' :

            // Get the post
            $_post = get_post($args[0]);
            if (!empty($_post)) {

                // Get caps for post type object
                $post_type = get_post_type_object($_post->post_type);
                $caps = array();

                // Add 'do_not_allow' cap if user is spam or deleted
                if (video_central_is_user_inactive($user_id)) {
                    $caps[] = 'do_not_allow';

                // User is author so allow edit if not in admin
                } elseif (!is_admin() && ((int) $user_id === (int) $_post->post_author)) {
                    $caps[] = $post_type->cap->edit_posts;

                // Unknown, so map to edit_others_posts
                } else {
                    $caps[] = $post_type->cap->edit_others_posts;
                }
            }

            break;

        /* Deleting **********************************************************/

        // Allow video authors to delete videos (for BuddyPress groups, etc)
        case 'delete_video' :

            // Get the post
            $_post = get_post($args[0]);
            if (!empty($_post)) {

                // Get caps for post type object
                $post_type = get_post_type_object($_post->post_type);
                $caps = array();

                // Add 'do_not_allow' cap if user is spam or deleted
                if (video_central_is_user_inactive($user_id)) {
                    $caps[] = 'do_not_allow';

                // User is author so allow to delete
                } elseif ((int) $user_id === (int) $_post->post_author) {
                    $caps[] = $post_type->cap->delete_posts;

                // Unknown so map to delete_others_posts
                } else {
                    $caps[] = $post_type->cap->delete_others_posts;
                }
            }

            break;

        /* Admin *************************************************************/

        case 'video_central_admin' :
            $caps = array('keep_gate');
            break;
    }

    return apply_filters(__FUNCTION__, $caps, $cap, $user_id, $args);
}
