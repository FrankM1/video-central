<script type="text/html" id="tmpl-video-central-playlist-playlist-video">
    <h4 class="video-central-playlist-video-title"><span class="text">{{{ data.title }}}</span> <i class="video-central-playlist-video-toggle js-toggle"></i></h4>

    <div class="video-central-playlist-video-inside">
        <div class="video-central-playlist-video-audio-group"></div>

        <div class="video-central-playlist-video-column-group">
            <div class="video-central-playlist-video-column video-central-playlist-video-column-artwork"></div>

            <div class="video-central-playlist-video-column video-central-playlist-video-column-left">
                <p>
                    <label>
                        <?php _e( 'Title:', 'cue' ); ?><br>
                        <input type="text" name="videos[][title]" placeholder="<?php esc_attr_e( 'Title', 'cue' ); ?>" value="{{{ data.title }}}" data-setting="title" class="regular-text">
                    </label>
                </p>
                <p>
                    <label>
                        <?php _e( 'Artist:', 'cue' ); ?><br>
                        <input type="text" name="videos[][artist]" placeholder="<?php esc_attr_e( 'Artist', 'cue' ); ?>" value="{{{ data.artist }}}" data-setting="artist" class="regular-text">
                    </label>
                </p>

                <?php do_action( 'cue_display_video_fields_left' ); ?>
            </div>

            <div class="video-central-playlist-video-column video-central-playlist-video-column-right">
                <p>
                    <label>
                        <?php _e( 'Length:', 'cue' ); ?><br>
                        <input type="text" name="videos[][length]" placeholder="<?php esc_attr_e( 'Length', 'cue' ); ?>" value="{{ data.length }}" data-setting="length" class="small-text">
                    </label>
                </p>

                <?php do_action( 'cue_display_video_fields_right' ); ?>
            </div>
        </div>

        <div class="video-central-playlist-video-actions">
            <a class="video-central-playlist-video-remove js-remove"><?php _e( 'Remove', 'cue' ); ?></a> |
            <a class="js-close"><?php _e( 'Close', 'cue' ); ?></a>
        </div>
    </div>
</script>

<script type="text/html" id="tmpl-video-central-playlist-playlist-video-artwork">
    <# if ( data.artworkUrl ) { #>
        <img src="{{ data.artworkUrl }}">
    <# } #>
</script>

<script type="text/html" id="tmpl-video-central-playlist-playlist-video-audio">
    <a class="button button-secondary video-central-playlist-video-audio-selector"><?php _e( 'Select Audio', 'cue' ); ?></a>

    <# if ( data.audioUrl ) { #>
        <audio src="{{ data.audioUrl }}" class="video-central-playlist-audio" controls preload="none" style="width: 100%; height: 30px"></audio>
    <# } #>
</script>
