<?php

/**
 * Videos Loop - Single Video
 *
 * @package Video Central
 * @subpackage Theme
 */

?>

<div id="video-central-player-<?php video_central_video_id(); ?>" class="video-central-player loading">

    <?php do_action( 'video_central_template_before_video_player' ); ?>

    <div class="video-central-player-video-wrapper video-central-video-source-<?php video_central_video_source(); ?>">
        <?php video_central_player(); ?>
    </div>

    <?php do_action( 'video_central_template_after_video_player' ); ?>

</div>
