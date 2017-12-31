<?php

/**
 * Video Central's Visual Composer Integration.
 *
 * @since 1.0.0
 */

/**
 * Map shortcodes and parameters to the visual composer.
 *
 * @since 1.0.0
 */
class Video_Central_Map_Shortcode
{
    public function __construct()
    {
        add_action('vc_before_init', array($this, 'video'));
        add_action('vc_before_init', array($this, 'video_central_index'));
        add_action('vc_before_init', array($this, 'video_central_single_video'));
        add_action('vc_before_init', array($this, 'video_central_categories'));
        add_action('vc_before_init', array($this, 'video_central_single_category'));
        add_action('vc_before_init', array($this, 'video_central_tags'));
        add_action('vc_before_init', array($this, 'video_central_single_tag'));
        add_action('vc_before_init', array($this, 'video_central_search'));
        add_action('vc_before_init', array($this, 'video_central_search_form'));
        add_action('vc_before_init', array($this, 'video_central_view'));
        add_action('vc_before_init', array($this, 'video_central_slider_grid'));
    }

    public function video()
    {
        $params[] = array(
            'type' => 'textfield',
            'heading' => __('Width', 'video_central'),
            'param_name' => 'width',
            'admin_label' => true,
            'std' => 600,
        );

        $params[] = array(
            'type' => 'textfield',
            'heading' => __('Height', 'video_central'),
            'param_name' => 'height',
            'admin_label' => true,
            'std' => 500,
        );

        $params[] = array(
            'type' => 'dropdown',
            'heading' => __('Type', 'video_central'),
            'param_name' => 'type',
            'admin_label' => true,
            'value' => array(
                'lightbox' => __('Popup', 'video_central'),
                'slider' => __('Slider', 'video_central'),
            ),
            'std' => 'slider',
        );

        $params[] = array(
            'type' => 'dropdown',
            'heading' => __('Type', 'video_central'),
            'param_name' => 'columns',
            'admin_label' => true,
            'value' => array(
                '2' => __('2', 'video_central'),
                '3' => __('3', 'video_central'),
                '4' => __('4', 'video_central'),
            ),
            'std' => '3',
        );

        $params[] = array(
            'type' => 'textfield',
            'heading' => __('Classes', 'video_central'),
            'param_name' => 'classes',
            'admin_label' => true,
            'std' => '',
        );

        $map_parameters = array(
            'name' => __('Video', 'video_central'),
            'base' => 'video',
            'icon' => 'icon-wpb-separator',
            'allowed_container_element' => 'vc_row',
            'category' => __('Video Central Elements', 'video_central'),
            'description' => __('Display latest videos', 'video_central'),
            'params' => $params,
        );

        vc_map($map_parameters);
    }

    public function video_central_index()
    {

        // Posts Latest
        $map_parameters = array(
            'name' => __('Video Index', 'video_central'),
            'base' => 'video-central-index',
            'icon' => 'icon-wpb-separator',
            'allowed_container_element' => 'vc_row',
            'category' => __('Video Central Elements', 'video_central'),
            'description' => __('Display Video Central Index', 'video_central'),
            'params' => array(
                array(
                    'type' => 'textfield',
                    'heading' => __('Classes', 'video_central'),
                    'param_name' => 'classes',
                    'admin_label' => true,
                    'std' => '',
                ),
            ),
        );

        vc_map($map_parameters);
    }

    public function video_central_single_video()
    {
        $map_parameters = array(
            'name' => __('Single Video', 'video_central'),
            'base' => 'video-central-single-video',
            'icon' => 'icon-wpb-separator',
            'allowed_container_element' => 'vc_row',
            'category' => __('Video Central Elements', 'video_central'),
            'description' => __('Display Video Central Index', 'video_central'),
            'params' => array(
                array(
                    'type' => 'dropdown',
                    'heading' => __('Video ID', 'video_central'),
                    'param_name' => 'id',
                    'admin_label' => true,
                    'value' => radium_builder_get_video_selection_data(),
                    'std' => '',
                ),
                array(
                    'type' => 'textfield',
                    'heading' => __('Classes', 'video_central'),
                    'param_name' => 'classes',
                    'admin_label' => true,
                    'std' => '',
                ),
            ),
        );

        vc_map($map_parameters);
    }

    public function video_central_categories()
    {
        $map_parameters = array(
            'name' => __('Video Categories List', 'video_central'),
            'base' => 'video-central-video-categories',
            'icon' => 'icon-wpb-separator',
            'allowed_container_element' => 'vc_row',
            'category' => __('Video Central Elements', 'video_central'),
            'description' => __('Display a list of Video Categories', 'video_central'),
            'params' => array(
                array(
                    'type' => 'textfield',
                    'heading' => __('Maximum number of categories to show', 'video_central'),
                    'param_name' => 'show_count',
                    'admin_label' => true,
                    'std' => '',
                ),
                array(
                    'type' => 'textfield',
                    'heading' => __('Classes', 'video_central'),
                    'param_name' => 'classes',
                    'admin_label' => true,
                    'std' => '',
                ),
            ),
        );

        vc_map($map_parameters);
    }

