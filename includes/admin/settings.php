<?php

/**
 * Video Central Admin Settings
 *
 * @package Video Central
 * @subpackage Administration
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/** Sections ******************************************************************/

/**
 * Get the Videos settings sections.
 *
 * @since 1.0.0
 * @return array
 */
function video_central_admin_get_settings_sections() {

	return (array) apply_filters( 'video_central_admin_get_settings_sections', array(

		'video_central_settings_users' => array(
			'title'    => __( 'Video Central User Settings', 'video_central' ),
			'callback' => 'video_central_admin_setting_callback_user_section',
			'page'     => 'discussion'
		),

		'video_central_settings_features' => array(
			'title'    => __( 'Video Features', 'video_central' ),
			'callback' => 'video_central_admin_setting_callback_features_section',
			'page'     => 'discussion'
		),
		'video_central_settings_theme_compat' => array(
			'title'    => __( 'Video Theme Packages', 'video_central' ),
			'callback' => 'video_central_admin_setting_callback_subtheme_section',
			'page'     => 'general'
		),
		'video_central_settings_per_page' => array(
			'title'    => __( 'Videos Per Page', 'video_central' ),
			'callback' => 'video_central_admin_setting_callback_per_page_section',
			'page'     => 'reading'
		),

		'video_central_settings_root_slugs' => array(
			'title'    => __( 'Videos Root Slug', 'video_central' ),
			'callback' => 'video_central_admin_setting_callback_root_slug_section',
			'page'     => 'permalink'
		),
		'video_central_settings_single_slugs' => array(
			'title'    => __( 'Single Video Slugs', 'video_central' ),
			'callback' => 'video_central_admin_setting_callback_single_slug_section',
			'page'     => 'permalink',
		),


	) );
}

/**
 * Get all of the settings fields.
 *
 * @since 1.0.0
 * @return type
 */
