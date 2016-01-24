<?php

if (!class_exists('Radium_Video_Metaboxes_Field_Checkbox')) {
    class Radium_Video_Metaboxes_Field_Checkbox extends Radium_Video_Metaboxes_Field
    {
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
            return sprintf(
                '<input type="checkbox" class="rwmb-checkbox" name="%s" id="%s" value="1" %s />',
                $field['field_name'],
                $field['id'],
                checked(!empty($meta), 1, false)
            );
        }

        /**
         * Set the value of checkbox to 1 or 0 instead of 'checked' and empty string
         * This prevents using default value once the checkbox has been unchecked.
         *
         * @link https://github.com/rilwis/meta-box/issues/6
         *
         * @param mixed $new
         * @param mixed $old
         * @param int   $post_id
         * @param array $field
         *
         * @return int
         */
        public static function value($new, $old, $post_id, $field)
        {
            return empty($new) ? 0 : 1;
        }
    }
}
