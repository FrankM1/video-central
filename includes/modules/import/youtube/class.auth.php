<?php

/**
 * YouTube OAuth functions.
 */

/**
 * Displays the link that begins OAuth authorization.
 *
 * @param string $text
 */
function video_central_show_oauth_link($text = '', $echo = true)
{
    if (empty($text)) {
        $text = __('Grant plugin access', 'video_central');
    }

    $options = video_central_youtube_api_oauth_details();

    if (empty($options['client_id']) || empty($options['client_secret'])) {
        return;
    } else {
        if (!empty($options['token']['value'])) {
            $nonce = wp_create_nonce('video-central-revoke-oauth-token');
            $url = menu_page_url('video_central_settings', false).'&unset_token=true&video_central_nonce='.$nonce.'#video-central-settings-auth-options';
            printf('<a href="%s" class="button">%s</a>', esc_url( $url ), __('Revoke access', 'video_central'));

            return;
        }
    }

    $endpoint = 'https://accounts.google.com/o/oauth2/auth';
    $parameters = array(
        'response_type' => 'code',
        'client_id' => $options['client_id'],
        'redirect_uri' => video_central_get_oauth_redirect_uri(),
        'scope' => 'https://www.googleapis.com/auth/youtube.readonly',
        'state' => wp_create_nonce('video-central-youtube-oauth-grant'),
        'access_type' => 'offline',
    );

    $url = $endpoint.'?'.http_build_query($parameters);

    $anchor = sprintf('<a href="%s">%s</a>', esc_url( $url ), esc_html( $text ) );
    if ($echo) {
        echo $anchor;
    }

    return $anchor;
}

/**
 * Returns the OAuth redirect URL.
 */
function video_central_get_oauth_redirect_uri()
{
    $url = get_admin_url();

    return $url;
}

add_action('admin_init', 'video_central_check_youtube_auth_code');
/**
 * Get authentification token if request is response returned from YouTube.
 */
function video_central_check_youtube_auth_code()
{
    if (isset($_GET['code']) && isset($_GET['state'])) {
        if (wp_verify_nonce($_GET['state'], 'video-central-youtube-oauth-grant')) {
            $options = video_central_get_youtube_oauth_details();
            $fields = array(
                'code' => $_GET['code'],
                'client_id' => $options['client_id'],
                'client_secret' => $options['client_secret'],
                'redirect_uri' => video_central_get_oauth_redirect_uri(),
                'grant_type' => 'authorization_code',
            );
            $token_url = 'https://accounts.google.com/o/oauth2/token';

            $response = wp_remote_post($token_url, array(
                'method' => 'POST',
                'timeout' => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => array(),
                'body' => $fields,
                'cookies' => array(),
                )
            );

            if (!is_wp_error($response)) {
                $response = json_decode(wp_remote_retrieve_body($response), true);
                if (isset($response['access_token'])) {
                    $token = array(
                        'value' => $response['access_token'],
                        'valid' => $response['expires_in'],
                        'time' => time(),
                    );

                    video_central_update_youtube_oauth(false, false, $token);
                }
            }

            wp_redirect(html_entity_decode(menu_page_url('video_central_settings', false)).'#video-central-settings-auth-options');
            wp_die();
        }
    }
}

/**
 * Refresh the access token.
 */
function video_central_refresh_oauth_token()
{
    $token = video_central_get_youtube_oauth_details();
    if (empty($token['client_id']) || empty($token['client_secret'])) {
        return new WP_Error('video_central_token_refresh_missing_oauth_login', __('YouTube API OAuth credentials missing. Please visit plugin Settings page and enter your credentials.', 'video_central'));
    }

    $endpoint = 'https://accounts.google.com/o/oauth2/token';
    $fields = array(
        'client_id' => $token['client_id'],
        'client_secret' => $token['client_secret'],
        'refresh_token' => $token['token']['value'],
        'grant_type' => 'refresh_token',
    );
    $response = wp_remote_post($endpoint, array(
        'method' => 'POST',
        'timeout' => 45,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => array(),
        'body' => $fields,
        'cookies' => array(),
        )
    );

    if (is_wp_error($response)) {
        return $response;
    }

    if (200 != wp_remote_retrieve_response_code($response)) {
        $details = json_decode(wp_remote_retrieve_body($response), true);
        if (isset($details['error'])) {
            return new WP_Error('video_central_invalid_youtube_grant', sprintf(__('While refreshing the access token, YouTube returned error code <strong>%s</strong>. Please refresh tokens manually by revoking current access and granting new access.', 'video_central'), $details['error']), $details);
        }

        return new WP_Error('video_central_token_refresh_error', __('While refreshing the access token, YouTube returned an unknown error.', 'video_central'));
    }

    $data = json_decode(wp_remote_retrieve_body($response), true);
    $token = array(
        'value' => $data['access_token'],
        'valid' => $data['expires_in'],
        'time' => time(),
    );
    video_central_update_youtube_oauth(false, false, $token);

    return $token;
}
