<?php

/**
 * Videos Loop - Single Playlist
 *
 * @package Video Central
 * @subpackage Theme
 */

?>

<div id="video-central-playlist-<?php video_central_playlist_id(); ?>" class="video-central-playlist loading">

    <?php do_action( 'video_central_template_before_playlist' ); ?>

    <?php video_central_playlist(); ?>

    <?php do_action( 'video_central_template_after_playlist' ); ?>

</div>
