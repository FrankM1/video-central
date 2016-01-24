<?php

/**
 * Are video likes allowed.
 *
 * @since 1.0.0
 *
 * @param $default bool Optional. Default value true
 *
 * @uses get_option() To get the allow tags
 *
 * @return bool Are tags allowed?
 */
function video_central_allow_likes($default = 1)
{
    return (bool) apply_filters(__FUNCTION__, (bool) get_option('_video_central_allow_likes', $default));
}

/**
 * Returns the count of likes for a given post (by post_id).
 *
 * @since 1.0.0
 *
 * @param int $post_id id of post to retrieve like count for
 *
 * @return int like count
 */
function get_video_central_likes_count($video_id = 0)
{
    if (!video_central_allow_likes()) {
        return;
    }

    $video_id = video_central_get_video_id($video_id);

    $output = get_post_meta($video_id, '_video_central_video_likes_count', true);

    if (!is_numeric($output)) {
        $output = 0;
    }

    return apply_filters(__FUNCTION__, $output);
}

/**
 * Returns the count of likes for a given post (by post id) as used in get_likes
 * and as required for updating count on like, unlike and auto ajax refresh.
 *
 * @since 1.0.0
 *
 * @param int    $post_id id of post to retrieve like count for
 * @param string $text    text displayed below count, empty to hide
 *
 * @return string like-meta html code including current count
 */
function video_central_get_likes_count($video_id = 0)
{
    if (!video_central_allow_likes()) {
        return;
    }

    $video_id = video_central_get_video_id($video_id);

    $attr = video_central_parse_args(array(
        'counter' => true,
        'style' => '',
        'class' => '',
        'text' => 'likes',
        'hover' => 'Don\'t Move',
    ), 'get_likes_count');

    extract($attr);

    $metaclass = $beta = null;

    if (!empty($hover)) {
        $beta = '<div class="like-meta-beta like-dontmove"><span>'.str_replace('//', '<br />', esc_html($hover)).'</span></div>'."\n\t\t";
        $metaclass = ' like-hideonhover';
    }

    $output = '<div class="like-meta like-meta-'.$video_id.'">'."\n\t\t\t";
    $output .= '<div class="like-meta-alpha'.$metaclass.'">'."\n\t\t\t\t";
    $output .= '<span class="like-count">'.get_video_central_likes_count($video_id).'</span>'."\n\t\t\t";

    if (!empty($text)) {
        $output .= "\t".'<span class="like-text">'.str_replace('//', '<br />', esc_html($text)).'</span>'."\n\t\t\t";
    }

    $output .= '</div>'."\n\t\t\t";
    $output .= $beta;
    $output .= '</div>'."\n\t";

    return apply_filters(__FUNCTION__, $output);
}

 /**
  * Output likes html.
  *
  * @since 1.0.0
  *
  * @uses video_central_get_likes()
  */
 function video_central_likes($video_id = 0, $attr = array())
 {
     echo video_central_get_likes($video_id, $attr);
 }
    /**
     * the like html code for a specific post for echoing.
     *
     * @since 1.0.0
     *
     * @param int  $video_id id of post to retrieve like for
     * @param bool $counter  if counter should be printed as well
     *
     * @return string like html code
     */
    function video_central_get_likes($video_id = 0, $attr = array())
    {
        if (!video_central_allow_likes()) {
            return;
        }

        $attr = video_central_parse_args($attr, array(
            'counter' => true,
        ), 'get_likes');

        extract($attr);

        if (!video_central_allow_likes()) {
            return;
        }

        //	$class, $style, $counter, $text, $hover
        $video_id = video_central_get_video_id($video_id);

        $style = isset($style) && !empty($style) ? trim($style) : '';
        $class = isset($class) && !empty($class) ? ' '.trim($class) : '';
        $css = !empty($style) ? ' style="'.esc_html($style).'"' : '';

        $output = '<div class="like-box'.esc_html($class).'"'.$css.'>'."\n\t";
        $output .= '<figure class="like likeable" data-id="'.$video_id.'">'."\n\t\t";

        $output .= '<a class="like-object">'."\n\t\t\t";
        $output .= '<div class="like-opening"><div class="like-circle">&nbsp;</div></div>'."\n\t\t";
        $output .= '</a>'."\n\t\t";

        if ($counter) {
            $output .= video_central_get_likes_count($video_id);
        }

        $output .= '</figure>'."\n";
        $output .= '</div>'."\n\n";

        return apply_filters(__FUNCTION__, $output);
    }
