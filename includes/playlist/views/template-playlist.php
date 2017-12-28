<script type="text/html" id="tmpl-video-central-playlist-video">
    <h4 class="video-central-playlist-track-title"><span class="text">{{{ data.title }}}</span> <i class="video-central-playlist-track-toggle js-toggle"></i></h4>

    <div class="video-central-playlist-track-inside">
        <div class="video-central-playlist-track-audio-group"></div>

        <div class="video-central-playlist-track-column-group">
            <div class="video-central-playlist-track-column video-central-playlist-track-column-artwork"></div>

            <div class="video-central-playlist-track-column video-central-playlist-track-column-left">
                <p>
                    <label>
                        <?php _e( 'Title:', 'video-central' ); ?><br>
                        <input type="text" name="tracks[][title]" placeholder="<?php esc_attr_e( 'Title', 'video-central' ); ?>" value="{{{ data.title }}}" data-setting="title" class="regular-text">
                    </label>
                </p>

                <?php do_action( 'video-central-playlist_display_track_fields_left' ); ?>
            </div>

        </div>

        <div class="video-central-playlist-track-actions">
            <a class="video-central-playlist-track-remove js-remove"><?php _e( 'Remove', 'video-central' ); ?></a> |
            <a class="js-close"><?php _e( 'Close', 'video-central' ); ?></a>
        </div>
    </div>
</script>

<script type="text/html" id="tmpl-video-central-playlist-playlist-track-artwork">
    <# if ( data.artworkUrl ) { #>
        <img src="{{ data.artworkUrl }}">
    <# } #>
</script>

<script type="text/html" id="tmpl-video-central-playlist-playlist-track-audio">
    <a class="button button-secondary video-central-playlist-track-audio-selector"><?php _e( 'Select Audio', 'video-central' ); ?></a>

    <# if ( data.audioUrl ) { #>
        <audio src="{{ data.audioUrl }}" class="video-central-playlist-audio" controls preload="none" style="width: 100%; height: 30px"></audio>
    <# } #>
</script>
