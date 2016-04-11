<?php
/**
 * List imported videos.
 */
class Video_Central_Youtube_Importer_ListTable extends WP_List_Table
{
    /**
     * The current list of items.
     *
     * @since 1.2.0
     *
     * @var array
     */
    public $items;

    /**
     * Feed error.
     *
     * @since 1.2.0
     *
     * @var bool
     */
    private $feed_errors = false;

    /**
     * Total items in feed.
     *
     * @since 1.2.0
     *
     * @var int
     */
    private $total_items = 0;

    /**
     * Next page token.
     *
     * @since 1.2.0
     *
     * @var string
     */
    private $next_token = '';

    /**
     * Previous page token.
     *
     * @since 1.2.0
     *
     * @var string
     */
    private $prev_token = '';

    /**
     * Constructor. Hooks all interactions to initialize the class.
     *
     * @since 1.0.0
     */
    public function __construct($args = array())
    {
        parent::__construct(array(
            'singular' => 'video',
            'plural' => 'videos',
            'screen' => isset($args['screen']) ? $args['screen'] : null,
        ));
    }

    /**
     * Default column.
     *
     * @param array  $item
     * @param string $column
     *
     * @since 1.0.0
     */
    public function column_default($item, $column)
    {
        if (array_key_exists($column, $item)) {
            return $item[ $column ];
        } else {
            return '<span style="color:red">'.sprintf(__('Column <em>%s</em> was not found.', 'video_central'), $column).'</span>';
        }
    }

    /**
     * Checkbox column.
     *
     * @param array $item
     *
     * @since 1.0.0
     */
    public function column_cb($item)
    {
        $output = sprintf('<input type="checkbox" name="video_central_import[]" value="%1$s" id="video_central_%1$s" />', $item['video_id']);

        return $output;
    }

    /**
     * Title column.
     *
     * @param array $item
     *
     * @since 1.0.0
     */
    public function column_title($item)
    {
        $label = sprintf('<label for="video_central_%1$s" class="video_central_label">%2$s</label>', $item['video_id'], $item['title']);

        // row actions
        $actions = array(
            'view' => sprintf('<a href="http://www.youtube.com/watch?v=%1$s" target="_video_central_youtube_open">%2$s</a>', $item['video_id'], __('View on YouTube', 'video_central')),
        );

        return sprintf('%1$s %2$s',
            $label,
            $this->row_actions($actions)
        );
    }

    /**
     * Column for video duration.
     *
     * @param array $item
     *
     * @since 1.0.0
     */
    public function column_duration($item)
    {
        return video_central_human_time($item['duration']);
    }

    /**
     * Rating column.
     *
     * @param array $item
     *
     * @since 1.0.0
     */
    public function column_rating($item)
    {
        if (0 == $item['stats']['rating_count']) {
            return '-';
        }

        return number_format($item['stats']['rating'], 2).sprintf(__(' (%d votes)', 'video_central'), $item['stats']['rating_count']);
    }

    /**
     * Views column.
     *
     * @param array $item
     *
     * @since 1.0.0
     */
    public function column_views($item)
    {
        if (0 == $item['stats']['views']) {
            return '-';
        }

        return number_format($item['stats']['views'], 0, '.', ',');
    }

    /**
     * Date when the video was published.
     *
     * @param array $item
     */
    public function column_date($item)
    {
        $time = strtotime($item['published']);

        return date('M dS, Y @ H:i:s', $time);
    }

    /**
     * (non-PHPdoc).
     *
     * @see WP_List_Table::get_bulk_actions()
     */
    public function get_bulk_actions()
    {
        $actions = array(
            /*'import' => __('Import', 'video_central')*/
        );

        return $actions;
    }

