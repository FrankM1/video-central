<?php
/**
 * Video Central Common Functions.
 */

/** Formatting ****************************************************************/

/**
 * A method of formatting numeric values.
 *
 * @since 1.0.0
 *
 * @param string $number   Number to format
 * @param string $decimals Optional. Display decimals
 *
 * @uses apply_filters() Calls 'video_central_number_format' with the formatted values,
 *                        number and display decimals bool
 *
 * @return string Formatted string
 */
function video_central_number_format($number = 0, $decimals = false, $dec_point = '.', $thousands_sep = ',')
{

    // If empty, set $number to (int) 0
    if (!is_numeric($number)) {
        $number = 0;
    }

    return apply_filters(__FUNCTION__, number_format($number, $decimals, $dec_point, $thousands_sep), $number, $decimals, $dec_point, $thousands_sep);
}

/**
 * Creates from a number of given seconds a readable duration ( HH:MM:SS ).
 *
 * @since 1.0.0
 *
 * @param int $seconds
 */
function video_central_human_time($seconds)
{
    $seconds = absint($seconds);

    if ($seconds < 0) {
        return;
    }

    $h = floor($seconds / 3600);
    $m = floor($seconds % 3600 / 60);
    $s = floor($seconds % 3600 % 60);

    return apply_filters(__FUNCTION__, (($h > 0 ? $h.':' : '').($m > 0 ? ($h > 0 && $m < 10 ? '0' : '').$m.':' : '0:').($s < 10 ? '0' : '').$s), $seconds);
}

/**
 * Convert video seconds to minutes.
 *
 * @since 1.0.0
 *
 * @param int $seconds time in seconds
 *
 * @return string Time in minutes
 */
function video_central_sec_to_time($seconds)
{
    $output = null;

    $hours = intval(intval($seconds) / 3600);
    $minutes = intval(($seconds / 60) % 60);
    $seconds = intval($seconds % 60);

    if ($hours != 0) {
        $output .=  $hours.':';
        $output .=  str_pad($minutes, 2, '0', STR_PAD_LEFT).':';
        $output .= str_pad($seconds, 2, '0', STR_PAD_LEFT);
    } else {
        $output .= $minutes.':';
        $output .= str_pad($seconds, 2, '0', STR_PAD_LEFT);
    }

    return apply_filters(__FUNCTION__, $output);
}

/**
 * Output formatted time to display human readable time difference.
 *
 * @since 1.0.0
 *
 * @param string $older_date Unix timestamp from which the difference begins.
 * @param string $newer_date Optional. Unix timestamp from which the
 *                           difference ends. False for current time.
 * @param int    $gmt        Optional. Whether to use GMT timezone. Default is false.
 *
 * @uses video_central_get_time_since() To get the formatted time
 */
