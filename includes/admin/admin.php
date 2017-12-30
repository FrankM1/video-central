<?php

/**
 * Main Video Central Admin Class.
 */

if (!class_exists('Video_Central_Admin')) :
/**
 * Loads Video Central plugin admin area.
 *
 * @since 1.0.0
 */
class Video_Central_Admin
{
    /** Directory *************************************************************/

    /**
     * @var string Path to the Video Central admin directory
     */
    public $admin_dir = '';

    /** URLs ******************************************************************/

    /**
     * @var string URL to the Video Central admin directory
     */
    public $admin_url = '';

    /**
     * @var string URL to the Video Central images directory
     */
    public $images_url = '';

    /**
     * @var string URL to the Video Central admin styles directory
     */
    public $styles_url = '';

    /**
     * @var string URL to the Video Central admin css directory
     */
    public $css_url = '';

    /**
     * @var string URL to the Video Central admin js directory
     */
    public $js_url = '';

    /** Capability ************************************************************/

    /**
     * @var bool Minimum capability to access Tools and Settings
     */
    public $minimum_capability = 'manage_options'; //'keep_gate'; //patch till user management is fully functional

    /** Separator *************************************************************/

    /**
     * @var bool Whether or not to add an extra top level menu separator
     */
    public $show_separator = false;

    /** Admin Settings ************************************************************/

    /**
     * @var string Settings page slug
     */
    private $general_settings_key = '';

    /**
     * @var array Settings tabs
     */
    private $plugin_settings_tabs = array();

    /** Functions *************************************************************/

    /**
     * The main Video Central admin loader.
     *
     * @since 1.0.0
     *
     * @uses Video_Central_Admin::setup_globals() Setup the globals needed
     * @uses Video_Central_Admin::includes() Include the required files
     * @uses Video_Central_Admin::setup_actions() Setup the hooks and actions
     */
    public function __construct()
    {
        $this->setup_globals();
        $this->includes();
        $this->setup_actions();
    }

    /**
     * Admin globals.
     *
     * @since 1.0.0
     */
    private function setup_globals()
    {
        $video_central = video_central();

        $this->admin_dir = trailingslashit($video_central->includes_dir.'admin'); // Admin path
        $this->admin_url = trailingslashit($video_central->core_assets_url.'admin'); // Admin url
        $this->images_url = trailingslashit($this->admin_url.'images'); // Admin images URL
        $this->styles_url = trailingslashit($this->admin_url.'styles'); // Admin styles URL
        $this->css_url = trailingslashit($this->admin_url.'css'); // Admin css URL
        $this->js_url = trailingslashit($this->admin_url.'js'); // Admin js URL
    }

    /**
     * Include required files.
     *
     * @since 1.0.0
     */
    private function includes()
    {
        require $this->admin_dir.'settings.php';
        require $this->admin_dir.'functions.php';
        require $this->admin_dir.'users.php';
    }

