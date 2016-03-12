<?php

/**
 * Video Central Shortcodes
 *
 * @package Video Central
 * @subpackage Shortcodes
 */

if ( !class_exists( 'Radium_Video_Shortcodes' ) ) :

/**
 * Video Central Shortcode Class
 *
 * @since 1.0.0
 */
class Radium_Video_Shortcodes {

    /** Vars ******************************************************************/

    /**
     * @var array Shortcode => function
     */
    public $codes = array();

    /** Functions *************************************************************/

    /**
     * Add the register_shortcodes action to video_central_init
     *
     * @since 1.0.0
     *
     * @uses setup_globals()
     * @uses add_shortcodes()
     */
    public function __construct() {
        $this->setup_globals();
        $this->add_shortcodes();
    }

    /**
     * Shortcode globals
     *
     * @since 1.0.0
     * @access private
     *
     * @uses apply_filters()
     */
    private function setup_globals() {

        // Setup the shortcodes
        $this->codes = apply_filters( 'video_central_shortcodes', array(

            /** Videos ********************************************************/

            'video-central-index'            => array( $this, 'display_videos_index' ), // Video Index
            'video-central-single-video'     => array( $this, 'display_video'        ), // Specific video - pass an 'id' attribute
            'video-central-playlist'         => array( $this, 'display_playlist'    ), // Video Player playlist
            'video-central-slider-grid'      => array( $this, 'video_slider_grid'     ), // Video Player shortcode

            /** Video Category ****************************************************/

            'video-central-video-categories'       => array( $this, 'display_video_categories'    ), // All video categories in a cloud
            'video-central-single-category'       => array( $this, 'display_videos_of_category' ), // Videos of category

            /** Video Tags ****************************************************/

            'video-central-video-tags'       => array( $this, 'display_video_tags'    ), // All video tags in a cloud
            'video-central-single-tag'       => array( $this, 'display_videos_of_tag' ), // Videos of Tag

            /** Views *********************************************************/
            'video-central-search'			=> array( $this, 'display_search' ), // search view
            'video-central-search-form'		=> array( $this, 'display_search_form' ), // search view

            'video-central-view'      		=> array( $this, 'display_view'   ), // Single view
            'video-central-single-video' 	=> array( $this, 'player'         ), // Single video

            'video-central-subtitle'        => array( $this, 'subtitle_track' )

        ) );

    }

    /**
     * Register the Video Central shortcodes
     *
     * @since 1.0.0
     *
     * @uses add_shortcode()
     * @uses do_action()
     */
    private function add_shortcodes() {

        foreach ( (array) $this->codes as $code => $function ) {
            add_shortcode( $code, $function );
        }

    }

    /**
     * Unset some globals in the $video_central object that hold query related info
     *
     * @since 1.0.0
     */
    private function unset_globals() {

        $video_central = video_central();

        // Unset global queries
        $video_central->video_query  = new WP_Query();

        // Unset global ID's
        $video_central->current_video_id     = 0;

        // Reset the post data
        wp_reset_postdata();

    }

    /** Output Buffers ********************************************************/

    /**
     * Start an output buffer.
     *
     * This is used to put the contents of the shortcode into a variable rather
     * than outputting the HTML at run-time. This allows shortcodes to appear
     * in the correct location in the_content() instead of when it's created.
     *
     * @since 1.0.0
     *
     * @param string $query_name
     *
     * @uses video_central_set_query_name()
     * @uses ob_start()
     */
    private function start( $query_name = '' ) {

        // Set query name
        video_central_set_query_name( $query_name );

        // Start output buffer
        ob_start();

    }

    /**
     * Return the contents of the output buffer and flush its contents.
     *
     * @since( r3079)
     *
     * @uses Radium_Video_Shortcodes::unset_globals() Cleans up global values
     * @return string Contents of output buffer.
     */
    private function end() {

        // Unset globals
        $this->unset_globals();

        // Reset the query name
        video_central_reset_query_name();

        // Return and flush the output buffer
        return ob_get_clean();

    }

    /** Video shortcodes ******************************************************/

