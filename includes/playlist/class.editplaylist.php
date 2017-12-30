<?php
/**
 * Edit playlist screen.
 *
 * @package Video Central
 */

/**
 * Edit playlist screen class.
 */
class Video_Central_Playlist_EditPlaylist {
	/**
	 * Register hooks.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'load-post.php',               array( $this, 'load_screen' ) );
		add_action( 'load-post-new.php',           array( $this, 'load_screen' ) );
		add_action( 'add_meta_boxes_' . video_central_get_playlists_post_type(), array( $this, 'register_meta_boxes' ) );
		add_action( 'save_post_' . video_central_get_playlists_post_type(),      array( $this, 'on_playlist_save' ) );
	}

	/**
	 * Set up the screen.
	 *
	 * @since 2.0.0
	 */
	public function load_screen() {
		if ( video_central_get_playlists_post_type() !== get_current_screen()->id ) {
			return;
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'admin_notices',         array( $this, 'print_javascript_required_notice' ) );
		add_action( 'edit_form_after_title', array( $this, 'display_edit_view' ) );
		add_action( 'admin_footer',          array( $this, 'print_templates' ) );
	}

	/**
	 * Register record meta boxes.
	 *
	 * @since 2.0.0
	 *
	 * @param WP_Post $post The record post object being edited.
	 */
	public function register_meta_boxes( $post ) {
		$players = video_central_get_playlist_players();

		if ( ! empty( $players ) ) {
			add_meta_box(
				'video-central-playlist-players',
				esc_html__( 'Players', 'video-central' ),
				array( $this, 'display_players_meta_box' ),
				video_central_get_playlists_post_type(),
				'side',
				'default'
			);
		}

		add_meta_box(
			'video-central-playlist-playlist-shortcode',
			esc_html__( 'Shortcode', 'video-central' ),
			array( $this, 'display_shortcode_meta_box' ),
			video_central_get_playlists_post_type(),
			'side',
			'default'
		);
	}

	/**
	 * Enqueue assets for the Edit Record screen.
	 *
	 * @since 2.0.0
	 */
	public function enqueue_assets() {
		$post   = get_post();
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
        $base = Video_Central::get_url();

        wp_enqueue_media();

        wp_enqueue_style(
			'video-central-playlist-admin',
			$base . '/assets/admin/css/playlist.css',
			array( 'dashicons' )
		);

        wp_register_script( 'video-central-playlist', $base . '/assets/admin/js/video-central.js',
            array(
                'backbone',
				'jquery-ui-sortable',
				'media-upload',
				'media-views',
				'wp-util',
            ),
            '1.2.2',
            true
        );

		wp_enqueue_script(
			'video-central-playlist-playlist-edit',
			$base . '/assets/admin/js/playlist-edit.js',
			array( 'video-central-playlist' ),
			'1.0.0',
			true
		);

		wp_localize_script( 'video-central-playlist-playlist-edit', 'videoCentralPlaylistConfig', array(
			'postId'     => $post->ID,
			'saveNonce'  => wp_create_nonce( 'save-videos_' . $post->ID ),
			'videos'     => video_central_get_playlist_videos( $post->ID, 'edit' ),
			'l10n'       => array(
				'addVideos'  => esc_html__( 'Add Videos', 'video-central' ),
				'addFromUrl' => esc_html__( 'Add from URL', 'video-central' ),
				'workflows'  => array(
					'selectArtwork' => array(
						'fileTypes'       => esc_html__( 'Image Files', 'video-central' ),
						'frameTitle'      => esc_html__( 'Choose an Image', 'video-central' ),
						'frameButtonText' => esc_html__( 'Update Image', 'video-central' ),
					),
					'selectAudio'   => array(
						'fileTypes'       => esc_html__( 'Video Files', 'video-central' ),
						'frameTitle'      => esc_html__( 'Choose an Video File', 'video-central' ),
						'frameButtonText' => esc_html__( 'Update Video', 'video-central' ),
					),
					'addVideos'     => array(
						'fileTypes'       => esc_html__( 'Video Files', 'video-central' ),
						'frameTitle'      => esc_html__( 'Choose Videos', 'video-central' ),
						'frameButtonText' => esc_html__( 'Add Videos', 'video-central' ),
					),
				),
			),
		) );
	}