    /**
     * Setup the admin hooks, actions and filters.
     *
     * @since 1.0.0
     *
     * @uses add_action() To add various actions
     * @uses add_filter() To add various filters
     */
    private function setup_actions()
    {

        // Bail to prevent interfering with the deactivation process
        if (video_central_is_deactivation()) {
            return;
        }

        /* General Actions ***************************************************/

        add_action('video_central_admin_menu',              array($this, 'AdminMenus')); // Add menu item to settings menu
        add_action('video_central_admin_head',              array($this, 'admin_head')); // Add some general styling to the admin area
        add_action('video_central_admin_notices',           array($this, 'activation_notice')); // Add notice if not using a Video Central theme
        add_action('video_central_register_admin_style',    array($this, 'register_admin_style')); // Add green admin style
        add_action('video_central_register_admin_settings', array($this, 'RegisterAdminSettings')); // Add settings
        add_action('video_central_activation',              array($this, 'NewInstall')); // Add menu item to settings menu

        add_action('admin_enqueue_scripts',                    array($this, 'enqueue_styles')); // Add enqueued CSS
        add_action('admin_enqueue_scripts',                    array($this, 'enqueue_scripts')); // Add enqueued JS

        add_action('wp_dashboard_setup',                       array($this, 'dashboard_widget_right_now')); // Videos 'Right now' Dashboard widget
        add_action('admin_bar_menu',                           array($this, 'admin_bar_about_link'), 15); // Add a link to Video Central about page to the admin bar

        /* Ajax **************************************************************/

        // No _nopriv_ equivalent - users must be logged in
        add_action('wp_ajax_video_central_suggest_video',        array($this, 'suggest_video'));
        add_action('wp_ajax_video_central_suggest_user',         array($this, 'suggest_user'));

        /* Filters ***********************************************************/

        // Modify Video Central's admin links
        add_filter('plugin_action_links', array($this, 'modify_plugin_action_links'), 10, 2);

        // Map settings capabilities
        add_filter('video_central_map_meta_caps',   array($this, 'MapSettingsMetaCaps'), 10, 4);

        // Hide the theme compat package selection
        add_filter('video_central_admin_get_settings_sections', array($this, 'hide_theme_compat_packages'));

        // Allow keymasters to save Videos settings
        add_filter('option_page_capability_video_central',  array($this, 'option_page_capability_video_central'));

        /* Network Admin *****************************************************/

        // Add menu item to settings menu
        add_action('network_admin_menu',  array($this, 'NetworkAdminMenus'));

        /* Dependencies ******************************************************/

        // Allow plugins to modify these actions
        do_action_ref_array('video_central_admin_loaded', array(&$this));
    }

    /**
     * Add the admin menus.
     *
     * @since 1.0.0
     *
     * @uses add_management_page() To add the Recount page in Tools section
     * @uses add_options_page() To add the Videos settings page in Settings
     *                           section
     */
    public function AdminMenus()
    {

        // Are settings enabled?
        if (!video_central_settings_integration() && current_user_can('video_central_settings_page')) {
            add_options_page(
                __('Video Central',  'video_central'),
                __('Video Central',  'video_central'),
                $this->minimum_capability,
                'video_central',
                array(&$this, 'plugin_options_page')
            );
        }

        // These are later removed in admin_head
        if (current_user_can('video_central_about_page')) {

            // About
            add_dashboard_page(
                __('Welcome to Video Central',  'video_central'),
                __('Welcome to Video Central',  'video_central'),
                $this->minimum_capability,
                'video-central-about',
                array($this, 'about_screen')
            );

            // credits
            add_dashboard_page(
                __('Video Central Credits',  'video_central'),
                __('Video Central Credits',  'video_central'),
                $this->minimum_capability,
                'video-central-credits',
                array($this, 'credits_screen')
            );
        }

        // Bail if plugin is not network activated
        if (!is_plugin_active_for_network(video_central()->basename)) {
            return;
        }

        add_submenu_page(
            'index.php',
            __('Update Videos', 'video_central'),
            __('Update Videos', 'video_central'),
            'manage_network',
            'video-central-update',
            array($this, 'update_screen')
        );
    }

    /**
     * Add the network admin menus.
     *
     * @since 1.0.0
     *
     * @uses add_submenu_page() To add the Update Videos page in Updates
     */
    public function NetworkAdminMenus()
    {

        // Bail if plugin is not network activated
        if (!is_plugin_active_for_network(video_central()->basename)) {
            return;
        }

        add_submenu_page('upgrade.php', __('Update Videos', 'video_central'), __('Update Videos', 'video_central'), 'manage_network', 'video_central-update', array($this, 'network_update_screen'));
    }

    /**
     * If this is a new installation, create some initial video content.
     *
     * @since 1.0.0
     *
     * @return type
     */
    public static function NewInstall()
    {
        if (!video_central_is_install()) {
            return;
        }

        video_central_create_initial_content();
    }

