<?php

class Video_Central_Youtube_API_Query
{
    /**
     * YouTube API server key.
     */
    private $server_key;

    /**
     * YouTube OAuth refresh token.
     */
    private $oauth_details;

    /**
     * YouTube API query base.
     */
    private $base = 'https://www.googleapis.com/youtube/v3/';

    /**
     * Results per page.
     */
    private $per_page = 10;

    /**
     * Store list statistics:
     * -.
     */
    private $list_info = array(
        'next_page' => '', // stores next page token for searches of playlists
        'prev_page' => '', // stores previous page token for searches or playlists
        'total_results' => 0, // stores total results from search or playlist
        'page_results' => 0, // stores current page results
    );

    private $include_categories = false;
    private $request_units = 0;

    /**
     * Constructor, sets up a few variables.
     */
    public function __construct($per_page = false, $include_categories = false)
    {
        $youtube_api_key = video_central_youtube_api_key();
        if (video_central_youtube_api_key()) {
            $this->server_key = $youtube_api_key;
        }

        // get registered OAuth details
        $this->oauth_details = video_central_youtube_api_oauth_details();

        if ($per_page) {
            $this->per_page = absint($per_page);
        }

        $this->include_categories = (bool) $include_categories;
    }

    /**
     * Performs a search on YouTube.
     *
     * @param string $query      - the search query
     * @param string $page_token - next/previous page token
     * @param string $order      - results ordering ( values: date, rating, relevance, title or viewCount )
     *
     * @return - array of videos or WP_Error if something went wrong
     */
    public function search($query, $page_token = '', $args = array())
    {
        // get videos feed
        $videos = $this->_query_videos('search', $query, $page_token, $args);

        return $videos;
    }

    /**
     * Get videos from a playlist from YouTube.
     *
     * @param string $query      - YouTube playlist ID
     * @param string $page_token - next/previous page token
     *
     * @return - array of videos or WP_Error is something went wrong
     */
    public function get_playlist($query, $page_token = '')
    {
        $videos = $this->_query_videos('playlist', $query, $page_token);

        return $videos;
    }

    /**
     * Get videos from a channel from YouTube.
     *
     * @param string $query      - YouTube channel ID
     * @param string $page_token - next/previous page token
     *
     * @return - array of videos or WP_Error is something went wrong
     */
    public function get_channel_uploads($query, $page_token = '')
    {
        $url = $this->_get_endpoint('channel_id', $query);
        if (is_wp_error($url)) {
            return $url;
        }
        $channel = $this->_make_request($url);
        // check for errors
        if (is_wp_error($channel)) {
            return $channel;
        }

        if (isset($channel['items'][0]['contentDetails']['relatedPlaylists']['uploads'])) {
            $playlist = $channel['items'][0]['contentDetails']['relatedPlaylists']['uploads'];
        } else {
            // return WP error is playlist ID could not be found
            return $this->_generate_error('youtube_api_channel_playlist_param_missing', __('User uploads playlist ID could not be found in YouTube API channel query response.', 'video_central'));
        }

        $videos = $this->get_playlist($playlist, $page_token);

        return $videos;
    }

    /**
     * Get videos from a user from YouTube.
     *
     * @param string $query      - YouTube user ID
     * @param string $page_token - next/previous page token
     *
     * @return - array of videos or WP_Error is something went wrong
     */
    public function get_user_uploads($query, $page_token = '')
    {
        $url = $this->_get_endpoint('user_channel', $query);
        if (is_wp_error($url)) {
            return $url;
        }
        $user = $this->_make_request($url);

        // check for errors
        if (is_wp_error($user)) {
            return $user;
        }

        if (isset($user['items'][0]['contentDetails']['relatedPlaylists']['uploads'])) {
            $playlist = $user['items'][0]['contentDetails']['relatedPlaylists']['uploads'];
        } else {
            // return WP error is playlist ID could not be found
            return $this->_generate_error('youtube_api_user_playlist_param_missing', __('User uploads playlist ID could not be found in YouTube API user query response.', 'video_central'));
        }

        $videos = $this->get_playlist($playlist, $page_token);

        return $videos;
    }

