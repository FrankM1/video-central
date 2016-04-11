<?php

/**
 * Videos Loop - Single Video
 *
 * @package Video Central
 * @subpackage Theme
 */

$image_size = video_central_thumbnail_dimensions();

?>

<li class="video-central-item preload image-loading">

    <?php do_action( 'video_central_template_before_video_thumb' ); ?>

    <a class="video-central-thumb" href="<?php video_central_video_permalink(); ?>">

        <img src="<?php video_central_featured_image_url(); ?>" alt="" height="<?php echo esc_attr( $image_size['height'] ); ?>" width="<?php echo esc_attr( $image_size['width'] ); ?>"/>

        <span class="video-icon-wrapper"><span class="icon icon-play"></span></span>

        <div class="video-entry-meta duration"><?php do_action( 'video_central_video_duration' ); ?></div>

    </a>

    <?php do_action( 'video_central_template_before_video_title' ); ?>

    <h3 class="entry-title">
        <a class="video-central-title" href="<?php video_central_video_permalink(); ?>">
            <?php video_central_video_short_title(); ?>
        </a>
    </h3>

    <?php do_action( 'video_central_template_after_video_title' ); ?>

</li>
