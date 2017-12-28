<?php
/**
 * Underscore.js templates for the Edit Playlist screen.
 */
?>
<script type="text/html" id="tmpl-video-central-playlist-playlist-track">
	<h4 class="video-central-playlist-track-title">
		<span class="text">
			<# if ( data.title ) { #>
				{{{ data.title }}}
			<# } else { #>
				<?php esc_html_e( '(no title)', 'video-central' ); ?>
			<# } #>
		</span>
		<i class="video-central-playlist-track-toggle js-toggle"></i>
	</h4>

	<div class="video-central-playlist-track-inside">
		<div class="video-central-playlist-track-audio-group"></div>

		<div class="video-central-playlist-track-column-group">
			<div class="video-central-playlist-track-column video-central-playlist-track-column-artwork"></div>

			<div class="video-central-playlist-track-column video-central-playlist-track-column-left">
				<p>
					<label>
						<?php esc_html_e( 'Title:', 'video-central' ); ?><br>
						<input type="text" name="tracks[][title]" placeholder="<?php esc_attr_e( 'Title', 'video-central' ); ?>" value="{{{ data.title }}}" data-setting="title" class="regular-text">
					</label>
				</p>
				<p>
					<label>
						<?php esc_html_e( 'Artist:', 'video-central' ); ?><br>
						<input type="text" name="tracks[][artist]" placeholder="<?php esc_attr_e( 'Artist', 'video-central' ); ?>" value="{{{ data.artist }}}" data-setting="artist" class="regular-text">
					</label>
				</p>

				<?php do_action( 'video_central_playlist_display_track_fields_left' ); ?>
			</div>

			<div class="video-central-playlist-track-column video-central-playlist-track-column-right">
				<p>
					<label>
						<?php esc_html_e( 'Length:', 'video-central' ); ?><br>
						<input type="text" name="tracks[][length]" placeholder="<?php esc_attr_e( 'Length', 'video-central' ); ?>" value="{{ data.length }}" data-setting="length" class="small-text">
					</label>
				</p>

				<?php do_action( 'video_central_playlist_display_track_fields_right' ); ?>
			</div>
		</div>

		<div class="video-central-playlist-track-actions">
			<a class="video-central-playlist-track-remove js-remove"><?php esc_html_e( 'Remove', 'video-central' ); ?></a> |
			<a class="js-close"><?php esc_html_e( 'Close', 'video-central' ); ?></a>
		</div>
	</div>
</script>

<script type="text/html" id="tmpl-video-central-playlist-playlist-track-artwork">
	<# if ( data.artworkUrl ) { #>
		<img src="{{ data.artworkUrl }}">
	<# } #>
</script>

<script type="text/html" id="tmpl-video-central-playlist-playlist-track-audio">
	<a class="button button-secondary video-central-playlist-track-audio-selector"><?php esc_html_e( 'Select Audio', 'video-central' ); ?></a>

	<# if ( data.audioUrl ) { #>
		<audio src="{{ data.audioUrl }}" class="video-central-playlist-audio" controls preload="none" style="width: 100%; height: 30px"></audio>
	<# } #>
</script>
