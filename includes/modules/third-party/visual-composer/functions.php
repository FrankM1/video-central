<?php

/**
 * Video Central's Visual Composer Functions.
 *
 * @since 1.0.0
 */

/**
 * Get array for use with builder selector.
 *
 * @since 1.0.0
 *
 * @param string $post_type post type to use
 * @param array  $args      Post type arguments
 *
 * @return $options New array
 */
function radium_builder_get_video_selection_data($post_type = false, $args = array())
{
    $options = array();

    $post_type = $post_type ? $post_type : video_central_get_video_post_type();

    $args = wp_parse_args($args, array(
        'post_type' => $post_type,
        'post_status' => 'publish',
        'posts_per_page' => -1,
    ));

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $post = $query->next_post();
            $options[$post->post_title] = $post->ID;
        }
    }

    return $options;
}

/**
 * Get array for use with builder selector.
 *
 * @since 1.0.0
 *
 * @return $options New array
 */
function video_central_vc_get_category_selection_data()
{
    $options = array();

    $categories = get_categories('taxonomy='.video_central_get_video_category_tax_id());

    foreach ($categories as $category) {
        if ($category->count > 0) {
            $options[$category->name] = $category->term_id;
        }
    }

    return $options;
}

/**
 * Get array for use with builder selector.
 *
 * @since 1.0.0
 *
 * @return $options New array
 */
function video_central_vc_get_tag_selection_data()
{
    $options = array();

    $tags = get_categories('taxonomy='.video_central_get_video_tag_tax_id());

    foreach ($tags as $tag) {
        if ($tag->count > 0) {
            $options[$tag->name] = $tag->term_id;
        }
    }

    return $options;
}

/**
 * Get array for use with builder selector.
 *
 * @since 1.0.0
 *
 * @return $options New array
 */
function radium_builder_get_registered_video_views_selection_data()
{
    $options = array();

    $views = video_central()->views;

    foreach ($views as $key => $data) {
        $options[$data['title']] = $key;
    }

    return $options;
}
