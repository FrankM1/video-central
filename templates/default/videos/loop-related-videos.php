 <?php do_action( 'video_central_template_before_related_videos_loop' ); ?>

 <div class="section-box related-posts">

     <div class="section-header"><h3 class="section-title"><span><?php _e('You may also like', 'video_central') ?></span></h3></div>

    <ul class="video-central-list <?php echo video_central_loop_item_size(); ?>">

     <?php while( video_central_related_videos() ) : video_central_the_related_video();

        video_central_get_template_part( 'loop', 'video' );

     endwhile; ?>

    </ul><!-- .video-central-directory -->

</div><!-- end .related-posts -->

<?php do_action( 'video_central_template_after_related_videos_loop' ); ?>