function video_central_admin_get_settings_fields() {
	return (array) apply_filters( 'video_central_admin_get_settings_fields', array(

		/** User Section ******************************************************/

		'video_central_settings_users' => array(

		),

		/** Features Section **************************************************/

		'video_central_settings_features' => array(

			// Allow topic tags
			'_video_central_allow_video_categories' => array(
				'title'             => __( 'Video categories', 'video_central' ),
				'callback'          => 'video_central_admin_setting_callback_video_categories',
				'sanitize_callback' => 'intval',
				'args'              => array()
			),

			// Allow topic tags
			'_video_central_allow_video_tags' => array(
				'title'             => __( 'Video tags', 'video_central' ),
				'callback'          => 'video_central_admin_setting_callback_video_tags',
				'sanitize_callback' => 'intval',
				'args'              => array()
			),

			// Allow topic tags
			'_video_central_allow_search' => array(
				'title'             => __( 'Search', 'video_central' ),
				'callback'          => 'video_central_admin_setting_callback_search',
				'sanitize_callback' => 'intval',
				'args'              => array()
			),

			// What to Slider show on Root
			'_video_central_show_slider_on_root' => array(
				'title'             => __( 'Videos slider should show on root', 'video_central' ),
				'callback'          => 'video_central_admin_setting_callback_show_slider_on_root',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array()
			),

			// What to show on Video Root
			'_video_central_allow_comments' => array(
				'title'             => __( 'Comments', 'video_central' ),
				'callback'          => 'video_central_admin_setting_callback_allow_comments',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array()
			),

		),

		/** Theme Packages ****************************************************/

		'video_central_settings_theme_compat' => array(

			// Theme package setting
			'_video_central_theme_package_id' => array(
				'title'             => __( 'Current Package', 'video_central' ),
				'callback'          => 'video_central_admin_setting_callback_subtheme_id',
				'sanitize_callback' => 'esc_sql',
				'args'              => array()
			)
		),

		/** Per Page Section **************************************************/

		'video_central_settings_per_page' => array(

			// Replies per page setting
			'_video_central_videos_per_page' => array(
				'title'             => __( 'Videos', 'video_central' ),
				'callback'          => 'video_central_admin_setting_callback_videos_per_page',
				'sanitize_callback' => 'intval',
				'args'              => array()
			),

		),

		/** Front Slugs *******************************************************/

		'video_central_settings_root_slugs' => array(

			// Root slug setting
			'_video_central_root_slug' => array(
				'title'             => __( 'Videos Root', 'video_central' ),
				'callback'          => 'video_central_admin_setting_callback_root_slug',
				'sanitize_callback' => 'esc_sql',
				'args'              => array()
			),

			// Include root setting
			'_video_central_include_root' => array(
				'title'             => __( 'Videos Prefix', 'video_central' ),
				'callback'          => 'video_central_admin_setting_callback_include_root',
				'sanitize_callback' => 'intval',
				'args'              => array()
			),

			// What to show on Video Root
			'_video_central_show_on_root' => array(
				'title'             => __( 'Videos root should show', 'video_central' ),
				'callback'          => 'video_central_admin_setting_callback_show_on_root',
				'sanitize_callback' => 'sanitize_text_field',
				'args'              => array()
			),

		),

		/** Single Slugs ******************************************************/

		'video_central_settings_single_slugs' => array(

			// Video slug setting
			'_video_central_video_slug' => array(
				'title'             => __( 'Video', 'video_central' ),
				'callback'          => 'video_central_admin_setting_callback_video_slug',
				'sanitize_callback' => 'sanitize_title',
				'args'              => array()
			),

			// video tag slug setting
			'_video_central_video_tag_tax_slug' => array(
				'title'             => __( 'Video Tag', 'video_central' ),
				'callback'          => 'video_central_admin_setting_callback_video_tag_tax_slug',
				'sanitize_callback' => 'sanitize_title',
				'args'              => array()
			),

			// video tag slug setting
			'_video_central_video_category_tax_slug' => array(
				'title'             => __( 'Video Category', 'video_central' ),
				'callback'          => 'video_central_admin_setting_callback_video_category_tax_slug',
				'sanitize_callback' => 'sanitize_title',
				'args'              => array()
			),

			// View slug setting
			'_video_central_view_slug' => array(
				'title'             => __( 'Video View', 'video_central' ),
				'callback'          => 'video_central_admin_setting_callback_view_slug',
				'sanitize_callback' => 'sanitize_title',
				'args'              => array()
			),

			// Search slug setting
			'_video_central_search_slug' => array(
				'title'             => __( 'Search', 'video_central' ),
				'callback'          => 'video_central_admin_setting_callback_search_slug',
				'sanitize_callback' => 'sanitize_title',
				'args'              => array()
			)
		),

		/** User Slugs ********************************************************/

		'video_central_settings_user_slugs' => array(

			// User slug setting
			'_video_central_user_slug' => array(
				'title'             => __( 'User Base', 'video_central' ),
				'callback'          => 'video_central_admin_setting_callback_user_slug',
				'sanitize_callback' => 'sanitize_title',
				'args'              => array()
			),

		),

	) );
}

/**
 * Get settings fields by section.
 *
 * @since 1.0.0
 * @param string $section_id
 * @return mixed False if section is invalid, array of fields otherwise.
 */
function video_central_admin_get_settings_fields_for_section( $section_id = '' ) {

	// Bail if section is empty
	if ( empty( $section_id ) )
		return false;

	$fields = video_central_admin_get_settings_fields();
	$retval = isset( $fields[$section_id] ) ? $fields[$section_id] : false;

	return (array) apply_filters( 'video_central_admin_get_settings_fields_for_section', $retval, $section_id );
}

/** User Section **************************************************************/

/**
 * User settings section description for the settings page
 *
 * @since 1.0.0
 */
function video_central_admin_setting_callback_user_section() {
?>

	<p><?php esc_html_e( 'Setting time limits and other user posting capabilities', 'video_central' ); ?></p>

<?php
}


/** Features Section **********************************************************/

/**
 * Features settings section description for the settings page
 *
 * @since 1.0.0
 */
function video_central_admin_setting_callback_features_section() {
?>

	<p><?php esc_html_e( 'Video Central features that can be toggled on and off', 'video_central' ); ?></p>

<?php
}

/**
 * Allow video tags
 *
 * @since 1.0.0
 *
 * @uses checked() To display the checked attribute
 */
function video_central_admin_setting_callback_video_tags() {
?>

	<input name="_video_central_allow_video_tags" id="_video_central_allow_video_tags" type="checkbox" value="1" <?php checked( video_central_allow_video_tags( true ) ); video_central_maybe_admin_setting_disabled( '_video_central_allow_video_tags' ); ?> />
	<label for="_video_central_allow_video_tags"><?php esc_html_e( 'Allow video tags', 'video_central' ); ?></label>

<?php
}

