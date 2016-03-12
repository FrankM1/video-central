<?php

/**
 *  Vimeo Importer Data class for Video Central.
 *
 * @since 1.0.0
 *
 * @author  Franklin M Gitonga
 */
class Video_Central_Vimeo_ImporterData
{
    /**
     * [$results description].
     *
     * @since 1.0.0
     */
    private $results;

    /**
     * [$total_items description].
     *
     * @since 1.0.0
     */
    private $total_items;

    /**
     * Constructor. Hooks all interactions to initialize the class.
     *
     * @since 1.0.0
     */
    public function __construct($args)
    {
        $defaults = array(
            'source' => 'vimeo', // video source
            'feed' => 'query', // type of feed to retrieve
            'query' => false, // feed query - can contain username, playlist ID or serach query
            'results' => 20, // number of results to retrieve
            'start-index' => 0,
            'response' => 'jsonc', // Vimeo response type
            'order' => 'published', // order
            'language' => 'en',
            'safe' => 'moderate',
            'hd' => false,
            'format' => '1,5',
            'duration' => false,
        );

        $data = wp_parse_args($args, $defaults);

        // if no query is specified, bail out
        if (!$data['query']) {
            return false;
        }

        // ordering or returned results. This needs to be processed
        $data['order'] = $this->order($data['source'], $data['feed'], $data['order']);

        $sources = $this->sources();

        // if sources doesn't exist, bail out
        if (!array_key_exists($data['source'], $sources)) {
            return false;
        }

        $source_data = $sources[ $data['source'] ];

        $vars = array();

        $feed_type = $source_data['feeds'][ $data['feed'] ];
        if (array_key_exists('vars', $feed_type)) {
            foreach ($feed_type['vars'] as $var) {
                if (isset($data[ $var ]) && $data[ $var ]) {
                    $vars[ $source_data['variables'][ $var ]['var'] ] = $data[ $var ];
                }
            }
        } else {
            foreach ($source_data['variables'] as $arg => $var) {
                if (isset($data[ $arg ]) && $data[ $arg ]) {
                    $vars[ $var['var'] ] = $data[ $arg ];
                }
            }
        }

        $source_url = $source_data['url'];
        $source_query = sprintf($source_data['feeds'][$data['feed']]['uri'], $data['query']);
        $full_url = add_query_arg(array($vars), $source_url.$source_query);

        $content = wp_remote_get($full_url);

        if (is_wp_error($content) || 200 != $content['response']['code']) {
            return false;
        }

        $result = json_decode($content['body'], true);

        if (isset($result['data']['items'])) {
            $raw_entries = $result['data']['items'];
        } else {
            $raw_entries = array();
        }

        $entries = array();
        foreach ($raw_entries as $entry) {
            $entries[] = video_central_format_vimeo_video_entry($entry);
        }

        $this->results = $entries;
        $this->total_items = $result['data']['totalItems'];
    }

    public function get_feed()
    {
        return $this->results;
    }

    public function get_total_items()
    {
        return $this->total_items;
    }

    /**
     * Video sources with complete variables and URI.
     *
     * @since 1.0.0
     */
    private function sources()
    {
        $sources = array(
            'vimeo' => array(
                'url' => 'http://gdata.vimeo.com/feeds/api/',
                'variables' => array(
                    'response' => array(
                        'var' => 'alt',
                        'value' => 'jsonc', // atom, json, jsonc
                    ),
                    /*
                     * Video feeds: relevance, published, viewCount, rating
                     * Playlist: position, commentCount, duration, published, reversedPosition, title, viewCount
                     */
                    'order' => array(
                        'var' => 'orderby',
                        'value' => 'published',
                    ),
                    'results' => array(
                        'var' => 'max-results',
                        'value' => false, // no more than 50
                    ),
                    'start-index' => array(
                        'var' => 'start-index',
                        'value' => 0,
                    ),
                    'language' => array(
                        'var' => 'hl',
                        'value' => 'en',
                    ),
                    'safe' => array(
                        'var' => 'safeSearch',
                        'value' => 'moderate',
                    ),
                    /*
                     * String true or false to return only HD videos
                     */
                    'hd' => array(
                        'var' => 'hd',
                        'value' => false,
                    ),
                    'format' => array(
                        'var' => 'format',
                        'value' => '1,5',
                    ),
                    'duration' => array(
                        'var' => 'duration',
                        'value' => 'medium', // short (< 4 min); medium ( > 4min, < 20min ); long ( > 20min )
                    ),
                ),
                'feeds' => array(
                    'user' => array(
                        'uri' => 'users/%1$s/uploads/?v=2',
                        'vars' => array('response', 'results', 'start-index', 'order'),
                    ),
                    'playlist' => array(
                        'uri' => 'playlists/%1$s/?v=2',
                        'vars' => array('response', 'results', 'start-index'),
                    ),
                    'query' => array(
                        'uri' => 'videos?v=2&q=%1$s',
                    ),
                ),
            ),
        );

        return $sources;
    }

    /**
     * Order data.
     *
     * @since 1.0.0
     */
    private function order($source, $feed_type = false, $orderby = false)
    {
        $order = array(
            'vimeo' => array(
                'query' => array('published', 'viewCount', 'relevance', 'rating'),
                'user' => array('published', 'viewCount', 'position', 'commentCount', 'duration', 'reversedPosition', 'title'),
                'playlist' => array('published', 'viewCount', 'position', 'commentCount', 'duration', 'reversedPosition', 'title'),
                'default' => 'published',
            ),
        );

        if (!array_key_exists($source, $order) || !array_key_exists($feed_type, $order[$source])) {
            return false;
        }

        $ord = $order[$source][$feed_type];

        if (!in_array($orderby, $ord)) {
            return $order[$source]['default'];
        }

        return $orderby;
    }
}
