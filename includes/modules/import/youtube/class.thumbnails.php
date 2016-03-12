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

class Video_Central_YouTube_Thumbnails extends Video_Central_Thumbnails_Providers
{
    // Human-readable name of the video provider
    public $service_name = 'YouTube';

    /**
     * service_name.
     */
    const service_name = 'YouTube';

    // Slug for the video provider
    public $service_slug = 'youtube';

    /**
     * service_slug.
     */
    const service_slug = 'youtube';

    /**
     * __construct description].
     */
    public function __construct()
    {
    }

    /**
     * [register_provider description].
     *
     * @param [type] $providers [description]
     *
     * @return [type] [description]
     */
    public static function register_provider($providers)
    {
        $providers[self::service_slug] = new self();

        return $providers;
    }

    // Regex strings
    public $regexes = array(
        '#(?:https?:)?//www\.youtube(?:\-nocookie)?\.com/(?:v|e|embed)/([A-Za-z0-9\-_]+)#', // Comprehensive search for both iFrame and old school embeds
        '#(?:https?(?:a|vh?)?://)?(?:www\.)?youtube(?:\-nocookie)?\.com/watch\?.*v=([A-Za-z0-9\-_]+)#', // Any YouTube URL. After http(s) support a or v for Youtube Lyte and v or vh for Smart Youtube plugin
        '#(?:https?(?:a|vh?)?://)?youtu\.be/([A-Za-z0-9\-_]+)#', // Any shortened youtu.be URL. After http(s) a or v for Youtube Lyte and v or vh for Smart Youtube plugin
        '#<div class="lyte" id="([A-Za-z0-9\-_]+)"#', // YouTube Lyte
        '#data-youtube-id="([A-Za-z0-9\-_]+)"#', // LazyYT.js
    );

    // Thumbnail URL
    public function get_thumbnail_url($id)
    {
        $maxres = 'http://img.youtube.com/vi/' . $id . '/maxresdefault.jpg';
        $response = wp_remote_head( $maxres );
        if ( !is_wp_error( $response ) && $response['response']['code'] == '200' ) {
            $result = $maxres;
        } else {
            $result = 'http://img.youtube.com/vi/' . $id . '/0.jpg';
        }
        return $result;
    }
}

// Add to provider array
add_filter('video_central_thumbnail_providers', array('Video_Central_YouTube_Thumbnails', 'register_provider'));
