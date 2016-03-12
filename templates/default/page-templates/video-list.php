<?php
/**
 * Template Name: Video List
 */

/**
* The template for displaying all pages.
*/
get_header();

   if ( !get_post_meta( get_the_ID(), '_video_central_hide_title', true ) )
        get_template_part( 'includes/content/content', 'header' );

    do_action('video_central_before_page');

    ?>
    <div class="video-central-row page-content">

        <main class="content large-12"  role="main" itemprop="mainContentOfPage" itemscope="itemscope" itemtype="http://schema.org/Page">

            <?php do_action( 'video_central_before_main_content' ); ?>

            <?php do_action( 'video_central_template_notices' ); ?>

            <div id="video-central-front" class="video-central-front">

                <div class="video-central-entry-content entry-content">

                    <?php echo do_shortcode('[video-central-index]'); ?>

                </div>

            </div><!-- #video-central-front -->

            <?php do_action( 'video_central_after_main_content' ); ?>

        </main><!-- #main -->

      </div><!--.video-central-row-->

<?php

do_action('video_central_after_page');

get_footer();

?>