    /**
     * Get details for a single video ID.
     *
     * @param string $query - YouTube video ID
     *
     * @return - array of videos or WP_Error is something went wrong
     */
    public function get_video($query)
    {
        // make request for video details
        $url = $this->_get_endpoint('videos', $query);
        if (is_wp_error($url)) {
            return $url;
        }
        $result = $this->_make_request($url);

        // check for errors
        if (is_wp_error($result)) {
            return $result;
        }

        $videos = $this->_format_videos($result);

        return $videos[0];
    }

    /**
     * Get details for multiple video IDs.
     *
     * @param string $query - YouTube video IDs comma separated or array of video ids
     *
     * @return - array of videos or WP_Error is something went wrong
     */
    public function get_videos($query)
    {
        // query can be a list of comma separated ids or array of ids
        if (is_array($query)) {
            $query = implode(',', $query);
        }
        // make request for video details
        $url = $this->_get_endpoint('videos', $query);
        if (is_wp_error($url)) {
            return $url;
        }
        $result = $this->_make_request($url);

        // check for errors
        if (is_wp_error($result)) {
            return $result;
        }

        $videos = $this->_format_videos($result);

        return $videos;
    }

    /**
     * Get video categories based on IDs.
     *
     * @param string $query - single ID or ids separated by comma
     */
    public function get_categories($query)
    {
        // make request
        $url = $this->_get_endpoint('categories', $query);
        if (is_wp_error($url)) {
            return $url;
        }
        $result = $this->_make_request($url);

        // check for errors
        if (is_wp_error($result)) {
            return $result;
        }

        $categories = array();
        foreach ($result['items'] as $category) {
            $categories[ $category['id'] ] = $category['snippet']['title'];
        }

        return $categories;
    }

    /**
     * Get all playlists created by the user that entered the OAuth details.
     *
     * @param string $page_token - next/previous page token
     *
     * @return - array of playlists or WP_Error is something went wrong
     */
    public function get_user_playlists($page_token = '')
    {
        // make request
        $url = $this->_get_endpoint('me_playlists', 'empty', $page_token);
        if (is_wp_error($url)) {
            return $url;
        }

        $result = $this->_make_request($url, true);
        // check for errors
        if (is_wp_error($result)) {
            return $result;
        }

        // populate $this->list_info with the results returned from query
        $this->_set_query_info($result);

        $playlists = array();
        $statuses = array('public', 'unlisted');
        foreach ($result['items'] as $item) {
            if (!in_array($item['status']['privacyStatus'], $statuses)) {
                continue;
            }

            $playlists[] = array(
                'playlist_id' => $item['id'],
                'channel_id' => $item['snippet']['channelId'],
                'title' => $item['snippet']['title'],
                'description' => $item['snippet']['description'],
                'status' => $item['status']['privacyStatus'],
                'videos' => $item['contentDetails']['itemCount'],
            );
        }

        return $playlists;
    }

    /**
     * Get all channels created by the user that entered the OAuth details.
     *
     * @param string $page_token - next/previous page token
     *
     * @return - array of channels or WP_Error is something went wrong
     */
    public function get_user_channels($page_token = '')
    {
        // make request
        $url = $this->_get_endpoint('me_channels', 'empty', $page_token);
        if (is_wp_error($url)) {
            return $url;
        }

        $result = $this->_make_request($url, true);
        // check for errors
        if (is_wp_error($result)) {
            return $result;
        }

        // populate $this->list_info with the results returned from query
        $this->_set_query_info($result);

        $channels = array();
        foreach ($result['items'] as $item) {
            $channels[] = array(
                'channel_id' => $item['id'],
                'title' => $item['snippet']['title'],
                'description' => $item['snippet']['description'],
                'status' => $item['status']['privacyStatus'],
                'videos' => $item['statistics']['videoCount'],
            );
        }

        return $channels;
    }

