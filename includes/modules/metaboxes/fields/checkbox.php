<?php

/**
 * Checkbox field class.
 */
class Video_Central_Metaboxes_Checkbox_Field extends Video_Central_Metaboxes_Input_Field {

	/**
	 * Enqueue scripts and styles.
	 */
	public static function admin_enqueue_scripts() {
		wp_enqueue_style( 'video-central-metaboxes-checkbox', Video_Central_Metaboxes_CSS_URL . 'checkbox.css', array(), Video_Central_Metaboxes_VER );
	}

	/**
	 * Get field HTML
	 *
	 * @param mixed $meta
	 * @param array $field
	 * @return string
	 */
	public static function html( $meta, $field ) {
		$attributes = self::get_attributes( $field, 1 );
		$output     = sprintf(
			'<input %s %s>',
			self::render_attributes( $attributes ),
			checked( ! empty( $meta ), 1, false )
		);
		if ( $field['desc'] ) {
			$output = "<label id='{$field['id']}_description' class='description'>$output {$field['desc']}</label>";
		}
		return $output;
	}

	/**
	 * Do not show field description.
	 *
	 * @param array $field
	 * @return string
	 */
	public static function element_description( $field ) {
		return '';
	}

	/**
	 * Format a single value for the helper functions.
	 *
	 * @param array  $field Field parameter
	 * @param string $value The value
	 * @return string
	 */
	public static function format_single_value( $field, $value ) {
		return $value ? __( 'Yes', 'meta-box' ) : __( 'No', 'meta-box' );
	}
}
