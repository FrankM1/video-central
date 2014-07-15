<?php

/**
 * Archive Video Content Part
 *
 * @package Video Central
 * @subpackage Theme
 */

?>

<div class="video-central video-central-content">

<?php if ( video_central_has_videos() ) :

        video_central_get_template_part('loop', 'actions');

        video_central_get_template_part( 'loop', 'videos' );

        video_central_get_template_part( 'pagination', 'videos' );

    else :

        video_central_get_template_part( 'feedback', 'no-videos' );

    endif; ?>

</div>