    /**
     * Register the settings.
     *
     * @since 1.0.0
     *
     * @uses add_settings_section() To add our own settings section
     * @uses add_settings_field() To add various settings fields
     * @uses register_setting() To register various settings
     *
     * @todo Put fields into multidimensional array
     */
    public function RegisterAdminSettings()
    {

        // Bail if no sections available
        $sections = video_central_admin_get_settings_sections();

        if (empty($sections)) {
            return false;
        }

        // Are we using settings integration?
        $settings_integration = video_central_settings_integration();

        // Loop through sections
        foreach ((array) $sections as $section_id => $section) {

            // Only proceed if current user can see this section
            if (!current_user_can($section_id)) {
                continue;
            }

            // Only add section and fields if section has fields
            $fields = video_central_admin_get_settings_fields_for_section($section_id);
            if (empty($fields)) {
                continue;
            }

            // Toggle the section if core integration is on
            if ((true === $settings_integration) && !empty($section['page'])) {
                $page = $section['page'];
            } else {
                $page = 'video_central';

                $this->general_settings_key = $section_id;
                $this->plugin_settings_tabs[$this->general_settings_key] = $section['title'];
                $page = $this->general_settings_key;
            }

            // Add the section
            add_settings_section($section_id, $section['title'], $section['callback'], $page);

            // Loop through fields for this section
            foreach ((array) $fields as $field_id => $field) {

                // Add the field
                if (!empty($field['callback']) && !empty($field['title'])) {
                    add_settings_field($field_id, $field['title'], $field['callback'], $page, $section_id, $field['args']);
                }

                // Register the setting
                register_setting($page, $field_id, $field['sanitize_callback']);
            }
        }
    }

    /**
     * Maps settings capabilities.
     *
     * @since 1.0.0
     *
     * @param array  $caps    Capabilities for meta capability
     * @param string $cap     Capability name
     * @param int    $user_id User id
     * @param mixed  $args    Arguments
     *
     * @uses get_post() To get the post
     * @uses apply_filters() Calls 'video_central_map_meta_caps' with caps, cap, user id and
     *                        args
     *
     * @return array Actual capabilities for meta capability
     */
    public static function MapSettingsMetaCaps($caps = array(), $cap = '', $user_id = 0, $args = array())
    {

        // What capability is being checked?
        switch ($cap) {

            // Video Central
            case 'video_central_about_page'                      : // About and Credits
            case 'video_central_settings_page'                   : // Settings Page
            case 'video_central_settings_users'                  : // Settings - Users
            case 'video_central_settings_features'               : // Settings - Features
            case 'video_central_settings_theme_compat'           : // Settings - Theme compat
            case 'video_central_settings_slugs'                  : // Settings - slugs
            case 'video_central_settings_single_video_page'      : // Settings - Single Video page
            case 'video_central_settings_user_slugs'             : // Settings - User slugs
            case 'video_central_settings_youtube_api'            : // Settings - Youtube Api

                $caps = array(video_central()->admin->minimum_capability);
                break;
        }

        return apply_filters('video_central_map_settings_meta_caps', $caps, $cap, $user_id, $args);
    }

    /**
     * Admin area activation notice.
     *
     * Shows a nag message in admin area about the theme not supporting Video Central
     *
     * @since 1.0.0
     *
     * @uses current_user_can() To check notice should be displayed.
     */
    public function activation_notice()
    {
    }

    /**
     * Add Settings link to plugins area.
     *
     * @since 1.0.0
     *
     * @param array  $links Links array in which we would prepend our link
     * @param string $file  Current plugin basename
     *
     * @return array Processed links
     */
    public static function modify_plugin_action_links($links, $file)
    {

        // Return normal links if not Video Central
        if (plugin_basename(video_central()->file) !== $file) {
            return $links;
        }

        // New links to merge into existing links
        $new_links = array();

        // Settings page link
        if (current_user_can('video_central_settings_page')) {
            $new_links['settings'] = '<a href="'. esc_url( add_query_arg( array( 'page' => 'video_central' ), admin_url( 'options-general.php' ) ) ).'">'.esc_html__( 'Settings', 'video_central' ).'</a>';
        }

        // About page link
        if (current_user_can('video_central_about_page')) {
            $new_links['about'] = '<a href="'. esc_url( add_query_arg( array( 'page' => 'video-central-about' ), admin_url( 'index.php' ) ) ).'">'.esc_html__( 'About',    'video_central' ).'</a>';
        }

        // Add a few links to the existing links array
        return array_merge($links, $new_links);
    }

    /**
     * Add the 'Right now in Videos' dashboard widget.
     *
     * @since 1.0.0
     *
     * @uses wp_add_dashboard_widget() To add the dashboard widget
     */
    public static function dashboard_widget_right_now()
    {
        //wp_add_dashboard_widget( 'video-central-dashboard-right-now', __( 'Right Now in Video Central', 'video_central' ), 'video_central_dashboard_widget_right_now' );
    }

