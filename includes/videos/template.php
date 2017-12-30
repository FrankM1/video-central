<?php

/**
 * Video Central Video Template Tags.
 */

/** Post Type *****************************************************************/

/**
 * Output the unique id of the custom post type for videos.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_video_post_type() To get the video post type
 */
function video_central_videos_post_type()
{
    echo video_central_get_video_post_type();
}
    /**
     * Return the unique id of the custom post type for videos.
     *
     * @since 1.0.0
     *
     * @uses apply_filters() Calls 'video_central_get_video_post_type' with the video
     *                        post type id
     *
     * @return string The unique video post type id
     */
    function video_central_get_video_post_type()
    {
        return video_central()->video_post_type;
    }

/**
 * Return array of labels used by the video post type.
 *
 * @since 1.0.0
 *
 * @return array
 */
function video_central_get_video_post_type_labels()
{
    return apply_filters(__FUNCTION__, array(
        'name'               => __('Videos',                   'video_central'),
        'menu_name'          => __('Videos',                   'video_central'),
        'singular_name'      => __('Video',                    'video_central'),
        'all_items'          => __('All Videos',               'video_central'),
        'add_new'            => __('New Video',                'video_central'),
        'add_new_item'       => __('Create New Video',         'video_central'),
        'edit'               => __('Edit',                     'video_central'),
        'edit_item'          => __('Edit Video',               'video_central'),
        'new_item'           => __('New Video',                'video_central'),
        'view'               => __('View Video',               'video_central'),
        'view_item'          => __('View Video',               'video_central'),
        'search_items'       => __('Search Videos',            'video_central'),
        'not_found'          => __('No videos found',          'video_central'),
        'not_found_in_trash' => __('No videos found in Trash', 'video_central'),
        'parent_item_colon'  => __('Parent Video:',            'video_central'),
    ));
}

/**
 * Return array of labels used by the category taxonomy.
 *
 * @since 1.0.0
 *
 * @return array
 */
function video_central_get_video_category_tax_labels()
{
    return apply_filters(__FUNCTION__, array(
            'name'                       => __('Video Categories', 'video_central'),
            'singular_name'              => __('Video Category', 'video_central'),
            'search_items'               => __('Search Video Categories', 'video_central'),
            'popular_items'              => __('Popular Video Categories', 'video_central'),
            'all_items'                  => __('All Video Categories', 'video_central'),
            'parent_item'                => __('Parent Video Category', 'video_central'),
            'parent_item_colon'          => __('Parent Video Category:', 'video_central'),
            'edit_item'                  => __('Edit Video Category', 'video_central'),
            'update_item'                => __('Update Video Category', 'video_central'),
            'add_new_item'               => __('Add New Video Category', 'video_central'),
            'new_item_name'              => __('New Video Category Name', 'video_central'),
            'separate_items_with_commas' => __('Separate video categories with commas', 'video_central'),
            'add_or_remove_items'        => __('Add or remove video categories', 'video_central'),
            'choose_from_most_used'      => __('Choose from the most used video categories', 'video_central'),
            'menu_name'                  => __('Video Categories', 'video_central'),
   ));
}

/**
 * Return array of labels used by tag taxonomy.
 *
 * @since 1.0.0
 *
 * @return array
 */
function video_central_get_video_tag_tax_labels()
{
    return apply_filters(__FUNCTION__, array(
        'name'                       => __('Video Tags', 'video_central'),
        'singular_name'              => __('Video Tag', 'video_central'),
        'search_items'               => __('Search Video Tags', 'video_central'),
        'popular_items'              => __('Popular Video Tags', 'video_central'),
        'all_items'                  => __('All Video Tags', 'video_central'),
        'parent_item'                => __('Parent Video Tag', 'video_central'),
        'parent_item_colon'          => __('Parent Video Tag:', 'video_central'),
        'edit_item'                  => __('Edit Video Tag', 'video_central'),
        'update_item'                => __('Update Video Tag', 'video_central'),
        'add_new_item'               => __('Add New Video Tag', 'video_central'),
        'new_item_name'              => __('New Video Tag Name', 'video_central'),
        'separate_items_with_commas' => __('Separate video tags with commas', 'video_central'),
        'add_or_remove_items'        => __('Add or remove video tags', 'video_central'),
        'choose_from_most_used'      => __('Choose from the most used video tags', 'video_central'),
        'menu_name'                  => __('Video Tags', 'video_central'),
    ));
}

/** Rewrite *********************************************************************/

/**
 * Return array of video post type rewrite settings.
 *
 * @since 1.0.0
 *
 * @return array
 */
function video_central_get_video_post_type_rewrite()
{
    return apply_filters(__FUNCTION__, array(
        'slug' => video_central_get_video_slug(),
        'with_front' => false,
    ));
}

/**
 * Return array of video post type tag rewrite settings.
 *
 * @since 1.0.0
 *
 * @return array
 */
function video_central_get_video_tag_tax_rewrite()
{
    return apply_filters(__FUNCTION__, array(
        'slug' => video_central_get_video_tag_tax_slug(),
        'with_front' => false,
    ));
}

/**
 * Return array of video post type category rewrite settings.
 *
 * @since 1.0.0
 *
 * @return array
 */
function video_central_get_video_category_tax_rewrite()
{
    return apply_filters(__FUNCTION__, array(
        'slug' => video_central_get_video_category_tax_slug(),
        'with_front' => false,
    ));
}

/**
 * Return array of features the video post type supports.
 *
 * @since 1.0.0
 *
 * @return array
 */
function video_central_get_video_post_type_supports()
{
    return apply_filters(__FUNCTION__, array(
        'title',
        'revisions',
        'comments',
    ));
}

/** Videos Loop ****************************************************************/

/**
 * The main video loop.
 *
 * WordPress makes this easy for us.
 *
 * @since 1.0.0
 *
 * @param mixed $args All the arguments supported by {@link WP_Query}
 *
 * @uses WP_Query To make query and get the videos
 * @uses video_central_get_video_post_type() To get the video post type id
 * @uses video_central_get_video_id() To get the video id
 * @uses get_option() To get the videos per page option
 * @uses current_user_can() To check if the current user is capable of editing
 *                           others' videos
 * @uses apply_filters() Calls 'video_central_has_videos' with
 *                        video_central::video_query::have_posts()
 *                        and video_central::video_query
 *
 * @return object Multidimensional array of video information
 */
function video_central_has_videos($args = '')
{
    global $wp_rewrite;

    /* Defaults **************************************************************/

    // Other defaults
    $default_video_search = !empty($_REQUEST['vs']) ? $_REQUEST['vs'] : false;
    $default_post_parent = video_central_is_single_video() ? video_central_get_video_id() : 'any';

    // Default argument array
    $default = array(
        'post_type' => video_central_get_video_post_type(), // Narrow query down to videos
        'post_parent' => $default_post_parent,      //  ID
        'orderby' => 'meta_value',              // 'meta_value', 'author', 'date', 'title', 'modified', 'parent', rand',
        'order' => 'DESC',                    // 'ASC', 'DESC'
        'posts_per_page' => video_central_get_videos_per_page(), // Videos per page
        'paged' => video_central_get_paged(),           // Page Number
        's' => $default_video_search,     // Video Search
        'max_num_pages' => false,                     // Maximum number of pages to show
    );

    // Maybe query for video tags
    if (video_central_is_video_tag()) {
        $default['term'] = video_central_get_video_tag_slug();
        $default['taxonomy'] = video_central_get_video_tag_tax_id();
    }

    // Maybe query for video category
    if (video_central_is_video_category()) {
        $default['term'] = video_central_get_video_category_slug();
        $default['taxonomy'] = video_central_get_video_category_tax_id();
    }

    /* Setup *****************************************************************/

    // Parse arguments against default values
    $r = video_central_parse_args($args, $default, 'has_videos');

    // get the video_central instance
    $video_central = video_central();

    // Call the query
    $video_central->video_query = new WP_Query($r);

    // Set post_parent back to 0 if originally set to 'any'
    if ('any' === $r['post_parent']) {
        $r['post_parent'] = 0;
    }

    // Limited the number of pages shown
    if (!empty($r['max_num_pages'])) {
        $video_central->video_query->max_num_pages = $r['max_num_pages'];
    }

    // If no limit to posts per page, set it to the current post_count
    if (-1 === $r['posts_per_page']) {
        $r['posts_per_page'] = $video_central->video_query->post_count;
    }

    // Add pagination values to query object
    $video_central->video_query->posts_per_page = $r['posts_per_page'];
    $video_central->video_query->paged = $r['paged'];

    // Video archive only shows root
    if (video_central_is_video_archive()) {
        $default_post_parent = 0;

    // Could be anything, so look for possible parent ID
    } else {
        $default_post_parent = video_central_get_video_id();
    }

    // Only add pagination if query returned results
    if (((int) $video_central->video_query->post_count || (int) $video_central->video_query->found_posts) && (int) $video_central->video_query->posts_per_page) {

        // Limit the number of videos shown based on maximum allowed pages
        if ((!empty($r['max_num_pages'])) && $video_central->video_query->found_posts > $video_central->video_query->max_num_pages * $video_central->video_query->post_count) {
            $video_central->video_query->found_posts = $video_central->video_query->max_num_pages * $video_central->video_query->post_count;
        }

        // If pretty permalinks are enabled, make our pagination pretty
        if ($wp_rewrite->using_permalinks()) {
            if (video_central_is_video_tag()) {
                $base = video_central_get_video_tag_link();

            // Page or single post
            } elseif (video_central_is_video_category()) {
                $base = video_central_get_video_category_link();

            // Page or single post
            } elseif (is_page() || is_single()) {
                $base = get_permalink();

            // View
            } elseif (video_central_is_single_view()) {
                $base = video_central_get_view_url();

            // video archive
            } elseif (video_central_is_video_archive()) {
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
            'total' => $r['posts_per_page'] === $video_central->video_query->found_posts ? 1 : ceil((int) $video_central->video_query->found_posts / (int) $r['posts_per_page']),
            'current' => (int) $video_central->video_query->paged,
            'prev_text' => is_rtl() ? '&rarr;' : '&larr;',
            'next_text' => is_rtl() ? '&larr;' : '&rarr;',
            'mid_size' => 8,
            'end_size' => 1,
        ));

        // Add pagination to query object
        $video_central->video_query->pagination_links = apply_filters('video_central_pagination_links', paginate_links($video_central_pagination), $video_central->video_query, $base, $r);

        // Remove first page from pagination
        $video_central->video_query->pagination_links = str_replace($wp_rewrite->pagination_base."/1/'", "'", $video_central->video_query->pagination_links);
    }

    return apply_filters(__FUNCTION__, $video_central->video_query->have_posts(), $video_central->video_query);
}

