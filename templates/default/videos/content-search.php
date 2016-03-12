<?php

/**
 * Search Content Part
 *
 * @package Video Central
 * @subpackage Theme
 */

?>

<div class="video-central video-central-content">

    <?php video_central_set_query_name( video_central_get_search_rewrite_id() ); ?>

    <?php do_action( 'video_central_template_before_search' ); ?>

    <?php if ( video_central_has_search_results() ) :

        video_central_get_template_part('loop', 'actions');

        video_central_get_template_part( 'loop', 'video' );

        video_central_get_template_part( 'pagination', 'search' ); ?>

    <?php elseif ( video_central_get_search_terms() ) : ?>

         <?php video_central_get_template_part( 'feedback',   'no-search' ); ?>

    <?php else : ?>

        <?php video_central_get_template_part( 'form', 'search' ); ?>

    <?php endif; ?>

    <?php do_action( 'video_central_template_after_search_results' ); ?>

</div>
