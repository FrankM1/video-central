<section class="video-central-content video-central-slider-grid-tab">

	<ul class="video-central-slider-grid-nav-tabs video-central-nav video-central-nav-tabs clearfix">
	    <li><a href="#video_central_slider_grid_featured_videos" data-toggle="tab"><?php _e('Featured Videos', 'video_central'); ?></a></li>
	    <li class="active"><a href="#video_central_slider_grid_popular_videos" data-toggle="tab"><?php _e('Popular Videos', 'video_central'); ?></a></li>
	</ul>

	<div class="video-central-tab-content">

		<div class="video-central-tab-pane latest" id="video_central_slider_grid_featured_videos">
			<?php video_central_get_template_part( 'content', 'video-slider-grid-featured' ); ?>
		</div><!-- .tab-pane -->

		<div class="video-central-tab-pane popular active" id="video_central_slider_grid_popular_videos">
			<?php video_central_get_template_part( 'content', 'video-slider-grid-popular' ); ?>
		</div><!-- .tab-pane -->

	</div><!-- .tab-content -->

</section>
