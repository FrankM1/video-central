<div class="video-central-single-video-meta">

    <div class="video-central-entry-author">
        <?php video_central_video_author_link(
            array(
                'size' => 50,
                'show_role' => true,
                'after' => '<div class="video-central-entry-date">'.video_central_get_video_date_added(0, true).'</div>', )
            ); ?>
        </div>

    <div class="video-central-sentiment"><?php do_action('video_central_video_sentiment'); ?></div>

</div>

<div class="video-central-single-video-actions">

    <div id="video-central-action-buttons" class="clearfix">

        <div id="video-central-secondary-actions">

            <?php do_action('video_central_template_secondar_action_button'); ?>

        </div>

    </div>

    <?php do_action('video_central_template_actions'); ?>

</div>
