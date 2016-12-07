<?php
/**
 * Media field class which users WordPress media popup to upload and select files.
 */
class Video_Central_Metaboxes_Media_Field extends Video_Central_Metaboxes_Field
{
	/**
	 * Enqueue scripts and styles
	 *
	 * @return void
	 */
	static function admin_enqueue_scripts()
	{
		wp_enqueue_media();
		wp_enqueue_style( 'video-central-metaboxes-media', Video_Central_Metaboxes_CSS_URL . 'media.css', array(), Video_Central_Metaboxes_VER );
		wp_enqueue_script( 'video-central-metaboxes-media', Video_Central_Metaboxes_JS_URL . 'media.js', array( 'jquery-ui-sortable', 'underscore', 'backbone' ), Video_Central_Metaboxes_VER, true );

		self::localize_script( 'video-central-metaboxes-media', 'i18nVcmMedia', array(
			'add'                => apply_filters( 'video_central_metaboxes_media_add_string', _x( '+ Add Media', 'media', 'video-central' ) ),
			'single'             => apply_filters( 'video_central_metaboxes_media_single_files_string', _x( ' file', 'media', 'video-central' ) ),
			'multiple'           => apply_filters( 'video_central_metaboxes_media_multiple_files_string', _x( ' files', 'media', 'video-central' ) ),
			'remove'             => apply_filters( 'video_central_metaboxes_media_remove_string', _x( 'Remove', 'media', 'video-central' ) ),
			'edit'               => apply_filters( 'video_central_metaboxes_media_edit_string', _x( 'Edit', 'media', 'video-central' ) ),
			'view'               => apply_filters( 'video_central_metaboxes_media_view_string', _x( 'View', 'media', 'video-central' ) ),
			'noTitle'            => _x( 'No Title', 'media', 'video-central' ),
			'loadingUrl'         => video_central()->core_assets_url .'/admin/images/loader.gif',
			'extensions'         => self::get_mime_extensions(),
			'select'             => apply_filters( 'video_central_metaboxes_media_select_string', _x( 'Select Files', 'media', 'video-central' ) ),
			'or'                 => apply_filters( 'video_central_metaboxes_media_or_string', _x( 'or', 'media', 'video-central' ) ),
			'uploadInstructions' => apply_filters( 'video_central_metaboxes_media_upload_instructions_string', _x( 'Drop files here to upload', 'media', 'video-central' ) ),
		) );
	}

	/**
	 * Add actions
	 *
	 * @return void
	 */
	static function add_actions()
	{
		// Print attachment templates
		add_action( 'print_media_templates', array( __CLASS__, 'print_templates' ) );
	}

	/**
	 * Get field HTML
	 *
	 * @param mixed $meta
	 * @param array $field
	 *
	 * @return string
	 */
	static function html( $meta, $field )
	{
		$meta       = (array) $meta;
		$meta       = implode( ',', $meta );
		$attributes = self::get_attributes( $field, $meta );

		$html = sprintf(
			'<input %s>
			<div class="video-central-metaboxes-media-view" data-mime-type="%s" data-max-files="%s" data-force-delete="%s"></div>',
			self::render_attributes( $attributes ),
			$field['mime_type'],
			$field['max_file_uploads'],
			$field['force_delete'] ? 'true' : 'false'
		);

		return $html;
	}

	/**
	 * Normalize parameters for field
	 *
	 * @param array $field
	 *
	 * @return array
	 */
	static function normalize( $field )
	{
		$field = parent::normalize( $field );
		$field = wp_parse_args( $field, array(
			'std'              => array(),
			'mime_type'        => '',
			'max_file_uploads' => 0,
			'force_delete'     => false,
		) );

		$field['multiple'] = true;

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
		$attributes             = parent::get_attributes( $field, $value );
		$attributes['type']     = 'hidden';
		$attributes['name']    .= ! $field['clone'] && $field['multiple'] ? '[]' : '';
		$attributes['disabled'] = true;
		$attributes['id']       = false;
		$attributes['value']    = $value;

		return $attributes;
	}

	/**
	 * Save meta value
	 *
	 * @param $new
	 * @param $old
	 * @param $post_id
	 * @param $field
	 */
	static function save( $new, $old, $post_id, $field )
	{
		delete_post_meta( $post_id, $field['id'] );
		parent::save( $new, array(), $post_id, $field );
	}

    /**
	 * Get supported mime extensions.
	 *
	 * @return array
	 */
	protected static function get_mime_extensions() {
		$mime_types = wp_get_mime_types();
		$extensions = array();
		foreach ( $mime_types as $ext => $mime ) {
			$ext               = explode( '|', $ext );
			$extensions[ $mime ] = $ext;

			$mime_parts = explode( '/', $mime );
			if ( empty( $extensions[ $mime_parts[0] ] ) ) {
				$extensions[ $mime_parts[0] ] = array();
			}
			$extensions[ $mime_parts[0] ] = $extensions[ $mime_parts[0] . '/*' ] = array_merge( $extensions[ $mime_parts[0] ], $ext );

		}

		return $extensions;
	}

	/**
	 * Template for media item
	 * @return void
	 */
	static function print_templates()
	{
        require_once video_central()->includes_dir . 'modules/metaboxes/templates/media.php';
	}
}
