<?php
/**
 * File advanced field class which users WordPress media popup to upload and select files.
 */
class Video_Central_Metaboxes_File_Upload_Field extends Video_Central_Metaboxes_Media_Field {

	/**
	 * Enqueue scripts and styles
	 */
	public static function admin_enqueue_scripts() {
		parent::admin_enqueue_scripts();
		wp_enqueue_style( 'video-central-metaboxes-upload', Video_Central_Metaboxes_CSS_URL . 'upload.css', array( 'video-central-metaboxes-media' ), Video_Central_Metaboxes_VER );
		wp_enqueue_script( 'video-central-metaboxes-file-upload', Video_Central_Metaboxes_JS_URL . 'file-upload.js', array( 'video-central-metaboxes-media' ), Video_Central_Metaboxes_VER, true );
	}

	/**
	 * Template for media item
	 */
	public static function print_templates() {
		parent::print_templates();
		require_once Video_Central_Metaboxes_INC_DIR . 'templates/upload.php';
	}
}
