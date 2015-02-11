<?php

/**
 * Posttype class for Radium_Video.
 *
 * @since 1.0.0
 *
 * @package Radium_Video
 * @author  Franklin M Gitonga
 */

class Radium_Video_Posttype {

    /**
     * Holds a copy of the object for easy reference.
     *
     * @since 1.0.0
     *
     * @var object
     */
    private static $instance;

    /**
     * Constructor. Hooks all interactions to initialize the class.
     *
     * @since 1.0.0
     */
    public function __construct() {

        self::$instance = $this;

        $this->post_type = video_central_get_video_post_type();

        $this->video_init();

        add_action( 'after_setup_theme', array( &$this, 'setup' ), 99 );

        // PORTFOLIO THUMBNAILS
       	add_filter( 'manage_edit-video_columns', array( &$this, 'add_thumbnail_column'), 10, 1 );

        // add columns to posts table
        add_filter('manage_edit-'.$this->post_type.'_columns', array( $this, 'extra_columns' ));

        add_filter('manage_edit-'.$this->post_type.'_sortable_columns', array( $this, 'make_extra_sortable_columns' ));

       	add_action('manage_'.$this->post_type.'_posts_custom_column', array($this, 'output_extra_columns'), 10, 2);

        add_action( 'restrict_manage_posts', array( &$this, 'add_taxonomy_filters' ) );

        add_action( 'right_now_content_table_end', array( &$this, 'add_video_counts' ) );

        add_action( 'admin_head', array( &$this, 'video_icon' ) );

       // add_action( 'admin_menu', array( &$this, 'create_video_sort_page') );

        add_action( 'wp_ajax_video_sort', array( &$this, 'save_video_sorted_order' ) );

        add_filter('pre_get_posts', array( &$this, 'admin_order') );

        add_action( 'load-edit.php', array( &$this, 'edit_videos_load' ) );


    }

    /**
     *  Setup theme support
     *
     * @return null featured image support late http://wordpress.stackexchange.com/questions/23839/using-add-theme-support-inside-a-plugin
     */
    public function setup() {

        global $_wp_theme_features;

        if( empty($_wp_theme_features['post-thumbnails']) )
            $_wp_theme_features['post-thumbnails'] = array( array('video') );

        elseif( true === $_wp_theme_features['post-thumbnails'])
            return;

        elseif( is_array($_wp_theme_features['post-thumbnails'][0]) )
            $_wp_theme_features['post-thumbnails'][0][] = 'video';

    }


    /*--------------------------------------------------------------------*/
    /*  FLUSH REWRITE RULES
    /*--------------------------------------------------------------------*/
    public function plugin_activation() {
        flush_rewrite_rules();
    }

    public function video_init() {

		$video_central = video_central();

        // Register Video content type
        register_post_type(
            video_central_get_video_post_type(),
            apply_filters( 'video_central_register_post_type', array(
                'labels'              => video_central_get_video_post_type_labels(),
                'rewrite'             => video_central_get_video_post_type_rewrite(),
                'supports'            => video_central_get_video_post_type_supports(),
                'description'         => __( 'Radium Videos', 'video_central' ),
                //'capabilities'        => video_central_get_video_caps(),
                //'capability_type'     => array( 'video', 'videos' ),
                'menu_position'       => 5,
                'has_archive'         => video_central_get_root_slug(),
                'exclude_from_search' => false,
                'show_in_nav_menus'   => true,
                'public'              => true,
                //'show_ui'             => current_user_can( 'video_central_admin' ),
                'can_export'          => true,
                'hierarchical'        => true,
                'query_var'           => true,
                'menu_icon'           => ''
            ) )
        );

        /*

        $taxonomy_video_tag_args = array(
            'labels' => video_central_get_video_tag_tax_labels(),
            'public' => true,
            'show_in_nav_menus' => true,
            'show_ui' => true,
            'show_tagcloud' => true,
            'hierarchical' => false,
            'rewrite' => video_central_get_video_tag_tax_rewrite(),
            'show_admin_column' => true,
            'query_var' => true
        );

        register_taxonomy( $video_central->video_tag_tax_id, array( $this->post_type ), $taxonomy_video_tag_args );

        $taxonomy_video_category_args = array(
            'labels'            => video_central_get_video_category_tax_labels(),
            'public'            => true,
            'show_in_nav_menus' => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_tagcloud'     => true,
            'hierarchical'      => true,
            'rewrite'           => video_central_get_video_category_tax_rewrite(),
            'query_var'         => true
        );

        register_taxonomy( $video_central->video_cat_tax_id, array( $this->post_type ), $taxonomy_video_category_args ); */

        $this->register_taxonomies();

    }