    /**
     * Get all subscriptions for the user that entered the OAuth details.
     *
     * @param string $page_token - next/previous page token
     *
     * @return - array of playlists or WP_Error is something went wrong
     */
    public function get_user_subscriptions($page_token = '')
    {
        // make request
        $url = $this->_get_endpoint('me_subscriptions', 'empty', $page_token);
        if (is_wp_error($url)) {
            return $url;
        }

        $result = $this->_make_request($url, true);
        // check for errors
        if (is_wp_error($result)) {
            return $result;
        }

        // populate $this->list_info with the results returned from query
        $this->_set_query_info($result);

        $channels = array();
        foreach ($result['items'] as $item) {
            $channels[] = array(
                'channel_id' => $item['snippet']['resourceId']['channelId'],
                'title' => $item['snippet']['title'],
                'description' => $item['snippet']['description'],
                'videos' => $item['contentDetails']['totalItemCount'],
            );
        }

        return $channels;
    }

    /**
     * Returns $this->list_info for query details.
     */
    public function get_list_info()
    {
        return $this->list_info;
    }

    /**
     * Queries videos based on a specific action.
     *
     * @param string $action     - search, playlist
     * @param string $query      - the query
     * @param string $page_token - next/previous page token returned by API
     * @param string $order      - results order
     *
     * @return - array of videos or WP_Error is something went wrong
     */
    private function _query_videos($action, $query, $page_token = '', $args = array())
    {
        $url = $this->_get_endpoint($action, $query, $page_token, $args);
        if (is_wp_error($url)) {
            return $url;
        }
        $result = $this->_make_request($url);

        // check for errors
        if (is_wp_error($result)) {
            return $result;
        }

        // populate $this->list_info with the results returned from query
        $this->_set_query_info($result);

        // get videos details
        $ids = array();
        foreach ($result['items'] as $video) {
            $key = 'id';
            switch ($action) {
                case 'playlist':
                    $key = 'contentDetails';
                break;
            }

            $ids[] = $video[ $key ]['videoId'];
        }
        // make request for video details
        $url = $this->_get_endpoint('videos', implode(',', $ids));
        if (is_wp_error($url)) {
            return $url;
        }
        $result = $this->_make_request($url);

        // check for errors
        if (is_wp_error($result)) {
            return $result;
        }

        $videos = $this->_format_videos($result);

        return $videos;
    }

    /**
     * Used to set the pagination details from $this->list_info.
     *
     * @param array $result - the result returned by YouTube API
     */
    private function _set_query_info($result)
    {
        // set default to empty
        $list_info = array(
            'next_page' => '', // stores next page token for searches of playlists
            'prev_page' => '', // stores previous page token for searches or playlists
            'total_results' => 0, // stores total results from search or playlist
            'page_results' => 0, // stores current page results
        );

        // set next page token if any
        if (isset($result['nextPageToken'])) {
            $list_info['next_page'] = $result['nextPageToken'];
        }
        // set prev page token if any
        if (isset($result['prevPageToken'])) {
            $list_info['prev_page'] = $result['prevPageToken'];
        }
        // set total results
        if (isset($result['pageInfo']['totalResults'])) {
            $list_info['total_results'] = $result['pageInfo']['totalResults'];
        }
        // set page results
        if (isset($result['pageInfo']['resultsPerPage'])) {
            $list_info['page_results'] = $result['pageInfo']['resultsPerPage'];
        }

        $this->list_info = $list_info;
    }