    /**
     * Returns the columns of the table as specified.
     */
    public function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => __('Title',        'video_central'),
            'category' => __('Category',    'video_central'),
            'video_id' => __('Video ID',    'video_central'),
            'duration' => __('Duration',    'video_central'),
            'rating' => __('Rating',    'video_central'),
            'views' => __('Views',        'video_central'),
            'date' => __('Date',    'video_central'),
        );

        return $columns;
    }

    public function extra_tablenav($which)
    {
        $suffix = 'top' == $which ? '_top' : '2';

        $selected = false;

        if (isset($_GET['cat'])) {
            $selected = $_GET['cat'];
        }

        $args = array(
            'show_count' => true,
            'hide_empty' => 0,
            'taxonomy' => video_central_get_video_category_tax_id(),
            'name' => 'cat'.$suffix,
            'id' => 'video_central_categories'.$suffix,
            'selected' => $selected,
            'hide_if_empty' => true,
            'echo' => false,
            'orderby' => 'NAME',
        );

        if (video_central_import_categories()) {
            $args['show_option_all'] = __('Create categories from YouTube', 'video_central');
        } else {
            $args['show_option_all'] = __('Select category (optional)', 'video_central');
        }

        // get dropdown output
        $category_select = wp_dropdown_categories($args);

        ?>
        <select name="action<?php echo esc_attr( $suffix );
        ?>" id="action_<?php echo esc_attr( $which );
        ?>">
            <option value="-1"><?php _e('Bulk actions', 'video_central');
        ?></option>
            <option value="import" selected="selected"><?php _e('Import', 'video_central');
        ?></option>
        </select>

        <?php if ($category_select): ?>
            <label for="video_central_categories<?php echo esc_attr( $suffix );
        ?>"><?php _e('Import into category', 'video_central');
        ?> :</label>
            <?php echo $category_select;
        ?>
        <?php endif;
        ?>

        <?php submit_button( __( 'Apply', 'video_central'), 'action', false, false, array('id' => 'doaction'. esc_attr( $suffix ) ) );
    }

    /**
     * (non-PHPdoc).
     *
     * @see WP_List_Table::prepare_items()
     */
    public function prepare_items()
    {
        $per_page = video_central_import_results_per_page();
        $token = isset($_GET['token']) ? $_GET['token'] : '';

        switch ($_GET['video_central_feed']) {
            case 'user':
            case 'playlist':
            case 'channel':
                $args = array(
                    'type' => 'manual',
                    'query' => $_GET['video_central_query'],
                    'page_token' => $token,
                    'include_categories' => true,
                    'playlist_type' => $_GET['video_central_feed'],
                );

                $q = video_central_youtube_api_get_list($args);

                $videos = $q['videos'];
                $list_stats = $q['page_info'];
            break;
            // perform a search query
            case 'query':
                $args = array(
                    'query' => $_GET['video_central_query'],
                    'page_token' => $token,
                    'order' => $_GET['video_central_order'],
                    'duration' => $_GET['video_central_duration'],
                );
                $q = video_central_youtube_api_search_videos($args);
                $videos = $q['videos'];
                $list_stats = $q['page_info'];

            break;
        }

        $videos = $q['videos'];
        $list_stats = $q['page_info'];

        if (is_wp_error($videos)) {
            $this->feed_errors = $videos;
            $videos = array();
        }

        $this->items = $videos;
        $this->total_items = $list_stats['total_results'];
        $this->next_token = $list_stats['next_page'];
        $this->prev_token = $list_stats['prev_page'];

        $this->set_pagination_args(array(
            'total_items' => $this->total_items,
            'per_page' => $per_page,
            'total_pages' => ceil($this->total_items / $per_page),
        ));
    }

    /**
     * Displays a message if playlist is empty.
     *
     * @since 1.2.0
     */
    public function no_items()
    {
        _e('YouTube feed is empty.', 'video_central');
        if (is_wp_error($this->feed_errors)) {
            echo '<br />';
            printf(__(' <strong>API error (code: %s)</strong>: %s', 'video_central'), $this->feed_errors->get_error_code(), $this->feed_errors->get_error_message());
        }
    }

    /**
     * Display pagination.
     *
     * @since 1.2.0
     */
    protected function pagination($which)
    {
        $current_url = set_url_scheme('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
        $current_url = remove_query_arg(array('hotkeys_highlight_last', 'hotkeys_highlight_first'), $current_url);
        $disable_first = empty($this->prev_token) ? ' disabled' : false;
        $disable_last = empty($this->next_token) ? ' disabled' : false;

        $prev_page = sprintf("<a class='%s' title='%s' href='%s'>%s</a>",
            'prev-page'.$disable_first,
            esc_attr__('Go to the previous page', 'video_central'),
            esc_url(add_query_arg('token', $this->prev_token, $current_url)),
            '&lsaquo;'
        );

        ?>
        <div class="tablenav-pages">

            <span class="displaying-num"><?php printf(_n('1 item', '%s items', $this->total_items), number_format_i18n($this->total_items));
        ?></span>
            <span class="pagination-links">
                <?php
                    // prev page
                    printf("<a class='%s' title='%s' href='%s'>%s</a>",
                        'prev-page'.$disable_first,
                        esc_attr__('Go to the previous page', 'video_central'),
                        esc_url(add_query_arg('token', $this->prev_token, $current_url)),
                        '&lsaquo;'
                    );

                    // next page
                    printf("<a class='%s' title='%s' href='%s'>%s</a>",
                        'prev-page'.$disable_last,
                        esc_attr__('Go to the next page', 'video_central'),
                        esc_url(add_query_arg('token', $this->next_token, $current_url)),
                        '&rsaquo;'
                    );
        ?>
            </span>
        </div>

        <?php

    }
}