    /**
     * Add a link to Video Central about page to the admin bar.
     *
     * @since 1.0.0
     *
     * @param WP_Admin_Bar $wp_admin_bar
     */
    public function admin_bar_about_link($wp_admin_bar)
    {
        if (is_user_logged_in()) {
            $wp_admin_bar->add_menu(array(
                'parent' => 'wp-logo',
                'id' => 'video-central-about',
                'title' => esc_html__('About Video Central', 'video_central'),
                'href' => add_query_arg(array('page' => 'video-central-about'), admin_url('index.php')),
            ));
        }
    }

    /**
     * Enqueue any admin scripts we might need.
     *
     * @since 1.0.0
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script('suggest');

        // Get the version to use for JS
        $version = video_central_get_version();

        wp_enqueue_script('video-central-admin-import', $this->js_url.'source/import.js', array('jquery'), $version, true);

        // Post type checker (only videos)
        if ('post' === get_current_screen()->base) {
            switch (get_current_screen()->post_type) {
                case video_central_get_video_post_type() :

                    // Video Central admin
                    if (video_central_get_video_post_type() === get_current_screen()->post_type) {
                        wp_enqueue_script('video-central-admin-meta-js', $this->js_url.'meta.min.js', array('jquery'), $version, true);
                    }

                    break;
            }
        }
    }

    /**
     * Enqueue any admin scripts we might need.
     *
     * @since 1.0.0
     */
    public function enqueue_styles()
    {
        wp_enqueue_style('video-central-admin-css', $this->css_url.'style.css', array('dashicons'), video_central_get_version());
    }

    /**
     * Remove the individual recount and converter menus.
     * They are grouped together by h2 tabs.
     *
     * @since 1.0.0
     *
     * @uses remove_submenu_page() To remove menu items with alternat navigation
     */
    public function admin_head()
    {
        remove_submenu_page('tools.php', 'video-central-repair');
        remove_submenu_page('tools.php', 'video-central-converter');
        remove_submenu_page('tools.php', 'video-central-reset');
        remove_submenu_page('index.php', 'video-central-about');
        remove_submenu_page('index.php', 'video-central-credits');
    }

    /**
     * Registers the Video Central admin color scheme.
     *
     * Because wp-content can exist outside of the WordPress root there is no
     * way to be certain what the relative path of the admin images is.
     * We are including the two most common configurations here, just in case.
     *
     * @since 1.0.0
     *
     * @uses wp_admin_css_color() To register the color scheme
     */
    public function register_admin_style()
    {

        // RTL and/or minified
        $suffix = is_rtl() ? '-rtl' : '';
        //$suffix .= defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

        // Mint
        wp_admin_css_color(
            'video-central-mint',
            esc_html_x('Mint',      'admin color scheme', 'video_central'),
            $this->styles_url.'mint'.$suffix.'.css',
            array('#4f6d59', '#33834e', '#5FB37C', '#81c498'),
            array('base' => '#f1f3f2', 'focus' => '#fff', 'current' => '#fff')
        );

        // Evergreen
        wp_admin_css_color(
            'video-central-evergreen',
            esc_html_x('Evergreen', 'admin color scheme', 'video_central'),
            $this->styles_url.'evergreen'.$suffix.'.css',
            array('#324d3a', '#446950', '#56b274', '#324d3a'),
            array('base' => '#f1f3f2', 'focus' => '#fff', 'current' => '#fff')
        );

        // Bail if already using the fresh color scheme
        if ('fresh' === get_user_option('admin_color')) {
            return;
        }

        // Force 'colors-fresh' dependency
        global $wp_styles;
        $wp_styles->registered[ 'colors' ]->deps[] = 'colors-fresh';
    }

    /**
     * Hide theme compat package selection if only 1 package is registered.
     *
     * @since 1.0.0
     *
     * @param array $sections Videos settings sections
     *
     * @return array
     */
    public function hide_theme_compat_packages($sections = array())
    {
        if (count(video_central()->theme_compat->packages) <= 1) {
            unset($sections['video_central_settings_theme_compat']);
        }

        return $sections;
    }

