<?php
/**
 * Select tree field class.
 */
class Video_Central_Metaboxes_Select_Tree_Field extends Video_Central_Metaboxes_Select_Field {

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
		$walker = new Video_Central_Metaboxes_Walker_Select_Tree( $db_fields, $field, $meta );
		return $walker->walk( $options );
	}

	/**
	 * Enqueue scripts and styles
	 */
	public static function admin_enqueue_scripts() {
		parent::admin_enqueue_scripts();
		wp_enqueue_style( 'video-central-metaboxes-select-tree', Video_Central_Metaboxes_CSS_URL . 'select-tree.css', array( 'video-central-metaboxes-select' ), Video_Central_Metaboxes_VER );
		wp_enqueue_script( 'video-central-metaboxes-select-tree', Video_Central_Metaboxes_JS_URL . 'select-tree.js', array( 'video-central-metaboxes-select' ), Video_Central_Metaboxes_VER, true );
	}

	/**
	 * Normalize parameters for field
	 *
	 * @param array $field
	 * @return array
	 */
	public static function normalize( $field ) {
		$field['multiple'] = true;
		$field['size']     = 0;
		$field             = parent::normalize( $field );

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
		$attributes             = parent::get_attributes( $field, $value );
		$attributes['multiple'] = false;
		$attributes['id']       = false;

		return $attributes;
	}
}
