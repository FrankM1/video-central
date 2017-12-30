<?php
/*
Plugin Name: Video Central
Plugin URI: http://plugins.radiumthemes.com/video-central
Description: The Ultimate Video Manager for WordPress
Author: Franklin M Gitonga
Version: 1.3.0
Author URI: http://radiumthemes.com/
License: GPL v2+
*/

//To add later
/**
 * 1. Channels
 * 2. Upload from frontend
 * 3. User accounts
 * 4. Subscriptions
 * 5. Watch Later
 * 6. Playlists - in progress
 * 7. Choice of multiple players
 * 8. E-commerce
 * 9. Membership levels
 * 10. JSON API
 * 11. Cron for auto importing videos.
 */

/* Load all of the necessary class files for the plugin (files from the Radium liblary) */
spl_autoload_register( 'Video_Central::autoload' );

/**
 * Init class for Video Central.
 *
 * Loads all of the necessary components for the radium Video plugin.
 *
 * @since 1.0.0
 *
 * @author  Franklin Gitonga
 */
class Video_Central
{
    /**
     * Current version of the plugin.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $version = '1.3.0';

    /**
     * Current db version of the plugin.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $db_version = '1.1';

    /** Magic *****************************************************************/

    /**
     * Video Central uses many variables, several of which can be filtered to
     * customize the way it operates. Most of these variables are stored in a
     * private array that gets updated with the help of PHP magic methods.
     *
     * This is a precautionary measure, to avoid potential errors produced by
     * unanticipated direct manipulation of Video Central's run-time data.
     *
     * @see video_central::setup_globals()
     *
     * @var array
     */
    private $data;

    /** Not Magic *************************************************************/

    /**
     * @var mixed False when not logged in; WP_User object when logged in
     */
    public $current_user = false;

    /**
     * @var obj Add-ons append to this (Akismet, BuddyPress, etc...)
     */
    public $extend;

    /**
     * @var array Video views
     */
    public $views = array();

    /**
     * @var array Overloads get_option()
     */
    public $options = array();

    /**
     * Getter method for retrieving the object instance.
     *
     * @since 1.0.0
     */
    public static function instance()
    {

        // Store the instance locally to avoid private static replication
        static $instance = null;

        // Only run these methods if they haven't been ran previously
        if (null === $instance) {
            $instance = new self();
            $instance->setup_globals();
            $instance->includes();
            $instance->setup_actions();
        }

        // Always return the instance
        return $instance;
    }

    /** Magic Methods *********************************************************/

    /**
     * A dummy constructor to prevent Video Central from being loaded more than once.
     *
     * @since 1.0.0
     * @see video_central::instance()
     * @see video_central();
     */
    private function __construct()
    { /* Do nothing here */
    }
    /**
     * A dummy magic method to prevent Video Central from being cloned.
     *
     * @since 1.0.0
     */
    public function __clone()
    {
        _doing_it_wrong(__FUNCTION__, __( 'Cheatin&#8217; huh?', 'video_central' ), '1.0' );
    }

    /**
     * A dummy magic method to prevent Video Central from being unserialized.
     *
     * @since 1.0.0
     */
    public function __wakeup()
    {
        _doing_it_wrong(__FUNCTION__, __( 'Cheatin&#8217; huh?', 'video_central' ), '1.0' );
    }

    /**
     * Magic method for checking the existence of a certain custom field.
     *
     * @since 1.0.0
     */
    public function __isset( $key)
    {
        return isset( $this->data[$key]);
    }

    /**
     * Magic method for getting Video Central variables.
     *
     * @since 1.0.0
     */
    public function __get( $key)
    {
        return isset( $this->data[$key]) ? $this->data[$key] : null;
    }