    /**
     * Display an index of all visible root level videos in an output buffer
     * and return to ensure that post/page contents are displayed first.
     *
     * @since 1.0.0
     *
     * @param array $attr
     * @param string $content
     * @uses video_central_has_videos()
     * @uses get_template_part()
     * @return string
     */
    public function display_videos_index() {

        // Unset globals
        $this->unset_globals();

        // Start output buffer
        $this->start( 'video_central_video_archive' );

        video_central_get_template_part( 'content', 'archive-video' );

        // Return contents of output buffer
        return $this->end();

    }

    /**
     * Display the contents of a specific video ID in an output buffer
     * and return to ensure that post/page contents are displayed first.
     *
     * @since 1.0.0
     *
     * @param array $attr
     * @param string $content
     * @uses get_template_part()
     * @uses video_central_single_video_description()
     * @return string
     */
    public function display_video( $attr, $content = '' ) {

        // Sanity check required info
        if ( !empty( $content ) || ( empty( $attr['id'] ) || !is_numeric( $attr['id'] ) ) )
            return $content;

        // Set passed attribute to $video_id for clarity
        $video_id = video_central()->current_video_id = $attr['id'];

        // Bail if ID passed is not a video
        if ( !video_central_is_video( $video_id ) )
            return $content;

        // Start output buffer
        $this->start( 'video_central_single_video' );

        // Check video caps
        if ( video_central_user_can_view_video( array( 'video_id' => $video_id ) ) ) {
            video_central_get_template_part( 'content',  'single-video' );
        }

        // Return contents of output buffer
        return $this->end();

    }

    /**
     * Display a grid video slider
     * and return to ensure that post/page contents are displayed first.
     *
     * @since 1.0.0
     *
     * @param array $attr
     * @param string $content
     * @uses get_template_part()
     * @uses video_central_single_video_description()
     * @return string
     */
    public function video_slider_grid( $attr, $content = '' ) {

        // Unset globals
        $this->unset_globals();

        // Start output buffer
        $this->start( 'video_central_video_slider' );

        video_central_get_template_part( 'content', 'video-slider-grid' );

        // Return contents of output buffer
        return $this->end();

    }

    /** Views *****************************************************************/

    /**
     * Display the contents of a specific view in an output buffer and return to
     * ensure that post/page contents are displayed first.
     *
     * @since 1.0.0
     *
     * @param array $attr
     * @param string $content
     * @uses get_template_part()
     * @return string
     */
    public function display_view( $attr, $content = '' ) {

        // Sanity check required info
        if ( empty( $attr['id'] ) )
            return $content;

        // Set passed attribute to $view_id for clarity
        $view_id = $attr['id'];

        // Start output buffer
        $this->start( 'video_central_single_view' );

        // Unset globals
        $this->unset_globals();

        // Set the current view ID
        video_central()->current_view_id = $view_id;

        // Load the view
        video_central_view_query( $view_id );

        // Output template
        video_central_get_template_part( 'content', 'single-view' );

        // Return contents of output buffer
        return $this->end();
    }


    /** Query Filters *********************************************************/

    /**
     * Filter the query for the video index
     *
     * @since 1.0.0
     *
     * @param array $args
     * @return array
     */
    public function display_video_index_query( $args = array() ) {

        $args['author']        = 0;
        $args['show_stickies'] = true;
        $args['order']         = 'DESC';
        return $args;

    }

    /** Video Categories ************************************************************/

