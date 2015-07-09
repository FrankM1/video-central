<?php

/**
 * Videos Loop - Single Video
 *
 * @package Video Central
 * @subpackage template
 */

$content_toggle = video_central_content_toggle();

?>

<article id="video-central-content-<?php video_central_video_id(); ?>" class="video-central">

    <?php do_action( 'video_central_template_before_video_title' ); ?>

    <h2><?php video_central_video_title(); ?></h2>

    <?php 
    
    do_action( 'video_central_template_after_video_title' ); 
    
	if ( video_central_allow_video_meta() ) {
 		video_central_get_template_part( 'loop-single-video', 'actions' ); 
 	} 
    
    do_action( 'video_central_template_before_video_content' ); 
    
    ?>

	<div class="video-central-single-content entry-content"><?php video_central_content(); ?></div>

    <?php if($content_toggle) { ?>

        <div class="video-central-content-toggle">

            <div class="video-central-content-gradient"></div>

            <a href="#" class="video-central-info-toggle-button video-central-info-more-button clearfix">

                <span class="video-central-more-text"><?php _e('Show more', 'video_central'); ?></span>

                <span class="video-central-less-text"><?php _e('Show less', 'video_central'); ?></span>
            </a>

        </div>

    <?php } ?>

	<div id="video-central-action-panels" class="video-central-entry-meta">

		<?php do_action( 'video_central_template_secondary_before_action_panel' ); ?>

		<div id="video-central-action-panel-details">

			<?php do_action( 'video_central_template_before_video_content' ); ?>

			<?php if( has_action('video_central_template_content_footer') ) { ?>

				<footer class="meta clearfix">

					<?php

						do_action( 'video_central_template_content_footer' );

						if ( video_central_allow_social_links() ) {

							video_central_get_template_part( 'social', 'share' );

						} ?>

				</footer>

			<?php } ?>

		</div>

		<?php do_action( 'video_central_template_secondary_after_action_panel' ); ?>

	</div>

	<?php do_action( 'video_central_template_after_video_content' ); ?>

</article>
