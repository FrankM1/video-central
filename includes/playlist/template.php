<?php

/**
 * Playlist Central Playlist Functions.
 */


/** Playlists Loop ****************************************************************/

/**
 * The main video loop.
 *
 * WordPress makes this easy for us.
 *
 * @since 1.0.0
 *
 * @param mixed $args All the arguments supported by {@link WP_Query}
 *
 * @uses WP_Query To make query and get the playlists
 * @uses video_central_get_playlists_post_type() To get the video post type id
 * @uses video_central_get_playlist_id() To get the video id
 * @uses get_option() To get the playlists per page option
 * @uses current_user_can() To check if the current user is capable of editing
 *                           others' playlists
 * @uses apply_filters() Calls 'video_central_has_playlists' with
 *                        video_central::playlist_query::have_posts()
 *                        and video_central::playlist_query
 *
 * @return object Multidimensional array of video information
 */
function video_central_has_playlists( $args = '' ) {
    global $wp_rewrite;

    /* Defaults **************************************************************/

    // Other defaults
    $default_playlists_search = !empty($_REQUEST['vs']) ? $_REQUEST['vs'] : false;
    $default_post_parent = video_central_is_playlist_video() ? video_central_get_playlist_id() : 'any';

    // Default argument array
    $default = array(
        'post_type' => video_central_get_playlists_post_type(), // Narrow query down to playlists
        'post_parent' => $default_post_parent,      //  ID
        'orderby' => 'meta_value',              // 'meta_value', 'author', 'date', 'title', 'modified', 'parent', rand',
        'order' => 'DESC',                    // 'ASC', 'DESC'
        'posts_per_page' => video_central_get_playlists_per_page(), // playlists per page
        'paged' => video_central_get_paged(),           // Page Number
        's' => $default_playlists_search,     // Video Search
        'max_num_pages' => false,                     // Maximum number of pages to show
    );

    // Maybe query for video tags
    if (video_central_is_playlist_tag()) {
        $default['term'] = video_central_get_playlist_tag_slug();
        $default['taxonomy'] = video_central_get_playlist_tag_tax_id();
    }

    // Maybe query for video category
    if (video_central_is_playlist_category()) {
        $default['term'] = video_central_get_playlist_category_slug();
        $default['taxonomy'] = video_central_get_playlist_category_tax_id();
    }

    /* Setup *****************************************************************/

    // Parse arguments against default values
    $r = video_central_parse_args($args, $default, 'has_playlists');

    // get the video_central instance
    $video_central = video_central();

    // Call the query
    $video_central->playlist_query = new WP_Query($r);

    // Set post_parent back to 0 if originally set to 'any'
    if ('any' === $r['post_parent']) {
        $r['post_parent'] = 0;
    }

    // Limited the number of pages shown
    if (!empty($r['max_num_pages'])) {
        $video_central->playlist_query->max_num_pages = $r['max_num_pages'];
    }

    // If no limit to posts per page, set it to the current post_count
    if (-1 === $r['posts_per_page']) {
        $r['posts_per_page'] = $video_central->playlist_query->post_count;
    }

    // Add pagination values to query object
    $video_central->playlist_query->posts_per_page = $r['posts_per_page'];
    $video_central->playlist_query->paged = $r['paged'];

    // Video archive only shows root
    if (video_central_is_playlist_archive()) {
        $default_post_parent = 0;

    // Could be anything, so look for possible parent ID
    } else {
        $default_post_parent = video_central_get_playlist_id();
    }

    // Only add pagination if query returned results
    if (((int) $video_central->playlist_query->post_count || (int) $video_central->playlist_query->found_posts) && (int) $video_central->playlist_query->posts_per_page) {

        // Limit the number of playlists shown based on maximum allowed pages
        if ((!empty($r['max_num_pages'])) && $video_central->playlist_query->found_posts > $video_central->playlist_query->max_num_pages * $video_central->playlist_query->post_count) {
            $video_central->playlist_query->found_posts = $video_central->playlist_query->max_num_pages * $video_central->playlist_query->post_count;
        }

        // If pretty permalinks are enabled, make our pagination pretty
        if ($wp_rewrite->using_permalinks()) {
            if (video_central_is_playlist_tag()) {
                $base = video_central_get_playlist_tag_link();

            // Page or single post
            } elseif (video_central_is_playlist_category()) {
                $base = video_central_get_playlist_category_link();

            // Page or single post
            } elseif (is_page() || is_single()) {
                $base = get_permalink();

            // View
            } elseif (video_central_is_single_view()) {
                $base = video_central_get_view_url();

            // video archive
            } elseif (video_central_is_playlist_archive()) {
                $base = video_central_get_archive_url();

            // Default
            } else {
                $base = get_permalink((int) $r['post_parent']);
            }

            // Use pagination base
            $base = trailingslashit($base).user_trailingslashit($wp_rewrite->pagination_base.'/%#%/');

        // Unpretty pagination
        } else {
            $base = add_query_arg('paged', '%#%');
        }

        // Pagination settings with filter
        $video_central_pagination = apply_filters('video_central_pagination', array(
            'base' => $base,
            'format' => '',
            'total' => $r['posts_per_page'] === $video_central->playlist_query->found_posts ? 1 : ceil((int) $video_central->playlist_query->found_posts / (int) $r['posts_per_page']),
            'current' => (int) $video_central->playlist_query->paged,
            'prev_text' => is_rtl() ? '&rarr;' : '&larr;',
            'next_text' => is_rtl() ? '&larr;' : '&rarr;',
            'mid_size' => 8,
            'end_size' => 1,
        ));

        // Add pagination to query object
        $video_central->playlist_query->pagination_links = apply_filters('video_central_pagination_links', paginate_links($video_central_pagination), $video_central->playlist_query, $base, $r);

        // Remove first page from pagination
        $video_central->playlist_query->pagination_links = str_replace($wp_rewrite->pagination_base."/1/'", "'", $video_central->playlist_query->pagination_links);
    }

    return apply_filters( __FUNCTION__, $video_central->playlist_query->have_posts(), $video_central->playlist_query );
}

