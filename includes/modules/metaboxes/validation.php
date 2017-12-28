<?php
/**
 * Validation module.
 *
 * @package Meta Box
 */

/**
 * Validation class.
 */
class Video_Central_Metaboxes_Validation
{
	/**
	 * Add hooks when module is loaded.
	 */
	public function __construct()
	{
		add_action( 'video_central_metaboxes_after', array( $this, 'rules' ) );
		add_action( 'video_central_metaboxes_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Output validation rules of each meta box.
	 * The rules are outputted in [data-rules] attribute of an hidden <script> and will be converted into JSON by JS.
	 * @param Video_Central_Metabox $object Meta Box object
	 */
	public function rules( $object )
	{
		if ( ! empty( $object->meta_box['validation'] ) )
		{
			echo '<script type="text/html" class="video-central-metaboxes-validation-rules" data-rules="' . esc_attr( json_encode( $object->meta_box['validation'] ) ) . '"></script>';
		}
	}

	/**
	 * Enqueue scripts for validation.
	 */
	public function enqueue( $object ) {
		if ( empty( $object->meta_box['validation'] ) ) {
			return;
		}
		wp_enqueue_script( 'jquery-validation', Video_Central_Metaboxes_JS_URL . 'jquery.validate.min.js', array( 'jquery' ), Video_Central_Metaboxes_VER, true );
		wp_enqueue_script( 'video-central-metaboxes-validate', Video_Central_Metaboxes_JS_URL . 'validate.js', array( 'jquery-validate' ), Video_Central_Metaboxes_VER, true );
		wp_localize_script( 'video-central-metaboxes-validate', 'rwmbValidate', array(
			'summaryMessage' => esc_html__( 'Please correct the errors highlighted below and try again.', 'meta-box' ),
		) );
	}
}
