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

/**
 * Retrieve a playlist's tracks.
 *
 * @since 1.0.0
 *
 * @param int|WP_Post $post    Playlist ID or post object.
 * @param string      $context Optional. Context to retrieve the tracks for. Defaults to display.
 * @return array
 */
function get_video_central_playlist_videos( $post = 0, $context = 'display' ) {
	$playlist = get_post( $post );
	$tracks = array_filter( (array) $playlist->tracks );

	// Add the audio file extension as a key pointing to the audio url.
	// Helpful for use with the jPlayer Playlist plugin.
	foreach ( $tracks as $key => $track ) {
		$parts = wp_parse_url( $track['audioUrl'] );
		if ( ! empty( $parts['path'] ) ) {
			$ext = pathinfo( $parts['path'], PATHINFO_EXTENSION );
			if ( ! empty( $ext ) ) {
				$tracks[ $key ][ $ext ] = $track['audioUrl'];
			}
		}
	}

	return apply_filters( 'video_central_playlist_videos', $tracks, $playlist, $context );
}

/**
 * Retrieve a default track.
 *
 * Useful for whitelisting allowed keys.
 *
 * @since 1.0.0
 *
 * @return array
 */
function get_video_central_default_track() {
	$args = array(
		'artist'     => '',
		'artworkId'  => '',
		'artworkUrl' => '',
		'videoId'    => '',
		'audioUrl'   => '',
		'length'     => '',
		'format'     => '',
		'order'      => '',
		'title'      => '',
	);

	return apply_filters( 'video_central_default_track_properties', $args );
}

/**
 * Sanitize a track based on the context.
 *
 * @since 1.0.0
 *
 * @param array  $track   Track data.
 * @param string $context Optional. Context to sanitize data for. Defaults to display.
 * @return array
 */
function sanitize_video_central_track( $track, $context = 'display' ) {
	if ( 'save' === $context ) {
		$valid_props = get_video_central_default_track();

		// Remove properties that aren't in the whitelist.
		$track = array_intersect_key( $track, $valid_props );

		// Sanitize valid properties.
		$track['artist']     = sanitize_text_field( $track['artist'] );
		$track['artworkId']  = absint( $track['artworkId'] );
		$track['artworkUrl'] = esc_url_raw( $track['artworkUrl'] );
		$track['videoId']    = absint( $track['videoId'] );
		$track['audioUrl']   = esc_url_raw( $track['audioUrl'] );
		$track['length']     = sanitize_text_field( $track['length'] );
		$track['format']     = sanitize_text_field( $track['format'] );
		$track['title']      = sanitize_text_field( $track['title'] );
		$track['order']      = absint( $track['order'] );
	}

	return apply_filters( 'video_central_sanitize_track', $track, $context );
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
	$playlist_id = get_video_central_playlist_player_id( $player_id );

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
 * Retrieve a list of players registered by the current them.
 *
 * Includes the player id, name and associated playlist if one has been saved.
 *
 * @since 1.1.0
 *
 * @return array
 */
function get_video_central_playlist_players() {
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
function get_video_central_playlist_player_id( $player_id ) {
	$players = get_theme_mod( 'video_central_playlist_players', array() );
	return isset( $players[ $player_id ] ) ? $players[ $player_id ] : 0;
}

/**
 * Retrieve playlist tracks for a registered player.
 *
 * @since 1.1.0
 *
 * @param string $player_id Player ID.
 * @param array  $args {
 *     An array of arguments. Optional.
 *
 *     @type string $context Context to retrieve the tracks for. Defaults to display.
 * }
 * @return array
 */
function get_video_central_playlist_player_videos( $player_id, $args = array() ) {
	$args = wp_parse_args( $args, array(
		'context' => 'display',
	) );

	$playlist_id = get_video_central_playlist_player_id( $player_id );
	return get_video_central_playlist_videos( $playlist_id, $args['context'] );
}
