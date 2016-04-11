<?php

class Video_Central_Vimeo_Importer_ListTable extends WP_List_Table
{
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
            'view' => sprintf('<a href="http://www.vimeo.com/watch?v=%1$s" target="_video_central_vimeo_open">%2$s</a>', $item['video_id'], __('View on Vimeo', 'video_central')),
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
    public function column_published($item)
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
            'uploader' => __('Uploader',    'video_central'),
            'duration' => __('Duration',    'video_central'),
            'rating' => __('Rating',    'video_central'),
            'views' => __('Views',        'video_central'),
            'published' => __('Published',    'video_central'),
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
            'show_count' => 1,
            'hide_empty' => 0,
            'taxonomy' => 'videos',
            'name' => 'cat'.$suffix,
            'id' => 'video_central_categories'.$suffix,
            'selected' => $selected,
            'hide_if_empty' => true,
            'echo' => false,
        );

        if (video_central_import_categories()) {
            $args['show_option_all'] = __('Create categories from Vimeo', 'video_central');
        } else {
            $args['show_option_all'] = __('Select category (optional)', 'video_central');
        }

        // get dropdown output
        $categ_select = wp_dropdown_categories($args);
        ?>
        <select name="action<?php echo esc_attr( $suffix );
        ?>" id="action_<?php echo esc_attr( $which );
        ?>">
            <option value="-1"><?php _e('Bulk actions', 'video_central');
        ?></option>
            <option value="import"><?php _e('Import', 'video_central');
        ?></option>
        </select>

        <?php if ($categ_select): ?>
        <label for="video_central_categories<?php echo esc_attr( $suffix );
        ?>"><?php _e('Import into category', 'video_central');
        ?> :</label>
        <?php echo $categ_select;
        ?>
        <?php endif;
        ?>

        <?php submit_button( __('Apply', 'video_central'), 'action', false, false, array( 'id' => 'doaction'. esc_attr( $suffix ) ) );
        ?>
        <span class="video-central-ajax-response"></span>
        <?php

    }

    /**
     * (non-PHPdoc).
     *
     * @see WP_List_Table::prepare_items()
     */
    public function prepare_items()
    {
        $per_page = 10;
        $total_items = (int) $_GET['video_central_results'];
        $current_page = $this->get_pagenum();

        $args = array(
            'source' => $_GET['video_central_source'],
            'feed' => $_GET['video_central_feed'],
            'query' => $_GET['video_central_query'],
            'order' => $_GET['video_central_order'],
            'duration' => isset($_GET['video_central_duration']) ? $_GET['video_central_duration'] : '',
            'results' => $per_page,
            'start-index' => ($current_page - 1) * $per_page + 1,
        );

        $import = new Video_Central_Vimeo_ImporterData($args);

        $videos = $import->get_feed();

        $total_yt_items = $import->get_total_items();

        if ($total_yt_items < $total_items) {
            $total_items = $total_yt_items;
        }

        $this->items = $videos;

        $this->set_pagination_args(array(
            'total_items' => $total_items,
            'per_page' => $per_page,
            'total_pages' => ceil($total_items / $per_page),
        ));
    }
}
