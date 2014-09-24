<?php

/*
 * SOCIAL SHARE COUNTERS
 */

/**
 * [video_central_get_tweets Twitter Share Counter]
 *
 * @since 1.0.0
 *
 * @param  [type] $url [description]
 * @return [type]      [description]
 */
function video_central_get_tweets($url, $cache_time) {

    $transient_name = md5('twitter_' . $url);

    if ($cache_time == 0) {
        delete_transient($transient_name);
    }
    if ($cache_time == '') {
        $cache_time = 15;
    }
    
    if (false === ( $trans = get_transient($transient_name) )) {
        
        $get_link = wp_remote_get('http://urls.api.twitter.com/1/urls/count.json?url=' . $url);
        
        if (is_wp_error($get_link)) {
        
            return 0;
        
        } else {
            $twitter_count = json_decode($get_link['body'], true);
            set_transient($transient_name, intval($twitter_count['count']), $cache_time * 60);
            return intval($twitter_count['count']);
        }
        
    } else {
        
        return get_transient($transient_name);
    
    }
}


/**
 * [video_central_get_plusones Google plus Share Counter]
 *
 * @since 1.0.0
 *
 * @param  [type] $url [description]
 * @return [type]      [description]
 */
function video_central_get_plusones($ur, $cache_time) {

    $transient_name = md5('google_plus_' . $url);

    if ($cache_time == 0) {
        delete_transient($transient_name);
    }
    if ($cache_time == '') {
        $cache_time = 15;
    }

    if (false === ( $trans = get_transient($transient_name) )) {

        $args = array(
            'method' => 'POST',
            'headers' => array(
                'Content-Type' => 'application/json'
            ),
            'timeout' => 30,
            'redirection' => 1,
            'body' => json_encode(array(
                'method' => 'pos.plusones.get',
                'id' => 'p',
                'method' => 'pos.plusones.get',
                'jsonrpc' => '2.0',
                'key' => 'p',
                'apiVersion' => 'v1',
                'params' => array(
                    'nolog' => true,
                    'id' => $url,
                    'source' => 'widget',
                    'userId' => '@viewer',
                    'groupId' => '@self'
                )
            )),
            'sslverify' => false
        );

        $json_string = wp_remote_post("https://clients6.google.com/rpc", $args);

        if (is_wp_error($json_string)) {
            return 0;
        } else {
            $json = json_decode($json_string['body'], true);
            set_transient($transient_name, intval($json['result']['metadata']['globalCounts']['count']), $cache_time * 60);
            return intval($json['result']['metadata']['globalCounts']['count']);
        }
    } else {
        return get_transient($transient_name);
    }
}

/**
 * [video_central_get_stumbleupon Stumbleupon Share Counter]
 *
 * @since 1.0.0
 *
 * @param  [type] $url [description]
 * @return [type]      [description]
 */
function video_central_get_stumbleupon($url, $cache_time) {

    $transient_name = md5('stumbleupon_' . $url);

    if ($cache_time == 0) {
        delete_transient($transient_name);
    }
    if ($cache_time == '') {
        $cache_time = 15;
    }

    if (false === ( $trans = get_transient($transient_name) )) {
        $get_link = wp_remote_get('http://www.stumbleupon.com/services/1.01/badge.getinfo?url=' . $url);
        if (is_wp_error($get_link)) {
            return 0;
        } else {
            $stumbleupon_count = json_decode($get_link['body'], true);
            if (@$stumbleupon_count['result']['views'] == '') {
                return 0;
            } else {
                set_transient($transient_name, intval($stumbleupon_count['result']['views']), $cache_time * 60);
                return intval($stumbleupon_count['result']['views']);
            }
        }
    } else {
        return get_transient($transient_name);
    }
}


/**
 * [video_central_get_linkedin Linkedin Share Counter]
 *
 * @since 1.0.0
 *
 * @param  [type] $url [description]
 * @return [type]      [description]
 */