/**
 * Whether there are more videos available in the loop.
 *
 * @since 1.0.0
 *
 * @uses video_central:video_query::have_posts() To check if there are more videos
 *                                          available
 *
 * @return object Video information
 */
function video_central_videos()
{

    // Put into variable to check against next
    $have_posts = video_central()->video_query->have_posts();

    // Reset the post data when finished
    if (empty($have_posts)) {
        wp_reset_postdata();
    }

    return $have_posts;
}

/**
 * Loads up the current video in the loop.
 *
 * @since 1.0.0
 *
 * @uses video_central::video_query::the_post() To get the current video
 *
 * @return object information
 */
function video_central_the_video()
{
    return video_central()->video_query->the_post();
}

/** Videos *********************************************************************/

/**
 * Output video id.
 *
 * @since 1.0.0
 *
 * @param $video_id Optional. Used to check emptiness
 *
 * @uses video_central_get_video_id() To get the video id
 */
function video_central_video_id($video_id = 0)
{
    echo video_central_get_video_id($video_id);
}
    /**
     * Return the video id.
     *
     * @since 1.0.0
     *
     * @param $video_id Optional. Used to check emptiness
     *
     * @uses video_central::video_query::in_the_loop To check if we're in the loop
     * @uses video_central::video_query::post::ID To get the video id
     * @uses WP_Query::post::ID To get the video id
     * @uses video_central_is_video() To check if the search result is a video
     * @uses video_central_is_single_video() To check if it's a video page
     * @uses video_central_get_video_id() To get the video id
     * @uses get_post_field() To get the post's post type
     * @uses apply_filters() Calls 'video_central_get_video_id' with the video id and
     *                        supplied video id
     *
     * @return int The video id
     */
    function video_central_get_video_id($video_id = 0)
    {
        global $wp_query;

        $video_central = video_central();

        // Easy empty checking
        if (!empty($video_id) && is_numeric($video_id)) {
            $video_central_video_id = $video_id;

        // Currently inside a video loop
        } elseif (!empty($video_central->video_query->in_the_loop) && isset($video_central->video_query->post->ID)) {
            $video_central_video_id = $video_central->video_query->post->ID;

        // Currently inside a search loop
        } elseif (!empty($video_central->search_query->in_the_loop) && isset($video_central->search_query->post->ID) && video_central_is_video($video_central->search_query->post->ID)) {
            $video_central_video_id = $video_central->search_query->post->ID;

        // Currently inside a related videos loop
        } elseif (!empty($video_central->related_video_query->in_the_loop) && isset($video_central->related_video_query->post->ID) && video_central_is_video($video_central->related_video_query->post->ID)) {
            $video_central_video_id = $video_central->related_video_query->post->ID;

        // Currently viewing a video
        } elseif ((video_central_is_single_video()) && !empty($video_central->current_video_id)) {
            $video_central_video_id = $video_central->current_video_id;

        // Currently viewing a video
        } elseif ((video_central_is_single_video()) && isset($wp_query->post->ID)) {
            $video_central_video_id = $wp_query->post->ID;

        // Fallback
        } else {
            $video_central_video_id = $video_id ? $video_id : 0;
        }

        return (int) apply_filters(__FUNCTION__, (int) $video_central_video_id, $video_id);
    }

/**
 * Gets a video.
 *
 * @since 1.0.0
 *
 * @param int|object $video  video id or video object
 * @param string     $output Optional. OBJECT, ARRAY_A, or ARRAY_N. Default = OBJECT
 * @param string     $filter Optional Sanitation filter. See {@link sanitize_post()}
 *
 * @uses get_post() To get the video
 * @uses apply_filters() Calls 'video_central_get_video' with the video, output type and
 *                        sanitation filter
 *
 * @return mixed Null if error or video (in specified form) if success
 */
function video_central_get_video($video, $output = OBJECT, $filter = 'raw')
{

    // Use video ID
    if (empty($video) || is_numeric($video)) {
        $video = video_central_get_video_id($video);
    }

    // Attempt to load the video
    $video = get_post($video, OBJECT, $filter);
    if (empty($video)) {
        return $video;
    }

    // Bail if post_type is not a video
    if ($video->post_type !== video_central_get_video_post_type()) {
        return;
    }

    // Tweak the data type to return
    if ($output === OBJECT) {
        return $video;
    } elseif ($output === ARRAY_A) {
        $_video = get_object_vars($video);

        return $_video;
    } elseif ($output === ARRAY_N) {
        $_video = array_values(get_object_vars($video));

        return $_video;
    }

    return apply_filters(__FUNCTION__, $video, $output, $filter);
}

/**
 * Output the link to the video.
 *
 * @since 1.0.0
 *
 * @param int $video_id Optional.  id
 *
 * @uses video_central_get_video_permalink() To get the permalink
 */
function video_central_video_permalink($video_id = 0)
{
    echo esc_url( video_central_get_video_permalink( $video_id ) );
}
    /**
     * Return the link to the video.
     *
     * @since 1.0.0
     *
     * @param int $video_id Optional.  id
     * @param $string $redirect_to Optional. Pass a redirect value for use with
     *                              shortcodes and other fun things.
     *
     * @uses video_central_get_video_id() To get the video id
     * @uses get_permalink() Get the permalink of the video
     * @uses apply_filters() Calls 'video_central_get_video_permalink' with the video
     *                        link
     *
     * @return string Permanent link to video
     */
    function video_central_get_video_permalink($video_id = 0, $redirect_to = '')
    {
        $video_id = video_central_get_video_id($video_id);

        // Use the redirect address
        if (!empty($redirect_to)) {
            $video_permalink = esc_url_raw($redirect_to);

        // Use the video permalink
        } else {
            $video_permalink = get_permalink($video_id);
        }

        return apply_filters(__FUNCTION__, $video_permalink, $video_id);
    }

/**
 * Output the title of the video.
 *
 * @since 1.0.0
 *
 * @param int $video_id Optional.  id
 *
 * @uses video_central_get_video_title() To get the video title
 */
function video_central_video_title($video_id = 0)
{
    echo video_central_get_video_title($video_id);
}
    /**
     * Return the title of the video.
     *
     * @since 1.0.0
     *
     * @param int $video_id Optional.  id
     *
     * @uses video_central_get_video_id() To get the video id
     * @uses get_the_title() To get the video title
     * @uses apply_filters() Calls 'video_central_get_video_title' with the title
     *
     * @return string Title of video
     */
    function video_central_get_video_title($video_id = 0)
    {
        $video_id = video_central_get_video_id($video_id);
        $title = get_the_title($video_id);

        return apply_filters(__FUNCTION__, $title, $video_id);
    }

/**
 * Output a short title of the video.
 *
 * @since 1.0.0
 *
 * @param int $video_id Optional.  id
 *
 * @uses video_central_get_video_title() To get the video title
 */
function video_central_video_short_title($video_id = 0)
{
    echo video_central_get_video_short_title($video_id);
}
    /**
     * Return the title of the video.
     *
     * @since 1.0.0
     *
     * @param int $video_id Optional.  id
     *
     * @uses video_central_get_video_id() To get the video id
     * @uses get_the_title() To get the video title
     * @uses apply_filters() Calls 'video_central_get_video_title' with the title
     *
     * @return string Title of video
     */
    function video_central_get_video_short_title($video_id = 0)
    {
        $video_id = video_central_get_video_id($video_id);

        $title = video_central_get_video_title($video_id);

        $title_length = video_central_short_title_length();

        $append = apply_filters('video_central_get_video_short_title_append', '&hellip;', $video_id);

        $title = video_central_truncate_text($title, $title_length, $append);

        return apply_filters(__FUNCTION__, $title, $video_id);
    }

/**
 * Output the video archive title.
 *
 * @since 1.0.0
 *
 * @param string $title Default text to use as title
 */
