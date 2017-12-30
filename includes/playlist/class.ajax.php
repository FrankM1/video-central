<?php

/**
 * AJAX playlist class.
 *
 * @package Video Central
 * @since   2.0.0
 */
class Video_Central_Playlist_Ajax {

	/**
	 * Register hooks.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
        add_action( 'wp_ajax_video_central_get_playlists',        array( $this, 'get_playlists' ) );
        add_action( 'wp_ajax_video_central_get_videos_for_frame', array( $this, 'get_videos_for_frame' ) );
		add_action( 'wp_ajax_video_central_get_playlist_videos',  array( $this, 'get_playlist_videos' ) );
		add_action( 'wp_ajax_video_central_save_playlist_videos', array( $this, 'save_playlist_videos' ) );
        add_action( 'wp_ajax_video_central_parse_shortcode',      array( $this, 'parse_shortcode' ) );
    }

    /**
	 * AJAX callback to retrieve videos.
	 *
	 * @since 1.3.0
	 */
	public function get_videos_for_frame() {
		$data = array();
		$page = isset( $_POST['paged'] ) ? absint( $_POST['paged'] ) : 1;
		$posts_per_page = isset( $_POST['posts_per_page'] ) ? absint( $_POST['posts_per_page'] ) : 40;

		$videos = new WP_Query( array(
			'post_type'      => video_central_get_video_post_type(),
			'post_status'    => 'publish',
			'posts_per_page' => $posts_per_page,
			'paged'          => $page,
		) );

		if ( $videos->have_posts() ) {
			foreach ( $videos->posts as $video ) {
				$image = video_central_get_featured_image_url( $video->ID, array( 'width' => 120, 'height' => 120 ) );

				$data[ $video->ID ] = array(
					'id'        => $video->ID,
					'title'     => $video->post_title,
					'thumbnail' => $image,
				);
			}
		}

		$send['maxNumPages'] = $videos->max_num_pages;
		$send['videos'] = array_values( $data );

		wp_send_json_success( $send );
	}

	/**
	 * AJAX callback to retrieve playlists.
	 *
	 * @since 2.2.0
	 */
	public function get_playlists() {
		$data = array();
		$page = isset( $_POST['paged'] ) ? absint( $_POST['paged'] ) : 1;
		$posts_per_page = isset( $_POST['posts_per_page'] ) ? absint( $_POST['posts_per_page'] ) : 40;

		$playlists = new WP_Query( array(
			'post_type'      => 'video_central_playlist',
			'post_status'    => 'publish',
			'posts_per_page' => $posts_per_page,
			'paged'          => $page,
		) );

		if ( $playlists->have_posts() ) {
			foreach ( $playlists->posts as $playlist ) {
				$image = wp_get_attachment_image_src( get_post_thumbnail_id( $playlist->ID ), array( 120, 120 ) );

				$data[ $playlist->ID ] = array(
					'id'        => $playlist->ID,
					'title'     => $playlist->post_title,
					'thumbnail' => $image[0],
				);
			}
		}

		$send['maxNumPages'] = $playlists->max_num_pages;
		$send['playlists'] = array_values( $data );

		wp_send_json_success( $send );
	}

	/**
	 * AJAX callback to retrieve a playlist's videos.
	 *
	 * @since 2.0.0
	 */
	public function get_playlist_videos() {
		$post_id = absint( $_POST['post_id'] );
		wp_send_json_success( video_central_get_playlist_videos( $post_id, 'edit' ) );
	}

	/**
	 * AJAX callback to save a playlist's videos.
	 *
	 * Videos are currently saved to post meta.
	 *
	 * @since 2.0.0
	 */
	public function save_playlist_videos() {
		$post_id = absint( $_POST['post_id'] );

		check_ajax_referer( 'save-videos_' . $post_id, 'nonce' );

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			wp_send_json_error();
		}

		// Sanitize the list of videos.
		$videos = empty( $_POST['videos'] ) ? array() : stripslashes_deep( $_POST['videos'] );
		foreach ( (array) $videos as $key => $video ) {
			if ( empty( $video ) ) {
				unset( $videos[ $key ] );
				continue;
			}

			$videos[ $key ] = sanitize_video_central_video( $video, 'save' );
		}

		// Save the list of videos to post meta.
		update_post_meta( $post_id, 'videos', $videos );

		// Response data.
		$data = array(
			'nonce' => wp_create_nonce( 'save-videos_' . $post_id ),
		);

		// Send the response.
		wp_send_json_success( $data );
	}

	/**
	 * Parse the Playlist shortcode for display within a TinyMCE view.
	 *
	 * @since 1.3.0
	 */
	public function parse_shortcode() {
		global $wp_scripts;

		check_ajax_referer( 'video_central_parse_shortcode' );

		if ( empty( $_POST['shortcode'] ) ) {
			wp_send_json_error();
		}

		$shortcode = wp_unslash( $_POST['shortcode'] );

		if ( 0 !== strpos( $shortcode, '[video_central_playlist ' ) ) {
			wp_send_json_error();
		}

		$shortcode = do_shortcode( $shortcode );

		if ( empty( $shortcode ) ) {
			wp_send_json_error( array(
				'type'    => 'no-items',
				'message' => esc_html__( 'No items found.', 'video-central' ),
			) );
		}

		$head  = '';
		$styles = wpview_media_sandbox_styles();

		// @codingStandardsIgnoreStart
		foreach ( $styles as $style ) {
			$head .= '<link type="text/css" rel="stylesheet" href="' . $style . '">'; // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet

		}

		$head .= '<link rel="stylesheet" href="' . $this->plugin->get_url( 'assets/css/video-central-playlist.min.css' ) . '">'; // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet
		$head .= '<style type="text/css">.video-central-playlist-videos { max-height: none;}</style>';
		// @codingStandardsIgnoreEnd

		if ( ! empty( $wp_scripts ) ) {
			$wp_scripts->done = array();
		}

		ob_start();
		echo $shortcode; // WPCS: XSS ok.
		wp_print_scripts( 'video-central-playlist' );

		wp_send_json_success( array(
			'head' => apply_filters( 'video_central_parse_shortcode_head', $head ),
			'body' => ob_get_clean(),
		) );
	}
}