function video_central_get_linkedin($url, $cache_time) {

    $transient_name = md5('linkedin_' . $url);

    if ($cache_time == 0) {
        delete_transient($transient_name);
    }
    if ($cache_time == '') {
        $cache_time = 15;
    }

    if (false === ( $trans = get_transient($transient_name) )) {
        $get_link = wp_remote_get('http://www.linkedin.com/countserv/count/share?url=' . $url . '&format=json');
        if (is_wp_error($get_link)) {
            return 0;
        } else {
            $linkedin_count = json_decode($get_link['body'], true);
            if ($linkedin_count['count'] == '') {
                return 0;
            } else {
                set_transient($transient_name, intval($linkedin_count['count']), $cache_time * 60);
                return intval($linkedin_count['count']);
            }
        }
    } else {
        return get_transient($transient_name);
    }
}

/**
 * video_central_get_pinit Pinterest Share Counter
 *
 * @since 1.0.0
 *
 * @param  [type] $url        [description]
 * @param  [type] $cache_time [description]
 * @return [type]             [description]
 */
function video_central_get_pinit($url, $cache_time) {

    $transient_name = md5('pinit_' . $url);

    if ($cache_time == 0) {
        delete_transient($transient_name);
    }
    if ($cache_time == '') {
        $cache_time = 15;
    }

    if (false === ( $trans = get_transient($transient_name) )) {

        $get_link = wp_remote_get('http://api.pinterest.com/v1/urls/count.json?callback=receiveCount&url=' . $url);

        $temp_json = str_replace("receiveCount(", "", $get_link['body']);
        $temp_json = substr($temp_json, 0, -1);

        if (is_wp_error($get_link)) {
            return 0;
        } else {
            $pinit_count = json_decode($temp_json, true);
            if ($pinit_count['count'] == '') {
                return 0;
            } else {
                set_transient($transient_name, intval($pinit_count['count']), $cache_time * 60);
                return intval($pinit_count['count']);
            }
        }
    } else {
        return get_transient($transient_name);
    }
}


/**
 * [video_central_get_twitter_followers description]
 *
 * @since 1.0.0
 *
 * @param  [type] $twitter_user [description]
 * @param  [type] $cache_time   [description]
 * @return [type]               [description]
 */

function video_central_get_twitter_followers() {

    $options = get_option( 'video_central_tweets_settings' );

    // CHECK SETTINGS & DIE IF NOT SET
    if( empty($options['consumerkey']) || empty($options['consumersecret']) ){
         return 0;
    }

    // some variables
    $consumerKey = $options['consumerkey'];
    $consumerSecret = $options['consumersecret'];
    $token = get_option('cfTwitterToken');

    // get follower count from cache
    $numberOfFollowers = get_transient('rm_twitter_followers');

    // cache version does not exist or expired
    if (false === $numberOfFollowers) {

        // getting new auth bearer only if we don't have one
        if(!$token) {
            // preparing credentials
            $credentials = $consumerKey . ':' . $consumerSecret;
            $toSend = base64_encode($credentials);

            // http post arguments
            $args = array(
                'method' => 'POST',
                'httpversion' => '1.1',
                'blocking' => true,
                'headers' => array(
                    'Authorization' => 'Basic ' . $toSend,
                    'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8'
                ),
                'body' => array( 'grant_type' => 'client_credentials' )
            );

            add_filter('https_ssl_verify', '__return_false');
            $response = wp_remote_post('https://api.twitter.com/oauth2/token', $args);

            $keys = json_decode(wp_remote_retrieve_body($response));

            if($keys) {
                // saving token to wp_options table
                update_option('cfTwitterToken', $keys->access_token);
                $token = $keys->access_token;
            }
        }

        // we have bearer token wether we obtained it from API or from options
        $args = array(
            'httpversion' => '1.1',
            'blocking' => true,
            'headers' => array(
                'Authorization' => "Bearer $token"
            )
        );

        add_filter('https_ssl_verify', '__return_false');
        $api_url = "https://api.twitter.com/1.1/users/show.json?screen_name={$options['username']}";
        $response = wp_remote_get($api_url, $args);

        if (!is_wp_error($response)) {

            $followers = json_decode(wp_remote_retrieve_body($response));
            $followers = $followers->followers_count;

        } else {
            // get old value and break
            $followers = get_option('video_central_twitter_followers');
            // uncomment below to debug
            //die($response->get_error_message());
        }

        // cache for an hour
        set_transient('video_central_twitter_followers', $followers, 1*60*60);
        update_option('video_central_twitter_followers', $followers);
    }

    return $followers;
}