     /**
      * Display a tag cloud of all video categories in an output buffer and return to
      * ensure that post/page contents are displayed first.
      *
      * @since 1.0.0
      *
      * @return string
      */
     public function display_video_categories($attr, $content = '') {

         extract(shortcode_atts(array(
            'show_count'   => true,
            'hierarchical' => true,
            'dropdown' => false,
            'title_li' => '',
            'show_option_none' => __('Select Category', "video_central" )
        ), $attr));

         // Unset globals
         $this->unset_globals();

         // Start output buffer
         $this->start( 'video_central_video_categories' );

        $args = array(
            'taxonomy' => video_central_get_video_category_tax_id(),
            'orderby' => 'name',
            'show_count' => $show_count,
            'hierarchical' => $hierarchical
        );

        if ( $dropdown ) {

            $args['show_option_none'] = $show_option_none;
            wp_dropdown_categories(apply_filters('video_central_shortcode_video_categories_dropdown_args', $args));

         ?>
         <script type='text/javascript'>
         /* <![CDATA[ */
             var dropdown = document.getElementById("cat");
             function onCatChange() {
                 if ( dropdown.options[dropdown.selectedIndex].value > 0 ) {
                     location.href = "<?php echo home_url(); ?>/?video_category="+dropdown.options[dropdown.selectedIndex].value;
                 }
             }
             dropdown.onchange = onCatChange;
         /* ]]> */
         </script>
         <?php } else { ?><ul><?php
                 $args['title_li'] = $title_li;

                 wp_list_categories(apply_filters('video_central_shortcode_video_categories_args', $args));
         ?>
                 </ul>
         <?php

         }

         // Return contents of output buffer
         return $this->end();
     }

     /**
     * Filter the query for video categories
     *
     * @since 1.0.0
     *
     * @param array $args
     * @return array
     */
    public function display_videos_of_category_query( $args = array() ) {

        $args['tax_query'] = array( array(
            'taxonomy' => video_central_get_video_category_tax_id(),
            'field'    => 'id',
            'terms'    => video_central()->current_video_category_id
        ) );

        return $args;

    }

    /**
     * Display the contents of a specific video categories in an output buffer
     * and return to ensure that post/page contents are displayed first.
     *
     * @since 1.0.0
     *
     * @param array $attr
     * @param string $content
     * @uses get_template_part()
     * @return string
     */
    public function display_videos_of_category( $attr, $content = '' ) {

        // Sanity check required info
        if ( !empty( $content ) || ( empty( $attr['id'] ) || !is_numeric( $attr['id'] ) ) )
            return $content;

        // Unset globals
        $this->unset_globals();

        // Filter the query
        if ( ! video_central_is_video_category() ) {
            add_filter( 'video_central_before_has_videos_parse_args', array( $this, 'display_videos_of_category_query' ) );
        }

        // Start output buffer
        $this->start( 'video_central_video_category' );

        // Set passed attribute to $category_id for clarity
        video_central()->current_video_category_id = $category_id = $attr['id'];

        // Output template
        video_central_get_template_part( 'content', 'archive-video' );

        // Return contents of output buffer
        return $this->end();
    }


    /** Video Tags ************************************************************/

    /**
     * Display a tag cloud of all video tags in an output buffer and return to
     * ensure that post/page contents are displayed first.
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function display_video_tags() {

        // Unset globals
        $this->unset_globals();

        // Start output buffer
        $this->start( 'video_central_video_tags' );

        // Output the video tags
        wp_tag_cloud( array(
            'smallest' => 9,
            'largest'  => 38,
            'number'   => 80,
            'taxonomy' => video_central_get_video_tag_tax_id()
        ) );

        // Return contents of output buffer
        return $this->end();
    }


    /**
     * Filter the query for video tags
     *
     * @since 1.0.0
     *
     * @param array $args
     * @return array
     */
    public function display_videos_of_tag_query( $args = array() ) {

        $args['tax_query'] = array( array(
            'taxonomy' => video_central_get_video_tag_tax_id(),
            'field'    => 'id',
            'terms'    => video_central()->current_video_tag_id
        ) );

        return $args;

    }

    /**
     * Display the contents of a specific video tag in an output buffer
     * and return to ensure that post/page contents are displayed first.
     *
     * @since 1.0.0
     *
     * @param array $attr
     * @param string $content
     * @uses get_template_part()
     * @return string
     */
    public function display_videos_of_tag( $attr, $content = '' ) {

        // Sanity check required info
        if ( !empty( $content ) || ( empty( $attr['id'] ) || !is_numeric( $attr['id'] ) ) )
            return $content;

        // Unset globals
        $this->unset_globals();

        // Filter the query
        if ( ! video_central_is_video_tag() ) {
            add_filter( 'video_central_before_has_videos_parse_args', array( $this, 'display_videos_of_tag_query' ) );
        }

        // Start output buffer
        $this->start( 'video_central_video_tag' );

        // Set passed attribute to video_central()->current_video_tag_id for clarity
        video_central()->current_video_tag_id = $attr['id'];

        // Output template
        video_central_get_template_part( 'content', 'archive-video' );

        // Return contents of output buffer
        return $this->end();
    }