    /**
     * Arranges videos into a generally accepted format
     * to be used into the plugin.
     */
    private function _format_videos($result)
    {
        $videos = array();
        $categories = array();

        foreach ($result['items'] as $video) {
            $videos[] = array(
                'video_id' => $video['id'],
                // store channel ID to get uploader name at a later time if needed
                'channel_id' => $video['snippet']['channelId'],
                'uploader_name' => '',
                'uploader' => '',
                'published' => $video['snippet']['publishedAt'],
                'title' => $video['snippet']['title'],
                'description' => $video['snippet']['description'],
                // category name needs to be retrieved based on category ID stored here
                'category_id' => $video['snippet']['categoryId'],
                'category' => '',
                'duration' => $this->_iso_to_timestamp($video['contentDetails']['duration']),
                'iso_duration' => $video['contentDetails']['duration'],
                // store video definition (sd, hd, etc)
                'definition' => $video['contentDetails']['definition'],
                'thumbnails' => (isset($video['snippet']['thumbnails']) ? $video['snippet']['thumbnails'] : array()),
                'stats' => array(
                    // rating no longer available in API V3
                    'rating' => 0,
                    'rating_count' => 0,
                    'comments' => '',
                    'comments_feed' => '',
                    'views' => $video['statistics']['viewCount'],
                    'likes' => $video['statistics']['likeCount'],
                    'dislikes' => $video['statistics']['dislikeCount'],
                    'favourite' => $video['statistics']['favoriteCount'],
                ),
                'privacy' => array(
                    'status' => $video['status']['privacyStatus'],
                    'embeddable' => $video['status']['embeddable'],
                    'license' => (isset($video['status']['license']) ? $video['status']['license'] : false),
                ),
            );
            $categories[] = $video['snippet']['categoryId'];
        }

        // query categories ids if they should be included
        if ($this->include_categories) {
            if ($categories) {
                $categories = array_unique($categories);
                $cat = $this->get_categories(implode(',', $categories));
                if (!is_wp_error($cat)) {
                    foreach ($videos as $key => $video) {
                        if (array_key_exists($video['category_id'], $cat)) {
                            $videos[ $key ]['category'] = $cat[ $video['category_id'] ];
                        }
                    }
                }
            }
        }

        return $videos;
    }

    /**
     * Makes a cURL request and stores unserialized response in
     * $this->api_response variable.
     */
    private function _make_request($url, $oauth = false)
    {
        $headers = array();

        if ($oauth) {
            $token = $this->_get_bearer_token();
            if (is_wp_error($token)) {
                return $token;
            }
            $headers['Authorization'] = 'Bearer '.$token;
        }

        // make the request
        $remote_args = array( 'timeout' => 60, 'headers' => $headers);
        $response = wp_remote_get( $url, $remote_args );

        // if something went wrong, return the error
        if (is_wp_error($response)) {
            return $response;
        }

        /*
         * Action that runs every time a request to YouTube API is made
         * @var $endpoint - YouTube endpoint
         * @var $request_units - number of units consumed by the request
         */
        do_action('video_central_youtube_api_query', $url, $this->request_units);

        // requests should be returned having code 200
        if (200 != wp_remote_retrieve_response_code($response)) {
            $body = json_decode(wp_remote_retrieve_body($response), true);
            $youtube_error = '';
            if (isset($body['error'])) {
                $youtube_error = $body['error']['errors'][0]['message'].'( code : '.$body['error']['errors'][0]['reason'].' ).';
            } else {
                $youtube_error = 'unknown.';
            }

            $error = sprintf(__('YouTube API returned a %s error code. Error returned is: %s', 'video_central'), wp_remote_retrieve_response_code($response), $youtube_error);

            return $this->_generate_error('youtube_api_error_code', $error, $body);
        }

        // decode the result
        $result = json_decode(wp_remote_retrieve_body($response), true);

        // check for empty result
        if (isset($result['pageInfo']['totalResults'])) {
            if (0 == $result['pageInfo']['totalResults']) {
                return $this->_generate_error('youtube_query_results_empty', __('Query to YouTube API returned no results.', 'video_central'));
            }
        }
        if ((isset($result['items']) && !$result['items']) || !isset($result['items'])) {
            return $this->_generate_error('youtube_query_results_empty', __('Query to YouTube API returned no results.', 'video_central'));
        }

        return $result;
    }

