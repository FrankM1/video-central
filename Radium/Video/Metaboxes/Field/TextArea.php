<?php

if (!class_exists('Radium_Video_Metaboxes_Field_TextArea')) {
    class Radium_Video_Metaboxes_Field_TextArea extends Radium_Video_Metaboxes_Field
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
                '<textarea class="rwmb-textarea large-text" name="%s" id="%s" cols="%s" rows="%s" placeholder="%s">%s</textarea>',
                $field['field_name'],
                $field['id'],
                $field['cols'],
                $field['rows'],
                $field['placeholder'],
                $meta
            );
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
                'cols' => 60,
                'rows' => 3,
            ));

            return $field;
        }
    }
}