    public function video_central_single_category()
    {
        $map_parameters = array(
            'name' => __('Video Category', 'video_central'),
            'base' => 'video-central-single-category',
            'icon' => 'icon-wpb-separator',
            'allowed_container_element' => 'vc_row',
            'category' => __('Video Central Elements', 'video_central'),
            'description' => __('Display a list of videos from a Video Category', 'video_central'),
            'params' => array(
                array(
                    'type' => 'dropdown',
                    'heading' => __('Category ID', 'video_central'),
                    'param_name' => 'id',
                    'admin_label' => true,
                    'value' => video_central_vc_get_category_selection_data(),
                    'std' => '',
                ),
                array(
                    'type' => 'textfield',
                    'heading' => __('Classes', 'video_central'),
                    'param_name' => 'classes',
                    'admin_label' => true,
                    'std' => '',
                ),
            ),
        );

        vc_map($map_parameters);
    }

    public function video_central_tags()
    {
        $map_parameters = array(
            'name' => __('Video Tags List', 'video_central'),
            'base' => 'video-central-video-tags',
            'icon' => 'icon-wpb-separator',
            'allowed_container_element' => 'vc_row',
            'category' => __('Video Central Elements', 'video_central'),
            'description' => __('Display a list of Video Tags', 'video_central'),
            'params' => array(
                array(
                    'type' => 'textfield',
                    'heading' => __('Maximum number of tags to show', 'video_central'),
                    'param_name' => 'show_count',
                    'admin_label' => true,
                    'std' => '',
                ),
                array(
                    'type' => 'textfield',
                    'heading' => __('Classes', 'video_central'),
                    'param_name' => 'classes',
                    'admin_label' => true,
                    'std' => '',
                ),
            ),
        );

        vc_map($map_parameters);
    }

    public function video_central_single_tag()
    {
        $map_parameters = array(
            'name' => __('Video Tag', 'video_central'),
            'base' => 'video-central-single-tag',
            'icon' => 'icon-wpb-separator',
            'allowed_container_element' => 'vc_row',
            'category' => __('Video Central Elements', 'video_central'),
            'description' => __('Display a list of videos from a Video Tag', 'video_central'),
            'params' => array(
                array(
                    'type' => 'dropdown',
                    'heading' => __('Tag ID', 'video_central'),
                    'param_name' => 'id',
                    'admin_label' => true,
                    'value' => video_central_vc_get_tag_selection_data(),
                    'std' => '',
                ),
                array(
                    'type' => 'textfield',
                    'heading' => __('Classes', 'video_central'),
                    'param_name' => 'classes',
                    'admin_label' => true,
                    'std' => '',
                ),
            ),
        );

        vc_map($map_parameters);
    }

    public function video_central_search()
    {
        $map_parameters = array(
            'name' => __('Video Search Results', 'video_central'),
            'base' => 'video-central-search',
            'icon' => 'icon-wpb-separator',
            'allowed_container_element' => 'vc_row',
            'category' => __('Video Central Elements', 'video_central'),
            'description' => __('Display a list of galleries based on a search query', 'video_central'),
            'params' => array(
                array(
                    'type' => 'textfield',
                    'heading' => __('Search query', 'video_central'),
                    'param_name' => 'search',
                    'admin_label' => true,
                    'std' => '',
                ),
                array(
                    'type' => 'textfield',
                    'heading' => __('Classes', 'video_central'),
                    'param_name' => 'classes',
                    'admin_label' => true,
                    'std' => '',
                ),
            ),
        );

        vc_map($map_parameters);
    }

    public function video_central_search_form()
    {
        $map_parameters = array(
            'name' => __('Video Search Form', 'video_central'),
            'base' => 'video-central-search-form',
            'icon' => 'icon-wpb-separator',
            'allowed_container_element' => 'vc_row',
            'category' => __('Video Central Elements', 'video_central'),
            'description' => __('Display the video search form', 'video_central'),
            'params' => array(
                array(
                    'type' => 'textfield',
                    'heading' => __('Classes', 'video_central'),
                    'param_name' => 'classes',
                    'admin_label' => true,
                    'std' => '',
                ),
            ),
        );

        vc_map($map_parameters);
    }

    public function video_central_view()
    {
        $map_parameters = array(
            'name' => __('Video Central View', 'video_central'),
            'base' => 'video-central-view',
            'icon' => 'icon-wpb-separator',
            'allowed_container_element' => 'vc_row',
            'category' => __('Video Central Elements', 'video_central'),
            'description' => __('Display video list based on registerd views eg recent, popular etc', 'video_central'),
            'params' => array(
                array(
                    'type' => 'dropdown',
                    'heading' => __('View ID', 'video_central'),
                    'param_name' => 'id',
                    'admin_label' => true,
                    'value' => radium_builder_get_registered_video_views_selection_data(),
                    'std' => '',
                ),
                array(
                    'type' => 'textfield',
                    'heading' => __('Classes', 'video_central'),
                    'param_name' => 'classes',
                    'admin_label' => true,
                    'std' => '',
                ),
            ),
        );

        vc_map($map_parameters);
    }

    public function video_central_slider_grid()
    {

        // Posts Latest
        $map_parameters = array(
            'name' => __('Video Slider Grid', 'video_central'),
            'base' => 'video-central-slider-grid',
            'icon' => 'icon-wpb-separator',
            'allowed_container_element' => 'vc_row',
            'category' => __('Video Central Elements', 'video_central'),
            'description' => __('Display Video Central Slider Grid', 'video_central'),
            'params' => array(
                array(
                    'type' => 'textfield',
                    'heading' => __('Title', 'video_central'),
                    'param_name' => 'title',
                    'admin_label' => true,
                    'std' => '',
                ),
                array(
                    'type' => 'textfield',
                    'heading' => __('Classes', 'video_central'),
                    'param_name' => 'classes',
                    'admin_label' => true,
                    'std' => '',
                ),
            ),
        );

        vc_map($map_parameters);
    }
}