function video_central_archive_title($title = '')
{
    echo video_central_get_archive_title($title);
}
    /**
     * Return the video archive title.
     *
     * @since 1.0.0
     *
     * @param string $title Default text to use as title
     *
     * @uses video_central_get_page_by_path() Check if page exists at root path
     * @uses get_the_title() Use the page title at the root path
     * @uses get_post_type_object() Load the post type object
     * @uses video_central_get_video_post_type() Get the video post type ID
     * @uses video_central_is_video_archive() Determine if on archive page
     * @uses get_post_type_labels() Get labels for video post type
     * @uses apply_filters() Allow output to be manipulated
     *
     * @return string The video archive title
     */
    function video_central_get_archive_title($title = '')
    {

        // If no title was passed
        if (empty($title)) {
            if (video_central_is_video_archive()) {
                if (video_central_is_video_category()) {
                    $title = __('Video Category: ', 'video_central').video_central_get_video_category_name();
                } elseif (video_central_is_video_tag()) {
                    $title = __('Video Tag: ', 'video_central').video_central_get_video_tag_name();
                }
            } else {

                // Set root text to page title
                $page = video_central_get_page_by_path(video_central_get_root_slug());

                if (!empty($page)) {
                    $title = get_the_title($page->ID);

                // Default to video post type name label
                } else {
                    $fto = get_post_type_object(video_central_get_video_post_type());
                    $title = $fto->labels->name;
                }
            }
        }

        return apply_filters(__FUNCTION__, $title);
    }

/**
 * Output the content of the video.
 *
 * @since 1.0.0
 *
 * @param int $video_id Optional. Video id
 *
 * @uses video_central_get_content() To get the video content
 */
function video_central_content($video_id = 0)
{
    echo video_central_get_content($video_id);
}
    /**
     * Return the content of the video.
     *
     * @since 1.0.0
     *
     * @param int $video_id Optional. Video id
     *
     * @uses video_central_get_video_id() To get the video id
     * @uses post_password_required() To check if the video requires pass
     * @uses get_the_password_form() To get the password form
     * @uses get_post_field() To get the content post field
     * @uses apply_filters() Calls 'video_central_get_content' with the content
     *                        and video id
     *
     * @return string Content of the video
     */
    function video_central_get_content($video_id = 0)
    {
        $video_id = video_central_get_video_id($video_id);

        // Check if password is required
        if (post_password_required($video_id)) {
            return get_the_password_form();
        }

        $content = get_post_meta($video_id, '_video_central_description', true);
        $content = apply_filters('the_content', $content);

        $args = array();

        $content = apply_filters('video_central_get_content', $content, $args);

        return apply_filters(__FUNCTION__, $content, $video_id);
    }

/**
 * Output the excerpt of the video.
 *
 * @since 1.1.3
 *
 * @param int $video_id Optional. Video id
 *
 * @uses video_central_get_excerpt() To get the video content
 */
function video_central_excerpt($video_id = 0, $excerpt_length = '')
{
    echo video_central_get_excerpt($video_id, $excerpt_length);
}
    /**
     * Return the content of the video.
     *
     * @since 1.1.3
     *
     * @param int $video_id Optional. Video id
     *
     * @uses video_central_get_video_id() To get the video id
     * @uses post_password_required() To check if the video requires pass
     * @uses get_the_password_form() To get the password form
     * @uses get_post_field() To get the content post field
     * @uses apply_filters() Calls 'video_central_get_excerpt' with the content
     *                        and video id
     *
     * @return string Content of the video
     */
    function video_central_get_excerpt($video_id = 0, $excerpt_length = 55)
    {
        $video_id = video_central_get_video_id($video_id);
        $excerpt_length = apply_filters('video_central_excerpt_length', $excerpt_length);

        // Check if password is required
        if (post_password_required($video_id)) {
            return get_the_password_form();
        }

        $content = get_post_meta($video_id, '_video_central_description', true);

        $args = array();

        /*
         * Filter the string in the "more" link displayed after a trimmed excerpt.
         *
         * @param string $more_string The string shown within the more link.
         */
        $excerpt_more = apply_filters('video_central_excerpt_more', ' '.'[&hellip;]');
        $content = wp_trim_words($content, $excerpt_length, $excerpt_more);

        $content = apply_filters('video_central_get_excerpt_content', $content, $args);

        return apply_filters(__FUNCTION__, $content, $video_id);
    }

/**
 * Output the post date and time of a video.
 *
 * @since 1.0.0
 *
 * @param int  $video_id Optional. Video id.
 * @param bool $humanize Optional. Humanize output using time_since
 * @param bool $gmt      Optional. Use GMT
 *
 * @uses video_central_get_video_date_added() to get the output
 */
function video_central_video_date_added($video_id = 0, $humanize = false, $gmt = false)
{
    echo video_central_get_video_date_added($video_id, $humanize, $gmt);
}
    /**
     * Return the post date and time of a video.
     *
     * @since 1.0.0
     *
     * @param int  $video_id Optional. Video id.
     * @param bool $humanize Optional. Humanize output using time_since
     * @param bool $gmt      Optional. Use GMT
     *
     * @uses video_central_get_video_id() To get the video id
     * @uses get_post_time() to get the video post time
     * @uses video_central_get_time_since() to maybe humanize the video post time
     *
     * @return string
     */
    function video_central_get_video_date_added($video_id = 0, $humanize = false, $gmt = false)
    {
        $video_id = video_central_get_video_id($video_id);

        // 4 days, 4 hours ago
        if (!empty($humanize)) {
            $gmt_s = !empty($gmt) ? 'U' : 'G';
            $date = get_post_time($gmt_s, $gmt, $video_id);
            $time = false; // For filter below
            $result = video_central_get_time_since($date);

        // August 4, 2012 at 2:37 pm
        } else {
            $date = get_post_time(get_option('date_format'), $gmt, $video_id, true);
            $time = get_post_time(get_option('time_format'), $gmt, $video_id, true);
            $result = sprintf(_x('%1$s at %2$s', 'date at time', 'video_central'), $date, $time);
        }

        return apply_filters(__FUNCTION__, $result, $video_id, $humanize, $gmt, $date, $time);
    }

/**
 * Output the videos last time video was edited.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_video_date_updated() To get the video's last active id
 *
 * @param int $video_id Optional.  id
 */
function video_central_video_date_updated($video_id = 0)
{
    echo video_central_get_video_date_updated($video_id);
}
    /**
     * Return the videos last active ID.
     *
     * @since 1.0.0
     *
     * @param int $video_id Optional.  id
     *
     * @uses video_central_get_video_id() To get the video id
     * @uses get_the_time() To get the video's added date
     * @uses apply_filters() Calls 'video_central_get_video_date_added'
     *
     * @return int 's last active id
     */
    function video_central_get_video_date_updated($video_id = 0)
    {
        $video_id = video_central_get_video_id($video_id);

        $output = the_modified_date();

        return (int) apply_filters(__FUNCTION__, (int) $output);
    }

/**
 * Output a list of videos (can be used to list subvideos).
 *
 * @param mixed $args The function supports these args:
 *                    - before: To put before the output. Defaults to '<ul class="video-central-videos">'
 *                    - after: To put after the output. Defaults to '</ul>'
 *                    - link_before: To put before every link. Defaults to '<li class="video-central-video">'
 *                    - link_after: To put after every link. Defaults to '</li>'
 *                    - separator: Separator. Defaults to ', '
 *                    - video_id:  id. Defaults to ''
 *                    - show_video_count - To show video video count or not. Defaults to true
 *
 * @uses video_central_videos_get_subvideos() To check if the video has subvideos or not
 * @uses video_central_get_video_permalink() To get video permalink
 * @uses video_central_get_video_title() To get video title
 * @uses video_central_is_video_category() To check if a video is a category
 * @uses video_central_get_video_video_count() To get video video count
 */
function video_central_list_videos($args = '')
{

    // Define used variables
    $output = $sub_videos = $counts = '';
    $i = 0;
    $count = array();

    // Parse arguments against default values
    $r = video_central_parse_args($args, array(
        'before' => '<ul class="video-central-videos-list">',
        'after' => '</ul>',
        'link_before' => '<li class="video-central-video">',
        'link_after' => '</li>',
        'count_before' => ' (',
        'count_after' => ')',
        'count_sep' => ', ',
        'separator' => ', ',
        'video_id' => '',
        'show_video_count' => true,
    ), 'list_videos');

    // Loop through videos and create a list
    $sub_videos = video_central_videos_get_subvideos($r['video_id']);
    if (!empty($sub_videos)) {

        // Total count (for separator)
        $total_subs = count($sub_videos);
        foreach ($sub_videos as $sub_video) {
            ++$i; // Separator count

            // Get video details
            $count = array();
            $show_sep = $total_subs > $i ? $r['separator'] : '';
            $permalink = video_central_get_video_permalink($sub_video->ID);
            $title = video_central_get_video_title($sub_video->ID);

            // Show video count
            if (!empty($r['show_video_count']) && !video_central_is_video_category($sub_video->ID)) {
                $count['video'] = video_central_get_video_video_count($sub_video->ID);
            }

            // Counts to show
            if (!empty($count)) {
                $counts = $r['count_before'].implode($r['count_sep'], $count).$r['count_after'];
            }

            // Build this sub videos link
            $output .= $r['link_before'].'<a href="'.esc_url($permalink).'" class="video-central-video-link">'.$title.$counts.'</a>'.$show_sep.$r['link_after'];
        }

        // Output the list
        echo apply_filters(__FUNCTION__, $r['before'].$output.$r['after'], $r);
    }
}