    /**
     * Based on $action and $query, create the endpoint URL to
     * interogate YouTube API.
     */
    private function _get_endpoint($action, $query = '', $page_token = '', $args = array())
    {
        // don't allow empty queries
        if (empty($query)) {
            /*
             * DO NOT USE HELPER $this->_generate_error().
             * This isn't a response generated by YouTube, it's a plugin error that shouldn't count as
             * YouTube error.
             */
            return new WP_Error('youtube_api_query_empty', __('No query specified.', 'video_central'));
        }
        // API3 will always ask for server key, make sure it isn't empty
        if (empty($this->server_key)) {
            /*
             * DO NOT USE HELPER $this->_generate_error().
             * This isn't a response generated by YouTube, it's a plugin error that shouldn't count as
             * YouTube error.
             */
            return new WP_Error('youtube_server_key_empty', __('You must enter your YouTube server key in plugins Settings page under tab API & License.', 'video_central'));
        }

        $defaults = array(
            'order' => 'date',
            'duration' => 'any',
            'embed' => 'any',
        );
        extract(wp_parse_args($args, $defaults), EXTR_SKIP);

        $actions = array(
            // https://developers.google.com/youtube/v3/docs/search/list
            'search' => array(
                'action' => 'search',
                'params' => array(
                    'q' => urlencode($query),
                    'part' => 'snippet',
                    'type' => 'video',
                    'pageToken' => $page_token,
                    'maxResults' => $this->per_page,
                    /*
                     * order param can have value:
                     * - date (newest to oldest)
                     * - rating (high to low)
                     * - relevance (default in API)
                     * - title (alphabetically by title)
                     * - viewCount (high to low)
                     */
                    'order' => $order,
                    'videoDuration' => $duration,
                    'videoEmbeddable' => $embed,
                ),
                // YouTube API quota
                'quota' => 100,
            ),
            // https://developers.google.com/youtube/v3/docs/playlistItems/list
            'playlist' => array(
                'action' => 'playlistItems',
                'params' => array(
                    'playlistId' => urlencode($query),
                    'part' => 'contentDetails',
                    'pageToken' => $page_token,
                    'maxResults' => $this->per_page,
                ),
                // YouTube API quota
                'quota' => 3,
            ),
            // https://developers.google.com/youtube/v3/docs/videos/list
            'videos' => array(
                'action' => 'videos',
                'params' => array(
                    'id' => $query,
                    'part' => 'contentDetails,id,snippet,statistics,status',
                ),
                // YouTube API quota
                'quota' => 9,
            ),
            // https://developers.google.com/youtube/v3/docs/channels/list
            'user_channel' => array(
                'action' => 'channels',
                'params' => array(
                    'forUsername' => urlencode($query),
                    'part' => 'contentDetails',
                    'maxResults' => $this->per_page,
                    'page_token' => '',
                ),
                // YouTube API quota
                'quota' => 3,
            ),
            // https://developers.google.com/youtube/v3/docs/channels/list
            'channel_id' => array(
                'action' => 'channels',
                'params' => array(
                    'id' => urlencode($query),
                    'part' => 'contentDetails',
                    'maxResults' => $this->per_page,
                    'page_token' => '',
                ),
                // YouTube API quota
                'quota' => 3,
            ),
            // https://developers.google.com/youtube/v3/docs/videoCategories/list
            'categories' => array(
                'action' => 'videoCategories',
                'params' => array(
                    'id' => $query,
                    'part' => 'snippet',
                ),
                // YouTube API quota
                'quota' => 3,
            ),

            // Authenticated requests - these require OAuth credentials

            //https://developers.google.com/youtube/v3/docs/playlists/list
            'me_playlists' => array(
                'action' => 'playlists',
                'params' => array(
                    'part' => 'contentDetails,id,snippet,status',
                    'mine' => 'true',
                    'pageToken' => $page_token,
                    'maxResults' => $this->per_page,
                ),
                'quota' => 7,
                'authorization' => 'bearer',
            ),
            // https://developers.google.com/youtube/v3/docs/channels/list
            'me_channels' => array(
                'action' => 'channels',
                'params' => array(
                    'part' => 'contentDetails,id,snippet,statistics,status,topicDetails',
                    'mine' => 'true',
                    'pageToken' => $page_token,
                    'maxResults' => $this->per_page,
                ),
                'quota' => 11,
                'authorization' => 'bearer',
            ),
            // https://developers.google.com/youtube/v3/docs/subscriptions/list
            'me_subscriptions' => array(
                'action' => 'subscriptions',
                'params' => array(
                    'part' => 'contentDetails,id,snippet',
                    'mine' => 'true',
                    'pageToken' => $page_token,
                    'maxResults' => $this->per_page,
                ),
                'quota' => 5,
                'authorization' => 'bearer',
            ),
        );

        if (array_key_exists($action, $actions)) {
            $youtube_action = $actions[ $action ]['action'];
            $params = $actions[ $action ]['params'];
            $params['key'] = $this->server_key;
            $endpoint = $this->base.$youtube_action.'/?'.http_build_query($params);
            // set up the number of units used by the request
            $this->request_units = $actions[ $action ]['quota'];

            return $endpoint;
        } else {
            /*
             * DO NOT USE HELPER $this->_generate_error().
             * This isn't a response generated by YouTube, it's a script error that shouldn't count as
             * YouTube error.
             */
            return new WP_Error('unknown_youtube_api_action', sprintf(__('Action %s could not be found to query YouTube.', $action), 'video_central'));
        }
    }