    /**
     * Allow keymaster role to save Video settings.
     *
     * @since 1.0.0
     *
     * @param string $capability
     *
     * @return string Return 'keep_gate' capability
     */
    public function option_page_capability_video_central($capability = 'manage_options')
    {
        return $capability;
    }

    /** Ajax ******************************************************************/

    /**
     * Ajax action for facilitating the video auto-suggest.
     *
     * @since 1.0.0
     *
     * @uses get_posts()
     * @uses video_central_get_video_post_type()
     * @uses video_central_get_video_id()
     * @uses video_central_get_video_title()
     */
    public function suggest_video()
    {

        // Try to get some videos
        $videos = get_posts(array(
            's' => like_escape($_REQUEST['q']),
            'post_type' => video_central_get_video_post_type(),
        ));

        // If we found some videos, loop through and display them
        if (!empty($videos)) {
            foreach ((array) $videos as $post) {
                printf(esc_html__('%s - %s', 'video_central'), video_central_get_video_id($post->ID), video_central_get_video_title($post->ID)."\n");
            }
        }
        wp_die();
    }

    /**
     * Ajax action for facilitating the topic and reply author auto-suggest.
     *
     * @since 1.0.0
     */
    public function suggest_user() {

        global $wpdb;

        // Bail early if no request
        if ( empty( $_REQUEST['q'] ) ) {
            wp_die( '0' );
        }

        // Bail if user cannot moderate - only moderators can change authorship
        if ( ! current_user_can( 'moderate' ) ) {
            wp_die( '0' );
        }

        // Check the ajax nonce
        check_ajax_referer( 'video_central_suggest_user_nonce' );

        // Try to get some users
        $users_query = new WP_User_Query(array(
            'search'         => '*' . $wpdb->esc_like( $_REQUEST['q'] ) . '*',
            'fields' => array('ID', 'user_nicename'),
            'search_columns' => array('ID', 'user_nicename', 'user_email'),
            'orderby' => 'ID',
        ));

        // If we found some users, loop through and display them
        if (!empty($users_query->results)) {
            foreach ((array) $users_query->results as $user) {
                printf(esc_html__('%s - %s', 'video_central'), video_central_get_user_id($user->ID), video_central_get_user_nicename($user->ID, array('force' => $user->user_nicename))."\n");
            }
        }
        wp_die();
    }

    /** About *****************************************************************/

