<?php

/**
 * Query Vimeo for single video details.
 *
 * @since 1.0.0
 *
 * @param string $video_id
 * @param string $source
 */
function video_central_query_vimeo_video($video_id, $source = 'vimeo')
{
    $sources = array(
        'vimeo' => 'http://gdata.vimeo.com/feeds/api/videos/%s?v=2&alt=jsonc',
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
function video_central_format_vimeo_video_entry($raw_entry)
{

    // playlists have individual items stored under key video
    if (array_key_exists('video', $raw_entry)) {
        $raw_entry = $raw_entry['video'];
    }

    // permissions
    $entry = array();
    $permissions = array();
    foreach ($raw_entry['accessControl'] as $k => $p) {
        $permissions[ $k ] = 'allowed' == $p;
    }

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
