<?php 
$args = array(
	'ajaxload' 		=> false,
	'autoplay' 		=> false,
	'auto_scroll_interval' 	=> 10000,
);

$ajaxload = isset($args['ajaxload']) ? $args['ajaxload'] : false;
$auto_scroll_interval = isset($args['auto_scroll_interval']) ? $args['auto_scroll_interval'] : false;

if (!$args['autoplay']) $auto_scroll_interval = 0;

$large_image_sizes = array( 'width' => 910, 'height' => 530);
$small_image_sizes = array( 'width' => 160, 'height' => 90);
	
$query_args = array(
	'posts_per_page' => 12,
	'meta_key'      => '_video_central_featured_video',
	'max_num_pages' => 1,
	'orderby'       => 'meta_value_num',
	'show_stickies' => false
);

if( video_central_has_videos($query_args) ) : ?>

<div id="video-home-featured" class="video-home-featured video-home-slider-grid wall">

	<div class="video-wall-wrap clearfix">

		<div class="video-wall-wrap-inner">

			<div class="video-carousel" data-autoscroll-interval="<?php echo $auto_scroll_interval; ?>">

				<div class="video-carousel-list">

					<?php

					$items = '';
                    $i = 0;

		            while( video_central_videos() ): video_central_the_video();

						$i++; ?>

						<div class="video-item" data-id="<?php video_central_video_id(); ?>">

							<div class="thumb">

								<a class="poster-link" href="<?php video_central_video_permalink() ?>" title="<?php the_title_attribute(); ?>" >

										<img src="<?php video_central_featured_image_url( video_central_get_video_id(), $large_image_sizes); ?>" alt="<?php the_title_attribute(); ?>" title="<?php the_title_attribute(); ?>">
								</a>

							</div>

							<div class="caption"<?php if(empty($args['auto_scroll_interval']) && $i == 1) echo 'style="display:none;"'; ?>>
								<h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php printf(__('Permalink to %s', 'video_central'), get_the_title()); ?>"><?php the_title(); ?></a></h2>
							</div>

						</div><!-- end .video-item -->

					<?php endwhile; ?>

				</div><!-- end .video-carousel-list -->

			</div><!-- end .video-carousel -->
			
			<a class="video-carousel-list-prev" href="#"></a>
			<a class="video-carousel-list-next" href="#"></a>

		</div><!-- end .video-wall-wrap-inner-->

		<div class="carousel-nav">

			<div class="video-carousel">

				<div class="video-carousel-clip">

					<ul class="video-carousel-list">
						<?php

						$items = '';
                        $i = 0;

		            	while(video_central_videos()): video_central_the_video();

		           	 	?>
							<li data-id="<?php the_ID(); ?>" class="item-video">
								<div class="inner">
									<div class="thumb">
										<a class="poster-link" data-id="<?php video_central_video_id(); ?>" title="<?php video_central_video_title(); ?>" href="<?php video_central_video_permalink(); ?>">
											<span class="clip">
												<img src="<?php video_central_featured_image_url( video_central_get_video_id(), $small_image_sizes ); ?>" alt="<?php video_central_video_title(); ?>" /><span class="vertical-align"></span>
											</span>
											<span class="overlay"></span>
										</a>
									</div>

									<div class="data">
										<h2 class="entry-title"><a href="<?php video_central_video_permalink(); ?>" rel="bookmark" title="<?php __('Permalink to', 'video_central').' '.video_central_get_video_title(); ?>"><?php video_central_video_title(); ?></a></h2>

										<p class="meta">
											<span class="time"><?php video_central_video_date_added(0, true); ?></span>
										</p>

									</div>

								</div>
							</li>

						<?php $i++; endwhile; ?>
					</ul>
				</div><!-- end .carousel-clip -->

				<a class="video-carousel-prev" href="#"></a>
				<a class="video-carousel-next" href="#"></a>

			</div><!-- end .carousel -->

		</div><!-- end .carousel-nav -->

	</div><!-- end .wrap -->

</div><!-- end #video-wall -->

<?php endif; ?>
