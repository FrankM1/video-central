<?php

/**
 * Videos Loop
 *
 * @package Video Central
 * @subpackage Theme
 */

?>

<?php do_action( 'video_central_template_before_videos_loop' ); ?>

<ul class="video-central-list <?php echo video_central_loop_item_size(); ?> switchable-view" data-view="<?php echo video_central_loop_item_size(); ?>">

    <?php

    while ( video_central_videos() ) : video_central_the_video();

        video_central_get_template_part( 'loop', 'video' );

    endwhile;

    ?>

</ul><!-- .video-central-directory -->

<?php do_action( 'video_central_template_after_videos_loop' ); ?>