/**
 * Whether there are more playlists available in the loop.
 *
 * @since 1.0.0
 *
 * @uses video_central:playlist_query::have_posts() To check if there are more playlists
 *                                          available
 *
 * @return object Video information
 */
function video_central_playlists() {

    // Put into variable to check against next
    $have_posts = video_central()->playlist_query->have_posts();

    // Reset the post data when finished
    if ( empty( $have_posts ) ) {
        wp_reset_postdata();
    }

    return $have_posts;
}

/**
 * Loads up the current video in the loop.
 *
 * @since 1.0.0
 *
 * @uses video_central::playlist_query::the_post() To get the current video
 *
 * @return object information
 */
function video_central_the_playlists() {
    return video_central()->playlist_query->the_post();
}

/**
 * Check if current page is a playlist archive.
 *
 * @since 1.0.0
 *
 * @param int $post_id Possible post_id to check
 *
 * @uses video_central_get_playlists_post_type() To get the video post type
 *
 * @return bool True if it's a playlist page, false if not
 */
function video_central_is_playlist($post_id = 0)
{

    // Assume false
    $retval = false;

    // Supplied ID is a playlist
    if (!empty($post_id) && (video_central_get_playlists_post_type() === get_post_type($post_id))) {
        $retval = true;
    }

    return (bool) apply_filters(__FUNCTION__, $retval, $post_id);
}

/**
 * Check if we are viewing a video archive.
 *
 * @since 1.0.0
 *
 * @uses is_post_type_archive() To check if we are looking at the video archive
 * @uses video_central_get_playlists_post_type() To get the video post type ID
 *
 * @return bool
 */
function video_central_is_playlist_archive()
{
    global $wp_query;

    // Default to false
    $retval = false;

    // In video archive
    if (is_post_type_archive(video_central_get_playlists_post_type()) || video_central_is_query_name('video_central_playlists_archive') || !empty($wp_query->video_central_show_videos_on_root) ||  video_central_is_playlist_category() || video_central_is_playlist_tag()) {
        $retval = true;
    }

    return (bool) apply_filters(__FUNCTION__, $retval);
}

/**
 * Viewing a single video.
 *
 * @since 1.0.0
 *
 * @uses is_single()
 * @uses video_central_get_playlists_post_type()
 * @uses get_post_type()
 * @uses apply_filters()
 *
 * @return bool
 */