	/**
	 * Print a notice about JavaScript being required.
	 *
	 * @since 2.0.0
	 */
	public function print_javascript_required_notice() {
		?>
		<noscript>
			<div class="notice notice-error">
				<h2 class="notice-title"><?php esc_html_e( 'JavaScript Disabled', 'video-central' ); ?></h2>
				<p>
					<?php
					$notice = sprintf(
						__( 'Video Central requires JavaScript in order to function correctly. Please <a href="%s">enable it in your browser</a> to continue.', 'video-central' ),
						'http://enable-javascript.com/'
					);

					echo wp_kses( $notice, array( 'a' => array( 'href' => array() ) ) );
					?>
				</p>
			</div>
		</noscript>
		<?php
	}

	/**
	 * Display the basic starting view.
	 *
	 * @since 2.0.0
	 *
	 * @param WP_Post $post Playlist post object.
	 */
	public function display_edit_view( $post ) {
		?>
		<div id="video-central-playlist-playlist-editor" class="video-central-playlist-panel hide-if-no-js">
			<div class="video-central-playlist-panel-header">
				<h2 class="video-central-playlist-panel-title"><?php esc_html_e( 'Videos', 'video-central' ); ?></h2>
			</div>
			<div class="video-central-playlist-panel-body">
				<p>
					<?php esc_html_e( 'Add videos to the playlist, then drag and drop to reorder them. Click the arrow on the right of each item to reveal more configuration options.', 'video-central' ); ?>
				</p>
			</div>
		</div>
		<?php
	}

	/**
	 * Display a meta box to choose which theme-registered players to connect a
	 * playlist with.
	 *
	 * @since 2.0.0
	 *
	 * @param WP_Post $post Post object.
	 */
	public function display_players_meta_box( $post ) {
		wp_nonce_field( 'save-playlist-players_' . $post->ID, 'video_central_playlist_players_nonce' );

		printf( '<p>%s</p>', esc_html__( 'Choose which players should use this playlist:', 'video-central' ) );

		$players = video_central_get_playlist_players();
		echo '<ul style="margin-bottom: 0">';
		foreach ( $players as $id => $player ) {
			printf(
				'<li><label><input type="checkbox" name="video_central_playlist_players[]" value="%2$s"%3$s> %1$s</label></li>',
				esc_html( $player['name'] ),
				esc_attr( $player['id'] ),
				checked( $player['playlist_id'], $post->ID, false )
			);
		}
		echo '</ul>';
	}

	/**
	 * Display a meta box with instructions to embed the playlist.
	 *
	 * @since 2.0.0
	 *
	 * @param WP_Post $post Post object.
	 */
	public function display_shortcode_meta_box( $post ) {
		?>
		<p>
			<?php esc_html_e( 'Copy and paste the following shortcode into a post or page to embed this playlist.', 'video-central' ); ?>
		</p>
		<p>
			<input type="text" value="<?php echo esc_attr( '[video-central-playlist id="' . $post->ID . '"]' ); ?>" readonly>
		</p>
		<?php
	}

	/**
	 * Include the HTML templates.
	 *
	 * @since 1.0.0
	 */
	public function print_templates() {
        include( Video_Central::get_dir() . 'includes/playlist/views/templates-playlist.php' );
        include( Video_Central::get_dir() . 'includes/playlist/views/templates-videos.php' );
	}

	/**
	 * Save players connected to a playlist.
	 *
	 * @since 2.0.0
	 *
	 * @param int $post_id Post ID.
	 */
	public function on_playlist_save( $post_id ) {
		static $is_active = false; // Prevent recursion.

		$is_autosave    = defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE;
		$is_revision    = wp_is_post_revision( $post_id );
		$is_valid_nonce = isset( $_POST['video_central_playlist_players_nonce'] ) && wp_verify_nonce( $_POST['video_central_playlist_players_nonce'], 'save-playlist-players_' . $post_id );

		// Bail if the data shouldn't be saved or intention can't be verified.
		if ( $is_active || $is_autosave || $is_revision || ! $is_valid_nonce ) {
			return;
		}

		$is_active = true;

		$data = get_theme_mod( 'video_central_playlist_players', array() );

		// Reset players connected to the current playlist.
		foreach ( $data as $player_id => $playlist_id ) {
			if ( $playlist_id == $post_id ) {
				$data[ $player_id ] = 0;
			}
		}

		// Connect selected players with the current playlist.
		if ( ! empty( $_POST['video_central_playlist_players'] ) ) {
			$players = array_map( 'sanitize_key', $_POST['video_central_playlist_players'] );

			foreach ( $players as $player_id ) {
				$data[ $player_id ] = $post_id;
			}
		}

		set_theme_mod( 'video_central_playlist_players', $data );

		$is_active = false;
	}
}
