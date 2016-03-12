<?php

/**
 * Video Central Woosidebars.
 */

/**
 * Video Central Woosidebars Integration.
 *
 * @since 1.0.0
 */
class Video_Central_Integration_Woosidebars
{
    
    /**
     * Constructor.
     *
     * @since  1.1.0
     */
    public function __construct()
    {
        add_filter('woo_conditions',            array(&$this, 'register_conditions'));
        add_filter('woo_conditions_headings',    array(&$this, 'register_conditions_headings'));
        add_filter('woo_conditions_reference', array(&$this, 'register_conditions_reference'));

        add_post_type_support(video_central()->video_post_type, 'woosidebars');
    } // End __construct()

    /**
     * Register the integration conditions with WooSidebars.
     *
     * @since  1.1.0
     *
     * @param array $conditions The existing array of conditions.
     *
     * @return array The modified array of conditions.
     */
    public function register_conditions($conditions)
    {
        global $post;

        if (function_exists('is_video_central') && !is_video_central()) {
            return $conditions;
        }

        $integration = array();

        if (function_exists('is_video_central') && is_video_central()) {
            $integration[] = 'vc-video_page';
        }
        if (function_exists('video_central_is_video_category') && video_central_is_video_category()) {
            $integration[] = 'vc-video_category';
        }
        if (function_exists('video_central_is_video_tag') && video_central_is_video_tag()) {
            $integration[] = 'vc-video_tag';
        }

        if (function_exists('is_video_central')    && is_video_central()) {
            $integration[] = 'vc-video';

            $categories = get_the_terms($post->ID, video_central()->video_cat_tax_id);

            if (!is_wp_error($categories) && is_array($categories) && (count($categories) > 0)) {
                foreach ($categories as $k => $v) {
                    $integration[] = 'in-term-'.esc_attr($v->term_id);
                }
            }

            $tags = get_the_terms($post->ID, video_central()->video_tag_tax_id);

            if (!is_wp_error($tags) && is_array($tags) && (count($tags) > 0)) {
                foreach ($tags as $k => $v) {
                    $integration[] = 'in-term-'.esc_attr($v->term_id);
                }
            }
        }

        $integration[] = $conditions[count($conditions) - 1];

        array_splice($conditions, count($conditions), 0, $integration);

        return $conditions;
    } // End register_conditions()

    /**
     * Register the integration's headings for the meta box.
     *
     * @since  1.1.0
     *
     * @param array $headings The existing array of headings.
     *
     * @return array The modified array of headings.
     */
    public function register_conditions_headings($headings)
    {
        $headings['video_central'] = __('Video Central', 'video_central');

        return $headings;
    } // End register_conditions_headings()

    /**
     * Register the integration's conditions reference for the meta box.
     *
     * @since  1.1.0
     *
     * @param array $headings The existing array of conditions.
     *
     * @return array The modified array of conditions.
     */
    public function register_conditions_reference($conditions)
    {
        $conditions['video_central'] = array();

        $conditions['video_central']['vc-video_page'] = array(
            'label' => __('Video Page', 'video_central'),
            'description' => __('The Video Central "Videos" landing page', 'video_central'),
        );

        $conditions['video_central']['vc-video_category'] = array(
            'label' => __('Video Categories', 'video_central'),
            'description' => __('All video categories', 'video_central'),
        );

        $conditions['video_central']['vc-video_tag'] = array(
            'label' => __('Video Tags', 'video_central'),
            'description' => __('All Video tags', 'video_central'),
        );

        $conditions['video_central']['vc-video'] = array(
            'label' => __('Videos', 'video_central'),
            'description' => __('All Videos', 'video_central'),
        );

        $conditions['video_central']['vc-account'] = array(
            'label' => __('Account Pages', 'video_central'),
            'description' => __('The Videos "Account" pages', 'video_central'),
        );

        // Setup terminologies for the "in category" and "tagged with" conditions.
        $terminologies = array(
            'taxonomy-'.video_central()->video_tag_cat_id => __('Videos in the "%s" category', 'video_central'),
            'taxonomy-'.video_central()->video_tag_tax_id => __('Videos tagged "%s"', 'video_central'),
        );

        foreach ($terminologies as $k => $v) {
            if (!isset($conditions[$k])) {
                continue;
            }
            foreach ($conditions[$k] as $i => $j) {
                $conditions[$k]['in-'.$i] = array('label' => sprintf($terminologies[$k], $j['label']), 'description' => sprintf($terminologies[$k], $j['label']));
            }
        }

        return $conditions;
    } // End register_conditions_reference()
} // End Class

// Initialise the integration.
new Video_Central_Integration_Woosidebars();
