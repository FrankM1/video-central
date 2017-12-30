<?php

/**
 * Playlist Central Playlist Functions.
 */


/** Settings ******************************************************************/

/**
 * Return the videos per page setting.
 *
 * @since 1.0.0
 *
 * @param int $default Default playlists per page (15)
 *
 * @uses get_option() To get the setting
 * @uses apply_filters() To allow the return value to be manipulated
 *
 * @return int
 */
function video_central_get_playlists_per_page($default = 16)
{

    // Get database option and cast as integer
    $retval = get_option('_video_central_playlists_per_page', $default);

    // If return val is empty, set it to default
    if (empty($retval)) {
        $retval = $default;
    }

    // Filter and return
    return (int) apply_filters( __FUNCTION__, $retval, $default );
}

/**
 * Return the playlists per RSS page setting.
 *
 * @since 1.0.0
 *
 * @param int $default Default playlists per page (25)
 *
 * @uses get_option() To get the setting
 * @uses apply_filters() To allow the return value to be manipulated
 *
 * @return int
 */
function video_central_get_playlists_per_rss_page($default = 35)
{

    // Get database option and cast as integer
    $retval = get_option('_video_central_playlists_per_rss_page', $default);

    // If return val is empty, set it to default
    if (empty($retval)) {
        $retval = $default;
    }

    // Filter and return
    return (int) apply_filters(__FUNCTION__, $retval, $default);
}

/** Post Type *****************************************************************/

/**
 * Output the unique id of the custom post type for playlists.
 *
 * @since 1.2.0
 *
 * @uses video_central_get_playlists_post_type() To get the playlist post type
 */
function video_central_playlist_post_type()
{
    echo video_central_get_playlists_post_type();
}
    /**
     * Return the unique id of the custom post type for playlists.
     *
     * @since 1.2.0
     *
     * @uses apply_filters() Calls 'video_central_get_playlists_post_type' with the playlist
     *                        post type id
     *
     * @return string The unique playlist post type id
     */
    function video_central_get_playlists_post_type()
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

/** Data Functions *********************************************************************/

/**
 * Retrieve a playlist's videos.
 *
 * @since 1.0.0
 *
 * @param int|WP_Post $post    Playlist ID or post object.
 * @param string      $context Optional. Context to retrieve the videos for. Defaults to display.
 * @return array
 */
function video_central_get_playlist_videos( $post = 0, $context = 'display' ) {
	$playlist = get_post( $post );
	$videos = array_filter( (array) $playlist->videos );
	return apply_filters( 'video_central_playlist_videos', $videos, $playlist, $context );
}

/**
 * Retrieve a default video.
 *
 * Useful for whitelisting allowed keys.
 *
 * @since 1.0.0
 *
 * @return array
 */
function get_video_central_default_video() {
	$args = array(
		'artist'     => '',
		'artworkId'  => '',
		'artworkUrl' => '',
		'videoId'    => '',
		'length'     => '',
		'order'      => '',
		'title'      => '',
	);

	return apply_filters( 'video_central_default_video_properties', $args );
}

/**
 * Sanitize a video based on the context.
 *
 * @since 1.0.0
 *
 * @param array  $video   Track data.
 * @param string $context Optional. Context to sanitize data for. Defaults to display.
 * @return array
 */
function sanitize_video_central_video( $video, $context = 'display' ) {
	if ( 'save' === $context ) {
		$valid_props = get_video_central_default_video();

		// Remove properties that aren't in the whitelist.
		$video = array_intersect_key( $video, $valid_props );

		// Sanitize valid properties.
		$video['artist']     = sanitize_text_field( $video['artist'] );
		$video['artworkId']  = absint( $video['artworkId'] );
		$video['artworkUrl'] = esc_url_raw( $video['artworkUrl'] );
		$video['videoId']    = absint( $video['videoId'] );
		$video['length']     = sanitize_text_field( $video['length'] );
		$video['title']      = sanitize_text_field( $video['title'] );
		$video['order']      = absint( $video['order'] );
	}

	return apply_filters( 'video_central_sanitize_video', $video, $context );
}