/**
 * Allow video wide search
 *
 * @since 1.0.0
 *
 * @uses checked() To display the checked attribute
 */
function video_central_admin_setting_callback_video_categories() {
?>

	<input name="_video_central_allow_video_categories" id="_video_central_allow_video_categories" type="checkbox" value="1" <?php checked( video_central_allow_video_categories( true ) ); video_central_maybe_admin_setting_disabled( '_video_central_allow_video_categories' ); ?> />
	<label for="_video_central_allow_video_categories"><?php esc_html_e( 'Allow video categories', 'video_central' ); ?></label>

<?php
}

/**
 * Allow video wide search
 *
 * @since 1.0.0
 *
 * @uses checked() To display the checked attribute
 */
function video_central_admin_setting_callback_search() {
?>

	<input name="_video_central_allow_search" id="_video_central_allow_search" type="checkbox" value="1" <?php checked( video_central_allow_search( true ) ); video_central_maybe_admin_setting_disabled( '_video_central_allow_search' ); ?> />
	<label for="_video_central_allow_search"><?php esc_html_e( 'Allow video wide search', 'video_central' ); ?></label>

<?php
}

/**
 * Allow video wide search
 *
 * @since 1.0.0
 *
 * @uses checked() To display the checked attribute
 */
function video_central_admin_setting_callback_allow_comments() {
?>

	<input name="_video_central_allow_comments" id="_video_central_allow_comments" type="checkbox" value="1" <?php checked( video_central_allow_comments( true ) ); video_central_maybe_admin_setting_disabled( '_video_central_allow_comments' ); ?> />
	<label for="_video_central_allow_comments"><?php esc_html_e( 'Allow video comments', 'video_central' ); ?></label>

<?php
}

/**
 * Main subtheme section
 *
 * @since 1.0.0
 */
function video_central_admin_setting_callback_subtheme_section() {
?>

	<p><?php esc_html_e( 'How your video content is displayed within your existing theme.', 'video_central' ); ?></p>

<?php
}

/**
 * Use the WordPress editor setting field
 *
 * @since 1.0.0
 *
 * @uses checked() To display the checked attribute
 */
function video_central_admin_setting_callback_subtheme_id() {

	// Declare locale variable
	$theme_options   = '';
	$current_package = video_central_get_theme_package_id( 'default' );

	// Note: This should never be empty. /templates/ is the
	// canonical backup if no other packages exist. If there's an error here,
	// something else is wrong.
	//
	// @see Video Central::register_theme_packages()
	foreach ( (array) video_central()->theme_compat->packages as $id => $theme ) {
		$theme_options .= '<option value="' . esc_attr( $id ) . '"' . selected( $theme->id, $current_package, false ) . '>' . sprintf( esc_html__( '%1$s - %2$s', 'video_central' ), esc_html( $theme->name ), esc_html( str_replace( WP_CONTENT_DIR, '', $theme->dir ) ) )  . '</option>';
	}

	if ( !empty( $theme_options ) ) : ?>

		<select name="_video_central_theme_package_id" id="_video_central_theme_package_id" <?php video_central_maybe_admin_setting_disabled( '_video_central_theme_package_id' ); ?>><?php echo $theme_options ?></select>
		<label for="_video_central_theme_package_id"><?php esc_html_e( 'will serve all Video Central templates', 'video_central' ); ?></label>

	<?php else : ?>

		<p><?php esc_html_e( 'No template packages available.', 'video_central' ); ?></p>

	<?php endif;
}


/** Per Page Section **********************************************************/

/**
 * Per page settings section description for the settings page
 *
 * @since 1.0.0
 */
function video_central_admin_setting_callback_per_page_section() {
?>

	<p><?php esc_html_e( 'How many videos to show per page', 'video_central' ); ?></p>

<?php
}

/**
 * Replies per page setting field
 *
 * @since 1.0.0
 *
 * @uses video_central_form_option() To output the option value
 */
