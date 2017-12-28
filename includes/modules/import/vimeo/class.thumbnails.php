<?php

/*  Copyright 2013 Sutherland Boswell  (email : sutherland.boswell@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
class Video_Central_Vimeo_Thumbnails extends Video_Central_Thumbnails_Providers
{
    // Human-readable name of the video provider
    public $service_name = 'Vimeo';

    const service_name = 'Vimeo';

    // Slug for the video provider
    public $service_slug = 'vimeo';

    const service_slug = 'vimeo';

    public $options_section = array(
        'description' => '<p><strong>Optional</strong>: Only required for accessing private videos. <a href="https://developer.vimeo.com/apps">Register an app with Vimeo</a> then fill in the appropriate keys below. Requires cURL to authenticate.</p>',
        'fields' => array(
            'client_id' => array(
                'name' => 'Client ID',
                'type' => 'text',
                'description' => '',
            ),
            'client_secret' => array(
                'name' => 'Client Secret',
                'type' => 'text',
                'description' => '',
            ),
            'access_token' => array(
                'name' => 'Access token',
                'type' => 'text',
                'description' => '',
            ),
            'access_token_secret' => array(
                'name' => 'Access token secret',
                'type' => 'text',
                'description' => '',
            ),
        ),
    );

    public static function register_provider($providers)
    {
        $providers[self::service_slug] = new self();

        return $providers;
    }

    // Regex strings
    public $regexes = array(
        '#<object[^>]+>.+?http://vimeo\.com/moogaloop.swf\?clip_id=([A-Za-z0-9\-_]+)&.+?</object>#s', // Standard Vimeo embed code
        '#(?:https?:)?//player\.vimeo\.com/video/([0-9]+)#', // Vimeo iframe player
        '#\[vimeo id=([A-Za-z0-9\-_]+)]#', // JR_embed shortcode
        '#\[vimeo clip_id="([A-Za-z0-9\-_]+)"[^>]*]#', // Another shortcode
        '#\[vimeo video_id="([A-Za-z0-9\-_]+)"[^>]*]#', // Yet another shortcode
        '#(?:https?://)?(?:www\.)?vimeo\.com/([0-9]+)#', // Vimeo URL
        '#(?:https?://)?(?:www\.)?vimeo\.com/channels/(?:[A-Za-z0-9]+)/([0-9]+)#', // Channel URL
    );

    // Thumbnail URL
    public function get_thumbnail_url($id)
    {
        $image = null;

        /* Get our settings
        $client_id = ( isset( $this->options['client_id'] ) && $this->options['client_id'] != '' ? $this->options['client_id'] : false );
        $client_secret = ( isset( $this->options['client_secret'] ) && $this->options['client_secret'] != '' ? $this->options['client_secret'] : false );
        $access_token = ( isset( $this->options['access_token'] ) && $this->options['access_token'] != '' ? $this->options['access_token'] : false );
        $access_token_secret = ( isset( $this->options['access_token_secret'] ) && $this->options['access_token_secret'] != '' ? $this->options['access_token_secret'] : false );
        // If API credentials are entered, use the API
        if ( $client_id && $client_secret && $access_token && $access_token_secret ) {
            $vimeo = new phpVimeo( $this->options['client_id'], $this->options['client_secret'] );
            $vimeo->setToken( $this->options['access_token'], $this->options['access_token_secret'] );
            $response = $vimeo->call('vimeo.videos.getThumbnailUrls', array('video_id'=>$id));
            $result = $response->thumbnails->thumbnail[count($response->thumbnails->thumbnail)-1]->_content;
        } else {
            $request = "http://vimeo.com/api/oembed.json?url=http%3A//vimeo.com/$id";
            $response = wp_remote_get( $request, array( 'sslverify' => false ) );
            if( is_wp_error( $response ) ) {
                $result = $this->construct_info_retrieval_error( $request, $response );
            } elseif ( $response['response']['code'] == 404 ) {
                $result = new WP_Error( 'vimeo_info_retrieval', __( 'The Vimeo endpoint located at <a href="' . $request . '">' . $request . '</a> returned a 404 error.<br />Details: ' . $response['response']['message'], 'video-thumbnails' ) );
            } elseif ( $response['response']['code'] == 403 ) {
                $result = new WP_Error( 'vimeo_info_retrieval', __( 'The Vimeo endpoint located at <a href="' . $request . '">' . $request . '</a> returned a 403 error.<br />This can occur when a video has embedding disabled or restricted to certain domains. Try entering API credentials in the provider settings.', 'video-thumbnails' ) );
            } else {
                $result = json_decode( $response['body'] );
                $result = $result->thumbnail_url;
            }
        }
        return $result; */

        $url = 'http://vimeo.com/api/v2/video/'.$id.'.php';

        $response = wp_remote_get($url, array('timeout' => 30));

        if (!is_wp_error($response) && $response['response']['code'] == '200') {

            $xml = wp_remote_retrieve_body($response);

            $image_data = maybe_unserialize($xml);

            if ($image_data && is_array($image_data)) {
                $image = $image_data[0]['thumbnail_large'];
            }

            return $image;
        } else {
            return false;
        }
    }

    // Test cases
    public static function get_test_cases()
    {
        return array(
            array(
                'markup' => '<iframe src="http://player.vimeo.com/video/41504360" width="500" height="281" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>',
                'expected' => 'http://i.vimeocdn.com/video/287850781_1280.jpg',
                'expected_hash' => '5388e0d772b827b0837444b636c9676c',
                'name' => __('iFrame Embed', 'video_central'),
            ),
            array(
                'markup' => '<object width="500" height="281"><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id=41504360&amp;force_embed=1&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=1&amp;color=00adef&amp;fullscreen=1&amp;autoplay=0&amp;loop=0" /><embed src="http://vimeo.com/moogaloop.swf?clip_id=41504360&amp;force_embed=1&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=1&amp;color=00adef&amp;fullscreen=1&amp;autoplay=0&amp;loop=0" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="500" height="281"></embed></object>',
                'expected' => 'http://i.vimeocdn.com/video/287850781_1280.jpg',
                'expected_hash' => '5388e0d772b827b0837444b636c9676c',
                'name' => __('Flash Embed', 'video_central'),
            ),
            array(
                'markup' => 'https://vimeo.com/channels/soundworkscollection/44520894',
                'expected' => 'http://i.vimeocdn.com/video/313130530_1280.jpg',
                'expected_hash' => '32f742bbe980e5d98d8aa0256026b459',
                'name' => __('Channel URL', 'video_central'),
            ),
        );
    }
}

// Add to provider array
add_filter('video_central_thumbnail_providers', array('Video_Central_Vimeo_Thumbnails', 'register_provider'));