    /**
	 * Register the topic tag taxonomy
	 *
	 * @since 1.0.0
	 * @uses register_taxonomy() To register the taxonomy
	 */
	public function register_taxonomies() {

		// Register the video-tag taxonomy
		register_taxonomy(
			video_central_get_video_tag_tax_id(),
			video_central_get_video_post_type(),
			apply_filters( 'video_central_register_video_tag_taxonomy', array(
				'labels'                => video_central_get_video_tag_tax_labels(),
				'rewrite'               => video_central_get_video_tag_tax_rewrite(),
				//'capabilities'          => video_central_get_video_tag_caps(),
				'query_var'             => true,
				'show_tagcloud'         => true,
				'hierarchical'          => false,
				'show_in_nav_menus'     => true,
				'public'                => true,
				'show_ui'               => video_central_allow_video_tags() /*&& current_user_can( 'video_central_video_tags_admin' ) */
			)
		) );

		// Register the video-tag taxonomy
		register_taxonomy(
			video_central_get_video_category_tax_id(),
			video_central_get_video_post_type(),
			apply_filters( 'video_central_register_video_category_taxonomy', array(
				'labels'                => video_central_get_video_category_tax_labels(),
				'rewrite'               => video_central_get_video_category_tax_rewrite(),
				//'capabilities'          => video_central_get_video_category_caps(),
				'query_var'             => true,
				'hierarchical'          => true,
				'show_in_nav_menus'     => true,
				'public'                => true,
				'show_ui'               => video_central_allow_video_categories() /*&& current_user_can( 'video_central_video_categories_admin' ) */
			)
		) );

	}

    /*--------------------------------------------------------------------*/
    /*  ADD TAXONOMY FILTERS TO THE ADMIN PAGE - http://pippinsplugins.com
    /*--------------------------------------------------------------------*/
    public function add_taxonomy_filters() {

        global $typenow;

        // USE TAXONOMY NAME OR SLUG
        $taxonomies = array( 'video_category', 'video_tag' );

        // POST TYPE FOR THE FILTER
        if ( $typenow == $this->post_type ) {

            foreach ( $taxonomies as $tax_slug ) {
                $current_tax_slug = isset( $_GET[$tax_slug] ) ? $_GET[$tax_slug] : false;
                $tax_obj = get_taxonomy( $tax_slug );
                $tax_name = $tax_obj->labels->name;
                $terms = get_terms($tax_slug);
                if ( count( $terms ) > 0) {
                    echo "<select name='$tax_slug' id='$tax_slug' class='postform'>";
                    echo "<option value=''>$tax_name</option>";
                    foreach ( $terms as $term ) {
                        echo '<option value=' . $term->slug, $current_tax_slug == $term->slug ? ' selected="selected"' : '','>' . $term->name .' (' . $term->count .')</option>';
                    }
                    echo "</select>";
                }
            }
        }
    }


    /*--------------------------------------------------------------------*/
    /*  ADD PORTFOLIO COUNT TO "RIGHT NOW" DASHBOARD WIDGET
    /*--------------------------------------------------------------------*/
    public function add_video_counts() {

        if ( ! post_type_exists( $this->post_type ) )
             return;

        $num_posts = wp_count_posts( $this->post_type );
        $num = number_format_i18n( $num_posts->publish );
        $text = _n( 'Video Item', 'Video Items', intval($num_posts->publish), 'video_central' );
        if ( current_user_can( 'edit_posts' ) ) {
            $num = "<a href='edit.php?post_type=".$this->post_type."'>$num</a>";
            $text = "<a href='edit.php?post_type=". $this->post_type ."'>$text</a>";
        }
        echo '<td class="first b b-'. $this->post_type .'">' . $num . '</td>';
        echo '<td class="t video">' . $text . '</td>';
        echo '</tr>';

        if ($num_posts->pending > 0) {
            $num = number_format_i18n( $num_posts->pending );
            $text = _n( 'Video Item Pending', 'Video Items Pending', intval($num_posts->pending), 'video_central' );
            if ( current_user_can( 'edit_posts' ) ) {
                $num = "<a href='edit.php?post_status=pending&post_type=video'>$num</a>";
                $text = "<a href='edit.php?post_status=pending&post_type=video'>$text</a>";
            }
            echo '<td class="first b b-video">' . $num . '</td>';
            echo '<td class="t video">' . $text . '</td>';

            echo '</tr>';
        }
    }


