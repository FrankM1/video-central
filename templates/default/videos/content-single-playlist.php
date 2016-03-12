<?php

/**
 * Single Video Content Part
 *
 * @package Video Central
 * @subpackage Theme
 */

$playlist_instance = video_central()->playlist_instance;

if ( 1 === $playlist_instance ) {
    /**
     * Print and enqueue playlist scripts, styles, and JavaScript templates.
     *
     * @since 1.2.0
     *
     * @param string $style The 'theme' for the playlist. Core provides 'light' and 'dark'.
     */
    do_action( 'video_central_playlist_scripts');
}

?>

<div class="video-central-content">

	<?php video_central_get_template_part( 'loop', 'single-playlist' ); ?>

</div>
