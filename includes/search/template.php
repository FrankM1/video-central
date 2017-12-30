<?php

/**
 * Video Central Search Template Tags.
 */

/** Search Loop Functions *****************************************************/

/**
 * The main search loop. WordPress does the heavy lifting.
 *
 * @since 1.0.0
 *
 * @param mixed $args All the arguments supported by {@link WP_Query}
 *
 * @uses video_central_get_view_all() Are we showing all results?
 * @uses video_central_get_public_status_id() To get the public status id
 * @uses video_central_get_closed_status_id() To get the closed status id
 * @uses video_central_get_spam_status_id() To get the spam status id
 * @uses video_central_get_trash_status_id() To get the trash status id
 * @uses video_central_get_video_post_type() To get the video post type
 * @uses video_central_get_paged() To get the current page value
 * @uses video_central_get_search_terms() To get the search terms
 * @uses WP_Query To make query and get the search results
 * @uses WP_Rewrite::using_permalinks() To check if the blog is using permalinks
 * @uses video_central_get_search_url() To get the video search url
 * @uses paginate_links() To paginate search results
 * @uses apply_filters() Calls 'video_central_has_search_results' with
 *                        video_central::search_query::have_posts()
 *
 * @return object Multidimensional array of search information
 */
function video_central_has_search_results($args = '')
{
    global $wp_rewrite;

    /* Defaults **************************************************************/

    // Default query args
    $default = array(
        'post_type' => video_central_get_video_post_type(),         // Videos
        'posts_per_page' => video_central_get_videos_per_page(), // This many
        'paged' => video_central_get_paged(),            // On this page
        'orderby' => 'date',                     // Sorted by date
        'order' => 'DESC',                     // Most recent first
        'ignore_sticky_posts' => true,                       // Stickies not supported
        's' => video_central_get_search_terms(),     // This is a search
    );

    /* Setup *****************************************************************/

    // Parse arguments against default values
    $r = video_central_parse_args($args, $default, 'has_search_results');

    // Get Video Central
    $video_central = video_central();

    // Call the query
    if (!empty($r['s'])) {
        $video_central->search_query = new WP_Query($r);
    }

    // Add pagination values to query object
    $video_central->search_query->posts_per_page = $r['posts_per_page'];
    $video_central->search_query->paged = $r['paged'];

    // Never home, regardless of what parse_query says
    $video_central->search_query->is_home = false;

    // Only add pagination is query returned results
    if (!empty($video_central->search_query->found_posts) && !empty($video_central->search_query->posts_per_page)) {

        // Array of arguments to add after pagination links
        $add_args = array();

        // If pretty permalinks are enabled, make our pagination pretty
        if ($wp_rewrite->using_permalinks()) {

            // Shortcode territory
            if (is_page() || is_single()) {
                $base = trailingslashit(get_permalink());

            // Default search location
            } else {
                $base = trailingslashit(video_central_get_search_results_url());
            }

            // Add pagination base
            $base = $base.user_trailingslashit($wp_rewrite->pagination_base.'/%#%/');

        // Unpretty permalinks
        } else {
            $base = add_query_arg('paged', '%#%');
        }

        // Add args
        //if ( video_central_get_view_all() ) {
        //	$add_args['view'] = 'all';
        //}

        // Add pagination to query object
        $pagination_links_args = apply_filters('video_central_search_results_pagination', array(
            'base' => $base,
            'format' => '',
            'total' => ceil((int) $video_central->search_query->found_posts / (int) $r['posts_per_page']),
            'current' => (int) $video_central->search_query->paged,
            'prev_text' => is_rtl() ? '&rarr;' : '&larr;',
            'next_text' => is_rtl() ? '&larr;' : '&rarr;',
            'mid_size' => 8,
            'end_size' => 1,
            'add_args' => $add_args,
        ));

        $video_central->search_query->pagination_links = paginate_links($pagination_links_args);

        // Remove first page from pagination
        if ($wp_rewrite->using_permalinks()) {
            $video_central->search_query->pagination_links = str_replace($wp_rewrite->pagination_base.'/1/', '', $video_central->search_query->pagination_links);
        } else {
            $video_central->search_query->pagination_links = str_replace('&#038;paged=1', '', $video_central->search_query->pagination_links);
        }

        $video_central->search_query->pagination_links = apply_filters('video_central_search_query_pagination_links', $video_central->search_query->pagination_links, $video_central->video_query, $base, $r);

    }

    // Return object
    return apply_filters(__FUNCTION__, $video_central->search_query->have_posts(), $video_central->search_query);
}