function video_central_is_playlist_video()
{

    // Assume false
    $retval = false;

    // Single and a match
    if (is_singular(video_central_get_playlists_post_type()) || video_central_is_query_name('video_central_playlist_video')) {
        $retval = true;
    }

    return (bool) apply_filters(__FUNCTION__, $retval);
}



/**
 * Check if the current page is a video tag.
 *
 * @since 1.0.0
 *
 * @return bool True if it's a video tag, false if not
 */
function video_central_is_playlist_tag()
{

    // Bail if video-tags are off
    if (!video_central_allow_playlist_tags()) {
        return false;
    }

    // Return false if editing a video tag
    if (video_central_is_playlist_tag_edit()) {
        return false;
    }

    // Assume false
    $retval = false;

    // Check tax and query vars
    if (is_tax(video_central_get_playlist_tag_tax_id()) || !empty(video_central()->video_query->is_tax) || get_query_var('video_central_tag')) {
        $retval = true;
    }

    return (bool) apply_filters(__FUNCTION__, $retval);
}

/**
 * Check if the current page is editing a video tag.
 *
 * @since 1.0.0
 *
 * @uses WP_Query Checks if WP_Query::video_central_is_playlist_tag_edit is true
 *
 * @return bool True if editing a video tag, false if not
 */
function video_central_is_playlist_tag_edit()
{
    global $wp_query, $pagenow, $taxnow;

    // Bail if video-tags are off
    if (! video_central_allow_playlist_tags() ) {
        return false;
    }

    // Assume false
    $retval = false;

    // Check query
    if (!empty($wp_query->video_central_is_playlist_tag_edit) && (true === $wp_query->video_central_is_playlist_tag_edit)) {
        $retval = true;
    }

    // Editing in admin
    elseif (is_admin() && ('edit-tags.php' === $pagenow) && (video_central_get_playlist_tag_tax_id() === $taxnow) && (!empty($_GET['action']) && ('edit' === $_GET['action']))) {
        $retval = true;
    }

    return (bool) apply_filters(__FUNCTION__, $retval);
}

/**
 * Check if the current page is a video tag.
 *
 * @since 1.0.0
 *
 * @return bool True if it's a video tag, false if not
 */
function video_central_is_playlist_category()
{

    // Bail if video-tags are off
    if (!video_central_allow_playlist_categories()) {
        return false;
    }

    // Return false if editing a video tag
    if (video_central_is_playlist_category_edit()) {
        return false;
    }

    // Assume false
    $retval = false;

    // Check tax and query vars
    if (is_tax(video_central_get_playlist_category_tax_id()) || !empty(video_central()->video_query->is_tax) || get_query_var('video_central_category')) {
        $retval = true;
    }

    return (bool) apply_filters(__FUNCTION__, $retval);
}

/**
 * Check if the current page is editing a video tag.
 *
 * @since 1.0.0
 *
 * @uses WP_Query Checks if WP_Query::video_central_is_playlist_category_edit is true
 *
 * @return bool True if editing a video tag, false if not
 */
function video_central_is_playlist_category_edit()
{
    global $wp_query, $pagenow, $taxnow;

    // Bail if video-tags are off
    if (!video_central_allow_playlist_categories()) {
        return false;
    }

    // Assume false
    $retval = false;

    // Check query
    if (!empty($wp_query->video_central_is_playlist_category_edit) && (true === $wp_query->video_central_is_playlist_category_edit)) {
        $retval = true;
    }

    // Editing in admin
    elseif (is_admin() && ('edit-categories.php' === $pagenow) && (video_central_get_playlist_category_tax_id() === $taxnow) && (!empty($_GET['action']) && ('edit' === $_GET['action']))) {
        $retval = true;
    }

    return (bool) apply_filters(__FUNCTION__, $retval);
}



 
/** Playlist Tags ****************************************************************/

/**
 * Output the unique id of the video tag taxonomy.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_playlist_tag_tax_id() To get the video tag id
 */
function video_central_playlist_tag_tax_id()
{
    echo video_central_get_playlist_tag_tax_id();
}
    /**
     * Return the unique id of the video tag taxonomy.
     *
     * @since 1.0.0
     *
     * @uses apply_filters() Calls 'video_central_get_playlist_tag_tax_id' with the video tax id
     *
     * @return string The unique video tag taxonomy
     */
    function video_central_get_playlist_tag_tax_id()
    {
        return apply_filters(__FUNCTION__, video_central()->video_tag_tax_id);
    }

