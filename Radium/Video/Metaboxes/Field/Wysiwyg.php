<?php

if (!class_exists('Radium_Video_Metaboxes_Field_Wysiwyg')) {
    class Radium_Video_Metaboxes_Field_Wysiwyg extends Radium_Video_Metaboxes_Field
    {
        /**
         * Enqueue scripts and styles.
         */
        public static function admin_enqueue_scripts()
        {
            wp_enqueue_style('rwmb-meta-box-wysiwyg', video_central()->admin->css_url.'metaboxes/wysiwyg.css', array(), video_central()->version);
        }

        /**
         * Change field value on save.
         *
         * @param mixed $new
         * @param mixed $old
         * @param int   $post_id
         * @param array $field
         *
         * @return string
         */
        public static function value($new, $old, $post_id, $field)
        {
            return ($field['raw'] ? $new : wpautop($new));
        }

        /**
         * Get field HTML.
         *
         * @param mixed $meta
         * @param array $field
         *
         * @return string
         */
        public static function html($meta, $field)
        {
            // Using output buffering because wp_editor() echos directly
            ob_start();

            $field['options']['textarea_name'] = $field['field_name'];

            // Use new wp_editor() since WP 3.3
            wp_editor($meta, $field['id'], $field['options']);

            return ob_get_clean();
        }

        /**
         * Normalize parameters for field.
         *
         * @param array $field
         *
         * @return array
         */
        public static function normalize_field($field)
        {
            $field = wp_parse_args($field, array(
                'raw' => false,
                'options' => array(),
            ));

            $field['options'] = wp_parse_args($field['options'], array(
                'editor_class' => 'rwmb-wysiwyg',
                'dfw' => true, // Use default WordPress full screen UI
            ));

            // Keep the filter to be compatible with previous versions
            $field['options'] = apply_filters('rwmb_wysiwyg_settings', $field['options']);

            return $field;
        }
    }
}