/**
 * [video_central_get_facebook_likes description]
 *
 * @since 1.0.0
 *
 * @param  [type] $facebook_user [description]
 * @param  [type] $cache_time    [description]
 * @return [type]                [description]
 */
function video_central_get_facebook_likes( $facebook_user, $cache_time ) {

    $transient_name = 'video_central_overall_facebook_likes';
    $fbData = '';

    if ($cache_time == 0)
        delete_transient($transient_name);

    if ($cache_time == '')
        $cache_time = 15;

    if (false === ( $video_central_overall_facebook_followers = get_transient($transient_name) )) {
        
        $json = wp_remote_get("http://graph.facebook.com/" . $facebook_user, array('timeout' => 30));

        if (is_wp_error($json)) {
        
            return 0;
        
        } else {
            
            $json = wp_remote_get("http://graph.facebook.com/" . $facebook_user, array('timeout' => 30));
            
            if (is_wp_error($json))
                return 0;
                
            $fbData = json_decode($json['body'], true);

            set_transient($transient_name, intval($fbData['likes']), $cache_time * 60);

            return intval($fbData['likes']);
        }
        
    } else {
        return get_transient($transient_name);
    }
}


/**
 * [video_central_gplus_count get googe plus circled count]
 *
 * @since 1.0.0
 *
 * @param  [type] $gplus_username [description]
 * @param  [type] $gplus_api      [description]
 * @param  [type] $cache_time     [description]
 * @return [type]                 [description]
 */
function video_central_gplus_count( $id, $cache_time ) {

    $transient_name = 'video_central_overall_google_pluses';
    $google_plus_count = '';

    if ($cache_time == 0) {
        delete_transient($transient_name);
    }

    if ($cache_time == '') {
        $cache_time = 15;
    }

    if ( false === ( $video_central_overall_twitter_followers = get_transient($transient_name) ) ) {

        // Google Plus.
        $link = "https://plus.google.com/".$id;

        $gplus = array(
            'method'    => 'POST',
            'sslverify' => false,
            'timeout'   => 30,
            'headers'   => array( 'Content-Type' => 'application/json' ),
            'body'      => '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . $link . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]'
        );

        $remote_data = wp_remote_get( 'https://clients6.google.com/rpc', $gplus );

        if (is_wp_error($remote_data)) {

            return 0;

        } else {

            $json_data = json_decode( $remote_data['body'], true );

            foreach($json_data[0]['result']['metadata']['globalCounts'] as $gcount){

                $google_plus_count = $gcount;

            }

            if ( !$google_plus_count) {

                $link = "https://plus.google.com/".$id."/posts";

                $page = file_get_contents($link);

                if (preg_match('/>([0-9,]+) people</i', $page, $matches))
                    $google_plus_count = str_replace(',', '', $matches[1]);

            }

            set_transient($transient_name, intval($google_plus_count), $cache_time * 60);

            return $google_plus_count;

        }

    } else {

        return get_transient($transient_name);

    }

}

/* single Article Social Counters */
function video_central_single_get_tweets( $url = null, $cache_time = 15 ) {
	
	$post_id = get_the_ID();
	
	$transient_name = 'video_central_single_get_tweets_'. $post_id;
	
    $url = $url ? $url : get_permalink();
    
	$url = apply_filters(__FUNCTION__.'_url', $url);
    
    if ($cache_time == 0)
        delete_transient($transient_name);
    
    if ($cache_time == '')
        $cache_time = 15;
    
    if ( false === ( $transient = get_transient($transient_name) ) ) {
    
	    $args = array(
	        'timeout'     => 30,
	        'sslverify'   => true,
	    ); 
	
	    $response = wp_remote_get('http://urls.api.twitter.com/1/urls/count.json?url=' . $url, $args);
	    	    
	    if( is_wp_error( $response ) )
	    	return 0;
	    
		$xml = $response['body'];
	
		if( is_wp_error( $xml ) )
			return 0;
	
		$json = json_decode( $xml, true );
	    
	    $shares = isset( $json['count'] ) ?  $json['count'] : 0;
	    
	    if($shares)
	    	set_transient($transient_name, $shares, $cache_time * 60);
	            
	    return $shares;
	    
    } else {
        
    	return get_transient($transient_name);
    
    }
}

/**
 * [video_central_single_get_shares description]
 * @param  [type] $url [description]
 * @return [type]      [description]
 */