/**
 * Whether there are more search results available in the loop.
 *
 * @since 1.0.0
 *
 * @uses WP_Query video_central::search_query::have_posts() To check if there are more
 *                                                     search results available
 *
 * @return object Search information
 */
function video_central_search_results()
{

    // Put into variable to check against next
    $have_posts = video_central()->search_query->have_posts();

    // Reset the post data when finished
    if (empty($have_posts)) {
        wp_reset_postdata();
    }

    return $have_posts;
}

/**
 * Loads up the current search result in the loop.
 *
 * @since 1.0.0
 *
 * @uses WP_Query video_central::search_query::the_post() To get the current search result
 *
 * @return object Search information
 */
function video_central_the_search_result()
{
    $search_result = video_central()->search_query->the_post();

    // Reset each current video id
    video_central()->current_video_id = video_central_get_video_id();

    return $search_result;
}

/**
 * Output the search page title.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_search_title()
 */
function video_central_search_title()
{
    echo video_central_get_search_title();
}

    /**
     * Get the search page title.
     *
     * @since 1.0.0
     *
     * @uses video_central_get_search_terms()
     */
    function video_central_get_search_title()
    {

        // Get search terms
        $search_terms = video_central_get_search_terms();

        // No search terms specified
        if (empty($search_terms)) {
            $title = esc_html__('Search', 'video_central');

        // Include search terms in title
        } else {
            $title = sprintf(esc_html__("Search Results for '%s'", 'video_central'), esc_attr($search_terms));
        }

        return apply_filters(__FUNCTION__, $title, $search_terms);
    }

/**
 * Output the search url.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_search_url() To get the search url
 */
function video_central_search_url()
{
    echo esc_url(video_central_get_search_url());
}
    /**
     * Return the search url.
     *
     * @since 1.0.0
     *
     * @uses user_trailingslashit() To fix slashes
     * @uses trailingslashit() To fix slashes
     * @uses video_central_get_videos_url() To get the root videos url
     * @uses video_central_get_search_slug() To get the search slug
     * @uses add_query_arg() To help make unpretty permalinks
     *
     * @return string Search url
     */
    function video_central_get_search_url()
    {
        global $wp_rewrite;

        // Pretty permalinks
        if ($wp_rewrite->using_permalinks()) {
            $url = $wp_rewrite->root.video_central_get_search_slug();
            $url = home_url(user_trailingslashit($url));

        // Unpretty permalinks
        } else {
            $url = add_query_arg(array(video_central_get_search_rewrite_id() => ''), home_url('/'));
        }

        return apply_filters(__FUNCTION__, $url);
    }

/**
 * Output the search results url.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_search_url() To get the search url
 */
function video_central_search_results_url()
{
    echo esc_url(video_central_get_search_results_url());
}
    /**
     * Return the search url.
     *
     * @since 1.0.0
     *
     * @uses user_trailingslashit() To fix slashes
     * @uses trailingslashit() To fix slashes
     * @uses video_central_get_videos_url() To get the root videos url
     * @uses video_central_get_search_slug() To get the search slug
     * @uses add_query_arg() To help make unpretty permalinks
     *
     * @return string Search url
     */
    function video_central_get_search_results_url()
    {
        global $wp_rewrite;

        // Get the search terms
        $search_terms = video_central_get_search_terms();

        // Pretty permalinks
        if ($wp_rewrite->using_permalinks()) {

            // Root search URL
            $url = $wp_rewrite->root.video_central_get_search_slug();

            // Append search terms
            if (!empty($search_terms)) {
                $url = trailingslashit($url).user_trailingslashit(urlencode($search_terms));
            }

            // Run through home_url()
            $url = home_url(user_trailingslashit($url));

        // Unpretty permalinks
        } else {
            $url = add_query_arg(array(video_central_get_search_rewrite_id() => urlencode($search_terms)), home_url('/'));
        }

        return apply_filters(__FUNCTION__, $url);
    }