/**
 * Output the name of the current tag.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_playlist_tag_name()
 */
function video_central_playlist_tag_name($tag = '')
{
    echo video_central_get_playlist_tag_name($tag);
}
    /**
     * Return the name of the current tag.
     *
     * @since 1.0.0
     *
     * @uses get_term_by()
     * @uses get_queried_object()
     * @uses get_query_var()
     * @uses apply_filters()
     *
     * @return string Term Name
     */
    function video_central_get_playlist_tag_name($tag = '')
    {

        // Get the term
        if (!empty($tag)) {
            $term = get_term_by('slug', $tag, video_central_get_playlist_tag_tax_id());
        } else {
            $tag = get_query_var('term');
            $term = get_queried_object();
        }

        // Add before and after if description exists
        if (!empty($term->name)) {
            $retval = $term->name;

        // No name
        } else {
            $retval = '';
        }

        return apply_filters('video_central_get_playlist_tag_name', $retval);
    }

/**
 * Output the slug of the current tag.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_playlist_tag_slug()
 */
function video_central_playlist_tag_slug($tag = '')
{
    echo video_central_get_playlist_tag_slug($tag);
}
    /**
     * Return the slug of the current tag.
     *
     * @since 1.0.0
     *
     * @uses get_term_by()
     * @uses get_queried_object()
     * @uses get_query_var()
     * @uses apply_filters()
     *
     * @return string Term Name
     */
    function video_central_get_playlist_tag_slug($tag = '')
    {

        // Get the term
        if (!empty($tag)) {
            $term = get_term_by('slug', $tag, video_central_get_playlist_tag_tax_id());
        } else {
            $tag = get_query_var('term');
            $term = get_queried_object();
        }

        // Add before and after if description exists
        if (!empty($term->slug)) {
            $retval = $term->slug;

        // No slug
        } else {
            $retval = '';
        }

        return apply_filters('video_central_get_playlist_tag_slug', $retval);
    }

/**
 * Output the link of the current tag.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_playlist_tag_link()
 */
function video_central_playlist_tag_link($tag = '')
{
    echo esc_url(video_central_get_playlist_tag_link($tag));
}
    /**
     * Return the link of the current tag.
     *
     * @since 1.0.0
     *
     * @uses get_term_by()
     * @uses get_queried_object()
     * @uses get_query_var()
     * @uses apply_filters()
     *
     * @return string Term Name
     */
    function video_central_get_playlist_tag_link($tag = '')
    {

        // Get the term
        if (!empty($tag)) {
            $term = get_term_by('slug', $tag, video_central_get_playlist_tag_tax_id());
        } else {
            $tag = get_query_var('term');
            $term = get_queried_object();
        }

        // Add before and after if description exists
        if (!empty($term->term_id)) {
            $retval = get_term_link($term, video_central_get_playlist_tag_tax_id());

        // No link
        } else {
            $retval = '';
        }

        return apply_filters('video_central_get_playlist_tag_link', $retval, $tag);
    }

/** Playlist Categories ****************************************************************/

/**
 * Output the unique id of the video category taxonomy.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_playlist_category_tax_id() To get the video category id
 */
function video_central_playlist_category_tax_id()
{
    echo video_central_get_playlist_category_tax_id();
}
    /**
     * Return the unique id of the video category taxonomy.
     *
     * @since 1.0.0
     *
     * @uses apply_filters() Calls 'video_central_get_playlist_cat_tax_id' with the video tax id
     *
     * @return string The unique video category taxonomy
     */
    function video_central_get_playlist_category_tax_id()
    {
        return apply_filters(__FUNCTION__, video_central()->video_cat_tax_id);
    }
/**
 * Output the name of the current category.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_playlist_tcategory_name()
 */
