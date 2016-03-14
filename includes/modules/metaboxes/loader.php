<?php
/**
 * Load plugin's files with check for installing it as a standalone plugin or
 * a module of a theme / plugin. If standalone plugin is already installed, it
 * will take higher priority.
 * @package Meta Box
 */

/**
 * Plugin loader class.
 * @package Meta Box
 */
class Video_Central_Metaboxes_Loader
{
    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->constants();
        spl_autoload_register( array( $this, 'autoload' ) );
        $this->init();
    }

    /**
     * Define plugin constants.
     */
    public function constants()
    {
        video_central()->version;

        // Script version, used to add version for scripts and styles
        define( 'Video_Central_Metaboxes_VER', video_central()->version );

        // Plugin URLs, for fast enqueuing scripts and styles
        define( 'Video_Central_Metaboxes_URL', video_central()->plugin_url );
        define( 'Video_Central_Metaboxes_JS_URL', trailingslashit( Video_Central_Metaboxes_URL . 'assets/admin/js/metaboxes' ) );
        define( 'Video_Central_Metaboxes_CSS_URL', trailingslashit( Video_Central_Metaboxes_URL . 'assets/admin/css/metaboxes' ) );

        // Plugin paths, for including files
        define( 'Video_Central_Metaboxes_DIR', video_central()->plugin_dir );
        define( 'Video_Central_Metaboxes_INC_DIR', trailingslashit( video_central()->plugin_dir . 'includes/modules/metaboxes' ) );
        define( 'Video_Central_Metaboxes_FIELDS_DIR', trailingslashit( Video_Central_Metaboxes_INC_DIR . 'fields' ) );
    }

    /**
     * Autoload fields' classes.
     * @param string $class Class name
     */
    public function autoload( $class )
    {
        // Only load plugin's classes
        if ( 'Video_Central_Metabox' != $class && 0 !== strpos( $class, 'Video_Central_Metaboxes_' ) )
        {
            return;
        }

        // Get file name
        $file = 'meta-box';
        if ( 'Video_Central_Metabox' != $class )
        {
            // Remove prefix 'Video_Central_Metaboxes_'
            $file = substr( $class, 24 );

            // Optional '_Field'
            $file = preg_replace( '/_Field$/', '', $file );
        }

        $file = strtolower( str_replace( '_', '-', $file ) ) . '.php';

        $dirs = array( Video_Central_Metaboxes_INC_DIR, Video_Central_Metaboxes_FIELDS_DIR, trailingslashit( Video_Central_Metaboxes_INC_DIR . 'walkers' ) );

        foreach ( $dirs as $dir )
        {
            if ( file_exists( $dir . $file ) )
            {
                require $dir . $file;
                return;
            }
        }
    }

    /**
     * Initialize plugin.
     */
    public function init()
    {
        // Plugin core
        new Video_Central_Metaboxes_Core;

        if ( is_admin() )
        {
            // Validation module
            new Video_Central_Metaboxes_Validation;
        }

        // Public functions
        require Video_Central_Metaboxes_INC_DIR . 'functions.php';
    }
}
