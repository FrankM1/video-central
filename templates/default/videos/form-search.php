<?php

/**
 * Search 
 *
 * @package Video Central
 * @subpackage Theme
 */

?>
<form role="search" method="get" id="video-central-search-form" action="<?php video_central_search_url(); ?>">
	<div>
		<label class="screen-reader-text hidden" for="video_search"><?php _e( 'Search for:', 'video_central' ); ?></label>
		<input type="hidden" name="action" value="video-search-request" />
		<input tabindex="<?php video_central_tab_index(); ?>" type="text" value="<?php echo esc_attr( video_central_get_search_terms() ); ?>" name="video_search" id="video-central-search" />
		<input tabindex="<?php video_central_tab_index(); ?>" class="button" type="submit" id="video-central-search-submit" value="<?php esc_attr_e( 'Search', 'video_central' ); ?>" />
	</div>
</form>
