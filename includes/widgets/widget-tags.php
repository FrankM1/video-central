<?php


/**
 * Tag cloud widget class.
 *
 * @since 2.8.0
 */
class Video_Central_Widget_Tags extends WP_Widget
{
    public function __construct()
    {
        $widget_ops = array('description' => __('A cloud of your most used tags.', 'video_central'));
        parent::__construct('video_tag_cloud', __('(Video Central) Tag Cloud', 'video_central'), $widget_ops);
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

    public function widget($args, $instance)
    {
        extract($args);

        $current_taxonomy = $this->_get_current_taxonomy($instance);

        if (!empty($instance['title'])) {
            $title = $instance['title'];
        } else {
            if (video_central()->video_tag_tax_id == $current_taxonomy) {
                $title = __('Tags', 'video_central');
            } else {
                $tax = get_taxonomy($current_taxonomy);
                $title = $tax->labels->name;
            }
        }

        $title = apply_filters('widget_title', $title, $instance, $this->id_base);

        echo $before_widget;
        if ($title) {
            echo $before_title.$title.$after_title;
        }
        echo '<div class="tagcloud">';
        wp_tag_cloud(apply_filters('widget_tag_cloud_args', array('taxonomy' => $current_taxonomy)));
        echo "</div>\n";
        echo $after_widget;
    }

    public function update($new_instance, $old_instance)
    {
        $instance['title'] = strip_tags(stripslashes($new_instance['title']));
        $instance['taxonomy'] = stripslashes($new_instance['taxonomy']);

        return $instance;
    }

    public function form($instance)
    {
        $video_central = video_central();

        $current_taxonomy = $this->_get_current_taxonomy($instance);

        $taxonomy_objects = get_object_taxonomies($video_central->video_post_type, 'objects');

        ?><p><label for="<?php echo $this->get_field_id('title');
        ?>"><?php _e('Title:', 'video_central') ?></label>
    <input type="text" class="widefat" id="<?php echo $this->get_field_id('title');
        ?>" name="<?php echo $this->get_field_name('title');
        ?>" value="<?php if (isset($instance['title'])) {
    echo esc_attr($instance['title']);
}
        ?>" /></p>
    <p><label for="<?php echo $this->get_field_id('taxonomy');
        ?>"><?php _e('Taxonomy:', 'video_central') ?></label>
    <select class="widefat" id="<?php echo $this->get_field_id('taxonomy');
        ?>" name="<?php echo $this->get_field_name('taxonomy');
        ?>">
    <?php foreach ($taxonomy_objects as $tax) : ?>
        <option value="<?php echo esc_attr($tax->name) ?>" <?php selected($tax->name, $current_taxonomy) ?>><?php echo $tax->label;
        ?></option>
    <?php endforeach;
        ?>
    </select></p><?php

    }

    public function _get_current_taxonomy($instance)
    {
        if (!empty($instance['taxonomy']) && taxonomy_exists($instance['taxonomy'])) {
            return $instance['taxonomy'];
        }

        return video_central()->video_tag_tax_id;
    }
}
