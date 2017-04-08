<?php
/**
 * Radio field class.
 */
class Video_Central_Metaboxes_Radio_Field extends Video_Central_Metaboxes_Input_List_Field {

	/**
	 * Normalize parameters for field
	 *
	 * @param array $field
	 * @return array
	 */
	static function normalize( $field ) {
		$field['multiple'] = false;
		$field = parent::normalize( $field );

		return $field;
	}
}
