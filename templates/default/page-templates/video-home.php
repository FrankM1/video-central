<?php
/**
 * Template Name: Video Home
 */

/**
* The template for displaying all pages.
*/
get_header();

global $wp_query;

$paged = isset($wp_query->query['paged']) ? $wp_query->query['paged'] : null;

do_action('video_central_before_page');

?>
    <div class="video-central-row page-content">

        <main class="content large-12"  role="main" itemprop="mainContentOfPage" itemscope="itemscope" itemtype="http://schema.org/Page">

            <?php do_action( 'video_central_before_main_content' ); ?>

            <?php do_action( 'video_central_template_notices' ); ?>

            <div id="video-central-front" class="video-central-front">

                <?php if( video_central_show_slider_on_root() && $paged <= 1) { ?>
                    <?php echo do_shortcode('[video-central-slider-grid]'); ?>
                <?php } ?>

                <?php echo do_shortcode('[video-central-index]'); ?>

            </div><!-- #video-central-front -->

            <?php do_action( 'video_central_after_main_content' ); ?>

        </main><!-- #main -->

      </div><!--.video-central-row-->

<?php

do_action('video_central_after_page');

get_footer();

?>
