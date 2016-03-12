<?php

/**
 * Video Central Popular Widget.
 *
 * Adds a widget which displays the latest videos
 *
 * @since 1.0.0
 *
 * @uses WP_Widget
 */
class Video_Central_Popular_Widget extends WP_Widget
{
    /**
     * video_central Popular Widget.
     *
     * Registers the popular widget
     *
     * @since 1.0.0
     *
     * @uses apply_filters() Calls 'video_central_popular_widget_options' with the
     *                        widget options
     */
    public function __construct()
    {
        $widget_ops = apply_filters('video_central_popular_widget_options', array(
            'classname' => 'widget_display_popular',
            'description' => __('The video central popular videos.', 'video_central'),
        ));

        parent::__construct(false, __('(Video Central) Popular Videos', 'video_central'), $widget_ops);
    }

    /**
     * Register the widget.
     *
     * @since 1.0.0
     *
     * @uses register_widget()
     */
    public static function register_widget()
    {
        register_widget(__CLASS__);
    }

    /**
     * Displays the output, the popular form.
     *
     * @since 1.0.0
     *
     * @uses apply_filters() Calls 'video_central_popular_widget_title' with the title
     * @uses get_template_part() To get the popular form
     */
    public function widget($args, $instance)
    {

        // Get widget settings
        $settings = $this->parse_settings($instance);

        // Typical WordPress filter
        $settings['title'] = apply_filters('widget_title', $settings['title'], $instance, $this->id_base);

        // video_central filter
        $settings['title'] = apply_filters('video_central_popular_widget_title', $settings['title'], $instance, $this->id_base);

        $thumbnail_size = isset($settings['thumbnail_size']) ? $settings['thumbnail_size'] : 'small-left';

        //Set Thumbs
        if ($thumbnail_size == 'small-left' ||  $thumbnail_size == 'small-right') {
            $thumb_w = '70'; //Define width
            $thumb_h = '48'; // Define height
            $title_tag = 'h5';
        } else {
            $thumb_w = '298'; //Define width
            $thumb_h = '140'; // Define height
            $title_tag = 'h4';
        }

        $widget_query = new WP_Query(array(
            'post_type' => video_central_get_video_post_type(),
            'post_status' => video_central_get_public_status_id(),
            'posts_per_page' => $settings['number'],
            'ignore_sticky_posts' => true,
            'no_found_rows' => true,
            'meta_key' => '_video_central_video_views_count',
            'max_num_pages' => 1,
            'orderby' => 'meta_value_num',
        ));

        // Bail if no posts
        if (!$widget_query->have_posts()) {
            return;
        }

        echo $args['before_widget'];

        if (!empty($settings['title'])) {
            echo $args['before_title'].$settings['title'].$args['after_title'];
        }
        ?>

        <div class="video-central">

            <ul class="thumbnail-<?php echo $thumbnail_size;
        ?>">

                <?php while ($widget_query->have_posts()) : $widget_query->the_post();
        ?>

                    <li class="clearfix">
                        <div class="video-central-video-thumb">
                            <a class="" href="<?php video_central_video_permalink($widget_query->post->ID);
        ?>">

                                <?php do_action('video_central_after_popular_widget_thumb');
        ?>

                                <img src="<?php video_central_featured_image_url($widget_query->post->ID, array('width' => $thumb_w, 'height' => $thumb_h));
        ?>" alt="<?php video_central_video_title($widget_query->post->ID);
        ?>" height="<?php echo $thumb_h;
        ?>" width="<?php echo $thumb_w;
        ?>"/>

                                <?php do_action('video_central_after_popular_widget_thumb');
        ?>

                                <?php if ($thumbnail_size == 'large') {
    ?><div class="video-entry-meta duration"><?php video_central_video_duration($widget_query->post->ID);
    ?></div><?php
}
        ?>

                                <span class="video-icon-wrapper"><span class="icon icon-play"></span></span>
                            </a>
                        </div>
                        <div class="video-central-video-title">
                            <<?php echo $title_tag;
        ?> class="entry-title"><a href="<?php video_central_video_permalink($widget_query->post->ID);
        ?>"><?php video_central_video_title($widget_query->post->ID);
        ?></a></<?php echo $title_tag;
        ?>>
                        </div>
                    </li>

                <?php endwhile;
        ?>

            </ul>

        </div>
        <?php

        echo $args['after_widget'];

        // Reset the $post global
        wp_reset_postdata();
    }

    /**
     * Update the widget options.
     *
     * @since 1.0.0
     *
     * @param array $new_instance The new instance options
     * @param array $old_instance The old instance options
     */
    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['number'] = strip_tags($new_instance['number']);
        $instance['thumbnail_size'] = $new_instance['thumbnail_size'];

        return $instance;
    }

    /**
     * Output the popular widget options form.
     *
     * @since v1.0.0
     *
     * @param $instance Instance
     *
     * @uses Video_Central_Popular_Widget::get_field_id() To output the field id
     * @uses Video_Central_Popular_Widget::get_field_name() To output the field name
     */
    public function form($instance)
    {

        // Get widget settings
        $settings = $this->parse_settings($instance);
        $thumbnail_size = isset($settings['thumbnail_size']) ? $settings['thumbnail_size'] : 'small-left';
        ?>

        <p>
            <label for="<?php echo $this->get_field_id('title');
        ?>"><?php _e('Title:', 'video_central');
        ?>
                <input class="widefat" id="<?php echo $this->get_field_id('title');
        ?>" name="<?php echo $this->get_field_name('title');
        ?>" type="text" value="<?php echo esc_attr($settings['title']);
        ?>" />
            </label>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('number');
        ?>"><?php _e('Number of videos to display:', 'video_central');
        ?>
                <input class="widefat" id="<?php echo $this->get_field_id('number');
        ?>" name="<?php echo $this->get_field_name('number');
        ?>" type="text" value="<?php echo esc_attr($settings['number']);
        ?>" />
            </label>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('thumbnail_size');
        ?>"><?php _e('Thumbnail size', 'video_central');
        ?></label>
            <select id="<?php echo $this->get_field_id('thumbnail_size');
        ?>" name="<?php echo $this->get_field_name('thumbnail_size');
        ?>" class="widefat">
                <?php
        $options = array('small-left', 'large', 'small-right');
        foreach ($options as $option) {
            $selected = $thumbnail_size == $option ? 'selected="selected"' : '';
            echo '<option value="'.$option.'" id="'.$option.'" '.$selected.'>'.$option.'</option>';
        }
        ?>
            </select>
        </p>

        <?php

    }

    /**
     * Merge the widget settings into defaults array.
     *
     * @since 1.0.0
     *
     * @param $instance Instance
     *
     * @uses video_central_parse_args() To merge widget settings into defaults
     */
    public function parse_settings($instance = array())
    {
        return video_central_parse_args($instance, array(
            'title' => __('Popular Videos', 'video_central'),
            'number' => 5,
        ), 'popular_widget_settings');
    }
}
