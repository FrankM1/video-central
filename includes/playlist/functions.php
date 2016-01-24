<?php

/**
 * Playlist Central Playlist Functions.
 */

/** Post Type *****************************************************************/

/**
 * Output the unique id of the custom post type for playlists.
 *
 * @since 1.2.0
 *
 * @uses video_central_get_playlist_post_type() To get the playlist post type
 */
function video_central_playlist_post_type()
{
    echo video_central_get_playlist_post_type();
}
    /**
     * Return the unique id of the custom post type for playlists.
     *
     * @since 1.2.0
     *
     * @uses apply_filters() Calls 'video_central_get_playlist_post_type' with the playlist
     *                        post type id
     *
     * @return string The unique playlist post type id
     */
    function video_central_get_playlist_post_type()
    {
        return video_central()->playlist_post_type;
    }

/**
 * Return array of labels used by the playlist post type.
 *
 * @since 1.2.0
 *
 * @return array
 */
function video_central_get_playlist_post_type_labels()
{
    return apply_filters(__FUNCTION__, array(
        'name' => __('Playlists',                   'video_central'),
        'menu_name' => __('Playlists',                   'video_central'),
        'singular_name' => __('Playlist',                    'video_central'),
        'all_items' => __('All Playlists',               'video_central'),
        'add_new' => __('New Playlist',                'video_central'),
        'add_new_item' => __('Create New Playlist',         'video_central'),
        'edit' => __('Edit',                        'video_central'),
        'edit_item' => __('Edit Playlist',               'video_central'),
        'new_item' => __('New Playlist',                'video_central'),
        'view' => __('View Playlist',               'video_central'),
        'view_item' => __('View Playlist',               'video_central'),
        'search_items' => __('Search Playlists',            'video_central'),
        'not_found' => __('No playlists found',          'video_central'),
        'not_found_in_trash' => __('No playlists found in Trash', 'video_central'),
        'parent_item_colon' => __('Parent Playlist:',            'video_central'),
    ));
}

/** Rewrite *********************************************************************/

/**
 * Return array of playlist post type rewrite settings.
 *
 * @since 1.2.0
 *
 * @return array
 */
function video_central_get_playlist_post_type_rewrite()
{
    return apply_filters(__FUNCTION__, array(
        'slug' => video_central_get_playlist_slug(),
        'with_front' => false,
    ));
}

/**
 * Return array of features the playlist post type supports.
 *
 * @since 1.2.0
 *
 * @return array
 */
function video_central_get_playlist_post_type_supports()
{
    return apply_filters(__FUNCTION__, array(
        'title',
        'revisions',
    ));
}
