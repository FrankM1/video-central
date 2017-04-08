<?php
/**
 * Password field class.
 */
class Video_Central_Metaboxes_Password_Field extends Video_Central_Metaboxes_Text_Field {

	/**
	 * Store secured password in the database.
	 *
	 * @param mixed $new
	 * @param mixed $old
	 * @param int   $post_id
	 * @param array $field
	 * @return string
	 */
	static function value( $new, $old, $post_id, $field ) {
		$new = $new != $old ? wp_hash_password( $new ) : $new;
		return $new;
	}
}
