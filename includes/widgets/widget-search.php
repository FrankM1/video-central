<?php

/**
 * video_central Search Widget.
 *
 * Adds a widget which displays the video search form
 *
 * @since 1.0.0
 *
 * @uses WP_Widget
 */
class Video_Central_Widget_Search extends WP_Widget
{
    /**
     * video_central Search Widget.
     *
     * Registers the search widget
     *
     * @since 1.0.0
     *
     * @uses apply_filters() Calls 'video_central_search_widget_options' with the
     *                        widget options
     */
    public function __construct()
    {
        $widget_ops = apply_filters('video_central_search_widget_options', array(
            'classname' => 'widget_video_search',
            'description' => __('The video_central video search form.', 'video_central'),
        ));

        parent::__construct(false, __('(Video Central) Video Search Form', 'video_central'), $widget_ops);
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
     * Displays the output, the search form.
     *
     * @since 1.0.0
     *
     * @uses apply_filters() Calls 'video_central_search_widget_title' with the title
     * @uses get_template_part() To get the search form
     */
    public function widget($args, $instance)
    {

        // Bail if search is disabled
        if (!video_central_allow_search()) {
            return;
        }

        // Get widget settings
        $settings = $this->parse_settings($instance);

        // Typical WordPress filter
        $settings['title'] = apply_filters('widget_title',            $settings['title'], $instance, $this->id_base);

        // video_central filter
        $settings['title'] = apply_filters('video_central_search_widget_title', $settings['title'], $instance, $this->id_base);

        echo $args['before_widget'];

        if (!empty($settings['title'])) {
            echo $args['before_title'].$settings['title'].$args['after_title'];
        }

        video_central_get_template_part('form', 'search');

        echo $args['after_widget'];
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

        return $instance;
    }

    /**
     * Output the search widget options form.
     *
     * @since v1.0.0
     *
     * @param $instance Instance
     *
     * @uses Video_Central_Search_Widget::get_field_id() To output the field id
     * @uses Video_Central_Search_Widget::get_field_name() To output the field name
     */
    public function form($instance)
    {

        // Get widget settings
        $settings = $this->parse_settings($instance);
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
            'title' => __('Search Videos', 'video_central'),
        ), 'search_widget_settings');
    }
}
