<?php
/**
 * Select advanced field which uses select2 library.
 */
class Video_Central_Metaboxes_Select_Advanced_Field extends Video_Central_Metaboxes_Select_Field {

	/**
	 * Enqueue scripts and styles
	 */
	public static function admin_enqueue_scripts() {
		parent::admin_enqueue_scripts();
		wp_enqueue_style( 'video-central-metaboxes-select2', Video_Central_Metaboxes_CSS_URL . 'select2/select2.css', array(), '4.0.1' );
		wp_enqueue_style( 'video-central-metaboxes-select-advanced', Video_Central_Metaboxes_CSS_URL . 'select-advanced.css', array(), Video_Central_Metaboxes_VER );

		wp_register_script( 'video-central-metaboxes-select2', Video_Central_Metaboxes_JS_URL . 'select2/select2.min.js', array( 'jquery' ), '4.0.2', true );

		// Localize
		$dependencies = array( 'video-central-metaboxes-select2', 'video-central-metaboxes-select' );
		$locale       = str_replace( '_', '-', get_locale() );
		$locale_short = substr( $locale, 0, 2 );
		$locale       = file_exists( Video_Central_Metaboxes_DIR . "js/select2/i18n/$locale.js" ) ? $locale : $locale_short;

		if ( file_exists( Video_Central_Metaboxes_DIR . "js/select2/i18n/$locale.js" ) ) {
			wp_register_script( 'video-central-metaboxes-select2-i18n', Video_Central_Metaboxes_JS_URL . "select2/i18n/$locale.js", array( 'video-central-metaboxes-select2' ), '4.0.2', true );
			$dependencies[] = 'video-central-metaboxes-select2-i18n';
		}

		wp_enqueue_script( 'video-central-metaboxes-select', Video_Central_Metaboxes_JS_URL . 'select.js', array( 'jquery' ), Video_Central_Metaboxes_VER, true );
		wp_enqueue_script( 'video-central-metaboxes-select-advanced', Video_Central_Metaboxes_JS_URL . 'select-advanced.js', $dependencies, Video_Central_Metaboxes_VER, true );
	}

	/**
	 * Normalize parameters for field
	 *
	 * @param array $field
	 * @return array
	 */
	public static function normalize( $field ) {
		$field = wp_parse_args( $field, array(
			'js_options'  => array(),
			'placeholder' => __( 'Select an item', 'meta-box' ),
		) );

		$field = parent::normalize( $field );

		$field['js_options'] = wp_parse_args( $field['js_options'], array(
			'allowClear'  => true,
			'width'       => 'none',
			'placeholder' => $field['placeholder'],
		) );

		return $field;
	}

	/**
	 * Get the attributes for a field
	 *
	 * @param array $field
	 * @param mixed $value
	 * @return array
	 */
	public static function get_attributes( $field, $value = null ) {
		$attributes = parent::get_attributes( $field, $value );
		$attributes = wp_parse_args( $attributes, array(
			'data-options' => wp_json_encode( $field['js_options'] ),
		) );

		return $attributes;
	}
}