/**
 * Output the search terms.
 *
 * @since 1.0.0
 *
 * @param string $search_terms Optional. Search terms
 *
 * @uses video_central_get_search_terms() To get the search terms
 */
function video_central_search_terms($search_terms = '')
{
    echo video_central_get_search_terms($search_terms);
}

    /**
     * Get the search terms.
     *
     * @since 1.0.0
     *
     * If search terms are supplied, those are used. Otherwise check the
     * search rewrite id query var.
     *
     * @param string $passed_terms Optional. Search terms
     *
     * @uses sanitize_title() To sanitize the search terms
     * @uses get_query_var() To get the search terms from query variable
     *
     * @return bool|string Search terms on success, false on failure
     */
    function video_central_get_search_terms($passed_terms = '')
    {

        // Sanitize terms if they were passed in
        if (!empty($passed_terms)) {
            $search_terms = sanitize_title($passed_terms);

        // Use query variable if not
        } else {
            $search_terms = get_query_var(video_central_get_search_rewrite_id());
        }

        $search_terms = get_query_var(video_central_get_search_rewrite_id());

        // Trim whitespace and decode, or set explicitly to false if empty
        $search_terms = !empty($search_terms) ? urldecode(trim($search_terms)) : false;

        return apply_filters(__FUNCTION__, $search_terms, $passed_terms);
    }

/**
 * Output the search result pagination count.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_search_pagination_count() To get the search result pagination count
 */
function video_central_search_pagination_count()
{
    echo video_central_get_search_pagination_count();
}

    /**
     * Return the search results pagination count.
     *
     * @since 1.0.0
     *
     * @uses video_central_number_format() To format the number value
     * @uses apply_filters() Calls 'video_central_get_search_pagination_count' with the
     *                        pagination count
     *
     * @return string Search pagination count
     */
    function video_central_get_search_pagination_count()
    {
        $video_central = video_central();

        // Define local variable(s)
        $retstr = '';

        // Set pagination values
        $start_num = intval(($video_central->search_query->paged - 1) * $video_central->search_query->posts_per_page) + 1;
        $from_num = video_central_number_format($start_num);

        $to_num = video_central_number_format(($start_num + ($video_central->search_query->posts_per_page - 1) > $video_central->search_query->found_posts) ? $video_central->search_query->found_posts : $start_num + ($video_central->search_query->posts_per_page - 1));

        $total_int = (int) !empty($video_central->search_query->found_posts) ? $video_central->search_query->found_posts : $video_central->search_query->post_count;

        $total = video_central_number_format($total_int);

        // Several videos in a single page
        if (empty($to_num)) {
            $retstr = sprintf(_n('Showing %1$s videos', 'Showing %1$s videos', $total_int, 'video_central'), $total);

        // several pages
        } else {
            $retstr = sprintf(_n('Showing %2$s (of %4$s total)', 'Showing %2$s - %3$s of %4$s', $total_int, 'video_central'), $video_central->search_query->post_count, $from_num, $to_num, $total);
        }

        // Filter and return
        return apply_filters(__FUNCTION__, esc_html($retstr));
    }

/**
 * Output search pagination links.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_search_pagination_links() To get the search pagination links
 */
function video_central_search_pagination_links()
{
    echo video_central_get_search_pagination_links();
}

    /**
     * Return search pagination links.
     *
     * @since 1.0.0
     *
     * @uses apply_filters() Calls 'video_central_get_search_pagination_links' with the
     *                        pagination links
     *
     * @return string Search pagination links
     */
    function video_central_get_search_pagination_links()
    {
        $video_central = video_central();

        if (!isset($video_central->search_query->pagination_links) || empty($video_central->search_query->pagination_links)) {
            return false;
        }

        return apply_filters(__FUNCTION__, $video_central->search_query->pagination_links);
    }
