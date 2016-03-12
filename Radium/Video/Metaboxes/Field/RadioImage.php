<?php

if ( ! class_exists( 'Radium_Video_Metaboxes_Field_RadioImage' ) )
{
    class Radium_Video_Metaboxes_Field_RadioImage
    {
        /**
         * Enqueue scripts and styles
         *
         * @return void
         */
        static function admin_enqueue_scripts()
        {

            wp_enqueue_script( 'video-central-admin-radio-image', video_central()->admin->js_url  . 'metaboxes/radio-image.js', array( 'jquery' ), video_central()->version, true );

        }

        /**
         * Get field HTML
         *
         * @param string $html
         * @param mixed  $meta
         * @param array  $field
         *
         * @return string
         */
        static function html( $html, $meta, $field )
        {
            $html = '';
            foreach ( $field['options'] as $key => $value )
            {
                $checked  = checked( $meta, $key, false );
                $selected = $checked ? ' selected' : '';
                $id       = strstr( $field['id'], '[]' ) ? str_replace( '[]', "-{$key}[]", $field['id'] ) : $field['id'];
                $id       = " id='{$id}'";
                $name     = "name='{$field['field_name']}'";
                $val      = " value='{$key}'";
                $html    .= "<label class='video-central-metaboxes-label-radio-image{$selected}'><input type='radio' class='video-central-metaboxes-radio-image'{$name}{$id}{$val}{$checked} /> {$value}</label> ";
            }

            return $html;
        }
    }
}
