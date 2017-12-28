<?php
/**
 * File advanced field class which users WordPress media popup to upload and select files.
 */
class Video_Central_Metaboxes_Image_Upload_Field extends Video_Central_Metaboxes_Image_Advanced_Field {

	/**
	 * Enqueue scripts and styles
	 */
	public static function admin_enqueue_scripts() {
		parent::admin_enqueue_scripts();
		Video_Central_Metaboxes_File_Upload_Field::admin_enqueue_scripts();
		wp_enqueue_script( 'video-central-metaboxes-image-upload', Video_Central_Metaboxes_JS_URL . 'image-upload.js', array( 'video-central-metaboxes-file-upload', 'video-central-metaboxes-image-advanced' ), Video_Central_Metaboxes_VER, true );
	}

	/**
	 * Template for media item
	 */
	public static function print_templates() {
		parent::print_templates();
		Video_Central_Metaboxes_File_Upload_Field::print_templates();
	}
}