function video_central_admin_setting_callback_videos_per_page() {
?>

	<input name="_video_central_videos_per_page" id="_video_central_videos_per_page" type="number" min="1" step="1" value="<?php video_central_form_option( '_video_central_videos_per_page', '16' ); ?>" class="small-text"<?php video_central_maybe_admin_setting_disabled( '_video_central_replies_per_page' ); ?> />
	<label for="_video_central_videos_per_page"><?php esc_html_e( 'per page', 'video_central' ); ?></label>

<?php
}

/** Slug Section **************************************************************/

/**
 * Slugs settings section description for the settings page
 *
 * @since 1.0.0
 */
function video_central_admin_setting_callback_root_slug_section() {

	// Flush rewrite rules when this section is saved
	if ( isset( $_GET['settings-updated'] ) && isset( $_GET['page'] ) )
		flush_rewrite_rules(); ?>

	<p><?php esc_html_e( 'Customize your videos root. Create a WordPress Page and use Shortcodes for more flexibility.', 'video_central' ); ?></p>

<?php
}

/**
 * Root slug setting field
 *
 * @since 1.0.0
 *
 * @uses video_central_form_option() To output the option value
 */
function video_central_admin_setting_callback_root_slug() {
?>

        <input name="_video_central_root_slug" id="_video_central_root_slug" type="text" class="regular-text code" value="<?php video_central_form_option( '_video_central_root_slug', 'videos', true ); ?>"<?php video_central_maybe_admin_setting_disabled( '_video_central_root_slug' ); ?> />

<?php
	// Slug Check
	video_central_form_slug_conflict_check( '_video_central_root_slug', 'videos' );
}

/**
 * Include root slug setting field
 *
 * @since 1.0.0
 *
 * @uses checked() To display the checked attribute
 */
function video_central_admin_setting_callback_include_root() {
?>

	<input name="_video_central_include_root" id="_video_central_include_root" type="checkbox" value="1" <?php checked( video_central_include_root_slug() ); video_central_maybe_admin_setting_disabled( '_video_central_include_root' ); ?> />
	<label for="_video_central_include_root"><?php esc_html_e( 'Prefix all video content with the Video Root slug (Recommended)', 'video_central' ); ?></label>

<?php
}

/**
 * Include root slug setting field
 *
 * @since 1.0.0
 *
 * @uses checked() To display the checked attribute
 */
function video_central_admin_setting_callback_show_slider_on_root() {
?>

	<input name="_video_central_show_slider_on_root" id="_video_central_show_slider_on_root" type="checkbox" value="1" <?php checked( video_central_show_slider_on_root() ); video_central_maybe_admin_setting_disabled( '_video_central_show_slider_on_root' ); ?> />
	<label for="_video_central_show_slider_on_root"><?php esc_html_e( 'Display featured video slider', 'video_central' ); ?></label>

<?php
}

/**
 * Include root slug setting field
 *
 * @since 1.0.0
 *
 * @uses checked() To display the checked attribute
 */
function video_central_admin_setting_callback_show_on_root() {

	// Current setting
	$show_on_root = video_central_show_on_root();

	// Options for video root output
	$root_options = array(
		'videos' => array(
			'name' => __( 'Video Index', 'video_central' )
		),
		'latest' => array(
			'name' => __( 'Latest Videos', 'video_central' )
		)
	); ?>

	<select name="_video_central_show_on_root" id="_video_central_show_on_root" <?php video_central_maybe_admin_setting_disabled( '_video_central_show_on_root' ); ?>>

		<?php foreach ( $root_options as $option_id => $details ) : ?>

			<option <?php selected( $show_on_root, $option_id ); ?> value="<?php echo esc_attr( $option_id ); ?>"><?php echo esc_html( $details['name'] ); ?></option>

		<?php endforeach; ?>

	</select>

<?php
}

/** Single Slugs **************************************************************/

/**
 * Slugs settings section description for the settings page
 *
 * @since 1.0.0
 */
function video_central_admin_setting_callback_single_slug_section() {

    // Flush rewrite rules when this section is saved
    if ( isset( $_GET['settings-updated'] ) && isset( $_GET['page'] ) )
        flush_rewrite_rules();
?>

	<p><?php printf( esc_html__( 'Custom slugs for single videos, categories, tags, views, and search.', 'video_central' ), get_admin_url( null, 'options-permalink.php' ) ); ?></p>

<?php
}

/**
 * Video slug setting field
 *
 * @since 1.0.0
 *
 * @uses video_central_form_option() To output the option value
 */