function video_central_playlist_category_name($category = '')
{
    echo video_central_get_playlist_category_name($category);
}
    /**
     * Return the name of the current category.
     *
     * @since 1.0.0
     *
     * @uses get_term_by()
     * @uses get_queried_object()
     * @uses get_query_var()
     * @uses apply_filters()
     *
     * @return string Term Name
     */
    function video_central_get_playlist_category_name($category = '')
    {

        // Get the term
        if (!empty($category)) {
            $term = get_term_by('slug', $category, video_central_get_playlist_category_tax_id());
        } else {
            $category = get_query_var('term');
            $term = get_queried_object();
        }

        // Add before and after if description exists
        if (!empty($term->name)) {
            $retval = $term->name;

        // No name
        } else {
            $retval = '';
        }

        return apply_filters(__FUNCTION__, $retval);
    }

/**
 * Output the slug of the current category.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_playlist_category_slug()
 */
function video_central_playlist_category_slug($category = '')
{
    echo video_central_get_playlist_category_slug($category);
}
    /**
     * Return the slug of the current category.
     *
     * @since 1.0.0
     *
     * @uses get_term_by()
     * @uses get_queried_object()
     * @uses get_query_var()
     * @uses apply_filters()
     *
     * @return string Term Name
     */
    function video_central_get_playlist_category_slug($category = '')
    {

        // Get the term
        if (!empty($category)) {
            $term = get_term_by('slug', $category, video_central_get_playlist_category_tax_id());
        } else {
            $category = get_query_var('term');
            $term = get_queried_object();
        }

        // Add before and after if description exists
        if (!empty($term->slug)) {
            $retval = $term->slug;

        // No slug
        } else {
            $retval = '';
        }

        return apply_filters(__FUNCTION__, $retval);
    }

/**
 * Output the link of the current category.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_playlist_category_link()
 */
function video_central_playlist_category_link($category = '')
{
    echo esc_url(video_central_get_playlist_category_link($category));
}
    /**
     * Return the link of the current category.
     *
     * @since 1.0.0
     *
     * @uses get_term_by()
     * @uses get_queried_object()
     * @uses get_query_var()
     * @uses apply_filters()
     *
     * @return string Term Name
     */
    function video_central_get_playlist_category_link($category = '')
    {

        // Get the term
        if (!empty($category)) {
            $term = get_term_by('slug', $category, video_central_get_playlist_category_tax_id());
        } else {
            $category = get_query_var('term');
            $term = get_queried_object();
        }

        // Add before and after if description exists
        if (!empty($term->term_id)) {
            $retval = get_term_link($term, video_central_get_playlist_category_tax_id());

        // No link
        } else {
            $retval = '';
        }

        return apply_filters(__FUNCTION__, $retval, $category);
    }

/**
 * Output the visibility of the video.
 *
 * @since 1.0.0
 *
 * @param int $playlist_id Optional. video id
 *
 * @uses video_central_get_playlist_visibility() To get the video visibility
 */
function video_central_playlist_visibility($playlist_id = 0)
{
    echo video_central_get_playlist_visibility($playlist_id);
}
    /**
     * Return the visibility of the video.
     *
     * @since 1.0.0
     *
     * @param int $forum_id Optional. Forum id
     *
     * @uses video_central_get_forum_id() To get the video id
     * @uses get_post_visibility() To get the video's visibility
     * @uses apply_filters() Calls 'video_central_get_forum_visibility' with the visibility
     *                        and video id
     *
     * @return string Status of video
     */
    function video_central_get_playlist_visibility($playlist_id = 0)
    {
        $playlist_id = video_central_get_playlist_id($playlist_id);

        return apply_filters(__FUNCTION__, get_post_status($playlist_id), $playlist_id);
    }

/**
 * Is the video trashed?
 *
 * @since 1.0.0
 *
 * @param int $playlist_id Optional. Playlist id
 *
 * @uses video_central_get_playlist_id() To get the video id
 * @uses video_central_get_playlist_status() To get the video status
 * @uses apply_filters() Calls 'video_central_is_playlist_trash' with the video id
 *
 * @return bool True if trashed, false if not.
 */
function video_central_is_playlist_trash($playlist_id = 0)
{
    $video_status = video_central_get_playlist_status(video_central_get_playlist_id($playlist_id)) === video_central_get_trash_status_id();

    return (bool) apply_filters(__FUNCTION__, (bool) $video_status, $playlist_id);
}

