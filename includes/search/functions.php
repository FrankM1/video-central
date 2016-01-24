<?php

/**
 * Video Central Search Functions.
 */

/** Query *********************************************************************/

/**
 * Run the search query.
 *
 * @since 1.0.0
 *
 * @param mixed $new_args New arguments
 *
 * @uses video_central_get_search_query_args() To get the search query args
 * @uses video_central_parse_args() To parse the args
 * @uses video_central_has_search_results() To make the search query
 *
 * @return bool False if no results, otherwise if search results are there
 */
function video_central_search_query($new_args = array())
{

    // Existing arguments
    $query_args = video_central_get_search_query_args();

    // Merge arguments
    if (!empty($new_args)) {
        $new_args = video_central_parse_args($new_args, array(), 'search_query');
        $query_args = array_merge($query_args, $new_args);
    }

    return video_central_has_search_results($query_args);
}

/**
 * Return the search's query args.
 *
 * @since 1.0.0
 *
 * @uses video_central_get_search_terms() To get the search terms
 *
 * @return array Query arguments
 */
function video_central_get_search_query_args()
{

    // Get search terms
    $search_terms = video_central_get_search_terms();
    $retval = !empty($search_terms) ? array('s' => $search_terms) : array();

    return apply_filters(__FUNCTION__, $retval);
}

/**
 * Redirect to search results page if needed.
 *
 * @since 1.0.0
 *
 * @return If a redirect is not needed
 */
function video_central_search_results_redirect()
{
    global $wp_rewrite;

    // Bail if not a search request action
    if (empty($_GET['action']) || ('video-search-request' !== $_GET['action'])) {
        return;
    }

    // Bail if not using pretty permalinks
    if (!$wp_rewrite->using_permalinks()) {
        return;
    }

    // Get the redirect URL
    $redirect_to = video_central_get_search_results_url();
    if (empty($redirect_to)) {
        return;
    }

    // Redirect and bail
    wp_safe_redirect($redirect_to);
    wp_die();
}
