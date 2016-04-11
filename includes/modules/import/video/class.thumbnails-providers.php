<?php

/*  Copyright 2014 Sutherland Boswell  (email : sutherland.boswell@gmail.com)

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

class Video_Central_Thumbnails_Providers
{
    public $options = array();

    public function __construct()
    {

        // If options are defined add the settings section
        if (isset($this->options_section)) {
            add_action('admin_init', array(&$this, 'initialize_options'));
        }

        // Get current settings for this provider
        $options = get_option('video_thumbnails');

        if (isset($options['providers'][$this->service_slug])) {
            $this->options = $options['providers'][$this->service_slug];
        }
    }

    public function initialize_options()
    {
        add_settings_section($this->service_slug.'_provider_settings_section', $this->service_name.' Settings', array(&$this, 'settings_section_callback'), 'video_thumbnails_providers');

        foreach ($this->options_section['fields'] as $key => $value) {
            add_settings_field($key, $value['name'], array(&$this, $value['type'].'_setting_callback'), 'video_thumbnails_providers', $this->service_slug.'_provider_settings_section', array('slug' => $key, 'description' => $value['description']));
        }
    }

    public function settings_section_callback()
    {
        echo $this->options_section['description'];
    }

    public function text_setting_callback($args)
    {
        $value = (isset($this->options[$args['slug']]) ? $this->options[$args['slug']] : '');
        $html = '<input type="text" id="'. esc_attr( $args['slug'] ) .'" name="video_thumbnails[providers]['. esc_attr( $this->service_slug ).']['. esc_attr( $args['slug'] ).']" value="'. esc_attr( $value ).'"/>';
        $html .= '<label for="'.$args['slug'].'"> '.$args['description'].'</label>';
        echo $html;
    }

    public function scan_for_thumbnail($markup, $video_id = null)
    {
        if ($video_id) {
            return $this->get_thumbnail_url($video_id);
        } else {
            foreach ($this->regexes as $regex) {
                if (preg_match($regex, $markup, $matches)) {
                    return $this->get_thumbnail_url($matches[1]);
                }
            }
        }
    }

    public function scan_for_videos($markup, $video_id = null)
    {
        $videos = array();

        if ($video_id) {
            $videos = array(
                0 => array(
                    0 => $video_id,
                    1 => null,
                ),
            );
        } else {
            foreach ($this->regexes as $regex) {
                if (preg_match_all($regex, $markup, $matches, PREG_OFFSET_CAPTURE)) {
                    $videos = array_merge($videos, $video_id);
                }
            }
        }

        return $videos;
    }
}