/**
 * Is the posted by an anonymous user?
 *
 * @since 1.0.0
 *
 * @param int $playlist_id Optional. Playlist id
 *
 * @uses video_central_get_playlist_id() To get the video id
 * @uses video_central_get_playlist_author_id() To get the video author id
 * @uses get_post_meta() To get the anonymous user name and email meta
 * @uses apply_filters() Calls 'video_central_is_playlist_anonymous' with the video id
 *
 * @return bool True if the post is by an anonymous user, false if not.
 */
function video_central_is_playlist_anonymous($playlist_id = 0)
{
    $playlist_id = video_central_get_playlist_id($playlist_id);
    $retval = false;

    if (!video_central_get_playlist_author_id($playlist_id)) {
        $retval = true;
    } elseif (get_post_meta($playlist_id, '_playlist_central_anonymous_name',  true)) {
        $retval = true;
    } elseif (get_post_meta($playlist_id, '_playlist_central_anonymous_email', true)) {
        $retval = true;
    }

    // The video is by an anonymous user
    return (bool) apply_filters(__FUNCTION__, $retval, $playlist_id);
}

/**
 * Is the video public?
 *
 * @since 1.0.0
 *
 * @param int  $playlist_id        Optional.  id
 * @param bool $check_ancestors Check if the ancestors are public (only if
 *                              they're a category)
 *
 * @uses get_post_meta() To get the video public meta
 * @uses video_central_get_playlist_ancestors() To get the video ancestors
 * @uses video_central_is_playlist_category() To check if the video is a category
 * @uses video_central_is_playlist_closed() To check if the video is closed
 *
 * @return bool True if closed, false if not
 */
function video_central_is_playlist_public($playlist_id = 0, $check_ancestors = true)
{
    $playlist_id = video_central_get_playlist_id($playlist_id);
    $visibility = video_central_get_playlist_visibility($playlist_id);

    // If post status is public, return true
    $retval = (video_central_get_public_status_id() === $visibility);

    // Check ancestors and inherit their privacy setting for display
    if (!empty($check_ancestors)) {
        $ancestors = video_central_get_playlist_ancestors($playlist_id);

        foreach ((array) $ancestors as $ancestor) {
            if (video_central_is_video($ancestor) && video_central_is_playlist_public($ancestor, false)) {
                $retval = true;
            }
        }
    }

    return (bool) apply_filters(__FUNCTION__, (bool) $retval, $playlist_id, $check_ancestors);
}

/**
 * Is the video private?
 *
 * @since 1.0.0
 *
 * @param int  $playlist_id        Optional.  id
 * @param bool $check_ancestors Check if the ancestors are private (only if
 *                              they're a category)
 *
 * @uses get_post_meta() To get the video private meta
 * @uses video_central_get_playlist_ancestors() To get the video ancestors
 * @uses video_central_is_playlist_closed() To check if the video is closed
 *
 * @return bool True if closed, false if not
 */
function video_central_is_playlist_private($playlist_id = 0, $check_ancestors = true)
{
    $playlist_id = video_central_get_playlist_id($playlist_id);
    $visibility = video_central_get_playlist_visibility($playlist_id);

    // If post status is private, return true
    $retval = (video_central_get_private_status_id() === $visibility);

    // Check ancestors and inherit their privacy setting for display
    if (!empty($check_ancestors)) {
        $ancestors = video_central_get_playlist_ancestors($playlist_id);

        foreach ((array) $ancestors as $ancestor) {
            if (video_central_is_video($ancestor) && video_central_is_playlist_private($ancestor, false)) {
                $retval = true;
            }
        }
    }

    return (bool) apply_filters(__FUNCTION__, (bool) $retval, $playlist_id, $check_ancestors);
}

/**
 * Is the video hidden?
 *
 * @since 1.0.0
 *
 * @param int  $playlist_id        Optional.  id
 * @param bool $check_ancestors Check if the ancestors are private (only if
 *                              they're a category)
 *
 * @uses get_post_meta() To get the video private meta
 * @uses video_central_get_playlist_ancestors() To get the video ancestors
 * @uses video_central_is_playlist_category() To check if the video is a category
 * @uses video_central_is_playlist_closed() To check if the video is closed
 *
 * @return bool True if closed, false if not
 */