    /**
     * Returns the current token.
     */
    private function _get_bearer_token()
    {
        if (!isset($this->oauth_details['token'])) {
            return new WP_Error('video_central_oauth_token_missing', __('Please visit plugin Settings page and setup the OAuth details to grant permission for the plugin to your YouTube account.', 'video_central'));
        }
        if (empty($this->oauth_details['client_id']) || empty($this->oauth_details['client_secret'])) {
            return new WP_Error('video_central_oauth_no_credentials', __('Please enter your OAuth credentials in order to be able to query your YouTube account.', 'video_central'));
        }
        // the token details
        $token = $this->oauth_details['token'];
        if (is_wp_error($token)) {
            return $token;
        }
        if (empty($token['value'])) {
            return new WP_Error('video_central_oauth_token_empty', __('Please grant permission for the plugin to access your YouTube account.', 'video_central'));
        }

        $expired = time() >= ($token['valid'] + $token['time']);
        if ($expired) {
            $token = video_central_refresh_oauth_token();
            $this->oauth_details['token'] = $token;
        }

        if (is_wp_error($token)) {
            return $token;
        }

        return $token['value'];
    }

    /**
     * Converts ISO time ( ie: PT1H30M55S ) to timestamp.
     *
     * @param string $iso_time - ISO time
     *
     * @return int - seconds
     */
    private function _iso_to_timestamp($iso_time)
    {
        preg_match_all('|([0-9]+)([a-z])|Ui', $iso_time, $matches);
        if (isset($matches[2])) {
            $seconds = 0;
            foreach ($matches[2] as $key => $unit) {
                $multiply = 1;
                switch ($unit) {
                    case 'M':
                        $multiply = 60;
                    break;
                    case 'H':
                        $multiply = 3600;
                    break;
                }
                $seconds += $multiply * $matches[1][ $key ];
            }
        }

        return $seconds;
    }

    /**
     * Generates and returns a WP_Error.
     *
     * @param string $code
     * @param string $message
     * @param mixed  $data
     */
    private function _generate_error($code, $message, $data = false)
    {
        $error = new WP_Error($code, $message, array('youtube_error' => true, 'data' => $data));

        return $error;
    }
}
