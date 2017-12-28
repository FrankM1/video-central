<?php
/**
 * Select field class.
 */
class Video_Central_Metaboxes_Select_Field extends Video_Central_Metaboxes_Choice_Field {

	/**
	 * Enqueue scripts and styles
	 */
	public static function admin_enqueue_scripts() {
		wp_enqueue_style( 'video-central-metaboxes-select', Video_Central_Metaboxes_CSS_URL . 'select.css', array(), Video_Central_Metaboxes_VER );
		wp_enqueue_script( 'video-central-metaboxes-select', Video_Central_Metaboxes_JS_URL . 'select.js', array(), Video_Central_Metaboxes_VER, true );
	}

	/**
	 * Walk options
	 *
	 * @param mixed $meta
	 * @param array $field
	 * @param mixed $options
	 * @param mixed $db_fields
	 *
	 * @return string
	 */
	public static function walk( $field, $options, $db_fields, $meta ) {
		$attributes = self::call( 'get_attributes', $field, $meta );
		$walker     = new Video_Central_Metaboxes_Walker_Select( $db_fields, $field, $meta );
		$output     = sprintf(
			'<select %s>',
			self::render_attributes( $attributes )
		);
		if ( false === $field['multiple'] ) {
			$output .= $field['placeholder'] ? '<option value="">' . esc_html( $field['placeholder'] ) . '</option>' : '';
		}
		$output .= $walker->walk( $options, $field['flatten'] ? - 1 : 0 );
		$output .= '</select>';
		$output .= self::get_select_all_html( $field );
		return $output;
	}

	/**
	 * Normalize parameters for field
	 *
	 * @param array $field
	 * @return array
	 */
	public static function normalize( $field ) {
		$field = parent::normalize( $field );
		$field = $field['multiple'] ? Video_Central_Metaboxes_Multiple_Values_Field::normalize( $field ) : $field;
		$field = wp_parse_args( $field, array(
			'size'            => $field['multiple'] ? 5 : 0,
			'select_all_none' => false,
		) );

		return $field;
	}

	/**
	 * Get the attributes for a field
	 *
	 * @param array $field
	 * @param mixed $value
	 *
	 * @return array
	 */
	public static function get_attributes( $field, $value = null ) {
		$attributes = parent::get_attributes( $field, $value );
		$attributes = wp_parse_args( $attributes, array(
			'multiple' => $field['multiple'],
			'size'     => $field['size'],
		) );

		return $attributes;
	}

	/**
	 * Get html for select all|none for multiple select
	 *
	 * @param array $field
	 * @return string
	 */
	public static function get_select_all_html( $field ) {
		if ( $field['multiple'] && $field['select_all_none'] ) {
			return '<div class="video-central-metaboxes-select-all-none">' . __( 'Select', 'meta-box' ) . ': <a data-type="all" href="#">' . __( 'All', 'meta-box' ) . '</a> | <a data-type="none" href="#">' . __( 'None', 'meta-box' ) . '</a></div>';
		}
		return '';
	}
}
