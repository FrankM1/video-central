<?php
/**
 * HTML5 range field class.
 */
class Video_Central_Metaboxes_Range_Field extends Video_Central_Metaboxes_Number_Field
{
	/**
	 * Enqueue styles
	 */
	static function admin_enqueue_scripts()
	{
		wp_enqueue_style( 'video-central-metaboxes-range', Video_Central_Metaboxes_CSS_URL . 'range.css', array(), Video_Central_Metaboxes_VER );
	}

	/**
	 * Normalize parameters for field.
	 *
	 * @param array $field
	 *
	 * @return array
	 */
	static function normalize( $field )
	{
		$field = wp_parse_args( $field, array(
			'min'  => 0,
			'max'  => 10,
			'step' => 1,
		) );

		$field = parent::normalize( $field );

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
	static function get_attributes( $field, $value = null )
	{
		$attributes = parent::get_attributes( $field, $value );
		$attributes['type'] = 'range';

		return $attributes;
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
	static function value( $new, $old, $post_id, $field )
	{
		$new = intval( $new );
		$min = intval( $field['min'] );
		$max = intval( $field['max'] );

		if ( $new < $min )
		{
			return $min;
		}
		elseif ( $new > $max )
		{
			return $max;
		}

		return $new;
	}
}