/**  Last Video **********************************************************/

/**
 * Output the type of the video.
 *
 * @since 1.0.0
 *
 * @param int $video_id Optional.  id
 *
 * @uses video_central_get_video_type() To get the video type
 */
function video_central_videos_type($video_id = 0)
{
    echo video_central_get_video_type($video_id);
}
    /**
     * Return the type of video (category/video/etc...).
     *
     * @since 1.0.0
     *
     * @param int $video_id Optional.  id
     *
     * @uses get_post_meta() To get the video category meta
     *
     * @return bool Whether the video is a category or not
     */
    function video_central_get_video_type($video_id = 0)
    {
        $video_id = video_central_get_video_id($video_id);
        $retval = get_post_meta($video_id, '_video_central_videos_type', true);
        if (empty($retval)) {
            $retval = 'video';
        }

        return apply_filters(__FUNCTION__, $retval, $video_id);
    }

/**
 * Output the source of the video.
 *
 * @since 1.0.0
 *
 * @param int $video_id Optional.  id
 *
 * @uses video_central_get_video_source() To get the video source
 */
function video_central_video_source($video_id = 0)
{
    echo video_central_get_video_source($video_id);
}
    /**
     * Return the type of video (vimeo/youtube/etc...).
     *
     * @since 1.0.0
     *
     * @param int $video_id Optional.  id
     *
     * @uses get_post_meta() To get the video category meta
     *
     * @return string Where the video is from
     */
    function video_central_get_video_source($video_id = 0)
    {
        $video_id = video_central_get_video_id($video_id);
        $retval = get_post_meta($video_id, '_video_central_source', true);
        if ( $retval !== 'self' ) {
            $retval = 'embed';
        }

        return apply_filters( __FUNCTION__, $retval, $video_id);
    }

    
/** Video Tags ****************************************************************/

/**
 * Output the unique id of the video tag taxonomy.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_video_tag_tax_id() To get the video tag id
 */
function video_central_tag_tax_id()
{
    echo video_central_get_video_tag_tax_id();
}
    /**
     * Return the unique id of the video tag taxonomy.
     *
     * @since 1.0.0
     *
     * @uses apply_filters() Calls 'video_central_get_video_tag_tax_id' with the video tax id
     *
     * @return string The unique video tag taxonomy
     */
    function video_central_get_video_tag_tax_id()
    {
        return apply_filters(__FUNCTION__, video_central()->video_tag_tax_id);
    }

/**
 * Output the name of the current tag.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_video_tag_name()
 */
