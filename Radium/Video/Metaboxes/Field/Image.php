<?php

if ( ! class_exists( 'Radium_Video_Metaboxes_Field_Image' ) )
{
    class Radium_Video_Metaboxes_Field_Image extends Radium_Video_Metaboxes_Field_File
    {
        /**
         * Enqueue scripts and styles
         *
         * @return void
         */
        static function admin_enqueue_scripts()
        {
            // Enqueue same scripts and styles as for file field
            parent::admin_enqueue_scripts();

            wp_enqueue_style( 'rwmb-image', video_central()->admin->css_url  . 'metaboxes/image.css', array(), video_central()->version );
            wp_enqueue_script( 'rwmb-image', video_central()->admin->js_url  . 'metaboxes/image.js', array( 'jquery-ui-sortable' ), video_central()->version, true );
        }

        /**
         * Add actions
         *
         * @return void
         */
        static function add_actions()
        {
            // Do same actions as file field
            parent::add_actions();

            // Reorder images via Ajax
            add_action( 'wp_ajax_rwmb_reorder_images', array( __CLASS__, 'wp_ajax_reorder_images' ) );
        }

        /**
         * Ajax callback for reordering images
         *
         * @return void
         */
        static function wp_ajax_reorder_images()
        {
            $field_id = isset( $_POST['field_id'] ) ? $_POST['field_id'] : 0;
            $order    = isset( $_POST['order'] ) ? $_POST['order'] : 0;
            $post_id  = isset( $_POST['post_id'] ) ? (int) $_POST['post_id'] : 0;

            check_ajax_referer( "rwmb-reorder-images_{$field_id}" );

            parse_str( $order, $items );

            delete_post_meta( $post_id, $field_id );
            foreach ( $items['item'] as $item )
            {
                add_post_meta( $post_id, $field_id, $item, false );
            }
            wp_send_json_success();
        }

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
            $i18n_title = apply_filters( 'rwmb_image_upload_string', _x( 'Upload Images', 'image upload', 'video_central' ), $field );
            $i18n_more  = apply_filters( 'rwmb_image_add_string', _x( '+ Add new image', 'image upload', 'video_central' ), $field );

            // Uploaded images
            $html = self::get_uploaded_images( $meta, $field );

            // Show form upload
            $html .= sprintf(
                '<h4>%s</h4>
                <div class="new-files">
                    <div class="file-input"><input type="file" name="%s[]" /></div>
                    <a class="rwmb-add-file" href="#"><strong>%s</strong></a>
                </div>',
                $i18n_title,
                $field['id'],
                $i18n_more
            );

            return $html;
        }

        /**
         * Get HTML markup for uploaded images
         *
         * @param array $images
         * @param array $field
         *
         * @return string
         */
        static function get_uploaded_images( $images, $field )
        {
            $reorder_nonce = wp_create_nonce( "rwmb-reorder-images_{$field['id']}" );
            $delete_nonce = wp_create_nonce( "rwmb-delete-file_{$field['id']}" );
            $classes = array( 'rwmb-images', 'rwmb-uploaded' );
            if ( count( $images ) <= 0  )
                $classes[] = 'hidden';
            $ul = '<ul class="%s" data-field_id="%s" data-delete_nonce="%s" data-reorder_nonce="%s" data-force_delete="%s" data-max_file_uploads="%s">';
            $html = sprintf(
                $ul,
                implode( ' ', $classes ),
                $field['id'],
                $delete_nonce,
                $reorder_nonce,
                $field['force_delete'] ? 1 : 0,
                $field['max_file_uploads']
            );

            foreach ( $images as $image )
            {
                $html .= self::img_html( $image );
            }

            $html .= '</ul>';

            return $html;
        }

        /**
         * Get HTML markup for ONE uploaded image
         *
         * @param int $image Image ID
         *
         * @return string
         */
        static function img_html( $image )
        {
            $i18n_delete = apply_filters( 'rwmb_image_delete_string', _x( 'Delete', 'image upload', 'video_central' ) );
            $i18n_edit   = apply_filters( 'rwmb_image_edit_string', _x( 'Edit', 'image upload', 'video_central' ) );
            $li = '
                <li id="item_%s">
                    <img src="%s" />
                    <div class="rwmb-image-bar">
                        <a title="%s" class="rwmb-edit-file" href="%s" target="_blank">%s</a> |
                        <a title="%s" class="rwmb-delete-file" href="#" data-attachment_id="%s">×</a>
                    </div>
                </li>
            ';

            $src  = wp_get_attachment_image_src( $image, 'thumbnail' );
            $src  = $src[0];
            $link = get_edit_post_link( $image );

            return sprintf(
                $li,
                $image,
                $src,
                $i18n_edit, $link, $i18n_edit,
                $i18n_delete, $image
            );
        }

        /**
         * Standard meta retrieval
         *
         * @param int   $post_id
         * @param array $field
         * @param bool  $saved
         *
         * @return mixed
         */
        static function meta( $post_id, $saved, $field )
        {
            global $wpdb;

            $meta = $wpdb->get_col( $wpdb->prepare( "
                SELECT meta_value FROM $wpdb->postmeta
                WHERE post_id = %d AND meta_key = '%s'
                ORDER BY meta_id ASC
            ", $post_id, $field['id'] ) );

            return empty( $meta ) ? array() : $meta;
        }
    }
}