function video_central_time_since($older_date, $newer_date = false, $gmt = false)
{
    echo video_central_get_time_since($older_date, $newer_date, $gmt);
}
    /**
     * Return formatted time to display human readable time difference.
     *
     * @since 1.0.0
     *
     * @param string $older_date Unix timestamp from which the difference begins.
     * @param string $newer_date Optional. Unix timestamp from which the
     *                           difference ends. False for current time.
     * @param int    $gmt        Optional. Whether to use GMT timezone. Default is false.
     *
     * @uses current_time() To get the current time in mysql format
     * @uses human_time_diff() To get the time differene in since format
     * @uses apply_filters() Calls 'video_central_get_time_since' with the time
     *                        difference and time
     *
     * @return string Formatted time
     */
    function video_central_get_time_since($older_date, $newer_date = false, $gmt = false)
    {

        // Setup the strings
        $unknown_text = apply_filters('video_central_core_time_since_unknown_text',   __('sometime',  'video_central'));
        $right_now_text = apply_filters('video_central_core_time_since_right_now_text', __('right now', 'video_central'));
        $ago_text = apply_filters('video_central_core_time_since_ago_text',       __('%s ago',    'video_central'));

        // array of time period chunks
        $chunks = array(
            array(60 * 60 * 24 * 365 , __('year',   'video_central'), __('years',   'video_central')),
            array(60 * 60 * 24 * 30 ,  __('month',  'video_central'), __('months',  'video_central')),
            array(60 * 60 * 24 * 7,    __('week',   'video_central'), __('weeks',   'video_central')),
            array(60 * 60 * 24 ,       __('day',    'video_central'), __('days',    'video_central')),
            array(60 * 60 ,            __('hour',   'video_central'), __('hours',   'video_central')),
            array(60 ,                 __('minute', 'video_central'), __('minutes', 'video_central')),
            array(1,                   __('second', 'video_central'), __('seconds', 'video_central')),
        );

        if (!empty($older_date) && !is_numeric($older_date)) {
            $time_chunks = explode(':', str_replace(' ', ':', $older_date));
            $date_chunks = explode('-', str_replace(' ', '-', $older_date));
            $older_date = gmmktime((int) $time_chunks[1], (int) $time_chunks[2], (int) $time_chunks[3], (int) $date_chunks[1], (int) $date_chunks[2], (int) $date_chunks[0]);
        }

        // $newer_date will equal false if we want to know the time elapsed
        // between a date and the current time. $newer_date will have a value if
        // we want to work out time elapsed between two known dates.
        $newer_date = (!$newer_date) ? strtotime(current_time('mysql', $gmt)) : $newer_date;

        // Difference in seconds
        $since = $newer_date - $older_date;

        // Something went wrong with date calculation and we ended up with a negative date.
        if (0 > $since) {
            $output = $unknown_text;

        // We only want to output two chunks of time here, eg:
        //     x years, xx months
        //     x days, xx hours
        // so there's only two bits of calculation below:
        } else {

            // Step one: the first chunk
            for ($i = 0, $j = count($chunks); $i < $j; ++$i) {
                $seconds = $chunks[$i][0];

                // Finding the biggest chunk (if the chunk fits, break)
                $count = floor($since / $seconds);
                if (0 != $count) {
                    break;
                }
            }

            // If $i iterates all the way to $j, then the event happened 0 seconds ago
            if (!isset($chunks[$i])) {
                $output = $right_now_text;
            } else {

                // Set output var
                $output = (1 == $count) ? '1 '.$chunks[$i][1] : $count.' '.$chunks[$i][2];

                // Step two: the second chunk
                if ($i + 2 < $j) {
                    $seconds2 = $chunks[$i + 1][0];
                    $name2 = $chunks[$i + 1][1];
                    $count2 = floor(($since - ($seconds * $count)) / $seconds2);

                    // Add to output var
                    if (0 != $count2) {
                        $output .= (1 == $count2) ? _x(',', 'Separator in time since', 'video_central').' 1 '.$name2 : _x(',', 'Separator in time since', 'video_central').' '.$count2.' '.$chunks[$i + 1][2];
                    }
                }

                // No output, so happened right now
                if (!(int) trim($output)) {
                    $output = $right_now_text;
                }
            }
        }

        // Append 'ago' to the end of time-since if not 'right now'
        if ($output != $right_now_text) {
            $output = sprintf($ago_text, $output);
        }

        return apply_filters(__FUNCTION__, $output, $older_date, $newer_date);
    }

/**
 * video_central_truncate_text truncate text and add ellipsis.
 *
 * @since 1.0.0
 *
 * @return string shortened text
 */
function video_central_truncate_text($string, $length = 100, $append = '&hellip;')
{
    $string = trim($string);

    if (strlen($string) > $length) {
        $string = wordwrap($string, $length);
        $string = explode("\n", $string);
        $string = array_shift($string).$append;
    }

    return apply_filters(__FUNCTION__, $string, $length, $append);
}

/**
 * Detect and make links in description clickable.
 *
 * @since 1.0.0
 *
 * @param string $content
 *
 * @return string $content
 */
function video_central_url_make_clickable($content, $args = array())
{
    $attribs = null;

    // Parse arguments against default values
    $r = video_central_parse_args($args, array(
        'nofollow' => true,
        'enable_target' => true,
        'target' => 'blank',
    ), 'url_make_clickable');

    if ( $r['nofollow'] ) {
        $attribs = ' rel="nofollow"';
    }
    if ($r['enable_target']) {
        $attribs .= ' target="'.$r['target'].'"';
    }

    // in testing, using arrays here was found to be faster
    $content = preg_replace(
        array(
            '#([\s>])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is',
            '#([\s>])((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*)#is',
            '#([\s>])([a-z0-9\-_.]+)@([^,< \n\r]+)#i', ),
        array(
            '$1<a href="$2"'.$attribs.'>$2</a>',
            '$1<a href="http://$2"'.$attribs.'>$2</a>',
            '$1<a href="mailto:$2@$3">$2@$3</a>', ),
        $content);
    // this one is not in an array because we need it to run last, for cleanup of accidental links within links
    $content = preg_replace('#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i', '$1$3</a>', $content);
    $content = trim($content);

    return apply_filters(__FUNCTION__, $content, $args);
}

/**
 * video_central_get_current_user_role Get current user role.
 *
 * @since 1.0.0
 *
 * @return string current user role
 */
function video_central_get_current_user_role()
{
    global $current_user;

    $user_roles = $current_user->roles;

    $user_role = array_shift($user_roles);

    return $user_role;
}

/**
 * Assist pagination by returning correct page number.
 *
 * @since 1.0.0
 *
 * @uses get_query_var() To get the 'paged' value
 *
 * @return int Current page number
 */