    /* The [video-central] or [videojs] shortcode */
    function player( $atts, $content=null ) {

        //video_central__player_js_swf();

        extract(shortcode_atts(array(
            'mp4' => '',
            'webm' => '',
            'ogg' => '',
            'youtube' => '',
            'poster' => '',
            'width' => '640',
            'height' => '264',
            'preload' => 'true',
            'autoplay' => 'false',
            'loop' => '',
            'controls' => '',
            'id' => null,
            'class' => '',
            'muted' => ''
        ), $atts));

        $dataSetup = array();

        return video_central_get_player( $id );

        // MP4 Source Supplied
        if ($mp4)
            $mp4_source = '<source src="'.$mp4.'" type=\'video/mp4\' />';
        else
            $mp4_source = '';

        // WebM Source Supplied
        if ($webm)
            $webm_source = '<source src="'.$webm.'" type=\'video/webm; codecs="vp8, vorbis"\' />';
        else
            $webm_source = '';

        // Ogg source supplied
        if ($ogg)
            $ogg_source = '<source src="'.$ogg.'" type=\'video/ogg; codecs="theora, vorbis"\' />';
        else
            $ogg_source = '';

        if ($youtube) {
            $dataSetup['forceSSL'] = 'true';
            $dataSetup['techOrder'] = array("youtube");
            $dataSetup['src'] = $youtube;
        }
        // Poster image supplied
        if ($poster)
            $poster_attribute = ' poster="'.$poster.'"';
        else
            $poster_attribute = '';

        // Preload the video?
        if ($preload == "auto" || $preload == "true" || $preload == "on")
            $preload_attribute = ' preload="auto"';
        elseif ($preload == "metadata")
            $preload_attribute = ' preload="metadata"';
        else
            $preload_attribute = ' preload="none"';

        // Autoplay the video?
        if ($autoplay == "true" || $autoplay == "on")
            $autoplay_attribute = " autoplay";
        else
            $autoplay_attribute = "";

        // Loop the video?
        if ($loop == "true")
            $loop_attribute = " loop";
        else
            $loop_attribute = "";

        // Controls?
        if ($controls == "false")
            $controls_attribute = "";
        else
            $controls_attribute = " controls";

        // Is there a custom class?
        if ($class)
            $class = ' ' . $class;

        // Muted?
        if ( $muted == "true" )
            $muted_attribute = " muted";
        else
            $muted_attribute = "";

        // Tracks
        if(!is_null( $content ))
            $track = do_shortcode($content);
        else
            $track = "";

        $jsonDataSetup = str_replace('\\/', '/', json_encode($dataSetup));

        if($id) $id = 'id="{$id}";';

        //Output the <video> tag
        $videojs = <<<_end_

        <video class="video-js vjs-default-skin{$class}" width="{$width}" height="{$height}"{$poster_attribute}{$controls_attribute}{$preload_attribute}{$autoplay_attribute}{$loop_attribute}{$muted_attribute} data-setup='{$jsonDataSetup}'>
            {$mp4_source}
            {$webm_source}
            {$ogg_source}{$track}
        </video>

_end_;

        return $videojs;

    }

    /* The [track] shortcode */
    function subtitle_track($atts, $content=null){

        extract(shortcode_atts(array(
            'kind' => '',
            'src' => '',
            'srclang' => '',
            'label' => '',
            'default' => ''
        ), $atts));

        if($kind)
            $kind = " kind='" . $kind . "'";

        if($src)
            $src = " src='" . $src . "'";

        if($srclang)
            $srclang = " srclang='" . $srclang . "'";

        if($label)
            $label = " label='" . $label . "'";

        if($default == "true" || $default == "default")
            $default = " default";
        else
            $default = "";

        $track = "<track" . $kind . $src . $srclang . $label . $default . " />";

        return $track;
    }

