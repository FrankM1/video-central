<?php

/**
 * Pagination for pages of search resurts (when viewing the archive)
 *
 * @package Video Central
 * @subpackage Theme
 */

?>
<?php do_action( 'video_central_template_before_pagination_loop' ); ?>
<div class="video-central-pagination">
    <div class="video-central-pagination-links"><?php video_central_search_pagination_links(); ?></div>
    <div class="video-central-pagination-count"><?php video_central_search_pagination_count(); ?></div>
</div>
<?php do_action( 'video_central_template_after_pagination_loop' ); ?>
