<?php

/**
 * Single Video
 *
 * @package Video Central
 * @subpackage Theme
 */
get_header(); ?>

   <div class="video-central-row">

        <div class="video-central-entry-video-wrapper clearfix">

            <div class="large-10 columns large-centered">

                <div class="video-central-entry-video">

                    <?php video_central_get_template_part( 'content', 'single-video' ); ?>

                </div>

            </div>

        </div>

        <div class="large-9 columns">

            <div id="video-central-view-<?php video_central_video_id(); ?>" class="video-central-view">

                <div class="video-central-entry-content entry-content">

                    <?php video_central_get_template_part( 'content', 'single-content' ); ?>

                </div>

                <div class="video-central-entry-related-videos">

                    <?php video_central_get_template_part( 'content', 'related-video' ); ?>

                </div>

            </div><!-- #video-central-view -->

        </div><!-- .large-9 -->

        <div class="large-3 columns">
        
            <div class="sidebar">
                <?php
                	/**
                	 * video_central_sidebar hook
                	 *
                	 * @hooked video_central_get_sidebar - 10
                	 */
                	do_action( 'video_central_sidebar' );
                ?>
       		</div>
        
        </div><!-- .large-3 -->

    </div><!-- .video-central-row -->

<?php get_footer(); ?>