function video_central_video_tag_name($tag = '')
{
    echo video_central_get_video_tag_name($tag);
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
    function video_central_get_video_tag_name($tag = '')
    {

        // Get the term
        if (!empty($tag)) {
            $term = get_term_by('slug', $tag, video_central_get_video_tag_tax_id());
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

        return apply_filters('video_central_get_video_tag_name', $retval);
    }

/**
 * Output the slug of the current tag.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_video_tag_slug()
 */
function video_central_video_tag_slug($tag = '')
{
    echo video_central_get_video_tag_slug($tag);
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
    function video_central_get_video_tag_slug($tag = '')
    {

        // Get the term
        if (!empty($tag)) {
            $term = get_term_by('slug', $tag, video_central_get_video_tag_tax_id());
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

        return apply_filters('video_central_get_video_tag_slug', $retval);
    }

/**
 * Output the link of the current tag.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_video_tag_link()
 */
function video_central_video_tag_link($tag = '')
{
    echo esc_url(video_central_get_video_tag_link($tag));
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
    function video_central_get_video_tag_link($tag = '')
    {

        // Get the term
        if (!empty($tag)) {
            $term = get_term_by('slug', $tag, video_central_get_video_tag_tax_id());
        } else {
            $tag = get_query_var('term');
            $term = get_queried_object();
        }

        // Add before and after if description exists
        if (!empty($term->term_id)) {
            $retval = get_term_link($term, video_central_get_video_tag_tax_id());

        // No link
        } else {
            $retval = '';
        }

        return apply_filters('video_central_get_video_tag_link', $retval, $tag);
    }

/** Video Categories ****************************************************************/

/**
 * Output the unique id of the video category taxonomy.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_video_category_tax_id() To get the video category id
 */
function video_central_video_category_tax_id()
{
    echo video_central_get_video_category_tax_id();
}
    /**
     * Return the unique id of the video category taxonomy.
     *
     * @since 1.0.0
     *
     * @uses apply_filters() Calls 'video_central_get_video_cat_tax_id' with the video tax id
     *
     * @return string The unique video category taxonomy
     */
    function video_central_get_video_category_tax_id()
    {
        return apply_filters(__FUNCTION__, video_central()->video_cat_tax_id);
    }
/**
 * Output the name of the current category.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_video_tcategory_name()
 */
function video_central_video_category_name($category = '')
{
    echo video_central_get_video_category_name($category);
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
    function video_central_get_video_category_name($category = '')
    {

        // Get the term
        if (!empty($category)) {
            $term = get_term_by('slug', $category, video_central_get_video_category_tax_id());
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
 * @uses video_central_get_video_category_slug()
 */
function video_central_video_category_slug($category = '')
{
    echo video_central_get_video_category_slug($category);
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
    function video_central_get_video_category_slug($category = '')
    {

        // Get the term
        if (!empty($category)) {
            $term = get_term_by('slug', $category, video_central_get_video_category_tax_id());
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
 * @uses video_central_get_video_category_link()
 */
function video_central_video_category_link($category = '')
{
    echo esc_url(video_central_get_video_category_link($category));
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
    function video_central_get_video_category_link($category = '')
    {

        // Get the term
        if (!empty($category)) {
            $term = get_term_by('slug', $category, video_central_get_video_category_tax_id());
        } else {
            $category = get_query_var('term');
            $term = get_queried_object();
        }

        // Add before and after if description exists
        if (!empty($term->term_id)) {
            $retval = get_term_link($term, video_central_get_video_category_tax_id());

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
 * @param int $video_id Optional. video id
 *
 * @uses video_central_get_video_visibility() To get the video visibility
 */
function video_central_video_visibility($video_id = 0)
{
    echo video_central_get_video_visibility($video_id);
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
    function video_central_get_video_visibility($video_id = 0)
    {
        $video_id = video_central_get_video_id($video_id);

        return apply_filters(__FUNCTION__, get_post_status($video_id), $video_id);
    }

/**
 * Is the video trashed?
 *
 * @since 1.0.0
 *
 * @param int $video_id Optional. Video id
 *
 * @uses video_central_get_video_id() To get the video id
 * @uses video_central_get_video_status() To get the video status
 * @uses apply_filters() Calls 'video_central_is_video_trash' with the video id
 *
 * @return bool True if trashed, false if not.
 */
function video_central_is_video_trash($video_id = 0)
{
    $video_status = video_central_get_video_status(video_central_get_video_id($video_id)) === video_central_get_trash_status_id();

    return (bool) apply_filters(__FUNCTION__, (bool) $video_status, $video_id);
}

/**
 * Is the posted by an anonymous user?
 *
 * @since 1.0.0
 *
 * @param int $video_id Optional. Video id
 *
 * @uses video_central_get_video_id() To get the video id
 * @uses video_central_get_video_author_id() To get the video author id
 * @uses get_post_meta() To get the anonymous user name and email meta
 * @uses apply_filters() Calls 'video_central_is_video_anonymous' with the video id
 *
 * @return bool True if the post is by an anonymous user, false if not.
 */
function video_central_is_video_anonymous($video_id = 0)
{
    $video_id = video_central_get_video_id($video_id);
    $retval = false;

    if (!video_central_get_video_author_id($video_id)) {
        $retval = true;
    } elseif (get_post_meta($video_id, '_video_central_anonymous_name',  true)) {
        $retval = true;
    } elseif (get_post_meta($video_id, '_video_central_anonymous_email', true)) {
        $retval = true;
    }

    // The video is by an anonymous user
    return (bool) apply_filters(__FUNCTION__, $retval, $video_id);
}

/**
 * Is the video public?
 *
 * @since 1.0.0
 *
 * @param int  $video_id        Optional.  id
 * @param bool $check_ancestors Check if the ancestors are public (only if
 *                              they're a category)
 *
 * @uses get_post_meta() To get the video public meta
 * @uses video_central_get_video_ancestors() To get the video ancestors
 * @uses video_central_is_video_category() To check if the video is a category
 * @uses video_central_is_video_closed() To check if the video is closed
 *
 * @return bool True if closed, false if not
 */
function video_central_is_video_public($video_id = 0, $check_ancestors = true)
{
    $video_id = video_central_get_video_id($video_id);
    $visibility = video_central_get_video_visibility($video_id);

    // If post status is public, return true
    $retval = (video_central_get_public_status_id() === $visibility);

    // Check ancestors and inherit their privacy setting for display
    if (!empty($check_ancestors)) {
        $ancestors = video_central_get_video_ancestors($video_id);

        foreach ((array) $ancestors as $ancestor) {
            if (video_central_is_video($ancestor) && video_central_is_video_public($ancestor, false)) {
                $retval = true;
            }
        }
    }

    return (bool) apply_filters(__FUNCTION__, (bool) $retval, $video_id, $check_ancestors);
}

/**
 * Is the video private?
 *
 * @since 1.0.0
 *
 * @param int  $video_id        Optional.  id
 * @param bool $check_ancestors Check if the ancestors are private (only if
 *                              they're a category)
 *
 * @uses get_post_meta() To get the video private meta
 * @uses video_central_get_video_ancestors() To get the video ancestors
 * @uses video_central_is_video_closed() To check if the video is closed
 *
 * @return bool True if closed, false if not
 */
function video_central_is_video_private($video_id = 0, $check_ancestors = true)
{
    $video_id = video_central_get_video_id($video_id);
    $visibility = video_central_get_video_visibility($video_id);

    // If post status is private, return true
    $retval = (video_central_get_private_status_id() === $visibility);

    // Check ancestors and inherit their privacy setting for display
    if (!empty($check_ancestors)) {
        $ancestors = video_central_get_video_ancestors($video_id);

        foreach ((array) $ancestors as $ancestor) {
            if (video_central_is_video($ancestor) && video_central_is_video_private($ancestor, false)) {
                $retval = true;
            }
        }
    }

    return (bool) apply_filters(__FUNCTION__, (bool) $retval, $video_id, $check_ancestors);
}

/**
 * Is the video hidden?
 *
 * @since 1.0.0
 *
 * @param int  $video_id        Optional.  id
 * @param bool $check_ancestors Check if the ancestors are private (only if
 *                              they're a category)
 *
 * @uses get_post_meta() To get the video private meta
 * @uses video_central_get_video_ancestors() To get the video ancestors
 * @uses video_central_is_video_category() To check if the video is a category
 * @uses video_central_is_video_closed() To check if the video is closed
 *
 * @return bool True if closed, false if not
 */
function video_central_is_video_hidden($video_id = 0, $check_ancestors = true)
{
    $video_id = video_central_get_video_id($video_id);
    $visibility = video_central_get_video_visibility($video_id);

    // If post status is private, return true
    $retval = (video_central_get_hidden_status_id() === $visibility);

    // Check ancestors and inherit their privacy setting for display
    if (!empty($check_ancestors)) {
        $ancestors = video_central_get_video_ancestors($video_id);

        foreach ((array) $ancestors as $ancestor) {
            if (video_central_is_video($ancestor) && video_central_is_video_hidden($ancestor, false)) {
                $retval = true;
            }
        }
    }

    return (bool) apply_filters(__FUNCTION__, (bool) $retval, $video_id, $check_ancestors);
}

/**
 * Replace video meta details for users that cannot view them.
 *
 * @since 1.0.0
 *
 * @param string $retval
 * @param int    $video_id
 *
 * @uses video_central_is_video_private()
 * @uses current_user_can()
 *
 * @return string
 */
function video_central_suppress_private_video_meta($retval, $video_id)
{
    if (video_central_is_video_private($video_id, false) && !current_user_can('read_private_videos')) {
        $retval = '-';
    }

    return apply_filters(__FUNCTION__, $retval);
}

/**
 * Replace video author details for users that cannot view them.
 *
 * @since 1.0.0
 *
 * @param string $retval
 * @param int    $video_id
 *
 * @uses video_central_is_video_private()
 * @uses get_post_field()
 * @uses video_central_get_video_post_type()
 * @uses video_central_is_video_private()
 * @uses video_central_get_video_id()
 *
 * @return string
 */
function video_central_suppress_private_author_link($author_link, $args)
{

    // Assume the author link is the return value
    $retval = $author_link;

    // Show the normal author link
    if (!empty($args['post_id']) && !current_user_can('read_private_videos')) {

        // What post type are we looking at?
        $post_type = get_post_field('post_type', $args['post_id']);

        switch ($post_type) {

            // Video
            case video_central_get_video_post_type() :
                if (video_central_is_video_private(video_central_get_video_video_id($args['post_id']))) {
                    $retval = '';
                }

                break;

            // Post
            default :
                if (video_central_is_video_private($args['post_id'])) {
                    $retval = '';
                }

                break;
        }
    }

    return apply_filters(__FUNCTION__, $retval);
}

/**
 * Output the row class of a video.
 *
 * @since 1.0.0
 *
 * @param int $video_id Optional.  ID.
 * @param array Extra classes you can pass when calling this function
 *
 * @uses video_central_get_video_class() To get the row class of the video
 */
function video_central_videos_class($video_id = 0, $classes = array())
{
    echo video_central_get_video_class($video_id, $classes);
}
    /**
     * Return the row class of a video.
     *
     * @since 1.0.0
     *
     * @param int $video_id Optional.  ID
     * @param array Extra classes you can pass when calling this function
     *
     * @uses video_central_get_video_id() To validate the video id
     * @uses video_central_is_video_category() To see if video is a category
     * @uses video_central_get_video_status() To get the video status
     * @uses video_central_get_video_visibility() To get the video visibility
     * @uses video_central_get_video_parent_id() To get the video parent id
     * @uses get_post_class() To get all the classes including ours
     * @uses apply_filters() Calls 'video_central_get_video_class' with the classes
     *
     * @return string Row class of the video
     */
    function video_central_get_video_class($video_id = 0, $classes = array())
    {
        $video_central = video_central();
        $video_id = video_central_get_video_id($video_id);
        $count = isset($video_central->video_query->current_post) ? $video_central->video_query->current_post : 1;
        $classes = (array) $classes;

        // Get some classes
        $classes[] = 'video-central-loop-item-'.$count;
        $classes[] = ((int) $count % 2) ? 'video-central-even' : 'video-central-odd';
        $classes[] = video_central_is_video_category($video_id)        ? 'video-central-status-category'   : '';
        $classes[] = video_central_get_video_subvideo_count($video_id) ? 'video-central-has-subvideos' : '';
        $classes[] = video_central_get_video_parent_id($video_id)      ? 'video-central-parent-video-'.video_central_get_video_parent_id($video_id) : '';
        $classes[] = 'video-central-video-status-'.video_central_get_video_status($video_id);
        $classes[] = 'video-central-video-visibility-'.video_central_get_video_visibility($video_id);

        // Ditch the empties
        $classes = array_filter($classes);
        $classes = get_post_class($classes, $video_id);

        // Filter the results
        $classes = apply_filters(__FUNCTION__, $classes, $video_id);
        $retval = 'class="'.implode(' ', $classes).'"';

        return apply_filters(__FUNCTION__, $retval);
    }

/** Single Video **************************************************************/

/**
 * Output a fancy description of the current video, including total videos,
 * total videos, and last activity.
 *
 * @since 1.0.0
 *
 * @param array $args Arguments passed to alter output
 *
 * @uses video_central_get_single_video_description() Return the eventual output
 */
function video_central_single_video_description($args = '')
{
    echo video_central_get_single_video_description($args);
}
    /**
     * Return a fancy description of the current video, including total
     * videos, total videos, and last activity.
     *
     * @since 1.0.0
     *
     * @param mixed $args This function supports these arguments:
     *                    - video_id:  id
     *                    - before: Before the text
     *                    - after: After the text
     *                    - size: Size of the avatar
     *
     * @uses video_central_get_video_id() To get the video id
     * @uses add_filter() To add the 'view all' filter back
     * @uses apply_filters() Calls 'video_central_get_single_video_description' with
     *                        the description and args
     *
     * @return string Filtered video description
     */
    function video_central_get_single_video_description($args = '')
    {

        // Parse arguments against default values
        $r = video_central_parse_args($args, array(
            'video_id' => 0,
            'before' => '<div class="video-central-description"><p>',
            'after' => '</p></div>',
        ), 'get_single_video_description');

        // Validate video_id
        $video_id = video_central_get_video_id($r['video_id']);

        $retstr = get_the_excerpt($video_id);

        // Unhook the 'view all' query var adder
        remove_filter('video_central_get_video_permalink', 'video_central_add_view_all');

        // Combine the elements together
        $retstr = $r['before'].$retstr.$r['after'];

        // Return filtered result
        return apply_filters(__FUNCTION__, $retstr, $r);
    }

    /**
     * Get featured image.
     *
     * @param int $video_id video id
     *
     * @return array
     */
    function video_central_get_featured_image_id($video_id = 0)
    {
        $video_id = video_central_get_video_id($video_id);

        $thumb_id = false;

        //Check if post has a featured image set else get the first image from the video and use it. If both statements are false display fallback image.
        if ( get_post_meta($video_id, '_video_poster', true) ) {

            $thumb_id = get_post_meta($video_id, '_video_poster', true);

        } elseif ( has_post_thumbnail( $video_id ) ) {

            //get featured image
            $thumb_id = get_post_thumbnail_id($video_id);

        } elseif ( get_post_meta($video_id, '_video_thumbnail', true) ) {

            $thumb_id = get_post_meta($video_id, '_video_thumbnail', true);

        }

        return apply_filters(__FUNCTION__, $thumb_id, $video_id);
    }

/**
 * Get featured image.
 *
 * @param int $video_id video id
 *
 * @return array
 */
function video_central_get_featured_image_src($video_id = 0)
{
    $video_id = video_central_get_video_id($video_id);

    $image = wp_get_attachment_image_src(video_central_get_featured_image_id($video_id), 'full');

    return apply_filters(__FUNCTION__, $image, $video_id);
}

/**
 * [video_central_featured_image_url description].
 *
 * @param string $args [description]
 *
 * @return [type] [description]
 */
function video_central_featured_image_url($video_id = 0, $image_size = array())
{
    echo video_central_get_featured_image_url($video_id, $image_size);
}

    function video_central_get_featured_image_url($video_id = 0, $image_size = array())
    {
        $video_id = video_central_get_video_id($video_id);

        $image = null;
        $img_url = false;
        $placeholder = false;

        // Parse arguments with default video query for most circumstances
        $image_size = $image_size ? $image_size : video_central_thumbnail_dimensions();

        $args = array('crop' => true);

        // Parse arguments against default values
        $image_size = video_central_parse_args($image_size, $args, 'get_featured_image_url');

        //Check if post has a featured image set else get the first image from the video and use it. If both statements are false display fallback image.
        if ($image = video_central_get_featured_image_src($video_id)) {
            $img_url = $image[0];
        } else {

            //find first image anywhere in the post
            $img_url = video_central_get_first_post_image($video_id);
        }

        $image = $img_url ? video_central_resize($img_url, $image_size['width'], $image_size['height'], $image_size['crop']) : false;

        $placeholder_url = video_central()->theme_compat->theme->url.'img/placeholder.gif';

        if (!$image) {
            //Defines a default image
            $image = $placeholder_url;
            $placeholder = true;
        }

        return apply_filters(__FUNCTION__, $image, $placeholder, $video_id, $image_size);
    }

    /*
    * Get First post image
    *
    * @param $fallback - set to true to show fallback image
    */

    function video_central_get_first_post_image($video_id = 0)
    {
        $video_id = video_central_get_video_id($video_id);

        $content = get_post_field('post_content', $video_id);

        $first_img = false;

        $args = array(
            'numberposts' => 1,
            'order' => 'ASC',
            'orderby' => 'menu_order',
            'post_mime_type' => 'image',
            'post_parent' => $video_id,
            'post_status' => null,
            'post_type' => 'attachment',
        );

        $attachments = get_children($args);

        if ($attachments) {

            $first_attachment = array_shift($attachments);

            if ($first_attachment) {
                $first_img = wp_get_attachment_url($first_attachment->ID, 'full');
            } //get full URL to image (use "large" or "medium" if the image is too big)

        } else {
            ob_start();
            ob_end_clean();

            if (preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $matches)) {
                $first_img = $matches[1][0];
            }
        }

        return apply_filters(__FUNCTION__, $first_img, $video_id);
    }

/**
 * [video_central_add_comments add comment form to single video page.
 *
 * @return [type] [description]
 */
function video_central_add_comments()
{
    if ( ( comments_open() || '0' != get_comments_number() ) && video_central_allow_comments() ) {
        comments_template('', true);
    }
}

/**
 * Output the video archive title.
 *
 * @since 1.0.0
 *
 * @param string $title Default text to use as title
 */
function video_central_video_archive_title($title = '')
{
    echo video_central_get_video_archive_title($title);
}
    /**
     * Return the video archive title.
     *
     * @since 1.0.0
     *
     * @param string $title Default text to use as title
     *
     * @uses video_central_get_page_by_path() Check if page exists at root path
     * @uses get_the_title() Use the page title at the root path
     * @uses get_post_type_object() Load the post type object
     * @uses video_central_get_video_post_type() Get the video post type ID
     * @uses get_post_type_labels() Get labels for video post type
     * @uses apply_filters() Allow output to be manipulated
     *
     * @return string The video archive title
     */
    function video_central_get_video_archive_title($title = '')
    {

        // If no title was passed
        if (empty($title)) {

            // Set root text to page title
            $page = video_central_get_page_by_path(video_central_get_root_slug());
            if (!empty($page)) {
                $title = get_the_title($page->ID);

            // Default to video post type name label
            } else {
                $fto = get_post_type_object(video_central_get_video_post_type());
                $title = $fto->labels->name;
            }
        }

        return apply_filters(__FUNCTION__, $title);
    }

/**
 * Output the video duration.
 *
 * @since 1.0.0
 *
 * @param string $duration Video duration
 */
function video_central_video_duration($video_id = 0)
{
    echo video_central_get_video_duration($video_id);
}
    /**
     * Return  the video duration.
     *
     * @since 1.0.0
     *
     * @param string $video_id Default video id
     *
     * @uses video_central_get_video_id() Get video id
     * @uses get_post_meta() Use the page title at the root path
     * @uses apply_filters() Allow output to be manipulated
     *
     * @return string The the video duration
     */
    function video_central_get_video_duration($video_id = 0)
    {

        $video_id = video_central_get_video_id($video_id);

        $duration_data = get_post_meta( $video_id, '_video_central_video_duration', true );

        $duration_data = trim($duration_data);

        if( empty($duration_data) ) {
            $duration_data = '000';
        }

        $array = explode(':', $duration_data);

        $time = (count($array) !== 1) ? $duration_data : video_central_sec_to_time($duration_data);

        return apply_filters(__FUNCTION__, $time);
    }

/** Related Videos **********************************************************/

/**
 * Related Posts.
 *
 * @since 1.0
 */
function video_central_has_related_videos($args = '')
{
    global $post;

    $video_central = video_central();

    $query_args = array();

    $defaults = array(
        'number' => video_central_get_related_videos_per_page(),
        'randomize' => video_central_get_randomize_related_videos(),
    );

    $args = wp_parse_args($args, $defaults);

    extract($args);

    //check if related videos are allowed
    if (!video_central_allow_related_videos()) {
        return;
    }

    // Only displayed on singular post pages
    if (!is_singular()) {
        return;
    }

    // Check limited number
    if (!$number) {
        return;
    }

    // Check taxonomies
    $taxes = get_post_taxonomies($post->ID);

    if (empty($taxes)) {
        return;
    }

    $taxes = array_unique(array_merge(array($video_central->video_cat_tax_id, $video_central->video_tag_tax_id), $taxes));

    $in_tax_query_array = array();
    $and_tax_query_array = array();
    $post_format_query_array = null;

    foreach ($taxes as $tax) {
        if ($tax == 'post_format') {
            // Post format
            $post_format = get_post_format($post->ID);
            if (!$post_format) {
                $post_format = 'standard';
            }
            $post_format_query_array = array(
                'taxonomy' => 'post_format',
                'field' => 'slug',
                'terms' => 'post-format-'.$post_format,
                'operator' => 'IN',
            );

            continue;
        }

        $terms = get_the_terms($post->ID, $tax);

        if (empty($terms)) {
            continue;
        }

        $term_ids = array();
        foreach ($terms as $term) {
            $term_ids[] = $term->term_id;
        }

        $in_tax_query_array[$tax] = array(
            'taxonomy' => $tax,
            'field' => 'id',
            'terms' => $term_ids,
            'operator' => 'IN',
        );

        $and_tax_query_array[$tax] = array(
            'taxonomy' => $tax,
            'field' => 'id',
            'terms' => $term_ids,
            'operator' => 'AND',
        );
    }

    if (empty($in_tax_query_array) && empty($and_tax_query_array)) {
        return;
    }

    $query_args = array(
        'post_type' => get_post_type($post->ID),
        'ignore_sticky_posts' => true,
        'posts_per_page' => $number,
    );

    $current_post_id = $post->ID;
    $found_posts = array();

    // Multiple Taxonomy Query: relation = AND, operator = AND
    $query_args['tax_query'] = $and_tax_query_array;
    $query_args['tax_query'][] = $post_format_query_array;
    $query_args['tax_query']['relation'] = 'AND';
    $query_args['post__not_in'] = array($post->ID);

    $related = new WP_Query($query_args);

    foreach ($related->posts as $post) {
        $found_posts[] = $post->ID;
    }

    // Multiple Taxonomy Query: relation = AND, operator = IN
    if (count($found_posts) < $number) {
        $query_args['tax_query'] = $in_tax_query_array;
        $query_args['tax_query'][] = $post_format_query_array;
        $query_args['tax_query']['relation'] = 'AND';
        $query_args['post__not_in'] = array_merge(array($current_post_id), $found_posts);
        $related = new WP_Query($query_args);
        foreach ($related->posts as $post) {
            $found_posts[] = $post->ID;
        }
    }

    // Foreach Each Taxonomy Query: operator = AND
    if (count($found_posts) < $number) {
        foreach ($and_tax_query_array as $and_tax_query) {
            $query_args['tax_query'] = array($and_tax_query);
            $query_args['tax_query'][] = $post_format_query_array;
            $query_args['tax_query']['relation'] = 'AND';
            $query_args['post__not_in'] = array_merge(array($current_post_id), $found_posts);
            $related = new WP_Query($query_args);
            foreach ($related->posts as $post) {
                $found_posts[] = $post->ID;
            }

            if (count($found_posts) > $number) {
                break;
            }
        }
    }

    // Foreach Each Taxonomy Query: operator = IN
    if (count($found_posts) < $number) {
        foreach ($in_tax_query_array as $in_tax_query) {
            $query_args['tax_query'] = array($in_tax_query);
            $query_args['tax_query'][] = $post_format_query_array;
            $query_args['tax_query']['relation'] = 'AND';
            $query_args['post__not_in'] = array_merge(array($current_post_id), $found_posts);
            $related = new WP_Query($query_args);
            foreach ($related->posts as $post) {
                $found_posts[] = $post->ID;
            }

            if (count($found_posts) > $number) {
                break;
            }
        }
    }

    if (empty($found_posts)) {
        return;
    }

    $query_args['tax_query'] = '';
    $query_args['post__in'] = $found_posts;

    if ($randomize) {
        $query_args['orderby'] = 'rand';
    }

    $video_central->related_video_query = new WP_Query($query_args);

    return apply_filters(__FUNCTION__, $video_central->related_video_query->have_posts(), $video_central->related_video_query);
}

/**
 * Whether there are more videos available in the loop.
 *
 * @since 1.0.0
 *
 * @uses video_central:related_video_query::have_posts() To check if there are more videos
 *                                          available
 *
 * @return object information
 */
function video_central_related_videos()
{

    // Put into variable to check against next
    $have_posts = video_central()->related_video_query->have_posts();

    // Reset the post data when finished
    if (empty($have_posts)) {
        wp_reset_postdata();
    }

    return $have_posts;
}

/**
 * Loads up the current video in the loop.
 *
 * @since 1.0.0
 *
 * @uses video_central:related_video_query::the_post() To get the current video
 *
 * @return object information
 */
function video_central_the_related_video()
{
    return video_central()->related_video_query->the_post();
}

/** Video Pagination **********************************************************/

/**
 * Output the pagination count.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_video_pagination_count() To get the video pagination count
 */
function video_central_pagination_count()
{
    echo video_central_get_pagination_count();
}
    /**
     * Return the pagination count.
     *
     * @since 1.0.0
     *
     * @uses video_central_number_format() To format the number value
     * @uses apply_filters() Calls 'video_central_get_videos_pagination_count' with the
     *                        pagination count
     *
     * @return string Pagintion count
     */
    function video_central_get_pagination_count()
    {
        $video_central = video_central();

        if (empty($video_central->video_query)) {
            return false;
        }

        // Set pagination values
        $start_num = intval(($video_central->video_query->paged - 1) * $video_central->video_query->posts_per_page) + 1;
        $from_num = video_central_number_format($start_num);
        $to_num = video_central_number_format(($start_num + ($video_central->video_query->posts_per_page - 1) > $video_central->video_query->found_posts) ? $video_central->video_query->found_posts : $start_num + ($video_central->video_query->posts_per_page - 1));
        $total_int = (int) !empty($video_central->video_query->found_posts) ? $video_central->video_query->found_posts : $video_central->video_query->post_count;
        $total = video_central_number_format($total_int);

        // Several videos in a single page
        if (empty($to_num)) {
            $retstr = sprintf(_n('Showing %1$s videos', 'Showing %1$s videos', $total_int, 'video_central'), $total);
        } elseif ($from_num = 1) {
            $retstr = null;

        // several pages
        } else {
            $retstr = sprintf(_n('Showing %2$s (of %4$s total)', 'Showing %2$s - %3$s of %4$s', $total_int, 'video_central'), $video_central->video_query->post_count, $from_num, $to_num, $total);
        }

        // Filter and return
        return apply_filters(__FUNCTION__, esc_html($retstr));
    }

/**
 * Output pagination links.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_video_pagination_links() To get the pagination links
 */
function video_central_pagination_links()
{
    echo video_central_get_pagination_links();
}
    /**
     * Return pagination links.
     *
     * @since 1.0.0
     *
     * @uses video_central::video_query::pagination_links To get the links
     *
     * @return string Pagination links
     */
    function video_central_get_pagination_links()
    {
        $rv = video_central();

        if (empty($rv->video_query)) {
            return false;
        }

        return apply_filters(__FUNCTION__, $rv->video_query->pagination_links);
    }

/**
 * Output the author ID of the video.
 *
 * @since 1.0.0
 *
 * @param int $video_id Optional. Video id
 *
 * @uses video_central_get_video_author_id() To get the video author id
 */
function video_central_video_author_id($video_id = 0)
{
    echo video_central_get_video_author_id($video_id);
}
    /**
     * Return the author ID of the video.
     *
     * @since 1.0.0
     *
     * @param int $video_id Optional. Video id
     *
     * @uses video_central_get_video_id() To get the video id
     * @uses get_post_field() To get the video author id
     * @uses apply_filters() Calls 'video_central_get_video_author_id' with the author
     *                        id and video id
     *
     * @return string Author of video
     */
    function video_central_get_video_author_id($video_id = 0)
    {
        $video_id = video_central_get_video_id($video_id);
        $author_id = get_post_field('post_author', $video_id);

        return (int) apply_filters(__FUNCTION__, (int) $author_id, $video_id);
    }

/**
 * Output the author display_name of the video.
 *
 * @since 1.0.0
 *
 * @param int $video_id Optional. Video id
 *
 * @uses video_central_get_video_author_display_name() To get the video author's display
 *                                            name
 */
function video_central_video_author_display_name($video_id = 0)
{
    echo video_central_get_video_author_display_name($video_id);
}
    /**
     * Return the author display_name of the video.
     *
     * @since 1.0.0
     *
     * @param int $video_id Optional. Video id
     *
     * @uses video_central_get_video_id() To get the video id
     * @uses video_central_is_video_anonymous() To check if the video is by an
     *                                 anonymous user
     * @uses video_central_get_video_author_id() To get the video author id
     * @uses get_the_author_meta() To get the author meta
     * @uses get_post_meta() To get the anonymous user name
     * @uses apply_filters() Calls 'video_central_get_video_author_id' with the
     *                        display name and video id
     *
     * @return string Video's author's display name
     */
    function video_central_get_video_author_display_name($video_id = 0)
    {
        $video_id = video_central_get_video_id($video_id);

        // Check for anonymous user
        if (!video_central_is_video_anonymous($video_id)) {

            // Get the author ID
            $author_id = video_central_get_video_author_id($video_id);

            // Try to get a display name
            $author_name = get_the_author_meta('display_name', $author_id);

            // Fall back to user login
            if (empty($author_name)) {
                $author_name = get_the_author_meta('user_login', $author_id);
            }

        // User does not have an account
        } else {
            $author_name = get_post_meta($video_id, '_video_central_anonymous_name', true);
        }

        // If nothing could be found anywhere, use Anonymous
        if (empty($author_name)) {
            $author_name = __('Anonymous', 'video_central');
        }

        // Encode possible UTF8 display names
        if (seems_utf8($author_name) === false) {
            $author_name = utf8_encode($author_name);
        }

        return apply_filters(__FUNCTION__, $author_name, $video_id);
    }

/**
 * Output the author avatar of the video.
 *
 * @since 1.0.0
 *
 * @param int $video_id Optional. Video id
 * @param int $size     Optional. Avatar size. Defaults to 40
 *
 * @uses video_central_get_video_author_avatar() To get the video author avatar
 */
function video_central_video_author_avatar($video_id = 0, $size = 40)
{
    echo video_central_get_video_author_avatar($video_id, $size);
}
    /**
     * Return the author avatar of the video.
     *
     * @since 1.0.0
     *
     * @param int $video_id Optional. Video id
     * @param int $size     Optional. Avatar size. Defaults to 40
     *
     * @uses video_central_get_video_id() To get the video id
     * @uses video_central_is_video_anonymous() To check if the video is by an
     *                                 anonymous user
     * @uses video_central_get_video_author_id() To get the video author id
     * @uses get_post_meta() To get the anonymous user's email
     * @uses get_avatar() To get the avatar
     * @uses apply_filters() Calls 'video_central_get_video_author_avatar' with the
     *                        avatar, video id and size
     *
     * @return string Avatar of the author of the video
     */
    function video_central_get_video_author_avatar($video_id = 0, $size = 40)
    {
        $author_avatar = '';

        $video_id = video_central_get_video_id($video_id);
        if (!empty($video_id)) {
            if (!video_central_is_video_anonymous($video_id)) {
                $author_avatar = get_avatar(video_central_get_video_author_id($video_id), $size);
            } else {
                $author_avatar = get_avatar(get_post_meta($video_id, '_video_central_anonymous_email', true), $size);
            }
        }

        return apply_filters(__FUNCTION__, $author_avatar, $video_id, $size);
    }

/**
 * Output the author link of the video.
 *
 * @since 1.0.0
 *
 * @param mixed|int $args If it is an integer, it is used as video_id. Optional.
 *
 * @uses video_central_get_video_author_link() To get the video author link
 */
function video_central_video_author_link($args = '')
{
    echo video_central_get_video_author_link($args);
}
    /**
     * Return the author link of the video.
     *
     * @since 1.0.0
     *
     * @param mixed|int $args If it is an integer, it is used as video id.
     *                        Optional.
     *
     * @uses video_central_get_video_id() To get the video id
     * @uses video_central_get_video_author_display_name() To get the video author
     * @uses video_central_is_video_anonymous() To check if the video is by an
     *                                 anonymous user
     * @uses video_central_get_video_author_url() To get the video author url
     * @uses video_central_get_video_author_avatar() To get the video author avatar
     * @uses video_central_get_video_author_display_name() To get the video author display
     *                                      name
     * @uses video_central_get_user_display_role() To get the video author display role
     * @uses video_central_get_video_author_id() To get the video author id
     * @uses apply_filters() Calls 'video_central_get_video_author_link' with the link
     *                        and args
     *
     * @return string Author link of video
     */
    function video_central_get_video_author_link($args = '')
    {

        // Parse arguments against default values
        $r = video_central_parse_args($args, array(
            'before' => '',
            'post_id' => 0,
            'link_title' => '',
            'type' => 'both',
            'size' => 80,
            'sep' => '&nbsp;',
            'show_role' => false,
            'after' => '',
        ), 'get_video_author_link');

        // Used as video_id
        if (is_numeric($args)) {
            $video_id = video_central_get_video_id($args);
        } else {
            $video_id = video_central_get_video_id($r['post_id']);
        }

        // Video ID is good
        if (!empty($video_id)) {

            // Get some useful video information
            $author_url = video_central_get_video_author_url($video_id);
            $anonymous = video_central_is_video_anonymous($video_id);

            // Tweak link title if empty
            if (empty($r['link_title'])) {
                $link_title = sprintf(empty($anonymous) ? __('View %s\'s profile', 'video_central') : __('Visit %s\'s website', 'video_central'), video_central_get_video_author_display_name($video_id));

            // Use what was passed if not
            } else {
                $link_title = $r['link_title'];
            }

            // Setup title and author_links array
            $link_title = !empty($link_title) ? ' title="'.esc_attr($link_title).'"' : '';
            $author_links = array();

            // Get avatar
            if ('avatar' === $r['type'] || 'both' === $r['type']) {
                $author_links['avatar'] = video_central_get_video_author_avatar($video_id, $r['size']);
            }

            // Get display name
            if ('name' === $r['type'] || 'both' === $r['type']) {
                $author_links['name'] = video_central_get_video_author_display_name($video_id);
            }

            // Link class
            $link_class = ' class="video-central-author-'.esc_attr($r['type']).'"';

            // Add links if not anonymous
            if (empty($anonymous) && video_central_user_has_profile(video_central_get_video_author_id($video_id))) {

                // Assemble the links
                foreach ($author_links as $link => $link_text) {
                    $link_class = ' class="video-central-author-'.esc_attr($link).'"';
                    $author_link[] = sprintf('<a href="%1$s"%2$s%3$s>%4$s</a>', esc_url($author_url), $link_title, $link_class, $link_text);
                }

                if (true === $r['show_role']) {
                    $author_link[] = video_central_get_video_author_role(array('video_id' => $video_id));
                }

                $author_link = implode($r['sep'], $author_link);

            // No links if anonymous
            } else {
                $author_link = implode($r['sep'], $author_links);
            }
        } else {
            $author_link = '';
        }

        $author_link = $r['before'].$author_link.$r['after'];

        return apply_filters(__FUNCTION__, $author_link, $args);
    }

/**
 * Output the author url of the video.
 *
 * @since 1.0.0
 *
 * @param int $video_id Optional. Video id
 *
 * @uses video_central_get_video_author_url() To get the video author url
 */
function video_central_video_author_url($video_id = 0)
{
    echo esc_url(video_central_get_video_author_url($video_id));
}

    /**
     * Return the author url of the video.
     *
     * @since 1.0.0
     *
     * @param int $video_id Optional. Video id
     *
     * @uses video_central_get_video_id() To get the video id
     * @uses video_central_is_video_anonymous() To check if the video is by an anonymous
     *                                 user or not
     * @uses video_central_user_has_profile() To check if the user has a profile
     * @uses video_central_get_video_author_id() To get video author id
     * @uses video_central_get_user_profile_url() To get profile url
     * @uses get_post_meta() To get anonmous user's website
     * @uses apply_filters() Calls 'video_central_get_video_author_url' with the link &
     *                        video id
     *
     * @return string Author URL of video
     */
    function video_central_get_video_author_url($video_id = 0)
    {
        $video_id = video_central_get_video_id($video_id);

        // Check for anonymous user or non-existant user
        if (!video_central_is_video_anonymous($video_id) && video_central_user_has_profile(video_central_get_video_author_id($video_id))) {
            $author_url = video_central_get_user_profile_url(video_central_get_video_author_id($video_id));
        } else {
            $author_url = get_post_meta($video_id, '_video_central_anonymous_website', true);

            // Set empty author_url as empty string
            if (empty($author_url)) {
                $author_url = '';
            }
        }

        return apply_filters(__FUNCTION__, $author_url, $video_id);
    }

/**
 * Output the video author email address.
 *
 * @since 1.0.0
 *
 * @param int $video_id Optional. Reply id
 *
 * @uses video_central_get_video_author_email() To get the video author email
 */
function video_central_video_author_email($video_id = 0)
{
    echo video_central_get_video_author_email($video_id);
}
    /**
     * Return the video author email address.
     *
     * @since 1.0.0
     *
     * @param int $video_id Optional. Reply id
     *
     * @uses video_central_get_video_id() To get the video id
     * @uses video_central_is_video_anonymous() To check if the video is by an anonymous
     *                                 user
     * @uses video_central_get_video_author_id() To get the video author id
     * @uses get_userdata() To get the user data
     * @uses get_post_meta() To get the anonymous poster's email
     * @uses apply_filters() Calls video_central_get_video_author_email with the author
     *                        email & video id
     *
     * @return string Video author email address
     */
    function video_central_get_video_author_email($video_id = 0)
    {
        $video_id = video_central_get_video_id($video_id);

        // Not anonymous user
        if (!video_central_is_video_anonymous($video_id)) {

            // Use video author email address
            $user_id = video_central_get_video_author_id($video_id);
            $user = get_userdata($user_id);
            $author_email = !empty($user->user_email) ? $user->user_email : '';

        // Anonymous
        } else {

            // Get email from post meta
            $author_email = get_post_meta($video_id, '_video_central_anonymous_email', true);

            // Sanity check for missing email address
            if (empty($author_email)) {
                $author_email = '';
            }
        }

        return apply_filters(__FUNCTION__, $author_email, $video_id);
    }

/**
 * Output the video author role.
 *
 * @since 1.0.0
 *
 * @param array $args Optional.
 *
 * @uses video_central_get_video_author_role() To get the video author role
 */
function video_central_video_author_role($args = array())
{
    echo video_central_get_video_author_role($args);
}
    /**
     * Return the video author role.
     *
     * @since 1.0.0
     *
     * @param array $args Optional.
     *
     * @uses video_central_get_video_id() To get the video id
     * @uses video_central_get_user_display_role() To get the user display role
     * @uses video_central_get_video_author_id() To get the video author id
     * @uses apply_filters() Calls video_central_get_video_author_role with the author
     *                        role & args
     *
     * @return string video author role
     */
    function video_central_get_video_author_role($args = array())
    {

        // Parse arguments against default values
        $r = video_central_parse_args($args, array(
            'video_id' => 0,
            'class' => 'video-central-author-role',
            'before' => '',
            'after' => '',
        ), 'get_video_author_role');

        $video_id = video_central_get_video_id($r['video_id']);
        $role = video_central_get_user_display_role(video_central_get_video_author_id($video_id));
        $role_class = strtolower($role);
        $author_role = sprintf('%1$s<div class="%2$s %3$s">%4$s</div>%5$s', $r['before'], $r['class'], $role_class, $role, $r['after']);

        return apply_filters(__FUNCTION__, $author_role, $r);
    }

/**
 * Output a list of categories.
 *
 * @since 1.0.0
 *
 * @param array $args Optional.
 *
 * @uses video_central_get_categories_list() To get the video categories
 */
function video_central_categories_list($args = array())
{
    echo video_central_get_categories_list($args);
}
    /**
     * Return a list of categories.
     *
     * @since 1.0.0
     *
     * @return string footer meta
     */
    function video_central_get_categories_list($args = array())
    {
        $output = null;

        // Parse arguments against default values
        $r = video_central_parse_args($args, array(
            'video_id' => 0,
            'class' => 'video-central-categories-list clearfix',
            'title' => __('Categories: ', 'video_central'),
            'before' => '',
            'after' => '',
        ), 'get_categories_list');

        $video_id = video_central_get_video_id($r['video_id']);

        if (!video_central_allow_video_categories()) {
            return;
        }

        $categories = get_the_term_list($video_id, video_central_get_video_category_tax_id());

        if ($categories) {
            $output = sprintf('%1$s<div class="%2$s"><strong>%3$s</strong>%4$s</div>%5$s', $r['before'], $r['class'], $r['title'], $categories, $r['after']);
        }

        return apply_filters(__FUNCTION__, $output, $r);
    }

/**
 * Output a list of tags.
 *
 * @since 1.1.3
 *
 * @param array $args Optional.
 *
 * @uses video_central_get_tags_list() To get the video tags
 */
function video_central_tags_list($args = array())
{
    echo video_central_get_tags_list($args);
}
    /**
     * Return a list of tags.
     *
     * @since 1.1.3
     *
     * @return string footer meta
     */
    function video_central_get_tags_list($args = array())
    {
        $output = null;

        // Parse arguments against default values
        $r = video_central_parse_args($args, array(
            'video_id' => 0,
            'class' => 'video-central-tags-list clearfix',
            'title' => __('Tags: ', 'video_central'),
            'before' => '',
            'after' => '',
        ), 'get_tags_list');

        $video_id = video_central_get_video_id($r['video_id']);

        if (!video_central_allow_video_tags()) {
            return;
        }

        $tags = get_the_term_list($video_id, video_central_get_video_tag_tax_id());

        if ($tags) {
            $output = sprintf('%1$s<div class="%2$s"><strong>%3$s</strong>%4$s</div>%5$s', $r['before'], $r['class'], $r['title'], $tags, $r['after']);
        }

        return apply_filters(__FUNCTION__, $output, $r);
    }