    /**
     * Output the about screen.
     *
     * @since 1.0.0
     */
    public function about_screen() {

        list($display_version) = explode('-', video_central_get_version() ); ?>

        <div class="wrap about-wrap">
            <h1><?php printf(esc_html__('Welcome to Video Central %s', 'video_central'), $display_version); ?></h1>
            <div class="about-text"><?php printf(esc_html__('Thank you for updating.', 'video_central'), $display_version); ?></div>

            <h2 class="nav-tab-wrapper">
                <a class="nav-tab nav-tab-active" href="<?php echo esc_url(admin_url(add_query_arg(array('page' => 'video-central-about'), 'index.php'))); ?>">
                    <?php esc_html_e('What&#8217;s New', 'video_central'); ?>
                </a>
                <a class="nav-tab" href="<?php echo esc_url(admin_url(add_query_arg(array('page' => 'video-central-credits'), 'index.php'))); ?>">
                    <?php esc_html_e('Credits', 'video_central'); ?>
                </a>
            </h2>

            <div class="changelog">
                <p><?php esc_html_e( 'Video central has been growing and improving with time to become the best video managerment plugin for WordPress. Below is a list of the latest changes in version ', 'video_central'); echo video_central_get_version(); ?>.</p>
                <h4><?php esc_html_e( 'Changelog', 'video_central' ); ?></h4>
                <div class="changelog-section">
                    <ul>
                        <li><?php esc_html_e( 'Added new playlist manager', 'video_central' ); ?></li>
                        <li><?php esc_html_e( 'Wordpress 4.9 compatibility', 'video_central' ); ?></li>
                        <li><?php esc_html_e( 'Removed get_currentuserinfo()', 'video_central' ); ?></li>
                    </ul>
            </div>
        </div>

            <div class="return-to-dashboard">
                <a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'video_central' ), 'options-general.php' ) ) ); ?>"><?php esc_html_e( 'Go to video Settings', 'video_central' ); ?></a>
            </div>

        </div><?php

    }

    /**
     * Output the about screen.
     *
     * @since 1.0.0
     */
    public function credits_screen() {

        list($display_version) = explode('-', video_central_get_version() ); ?>

        <div class="wrap about-wrap">
            <h1><?php printf(esc_html__('Welcome to Video Central %s', 'video_central'), $display_version); ?></h1>
            <div class="about-text"><?php printf(esc_html__('Thank you for updating.', 'video_central'), $display_version); ?></div>

            <h2 class="nav-tab-wrapper">
                <a class="nav-tab" href="<?php echo esc_url(admin_url(add_query_arg(array('page' => 'video-central-about'), 'index.php'))); ?>">
                    <?php esc_html_e('What&#8217;s New', 'video_central'); ?>
                </a>
                <a class="nav-tab nav-tab-active" href="<?php echo esc_url(admin_url(add_query_arg(array('page' => 'video-central-credits'), 'index.php'))); ?>">
                    <?php esc_html_e('Credits', 'video_central'); ?>
                </a>
            </h2>

            <div class="credits">
                <p><?php esc_html_e( 'Video central was created and is maintained by Franklin Gitonga.', 'video_central'); ?></br></br>
                <?php esc_html_e( 'Twitter: ', 'video_central'); ?><a href="https://twitter.com/FrankGM1">@FrankGM1</a></br>
                <?php esc_html_e( 'Website: ', 'video_central'); ?><a href="http://radiumthemes.com">RadiumThemes</a></br>
                <?php esc_html_e( 'Plugin Demo: ', 'video_central'); ?><a href="http://demo.videocentral.co">Video Central Demo</a></p>
            </div>

            <div class="return-to-dashboard">
                <a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'video_central' ), 'options-general.php' ) ) ); ?>"><?php esc_html_e( 'Go to video Settings', 'video_central' ); ?></a>
            </div>

        </div><?php

    }

    /** Updaters **************************************************************/

    /**
     * Update all Video Central Videos across all sites.
     *
     * @since 1.0.0
     *
     * @global WPDB $wpdb
     *
     * @uses get_blog_option()
     * @uses wp_remote_get()
     */
    public static function update_screen()
    {
    }

    /**
     * Update all Video Central Videos across all sites.
     *
     * @since 1.0.0
     *
     * @global WPDB $wpdb
     *
     * @uses get_blog_option()
     * @uses wp_remote_get()
     */
    public static function network_update_screen()
    {
    }

    /** Admin Settings UI **************************************************************/

    /*
     * Plugin Options page rendering goes here, checks
     * for active tab and replaces key with the related
     * settings key. Uses the plugin_options_tabs method
     * to render the tabs.
     */
    public function plugin_options_page() {
        $tab = isset($_GET['tab']) ? $_GET['tab'] : $this->general_settings_key; ?>

        <div class="wrap">

            <?php $this->plugin_options_tabs(); ?>

            <form method="post" action="options.php">

                <?php wp_nonce_field('update-options'); ?>

                <?php settings_fields($tab); ?>

                <?php do_settings_sections($tab); ?>

                <?php submit_button(); ?>

            </form>

        </div>

        <?php

    }

    /*
     * Renders our tabs in the plugin options page,
     * walks through the object's tabs array and prints
     * them one by one. Provides the heading for the
     * plugin_options_page method.
     */
    public function plugin_options_tabs()
    {
        $current_tab = isset($_GET['tab']) ? $_GET['tab'] : $this->general_settings_key;

        echo '<h2 class="nav-tab-wrapper">';

        foreach ($this->plugin_settings_tabs as $tab_key => $tab_caption) {
            $active = $current_tab == $tab_key ? 'nav-tab-active' : '';

            echo '<a class="nav-tab '.$active.'" href="?page=video_central&tab='.esc_attr($tab_key).'">'.$tab_caption.'</a>';
        }

        echo '</h2>';
    }
}
endif; // class_exists check

/**
 * Setup Video Central Admin.
 *
 * @since 1.0.0
 *
 * @uses Video_Central_Admin
 */
function video_central_admin()
{
    video_central()->admin = new Video_Central_Admin();
}