/**
 * Display a theme-registered player.
 *
 * @since 1.1.0
 *
 * @param string $player_id Player ID.
 * @param array  $args      Playlist arguments.
 */
function video_central_playlist_player( $player_id, $args = array() ) {
	$playlist_id = video_central_get_playlist_player_id( $player_id );

	$args = array(
		'enqueue'  => false,
		'player'   => $player_id,
		'template' => array(
			"player-{$player_id}.php",
			'player.php',
		),
	);

	video_central_playlist( $playlist_id, $args );
}

/**
 * Retrieve a list of players registered by the current theme.
 *
 * Includes the player id, name and associated playlist if one has been saved.
 *
 * @since 1.1.0
 *
 * @return array
 */
function video_central_get_playlist_players() {
	$players = array();
	$assigned = get_theme_mod( 'video_central_playlist_players', array() );

	/**
	 * List of registered players.
	 *
	 * Format: array( 'player_id' => 'Player Name' )
	 *
	 * @since 1.1.0
	 *
	 * @param array $players List of players.
	 */
	$registered = apply_filters( 'video_central_playlist_players', array() );

	if ( ! empty( $registered ) ) {
		asort( $registered );
		foreach ( $registered as $id => $name ) {
			$playlist_id = isset( $assigned[ $id ] ) ? $assigned[ $id ] : 0;

			$players[ $id ] = array(
				'id'          => $id,
				'name'        => $name,
				'playlist_id' => $playlist_id,
			);
		}
	}

	return $players;
}

/**
 * Retreive the ID of a playlist connected to a player.
 *
 * @since 1.1.0
 *
 * @param string $player_id Player ID.
 * @return int
 */
function video_central_get_playlist_player_id( $player_id ) {
	$players = get_theme_mod( 'video_central_playlist_players', array() );
	return isset( $players[ $player_id ] ) ? $players[ $player_id ] : 0;
}

/**
 * Retrieve playlist videos for a registered player.
 *
 * @since 1.1.0
 *
 * @param string $player_id Player ID.
 * @param array  $args {
 *     An array of arguments. Optional.
 *
 *     @type string $context Context to retrieve the videos for. Defaults to display.
 * }
 * @return array
 */
function video_central_get_playlist_player_videos( $player_id, $args = array() ) {
	$args = wp_parse_args( $args, array(
		'context' => 'display',
	) );

	$playlist_id = video_central_get_playlist_player_id( $player_id );
	return video_central_get_playlist_videos( $playlist_id, $args['context'] );
}


/**
 * Prepare an audio attachment for JavaScript.
 *
 * Filters the core method and inserts data using 'video_central' as the top level key.
 *
 * @since 1.0.0
 *
 * @param WP_Post $attachment Attachment object.
 * @return array
 */
function prepare_video_central_playlist_video_for_js( $attachment ) {
    if ( ! $attachment = get_post( $attachment ) )
		return;

	if ( video_central_get_video_post_type() !== $attachment->post_type )
		return;

    $data = array();

    // Fall back to the attachment title if the audio meta doesn't have one.
    $data['title']    = video_central_get_video_title( $attachment->ID );
    $data['videoId']  = $attachment->ID;

    if ( has_post_thumbnail( $attachment->ID ) ) {
        $thumbnail_id = get_post_thumbnail_id( $attachment->ID );
        $size         = apply_filters( 'video_central_artwork_size', array( 300, 300 ) );
        $image        = image_downsize( $thumbnail_id, $size );

        $data['artworkId']  = $thumbnail_id;
        $data['artworkUrl'] = $image[0];
    }

    $data['id'] = $attachment->ID;

    return apply_filters( 'prepare_video_central_playlist_video_for_js', $data, $attachment );
}