    /*--------------------------------------------------------------------*/
    /*  PORTFOLIO SORTING
    /*--------------------------------------------------------------------*/
    public function create_video_sort_page() {

        $video_central_sort_page = add_submenu_page('edit.php?post_type='.$this->post_type, __('Sort Videos', 'video_central'), __('Sort', 'video_central'), 'edit_posts', basename(__FILE__), array($this, 'video_sort'));

        add_action('admin_print_styles-' . $video_central_sort_page, array($this, 'print_sort_styles')) ;
        add_action('admin_print_scripts-' . $video_central_sort_page , array($this,'print_sort_scripts'));

    }

    //OUTPUT FOR SORTING PAGE
    public function video_sort() {

        $videos = new WP_Query('post_type=video&posts_per_page=-1&orderby=menu_order&order=DESC'); ?>

        <div class="wrap">

            <div id="icon-tools" class="icon32"></div>

            <h2><?php _e('Sort Video', 'video_central'); ?></h2>

            <p><?php _e('Click, drag, re-order & repeat as necessary. The item at the top of the list will display first.', 'video_central'); ?></p>

                <ul id="video_list">

                    <?php while( $videos->have_posts() ) : $videos->the_post();

                        if( get_post_status() == 'publish' ) { ?>

                            <li id="<?php the_id(); ?>" class="menu-item">

                                <dl class="menu-item-bar">

                                    <dt class="menu-item-handle">

                                        <span class="menu-item-title"><?php the_title(); ?></span>

                                    </dt><!-- END .menu-item-handle -->

                                </dl><!-- END .menu-item-bar -->

                                <ul class="menu-item-transport"></ul>

                            </li><!-- END .menu-item -->

                    <?php } endwhile; wp_reset_postdata(); ?>

                </ul><!-- END #video_list -->

            </div><!-- END .wrap -->

    <?php }

    //ORDER
    public function save_video_sorted_order() {

        global $wpdb;

        $order = explode(',', $_POST['order']);
        $counter = 0;

        foreach($order as $video_id) {
            $wpdb->update($wpdb->posts, array('menu_order' => $counter), array('ID' => $video_id));
            $counter++;
        }

        die(1);

    }

    // SCRIPTS
    public function print_sort_scripts() {
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script( 'video_central_sort', Video_Central::get_url() .'/assets/admin/js/video_central_sort.js', array('jquery') );

    }

    // SORTER STYLES
    public function print_sort_styles() {
        wp_enqueue_style ('nav-menu');
    }

    /**
     * Extra columns in list table
     * @param array $columns
     */
    public function extra_columns( $columns ){

        $cols = array();

        foreach( $columns as $c => $t ){

            $cols[$c] = $t;

            if( 'title' == $c ){

                $cols['video_id']   = __('Video ID',    'video_central');
                $cols['duration']   = __('Duration',    'video_central');
                $cols['post_views'] = __('Views',       'video_central');

            }

        }

        return $cols;

    }

	/* Thumbnail COLUMNS
	 * @param array $columns
	 */

	public function add_thumbnail_column( $columns ) {

	    $column_thumb = array( 'thumbnail' => __('Thumbnail', 'video_central') );

	    $columns = array_slice( $columns, 0, 2, true ) + $column_thumb + array_slice( $columns, 1, NULL, true );

	   return $columns;
	}

