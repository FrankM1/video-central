<?php

/**
 * Categories widget class.
 *
 * @since 2.8.0
 */
class Video_Central_Widget_Categories extends WP_Widget
{
    public function __construct()
    {
        $widget_ops = array('classname' => 'video_central_widget_categories', 'description' => __('A list or dropdown of video categories.', 'video_central'));
        parent::__construct('video_central_widget_categories', __('(Video Central) Categories', 'video_central'), $widget_ops);
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

        $title = apply_filters('widget_title', empty($instance['title']) ? __('Categories', 'video_central') : $instance['title'], $instance, $this->id_base);
        $c = !empty($instance['count']) ? '1' : '0';
        $h = !empty($instance['hierarchical']) ? '1' : '0';
        $d = !empty($instance['dropdown']) ? '1' : '0';

        echo $before_widget;
        if ($title) {
            echo $before_title.$title.$after_title;
        }

        $cat_args = array(
            'taxonomy' => video_central_get_video_category_tax_id(),
            'orderby' => 'name',
            'show_count' => $c,
            'hierarchical' => $h,
        );

        if ($d) {
            $categories = get_categories('taxonomy='.video_central_get_video_category_tax_id());
            $select = "<select name='cat' id='cat' class='postform'>n";

            $select .= "<option value='-1'>".__('Select Category', 'video_central').'</option>';
            foreach ($categories as $category) {
                if ($category->count > 0) {
                    $select .= "<option value='".$category->slug."'>".$category->name.'</option>';
                }
            }

            $select .= '</select>';

            echo $select;

            ?>

<script type='text/javascript'>
/* <![CDATA[ */
    var dropdown = document.getElementById("cat");

    function onCatChange() {
        if ( dropdown.options[dropdown.selectedIndex].value > 0 ) {
            location.href = "<?php echo home_url();
            ?>/?video_category="+dropdown.options[dropdown.selectedIndex].value;
        }
    }
    dropdown.onchange = onCatChange;
/* ]]> */
</script>

<?php

        } else {
            ?>
        <ul>
<?php
        $cat_args['title_li'] = '';
            wp_list_categories(apply_filters('widget_video_categories_args', $cat_args));
            ?>
        </ul>
<?php

        }

        echo $after_widget;
    }

    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['count'] = !empty($new_instance['count']) ? 1 : 0;
        $instance['hierarchical'] = !empty($new_instance['hierarchical']) ? 1 : 0;
        $instance['dropdown'] = !empty($new_instance['dropdown']) ? 1 : 0;

        return $instance;
    }

    public function form($instance)
    {
        //Defaults
        $instance = wp_parse_args((array) $instance, array('title' => ''));
        $title = esc_attr($instance['title']);
        $count = isset($instance['count']) ? (bool) $instance['count'] : false;
        $hierarchical = isset($instance['hierarchical']) ? (bool) $instance['hierarchical'] : false;
        $dropdown = isset($instance['dropdown']) ? (bool) $instance['dropdown'] : false;
        ?>
        <p><label for="<?php echo $this->get_field_id('title');
        ?>"><?php _e('Title:', 'video_central');
        ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title');
        ?>" name="<?php echo $this->get_field_name('title');
        ?>" type="text" value="<?php echo $title;
        ?>" /></p>

        <p><input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('dropdown');
        ?>" name="<?php echo $this->get_field_name('dropdown');
        ?>"<?php checked($dropdown);
        ?> />
        <label for="<?php echo $this->get_field_id('dropdown');
        ?>"><?php _e('Display as dropdown', 'video_central');
        ?></label><br />

        <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('count');
        ?>" name="<?php echo $this->get_field_name('count');
        ?>"<?php checked($count);
        ?> />
        <label for="<?php echo $this->get_field_id('count');
        ?>"><?php _e('Show post counts', 'video_central');
        ?></label><br />

        <input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('hierarchical');
        ?>" name="<?php echo $this->get_field_name('hierarchical');
        ?>"<?php checked($hierarchical);
        ?> />
        <label for="<?php echo $this->get_field_id('hierarchical');
        ?>"><?php _e('Show hierarchy', 'video_central');
        ?></label></p>
<?php

    }
}
