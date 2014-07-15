<?php

/**
 * Single View Content Part
 *
 * @package Video Central
 * @subpackage Theme
 */

?>

<div class="video-central video-central-content">

	<?php

	video_central_set_query_name( video_central_get_view_rewrite_id() );

	if ( video_central_view_query() ) :

		video_central_get_template_part( 'loop',       'videos'    );

		video_central_get_template_part( 'pagination', 'videos'    );

	else :

		video_central_get_template_part( 'feedback',   'no-videos' );

	endif;

	video_central_reset_query_name();

	?>

</div>
