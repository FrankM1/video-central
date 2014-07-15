<?php

/**
 * Radium Video - Search
 *
 * @package Video Central
 * @subpackage Theme
 */

get_header(); ?>

    <div class="video-central-row">

        <div class="large-9 columns">

        	<?php do_action( 'video_central_template_before_main_content' ); ?>

        	<?php do_action( 'video_central_template_notices' ); ?>

        	<div id="video-central-front" class="video-central-front">

        		<div class="video-central-entry-content entry-content">

        			<?php video_central_get_template_part( 'content', 'search' ); ?>

        		</div>

        	</div><!-- #video-central-front -->

        	<?php do_action( 'video_central_template_after_main_content' ); ?>

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
        </div><!-- .large-3  -->

    </div><!-- .video-central-row -->

<?php get_footer(); ?>
