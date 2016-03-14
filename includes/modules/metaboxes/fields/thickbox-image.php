<?php
/**
 * Image upload field which uses thickbox library to upload.
 * @deprecated
 */
class Video_Central_Metaboxes_Thickbox_Image_Field extends Video_Central_Metaboxes_Image_Field
{
	/**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	static function admin_enqueue_scripts()
	{
		parent::admin_enqueue_scripts();

		add_thickbox();
		wp_enqueue_script( 'media-upload' );

		wp_enqueue_script( 'video-central-metaboxes-thickbox-image', Video_Central_Metaboxes_JS_URL . 'thickbox-image.js', array( 'jquery' ), Video_Central_Metaboxes_VER, true );
	}

	/**
	 * Get field HTML
	 *
	 * @param mixed $meta
	 * @param array $field
	 *
	 * @return string
	 */
	static function html( $meta, $field )
	{
		$i18n_title = apply_filters( 'video_central_metaboxes_thickbox_image_upload_string', _x( 'Upload Images', 'image upload', 'meta-box' ), $field );

		// Uploaded images
		$html = self::get_uploaded_images( $meta, $field );

		// Show form upload
		$html .= "<a href='#' class='button video-central-metaboxes-thickbox-upload' data-field_id='{$field['id']}'>{$i18n_title}</a>";

		return $html;
	}

	/**
	 * Get field value
	 * It's the combination of new (uploaded) images and saved images
	 *
	 * @param array $new
	 * @param array $old
	 * @param int   $post_id
	 * @param array $field
	 *
	 * @return array
	 */
	static function value( $new, $old, $post_id, $field )
	{
		return array_unique( array_merge( $old, $new ) );
	}
}