function video_central_get_paged()
{
    global $wp_query;

    // Check the query var
    if (get_query_var('paged')) {
        $paged = get_query_var('paged');

    // Check query paged
    } elseif (!empty($wp_query->query['paged'])) {
        $paged = $wp_query->query['paged'];
    }

    // Paged found
    if (!empty($paged)) {
        return (int) $paged;
    }

    // Default to first page
    return 1;
}

/** Queries *******************************************************************/

/**
 * Merge user defined arguments into defaults array.
 *
 * This function is used throughout the plugin to allow for either a string or array
 * to be merged into another array. It is identical to wp_parse_args() except
 * it allows for arguments to be passively or aggressively filtered using the
 * optional $filter_key parameter.
 *
 * @since 1.0.0
 *
 * @param string|array $args       Value to merge with $defaults
 * @param array        $defaults   Array that serves as the defaults.
 * @param string       $filter_key String to key the filters from
 *
 * @return array Merged user defined values with defaults.
 */
function video_central_parse_args($args, $defaults = array(), $filter_key = '')
{

    // Setup a temporary array from $args
    if (is_object($args)) {
        $r = get_object_vars($args);
    } elseif (is_array($args)) {
        $r = &$args;
    } else {
        wp_parse_str($args, $r);
    }

    // Passively filter the args before the parse
    if (!empty($filter_key)) {
        $r = apply_filters('video_central_before_'.$filter_key.'_parse_args', $r);
    }

    // Parse
    if (is_array($defaults) && !empty($defaults)) {
        $r = array_merge($defaults, $r);
    }

    // Aggressively filter the args after the parse
    if (!empty($filter_key)) {
        $r = apply_filters('video_central_after_'.$filter_key.'_parse_args', $r);
    }

    // Return the parsed results
    return $r;
}

/** Templates ******************************************************************/

/**
 * Used to guess if page exists at requested path.
 *
 * @since 1.0.0
 *
 * @uses get_option() To see if pretty permalinks are enabled
 * @uses get_page_by_path() To see if page exists at path
 *
 * @param string $path
 *
 * @return mixed False if no page, Page object if true
 */
function video_central_get_page_by_path($path = '')
{

    // Default to false
    $retval = false;

    // Path is not empty
    if (!empty($path)) {

        // Pretty permalinks are on so path might exist
        if (get_option('permalink_structure')) {
            $retval = get_page_by_path($path);
        }
    }

    return apply_filters(__FUNCTION__, $retval, $path);
}

/**
 * Sets the 404 status.
 *
 * Used primarily with hidden videos.
 *
 * @since 1.0.0
 *
 * @global WP_Query $wp_query
 *
 * @uses WP_Query::set_404()
 */
function video_central_set_404()
{
    global $wp_query;

    if (!isset($wp_query)) {
        _doing_it_wrong(__FUNCTION__, __('Conditional query tags do not work before the query is run. Before then, they always return false.', 'video_central'), '3.1');

        return false;
    }

    $wp_query->set_404();
}

/** Statistics ****************************************************************/

/**
 * Log page views.
 *
 * @since 1.0.0
 */
function video_central_log_hit_stat($video_id)
{
}

/**
 * video_central_video_views function to display number of posts.
 *
 * @param int $video_id Video id
 */
function video_central_video_views($video_id = 0)
{
    $count = video_central_get_video_views($video_id);

    if ($count == '') {
        echo 0;

        return;
    }

    echo esc_html( $count );
}

    /**
     * [video_central_get_video_views description].
     *
     * @param [type] $video_id [description]
     *
     * @return [type] [description]
     */
    function video_central_get_video_views($video_id = 0)
    {
        if (!is_admin()) {
            $video_id = video_central_get_video_id();
        }

        $count_key = '_video_central_video_views_count';

        $count = get_post_meta($video_id, $count_key, true);

        if ($count == '') {
            delete_post_meta($video_id, $count_key);

            add_post_meta($video_id, $count_key, '0');

            return '0';
        }

        return $count;
    }

/**
 * [video_central_set_video_views description].
 *
 * @since 1.0.0
 *
 * @param [type] $video_id [description]
 *
 * @return [type] [description]
 */
function video_central_set_video_views($video_id = 0)
{
    if (video_central_get_current_user_role() == 'administrator') {
        return;
    }

    $video_id = video_central_get_video_id();

    $count_key = '_video_central_video_views_count';

    $count = get_post_meta($video_id, $count_key, true);

    if ($count == '') {
        $count = 0;

        delete_post_meta($video_id, $count_key);

        add_post_meta($video_id, $count_key, '0');
    } else {
        ++$count;

        update_post_meta($video_id, $count_key, $count);
    }
}
