<?php

/**
 * Query YouTube for single video details.
 *
 * @since 1.0.0
 *
 * @param string $video_id
 * @param string $source
 */
function video_central_query_youtube_video($video_id, $source = 'youtube')
{
    $sources = array(
        'youtube' => 'http://gdata.youtube.com/feeds/api/videos/%s?v=2&alt=jsonc',
    );

    if (!array_key_exists($source, $sources)) {
        return false;
    }

    $url = $sources[ $source ];
    $request = wp_remote_get(sprintf($url, $video_id), array('timeout' => 30));

    if (is_wp_error($request)) {
        return "{$request->get_error_code()}: {$request->get_error_message()}";
    }

    return $request;
}

/**
 * Formats the response from the feed for a single entry.
 *
 * @since 1.0.0
 *
 * @param array $entry
 */
function video_central_format_youtube_video_entry($raw_entry)
{

    // playlists have individual items stored under key video
    if (array_key_exists('video', $raw_entry)) {
        $raw_entry = $raw_entry['video'];
    }

    // permissions
    $entry = array();

    $thumbnails = array();
    foreach ($raw_entry['thumbnail'] as $thumbnail) {
        $thumbnails[] = $thumbnail;
    }

    $entry = array(
        'video_id' => $raw_entry['id'],
        'uploader' => $raw_entry['uploader'],
        'published' => $raw_entry['uploaded'],
        'updated' => $raw_entry['updated'],
        'title' => $raw_entry['title'],
        'description' => $raw_entry['description'],
        'category' => $raw_entry['category'],
        'duration' => $raw_entry['duration'],
        'thumbnails' => $thumbnails,
        'stats' => array(
            'comments' => isset($raw_entry['commentCount'])  ? $raw_entry['commentCount']    : 0,
            'rating' => isset($raw_entry['rating'])        ? $raw_entry['rating']          : 0,
            'rating_count' => isset($raw_entry['ratingCount'])   ? $raw_entry['ratingCount']     : 0,
            'views' => isset($raw_entry['viewCount'])     ? $raw_entry['viewCount']       : 0,
        ),
    );

    return $entry;
}

/**
 * Perform a YouTube search. Arguments:.
 *
 * include_categories bool - when true, video categories will be retrieved, if false, they won't
 * query string - the search query
 * page_token - YT API 3 page token for pagination
 * order string - any of: date, rating, relevance, title, viewCount
 * duration string - any of: any, short, medium, long
 *
 * @since 1.2.0
 *
 * @return array of videos or WP error
 */
function video_central_youtube_api_search_videos($args = array())
{
    $defaults = array(
        // if false, YouTube categories won't be retrieved
        'include_categories' => true,
        // the search query
        'query' => '',
        // as of API 3, results pagination is done by tokens
        'page_token' => '',
        // can be: date, rating, relevance, title, viewCount
        'order' => 'relevance',
        // can be: any, short, medium, long
        'duration' => 'any',
        // not used but into the script
        'embed' => 'any',
    );

    $args = wp_parse_args($args, $defaults);

    extract($args, EXTR_SKIP);

    $per_page = video_central_import_results_per_page();

    $q = new Video_Central_Youtube_API_Query($per_page, $include_categories);
    $videos = $q->search($query, $page_token, array('order' => $order, 'duration' => $duration, 'embed' => $embed));
    $page_info = $q->get_list_info();

    return array(
        'videos' => $videos,
        'page_info' => $page_info,
    );
}

/**
 * Get videos for a given YouTube playlist. Arguments:.
 *
 * include_categories bool - when true, video categories will be retrieved, if false, they won't
 * query string - the search query
 * page_token - YT API 3 page token for pagination
 * type string - auto or manual
 *
 * @since 1.2.0
 *
 * @param array $args
 */
function video_central_youtube_api_get_playlist($args = array())
{
    $args['playlist_type'] = 'playlist';

    return video_central_youtube_api_get_list($args);
}

/**
 * Get videos for a given YouTube user. Arguments:.
 *
 * include_categories bool - when true, video categories will be retrieved, if false, they won't
 * query string - the search query
 * page_token - YT API 3 page token for pagination
 * type string - auto or manual
 *
 * @since 1.2.0
 *
 * @param array $args
 */
function video_central_youtube_api_get_user($args = array())
{
    $args['playlist_type'] = 'user';

    return video_central_youtube_api_get_list($args);
}

/**
 * Get videos for a given YouTube channel. Arguments:.
 *
 * include_categories bool - when true, video categories will be retrieved, if false, they won't
 * query string - the search query
 * page_token - YT API 3 page token for pagination
 * type string - auto or manual
 *
 * @since 1.2.0
 *
 * @param array $args
 */
function video_central_youtube_api_get_channel($args = array())
{
    $args['playlist_type'] = 'channel';

    return video_central_youtube_api_get_list($args);
}

/**
 * Get details about a single video ID.
 *
 * @since 1.2.0
 *
 * @param string $video_id - YouTube video ID
 */
function video_central_youtube_api_get_video($video_id)
{
    $q = new Video_Central_Youtube_API_Query(1, true);
    $video = $q->get_video($video_id);

    return $video;
}

/**
 * Get details about multiple video IDs.
 *
 * @since 1.2.0
 *
 * @param string $video_ids - YouTube video IDs comma separated or array of video ids
 */
function video_central_youtube_api_get_videos($video_ids)
{
    $q = new Video_Central_Youtube_API_Query(50, true);
    $videos = $q->get_videos($video_ids);

    return $videos;
}

/**
 * Returns a playlist feed.
 *
 * include_categories bool - when true, video categories will be retrieved, if false, they won't
 * query string - the search query
 * page_token - YT API 3 page token for pagination
 * type string - auto or manual
 * playlist_type - one of the following: user, playlist or channel
 *
 * @since 1.2.0
 *
 * @param array $args
 */
function video_central_youtube_api_get_list($args = array())
{
    $defaults = array(
        'playlist_type' => 'playlist',
        // can be auto or manual - will set pagination according to user settings
        'type' => 'manual',
        // if false, YouTube categories won't be retrieved
        'include_categories' => true,
        // the search query
        'query' => '',
        // as of API 3, results pagination is done by tokens
        'page_token' => '',
    );

    $args = wp_parse_args($args, $defaults);
    extract($args, EXTR_SKIP);

    $types = array('user', 'playlist', 'channel');
    if (!in_array($playlist_type, $types)) {
        trigger_error(__('Invalid playlist type. Use as playlist type one of the following: user, playlist or channel.', 'video_central_video'), E_USER_NOTICE);

        return;
    }

    if ('auto' == $type) {
        $per_page = video_central_auto_import_results_per_page();
    } else {
        $per_page = video_central_import_results_per_page();
    }

    $q = new Video_Central_Youtube_API_Query($per_page, $include_categories);

    switch ($playlist_type) {
        case 'playlist':
            $videos = $q->get_playlist($query, $page_token);
        break;
        case 'user':
            $videos = $q->get_user_uploads($query, $page_token);
        break;
        case 'channel':
            $videos = $q->get_channel_uploads($query, $page_token);
        break;
    }

    $page_info = $q->get_list_info();

    return array(
        'videos' => $videos,
        'page_info' => $page_info,
    );
}

/**
 * Checks whether variable is a WP error in first place
 * and second will verifyis the error has YouTube flag on it.
 *
 * @since 1.2.0
 */
function video_central_is_youtube_error($obj)
{
    if (!is_wp_error($obj)) {
        return false;
    }

    $data = $obj->get_error_data();
    if ($data && isset($data['youtube_error'])) {
        return true;
    }

    return false;
}
