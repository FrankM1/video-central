<script type="text/html" id="tmpl-video-central-playlist-video">
    <h4 class="cue-track-title"><span class="text">{{{ data.title }}}</span> <i class="cue-track-toggle js-toggle"></i></h4>

    <div class="cue-track-inside">
        <div class="cue-track-audio-group"></div>

        <div class="cue-track-column-group">
            <div class="cue-track-column cue-track-column-artwork"></div>

            <div class="cue-track-column cue-track-column-left">
                <p>
                    <label>
                        <?php _e( 'Title:', 'cue' ); ?><br>
                        <input type="text" name="tracks[][title]" placeholder="<?php esc_attr_e( 'Title', 'cue' ); ?>" value="{{{ data.title }}}" data-setting="title" class="regular-text">
                    </label>
                </p>
                
                <?php do_action( 'cue_display_track_fields_left' ); ?>
            </div>

        </div>

        <div class="cue-track-actions">
            <a class="cue-track-remove js-remove"><?php _e( 'Remove', 'cue' ); ?></a> |
            <a class="js-close"><?php _e( 'Close', 'cue' ); ?></a>
        </div>
    </div>
</script>

<script type="text/html" id="tmpl-cue-playlist-track-artwork">
    <# if ( data.artworkUrl ) { #>
        <img src="{{ data.artworkUrl }}">
    <# } #>
</script>

<script type="text/html" id="tmpl-cue-playlist-track-audio">
    <a class="button button-secondary cue-track-audio-selector"><?php _e( 'Select Audio', 'cue' ); ?></a>

    <# if ( data.audioUrl ) { #>
        <audio src="{{ data.audioUrl }}" class="cue-audio" controls preload="none" style="width: 100%; height: 30px"></audio>
    <# } #>
</script>