function video_central_is_playlist_hidden($playlist_id = 0, $check_ancestors = true)
{
    $playlist_id = video_central_get_playlist_id($playlist_id);
    $visibility = video_central_get_playlist_visibility($playlist_id);

    // If post status is private, return true
    $retval = (video_central_get_hidden_status_id() === $visibility);

    // Check ancestors and inherit their privacy setting for display
    if (!empty($check_ancestors)) {
        $ancestors = video_central_get_playlist_ancestors($playlist_id);

        foreach ((array) $ancestors as $ancestor) {
            if (video_central_is_video($ancestor) && video_central_is_playlist_hidden($ancestor, false)) {
                $retval = true;
            }
        }
    }

    return (bool) apply_filters(__FUNCTION__, (bool) $retval, $playlist_id, $check_ancestors);
}

/**
 * Replace video meta details for users that cannot view them.
 *
 * @since 1.0.0
 *
 * @param string $retval
 * @param int    $playlist_id
 *
 * @uses video_central_is_playlist_private()
 * @uses current_user_can()
 *
 * @return string
 */
function video_central_suppress_private_playlist_meta($retval, $playlist_id)
{
    if (video_central_is_playlist_private($playlist_id, false) && !current_user_can('read_private_videos')) {
        $retval = '-';
    }

    return apply_filters(__FUNCTION__, $retval);
}

/**
 * Output the row class of a video.
 *
 * @since 1.0.0
 *
 * @param int $playlist_id Optional.  ID.
 * @param array Extra classes you can pass when calling this function
 *
 * @uses video_central_get_playlist_class() To get the row class of the video
 */
function video_central_playlists_class($playlist_id = 0, $classes = array())
{
    echo video_central_get_playlist_class($playlist_id, $classes);
}
    /**
     * Return the row class of a video.
     *
     * @since 1.0.0
     *
     * @param int $playlist_id Optional.  ID
     * @param array Extra classes you can pass when calling this function
     *
     * @uses video_central_get_playlist_id() To validate the video id
     * @uses video_central_is_playlist_category() To see if video is a category
     * @uses video_central_get_playlist_status() To get the video status
     * @uses video_central_get_playlist_visibility() To get the video visibility
     * @uses video_central_get_playlist_parent_id() To get the video parent id
     * @uses get_post_class() To get all the classes including ours
     * @uses apply_filters() Calls 'video_central_get_playlist_class' with the classes
     *
     * @return string Row class of the video
     */
    function video_central_get_playlist_class($playlist_id = 0, $classes = array())
    {
        $video_central = video_central();
        $playlist_id = video_central_get_playlist_id($playlist_id);
        $count = isset($video_central->video_query->current_post) ? $video_central->video_query->current_post : 1;
        $classes = (array) $classes;

        // Get some classes
        $classes[] = 'video-central-loop-item-'.$count;
        $classes[] = ((int) $count % 2) ? 'video-central-even' : 'video-central-odd';
        $classes[] = video_central_is_playlist_category($playlist_id)        ? 'video-central-status-category'   : '';
        $classes[] = video_central_get_playlist_subvideo_count($playlist_id) ? 'video-central-has-subvideos' : '';
        $classes[] = video_central_get_playlist_parent_id($playlist_id)      ? 'video-central-parent-video-'.video_central_get_playlist_parent_id($playlist_id) : '';
        $classes[] = 'video-central-video-status-'.video_central_get_playlist_status($playlist_id);
        $classes[] = 'video-central-video-visibility-'.video_central_get_playlist_visibility($playlist_id);

        // Ditch the empties
        $classes = array_filter($classes);
        $classes = get_post_class($classes, $playlist_id);

        // Filter the results
        $classes = apply_filters(__FUNCTION__, $classes, $playlist_id);
        $retval = 'class="'.implode(' ', $classes).'"';

        return apply_filters(__FUNCTION__, $retval);
    }



/** Video Playlist *****************************************************************/

/**
 * Output playlist id.
 *
 * @since 1.2.0
 *
 * @param $playlist_id Optional. Used to check emptiness
 *
 * @uses video_central_get_playlist_id() To get the playlist id
 */
