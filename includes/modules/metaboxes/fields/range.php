<?php
/**
 * HTML5 range field class.
 */
class Video_Central_Metaboxes_Range_Field extends Video_Central_Metaboxes_Number_Field {

	/**
	 * Get field HTML
	 *
	 * @param mixed $meta
	 * @param array $field
	 * @return string
	 */
	public static function html( $meta, $field ) {
		$output = parent::html( $meta, $field );
		$output .= sprintf( '<span class="video-central-metaboxes-output">%s</span>', $meta );
		return $output;
	}

	/**
	 * Enqueue styles
	 */
	public static function admin_enqueue_scripts() {
		wp_enqueue_style( 'video-central-metaboxes-range', Video_Central_Metaboxes_CSS_URL . 'range.css', array(), Video_Central_Metaboxes_VER );
		wp_enqueue_script( 'video-central-metaboxes-range', Video_Central_Metaboxes_JS_URL . 'range.js', array(), Video_Central_Metaboxes_VER, true );
	}

	/**
	 * Normalize parameters for field.
	 *
	 * @param array $field
	 * @return array
	 */
	public static function normalize( $field ) {
		$field = wp_parse_args( $field, array(
			'max' => 10,
		) );
		$field = parent::normalize( $field );
		return $field;
	}

	/**
	 * Ensure number in range.
	 *
	 * @param mixed $new
	 * @param mixed $old
	 * @param int   $post_id
	 * @param array $field
	 *
	 * @return int
	 */
	public static function value( $new, $old, $post_id, $field ) {
		$new = intval( $new );
		$min = intval( $field['min'] );
		$max = intval( $field['max'] );

		if ( $new < $min ) {
			return $min;
		}
		if ( $new > $max ) {
			return $max;
		}
		return $new;
	}
}