    /**
     * Extra columns in list table output
     * @param string $column_name
     * @param int $post_id
     */
    public function output_extra_columns($column_name, $post_id){

        switch( $column_name ){

            case 'video_id':

                echo get_post_meta( $post_id, '_video_central_video_id', true );

            break;

            case 'thumbnail':

                echo get_the_post_thumbnail( $post_id, array(35, 35) );

            break;

            case 'duration':

                $meta = get_post_meta( $post_id, '_video_central_video_data', true );

                $duration = isset($meta['duration']) ? video_central_sec_to_time($meta['duration']) : "null";

                echo $duration;

            break;

            case 'post_views':

                echo video_central_get_video_views($post_id);

            break;
        }

    }

    /**
     * Extra sortable columns in list table output
     * @param string $column_name
     * @param int $post_id
     */
    public function make_extra_sortable_columns($columns){

        $columns['duration'] = 'duration';
        $columns['post_views'] = 'post_views';

        return $columns;

    }

    /**
     * Make the default order DESC by date ie latest videos are displayed first
     *
     * @since  1.0.0
     */
    function admin_order($wp_query) {

        if (is_admin()) {
			
			global $wp_query;
			
            // Get the post type from the query
            $post_type = $wp_query->query['post_type'];

            if ( $post_type == $this->post_type ) {

                // 'orderby' value can be any column name
                $wp_query->set('orderby', 'date');

                // 'order' value can be ASC or DESC
                $wp_query->set('order', 'DESC');

            }

        }
    }

    /**
     * Make columns sortable
     *
     * @since  1.0.0
     */
    function edit_videos_load() {
        add_filter( 'request', array(&$this, 'sort_videos_by_column' ));
    }

    /**
     *
     * Make videos sortable columns
     *
     * Only run our customization on the 'edit.php' page in the admin.
     *
     * @since  1.0.0
     */
    function sort_videos_by_column( $vars ) {

        /* Check if we're viewing the 'movie' post type. */
        if ( isset( $vars['post_type'] ) &&  $this->post_type == $vars['post_type'] ) {

            /* Check if 'orderby' is set to 'duration'. */
            if ( isset( $vars['orderby'] ) && 'duration' == $vars['orderby'] ) {

                /* Merge the query vars with our custom variables. */
                $vars = array_merge(
                    $vars,
                    array(
                        'meta_key' => '_video_central_video_duration',
                        'orderby' => 'meta_value_num'
                    )
                );

            } elseif ( ( $vars['orderby'] ) && 'post_views' == $vars['orderby'] ) {

                /* Merge the query vars with our custom variables. */
                $vars = array_merge(
                    $vars,
                    array(
                        'meta_key' => '_video_central_video_views_count',
                        'orderby' => 'meta_value_num'
                    )
                );

            }
        }

        return $vars;
    }

    /*--------------------------------------------------------------------*/
    /*  CUSTOM ICON FOR POST TYPE
    /*--------------------------------------------------------------------*/
    public function video_icon() { ?>
        <style type="text/css" media="screen">
             #adminmenu #menu-posts-video div.wp-menu-image{
                background:transparent url(<?php echo Video_Central::get_url() ?>/assets/admin/images/icon-video.png) no-repeat 6px 7px !important;
                }
             #adminmenu #menu-posts-video:hover div.wp-menu-image,
             #adminmenu #menu-posts-video.wp-has-current-submenu div.wp-menu-image {
                background:transparent url(<?php echo Video_Central::get_url() ?>/assets/admin/images/icon-video.png) no-repeat 6px 7px !important;
                }

             @media all and (-webkit-min-device-pixel-ratio: 1.5) {
                #adminmenu #menu-posts-video div.wp-menu-image{
                    background:transparent url(<?php echo Video_Central::get_url() ?>/assets/admin/images/icon-video@2x.png) no-repeat 6px 7px !important;
                    background-size: 16px 40px!important;
                    }
                #adminmenu #menu-posts-video:hover div.wp-menu-image,
                #adminmenu #menu-posts-video.wp-has-current-submenu div.wp-menu-image {
                    background:transparent url(<?php echo Video_Central::get_url() ?>/assets/admin/images/icon-video@2x.png) no-repeat 6px 7px !important;
                    background-size: 16px 40px!important;
                    }
             }
        </style>
        <?php
    }

    /**
     * Getter method for retrieving the object instance.
     *
     * @since 1.0.0
     */
    public static function get_instance() {

        return self::$instance;

    }

}