function video_central_playlist_id($playlist_id = 0)
{
    echo video_central_get_playlist_id($playlist_id);
}
    /**
     * Return the playlist id.
     *
     * @since 1.2.0
     *
     * @param $playlist_id Optional. Used to check emptiness
     *
     * @uses apply_filters() Calls 'video_central_get_playlist_id' with the playlist id and
     *                        supplied playlist id
     *
     * @return int The playlist id
     */
    function video_central_get_playlist_id($playlist_id = 0)
    {
        $video_central = video_central();

        // Easy empty checking
        if (!empty($playlist_id) && is_numeric($playlist_id)) {
            $video_central_playlist_id = $playlist_id;

        // Fallback
        } else {
            $video_central_playlist_id = $playlist_id ? $playlist_id : $video_central->playlist_instance;
        }

        return (int) apply_filters( __FUNCTION__, (int) $video_central_playlist_id, $playlist_id );
    }

/**
 * Output the playlist playlist.
 *
 * @since 1.2.0
 *
 * @uses video_central_get_playlist() To get the playlist player
 */
function video_central_playlist( $post, $args = array())
{
    echo video_central_get_playlist( $post, $args );
}

/**
 * Output the title of the video.
 *
 * @since 1.0.0
 *
 * @param int $playlist_id Optional.  id
 *
 * @uses video_central_get_playlist_title() To get the video title
 */
function video_central_playlist_title($playlist_id = 0)
{
    echo video_central_get_playlist_title($playlist_id);
}
    /**
     * Return the title of the video.
     *
     * @since 1.0.0
     *
     * @param int $playlist_id Optional.  id
     *
     * @uses video_central_get_playlist_id() To get the video id
     * @uses get_the_title() To get the video title
     * @uses apply_filters() Calls 'video_central_get_playlist_title' with the title
     *
     * @return string Title of video
     */
    function video_central_get_playlist_title($playlist_id = 0)
    {
        $playlist_id = video_central_get_playlist_id($playlist_id);
        $title = get_the_title($playlist_id);

        return apply_filters(__FUNCTION__, $title, $playlist_id);
    }
    /**
     * Display a playlist.
     *
     * @since 1.0.0
     * @todo Add an arg to specify a template path that doesn't exist in the /video-central directory.
     *
     * @param mixed $post A post ID, WP_Post object or post slug.
     * @param array $args Playlist arguments.
     */
    function video_central_get_playlist( $post, $args = array() ) {
        if ( is_string( $post ) && ! is_numeric( $post ) ) {
            // Get a playlist by its slug.
            $post = get_page_by_path( $post, OBJECT, video_central_get_playlists_post_type() );
        } else {
            $post = get_post( $post );
        }

        if ( ! $post || video_central_get_playlists_post_type() !== get_post_type( $post ) ) {
            return;
        }

        $videos = video_central_get_playlist_videos( $post );

        if ( empty( $videos ) ) {
            return;
        }

        $args = wp_parse_args( $args, array(
            'container'     => true,
            'enqueue'       => true,
            'print_data'    => true,
            'show_playlist' => true,
            'player'        => '',
            'theme'         => get_playlists_central_default_theme(),
            'template'      => '',
        ) );

        if ( $args['enqueue'] ) {
            VideoCentral::enqueue_assets();
        }

        $template_names = array(
            "playlist-{$post->ID}.php",
            "playlist-{$post->post_name}.php",
            'playlist.php',
        );

        // Prepend custom templates.
        if ( ! empty( $args['template'] ) ) {
            $add_templates = array_filter( (array) $args['template'] );
            $template_names = array_merge( $add_templates, $template_names );
        }

        $template_loader = new VideoCentral_Template_Loader();
        $template = $template_loader->locate_template( $template_names );

        $themes = get_playlists_central_themes();
        if ( ! isset( $themes[ $args['theme'] ] ) ) {
            $args['theme'] = 'default';
        }

        $classes   = array( 'video-central-playlist-playlist' );
        $classes[] = $args['show_playlist'] ? '' : 'is-playlist-hidden';
        $classes[] = sprintf( 'video-central-playlist-theme-%s', sanitize_html_class( $args['theme'] ) );
        $classes   = implode( ' ', array_filter( $classes ) );

        if ( $args['container'] ) {
            echo '<div class="video-central-playlist-playlist-container">';
        }

        do_action( 'video_central_before_playlist', $post, $videos, $args );

        include( $template );

        do_action( 'video_central_after_playlist', $post, $videos, $args );

        if ( $args['container'] ) {
            echo '</div>';
        }
    }
    