function video_central_single_get_linkedin_shares( $url = null, $cache_time = 15 ) {

    $post_id = get_the_ID();
    
    $transient_name = 'video_central_single_get_linkedin_'. $post_id;
    
    $url = $url ? $url : get_permalink();
    
	$url = apply_filters(__FUNCTION__.'_url', $url);
    
	if ($cache_time == 0)
	    delete_transient($transient_name);
	
	if ($cache_time == '')
	    $cache_time = 15;
	
	if ( false === ( $transient = get_transient($transient_name) ) ) {
		
		$args = array(
		    'timeout'     => 30,
		    'sslverify'   => true,
		);
		
	    $response = wp_remote_get("http://www.linkedin.com/countserv/count/share?url=$url&format=json", $args);
	    
	    if( is_wp_error( $response ) )
	    	return 0;
	        
		$xml = $response['body'];
	
		if( is_wp_error( $xml ) )
			return 0;
	
		$json = json_decode( $xml, true );
	
	    $shares = isset( $json['count'] ) ? $json['count'] : 0;
	    
	    if($shares)
	    	set_transient($transient_name, $shares, $cache_time * 60);
	            
	    return $shares;
        
    } else {
        
    	return get_transient($transient_name);
    
    }
}


/**
 * [video_central_single_get_likes description]
 * @param  [type] $url [description]
 * @return [type]      [description]
 */
function video_central_single_get_facebook_likes( $url = null, $cache_time = 15 ) {
 
	$post_id = get_the_ID();
	
	$transient_name = 'video_central_single_get_facebook_likes_'. $post_id;
	
	$url = $url ? $url : get_permalink();
	
	$url = apply_filters(__FUNCTION__.'_url', $url);
	
	if ($cache_time == 0)
	    delete_transient($transient_name);
	
	if ($cache_time == '')
	    $cache_time = 15;
	
	if ( false === ( $transient = get_transient($transient_name) ) ) {
		
		$args = array(
		    'timeout'     => 30,
		    'sslverify'   => true,
		);
		
    	$response = wp_remote_get('http://graph.facebook.com/?ids=' . $url, $args);
    	
    	if( is_wp_error( $response ) )
    	    	return 0;
    	        
		$xml = $response['body'];
	
		if( is_wp_error( $xml ) )
			return 0;
	
		$json = json_decode( $xml, true );
		    
    	$shares = isset( $json[$url]['shares'] ) ? intval( $json[$url]['shares'] ) : 0;
    	
    	if ( $shares )
    		set_transient($transient_name, $shares, $cache_time * 60);
    	
    	return $shares;
    
    } else {
    	return get_transient($transient_name);
    
    }
}

/**
 * [video_central_single_get_plusones description]
 * @param  [type] $url [description]
 * @return [type]      [description]
 */
function video_central_single_get_plusones( $url = null, $cache_time = 15 ) {
	
	$post_id = get_the_ID();
	
	$transient_name = 'video_central_single_get_googleplus_'. $post_id;

    $url = $url ? $url : get_permalink();
    
	$url = apply_filters(__FUNCTION__.'_url', $url);
	
	if ($cache_time == 0)
	    delete_transient($transient_name);
	
	if ($cache_time == '')
	    $cache_time = 15;
	
	if ( false === ( $transient = get_transient($transient_name) ) ) {

	     // Google Plus.
        $link = "https://clients6.google.com/rpc";

        $args = array(
            'method'    => 'POST',
            'sslverify' => false,
            'timeout'   => 30,
            'headers'   => array( 'Content-Type' => 'application/json' ),
            'body'      => '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . $url . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]'
        );

        $response = wp_remote_get( 'https://clients6.google.com/rpc', $args );
		
		if( is_wp_error( $response ) )
		    return 0;
		        
		$xml = $response['body'];
		
		if( is_wp_error( $xml ) )
			return 0;
		
		$json = json_decode( $xml, true );
	    
	    $shares = isset( $json[0]['result']['metadata']['globalCounts']['count'] ) ? intval( $json[0]['result']['metadata']['globalCounts']['count'] ) : 0;
	    
	    if ( $shares )
	    	set_transient($transient_name, $shares, $cache_time * 60);
	    
	    return intval( $shares );
	    
    } else {
        
    	return get_transient($transient_name);
    
    }
}