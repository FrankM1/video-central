<?php

/**
 * WIdget class for Radium_Video.
 *
 * Loads all of the necessary components for the radium tweets plugin.
 *
 * @since 1.0.0
 *
 * @author  Franklin Gitonga
 */
class Radium_Video_Widget extends WP_Widget
{
    /*--------------------------------------------------------------------*/
    /*  WIDGET SETUP
    /*--------------------------------------------------------------------*/
    public function __construct()
    {
        parent::__construct(
            'video_central', // BASE ID
            'Radium Recent Video', // NAME
            array('description' => __('A widget that displays your recent videos.', 'video_central'))
        );
    }

    /*--------------------------------------------------------------------*/
    /*  DISPLAY WIDGET
    /*--------------------------------------------------------------------*/
    public function widget($args, $instance)
    {

        extract($args);

        $title = apply_filters('widget_title', $instance['title']);

    /* Our variables from the widget settings. */
    $number = (isset($instance['number'])) ? $instance['number'] : 0;
        $desc = $instance['desc'];

    /* Before widget (defined by themes). */

    echo $before_widget;

    /* Display Widget */
    ?>
    <?php

        /* Display the widget title if one was input (before and after defined by themes). */
        if ($title) {
            echo $before_title.$title.$after_title;
        }
        ?>

        <div class="video-central-recent-video-widget">
            <?php if ($desc) {
    ?>
                <p><?php echo $desc;
    ?></p>
            <?php
}

                //Set Thumbs
                $thumb_w = 65; //Define width
                $thumb_h = 65; // Define height
                $crop = true; //resize
                $single = true; //return array

                $args = array(
                    'post_type' => 'video',
                    'orderby' => 'menu_order',
                    'order' => 'ASC',
                    'posts_per_page' => $number,
                );
        $query = new WP_Query($args);

        while ($query->have_posts()) : $query->the_post();
        ?>

                <article class="grid-item">
                    <div class="grid-thumb">
                        <a title="<?php printf(__('Permanent Link to %s', 'video_central'), get_the_title());
        ?>" href="<?php the_permalink();
        ?>" data-width="<?php echo $thumb_w;
        ?>" data-height="<?php echo $thumb_h;
        ?>">

                        <?php $post_type = get_post_meta(get_the_ID(), '_video_central_type', true);

        switch ($post_type) {

                                default:

                                    $image = get_video_central_post_image(get_the_ID(), $post_type, $thumb_w, $thumb_h, $crop, $single);

                                ?>

                                <img src="<?php echo $image ?>" alt="<?php the_title();?>"/>

                            <?php break;

                        }
        ?>
                </a>
            </div>
            <h4>
                <a title="<?php printf(__('Permanent Link to %s', 'video_central'), get_the_title());
        ?>" href="<?php the_permalink();
        ?>">
                    <?php the_title();
        ?>
                </a>
            </h4>
        </article>

        <?php endwhile;
        ?>
        <?php wp_reset_postdata();
        ?>

    </div><!-- End Recent Videos Widget -->

    <?php

        /* After widget (defined by themes). */
        echo $after_widget;
    }

/*-----------------------------------------------------------------------------------*/
/*  Update Widget
/*-----------------------------------------------------------------------------------*/

    public function update($new_instance, $old_instance)
    {
        $instance = $old_instance;

        /* Strip tags to remove HTML (important for text inputs). */
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['number'] = strip_tags($new_instance['number']);
        $instance['desc'] = $new_instance['desc'];

        /* No need to strip tags for.. */

        return $instance;
    }

/*-----------------------------------------------------------------------------------*/
/*  Widget Settings
/*-----------------------------------------------------------------------------------*/

    public function form($instance)
    {

        /* Set up some default widget settings. */
        $defaults = array(
            'title' => 'Our Recent Works.',
            'desc' => '',
            'number' => 3,
        );

        $instance = wp_parse_args((array) $instance, $defaults);
        ?>

        <p><!-- Widget Title: Text Input -->
            <label for="<?php echo $this->get_field_id('title');
        ?>"><?php _e('Title:', 'video_central') ?></label>
            <input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') );
        ?>" name="<?php echo esc_attr( $this->get_field_name('title') );
        ?>" value="<?php echo esc_attr( $instance['title'] );
        ?>" />
        </p>

        <p><!-- Number Input: Text Input -->
            <label for="<?php echo $this->get_field_id('number');
        ?>"><?php _e('Number of Posts to Display:', 'video_central') ?></label>
            <input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id('number') );
        ?>" name="<?php echo esc_attr( $this->get_field_name('number') );
        ?>" value="<?php echo esc_attr( $instance['number'] );
        ?>" />
        </p>

        <p><!-- Description Input: Text Input -->
            <label for="<?php echo $this->get_field_id('desc');
        ?>"><?php _e('Description:', 'video_central') ?></label>
            <textarea class="widefat" rows="6" cols="15" id="<?php echo $this->get_field_id('desc');
        ?>" name="<?php echo $this->get_field_name('desc');
        ?>"><?php echo esc_html( $instance['desc'] );
        ?></textarea>
        </p>

    <?php

    }
}