    /**
     * Magic method for setting Video Central variables.
     *
     * @since 1.0.0
     */
    public function __set( $key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Magic method for unsetting Video Central variables.
     *
     * @since 1.0.0
     */
    public function __unset( $key)
    {
        if ( isset( $this->data[$key] ) ) {
            unset( $this->data[$key]);
        }
    }

    /**
     * Magic method to prevent notices and errors from invalid method calls.
     *
     * @since 1.0.0
     */
    public function __call( $name = '', $args = array() )
    {
        unset( $name, $args );

        return;
    }

    /** Private Methods *******************************************************/

    /**
     * Set some smart defaults to class variables. Allow some of them to be
     * filtered to allow for early overriding.
     *
     * @since 1.0.0
     *
     * @uses plugin_dir_path() To generate Video Central plugin path
     * @uses plugin_dir_url() To generate Video Central plugin url
     * @uses apply_filters() Calls various filters
     */
    private function setup_globals()
    {

        /* Paths *************************************************************/

        // Setup some base path and URL information
        $this->file = __FILE__;
        $this->basename = apply_filters( 'video_central_plugin_basenname', plugin_basename( $this->file ) );
        $this->plugin_dir = apply_filters( 'video_central_plugin_dir_path',  plugin_dir_path( $this->file ) );
        $this->plugin_url = apply_filters( 'video_central_plugin_dir_url',   plugin_dir_url( $this->file ) );

        // core assets
        $this->core_assets_dir = apply_filters( 'video_central_core_assets_dir', trailingslashit( $this->plugin_dir . 'assets' ) );
        $this->core_assets_url = apply_filters( 'video_central_core_assets_url', trailingslashit( $this->plugin_url.'assets' ) );

        // Includes
        $this->includes_dir = apply_filters( 'video_central_includes_dir', trailingslashit( $this->plugin_dir . 'includes' ) );
        $this->includes_url = apply_filters( 'video_central_includes_url', trailingslashit( $this->plugin_url.'includes' ) );

        // Languages
        $this->lang_dir = apply_filters( 'video_central_lang_dir',     trailingslashit( $this->plugin_dir . 'languages' ) );

        // Templates
        $this->themes_dir = apply_filters( 'video_central_themes_dir',   trailingslashit( $this->plugin_dir . 'templates' ) );
        $this->themes_url = apply_filters( 'video_central_themes_url',   trailingslashit( $this->plugin_url.'templates' ) );

        /* Identifiers *******************************************************/

        // Post type identifiers
        $this->video_post_type = apply_filters( 'video_central_videos_post_type',  'video' );
        $this->video_tag_tax_id = apply_filters( 'video_central_videos_tag_tax_id', 'video_tag' );
        $this->video_cat_tax_id = apply_filters( 'video_central_videos_cat_tax_id', 'video_category' );
        $this->playlist_post_type = apply_filters( 'video_central_playlists_post_type',  'playlist' );

        // Status identifiers
        $this->spam_status_id = apply_filters( 'video_central_spam_post_status',    'spam' );
        $this->closed_status_id = apply_filters( 'video_central_closed_post_status',  'closed' );
        $this->public_status_id = apply_filters( 'video_central_public_post_status',  'publish' );
        $this->pending_status_id = apply_filters( 'video_central_pending_post_status', 'pending' );
        $this->private_status_id = apply_filters( 'video_central_private_post_status', 'private' );
        $this->hidden_status_id = apply_filters( 'video_central_hidden_post_status',  'hidden' );
        $this->trash_status_id = apply_filters( 'video_central_trash_post_status',   'trash' );

        $this->search_id = apply_filters( 'video_central_search_id',         'video_search' );
        $this->user_id = apply_filters( 'video_central_user_id',           'video_user' );
        $this->view_id = apply_filters( 'video_central_view_id',           'video_view' );

        /* Queries ***********************************************************/
        $this->current_view_id = 0; // Current view id
        $this->current_video_id = 0; // Current video id

        $this->video_query      = new WP_Query(); // Main video query
        $this->playlist_query   = new WP_Query(); // Main playlist query
        $this->search_query     = new WP_Query(); // Main search query

        /* Theme Compat ******************************************************/

        $this->theme_compat = new stdClass(); // Base theme compatibility class
        $this->filters = new stdClass(); // Used when adding/removing filters
        $this->admin = new StdClass(); // Used by admin

        /* Misc **************************************************************/

        $this->providers = array();
        $this->domain = 'video_central'; // Unique identifier for retrieving translated strings
        $this->extend = new stdClass(); // Plugins add data here
        $this->errors = new WP_Error(); // Feedback
    }

    /**
     * Include required files.
     *
     * @since 1.0.0
     *
     * @uses is_admin() If in WordPress admin, load additional file
     */
    private function includes()
    {

        /** Core **************************************************************/
        require $this->includes_dir . 'core/sub-actions.php';
        require $this->includes_dir . 'core/functions.php';
        require $this->includes_dir . 'core/options.php';
        require $this->includes_dir . 'core/update.php';
        require $this->includes_dir . 'core/capabilities.php';
        require $this->includes_dir . 'core/template-functions.php';
        require $this->includes_dir . 'core/template-loader.php';
        require $this->includes_dir . 'core/theme-compat.php';

        /** Components ********************************************************/

        // Common
        require $this->includes_dir . 'common/functions.php';
        require $this->includes_dir . 'common/template.php';

        //images
        require $this->includes_dir . 'modules/import/thumbnail.php';
        require $this->includes_dir . 'modules/resize.php';

        // Videos
        require $this->includes_dir . 'videos/class.posttype.php';
        require $this->includes_dir . 'videos/capabilities.php';
        require $this->includes_dir . 'videos/functions.php';
        require $this->includes_dir . 'videos/template.php';
        require $this->includes_dir . 'videos/metaboxes.php';

        // Player
        require $this->includes_dir . 'player/functions.php';
        require $this->includes_dir . 'player/template.php';

        // Search
        require $this->includes_dir . 'search/functions.php';
        require $this->includes_dir . 'search/template.php';

        // Users
        require $this->includes_dir . 'users/capabilities.php';
        require $this->includes_dir . 'users/functions.php';
        require $this->includes_dir . 'users/template.php';
        // require( $this->includes_dir . 'users/options.php'        );

        // playlist
        require $this->includes_dir . 'playlist/class.posttype.php';
        require $this->includes_dir . 'playlist/class.ajax.php';
        require $this->includes_dir . 'playlist/class.editplaylist.php';
        require $this->includes_dir . 'playlist/functions.php';
        require $this->includes_dir . 'playlist/template.php';
        require $this->includes_dir . 'playlist/metaboxes.php';

        // Likes
        require $this->includes_dir . 'modules/likes/functions.php';
        require $this->includes_dir . 'modules/likes/ajax.php';

        // Widgets
        require $this->includes_dir . 'widgets/widget-categories.php';
        require $this->includes_dir . 'widgets/widget-featured.php';
        require $this->includes_dir . 'widgets/widget-popular.php';
        require $this->includes_dir . 'widgets/widget-recent.php';
        require $this->includes_dir . 'widgets/widget-search.php';
        require $this->includes_dir . 'widgets/widget-tags.php';

        /** Hooks *************************************************************/
        require $this->includes_dir . 'core/actions.php';
        require $this->includes_dir . 'core/filters.php';

        // Woosidebars integration
        require $this->includes_dir . 'modules/third-party/class.woosidebars.integration.php';

        // Visual Composer
        require $this->includes_dir . 'modules/third-party/visual-composer/functions.php';
        require $this->includes_dir . 'modules/third-party/visual-composer/integrate.php';

        /* Admin *************************************************************/

        if ( is_admin() ) :

            // Quick admin check and load if needed
            require $this->includes_dir . 'admin/admin.php';
            require $this->includes_dir . 'admin/actions.php';
            require $this->includes_dir . 'admin/fields.php';

            // Check that 'class-wp-list-table.php' is available
            if ( ! class_exists( 'WP_List_Table' ) ) :
                require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
            endif;

            // Metaboxes
            include_once $this->includes_dir . 'modules/metaboxes/loader.php';

            // Modules (Modules can run as 'independent' plugins to enhance or add features)
            include_once $this->includes_dir . 'modules/import/options.php';

            include_once $this->includes_dir . 'modules/import/video/class.settings.php';
            include_once $this->includes_dir . 'modules/import/video/class.thumbnails-providers.php';
            include_once $this->includes_dir . 'modules/import/video/class.wizard.php';
            include_once $this->includes_dir . 'modules/import/video/class.importer.php';

            // Import youtube videos
            include_once $this->includes_dir . 'modules/import/youtube/functions.php';
            include_once $this->includes_dir . 'modules/import/youtube/class.api-query.php';
            include_once $this->includes_dir . 'modules/import/youtube/class.importer.php';
            include_once $this->includes_dir . 'modules/import/youtube/class.auto.importer.php';
            include_once $this->includes_dir . 'modules/import/youtube/class.thumbnails.php';
            include_once $this->includes_dir . 'modules/import/youtube/class.wizard.php';
            include_once $this->includes_dir . 'modules/import/youtube/class.list-table.php';

            // Import youtube videos
            //include_once $this->includes_dir . 'modules/import/vimeo/functions.php';
            //include_once $this->includes_dir . 'modules/import/vimeo/class.importer.php';
            //include_once $this->includes_dir . 'modules/import/vimeo/class.importer-data.php';
            include_once $this->includes_dir . 'modules/import/vimeo/class.thumbnails.php';
            //include_once $this->includes_dir . 'modules/import/vimeo/class.wizard.php';
            //include_once $this->includes_dir . 'modules/import/vimeo/class.list-table.php';

        else :

            // frontend includes

        endif;
    }

    /**
     * Constructor. Hooks all interactions into correct areas to start
     * the class.
     *
     * @since 1.0.0
     */
    public function setup_actions()
    {

        // Add actions to plugin activation and deactivation hooks
        add_action( 'activate_' . $this->basename, 'video_central_activation' );
        add_action( 'deactivate_' . $this->basename, 'video_central_deactivation' );

         // If Video Central is being deactivated, do not add any actions
        if ( video_central_is_deactivation( $this->basename ) ) {
            return;
        }

        // Array of Video Central core actions
        $actions = array(
            'init_classes',             // Load plugin classes
            'setup_theme',              // Setup the default theme compatibility
            'register_views',           // Register the views (popular, latest)
            'register_theme_packages',  // Register bundled theme packages (templates/default)
            'register_shortcodes',      // Register shortcodes
            'load_textdomain',          // Load textdomain (video_central)
            'enqueue_scripts',
            'add_rewrite_tags',         // Add rewrite tags (search)
            'add_rewrite_rules',        // Generate rewrite rules (paged|search)
            'add_permastructs',         // Add permalink structures (|search)
        );

        // Add the actions
        foreach ( $actions as $class_action) {
            add_action( 'video_central_' . $class_action, array( $this, $class_action ), 5 );
        }

        // All Video Central actions are setup (includes video-central-core-hooks.php)
        do_action_ref_array( 'video_central_after_setup_actions', array( &$this ) );

        //Add Page Templates
        add_action( 'after_setup_theme', array( 'Radium_Video_Template', 'get_instance' ) ); // Load late for filters to work
    }

    /**
     * Registers a plugin activation hook to make sure the current WordPress
     * version is suitable (>= 3.3.1) for use.
     *
     * @since 1.0.0
     *
     * @global int $wp_version The current version of this particular WP instance
     */
    public function activation()
    {
        global $wp_version;

        if (version_compare( $wp_version, '3.0.0', '<' ) ) {
            deactivate_plugins( plugin_basename(__FILE__ ) );
            wp_die(printf( __( 'Sorry, but your version of WordPress, <strong>%s</strong>, does not meet the Video Central\'s required version of <strong>3.3.1</strong> to run properly. The plugin has been deactivated. <a href="%s">Click here to return to the Dashboard</a>', 'video_central' ), $wp_version, admin_url() ));
        }
    }

    /**
     * Loads the plugin classes.
     *
     * @since 1.0.0
     */
    public function init_classes()
    {

        /* Load the plugin */
        new Video_Central_Video_Posttype();
        new Radium_MediaElements_Shortcode();
        new Video_Central_Playlist_Posttype();
        new Video_Central_Playlist_Ajax();
        new Video_Central_Map_Shortcode();

        // Only run certain processes in the admin.
        if ( is_admin() ) :

            $this->metaboxes = new Video_Central_Metaboxes_Loader();
            $this->metaboxes->init();

           // $this->playlist_admin = new Video_Central_Playlist_Admin();
           new Video_Central_Playlist_EditPlaylist();

            $this->import_thumbnails = new Video_Central_Import_Thumbnails();
            //$this->auto_import_youtube  = new Video_Central_Youtube_Auto_Importer;

            new Video_Central_Likes_Ajax();

        endif;
    }

    /**
     * loads the frontend core assets.
     *
     * @since 1.0.0
     */
    public function enqueue_scripts()
    {

        //video js (http://videojs.com)
        wp_enqueue_script( 'video-central-player', $this->core_assets_url . 'frontend/js/video-js.js', array( 'jquery' ), $this->version, true);

        //custom css files
        wp_enqueue_style( 'video-central-player-style', $this->core_assets_url.'frontend/css/video-js.css', array(), $this->version);
    }

    /**
     * Registers the widget with WordPress.
     *
     * @since 1.0.0
     */
    public function widget()
    {
        register_widget( 'Radium_Video_Widget' );
    }

    /**
     * Register bundled theme packages.
     *
     * Note that since we currently have complete control over video-central-themes and
     * the video-central-theme-compat folders, it's fine to hardcode these here. If at a
     * later date we need to automate this, and API will need to be built.
     *
     * @since 1.0.0
     */
    public function register_theme_packages()
    {

        // Register the default theme compatibility package
        video_central_register_theme_package(array(
            'id' => 'default',
            'name' => __( 'Videos Default', 'video_central' ),
            'version' => $this->version,
            'dir' => trailingslashit( $this->themes_dir . 'default' ),
            'url' => trailingslashit( $this->themes_url.'default' ),
         ) );

        // Register the basic theme stack. This is really dope.
        video_central_register_template_stack( 'get_stylesheet_directory', 10);
        video_central_register_template_stack( 'get_template_directory',   12);
        video_central_register_template_stack( 'video_central_get_theme_compat_dir', 14);
    }

    /**
     * Setup the default Video Central theme compatibility location.
     *
     * @since 1.0.0
     */
    public function setup_theme()
    {

        // Bail if something already has this under control
        if (!empty( $this->theme_compat->theme ) ) {
            return;
        }

        // Setup the theme package to use for compatibility
        video_central_setup_theme_compat(video_central_get_theme_package_id() );
    }

    /**
     * Register the Video Central views.
     *
     * @since 1.0.0
     *
     * @uses video_central_register_view() To register the views
     */
    public static function register_views()
    {

        // Popular videos
        video_central_register_view(
            'popular', __( 'Most popular videos', 'video_central' ),
            apply_filters( 'video_central_register_view_popular', array(
                'meta_key' => '_video_central_video_views_count',
                'max_num_pages' => 1,
                'orderby' => 'meta_value_num',
                'show_stickies' => false,
             ) )
        );

        // Latest videos
        video_central_register_view(
            'latest', __( 'Latest videos', 'video_central' ),
            apply_filters( 'video_central_register_view_latest', array(
                'max_num_pages' => 1,
                'orderby' => 'date',
                'show_stickies' => false,
             ) )
        );

        // Latest videos
        video_central_register_view(
            'featured', __( 'Featured videos', 'video_central' ),
            apply_filters( 'video_central_register_view_featured', array(
                'meta_key' => '_video_central_featured_video',
                'max_num_pages' => 1,
                'orderby' => 'meta_value_num date',
                'show_stickies' => false,
             ) )
        );
    }

    /**
     * Register the Video Central shortcodes.
     *
     * @since 1.0.0
     *
     * @uses Radium_Video_Shortcodes
     */
    public function register_shortcodes()
    {
        $this->shortcodes = new Radium_Video_Shortcodes();
    }

    /**
     * Load the translation file for current language. Checks the languages
     * folder inside the Video Central plugin first, and then the default WordPress
     * languages folder.
     *
     * Note that custom translation files inside the Video Central plugin folder
     * will be removed on Video Central updates. If you're creating custom
     * translation files, please use the global language folder.
     *
     * @since 1.0.0
     *
     * @uses apply_filters() Calls 'plugin_locale' with {@link get_locale()} value
     * @uses load_textdomain() To load the textdomain
     */
    public function load_textdomain()
    {

        // Traditional WordPress plugin locale filter
        $locale = apply_filters( 'plugin_locale', get_locale(), $this->domain);
        $mofile = $locale.'.mo';

        // Setup paths to current locale file
        $mofile_local = $this->lang_dir.$mofile;
        $mofile_global = WP_LANG_DIR.'/plugins/video-central/' . $mofile;

        // Look in global /wp-content/languages/video-central folder
        load_textdomain( $this->domain, $mofile_global);

        // Look in local /wp-content/plugins/video-central/ folder
        load_textdomain( $this->domain, $mofile_local);

        // Look in global /wp-content/languages/plugins/
        load_plugin_textdomain( $this->domain);
    }

    /** Custom Rewrite Rules **************************************************/

    /**
     * Add the Video Central-specific rewrite tags.
     *
     * @since 1.0.0
     *
     * @uses add_rewrite_tag() To add the rewrite tags
     */
    public static function add_rewrite_tags()
    {
        add_rewrite_tag( '%'.video_central_get_view_rewrite_id().'%', '([^/]+)' ); // View Page tag
        add_rewrite_tag( '%'.video_central_get_search_rewrite_id().'%', '([^/]+)' ); // Search Results tag
    }

    /**
     * Add Video Central-specific rewrite rules for uri's that are not
     * setup for us by way of custom post types or taxonomies. This includes:
     * - Front-end editing
     * - Video views
     * - User profiles.
     *
     * @since 1.0.0
     */
    public static function add_rewrite_rules()
    {

        /* Setup *************************************************************/

        // Add rules to top or bottom?
        $priority = 'top';

        // Archive Slugs
        $search_slug = video_central_get_search_slug();

        // Tertiary Slugs
        $paged_slug = video_central_get_paged_slug();
        $view_slug = video_central_get_view_slug();

        // Unique rewrite ID's
        $paged_id = video_central_get_paged_rewrite_id();
        $search_id = video_central_get_search_rewrite_id();
        $view_id = video_central_get_view_rewrite_id();

        // Rewrite rule matches used repeatedly below
        $root_rule = '/([^/]+)/?$';
        $paged_rule = '/([^/]+)/' . $paged_slug.'/?([0-9]{1,})/?$';

        // Search rules (without slug check)
        $search_root_rule = '/?$';
        $search_paged_rule = '/' . $paged_slug.'/?([0-9]{1,})/?$';

        // Video-View Pagination|Feed|View
        add_rewrite_rule( $view_slug.$paged_rule, 'index.php?' . $view_id.'=$matches[1]&' . $paged_id.'=$matches[2]', $priority);
        add_rewrite_rule( $view_slug.$root_rule,  'index.php?' . $view_id.'=$matches[1]',                               $priority);

        // Search All
        add_rewrite_rule( $search_slug.$search_paged_rule, 'index.php?' . $paged_id.'=$matches[1]', $priority);
        add_rewrite_rule( $search_slug.$search_root_rule,  'index.php?' . $search_id,                $priority);
    }

    /**
     * Add permalink structures for new archive-style destinations.
     *
     * - Users
     * - Video Views
     * - Search
     *
     * @since 1.0.0
     */
    public static function add_permastructs()
    {

        // Get unique ID's
        $user_id = video_central_get_user_rewrite_id();
        $view_id = video_central_get_view_rewrite_id();
        $search_id = video_central_get_search_rewrite_id();

        // Get root slugs
        $user_slug = video_central_get_user_slug();
        $view_slug = video_central_get_view_slug();
        $search_slug = video_central_get_search_slug();

        // User Permastruct
        add_permastruct( $user_id, $user_slug.'/%' . $user_id.'%', array(
            'with_front' => false,
            'ep_mask' => EP_NONE,
            'paged' => false,
            'feed' => false,
            'forcomments' => false,
            'walk_dirs' => true,
            'endpoints' => false,
         ) );

        // Video View Permastruct
        add_permastruct( $view_id, $view_slug.'/%' . $view_id.'%', array(
            'with_front' => false,
            'ep_mask' => EP_NONE,
            'paged' => false,
            'feed' => false,
            'forcomments' => false,
            'walk_dirs' => true,
            'endpoints' => false,
         ) );

        // Search Permastruct
        add_permastruct( $user_id, $search_slug.'/%' . $search_id.'%', array(
            'with_front' => false,
            'ep_mask' => EP_NONE,
            'paged' => true,
            'feed' => false,
            'forcomments' => false,
            'walk_dirs' => true,
            'endpoints' => false,
         ) );
    }

    /**
     * PSR-0 compliant autoloader to load classes as needed.
     *
     * @since 1.0.0
     *
     * @param string $classname The name of the class
     */
    public static function autoload( $classname)
    {
        if ( 'Radium' !== mb_substr( $classname, 0, 6 ) ) {
            return;
        }

        $filename = dirname(__FILE__).DIRECTORY_SEPARATOR.str_replace( '_', DIRECTORY_SEPARATOR, $classname).'.php';

        if (file_exists( $filename ) ) {
            require $filename;
        }
    }

    /**
     * Getter method for retrieving the url.
     *
     * @since 1.0.0
     */
    public static function get_url()
    {
        return plugins_url( '', __FILE__);
    }

    /**
     * Getter method for retrieving the url.
     *
     * @since 1.0.0
     */
    public static function get_dir()
    {
        return plugin_dir_path(__FILE__);
    }

    /**
     * Getter method for retrieving the main plugin filepath.
     *
     * @since 1.0.0
     */
    public static function get_file()
    {
        return self::$file;
    }
}

/**
 * The main function responsible for returning the one true Video Central Instance
 * to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $video_central = video_central(); ?>
 *
 * @since 1.0.0
 *
 * @return The one true Video Central Instance
 */
function video_central() {

    $instance = Video_Central::instance();

    return $instance;
}

/*
 * Hook Video_Central early onto the 'plugins_loaded' action.
 *
 * This gives all other plugins the chance to load before Video Central, to get their
 * actions, filters, and overrides setup without Video_Central being in the way.
 */
if (defined( 'VIDEO_CENTRAL_LATE_LOAD' ) ) {
    add_action( 'plugins_loaded', 'video_central', (int) VIDEO_CENTRAL_LATE_LOAD);
} else {
    video_central();
}
// End class
