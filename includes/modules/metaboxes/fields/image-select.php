<?php

/**
 * Image select field class which uses images as radio options.
 */
class Video_Central_Metaboxes_Image_Select_Field extends Video_Central_Metaboxes_Field {

	/**
	 * Enqueue scripts and styles
	 */
	static function admin_enqueue_scripts() {
		wp_enqueue_style( 'video-central-metaboxes-image-select', Video_Central_Metaboxes_CSS_URL . 'image-select.css', array(), Video_Central_Metaboxes_VER );
		wp_enqueue_script( 'video-central-metaboxes-image-select', Video_Central_Metaboxes_JS_URL . 'image-select.js', array( 'jquery' ), Video_Central_Metaboxes_VER, true );
	}

	/**
	 * Get field HTML
	 *
	 * @param mixed $meta
	 * @param array $field
	 * @return string
	 */
	static function html( $meta, $field ) {
		$html = array();
		$tpl  = '<label class="video-central-metaboxes-image-select"><img src="%s"><input type="%s" class="video-central-metaboxes-image_select hidden" name="%s" value="%s"%s></label>';

		$meta = (array) $meta;
		foreach ( $field['options'] as $value => $image ) {
			$html[] = sprintf(
				$tpl,
				$image,
				$field['multiple'] ? 'checkbox' : 'radio',
				$field['field_name'],
				$value,
				checked( in_array( $value, $meta ), true, false )
			);
		}

		return implode( ' ', $html );
	}

	/**
	 * Normalize parameters for field
	 *
	 * @param array $field
	 * @return array
	 */
	static function normalize( $field ) {
		$field = parent::normalize( $field );
		$field['field_name'] .= $field['multiple'] ? '[]' : '';

		return $field;
	}

	/**
	 * Format a single value for the helper functions.
	 *
	 * @param array  $field Field parameter
	 * @param string $value The value
	 * @return string
	 */
	static function format_single_value( $field, $value ) {
		return sprintf( '<img src="%s">', esc_url( $field['options'][ $value ] ) );
	}
}
