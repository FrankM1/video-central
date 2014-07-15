<?php

/**
 * Archive Video Content Part
 *
 * @package Video Central
 * @subpackage Theme
 */

?>

<div id="video-central-related">

<?php if ( video_central_has_related_videos() ) :

        video_central_get_template_part( 'loop', 'related-videos' );

    endif; ?>

</div>
