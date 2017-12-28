<?php
/**
 * Input list field.
 */
class Video_Central_Metaboxes_Input_List_Field extends Video_Central_Metaboxes_Choice_Field {

	/**
	 * Enqueue scripts and styles
	 */
	public static function admin_enqueue_scripts() {
		wp_enqueue_style( 'video-central-metaboxes-input-list', Video_Central_Metaboxes_CSS_URL . 'input-list.css', array(), Video_Central_Metaboxes_VER );
		wp_enqueue_script( 'video-central-metaboxes-input-list', Video_Central_Metaboxes_JS_URL . 'input-list.js', array(), Video_Central_Metaboxes_VER, true );
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
		$walker = new Video_Central_Metaboxes_Walker_Input_List( $db_fields, $field, $meta );
		$output = sprintf( '<ul class="video-central-metaboxes-input-list %s %s">',
			$field['collapse'] ? 'collapse' : '',
		 	$field['inline']   ? 'inline'   : ''
		);
		$output .= $walker->walk( $options, $field['flatten'] ? - 1 : 0 );
		$output .= '</ul>';

		return $output;
	}

	/**
	 * Normalize parameters for field
	 *
	 * @param array $field
	 * @return array
	 */
	public static function normalize( $field ) {
		$field = $field['multiple'] ? Video_Central_Metaboxes_Multiple_Values_Field::normalize( $field ) : $field;
		$field = Video_Central_Metaboxes_Input_Field::normalize( $field );
		$field = parent::normalize( $field );
		$field = wp_parse_args( $field, array(
			'collapse' => true,
			'inline'   => null,
		) );

		$field['flatten'] = $field['multiple'] ? $field['flatten'] : true;
		$field['inline'] = ! $field['multiple'] && ! isset( $field['inline'] ) ? true : $field['inline'];

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
		$attributes           = Video_Central_Metaboxes_Input_Field::get_attributes( $field, $value );
		$attributes['id']     = false;
		$attributes['type']   = $field['multiple'] ? 'checkbox' : 'radio';
		$attributes['value']  = $value;

		return $attributes;
	}
}
