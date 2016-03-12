<?php

/*-----------------------------------------------------------------------------------*
* Radium Image Resize Based on Aqua Resizer https://github.com/sy4mil/Aqua-Resizer

* Title     : Aqua Resizer
* Description   : Resizes WordPress images on the fly
* Version   : 1.1.6
* Author    : Syamil MJ
* Author URI    : http://aquagraphite.com
* License   : WTFPL - http://sam.zoy.org/wtfpl/
* Documentation : https://github.com/sy4mil/Aqua-Resizer/
*
* @param string $url - (required) must be uploaded using wp media uploader
* @param int $width - (required)
* @param int $height - (optional)
* @param bool $crop - (optional) default to soft crop
* @param bool $single - (optional) returns an array if false
*
* @return str|array
/*-----------------------------------------------------------------------------------*/

function video_central_resize($url, $width, $height = null, $crop = null, $single = true, $quality = 100, $retina = false)
{
    if ($retina) {
        $width = ($width * 2);

        $height = isset($height) ?  ($height * 2) : null;
    }

    //validate inputs
    if (!$url || !$width) {
        return false;
    }

    //define upload path & dir
    $upload_info = wp_upload_dir();
    $upload_dir = $upload_info['basedir'];
    $upload_url = $upload_info['baseurl'];

    $http_prefix = 'http://';
    $https_prefix = 'https://';

    /* if the $url scheme differs from $upload_url scheme, make them match
       if the schemes differe, images don't show up. */
    if (!strncmp($url, $https_prefix, strlen($https_prefix))) { //if url begins with https:// make $upload_url begin with https:// as well
        $upload_url = str_replace($http_prefix, $https_prefix, $upload_url);
    } elseif (!strncmp($url, $http_prefix, strlen($http_prefix))) { //if url begins with http:// make $upload_url begin with http:// as well
        $upload_url = str_replace($https_prefix, $http_prefix, $upload_url);
    }

    //check if $img_url is local
    if (strpos($url, $upload_url) === false) {
        return false;
    }

    //define path of image
    $rel_path = str_replace($upload_url, '', $url);
    $img_path = $upload_dir.$rel_path;

    //check if img path exists, and is an image indeed
    if (!file_exists($img_path) || !getimagesize($img_path)) {
        return false;
    }

    //get image info
    $info = pathinfo($img_path);
    $ext = $info['extension'];
    list($orig_w, $orig_h) = getimagesize($img_path);

    //get image size after cropping
    $dims = image_resize_dimensions($orig_w, $orig_h, $width, $height, $crop);
    $dst_w = $dims[4];
    $dst_h = $dims[5];

    //use this to check if cropped image already exists, so we can return that instead
    $suffix = "{$dst_w}x{$dst_h}";
    $dst_rel_path = str_replace('.'.$ext, '', $rel_path);
    $destfilename = "{$upload_dir}{$dst_rel_path}-{$suffix}.{$ext}";

    if (!$dst_h) {
        //can't resize, so return original url
        $img_url = $url;
        $dst_w = $orig_w;
        $dst_h = $orig_h;
    }
    //else check if cache exists
    elseif (file_exists($destfilename) && getimagesize($destfilename)) {
        $img_url = "{$upload_url}{$dst_rel_path}-{$suffix}.{$ext}";
    }
    //else, we resize the image and return the new resized image url
    else {

        // Note: This pre-3.5 fallback check will edited out in subsequent version
        if (function_exists('wp_get_image_editor')) {
            $editor = wp_get_image_editor($img_path);

            if (is_wp_error($editor) || is_wp_error($editor->resize($width, $height, $crop))) {
                return false;
            }

            if ($quality) {
                $editor->set_quality($quality);
            }
            $resized_file = $editor->save();

            if (!is_wp_error($resized_file)) {
                $resized_rel_path = str_replace($upload_dir, '', $resized_file['path']);
                $img_url = $upload_url.$resized_rel_path;
            } else {
                return false;
            }
        } else {
            $resized_img_path = image_resize($img_path, $width, $height, $crop); // Fallback foo
            if (!is_wp_error($resized_img_path)) {
                $resized_rel_path = str_replace($upload_dir, '', $resized_img_path);
                $img_url = $upload_url.$resized_rel_path;
            } else {
                return false;
            }
        }
    }

    //return the output
    if ($single) {
        //str return
        $image = $img_url;
    } else {
        //array return
        $image = array(
            0 => $img_url,
            1 => $dst_w,
            2 => $dst_h,
        );
    }

    // RETINA Support --------------------------------------------------------------->
    // Thanks to @wpexplorer
    // https://github.com/syamilmj/Aqua-Resizer/issues/36
    $retina_w = $dst_w * 2;
    $retina_h = $dst_h * 2;

    //get image size after cropping
    $dims_x2 = image_resize_dimensions($orig_w, $orig_h, $retina_w, $retina_h, $crop);
    $dst_x2_h = $dims_x2[5];

    // If possible lets make the @2x image
    if ($dst_x2_h) {

        //@2x image url
        $destfilename = "{$upload_dir}{$dst_rel_path}-{$suffix}@2x.{$ext}";

        //check if retina image exists
        if (file_exists($destfilename) && getimagesize($destfilename)) {
            // already exists, do nothing
        } else {
            // doesnt exist, lets create it
            $editor = wp_get_image_editor($img_path);

            if (!is_wp_error($editor)) {
                $editor->resize($retina_w, $retina_h, $crop);
                $editor->set_quality(100);
                $filename = $editor->generate_filename($dst_w.'x'.$dst_h.'@2x');
                $editor = $editor->save($filename);
            }
        }
    } //end retina

    return $image;
}