function video_central_admin_setting_callback_video_slug() {
?>

	<input name="_video_central_video_slug" id="_video_central_video_slug" type="text" class="regular-text code" value="<?php video_central_form_option( '_video_central_video_slug', 'video', true ); ?>"<?php video_central_maybe_admin_setting_disabled( '_video_central_video_slug' ); ?> />

<?php
	// Slug Check
	video_central_form_slug_conflict_check( '_video_central_video_slug', 'video' );
}

/**
 * Video tag slug setting field
 *
 * @since 1.0.0
 *
 * @uses video_central_form_option() To output the option value
 */
function video_central_admin_setting_callback_video_category_tax_slug() {
?>

	<input name="_video_central_video_category_tax_slug" id="_video_central_video_category_tax_slug" type="text" class="regular-text code" value="<?php video_central_form_option( '_video_central_video_category_tax_slug', 'video-categories', true ); ?>"<?php video_central_maybe_admin_setting_disabled( '_video_central_video_category_tax_slug' ); ?> />

<?php

	// Slug Check
	video_central_form_slug_conflict_check( '_video_central_video_category_tax_slug', 'video-category' );
}

/**
 * Topic tag slug setting field
 *
 * @since 1.0.0
 *
 * @uses video_central_form_option() To output the option value
 */
function video_central_admin_setting_callback_video_tag_tax_slug() {
?>

	<input name="_video_central_video_tag_tax_slug" id="_video_central_video_tag_tax_slug" type="text" class="regular-text code" value="<?php video_central_form_option( '_video_central_video_tag_tax_slug', 'video-tag', true ); ?>"<?php video_central_maybe_admin_setting_disabled( '_video_central_video_tag_slug' ); ?> />

<?php

	// Slug Check
	video_central_form_slug_conflict_check( '_video_central_video_tag_tax_slug', 'video-tag' );
}


/**
 * View slug setting field
 *
 * @since 1.0.0
 *
 * @uses video_central_form_option() To output the option value
 */
function video_central_admin_setting_callback_view_slug() {
?>

	<input name="_video_central_view_slug" id="_video_central_view_slug" type="text" class="regular-text code" value="<?php video_central_form_option( '_video_central_view_slug', 'view', true ); ?>"<?php video_central_maybe_admin_setting_disabled( '_video_central_view_slug' ); ?> />

<?php
	// Slug Check
	video_central_form_slug_conflict_check( '_video_central_view_slug', 'view' );
}

/**
 * Search slug setting field
 *
 * @since 1.0.0
 *
 * @uses video_central_form_option() To output the option value
 */
function video_central_admin_setting_callback_search_slug() {
?>

	<input name="_video_central_search_slug" id="_video_central_search_slug" type="text" class="regular-text code" value="<?php video_central_form_option( '_video_central_search_slug', 'search', true ); ?>"<?php video_central_maybe_admin_setting_disabled( '_video_central_search_slug' ); ?> />

<?php
	// Slug Check
	video_central_form_slug_conflict_check( '_video_central_search_slug', 'search' );
}


/** Settings Page *************************************************************/

/**
 * The main settings page
 *
 * @since 1.0.0
 *
 * @uses screen_icon() To display the screen icon
 * @uses settings_fields() To output the hidden fields for the form
 * @uses do_settings_sections() To output the settings sections
 */
function video_central_admin_settings() {
?>

	<div class="wrap">

		<?php screen_icon(); ?>

		<h2><?php esc_html_e( 'Video Central Settings', 'video_central' ) ?></h2>

		<form action="options.php" method="post">

			<?php settings_fields( 'video_central' ); ?>

			<?php do_settings_sections( 'video_central' ); ?>

			<p class="submit">
				<input type="submit" name="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'video_central' ); ?>" />
			</p>

		</form>
	</div>

<?php
}


/** Helpers *******************************************************************/

/**
 * Disable a settings field if the value is forcibly set in Video Central's global
 * options array.
 *
 * @since 1.0.0
 *
 * @param string $option_key
 */
function video_central_maybe_admin_setting_disabled( $option_key = '' ) {
	disabled( isset( video_central()->options[$option_key] ) );
}

