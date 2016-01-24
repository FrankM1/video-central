<?php

if ( !class_exists( 'Radium_Video_Metaboxes_Field_Text' ) )
{
    class Radium_Video_Metaboxes_Field_Text extends Radium_Video_Metaboxes_Field
    {
        /**
         * Get field HTML
         *
         * @param mixed  $meta
         * @param array  $field
         *
         * @return string
         */
        static function html( $meta, $field )
        {
            return sprintf(
                '<input type="text" class="rwmb-text" name="%s" id="%s" value="%s" placeholder="%s" size="%s" %s>%s',
                $field['field_name'],
                $field['id'],
                $meta,
                $field['placeholder'],
                $field['size'],
                !$field['datalist'] ?  '' : "list='{$field['datalist']['id']}'",
                self::datalist_html($field)
            );
        }

        /**
         * Normalize parameters for field
         *
         * @param array $field
         *
         * @return array
         */
        static function normalize_field( $field )
        {
            $field = wp_parse_args( $field, array(
                'size'        => 30,
                'datalist'    => false,
                'placeholder' => '',
            ) );
            return $field;
        }

        /**
         * Create datalist, if any
         *
         * @param array $field
         *
         * @return array
         */
        static function datalist_html( $field )
        {
            if( !$field['datalist'] )
                return '';
            $datalist = $field['datalist'];
            $html = sprintf(
                '<datalist id="%s">',
                $datalist['id']
            );

            foreach( $datalist['options'] as $option ) {
                $html.= sprintf('<option value="%s"></option>', $option);
            }

            $html .= '</datalist>';

            return $html;
        }
    }
}
