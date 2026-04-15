<?php

class Video_Central_Likes_Ajax
{
    /**
     * Initializes the AJAX functions with the wp_ajax and wp_ajax_nopriv
     * (no privileges) hooks.
     *
     * @since 1.0.0
     */
    public function __construct() {
        if ( ! video_central_allow_likes()) {
            return;
        }

        add_action('wp_ajax_like',                array(&$this, 'like'));
        add_action('wp_ajax_nopriv_like',            array(&$this, 'like'));

        add_action('wp_ajax_unlike',                array(&$this, 'unlike'));
        add_action('wp_ajax_nopriv_unlike',        array(&$this, 'unlike'));

        add_action('wp_ajax_likes_count',            array(&$this, 'likes_count'));
        add_action('wp_ajax_nopriv_likes_count',    array(&$this, 'likes_count'));
    }

    /**
     * Validate post ID from request: must be an integer and a valid video post.
     *
     * @since 1.3.2
     *
     * @param mixed $raw_id Raw POST value.
     * @return int|false Sanitized post ID or false on failure.
     */
    private function validate_video_id( $raw_id ) {
        $id = absint( $raw_id );

        if ( ! $id ) {
            return false;
        }

        if ( get_post_type( $id ) !== video_central_get_video_post_type() ) {
            return false;
        }

        return $id;
    }

    /**
     * Fired when a like is triggered for incrementing the post's like count.
     *
     * @since 1.0.0
     *
     * Expects:
     * $_POST['nonce'] - WP Security Nonce
     * $_POST['id']		 - post id of target post
     *
     * Returns:
     * success 	- boolean
     * time 		- int; time of response
     * count 		- int; likes count
     * message 	- string; short message of error / success
     */
    public function like()
    {
        header('Content-Type: application/json');

        if (!isset($_POST['nonce']) ||
                !wp_verify_nonce($_POST['nonce'], 'likes-nonce')) {
            wp_die(json_encode(array(
                'success' => false,
                'time' => time(),
                'count' => 0,
                'message' => 'invalid nonce',
            )));
        }

        $id = $this->validate_video_id( isset( $_POST['id'] ) ? $_POST['id'] : 0 );
        if ( ! $id ) {
            wp_die(json_encode(array(
                'success' => false,
                'time' => time(),
                'count' => 0,
                'message' => 'invalid post',
            )));
        }

        // increment like count in database
        $count = get_post_meta($id, '_video_central_video_likes_count', true);
        if (empty($count) || $count < 0) {
            $count = 0;
        }
        update_post_meta($id, '_video_central_video_likes_count', ++$count);

        // return results
        echo json_encode(array(
            'success' => true,
            'time' => time(),
            'count' => $count,
            'message' => 'count incremented',
        ));
        wp_die();
    }

    /**
     * Fired when an complete like is clicked for decrementing the post's like
     * count.
     *
     * @since 1.0.0
     *
     * Expects:
     * $_POST['nonce'] - WP Security Nonce
     * $_POST['id']		 - post id of target post
     *
     * Returns:
     * success 	- boolean
     * time 		- int; time of response
     * count 		- int; likes count
     * message 	- string; short message of error / success
     */
    public function unlike()
    {
        header('Content-Type: application/json');

        if (!isset($_POST['nonce']) ||
                !wp_verify_nonce($_POST['nonce'], 'likes-nonce')) {
            wp_die(json_encode(array(
                'success' => false,
                'time' => time(),
                'count' => 0,
                'message' => 'invalid nonce',
            )));
        }

        $id = $this->validate_video_id( isset( $_POST['id'] ) ? $_POST['id'] : 0 );
        if ( ! $id ) {
            wp_die(json_encode(array(
                'success' => false,
                'time' => time(),
                'count' => 0,
                'message' => 'invalid post',
            )));
        }

        // decrement like count in database
        $count = get_post_meta($id, '_video_central_video_likes_count', true);
        if (empty($count) || $count <= 0) {
            $count = 1;
        }
        if ($count == 1) {
            delete_post_meta($id, '_video_central_video_likes_count');
        } else {
            update_post_meta($id, '_video_central_video_likes_count', --$count);
        }

        // return results
        echo json_encode(array(
            'success' => true,
            'time' => time(),
            'count' => $count,
            'message' => 'count decremented',
        ));
        wp_die();
    }

    /**
     * Used for auto updating like counts without reloading the page.
     *
     * @since 1.0.0
     *
     * Expects:
     * $_POST['nonce']
     * $_POST['ids']
     *
     * Returns
     * success 	- boolean
     * time 		- int; time of response
     * counts 	- assoc int array; post_id => count pairs
     * message 	- string; short message of error / success
     */
    public function likes_count()
    {
        header('Content-Type: application/json');

        // check nonce
        if (!isset($_POST['nonce']) ||
                !wp_verify_nonce($_POST['nonce'], 'likes-nonce')) {
            wp_die(json_encode(array(
                'success' => false,
                'message' => 'invalid nonce',
            )));
        }

        $counts = array();
        if ( isset( $_POST['ids'] ) && is_array( $_POST['ids'] ) ) {
            foreach ($_POST['ids'] as $raw_id) {
                $id = absint( $raw_id );
                if ( $id ) {
                    $counts[ $id ] = get_video_central_likes_count( $id );
                }
            }
        }

        // return results
        echo json_encode(array(
            'success' => true,
            'time' => time(),
            'counts' => $counts,
        ));
        wp_die();
    }
}
