<?php
/**
 * Underscore.js templates for the Media Manager.
 *
 * @package   Cue
 * @since     2.2.0
 * @copyright Copyright (c) 2016 AudioTheme, LLC
 * @license   GPL-2.0+
 */

?>

<script type="text/html" id="tmpl-video-central-videos-browser-list-item">
	<div class="video-central-videos-media-thumbnail">
		<div class="video-central-videos-media-thumbnail-image">
			<# if ( data.thumbnail ) { #>
				<img src="{{ data.thumbnail }}">
			<# } #>
		</div>
		<span class="video-central-videos-media-thumbnail-title">{{ data.title }}</span>
	</div>
	<button type="button" class="video-central-videos-media-toggle-button dashicons dashicons-yes" tabindex="0">
		<span class="screen-reader-text"><?php esc_html_e( 'Deselect', 'cue' ); ?></span>
	</button>
</script>

<script type="text/html" id="tmpl-video-central-videos-browser-sidebar">
	<div class="video-central-videos-browser-settings collection-settings">
		<h2><?php esc_html_e( 'Playlist Settings', 'cue' ); ?></h2>

		<div class="setting">
			<p>
				<label>
					<span><?php esc_html_e( 'Theme', 'cue' ); ?></span>
					<select data-setting="theme">
						<option value=""></option>
					</select>
				</label>
			</p>
		</div>

		<div class="setting">
			<p>
				<label>
					<input type="checkbox" data-setting="show_videos" checked>
					<span><?php esc_html_e( 'Show Tracklist', 'cue' ); ?></span>
				</label>
			</p>
		</div>
	</div>
</script>

<script type="text/html" id="tmpl-video-central-videos-browser-empty">
	<h2><?php esc_html_e( 'No items found.', 'cue' ); ?></h2>
	<p>
		<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=' . video_central_get_video_post_type() ) ); ?>"><?php esc_html_e( 'Create a video.', 'cue' ); ?></a>
	</p>
</script>
