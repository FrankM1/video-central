<?php

/**
 * Search Loop.
 */
?>

<?php do_action('video_central_template_before_search_results_loop'); ?>

    <ul class="video-central-list <?php echo video_central_loop_item_size(); ?> switchable-view search-results" data-view="<?php echo video_central_loop_item_size(); ?>">

        <?php while (video_central_search_results()) : video_central_the_search_result(); ?>

            <?php video_central_get_template_part('loop', 'video'); ?>

        <?php endwhile; ?>

    </ul><!-- #video-central-search-results -->

<?php do_action('video_central_template_after_search_results_loop'); ?>
