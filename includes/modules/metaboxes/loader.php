<?php
/**
 * Load plugin's files with check for installing it as a standalone plugin or
 * a module of a theme / plugin. If standalone plugin is already installed, it
 * will take higher priority.
 *
 * @package Meta Box
 */

/**
 * Plugin loader class.
 *
 * @package Meta Box
 */
class Video_Central_Metaboxes_Loader {

	/**
	 * Define plugin constants.
	 */
	protected function constants() {
		// Script version, used to add version for scripts and styles
		define( 'Video_Central_Metaboxes_VER', '4.9.8' );

		// Plugin URLs, for fast enqueuing scripts and styles
		define( 'Video_Central_Metaboxes_URL', video_central()->core_assets_url . 'admin/' );
		define( 'Video_Central_Metaboxes_JS_URL', trailingslashit( Video_Central_Metaboxes_URL . 'js/metaboxes' ) );
		define( 'Video_Central_Metaboxes_CSS_URL', trailingslashit( Video_Central_Metaboxes_URL . 'css/metaboxes' ) );

        $path       = video_central()->includes_dir.'modules/';

		// Plugin paths, for including files
		define( 'Video_Central_Metaboxes_DIR', $path );
		define( 'Video_Central_Metaboxes_INC_DIR', trailingslashit( Video_Central_Metaboxes_DIR . 'metaboxes' ) );
	}

	/**
	 * Get plugin base path and URL.
	 * The method is static and can be used in extensions.
	 *
	 * @link http://www.deluxeblogtips.com/2013/07/get-url-of-php-file-in-wordpress.html
	 * @param string $path Base folder path
	 * @return array Path and URL.
	 */
	public static function get_path( $path = '' ) {

		// Plugin base path
		$path       = video_central()->includes_dir.'modules/';
		$themes_dir = wp_normalize_path( untrailingslashit( dirname( realpath( get_stylesheet_directory() ) ) ) );

		// Default URL
		$url = plugins_url( '', $path . '/' . basename( $path ) . '.php' );

		// Included into themes
		if (
			0 !== strpos( $path, wp_normalize_path( WP_PLUGIN_DIR ) )
			&& 0 !== strpos( $path, wp_normalize_path( WPMU_PLUGIN_DIR ) )
			&& 0 === strpos( $path, $themes_dir )
		) {
			$themes_url = untrailingslashit( dirname( get_stylesheet_directory_uri() ) );
			$url        = str_replace( $themes_dir, $themes_url, $path );
		}

		$path = trailingslashit( $path );
		$url  = trailingslashit( $url );

		return array( $path, $url );
	}

	/**
	 * Bootstrap the plugin.
	 */
	public function init() {
		$this->constants();

		// Register autoload for classes
		require_once Video_Central_Metaboxes_INC_DIR . 'autoloader.php';
		$autoloader = new Video_Central_Metaboxes_Autoloader;
		$autoloader->add( Video_Central_Metaboxes_INC_DIR, 'Video_Central_Metaboxes_' );
		$autoloader->add( Video_Central_Metaboxes_INC_DIR . 'fields', 'Video_Central_Metaboxes_', '_Field' );
		$autoloader->add( Video_Central_Metaboxes_INC_DIR . 'walkers', 'Video_Central_Metaboxes_Walker_' );
		$autoloader->register();

		// Plugin core
		new Video_Central_Metaboxes_Core;

		if ( is_admin() ) {
			// Validation module
			new Video_Central_Metaboxes_Validation;

			$sanitize = new Video_Central_Metaboxes_Sanitizer;
			$sanitize->init();
		}

		// Public functions
		require_once Video_Central_Metaboxes_INC_DIR . 'functions.php';
	}
}
