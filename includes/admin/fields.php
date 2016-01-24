<?php

/** FIELDS **/

/**
 * Displays checked argument in checkbox.
 *
 * @param bool $val
 * @param bool $echo
 */
function video_central_check($val, $echo = true)
{
    $checked = '';
    if (is_bool($val) && $val) {
        $checked = ' checked="checked"';
    }
    if ($echo) {
        echo $checked;
    } else {
        return $checked;
    }
}

/**
 * Display select box.
 *
 * @param array $args - see $defaults in function
 * @param bool  $echo
 */
function video_central_select($args = array(), $echo = true)
{
    $defaults = array(
        'options' => array(),
        'name' => false,
        'id' => false,
        'class' => '',
        'selected' => false,
        'use_keys' => true,
    );

    $o = wp_parse_args($args, $defaults);

    if (!$o['id']) {
        $output = sprintf('<select name="%1$s" id="%1$s" class="%2$s">', $o['name'], $o['class']);
    } else {
        $output = sprintf('<select name="%1$s" id="%2$s" class="%3$s">', $o['name'], $o['id'], $o['class']);
    }

    foreach ($o['options'] as $val => $text) {
        $opt = '<option value="%1$s"%2$s>%3$s</option>';

        $value = $o['use_keys'] ? $val : $text;
        $c = $o['use_keys'] ? $val == $o['selected'] : $text == $o['selected'];
        $checked = $c ? ' selected="selected"' : '';
        $output .= sprintf($opt, $value, $checked, $text);
    }

    $output .= '</select>';

    if ($echo) {
        echo $output;
    }

    return $output;
}