    /**
     * The Video player shortcode.
     *
     * This implements the functionality of the Video Shortcode for displaying
     * WordPress mp4s in a post.
     *
     * @since 3.6.0
     *
     * @param array $attr Attributes of the shortcode.
     * @return string HTML content to display video.
     */
    function video_player( $attr ) {

        global $content_width;

        $post_id = get_post() ? get_the_ID() : 0;

        static $instances = 0;

        $instances++;

        $video = null;

        $default_types = wp_get_video_extensions();

        $defaults_atts = array(
            'src'      => '',
            'poster'   => '',
            'loop'     => '',
            'autoplay' => '',
            'preload'  => 'metadata',
            'height'   => 360,
            'width'    => empty( $content_width ) ? 820 : $content_width,
            'upload_source' => 'self',
        );

        foreach ( $default_types as $type )
            $defaults_atts[$type] = '';

        $atts = shortcode_atts( $defaults_atts, $attr, 'video' );

        extract( $atts );

        $w = $width;
        $h = $height;

        if ( is_admin() && $width > 600 )
            $w = 600;

        elseif ( ! is_admin() && $w > $defaults_atts['width'] )
            $w = $defaults_atts['width'];

        if ( $w < $width )
            $height = round( ( $h * $w ) / $width );

        $width = $w;

        $primary = false;

        if ( ! empty( $src ) ) {

            $type = wp_check_filetype( $src, wp_get_mime_types() );

            $primary = true;

            array_unshift( $default_types, 'src' );

        } else {

            foreach ( $default_types as $ext ) {

                if ( ! empty( $ext ) ) {

                    $type = wp_check_filetype( $ext, wp_get_mime_types() );

                    if ( $type['ext'] === $ext )
                        $primary = true;

                }
            }

        }

        if ( ! $primary ) {

            $videos = get_attached_media( 'video', $post_id );

            if ( empty( $videos ) )
                return;

            $video = reset( $videos );

            $src = wp_get_attachment_url( $video->ID );

            if ( empty( $src ) )
                return;

            array_unshift( $default_types, 'src' );

        }

        $library = apply_filters( 'video_central_shortcode_library', 'mediaelement' );

        if ( 'mediaelement' === $library && did_action( 'init' ) ) {

            wp_dequeue_style( 'wp-mediaelement' );
            wp_enqueue_style( 'video-central-mediaelement-skin' );
            wp_enqueue_script( 'wp-mediaelement' );

        }

        $atts = array(
            'class'    => apply_filters( 'video_central_shortcode_class', 'video-central-player video-central-skin' ),
            'id'       => sprintf( 'video-%d-%d', $post_id, $instances ),
            'width'    => absint( $width ),
            'height'   => absint( $height ),
            'poster'   => esc_url( $poster ),
            'loop'     => $loop,
            'autoplay' => $autoplay,
            'preload'  => $preload,
        );

        // These ones should just be omitted altogether if they are blank
        foreach ( array( 'poster', 'loop', 'autoplay', 'preload' ) as $a ) {
            if ( empty( $atts[$a] ) )
                unset( $atts[$a] );
        }

        $attr_strings = array();

        foreach ( $atts as $k => $v ) {
            $attr_strings[] = $k . '="' . esc_attr( $v ) . '"';
        }

        $html = null;

        if ( 'mediaelement' === $library && 1 === $instances )
            $html .= "<!--[if lt IE 9]><script>document.createElement('video');</script><![endif]-->\n";

        $html .= sprintf( '<video %s controls="controls">', join( ' ', $attr_strings ) );

        $fileurl = '';

        $source = '<source type="%s" src="%s" />';

        foreach ( $default_types as $fallback ) {

            if ( ! empty( $fallback ) && $upload_source == 'self' ) {

                if ( empty( $fileurl ) )
                    $fileurl = $fallback;

                $type = wp_check_filetype( $fallback, wp_get_mime_types() );

                // m4v sometimes shows up as video/mpeg which collides with mp4
                if ( 'm4v' === $type['ext'] ) {
                    $type['type'] = 'video/m4v';
                }

                $html .= sprintf( $source, $type['type'], esc_url( $fallback ) );

            }

        }

        if ( $upload_source == 'youtube') {

            $video_type = 'video/youtube';
            $html .= sprintf( $source, $video_type, esc_url( $src ) );

        }

        if ( 'mediaelement' === $library )
            $html .= wp_mediaelement_fallback( $fileurl );

        $html .= '</video>';

        $html = sprintf( '<div class="video-central-player-wrapper" style="width: %dpx; max-width: 100%%;">%s</div>', $width, $html );

        return apply_filters( 'video_central_shortcode', $html, $atts, $video, $post_id, $library );

    }

