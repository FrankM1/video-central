<?php

/**
 * WYSIWYG (editor) field class.
 */
class Video_Central_Metaboxes_Wysiwyg_Field extends Video_Central_Metaboxes_Field {

	/**
	 * Array of cloneable editors.
	 *
	 * @var array
	 */
	static $cloneable_editors = array();

	/**
	 * Enqueue scripts and styles.
	 */
	static function admin_enqueue_scripts() {
		wp_enqueue_style( 'video-central-metaboxes-wysiwyg', Video_Central_Metaboxes_CSS_URL . 'wysiwyg.css', array(), Video_Central_Metaboxes_VER );
		wp_enqueue_script( 'video-central-metaboxes-wysiwyg', Video_Central_Metaboxes_JS_URL . 'wysiwyg.js', array( 'jquery' ), Video_Central_Metaboxes_VER, true );
	}

	/**
	 * Change field value on save
	 *
	 * @param mixed $new
	 * @param mixed $old
	 * @param int   $post_id
	 * @param array $field
	 * @return string
	 */
	static function value( $new, $old, $post_id, $field ) {
		return  $field['raw'] ? $new : wpautop( $new );
	}

	/**
	 * Get field HTML
	 *
	 * @param mixed $meta
	 * @param array $field
	 * @return string
	 */
	static function html( $meta, $field ) {
		// Using output buffering because wp_editor() echos directly
		ob_start();

		$field['options']['textarea_name'] = $field['field_name'];
		$attributes = self::get_attributes( $field );

		// Use new wp_editor() since WP 3.3
		wp_editor( $meta, $attributes['id'], $field['options'] );

		return ob_get_clean();
	}

	/**
	 * Escape meta for field output
	 *
	 * @param mixed $meta
	 * @return mixed
	 */
	static function esc_meta( $meta ) {
		return $meta;
	}

	/**
	 * Normalize parameters for field
	 *
	 * @param array $field
	 * @return array
	 */
	static function normalize( $field ) {
		$field = parent::normalize( $field );
		$field = wp_parse_args( $field, array(
			'raw'     => false,
			'options' => array(),
		) );

		$field['options'] = wp_parse_args( $field['options'], array(
			'editor_class' => 'video-central-metaboxes-wysiwyg',
			'dfw'          => true, // Use default WordPress full screen UI
		) );

		// Keep the filter to be compatible with previous versions
		$field['options'] = apply_filters( 'video_central_metaboxes_wysiwyg_settings', $field['options'] );

		return $field;
	}
}