/**
 * Output settings API option
 *
 * @since 1.0.0
 *
 * @uses video_central_get_video_central_form_option()
 *
 * @param string $option
 * @param string $default
 * @param bool $slug
 */
function video_central_form_option( $option, $default = '' , $slug = false ) {
	echo video_central_get_form_option( $option, $default, $slug );
}
	/**
	 * Return settings API option
	 *
	 * @since 1.0.0
	 *
	 * @uses get_option()
	 * @uses esc_attr()
	 * @uses apply_filters()
	 *
	 * @param string $option
	 * @param string $default
	 * @param bool $slug
	 */
	function video_central_get_form_option( $option, $default = '', $slug = false ) {

		// Get the option and sanitize it
		$value = get_option( $option, $default );

		// Slug?
		if ( true === $slug ) {
			$value = esc_attr( apply_filters( 'editable_slug', $value ) );

		// Not a slug
		} else {
			$value = esc_attr( $value );
		}

		// Fallback to default
		if ( empty( $value ) )
			$value = $default;

		// Allow plugins to further filter the output
		return apply_filters( 'video_central_get_form_option', $value, $option );
	}

/**
 * Used to check if a Video Central slug conflicts with an existing known slug.
 *
 * @since 1.0.0
 *
 * @param string $slug
 * @param string $default
 *
 * @uses video_central_get_form_option() To get a sanitized slug string
 */
function video_central_form_slug_conflict_check( $slug, $default ) {

	// Only set the slugs once ver page load
	static $the_core_slugs = array();

	// Get the form value
	$this_slug = video_central_get_form_option( $slug, $default, true );

	if ( empty( $the_core_slugs ) ) {

		// Slugs to check
		$core_slugs = apply_filters( 'video_central_slug_conflict_check', array(

			/** WordPress Core ****************************************************/

			// Core Post Types
			'post_base'       => array( 'name' => __( 'Posts',         'video_central' ), 'default' => 'post',          'context' => 'WordPress' ),
			'page_base'       => array( 'name' => __( 'Pages',         'video_central' ), 'default' => 'page',          'context' => 'WordPress' ),
			'nav_menu_base'   => array( 'name' => __( 'Menus',         'video_central' ), 'default' => 'nav_menu_item', 'context' => 'WordPress' ),

			// Post Tags
			'tag_base'        => array( 'name' => __( 'Tag base',      'video_central' ), 'default' => 'tag',           'context' => 'WordPress' ),

			// Post Categories
			'category_base'   => array( 'name' => __( 'Category base', 'video_central' ), 'default' => 'category',      'context' => 'WordPress' ),

			/** Video Central Core ******************************************************/

			// Video archive slug
			'_video_central_root_slug'          => array( 'name' => __( 'Videos base', 'video_central' ), 'default' => 'videos', 'context' => 'Video Central' ),

			// Video slug
			'_video_central_video_slug'         => array( 'name' => __( 'Video slug',  'video_central' ), 'default' => 'video',  'context' => 'Video Central' ),

			// User profile slug
			'_video_central_user_slug'          => array( 'name' => __( 'User base',   'video_central' ), 'default' => 'users',  'context' => 'Video Central' ),

			// View slug
			'_video_central_view_slug'          => array( 'name' => __( 'View base',   'video_central' ), 'default' => 'view',   'context' => 'Video Central' ),

			// Video tag slug
			'_video_central_video_tag_tax_slug'     => array( 'name' => __( 'Video tag slug', 'video_central' ), 'default' => 'video-tag', 'context' => 'Video Central' ),

            // Video category slug
			'_video_central_video_category_tax_slug' => array( 'name' => __( 'Video category slug', 'video_central' ), 'default' => 'video-category', 'context' => 'Video Central' ),

		) );

		// Set the static
		$the_core_slugs = apply_filters( 'video_central_slug_conflict', $core_slugs );
	}

	// Loop through slugs to check
	foreach ( $the_core_slugs as $key => $value ) {

		// Get the slug
		$slug_check = video_central_get_form_option( $key, $value['default'], true );

		// Compare
		if ( ( $slug !== $key ) && ( $slug_check === $this_slug ) ) : ?>

			<span class="attention"><?php printf( esc_html__( 'Possible %1$s conflict: %2$s', 'video_central' ), $value['context'], '<strong>' . $value['name'] . '</strong>' ); ?></span>

		<?php endif;
	}
}