    /** Search ****************************************************************/

    /**
     * Display the search form in an output buffer and return to ensure
     * post/page contents are displayed first.
     *
     * @since 1.0.0
     *
     * @uses get_template_part()
     */
    public function display_search_form() {

        // Bail if search is disabled
        if ( ! video_central_allow_search() ) {
            return;
        }

        // Start output buffer
        $this->start( 'video_central_search_form' );

        // Output templates
        video_central_get_template_part( 'form', 'search' );

        // Return contents of output buffer
        return $this->end();
    }

    /**
     * Display the contents of search results in an output buffer and return to
     * ensure that post/page contents are displayed first.
     *
     * @since 1.0.0
     *
     * @param array $attr
     * @param string $content
     * @uses video_central_search_query()
     * @uses get_template_part()
     */
    public function display_search( $attr, $content = '' ) {

        // Sanity check required info
        if ( !empty( $content ) ) {
            return $content;
        }

        // Bail if search is disabled
        if ( ! video_central_allow_search() ) {
            return;
        }

        // Trim search attribute if it's set
        if ( isset( $attr['search'] ) ) {
            $attr['search'] = trim( $attr['search'] );
        }

        // Set passed attribute to $search_terms for clarity
        $search_terms = empty( $attr['search'] ) ? video_central_get_search_terms() : $attr['search'];

        // Unset globals
        $this->unset_globals();

        // Set terms for query
        set_query_var( video_central_get_search_rewrite_id(), $search_terms );

        // Start output buffer
        $this->start( video_central_get_search_rewrite_id() );

        // Output template
        video_central_get_template_part( 'content', 'search' );

        // Return contents of output buffer
        return $this->end();
    }

     /**
     * Display the contents of a specific video ID in an output buffer
     * and return to ensure that post/page contents are displayed first.
     *
     * @since 1.2.0
     *
     * @param array $attr
     * @param string $content
     * @uses get_template_part()
     * @uses video_central_single_video_description()
     * @return string
     */
    public function display_playlist( $attr, $content = '' ) {

        if(!function_exists('video_central_get_playlist_id')) return __('Playlist feature not available', 'video_central');

        // Sanity check required info
        if ( !empty( $content ) || ( empty( $attr['id'] ) ) )
            return $content;

        $playlist_instance = 0;
        video_central()->playlist_instance++;

        // check if attr is set
        if( !is_array( $attr ) || !array_key_exists('id', $attr) ){
            return;
        }

        /**
         * Filter the playlist output.
         *
         * Passing a non-empty value to the filter will short-circuit generation
         * of the default playlist output, returning the passed value instead.
         *
         * @since 1.2.0
         *
         * @param string $output   Playlist output. Default empty.
         * @param array  $attr     An array of shortcode attributes.
         * @param int    $instance Unique numeric ID of this playlist shortcode instance.
         */
        $output = apply_filters( 'video_central_playlist_shortcode', '', $attr, $playlist_instance );

        if ( $output != '' ) {
            return $output;
        }

        $attr = shortcode_atts( array(
            'id'        => '',
            'order'     => 'ASC',
            'orderby'   => 'menu_order ID',
            'style'     => 'light',
        ), $attr, 'video_central_playlist' );

        if ( is_feed() ) {
            $output = "\n";
            foreach ( $attachments as $att_id => $attachment ) {
                $output .= video_central_get_video_permalink( $att_id ) . "\n";
            }
            return $output;
        }

        $output .= '<div id="video-central-playlist-' . video_central_get_playlist_id().'" class="video-central-playlist loading">';

        $output .= video_central_get_playlist($attr);

        $output .= '</div>';

        return $output;

    }

}

endif;
